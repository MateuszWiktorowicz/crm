<script setup lang="ts">
  import { useOfferStore } from '@/store/offer';
  import useCustomerStore from '../../../store/customer';
  import { onMounted, watch } from 'vue';
  import useToolsStore from '../../../store/tools';
  import useCoatingStore from '../../../store/coating';
  import { ref, computed, nextTick } from 'vue';
  import Button from '@/components/Button.vue';
  import SelectModal from '@/components/SelectModal.vue';
  import OfferFormHeader from './OfferForm/OfferFormHeader.vue';
  import { CoatingType, OfferDetail, Tool, ToolType } from '@/types/types';

  /*
Początek nowej logiki
*/
  defineProps<{
    isModalOpen: boolean;
    closeModal: () => void;
  }>();

  const offerStore = useOfferStore();
  const customerStore = useCustomerStore();
  const toolStore = useToolsStore();
  const coatingStore = useCoatingStore();

  const isCalculatedTool = (detail: OfferDetail) => {
    return (
      detail.toolType?.toolTypeName !== undefined &&
      detail.toolType?.toolTypeName !== 'Niestandardowe' &&
      detail.toolType?.toolTypeName !== 'Kartoteka'
    );
  };

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
          detail.symbol = `${detail.toolType.toolTypeName} Z${detail.flutesNumber} D${detail.diameter}`;
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
        // pierwsza zmiana po wejściu w edycję — ignoruj, ale wyłącz flagę
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

  function handleCoatingCodeChange(index: number, newCode: string) {
    const detail = offerStore.offer.offerDetails[index];

    const newCoating = coatingStore.findCoatingByDiameterAndCode(detail.diameter, newCode);

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

  /*
Koniec nowej logiki
*/

  const isCustomerModalOpen = ref(false);
  const isFilesModalOpen = ref(false);
  const selectedFileModalIndex = ref<number | null>(null);
  const searchQuery = ref('');

const toolRows = ref<HTMLElement[]>([]);

  const inputRef = ref(null);

  const scrollToNewTool = async () => {
    offerStore.addToolRow();
    await nextTick();
    const lastRow = toolRows.value.at(-1);
    if (lastRow) {
      lastRow.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  // Funkcja do wyboru kontrahenta i zamknięcia modala wyboru kontrahenta
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
  if (!detail) return; // jeśli brak szczegółu, przerywamy

  // Sprawdzamy, czy tool istnieje i czy ma id === null
  if (detail.tool?.id === null) {
    detail.toolType = {id: 1, toolTypeName: 'Frez Walcowy'};
  }

  isFilesModalOpen.value = false;
  selectedFileModalIndex.value = null;
};

  onMounted(() => {
    customerStore.fetchCustomers();
    toolStore.fetchTools();
    coatingStore.fetchCoatings();
  });
</script>

<template>
  <div>
    <SelectModal
      :isOpen="isFilesModalOpen"
      title="Wybierz kartotekę"
      searchPlaceholder="Wyszukaj kartotekę..."
      :items="toolStore.files"
      :onSelect="selectFile"
      :onClose="handleFilesModalClose"
    />
    <div v-if="isModalOpen" class="fixed inset-0 z-10 flex items-center justify-center bg-black/50">
      <div
        class="w-full min-h-screen max-w-8xl bg-[#D3D3D3] p-8 rounded-lg shadow-lg overflow-y-auto h-full"
      >
        <h2 class="text-lg font-semibold mb-4">Oferta</h2>
        <form @submit.prevent="offerStore.saveOffer">
          <OfferFormHeader />

          <div
            class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300"
          >
            <table class="w-[100%] border-separate border-spacing-0 table-fixed">
              <thead class="bg-gray-100 font-normal sticky top-0 z-10">
                <tr
                  class="bg-gray-100 font-normal text-gray-700 uppercase text-sm leading-normal rounded-t-lg"
                >
                  <th
                    class="bg-gray-100 font-normal text-gray-700 uppercase text-sm w-[50px] leading-normal rounded-t-lg"
                  >
                    Lp.
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left w-[150px]">
                    Typ narzędzia
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left w-[150px]">Symbol</th>
                  <th class="border font-normal border-gray-300 p-3 text-left w-[90px]">
                    Ilość ostrzy
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left w-[90px]">
                    Średnica
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left w-[80px]">Promień</th>
                  <th class="border font-normal border-gray-300 p-3 text-left w-[150px]">
                    Wariant ostrzenia
                  </th>
                  <th class="border font-normal border-gray-300 w-[120px] p-3 text-left">
                    Cena <span class="font-bold">katalogowa</span> ostrzenia netto [PLN]
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left w-[100px]">
                    Pokrycie
                  </th>
                  <th
                    class="border font-normal font-normal border-gray-300 p-3 w-[120px] text-left"
                  >
                    Cena <span class="font-extrabold">katalogowa</span> pokrycia netto [PLN]
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left w-[80px]">Ilość</th>
                  <th class="border font-normal border-gray-300 p-3 text-left w-[80px]">Rabat</th>
                  <th class="border font-normal border-gray-300 p-3 w-[120px] text-left">
                    Cena całkowita netto [PLN]
                  </th>
                  <!-- <th class="border font-normal border-gray-300 p-3 text-left">
                    Cena całkowita brutto [PLN]
                  </th> -->
                  <th class="border font-normal border-gray-300 p-3 text-left w-[200px]">Opis</th>
                  <th class="border font-normal border-gray-300 p-3 w-[100px] text-left">Akcja</th>
                </tr>
              </thead>
              <tbody class="text-gray-600 text-sm">
                <tr
                  v-for="(detail, index) in offerStore.offer.offerDetails as OfferDetail[]"
                  :key="index"
                  ref="toolRows"
                >
                  <td class="border border-gray-300 p-3">
                    {{ index + 1 }}
                  </td>

                  <!-- Typ narzędzia -->
                  <td class="border border-gray-300 p-3">
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
                  <td class="border border-gray-300 p-3">
                    <input
                      type="text"
                      v-model="detail.symbol"
                      :disabled="isCalculatedTool(detail)"
                      class="w-full p-2 border rounded text-[11px]"
                    />
                  </td>

                  <!-- Ilość ostrzy -->
                  <td class="border border-gray-300 p-3">
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
                  <td class="border border-gray-300 p-3">
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
                  <td class="border border-gray-300 p-3">
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
                  <td class="border border-gray-300 p-3">
                    <select
                      :key="
                        detail.toolType +
                        '-' +
                        detail.flutesNumber +
                        '-' +
                        detail.diameter +
                        '-' +
                        index
                      "
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
                  <td class="border border-gray-300 p-3">
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
                      type="number"
                      step="0.01"
                      v-model="detail.toolNetPrice"
                      @input="detail.isToolPriceManual = true"
                      class="w-full p-2 border rounded text-[11px]"
                    />
                  </td>
                  <!-- Pokrycie -->
                  <td class="border border-gray-300 p-3">
                    <select
                      :value="detail.coatingPrice?.coatingType?.mastermetCode"
                      class="w-full p-2 border rounded text-[11px]"
                      @change="
                        handleCoatingCodeChange(index, ($event.target as HTMLInputElement).value)
                      "
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
                  <td class="border border-gray-300 p-3">
                    <input
                      type="number"
                      step="0.01"
                      v-model="detail.coatingNetPrice"
                      class="w-full p-2 border rounded text-[11px]"
                    />
                  </td>
                  <!-- Ilość -->
                  <td class="border border-gray-300 p-3">
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
                  <td class="border border-gray-300 p-3">
                    <input
                      type="number"
                      v-model="detail.discount"
                      class="w-full p-2 border rounded text-[11px]"
                      placeholder="Rabat (%)"
                      :disabled="offerStore.offer.globalDiscount !== 0"
                    />
                  </td>
                  <!-- Cena całkowita netto -->
                  <td class="border border-gray-300 p-3">
                    {{ offerStore.getTotalNetDetailPrice(detail) }}
                  </td>
                  <!-- Cena całkowita brutto -->
                  <!-- <td class="border border-gray-300 p-3"> -->
                  <!-- {{ (Number(offerStore.getTotalNetDetailPrice(detail)) * 1.23).toFixed(2) }} PLN -->
                  <!-- </td>  -->
                  <!-- Opis -->
                  <td class="border border-gray-300 p-3">
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
                  <td class="border border-gray-300 p-3">
                    <Button @click="offerStore.removeToolRow(index)" variant="danger">
                      Usuń
                    </Button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Przycisk dodawania nowego narzędzia -->
          <div class="mt-4">
            <Button @click="scrollToNewTool" variant="success"> Dodaj narzędzie </Button>
            <div class="flex justify-end space-x-2 mt-4">
              <Button @click="closeModal" variant="secondary"> Anuluj </Button>

              <Button :key="'id' + offerStore.offer.id" type="submit">
                {{ offerStore.offer.id ? 'Edytuj' : 'Zapisz' }}
              </Button>
              <Button
                @click="offerStore.generatePdf()"
                variant="warning"
                :disabled="offerStore.offer.id === null"
              >
                Generuj PDF
              </Button>
            </div>
          </div>
          <!-- Przycisk zapisu oferty -->
        </form>
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
          Uwaga: Rabat całej oferty jest ustawiony! Jeśli chcesz zmienić rabat pojedyńczego
          narzędzia ustaw rabat globalny na 0.
        </p>
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
      </div>
    </div>
  </div>
</template>
