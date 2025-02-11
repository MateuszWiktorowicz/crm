<script setup>
import { ref, onMounted } from 'vue';
import useCustomerStore from '../../store/customer';
import CustomerModal from './CustomerModal.vue';

const customerStore = useCustomerStore();
const fileInput = ref(null);

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
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Lista klientów</h1>

    <button
      @click="customerStore.openModal()"
      class="mx-2 mb-4 px-5 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition"
    >
      + Dodaj Klienta
    </button>

    <button
      @click="selectFile"
      class="mx-2 mb-4 px-5 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition"
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

    <div class="overflow-x-auto bg-white shadow-lg rounded-lg border border-gray-300">
      <table class="w-full border-separate border-spacing-0">
        <thead>
          <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal rounded-t-lg">
            <th class="border border-gray-300 p-3 text-left">ID</th>
            <th class="border border-gray-300 p-3 text-left">Kod</th>
            <th class="border border-gray-300 p-3 text-left">Nazwa</th>
            <th class="border border-gray-300 p-3 text-left">NIP</th>
            <th class="border border-gray-300 p-3 text-left">Miasto</th>
            <th class="border border-gray-300 p-3 text-left">Adres</th>
            <th class="border border-gray-300 p-3 text-left">Kod Pocztowy</th>
            <th class="border border-gray-300 p-3 text-left">Znacznik</th>
            <th class="border border-gray-300 p-3 text-left">Uwagi</th>
            <th class="border border-gray-300 p-3 text-left">Akcje</th>
          </tr>
        </thead>
        <tbody class="text-gray-600 text-sm">
          <tr
            v-for="customer in customerStore.customers"
            :key="customer.id"
            class="border-b border-gray-300 hover:bg-gray-50 transition"
          >
            <td class="border border-gray-300 p-3">{{ customer.id }}</td>
            <td class="border border-gray-300 p-3">{{ customer.code }}</td>
            <td class="border border-gray-300 p-3">{{ customer.name }}</td>
            <td class="border border-gray-300 p-3">{{ customer.nip }}</td>
            <td class="border border-gray-300 p-3">{{ customer.city }}</td>
            <td class="border border-gray-300 p-3">{{ customer.address }}</td>
            <td class="border border-gray-300 p-3">{{ customer.zip_code }}</td>
            <td class="border border-gray-300 p-3">{{ customer.saler_marker }}</td>
            <td class="border border-gray-300 p-3">{{ customer.description }}</td>
            <td class="border border-gray-300 p-3">
              <button
                @click="customerStore.openModal(customer)"
                class="mx-2 px-2 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition"
              >
                Edytuj
              </button>
              <button

                class="mx-2 px-2 py-1 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition"
              >
                Usuń
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <CustomerModal />
  </div>
</template>

<style scoped>

</style>