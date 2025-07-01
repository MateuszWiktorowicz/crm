<script setup lang="ts">
  import { useOfferStore } from '@/store/offer';
  import Button from '@/components/Button.vue';
  import { useUserStore } from '@/store/user';

  defineProps<{
    scrollToNewTool: () => void;
    closeModal: () => void;
  }>();

  const offerStore = useOfferStore();
  const userStore = useUserStore();
</script>

<template>
  <div class="mt-4">
    <Button @click="scrollToNewTool" variant="success"> Dodaj narzędzie </Button>
    <div class="flex justify-end space-x-2 mt-4">
      <Button @click="closeModal" variant="secondary"> Anuluj </Button>

      <Button :key="'id' + offerStore.offer.id" type="submit" :disabled="offerStore.isLoading">
        <template v-if="offerStore.isSaving">
          <span class="animate-spin mr-2">⏳</span> Zapisuję...
        </template>
        <template v-else> Zapisz </template>
      </Button>
      <Button
        @click="offerStore.generatePdf()"
        variant="warning"
        :disabled="offerStore.offer.id === null || offerStore.isLoading"
        v-if="userStore.isCreator()"
      >
        <template v-if="offerStore.isPdfGenerating">
          <span class="animate-spin mr-2">⏳</span> Generuję...
        </template>
        <template v-else>Generuj PDF</template>
      </Button>
    </div>
  </div>
</template>
