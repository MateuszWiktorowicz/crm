<script setup lang="ts">
  import { useOfferStore } from '@/store/offer';

  const offerStore = useOfferStore();
</script>

<template>
  <div class="mt-6 flex gap-3 space-y-3">
    <div class="flex flex-col">
      <label class="font-semibold">Termin realizacji:</label>
      <input
        v-model="offerStore.offer.pdfInfo.deliveryTime"
        class="border rounded p-2 bg-gray-100"
      />
    </div>

    <div class="flex flex-col">
      <label class="font-semibold">Ważność oferty:</label>
      <input
        v-model="offerStore.offer.pdfInfo.offerValidity"
        class="border rounded p-2 bg-gray-100"
      />
    </div>

    <div class="flex flex-col">
      <label class="font-semibold">Warunki płatności:</label>
      <input
        v-model="offerStore.offer.pdfInfo.paymentTerms"
        class="border rounded p-2 bg-gray-100"
      />
    </div>
    <div class="flex flex-col">
      <label class="font-semibold">Pokaż rabat:</label>
      <input
        type="checkbox"
        v-model="offerStore.offer.pdfInfo.displayDiscount"
        class="border flex mt-4 f-full items-center self-center rounded p-5"
      />
    </div>
  </div>
</template>
