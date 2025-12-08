<script setup lang="ts">
  import { onMounted, watch, computed } from 'vue';
  import Header from '../components/Header.vue';
  import Button from '../components/Button.vue';
  import OfferStatsChart from '../components/OfferStatsChart.vue';
  import { useDashboardStore } from '@/store/dashboard';
  import useCustomerStore from '@/store/customer';
  import { useUserStore } from '@/store/user';

  const dashboardStore = useDashboardStore();
  const customerStore = useCustomerStore();
  const userStore = useUserStore();

  onMounted(async () => {
    await customerStore.fetchCustomers();
    await userStore.fetchUsers();
    await dashboardStore.fetchStats();
  });

  watch(
    () => dashboardStore.filters,
    () => {
      dashboardStore.fetchStats();
    },
    { deep: true }
  );

  const handlePeriodChange = (period: 'week' | 'month' | 'year' | 'custom') => {
    dashboardStore.setFilter({ period });
    if (period !== 'custom') {
      dashboardStore.setFilter({ startDate: '', endDate: '' });
    }
  };

  const handleApplyCustomDate = () => {
    if (dashboardStore.filters.startDate && dashboardStore.filters.endDate) {
      dashboardStore.fetchStats();
    }
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pl-PL', {
      style: 'currency',
      currency: 'PLN',
    }).format(value);
  };

  // Pobierz unikalne znaczniki handlowców z klientów
  const uniqueMarkers = computed(() => {
    const markers = customerStore.customers
      .map((customer) => customer.salerMarker)
      .filter((marker) => marker && marker.trim() !== '');
    return [...new Set(markers)].sort();
  });
</script>

<template>
  <div>
    <Header title="Dashboard" />
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <!-- Filtry -->
      <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Filtry</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <!-- Filtrowanie po kliencie -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Klient</label>
            <select
              :value="dashboardStore.filters.customerId ?? ''"
              class="w-full p-2 border rounded"
              @change="
                dashboardStore.setFilter({
                  customerId: ($event.target as HTMLSelectElement).value
                    ? Number(($event.target as HTMLSelectElement).value)
                    : null,
                });
                dashboardStore.fetchStats();
              "
            >
              <option value="">Wszyscy klienci</option>
              <option
                v-for="customer in customerStore.customers"
                :key="customer.id ?? `customer-${customer.id}`"
                :value="customer.id"
              >
                {{ customer.name }}
              </option>
            </select>
          </div>

          <!-- Filtrowanie po handlowcu (po znaczniku) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Handlowiec</label>
            <select
              :value="dashboardStore.filters.employeeMarker ?? ''"
              class="w-full p-2 border rounded"
              @change="
                dashboardStore.setFilter({
                  employeeMarker: ($event.target as HTMLSelectElement).value || null,
                });
                dashboardStore.fetchStats();
              "
            >
              <option value="">Wszyscy handlowcy</option>
              <option
                v-for="marker in uniqueMarkers"
                :key="marker"
                :value="marker"
              >
                {{ marker }}
              </option>
            </select>
          </div>

          <!-- Okres -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Okres</label>
            <select
              v-model="dashboardStore.filters.period"
              class="w-full p-2 border rounded"
              @change="handlePeriodChange(dashboardStore.filters.period)"
            >
              <option value="week">Tydzień</option>
              <option value="month">Miesiąc</option>
              <option value="year">Ten rok</option>
              <option value="custom">Zakres dat</option>
            </select>
          </div>
        </div>

        <!-- Zakres dat (tylko gdy wybrano custom) - w osobnym wierszu -->
        <div v-if="dashboardStore.filters.period === 'custom'" class="mt-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Zakres dat</label>
          <div class="flex gap-2 items-end">
            <div class="flex-1">
              <label class="block text-xs text-gray-500 mb-1">Od</label>
              <input
                v-model="dashboardStore.filters.startDate"
                type="date"
                class="w-full p-2 border rounded"
              />
            </div>
            <div class="flex-1">
              <label class="block text-xs text-gray-500 mb-1">Do</label>
              <input
                v-model="dashboardStore.filters.endDate"
                type="date"
                class="w-full p-2 border rounded"
              />
            </div>
            <Button @click="handleApplyCustomDate" variant="primary" size="small">
              Zastosuj
            </Button>
          </div>
        </div>
      </div>

      <!-- Błędy -->
      <div v-if="dashboardStore.errors.length > 0" class="mb-4">
        <ul class="bg-red-100 text-red-800 border border-red-400 p-4 rounded">
          <li v-for="(error, index) in dashboardStore.errors" :key="index">{{ error }}</li>
        </ul>
      </div>

      <!-- Statystyki -->
      <div v-if="dashboardStore.stats && !dashboardStore.isLoading" class="space-y-6">
        <!-- Główne statystyki - liczba ofert -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">W przygotowaniu</h3>
            <p class="text-3xl font-bold text-gray-900">
              {{ dashboardStore.stats.stats.inPreparation }}
            </p>
          </div>

          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Wysłane</h3>
            <p class="text-3xl font-bold text-blue-600">
              {{ dashboardStore.stats.stats.sent }}
            </p>
          </div>

          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Zaakceptowane</h3>
            <p class="text-3xl font-bold text-green-600">
              {{ dashboardStore.stats.stats.accepted }}
            </p>
          </div>

          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Odrzucone</h3>
            <p class="text-3xl font-bold text-red-600">
              {{ dashboardStore.stats.stats.rejected }}
            </p>
          </div>
        </div>

        <!-- Wartości ofert - dynamiczne w zależności od okresu -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">
              {{
                dashboardStore.filters.period === 'week'
                  ? 'Wartość tygodniowa'
                  : dashboardStore.filters.period === 'month'
                    ? 'Wartość miesięczna'
                    : dashboardStore.filters.period === 'year'
                      ? 'Wartość roczna'
                      : 'Wartość w okresie'
              }}
            </h3>
            <p class="text-2xl font-bold text-gray-900">
              {{ formatCurrency(dashboardStore.stats.periodValue || dashboardStore.stats.totalValue) }}
            </p>
          </div>

          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Wartość miesięczna (bieżący miesiąc)</h3>
            <p class="text-2xl font-bold text-gray-900">
              {{ formatCurrency(dashboardStore.stats.monthlyValue) }}
            </p>
          </div>

          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Wartość kwartalna (bieżący kwartał)</h3>
            <p class="text-2xl font-bold text-gray-900">
              {{ formatCurrency(dashboardStore.stats.quarterlyValue) }}
            </p>
          </div>
        </div>

        <!-- Wartość wygranych vs przegranych -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Wartość wygranych ofert</h3>
            <p class="text-3xl font-bold text-green-600">
              {{ formatCurrency(dashboardStore.stats.wonValue) }}
            </p>
          </div>

          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Wartość przegranych ofert</h3>
            <p class="text-3xl font-bold text-red-600">
              {{ formatCurrency(dashboardStore.stats.lostValue) }}
            </p>
          </div>
        </div>

        <!-- Wartość całkowita i skuteczność -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Wartość całkowita (okres)</h3>
            <p class="text-3xl font-bold text-gray-900">
              {{ formatCurrency(dashboardStore.stats.totalValue) }}
            </p>
          </div>

          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Skuteczność ofert</h3>
            <p class="text-3xl font-bold" :class="dashboardStore.stats.successRate >= 50 ? 'text-green-600' : 'text-yellow-600'">
              {{ dashboardStore.stats.successRate.toFixed(1) }}%
            </p>
          </div>
        </div>

        <!-- Wykresy -->
        <OfferStatsChart
          :stats="dashboardStore.stats.stats"
          :won-value="dashboardStore.stats.wonValue"
          :lost-value="dashboardStore.stats.lostValue"
        />
      </div>

      <!-- Loading state -->
      <div v-if="dashboardStore.isLoading" class="text-center py-12">
        <p class="text-gray-600">Ładowanie statystyk...</p>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
