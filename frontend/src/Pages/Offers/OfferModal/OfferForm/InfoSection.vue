<script setup lang="ts">
  import { useOfferStore } from '@/store/offer';

  const offerStore = useOfferStore();
</script>

<template>
  <div
    v-if="Object.keys(offerStore.errors).length"
    class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg"
  >
    <p class="font-semibold">Wystąpiły błędy:</p>
    <ul class="list-disc list-inside">
      <li v-for="(errorMessages, field) in offerStore.errors" :key="field">
        <span v-for="(message, index) in errorMessages" :key="index">{{ message }}</span>
      </li>
    </ul>
  </div>
  <p v-if="offerStore.offer.globalDiscount !== 0" class="text-sm text-red-500 mt-1">
    Uwaga: Rabat całej oferty jest ustawiony! Jeśli chcesz zmienić rabat pojedyńczego narzędzia
    ustaw rabat globalny na 0.
  </p>
</template>
