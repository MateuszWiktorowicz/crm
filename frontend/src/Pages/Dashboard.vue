<script setup lang="ts">
  import { onMounted, watch, computed, ref } from 'vue';
  import Header from '../components/Header.vue';
  import Button from '../components/Button.vue';
  import OfferStatsChart from '../components/OfferStatsChart.vue';
  import SelectModal from '../components/SelectModal.vue';
  import { useDashboardStore } from '@/store/dashboard';
  import useCustomerStore from '@/store/customer';
  import { useUserStore } from '@/store/user';
  import { OfferService } from '@/services/OfferService';
  import { Customer } from '@/types/types';

  const dashboardStore = useDashboardStore();
  const customerStore = useCustomerStore();
  const userStore = useUserStore();

  const isCustomerModalOpen = ref(false);
  const employeeMarkers = ref<string[]>([]);

  const selectedCustomerName = computed(() => {
    if (!dashboardStore.filters.customerId) return 'Wybierz klienta';
    const customer = customerStore.customers.find((c) => c.id === dashboardStore.filters.customerId);
    return customer?.name || 'Wybierz klienta';
  });

  const selectCustomer = (customer: Customer) => {
    dashboardStore.setFilter({ customerId: customer.id });
    dashboardStore.fetchStats();
    isCustomerModalOpen.value = false;
  };

  const clearCustomer = () => {
    dashboardStore.setFilter({ customerId: null });
    dashboardStore.fetchStats();
  };

  onMounted(async () => {
    await customerStore.fetchCustomers(1);
    await userStore.fetchUsers();
    await dashboardStore.fetchStats();
    
    // Pobierz znaczniki handlowców z backendu
    try {
      const markers = await OfferService.getEmployeeMarkers();
      employeeMarkers.value = markers;
    } catch (error) {
      console.error('Błąd pobierania znaczników:', error);
    }
  });

  const isCustomDateValid = computed(() => {
    if (dashboardStore.filters.period !== 'custom') return true;
    return !!(dashboardStore.filters.startDate && dashboardStore.filters.endDate);
  });

  const periodLabel = computed(() => {
    if (dashboardStore.filters.period === 'week') return 'Wartość tygodniowa';
    if (dashboardStore.filters.period === 'month') return 'Wartość miesięczna';
    if (dashboardStore.filters.period === 'year') return 'Wartość roczna';
    if (dashboardStore.filters.period === 'custom') return 'Wartość w okresie';
    return 'Wartość całkowita';
  });

  const customDateError = computed(() => {
    if (dashboardStore.filters.period !== 'custom') return '';
    if (!dashboardStore.filters.startDate && !dashboardStore.filters.endDate) {
      return 'Wybierz datę początkową i końcową';
    }
    if (!dashboardStore.filters.startDate) {
      return 'Wybierz datę początkową';
    }
    if (!dashboardStore.filters.endDate) {
      return 'Wybierz datę końcową';
    }
    if (dashboardStore.filters.startDate > dashboardStore.filters.endDate) {
      return 'Data początkowa nie może być późniejsza niż końcowa';
    }
    return '';
  });

  watch(
    () => [dashboardStore.filters.period, dashboardStore.filters.customerId, dashboardStore.filters.employeeMarker],
    () => {
      // Automatycznie pobierz statystyki tylko jeśli nie jest custom lub jeśli custom ma poprawne daty
      if (dashboardStore.filters.period !== 'custom' || isCustomDateValid.value) {
        dashboardStore.fetchStats();
      }
    }
  );

  const handlePeriodChange = (period: 'week' | 'month' | 'year' | 'custom' | 'all') => {
    dashboardStore.setFilter({ period });
    if (period !== 'custom') {
      dashboardStore.setFilter({ startDate: '', endDate: '' });
      // Automatycznie pobierz statystyki dla predefiniowanych okresów i "cały okres"
      dashboardStore.fetchStats();
    }
  };

  const handleApplyCustomDate = () => {
    if (isCustomDateValid.value && !customDateError.value) {
      dashboardStore.fetchStats();
    }
  };

  const handleDateChange = () => {
    // Automatycznie zastosuj gdy obie daty są wypełnione i poprawne
    if (dashboardStore.filters.period === 'custom' && isCustomDateValid.value && !customDateError.value) {
      dashboardStore.fetchStats();
    }
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pl-PL', {
      style: 'currency',
      currency: 'PLN',
    }).format(value);
  };

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
            <div class="flex gap-2 items-center">
              <Button
                @click="isCustomerModalOpen = true"
                variant="success"
                class="flex-1 flex items-center justify-start overflow-hidden"
              >
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                  />
                </svg>
                <span class="truncate">{{ selectedCustomerName }}</span>
              </Button>
              <Button
                v-if="dashboardStore.filters.customerId"
                @click="clearCustomer"
                variant="secondary"
                size="small"
                title="Wyczyść"
                class="flex-shrink-0"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </Button>
            </div>
          </div>

          <!-- Filtrowanie po handlowcu (po znaczniku) -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Handlowiec</label>
            <div class="flex gap-2">
              <select
                :value="dashboardStore.filters.employeeMarker ?? ''"
                class="w-full p-2 border rounded flex-1"
                @change="
                  dashboardStore.setFilter({
                    employeeMarker: ($event.target as HTMLSelectElement).value || null,
                  });
                  dashboardStore.fetchStats();
                "
              >
                <option value="">Wszyscy handlowcy</option>
                <option
                  v-for="marker in employeeMarkers"
                  :key="marker"
                  :value="marker"
                >
                  {{ marker }}
                </option>
              </select>
              <Button
                v-if="dashboardStore.filters.employeeMarker"
                @click="
                  dashboardStore.setFilter({ employeeMarker: null });
                  dashboardStore.fetchStats();
                "
                variant="secondary"
                size="small"
                title="Wyczyść"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </Button>
            </div>
          </div>

          <!-- Okres -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Okres</label>
            <select
              v-model="dashboardStore.filters.period"
              class="w-full p-2 border rounded"
              @change="handlePeriodChange(dashboardStore.filters.period)"
            >
              <option value="all">Cały okres</option>
              <option value="week">Tydzień</option>
              <option value="month">Miesiąc</option>
              <option value="year">Ten rok</option>
              <option value="custom">Zakres dat</option>
            </select>
          </div>
        </div>

        <!-- Zakres dat (tylko gdy wybrano custom) - w osobnym wierszu -->
        <div v-if="dashboardStore.filters.period === 'custom'" class="mt-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Zakres dat
            <span class="text-red-500">*</span>
          </label>
          <div class="space-y-2">
            <div class="flex gap-2 items-end">
              <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Od</label>
                <input
                  v-model="dashboardStore.filters.startDate"
                  type="date"
                  :max="dashboardStore.filters.endDate || undefined"
                  class="w-full p-2 border rounded"
                  :class="{
                    'border-red-500': customDateError && !dashboardStore.filters.startDate,
                    'border-gray-300': !customDateError || dashboardStore.filters.startDate
                  }"
                  @change="handleDateChange"
                />
              </div>
              <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Do</label>
                <input
                  v-model="dashboardStore.filters.endDate"
                  type="date"
                  :min="dashboardStore.filters.startDate || undefined"
                  class="w-full p-2 border rounded"
                  :class="{
                    'border-red-500': customDateError && !dashboardStore.filters.endDate,
                    'border-gray-300': !customDateError || dashboardStore.filters.endDate
                  }"
                  @change="handleDateChange"
                />
              </div>
            </div>
            <!-- Komunikat błędu -->
            <div v-if="customDateError" class="text-sm text-red-600 bg-red-50 border border-red-200 rounded p-2 flex items-center gap-2">
              <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <span>{{ customDateError }}</span>
            </div>
            <!-- Komunikat informacyjny -->
            <div v-else-if="!isCustomDateValid" class="text-sm text-amber-600 bg-amber-50 border border-amber-200 rounded p-2 flex items-center gap-2">
              <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <span>Wybierz datę początkową i końcową, aby zobaczyć statystyki</span>
            </div>
            <!-- Komunikat sukcesu -->
            <div v-else class="text-sm text-green-600 bg-green-50 border border-green-200 rounded p-2 flex items-center gap-2">
              <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
              <span>Zakres dat został zastosowany</span>
            </div>
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
              {{ periodLabel }}
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

        <!-- Najpopularniejsze narzędzia -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Najpopularniejsze kartoteki -->
          <!-- <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
              Najpopularniejsze kartoteki
            </h3>
            <div v-if="dashboardStore.popularFiles.length > 0" class="space-y-3">
              <div
                v-for="(file, index) in dashboardStore.popularFiles"
                :key="file.id"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div class="flex items-center gap-3 flex-1">
                  <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-700">
                    {{ index + 1 }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ file.name }}</p>
                    <p class="text-sm text-gray-600">{{ file.code }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-4 ml-4">
                  <div class="text-right">
                    <p class="text-sm text-gray-600">Łączna ilość</p>
                    <p class="font-bold text-gray-900">{{ file.totalQuantity }} szt.</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-gray-600">W ofertach</p>
                    <p class="font-bold text-gray-900">{{ file.usageCount }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              Brak danych o kartotekach
            </div>
          </div> -->

          <!-- Najpopularniejsze kombinacje -->
          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
              Najpopularniejsze kombinacje
            </h3>
            <div v-if="dashboardStore.popularCombinations.length > 0" class="space-y-3">
              <div
                v-for="(combination, index) in dashboardStore.popularCombinations"
                :key="index"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div class="flex items-center gap-3 flex-1">
                  <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center font-bold text-green-700">
                    {{ index + 1 }}
                  </div>
                    <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900">{{ combination.toolType }}</p>
                    <div class="flex flex-wrap gap-2 mt-1 text-xs text-gray-600">
                      <span v-if="combination.flutes" class="px-2 py-1 bg-blue-100 rounded">
                        Zęby: {{ combination.flutes }}
                      </span>
                      <span v-if="combination.diameter" class="px-2 py-1 bg-purple-100 rounded">
                        Ø{{ combination.diameter }}mm
                      </span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center gap-4 ml-4">
                  <div class="text-right">
                    <p class="text-sm text-gray-600">Łączna ilość</p>
                    <p class="font-bold text-gray-900">{{ combination.totalQuantity }} szt.</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-gray-600">W ofertach</p>
                    <p class="font-bold text-gray-900">{{ combination.usageCount }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              Brak danych o kombinacjach
            </div>
          </div>

          <!-- Najpopularniejsze pokrycia -->
          <div class="bg-white shadow-lg rounded-lg border border-gray-300 p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
              Najpopularniejsze pokrycia
            </h3>
            <div v-if="dashboardStore.popularCoatings.length > 0" class="space-y-3">
              <div
                v-for="(coating, index) in dashboardStore.popularCoatings"
                :key="coating.code"
                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <div class="flex items-center gap-3 flex-1">
                  <div class="flex-shrink-0 w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center font-bold text-amber-700">
                    {{ index + 1 }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">{{ coating.name }}</p>
                    <p class="text-sm text-gray-600">{{ coating.code }}</p>
                  </div>
                </div>
                <div class="flex items-center gap-4 ml-4">
                  <div class="text-right">
                    <p class="text-sm text-gray-600">Łączna ilość</p>
                    <p class="font-bold text-gray-900">{{ coating.totalQuantity }} szt.</p>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-gray-600">W ofertach</p>
                    <p class="font-bold text-gray-900">{{ coating.usageCount }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              Brak danych o pokryciach
            </div>
          </div>
        </div>
      </div>

      <!-- Loading state -->
      <div v-if="dashboardStore.isLoading" class="text-center py-12">
        <p class="text-gray-600">Ładowanie statystyk...</p>
      </div>
    </div>

    <!-- Modal wyboru klienta -->
    <SelectModal
      :isOpen="isCustomerModalOpen"
      title="Wybierz klienta"
      searchPlaceholder="Wyszukaj klienta..."
      :fetchFunction="customerStore.fetchCustomers"
      :store="customerStore"
      :items="customerStore.customers"
      :onSelect="selectCustomer"
      :onClose="() => { isCustomerModalOpen = false; }"
    />
  </div>
</template>

<style scoped></style>
