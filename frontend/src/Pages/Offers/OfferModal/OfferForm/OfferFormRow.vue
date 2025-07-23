<script setup lang="ts">
  import useCoatingStore from '@/store/coating';
  import { useOfferStore } from '@/store/offer';
  import useToolsStore from '@/store/tools';
  import { useUserStore } from '@/store/user';
  import { OfferDetail, ToolType } from '@/types/types';
  import Button from '@/components/Button.vue';
  import { watch } from 'vue';

  const props = defineProps<{
    detail: OfferDetail;
    index: number;
    openFilesModal: (index: number) => void;
  }>();

  watch(
    () => props.detail.toolType?.toolTypeName,
    (newType, oldType) => {
      if (newType === 'Kartoteka' && oldType !== 'Kartoteka') {
        props.openFilesModal(props.index);
      }
    }
  );

  const offerStore = useOfferStore();
  const userStore = useUserStore();
  const toolStore = useToolsStore();
  const coatingStore = useCoatingStore();

  const isCalculatedTool = (detail: OfferDetail) => {
    return (
      detail.toolType?.toolTypeName !== undefined &&
      detail.toolType?.toolTypeName !== 'Niestandardowe' &&
      detail.toolType?.toolTypeName !== 'Kartoteka'
    );
  };

  function handleCoatingCodeChange(index: number, newCode: string) {
    const detail = offerStore.offer.offerDetails[index];

    const newCoating = coatingStore.findCoatingByDiameterAndCode(detail.diameter, newCode, detail.toolType.toolTypeName);

    if (newCoating) {
      detail.coatingPrice = newCoating;
      detail.coatingNetPrice = newCoating.price;
      detail.isCoatingPriceManual = false; // jeśli potrzebujesz
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
</script>

<template>
  <tr :key="index" ref="toolRows">
    <td class="border border-gray-300 px-3">
      {{ index + 1 }}
    </td>

    <!-- Typ narzędzia -->
    <td class="border border-gray-300 px-3">
      <select
        :key="detail.toolType + '-' + index"
        v-model="detail.toolType"
        class="w-full p-2 border rounded text-[11px]"
      >
        <option disabled value="">Wybierz typ narzędzia</option>
        <option
          v-for="toolType in toolStore.toolTypes as ToolType[]"
          :key="toolType.id"
          :value="toolType"
        >
          {{ toolType.toolTypeName }}
        </option>
      </select>
    </td>
    <td class="border border-gray-300 px-3">
      <input
        type="text"
        v-model="detail.symbol"
        :disabled="isCalculatedTool(detail)"
        class="w-full p-2 border rounded text-[11px]"
      />
    </td>

    <!-- Ilość ostrzy -->
    <td class="border border-gray-300 px-3">
      <select
        :key="detail.flutesNumber + '-' + detail.toolType + '-' + index"
        v-model="detail.flutesNumber"
        :class="[
          'w-full p-2 border rounded text-[11px]',
          !isCalculatedTool(detail)
            ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
            : 'bg-white text-black cursor-pointer',
        ]"
        :disabled="!isCalculatedTool(detail)"
      >
        <option disabled value="">Ilość ostrzy</option>
        <option
          v-for="flute in toolStore.getUniqueFlutesNumbers(detail.toolType)"
          :key="flute"
          :value="flute"
        >
          {{ flute }}
        </option>
      </select>
    </td>
    <!-- Średnica -->
    <td class="border border-gray-300 px-3">
      <select
        :key="detail.toolType + '-' + detail.flutesNumber + '-' + index"
        v-model="detail.diameter"
        :class="[
          'w-full p-2 border rounded text-[11px]',
          !isCalculatedTool(detail)
            ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
            : 'bg-white text-black cursor-pointer',
        ]"
        :disabled="!isCalculatedTool(detail)"
      >
        <option disabled value="">Średnica</option>
        <option
          v-for="{ value, label } in toolStore.getDiameterOptions(
            detail.toolType,
            detail.flutesNumber
          )"
          :key="value"
          :value="value"
        >
          {{ label }}
        </option>
      </select>
    </td>
    <!-- Promień -->
    <td class="border border-gray-300 px-3">
      <input
        :disabled="!offerStore.isRadiusEndMill(detail)"
        min="0"
        type="number"
        step="0.01"
        v-model="detail.radius"
        :value="offerStore.getRadius(detail)"
        :class="[
          'w-full p-2 border rounded text-[11px]',
          !isCalculatedTool(detail) || !offerStore.isRadiusEndMill(detail)
            ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
            : 'bg-white text-black cursor-pointer',
        ]"
        placeholder="Promień"
      />
    </td>
    <!-- Wariant ostrzenia -->
    <td class="border border-gray-300 px-3">
      <select
        :key="detail.toolType + '-' + detail.flutesNumber + '-' + detail.diameter + '-' + index"
        v-model="detail.regrindingOption"
        :class="[
          'w-full p-2 border rounded text-[11px]',
          !isCalculatedTool(detail)
            ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
            : 'bg-white text-black cursor-pointer',
        ]"
      >
        <option
          v-for="{ key, label } in toolStore.getRegrindingOptions(
            detail.toolType,
            detail.flutesNumber,
            detail.diameter
          )"
          :key="key"
          :value="key"
        >
          {{ label }}
        </option>
      </select>
    </td>
    <!-- Cena jednostkowa ostrzenia netto -->
    <td class="border border-gray-300 px-3">
      <input
        :key="
          detail.toolType +
          '-' +
          detail.flutesNumber +
          '-' +
          detail.diameter +
          '-' +
          detail.regrindingOption +
          '-' +
          index
        "
        :disabled="!userStore.isCreator()"
        type="number"
        step="0.01"
        v-model="detail.toolNetPrice"
        @input="detail.isToolPriceManual = true"
        class="w-full p-2 border rounded text-[11px]"
      />
    </td>
    <!-- Pokrycie -->
    <td class="border border-gray-300 px-3">
      <select
        :value="detail.coatingPrice?.coatingType?.mastermetCode"
        class="w-full p-2 border rounded text-[11px]"
        @change="handleCoatingCodeChange(index, ($event.target as HTMLInputElement).value)"
      >
        <option value="none" selected>Brak pokrycia</option>
        <option
          v-for="coatingType in coatingStore.coatingTypes"
          :key="coatingType.id"
          :value="coatingType.mastermetCode"
        >
          {{ coatingType.mastermetCode }}
        </option>
      </select>
    </td>
    <!-- Cena jednostkowa pokrycia netto -->
    <td class="border border-gray-300 px-3">
      <input
        :disabled="!userStore.isCreator()"
        type="number"
        step="0.01"
        v-model="detail.coatingNetPrice"
        class="w-full p-2 border rounded text-[11px]"
      />
    </td>
    <!-- Ilość -->
    <td class="border border-gray-300 px-3">
      <input
        type="number"
        v-model="detail.quantity"
        class="w-full p-2 border rounded text-[11px]"
        placeholder="Ilość"
        min="1"
        step="1"
      />
    </td>
    <!-- Rabat -->
    <td class="border border-gray-300 px-3">
      <input
        type="number"
        v-model="detail.discount"
        class="w-full p-2 border rounded text-[11px]"
        placeholder="Rabat (%)"
        :max="!userStore.isCreator() ? 10 : undefined"
        :disabled="offerStore.offer.globalDiscount !== 0"
      />
    </td>
    <!-- Cena całkowita netto -->
    <td class="border border-gray-300 px-3">
      {{ offerStore.getTotalNetDetailPrice(detail) }}
    </td>
    <!-- Cena całkowita brutto -->
    <!-- <td class="border border-gray-300 p-3"> -->
    <!-- {{ (Number(offerStore.getTotalNetDetailPrice(detail)) * 1.23).toFixed(2) }} PLN -->
    <!-- </td>  -->
    <!-- Opis -->
    <td class="border border-gray-300 px-3">
      <textarea
        v-model="detail.description"
        class="w-full p-2 border rounded text-left resize-none"
        rows="2"
      >
                      Opis pozycji
                      </textarea
      >
    </td>
    <!-- Akcja -->
    <td class="border border-gray-300 px-3">
      <Button @click="offerStore.removeToolRow(index)" variant="danger"> Usuń </Button>
    </td>
  </tr>
</template>
