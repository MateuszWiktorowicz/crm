<script setup lang="ts">
  import useCustomerStore from '../../../store/customer';
  import { onMounted } from 'vue';
  import useToolsStore from '../../../store/tools';
  import useCoatingStore from '../../../store/coating';
  import { useUserStore } from '@/store/user';
  import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
  import PdfConfig from './OfferForm/PdfConfig.vue';
  import InfoSection from './OfferForm/InfoSection.vue';
  import OfferForm from './OfferForm/OfferForm.vue';

  /*
Początek nowej logiki
*/
  defineProps<{
    isModalOpen: boolean;
    closeModal: () => void;
  }>();

  const customerStore = useCustomerStore();
  const toolStore = useToolsStore();
  const coatingStore = useCoatingStore();
  const userStore = useUserStore();

  onMounted(() => {
    customerStore.fetchCustomers();
    toolStore.fetchTools();
    coatingStore.fetchCoatings();
  });
</script>

<template>
  <div>
    <TransitionRoot appear :show="isModalOpen" as="template">
      <Dialog as="div" class="relative z-10" @close="closeModal">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 flex items-center justify-center p-4">
          <DialogPanel
            class="w-full max-w-[95vw] bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[95vh]"
          >
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white">
              <DialogTitle class="text-xl font-semibold">Oferta</DialogTitle>
              <button
                @click="closeModal"
                class="text-white hover:text-gray-200 transition-colors p-1 rounded-full hover:bg-white/20"
                aria-label="Zamknij"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-y-auto px-6 py-6 bg-gray-50">
              <OfferForm :closeModal="closeModal" />
              <InfoSection />
              <PdfConfig v-if="userStore.isCreator()" />
            </div>
          </DialogPanel>
        </div>
      </Dialog>
    </TransitionRoot>
  </div>
</template>
