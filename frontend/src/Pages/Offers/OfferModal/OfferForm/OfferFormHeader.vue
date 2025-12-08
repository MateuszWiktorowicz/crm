<script setup lang="ts">
  import { ref, computed, watch } from 'vue';
  import { useOfferStore } from '@/store/offer';
  import useCustomerStore from '@/store/customer';
  import Button from '@/components/Button.vue';
  import SelectModal from '@/components/SelectModal.vue';
  import { Customer } from '@/types/types';
  import { storeToRefs } from 'pinia';
  import { useUserStore } from '@/store/user';
  import { useDiscountWatcher } from '@/composables/useDiscountWatcher';

  const offerStore = useOfferStore();
  const customerStore = useCustomerStore();
  const userStore = useUserStore();
  const { offer } = storeToRefs(offerStore);

  useDiscountWatcher();

  const customerName = computed(() => {
    // Sprawdź najpierw w aktualnie załadowanych klientach
    const found = customerStore.customers.find((c) => c.id === offer.value.customer?.id);
    if (found) {
      return found.name;
    }
    // Jeśli nie znaleziono, użyj nazwy z obiektu customer (jeśli istnieje)
    return offer.value.customer?.name || 'Wybierz kontrahenta';
  });

  watch(
    () => offer.value.globalDiscount,
    () => {
      offerStore.applyGlobalDiscount(offer.value.globalDiscount);
      offerStore.calculateOfferTotalNetPrice();
    }
  );

  watch(
    () => offer.value.offerDetails,
    () => offerStore.calculateOfferTotalNetPrice(),
    { deep: true }
  );

  const isCustomerModalOpen = ref(false);

  const handleGlobalDiscountChange = (e: Event) => {
    const input = e.target as HTMLInputElement;
    const value = parseFloat(input.value);

    if (isNaN(value) || value < 0) {
      offer.value.globalDiscount = 0;
    }
  };

  const selectCustomer = (customer: Customer) => {
    offerStore.offer.customer = customer || null;
    isCustomerModalOpen.value = false;
  };
</script>

<template>
  <div class="p-6 bg-white rounded-lg border border-gray-200 shadow-sm mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Kontrahent -->
      <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Kontrahent</label>
        <Button @click="isCustomerModalOpen = true" variant="success" class="w-full justify-start">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
            />
          </svg>
          {{ customerName }}
        </Button>
      </div>

      <!-- Status oferty -->
      <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Status oferty</label>
        <select
          v-model="offer.status"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all bg-white"
        >
          <option
            v-for="(status, index) in offerStore.statuses"
            :key="status.id"
            :value="status"
            :disabled="!userStore.isCreator() && index > 1"
          >
            {{ status.name }}
          </option>
        </select>
      </div>

      <!-- Kwota netto -->
      <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Kwota netto oferty</label>
        <div class="px-4 py-2.5 bg-gradient-to-r from-indigo-50 to-indigo-100 border border-indigo-200 rounded-lg">
          <span class="text-xl font-bold text-indigo-700">
            {{ offer.totalPrice.toFixed(2) }} PLN
          </span>
        </div>
      </div>

      <!-- Rabat globalny -->
      <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Rabat całej oferty (%)</label>
        <input
          type="number"
          v-model="offer.globalDiscount"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
          min="0"
          step="0.1"
          @input="handleGlobalDiscountChange"
          placeholder="0.0"
        />
      </div>
    </div>
  </div>

  <SelectModal
    :isOpen="isCustomerModalOpen"
    title="Wybierz kontrahenta"
    searchPlaceholder="Wyszukaj kontrahenta..."
    :fetchFunction="customerStore.fetchCustomers"
    :store="customerStore"
    :items="customerStore.customers"
    :onSelect="selectCustomer"
    :onClose="
      () => {
        isCustomerModalOpen = false;
      }
    "
  />
</template>
