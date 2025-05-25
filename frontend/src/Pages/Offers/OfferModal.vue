<script setup>
  import useOfferStore from '../../store/offer';
  import useCustomerStore from '../../store/customer';
  import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
  import { onMounted } from 'vue';
  import useToolsStore from '../../store/tools';
  import useCoatingStore from '../../store/coating';
  import { ref, computed, nextTick } from 'vue';
  import Button from '@/components/Button.vue';

  const isCustomerModalOpen = ref(false);
  const isFilesModalOpen = ref(false);
  const selectedFileModalIndex = ref(null);
  const searchQuery = ref('');

  const toolRows = ref([]);

  const inputRef = ref(null);

  const scrollToNewTool = async () => {
    offerStore.addToolRow();
    await nextTick();
    const lastRow = toolRows.value.at(-1);
    if (lastRow) {
      lastRow.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  };

  // Filtrowanie kontrahentów na podstawie wpisanego tekstu w polu wyszukiwania
  const filteredCustomers = computed(() => {
    if (!searchQuery.value) {
      return customerStore.customers;
    }
    return customerStore.customers.filter((customer) =>
      customer.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  });

  // Funkcja do wyboru kontrahenta i zamknięcia modala wyboru kontrahenta
  const selectCustomer = (customerId) => {
    offerStore.offer.customer_id = customerId;
    isCustomerModalOpen.value = false;
  };

  const filteredFiles = computed(() => {
    if (!searchQuery.value) {
      return toolStore.files;
    }
    return toolStore.files.filter((file) =>
      file.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
  });

  // Funkcja do wyboru kontrahenta i zamknięcia modala wyboru kontrahenta
  const selectFile = (fileId) => {
    if (selectedFileModalIndex.value !== null) {
      offerStore.offerDetails[selectedFileModalIndex.value].fileId = fileId;
      offerStore.applyFileDataToDetail(selectedFileModalIndex.value);
    }
    isFilesModalOpen.value = false;
    selectedFileModalIndex.value = null;
  };

  const handleFilesModalClose = () => {
    if (offerStore.offerDetails[selectedFileModalIndex.value].fileId === null) {
      offerStore.resetDetail(selectedFileModalIndex.value);
      offerStore.offerDetails[selectedFileModalIndex.value].toolType = null;
    }
    isFilesModalOpen.value = false;
    selectedFileModalIndex.value = null;
  };

  const offerStore = useOfferStore();
  const customerStore = useCustomerStore();
  const toolStore = useToolsStore();
  const coatingStore = useCoatingStore();

  onMounted(() => {
    customerStore.fetchCustomers();
    toolStore.fetchTools();
    coatingStore.fetchCoatings();
  });

  const handleToolTypeChange = (index) => {
    offerStore.resetDetail(index);
    offerStore.updateToolNetPrice(index);
    offerStore.calculateOfferTotalNetPrice();
    offerStore.calculateOfferTotalNetPrice();
    offerStore.setToolGeometryIdIfCustom(index);

    console.log(offerStore.offerDetails[index]);

    if (offerStore.offerDetails[index].toolType === 'Kartoteka') {
      selectedFileModalIndex.value = index;
      isFilesModalOpen.value = true;
    }
  };

  const handleFlutesNumberChange = (index) => {
    offerStore.resetDetail(index);
    offerStore.updateToolNetPrice(index);
    offerStore.calculateOfferTotalNetPrice();
    offerStore.calculateOfferTotalNetPrice();
  };

  const handleDiameterChange = (index) => {
    offerStore.updateToolNetPrice(index);
    offerStore.updateCoatingNetPrice(index);
    offerStore.calculateOfferTotalNetPrice();
    offerStore.calculateOfferTotalNetPrice();
  };

  const handleCoatingCodeChange = (index) => {
    offerStore.updateCoatingNetPrice(index);
    offerStore.calculateOfferTotalNetPrice();
    offerStore.calculateOfferTotalNetPrice();
  };

  const handleRegrindingOptionChange = (index) => {
    offerStore.updateToolNetPrice(index);
    offerStore.calculateOfferTotalNetPrice();
    offerStore.calculateOfferTotalNetPrice();
  };

  const handleRadiusChange = (index) => {
    offerStore.updateToolNetPrice(index);
    offerStore.calculateOfferTotalNetPrice();
    offerStore.calculateOfferTotalNetPrice();
  };

  const handleQuantityChange = () => {
    offerStore.calculateOfferTotalNetPrice();
  };

  const handleManualToolPriceChange = () => {
    offerStore.calculateOfferTotalNetPrice();
  };

  const handleManualCoatingPriceChange = () => {
    offerStore.calculateOfferTotalNetPrice();
  };

  const handleDiscountChange = () => {
    offerStore.calculateOfferTotalNetPrice();
    offerStore.offer.globalDiscount = 0;
  };
  const handleGlobalDiscountChange = (e) => {
    const value = parseFloat(e.target.value);

if (isNaN(value) || value < 0) {
  offerStore.offer.globalDiscount = 0;
}
    offerStore.applyGlobalDiscount();
  };
</script>

<template>
  <div>
    <TransitionRoot appear :show="isCustomerModalOpen" as="template">
      <Dialog as="div" class="relative z-20" :initialFocus="inputRef">
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="fixed inset-0 flex items-center justify-center">
          <DialogPanel
            class="w-full max-w-2xl bg-white p-6 rounded-lg shadow-lg overflow-y-auto max-h-[80vh] flex flex-col"
          >
            <DialogTitle class="text-lg font-semibold mb-4">Wybierz kontrahenta</DialogTitle>

            <!-- Pole wyszukiwania -->
            <div class="mb-4">
              <input
                ref="inputRef"
                v-model="searchQuery"
                type="text"
                placeholder="Wyszukaj kontrahenta..."
                class="w-full p-2 border border-gray-300 rounded"
              />
            </div>

            <!-- Lista kontrahentów -->
            <ul class="divide-y divide-gray-200 flex-1 overflow-y-auto">
              <li
                v-for="customer in filteredCustomers"
                :key="customer.id"
                class="p-2 hover:bg-gray-100 cursor-pointer"
                @click="selectCustomer(customer.id)"
              >
                <div class="font-medium">{{ customer.name }}</div>
              </li>
            </ul>

            <!-- Przycisk anulowania na stałe na dole -->
            <div class="mt-4">
              <Button
              @click="isCustomerModalOpen = false"
              variant="secondary"
              >
            Anuluj

              </Button>
            </div>
          </DialogPanel>
        </div>
      </Dialog>
    </TransitionRoot>

    <TransitionRoot appear :show="isFilesModalOpen" as="template">
      <Dialog as="div" class="relative z-20">
        <div class="fixed inset-0 bg-black/50"></div>
        <div class="fixed inset-0 flex items-center justify-center">
          <DialogPanel
            class="w-full max-w-2xl bg-white p-6 rounded-lg shadow-lg overflow-y-auto max-h-[80vh] flex flex-col"
          >
            <DialogTitle class="text-lg font-semibold mb-4">Wybierz kartotekę</DialogTitle>

            <!-- Pole wyszukiwania -->
            <div class="mb-4">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Wyszukaj kartotekę..."
                class="w-full p-2 border border-gray-300 rounded"
              />
            </div>

            <!-- Lista kontrahentów -->
            <ul class="divide-y divide-gray-200 flex-1 overflow-y-auto">
              <li
                v-for="file in filteredFiles"
                :key="file.id"
                class="p-2 hover:bg-gray-100 cursor-pointer"
                @click="selectFile(file.id)"
              >
                <div class="font-medium">{{ file.name }}</div>
              </li>
            </ul>

            <!-- Przycisk anulowania na stałe na dole -->
            <div class="mt-4">
              <Button
              @click="handleFilesModalClose(index)"
              variant="secondary"
              >
            Anuluj

              </Button>
            </div>
          </DialogPanel>
        </div>
      </Dialog>
    </TransitionRoot>
    <div
      v-if="offerStore.isModalOpen"
      class="fixed inset-0 z-10 flex items-center justify-center bg-black/50"
    >
      <div
        class="w-full min-h-screen max-w-8xl bg-[#D3D3D3] p-8 rounded-lg shadow-lg overflow-y-auto h-full"
      >
        <h2 class="text-lg font-semibold mb-4">Oferta</h2>
        <form @submit.prevent="offerStore.saveOffer">
          <div class="flex items-center gap-5 justify-between p-4 bg-gray-100 rounded-lg shadow-md">
            <div class="mb-4 max-w-3xl">
              <label class="block text-sm font-medium text-gray-700 mb-1">Kontrahent</label>
              <Button
              @click="isCustomerModalOpen = true"
              variant="success"
              >
              {{
                  customerStore.customers.find((c) => c.id === offerStore.offer.customer_id)
                    ?.name || 'Wybierz kontrahenta'
              }}
              </Button>
            </div>

            <div class="flex flex-col items-end">
              <label class="block text-sm font-medium text-gray-700 mb-1">Status oferty</label>
              <select v-model="offerStore.offer.status_id" class="w-full p-2 border rounded">
                <option v-for="status in offerStore.statuses" :key="status.id" :value="status.id">
                  {{ status.name }}
                </option>
              </select>
            </div>
            <div class="flex flex-col items-end">
              <label class="block text-sm font-medium text-gray-700 mb-1">Kwota netto oferty</label>
              <span
                class="text-lg font-semibold text-gray-800 bg-gray-200 px-3 py-1 rounded-lg shadow-sm"
              >
                {{ offerStore.offer.total_net_price }} PLN
              </span>
            </div>
            <div class="flex flex-col items-end">
              <label class="block text-sm font-medium text-gray-700 mb-1"
                >Rabat całej oferty (%)</label
              >
              <input
                type="number"
                v-model="offerStore.offer.globalDiscount"
                class="w-full p-2 border rounded"
                placeholder="Rabat całej oferty"
                min="0"
                step="0.1"
                @input="handleGlobalDiscountChange"
              />
            </div>
          </div>
          <div
            class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300"
          >
            <table class="w-full border-separate border-spacing-0">
              <thead class="bg-gray-100 font-normal sticky top-0 z-10">
                <tr
                  class="bg-gray-100 font-normal text-gray-700 uppercase text-sm leading-normal rounded-t-lg"
                >
                  <th
                    class="bg-gray-100 font-normal text-gray-700 uppercase text-sm leading-normal rounded-t-lg"
                  >
                    Lp.
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left min-w-[150px]">
                    Typ narzędzia
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left min-w-[150px]">
                    Symbol
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left min-w-[140px]">
                    Ilość ostrzy
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left min-w-[80px]">
                    Średnica
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left">Promień</th>
                  <th class="border font-normal border-gray-300 p-3 text-left min-w-[100px]">
                    Wariant ostrzenia
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left">
                    Cena <span class="font-bold">katalogowa</span> ostrzenia netto [PLN]
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left min-w-[100px]">Pokrycie</th>
                  <th class="border font-normal font-normal border-gray-300 p-3 text-left">
                    Cena <span class="font-extrabold">katalogowa</span> pokrycia netto [PLN]
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left min-w-[80px]">
                    Ilość
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left min-w-[80px]">
                    Rabat
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left">
                    Cena całkowita netto [PLN]
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left">
                    Cena całkowita brutto [PLN]
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left min-w-[200px]">
                    Opis
                  </th>
                  <th class="border font-normal border-gray-300 p-3 text-left">Akcja</th>
                </tr>
              </thead>
              <tbody class="text-gray-600 text-sm">
                <tr v-for="(detail, index) in offerStore.offerDetails" :key="index" ref="toolRows">
                  <td class="border border-gray-300 p-3">
                    {{ index + 1 }}
                  </td>

                  <!-- Typ narzędzia -->
                  <td class="border border-gray-300 p-3">
                    <select
                      v-model="detail.toolType"
                      class="w-full p-2 border rounded text-[11px]"
                      @change="handleToolTypeChange(index)"
                    >
                      <option disabled value="">Wybierz typ narzędzia</option>
                      <option
                        v-for="toolType in toolStore.toolTypes"
                        :key="toolType.id"
                        :value="toolType.tool_type_name"
                      >
                        {{ toolType.tool_type_name }}
                      </option>
                    </select>
                  </td>
                  <td class="border border-gray-300 p-3 ">
                    <input
                      type="text"
                      v-model="detail.symbol"
                      :disabled="!offerStore.isCustom(detail)"
                      class="w-full p-2 border rounded text-[11px]"
                      :value="offerStore.getSymbolForDetail(detail)"
                    />
                  </td>

                  <!-- Ilość ostrzy -->
                  <td class="border border-gray-300 p-3">
                    <select
                      v-model="detail.flutesNumber"
                      :class="[
                        'w-full p-2 border rounded text-[11px]',
                        offerStore.isCustom(detail)
                          ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
                          : 'bg-white text-black cursor-pointer',
                      ]"
                      @change="handleFlutesNumberChange(index)"
                      :disabled="offerStore.isCustom(detail)"
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
                      v-model="detail.diameter"
                      :class="[
                        'w-full p-2 border rounded text-[11px]',
                        offerStore.isCustom(detail)
                          ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
                          : 'bg-white text-black cursor-pointer',
                      ]"
                      @change="handleDiameterChange(index)"
                      :disabled="offerStore.isCustom(detail)"
                    >
                      <option disabled value="">Średnica</option>
                      <option
                        v-for="diameter in toolStore.getUniqueDiameters(
                          detail.toolType,
                          detail.flutesNumber
                        )"
                        :key="diameter"
                        :value="diameter"
                      >
                        {{ diameter }}
                      </option>
                    </select>
                  </td>
                  <!-- Promień -->
                  <td class="border border-gray-300 p-3">
                    <input
                      :disabled="!offerStore.isRadiusEndMill(detail)"
                      min="0"
                      type="number"
                      v-model="detail.radius"
                      :value="offerStore.getRadius(detail)"
                      :class="[
                        'w-full p-2 border rounded text-[11px]',
                        offerStore.isCustom(detail) || !offerStore.isRadiusEndMill(detail)
                          ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
                          : 'bg-white text-black cursor-pointer',
                      ]"
                      placeholder="Promień"
                      @change="handleRadiusChange(index)"
                    />
                  </td>
                  <!-- Wariant ostrzenia -->
                  <td class="border border-gray-300 p-3">
                    <select
                      v-model="detail.regrinding_option"
                      :class="[
                        'w-full p-2 border rounded text-[11px]',
                        offerStore.isCustom(detail)
                          ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
                          : 'bg-white text-black cursor-pointer',
                      ]"
                      @change="handleRegrindingOptionChange(index)"
                    >
                      <option
                        v-for="{ key, label } in offerStore.getRegrindingOptions(detail)"
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
                      type="number"
                      step="0.01"
                      v-model="detail.tool_net_price"
                      class="w-full p-2 border rounded text-[11px]"
                      @input="handleManualToolPriceChange(index)"
                    />
                  </td>
                  <!-- Pokrycie -->
                  <td class="border border-gray-300 p-3">
                    <select
                      v-model="detail.coatingCode"
                      class="w-full p-2 border rounded text-[11px]"
                      @change="handleCoatingCodeChange(index)"
                    >
                      <option value="none" selected>Brak pokrycia</option>
                      <option
                        v-for="coatingType in coatingStore.coatingTypes"
                        :key="coatingType.mastermet_code"
                        :value="coatingType.mastermet_code"
                      >
                        {{ coatingType.mastermet_code }}
                      </option>
                    </select>
                  </td>
                  <!-- Cena jednostkowa pokrycia netto -->
                  <td class="border border-gray-300 p-3">
                    <input
                      type="number"
                      step="0.01"
                      v-model="detail.coating_net_price"
                      class="w-full p-2 border rounded text-[11px]"
                      @input="handleManualCoatingPriceChange(index)"
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
                      @change="handleQuantityChange()"
                    />
                  </td>
                  <!-- Rabat -->
                  <td class="border border-gray-300 p-3">
                    <input
                      type="number"
                      v-model="detail.discount"
                      class="w-full p-2 border rounded text-[11px]"
                      placeholder="Rabat (%)"
                      @change="handleDiscountChange()"
                      :disabled="offerStore.offer.globalDiscount !== 0"
                    />
                  </td>
                  <!-- Cena całkowita netto -->
                  <td class="border border-gray-300 p-3">
                    {{ offerStore.getTotalNetDetailPrice(detail) }} PLN
                  </td>
                  <!-- Cena całkowita brutto -->
                  <td class="border border-gray-300 p-3">
                    {{ (offerStore.getTotalNetDetailPrice(detail) * 1.23).toFixed(2) }} PLN
                  </td>
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
                    <Button
                    @click="offerStore.removeToolRow(index)"
                    variant="danger"
                    >
                    Usuń
                    </Button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Przycisk dodawania nowego narzędzia -->
          <div class="mt-4">
            <Button
                    @click="scrollToNewTool"
                    variant="success"
                    >
                    Dodaj narzędzie
            </Button>
            <div class="flex justify-end space-x-2 mt-4">
              <Button
                    @click="offerStore.closeModal"
                    variant="secondary"
                    >
                    Anuluj
            </Button>

            <Button
                    type="submit"

                    >
                    Zapisz
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
