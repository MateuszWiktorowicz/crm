<script setup lang="ts">
  import { useOfferStore } from '@/store/offer';
  import Button from '@/components/Button.vue';
  import { useUserStore } from '@/store/user';
  import { useToast } from '@/composables/useToast';
  import { watch } from 'vue';

  defineProps<{
    scrollToNewTool: () => void;
    closeModal: () => void;
  }>();

  const offerStore = useOfferStore();
  const userStore = useUserStore();
  const { success, error } = useToast();

  // Obserwuj zmiany w isSaving, aby pokazać notyfikację po zapisaniu
  let wasSaving = false;
  watch(
    () => offerStore.isSaving,
    (isSaving) => {
      if (wasSaving && !isSaving && (!offerStore.errors || Object.keys(offerStore.errors).length === 0)) {
        success(offerStore.offer.id ? 'Oferta została zaktualizowana' : 'Oferta została utworzona');
      }
      wasSaving = isSaving;
    }
  );

  // Obserwuj zmiany w isPdfGenerating
  let wasGenerating = false;
  watch(
    () => offerStore.isPdfGenerating,
    (isGenerating) => {
      if (wasGenerating && !isGenerating) {
        success('PDF został wygenerowany');
      }
      wasGenerating = isGenerating;
    }
  );
</script>

<template>
  <div class="mt-6 pt-6 border-t border-gray-200">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <Button @click="scrollToNewTool" variant="success" class="w-full sm:w-auto">
        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Dodaj narzędzie
      </Button>

      <div class="flex flex-wrap gap-3 w-full sm:w-auto justify-end">
        <Button @click="closeModal" variant="secondary" class="flex-1 sm:flex-none"> Anuluj </Button>

        <Button
          :key="'id' + offerStore.offer.id"
          type="submit"
          :disabled="offerStore.isLoading"
          class="flex-1 sm:flex-none"
        >
          <template v-if="offerStore.isSaving">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            Zapisuję...
          </template>
          <template v-else>
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Zapisz
          </template>
        </Button>

        <Button
          @click="offerStore.generatePdf()"
          variant="warning"
          :disabled="offerStore.offer.id === null || offerStore.isLoading"
          v-if="userStore.isCreator()"
          class="flex-1 sm:flex-none"
        >
          <template v-if="offerStore.isPdfGenerating">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            Generuję...
          </template>
          <template v-else>
            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
              />
            </svg>
            Generuj PDF
          </template>
        </Button>
      </div>
    </div>
  </div>
</template>
