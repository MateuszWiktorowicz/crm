import { ref } from 'vue';

const selectedFileModalIndex = ref<number | null>(null);
const isFilesModalOpen = ref(false);

export function useFilesModal() {
  function openFilesModal(index: number) {
    selectedFileModalIndex.value = index;
    isFilesModalOpen.value = true;
  }

  function closeFilesModal() {
    selectedFileModalIndex.value = null;
    isFilesModalOpen.value = false;
  }

  return {
    selectedFileModalIndex,
    isFilesModalOpen,
    openFilesModal,
    closeFilesModal,
  };
}
