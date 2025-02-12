<script setup>
import { ref, onMounted } from 'vue';
import useCustomerStore from '../../store/customer';
import CustomerModal from './CustomerModal.vue';
import useUserStore from '../../store/user';
import CustomersFilterInput from '../../components/CustomersFilterInput.vue';

const customerStore = useCustomerStore();
const userStore = useUserStore();

onMounted(() => {
    customerStore.fetchCustomers();
})

const selectFile = () => {
    // Jeśli masz input w szablonie, po prostu kliknij go.
    const fileInput = document.querySelector('#fileInput');
    if (fileInput) fileInput.click();
};

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    customerStore.setSelectedFile(file); // Ustawienie pliku w Pinia

    const formData = new FormData();
    formData.append('file', file);

    try {
        await customerStore.importCustomers(formData); // Wywołanie importu
        alert('Import zakończony sukcesem!');
        customerStore.clearSelectedFile(); // Czyszczenie stanu pliku
    } catch (error) {
        console.error('Błąd importu:', error);
        alert('Wystąpił błąd podczas importu.');
    }
};

</script>

<template>
<div class="container mx-auto p-2">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Lista klientów</h1>

    <button
      @click="customerStore.openModal()"
      class="mx-2 mb-4 px-5 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition"
    >
      + Dodaj Klienta
    </button>

    <button
      @click="selectFile"
      :disabled="!userStore.isCreator()"
      :class="[
      'mx-2 mb-4 px-5 py-2 rounded-lg shadow-md transition',
      userStore.isCreator() ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-400 text-gray-600 cursor-not-allowed'
    ]"
    >
      📂 Importuj Klientów
    </button>

    <!-- Ukryty input, aby nie wyświetlał się na stronie -->
    <input
      type="file"
      id="fileInput"
      @change="handleFileUpload"
      class="hidden"
    />

    <div class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300">
      <table class="w-full border-separate border-spacing-0">
        <thead class="bg-gray-100 sticky top-0 z-10">
          <tr class="bg-gray-100 text-gray-700 uppercase text-xs leading-normal rounded-t-lg">
            <!-- <th class="border border-gray-300 p-3 text-left">ID</th> -->
            <th class="border border-gray-300 p-3 text-center">
              Kod
              <CustomersFilterInput 
                  :column="'code'" 
                  placeholder="Filtruj"
                />
            </th>
            <th class="border border-gray-300 p-3 text-center">
              Nazwa
              <CustomersFilterInput 
                  :column="'name'" 
                  placeholder="Filtruj"
                />
            </th>
            <th class="border border-gray-300 p-3 text-center">
              NIP
              <CustomersFilterInput 
                  :column="'nip'" 
                  placeholder="Filtruj"
                />
            </th>
            <th class="border border-gray-300 p-3 text-center">
              Miasto
              <CustomersFilterInput 
                  :column="'city'" 
                  placeholder="Filtruj"
                />
            </th>
            <th class="border border-gray-300 p-3 text-center">
              Adres
              <CustomersFilterInput 
                  :column="'address'" 
                  placeholder="Filtruj"
                />
            </th>
            <!-- <th class="border border-gray-300 p-3 text-left">Kod Pocztowy</th> -->
            <th class="border border-gray-300 p-3 text-center">
              Znacznik
              <CustomersFilterInput 
                  :column="'saler_marker'" 
                  placeholder="Filtruj"
                />
            </th>
            <th class="border border-gray-300 p-3 text-center">
              Uwagi
              <CustomersFilterInput 
                  :column="'description'" 
                  placeholder="Filtruj"
                />
            </th>
            <th class="border border-gray-300 p-3 text-center">Akcje</th>
          </tr>
        </thead>
          <tbody v-if="customerStore.filteredCustomers.length > 0" class="text-gray-600 text-xs">
            <tr v-for="customer in customerStore.filteredCustomers" :key="customer.id">
              <td class="border border-gray-300 p-3">{{ customer.code }}</td>
              <td class="border border-gray-300 p-3">{{ customer.name }}</td>
              <td class="border border-gray-300 p-3">{{ customer.nip }}</td>
              <td class="border border-gray-300 p-3">{{ customer.city }}</td>
              <td class="border border-gray-300 p-3">{{ customer.address }}</td>
              <td class="border border-gray-300 p-3">{{ customer.saler_marker }}</td>
              <td class="border border-gray-300 p-3 max-h-12 overflow-y-auto ">
                <div class="max-h-24 overflow-y-auto">
                  {{ customer.description }}
                </div>
              </td>
              <td class="border border-gray-300 p-3">
                <button @click="customerStore.openModal(customer)" class="mx-2 px-2 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition">
                  Edytuj
                </button>
                <button @click="customerStore.deleteCustomer(customer.id)" class="mx-2 px-2 py-1 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition">
                  Usuń
                </button>
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="8" class="text-center text-gray-500 p-4">Brak klientów do wyświetlenia.</td>
            </tr>
          </tbody>
      </table>
    </div>
    </div>
    <CustomerModal />
</template>

<style scoped>

</style>