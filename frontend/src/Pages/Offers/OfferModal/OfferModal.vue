<script setup lang="ts">
  import useCustomerStore from '../../../store/customer';
  import { onMounted } from 'vue';
  import useToolsStore from '../../../store/tools';
  import useCoatingStore from '../../../store/coating';
  import { useUserStore } from '@/store/user';
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
    <div v-if="isModalOpen" class="fixed inset-0 z-10 flex items-center justify-center bg-black/50">
      <div
        class="w-full min-h-screen max-w-8xl bg-[#D3D3D3] p-8 rounded-lg shadow-lg overflow-y-auto h-full"
      >
        <h2 class="text-lg font-semibold mb-4">Oferta</h2>
        <OfferForm :closeModal="closeModal" />
        <InfoSection />
        <PdfConfig v-if="userStore.isCreator()" />
      </div>
    </div>
  </div>
</template>
