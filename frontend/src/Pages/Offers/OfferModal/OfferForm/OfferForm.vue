<script setup lang="ts">
  import { useOfferStore } from '@/store/offer';
  import useCustomerStore from '@/store/customer';
  import useToolsStore from '@/store/tools';
  import useCoatingStore from '@/store/coating';
  import { useUserStore } from '@/store/user';

  // Komponenty
  import Button from '@/components/Button.vue';
  import SelectModal from '@/components/SelectModal.vue';
  import OfferFormTableHeader from '../OfferForm/OfferFormTableHeader.vue';
  import OfferFormHeader from '../OfferForm/OfferFormHeader.vue';
  import FormActionsSection from '../OfferForm/FormActionsSection.vue';

  // Vue API
  import { ref, computed, nextTick, watch, onMounted } from 'vue';

  // Typy
  import type { CoatingType, OfferDetail, Tool, ToolType } from '@/types/types';

  // Composables
  import { useDiscountWatcher } from '@/composables/useDiscountWatcher';
  import OfferFormRow from './OfferFormRow.vue';
  import { useFilesModal } from '@/composables/useFilesModal';
import { useOfferLogic } from '@/composables/useOfferLogic';

  /*
Początek nowej logiki
*/
  defineProps<{
    closeModal: () => void;
  }>();

  const offerStore = useOfferStore();
  const customerStore = useCustomerStore();
  const toolStore = useToolsStore();
  const coatingStore = useCoatingStore();



const { selectedFileModalIndex, isFilesModalOpen, openFilesModal, closeFilesModal } = useFilesModal();

  const setDetailToolNetPrice = (detail: OfferDetail) => {
    if (!isCalculatedTool(detail) || detail.toolGeometry === null || detail.isToolPriceManual)
      return;
    let price = 0;

    if (detail.regrindingOption === 'face_regrinding') {
      price = detail.toolGeometry?.faceGrindingPrice ?? 0;
    } else {
      price =
        (detail.toolGeometry?.faceGrindingPrice ?? 0) +
        (detail.toolGeometry?.peripheryGrindingPrice ?? 0);
    }

    if (offerStore.isRadiusEndMill(detail)) {
      if (detail.radius < 1) {
        price -= 5;
      } else if (detail.radius >= 2.5) {
        price += 5;
      }
    }

    detail.toolNetPrice = price;
  };

  const isCalculatedTool = (detail: OfferDetail) => {
    return (
      detail.toolType?.toolTypeName !== undefined &&
      detail.toolType?.toolTypeName !== 'Niestandardowe' &&
      detail.toolType?.toolTypeName !== 'Kartoteka'
    );
  };

  function getTwistDrillDiameterLabel(diameter: number, allDiameters: number[]): string {
    const unique = [...new Set(allDiameters)].sort((a, b) => a - b);
    const index = unique.indexOf(diameter);

    if (index === 0) {
      return `<=${diameter}`;
    }

    const lower = +(unique[index - 1] + 0.1).toFixed(1);
    return `${lower} - ${diameter}`;
  }

  useDiscountWatcher();

  watch(
    () => offerStore.offer.offerDetails,
    (newDetails) => {
      newDetails.forEach((detail: OfferDetail, index: number) => {
        if (
          isCalculatedTool(detail) &&
          detail.toolType?.toolTypeName &&
          detail.flutesNumber &&
          detail.diameter
        ) {
          const isTwistDrill =
            detail.toolType.toolTypeName.toLowerCase().replace('ó', 'o').trim() === 'wiertlo krete';

          let diameterLabel = `${detail.diameter}`;
          if (isTwistDrill) {
            const allDiameters = toolStore.tools
              .filter(
                (t) =>
                  t.toolType.toolTypeName === detail.toolType.toolTypeName &&
                  t.flutesNumber === detail.flutesNumber
              )
              .map((t) => t.diameter);

            diameterLabel = getTwistDrillDiameterLabel(detail.diameter, allDiameters);
          }

         const toolTypeName = detail.toolType.toolTypeName.toLowerCase();

        if (toolTypeName.includes('promieniowy')) {
          detail.symbol = `${detail.toolType.toolTypeName} Z${detail.flutesNumber} D${diameterLabel} R${detail.radius ?? 0}`;
        } else {
          detail.symbol = `${detail.toolType.toolTypeName} Z${detail.flutesNumber} D${diameterLabel}`;
        }

          const newGeometry = toolStore.getSelectedTool(
            detail.toolType,
            detail.flutesNumber,
            detail.diameter
          );

          if (detail.toolGeometry !== newGeometry) {
            detail.toolGeometry = newGeometry;
            detail.isToolPriceManual = false;
          }
        }

        if (
          isCalculatedTool(detail) &&
          detail.toolType?.toolTypeName &&
          detail.flutesNumber != null &&
          detail.diameter != null &&
          detail.radius != null &&
          detail.regrindingOption != null
        ) {
          setDetailToolNetPrice(detail);
        }
      });
    },
    { deep: true, immediate: true }
  );

  watch(
    () => offerStore.offer.offerDetails.map((d: OfferDetail) => d.toolType?.toolTypeName),
    (newTypes, oldTypes) => {
      if (offerStore.isEditing && offerStore.isInitialEditPhase) {
        offerStore.isInitialEditPhase = false;
        return;
      }

      newTypes.forEach((type, index) => {
        if (type === 'Kartoteka' && oldTypes?.[index] !== 'Kartoteka') {
          offerStore.offer.offerDetails[index].toolGeometry = null;
          selectedFileModalIndex.value = index;
          isFilesModalOpen.value = true;
        }
      });
    }
  );

  watch(
    () =>
      offerStore.offer.offerDetails.map((detail: OfferDetail) => ({
        diameter: detail.diameter ?? null,
        code: detail.coatingPrice?.coatingType?.mastermetCode ?? null,
      })),
    (newValues, oldValues) => {
      newValues.forEach((newVal, index) => {
        const oldVal = oldValues?.[index];
        if (!oldVal || newVal.diameter !== oldVal.diameter || newVal.code !== oldVal.code) {
          const detail = offerStore.offer.offerDetails[index];

          const newCoating = coatingStore.findCoatingByDiameterAndCode(
            detail.diameter ?? null,
            detail.coatingPrice?.coatingType?.mastermetCode ?? null,
            detail.toolType.toolTypeName
          );

          if (newCoating) {
            detail.coatingPrice = newCoating;
          }

          detail.coatingNetPrice = detail.coatingNetPrice ?? 0;

          console.log('[coating updated]', detail.coatingPrice);
        }
      });
    },
    { immediate: true }
  );

  watch(
    () => offerStore.offer.offerDetails.map((d: OfferDetail) => d.regrindingOption),
    (newOptions, oldOptions) => {
      newOptions.forEach((newVal, index) => {
        const oldVal = oldOptions?.[index];
        if (newVal !== oldVal) {
          const detail = offerStore.offer.offerDetails[index];
          if (isCalculatedTool(detail)) {
            detail.isToolPriceManual = false;
          }
        }
      });
    },
    { deep: false }
  );

  watch(
    () => offerStore.offer.offerDetails,
    (details) => {
      details.forEach((detail: OfferDetail) => {
        if (detail.tool) {
          detail.toolNetPrice = detail.tool.price ?? 0;
          detail.symbol = detail.tool.code ?? '';
        }
      });
    },
    { deep: true }
  );

  function handleCoatingCodeChange(index: number, newCode: string) {
    const detail = offerStore.offer.offerDetails[index];

    const newCoating = coatingStore.findCoatingByDiameterAndCode(detail.diameter, newCode, detail.toolType.toolTypeName);

    if (newCoating) {
      detail.coatingPrice = newCoating;
      detail.coatingNetPrice = newCoating.price;
      detail.isCoatingPriceManual = false;
      console.log('[coating updated]', newCoating);
    } else {
      // Opcjonalnie: jeśli "Brak pokrycia"
      detail.coatingPrice = {
        id: 0,
        diameter: detail.diameter ?? 0,
        price: 0,
        coatingType: {
          id: 0,
          mastermetCode: 'none',
        },
      };
      detail.coatingNetPrice = 0;
    }
  }

  /*
Koniec nowej logiki
*/



  const toolRows = ref<HTMLElement[]>([]);


  const scrollToNewTool = async () => {
    offerStore.addToolRow();
    await nextTick();
    const lastRow = toolRows.value.at(-1);
    if (lastRow) {
      lastRow.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  onMounted(() => {
    customerStore.fetchCustomers();
    toolStore.fetchTools();
    coatingStore.fetchCoatings();
  });

  const selectFile = (tool: Tool) => {
    if (selectedFileModalIndex.value !== null) {
      offerStore.offer.offerDetails[selectedFileModalIndex.value].tool = tool;
    }
    isFilesModalOpen.value = false;
    selectedFileModalIndex.value = null;
  };

  const handleFilesModalClose = () => {
    const idx = selectedFileModalIndex.value;
    if (idx === null || idx === undefined) return;

    const detail = offerStore.offer.offerDetails?.[idx];
    if (!detail) return;

    if (detail.tool?.id === null) {
      detail.toolType = { id: 1, toolTypeName: 'Frez Walcowy' };
    }

    isFilesModalOpen.value = false;
    selectedFileModalIndex.value = null;
  };

  // const openFilesModal = (index: number) => {
  //   selectedFileModalIndex.value = index;
  //   isFilesModalOpen.value = true;
  // };
</script>

<template>
  <SelectModal
    :isOpen="isFilesModalOpen"
    title="Wybierz kartotekę"
    searchPlaceholder="Wyszukaj kartotekę..."
    :items="toolStore.files"
    :onSelect="selectFile"
    :onClose="handleFilesModalClose"
  />
  <form @submit.prevent="offerStore.saveOffer">
    <OfferFormHeader />

    <div
      class="overflow-x-auto max-h-128 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300"
    >
      <table class="w-[100%] border-separate border-spacing-0 table-fixed">
        <OfferFormTableHeader />
        <tbody class="text-gray-600 text-sm">
          <OfferFormRow
            v-for="(detail, index) in offerStore.offer.offerDetails"
            :key="index"
            :detail="detail"
            :index="index"
            :open-files-modal="openFilesModal"
          />
        </tbody>
      </table>
    </div>
    <FormActionsSection :scrollToNewTool="scrollToNewTool" :closeModal="closeModal" />
    <!-- Przycisk zapisu oferty -->
  </form>
</template>
