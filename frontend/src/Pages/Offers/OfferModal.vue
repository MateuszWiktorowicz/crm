<script setup>
import useOfferStore from "../../store/offer";
import useCustomerStore from "../../store/customer";
import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from "@headlessui/vue";
import InputError from "../../components/InputError.vue";
import { onMounted, watch } from "vue";
import useToolsStore from "../../store/tools";

const offerStore = useOfferStore();
const customerStore = useCustomerStore();
const toolStore = useToolsStore();

onMounted(() => {
  customerStore.fetchCustomers();
  toolStore.fetchTools();
});

watch(
  () => offerStore.offerDetails,
  (newDetails) => {
    newDetails.forEach((detail, index) => {
      const selectedTool = getSelectedTool(detail.toolType, detail.flutesNumber, detail.diameter);
      if (selectedTool) {
        detail.tool_net_price = (selectedTool.face_grinding_price || 0) * (detail.tool_quantity || 0);
        detail.tool_gross_price = detail.tool_net_price * 1.23; // VAT 23%
      }
    });
  },
  { deep: true }
);

// Metoda zwracająca unikatowe liczby ostrzy dla danego typu narzędzia
function getUniqueFlutesNumbers(toolType) {
  if (!toolType || !toolStore.tools) return [];
  const filtered = toolStore.tools.filter(tool => tool.tool_type_name === toolType);
  return [...new Set(filtered.map(tool => tool.flutes_number))];
}

// Metoda zwracająca unikatowe średnice dla danego typu i liczby ostrzy
function getUniqueDiameters(toolType, flutesNumber) {
  if (!toolType || !flutesNumber || !toolStore.tools) return [];
  const filtered = toolStore.tools.filter(tool =>
    tool.tool_type_name === toolType && tool.flutes_number === flutesNumber
  );
  return [...new Set(filtered.map(tool => tool.diameter))];
}

// Metoda zwracająca wybrane narzędzie na podstawie danych z wiersza
function getSelectedTool(toolType, flutesNumber, diameter) {
  if (!toolType || !flutesNumber || !diameter || !toolStore.tools) return null;
  return toolStore.tools.find(tool =>
    tool.tool_type_name === toolType &&
    tool.flutes_number === flutesNumber &&
    tool.diameter === diameter
  ) || null;
}


</script>

<template>
  <TransitionRoot appear :show="offerStore.isModalOpen" as="template">
    <Dialog as="div" class="relative z-10" @close="offerStore.closeModal">
      <div class="fixed inset-0 bg-black/50"></div>
      <div class="fixed inset-0 flex items-center justify-center">
        <DialogPanel class="w-full max-w-7xl bg-[#D3D3D3] p-6 rounded-lg shadow-lg">
          <DialogTitle class="text-lg font-semibold">Oferta</DialogTitle>
          <form @submit.prevent="offerStore.createOffer">
            <div class="mb-4 max-w-3xl">
              <label class="block text-sm font-medium">Kontrahent</label>
              <select v-model="offerStore.offer.customer_id" class="w-full p-2 border rounded">
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

            <div class="bg-white rounded min-h-50">
              <table class="w-full border-separate border-spacing-0">
                <thead class="bg-gray-100 sticky top-0 z-10">
                  <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal rounded-t-lg">
                    <th class="border border-gray-300 p-3 text-left">Typ narzędzia</th>
                    <th class="border border-gray-300 p-3 text-left">Ilość ostrzy</th>
                    <th class="border border-gray-300 p-3 text-left">Średnica</th>
                    <th class="border border-gray-300 p-3 text-left">Ilość</th>
                    <th class="border border-gray-300 p-3 text-left">Rabat</th>
                    <th class="border border-gray-300 p-3 text-left">Cena netto</th>
                    <th class="border border-gray-300 p-3 text-left">Cena brutto</th>
                    <th class="border border-gray-300 p-3 text-left">Akcja</th>
                  </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                  <tr v-for="(detail, index) in offerStore.offerDetails" :key="index">
                    <td class="border border-gray-300 p-3">
                      <select v-model="detail.toolType" class="w-full p-2 border rounded">
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
                    <td class="border border-gray-300 p-3">
                      <select v-model="detail.flutesNumber" class="w-full p-2 border rounded">
                        <option disabled value="">Ilość ostrzy</option>
                        <option 
                          v-for="flute in getUniqueFlutesNumbers(detail.toolType)" 
                          :key="flute" 
                          :value="flute"
                        >
                          {{ flute }}
                        </option>
                      </select>
                    </td>
                    <td class="border border-gray-300 p-3">
                      <select v-model="detail.diameter" class="w-full p-2 border rounded" @change="offerStore.updateToolDetail(index, getSelectedTool(detail.toolType, detail.flutesNumber, detail.diameter))">
                        <option disabled value="">Średnica</option>
                        <option 
                          v-for="diameter in getUniqueDiameters(detail.toolType, detail.flutesNumber)" 
                          :key="diameter" 
                          :value="diameter"
                        >
                          {{ diameter }}
                        </option>
                      </select>
                    </td>
                    <td class="border border-gray-300 p-3">
                      <input type="number" v-model="detail.tool_quantity" class="w-full p-2 border rounded" placeholder="Ilość" />
                    </td>
                    <td class="border border-gray-300 p-3">
                      <input type="number" v-model="detail.tool_discount" class="w-full p-2 border rounded" placeholder="Rabat (%)" />
                    </td>
                    <td class="border border-gray-300 p-3">
                      {{ detail.tool_net_price || '-' }}
                    </td>
                    <td class="border border-gray-300 p-3">
                      {{ detail.tool_gross_price || '-' }}
                    </td>
                    <td class="border border-gray-300 p-3">
                      <button type="button" @click="offerStore.removeToolRow(index)" class="px-2 py-1 bg-red-500 text-white rounded">
                        Usuń
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="mt-4">
              <button type="button" @click="offerStore.addToolRow" class="px-4 py-2 bg-green-600 text-white rounded">
                Dodaj narzędzie
              </button>
            </div>

            <div class="flex justify-end space-x-2 mt-4">
              <button type="button" @click="offerStore.closeModal" class="px-4 py-2 bg-gray-300 rounded">
                Anuluj
              </button>
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                Zapisz
              </button>
            </div>
          </form>
        </DialogPanel>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
  

  