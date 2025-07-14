import { watch, nextTick } from 'vue';
import { useOfferStore } from '@/store/offer';
import useToolsStore from '@/store/tools';
import useCoatingStore from '@/store/coating';
import { useDiscountWatcher } from '@/composables/useDiscountWatcher';
import type { OfferDetail, Tool } from '@/types/types';

export function useOfferLogic() {
  const offerStore = useOfferStore();
  const toolStore = useToolsStore();
  const coatingStore = useCoatingStore();

  const isCalculatedTool = (detail: OfferDetail) => {
    return (
      detail.toolType?.toolTypeName !== undefined &&
      detail.toolType?.toolTypeName !== 'Niestandardowe' &&
      detail.toolType?.toolTypeName !== 'Kartoteka'
    );
  };

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

  function getTwistDrillDiameterLabel(diameter: number, allDiameters: number[]): string {
    const unique = [...new Set(allDiameters)].sort((a, b) => a - b);
    const index = unique.indexOf(diameter);

    if (index === 0) {
      return `<=${diameter}`;
    }

    const lower = +(unique[index - 1] + 0.1).toFixed(1);
    return `${lower} - ${diameter}`;
  }

  // Watchery

  watch(
    () => offerStore.offer.offerDetails,
    (newDetails) => {
      newDetails.forEach((detail: OfferDetail) => {
        if (
          isCalculatedTool(detail) &&
          detail.toolType?.toolTypeName &&
          detail.flutesNumber &&
          detail.diameter
        ) {
          const normalizedType = detail.toolType.toolTypeName.toLowerCase().replace('ó', 'o').trim();
          const isTwistDrill = normalizedType === 'wiertlo krete';

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

          detail.symbol = `${detail.toolType.toolTypeName} Z${detail.flutesNumber} D${diameterLabel}`;

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
            detail.coatingPrice?.coatingType?.mastermetCode ?? null
          );

          if (newCoating) {
            detail.coatingPrice = newCoating;
          }

          detail.coatingNetPrice = detail.coatingPrice?.price ?? 0;
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
    }
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

    const newCoating = coatingStore.findCoatingByDiameterAndCode(detail.diameter, newCode);

    if (newCoating) {
      detail.coatingPrice = newCoating;
      detail.coatingNetPrice = newCoating.price;
      detail.isCoatingPriceManual = false;
    } else {
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

  // Eksportujemy funkcje, które mogą być przydatne na zewnątrz
  return {
    isCalculatedTool,
    setDetailToolNetPrice,
    handleCoatingCodeChange,
  };
}
