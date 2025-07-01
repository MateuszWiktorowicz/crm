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

  const customerName = computed(
    () =>
      customerStore.customers.find((c) => c.id === offer.value.customer?.id)?.name ||
      'Wybierz kontrahenta'
  );

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
  <div class="flex items-center gap-5 justify-between p-4 bg-gray-100 rounded-lg shadow-md">
    <!-- Kontrahent -->
    <div class="mb-4 max-w-3xl">
      <label class="block text-sm font-medium text-gray-700 mb-1">Kontrahent</label>
      <Button @click="isCustomerModalOpen = true" variant="success">
        {{ customerName }}
      </Button>
    </div>

    <!-- Status oferty -->
    <div class="flex flex-col items-end">
      <label class="block text-sm font-medium text-gray-700 mb-1">Status oferty</label>
      <select v-model="offer.status" class="w-full p-2 border rounded">
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
    <div class="flex flex-col items-end">
      <label class="block text-sm font-medium text-gray-700 mb-1">Kwota netto oferty</label>
      <span class="text-lg font-semibold text-gray-800 bg-gray-200 px-3 py-1 rounded-lg shadow-sm">
        {{ offer.totalPrice.toFixed(2) }} PLN
      </span>
    </div>

    <!-- Rabat globalny -->
    <div class="flex flex-col items-end">
      <label class="block text-sm font-medium text-gray-700 mb-1">Rabat całej oferty (%)</label>
      <input
        type="number"
        v-model="offer.globalDiscount"
        class="w-full p-2 border rounded"
        min="0"
        step="0.1"
        @input="handleGlobalDiscountChange"
      />
    </div>
  </div>

  <SelectModal
    :isOpen="isCustomerModalOpen"
    title="Wybierz kontrahenta"
    searchPlaceholder="Wyszukaj kontrahenta..."
    :items="customerStore.customers"
    :onSelect="selectCustomer"
    :onClose="
      () => {
        isCustomerModalOpen = false;
      }
    "
  />
</template>
