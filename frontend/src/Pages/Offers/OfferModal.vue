<script setup>
  import useOfferStore from '../../store/offer';
  import useCustomerStore from '../../store/customer';
  import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
  import { onMounted } from 'vue';
  import useToolsStore from '../../store/tools';
  import useCoatingStore from '../../store/coating';

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
  };
</script>

<template>
  <TransitionRoot appear :show="offerStore.isModalOpen" as="template">
    <Dialog as="div" class="relative z-10" @close="offerStore.closeModal" :static="true">
      <div class="fixed inset-0 bg-black/50"></div>
      <div class="fixed inset-0 flex items-center justify-center">
        <DialogPanel class="w-full min-h-screen max-w-8xl bg-[#D3D3D3] p-8 rounded-lg shadow-lg">
          <DialogTitle class="text-lg font-semibold">Oferta</DialogTitle>
          <form @submit.prevent="offerStore.saveOffer">
            <div
              class="flex items-center gap-5 justify-between p-4 bg-gray-100 rounded-lg shadow-md"
            >
              <div class="mb-4 max-w-3xl">
                <label class="block text-sm font-medium text-gray-700 mb-1">Kontrahent</label>
                <select
                  v-model="offerStore.offer.customer_id"
                  class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                  <option disabled value="">Wybierz kontrahenta</option>
                  <option
                    v-for="customer in customerStore.customers"
                    :key="customer.id"
                    :value="customer.id"
                  >
                    {{ customer.name }}
                  </option>
                </select>
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
                <label class="block text-sm font-medium text-gray-700 mb-1"
                  >Kwota netto oferty</label
                >
                <span
                  class="text-lg font-semibold text-gray-800 bg-gray-200 px-3 py-1 rounded-lg shadow-sm"
                >
                  {{ offerStore.offer.total_net_price }} PLN
                </span>
              </div>
            </div>
            <div
              class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300"
            >
              <table class="w-full border-separate border-spacing-0">
                <thead class="bg-gray-100 sticky top-0 z-10">
                  <tr
                    class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal rounded-t-lg"
                  >
                    <th class="border border-gray-300 p-3 text-left min-w-[150px]">
                      Typ narzędzia
                    </th>
                    <th class="border border-gray-300 p-3 text-left min-w-[140px]">Ilość ostrzy</th>
                    <th class="border border-gray-300 p-3 text-left min-w-[80px]">Średnica</th>
                    <th class="border border-gray-300 p-3 text-left">Promień</th>
                    <th class="border border-gray-300 p-3 text-left min-w-[100px]">
                      Wariant ostrzenia
                    </th>
                    <th class="border border-gray-300 p-3 text-left">Cena ostrzenia netto [PLN]</th>
                    <th class="border border-gray-300 p-3 text-left">Pokrycie</th>
                    <th class="border border-gray-300 p-3 text-left">
                      Cena jednostkowa pokrycia netto [PLN]
                    </th>
                    <th class="border border-gray-300 p-3 text-left min-w-[80px]">Ilość</th>
                    <th class="border border-gray-300 p-3 text-left min-w-[80px]">Rabat</th>
                    <th class="border border-gray-300 p-3 text-left">Cena całkowita netto [PLN]</th>
                    <th class="border border-gray-300 p-3 text-left">
                      Cena całkowita brutto [PLN]
                    </th>
                    <th class="border border-gray-300 p-3 text-left min-w-[200px]">Opis</th>
                    <th class="border border-gray-300 p-3 text-left">Akcja</th>
                  </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                  <tr v-for="(detail, index) in offerStore.offerDetails" :key="index">
                    <!-- Typ narzędzia -->
                    <td class="border border-gray-300 p-3">
                      <select
                        v-model="detail.toolType"
                        class="w-full p-2 border rounded"
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
                    <!-- Ilość ostrzy -->
                    <td class="border border-gray-300 p-3">
                      <select
                        v-model="detail.flutesNumber"
                        :class="[
      'w-full p-2 border rounded',
      offerStore.isCustom(detail)
        ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
        : 'bg-white text-black cursor-pointer'
    ]"
                        @change="handleFlutesNumberChange(index)"
                        :disabled=offerStore.isCustom(detail)
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
      'w-full p-2 border rounded',
      offerStore.isCustom(detail)
        ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
        : 'bg-white text-black cursor-pointer'
    ]"
                        @change="handleDiameterChange(index)"
                        :disabled=offerStore.isCustom(detail)
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
                        :class="[
      'w-full p-2 border rounded',
      offerStore.isCustom(detail)
        ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
        : 'bg-white text-black cursor-pointer'
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
      'w-full p-2 border rounded',
      offerStore.isCustom(detail)
        ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
        : 'bg-white text-black cursor-pointer'
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
                        class="w-full p-2 border rounded text-right"
                        @input="handleManualToolPriceChange(index)"
                      />
                    </td>
                    <!-- Pokrycie -->
                    <td class="border border-gray-300 p-3">
                      <select
                        v-model="detail.coatingCode"
                        class="w-full p-2 border rounded"
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
                        class="w-full p-2 border rounded text-right"
                        @input="handleManualCoatingPriceChange(index)"
                      />
                    </td>
                    <!-- Ilość -->
                    <td class="border border-gray-300 p-3">
                      <input
                        type="number"
                        v-model="detail.quantity"
                        class="w-full p-2 border rounded"
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
                        class="w-full p-2 border rounded"
                        placeholder="Rabat (%)"
                        @change="handleDiscountChange()"
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
                        class="w-full p-2 border rounded text-right resize-none"
                        rows="2"
                      >
                      Opis pozycji
                      </textarea>
                    </td>
                    <!-- Akcja -->
                    <td class="border border-gray-300 p-3">
                      <button
                        type="button"
                        @click="offerStore.removeToolRow(index)"
                        class="px-2 py-1 bg-red-500 text-white rounded"
                      >
                        Usuń
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- Przycisk dodawania nowego narzędzia -->
            <div class="mt-4">
              <button
                type="button"
                @click="offerStore.addToolRow"
                class="px-4 py-2 bg-green-600 text-white rounded"
              >
                Dodaj narzędzie
              </button>
            </div>
            <!-- Przycisk zapisu oferty -->
            <div class="flex justify-end space-x-2 mt-4">
              <button
                type="button"
                @click="offerStore.closeModal"
                class="px-4 py-2 bg-gray-300 rounded"
              >
                Anuluj
              </button>
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Zapisz</button>

  <button
    type="button"
    @click="offerStore.generatePdf()"
    class="px-4 py-2 bg-blue-600 text-white rounded shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed"
    :disabled="offerStore.offer.id === null"
  >
    Generuj PDF
  </button>



            </div>
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
        </DialogPanel>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
