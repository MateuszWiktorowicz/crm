<script setup lang="ts">
  import { useOfferStore } from '@/store/offer';

  const offerStore = useOfferStore();
</script>

<template>
  <div class="mt-6 p-6 bg-white rounded-lg border border-gray-200 shadow-sm">
    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
      Konfiguracja PDF
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Termin realizacji</label>
        <input
          v-model="offerStore.offer.pdfInfo.deliveryTime"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
          placeholder="np. 12–15 dni roboczych"
        />
      </div>

      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Ważność oferty</label>
        <input
          v-model="offerStore.offer.pdfInfo.offerValidity"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
          placeholder="np. 7 dni"
        />
      </div>

      <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">Warunki płatności</label>
        <input
          v-model="offerStore.offer.pdfInfo.paymentTerms"
          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
          placeholder="np. przelew 14 dni"
        />
      </div>

      <div class="space-y-1 flex flex-col justify-end">
        <label class="flex items-center space-x-3 cursor-pointer p-3 rounded-lg hover:bg-gray-50 transition-colors">
          <input
            type="checkbox"
            v-model="offerStore.offer.pdfInfo.displayDiscount"
            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
          />
          <span class="text-sm font-medium text-gray-700">Pokaż rabat w PDF</span>
        </label>
      </div>
    </div>
  </div>
</template>
