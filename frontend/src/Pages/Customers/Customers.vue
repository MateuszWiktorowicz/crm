<script setup>
  import { onMounted } from 'vue';
  import useCustomerStore from '../../store/customer';
  import CustomerModal from './CustomerModal.vue';
  import useUserStore from '../../store/user';
  import FilterInput from '../../components/FilterInput.vue';

  const customerStore = useCustomerStore();
  const userStore = useUserStore();

  onMounted(() => {
    customerStore.fetchCustomers();
  });

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
  <div>
    <div class="container mx-auto p-2">
      <h1 class="text-2xl font-bold mb-6 text-gray-800">Lista klientów</h1>

      <button
        @click="customerStore.openModal()"
        class="mx-2 mb-4 px-5 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition cursor-pointer"
      >
        + Dodaj Klienta
      </button>

      <button
        @click="selectFile"
        :disabled="!userStore.isCreator()"
        :class="[
          'mx-2 mb-4 px-5 py-2 rounded-lg shadow-md transition cursor-pointer',
          userStore.isCreator()
            ? 'bg-blue-600 text-white hover:bg-blue-700'
            : 'bg-gray-400 text-gray-600 cursor-not-allowed',
        ]"
      >
        📂 Importuj Klientów
      </button>

      <!-- Ukryty input, aby nie wyświetlał się na stronie -->
      <input type="file" id="fileInput" @change="handleFileUpload" class="hidden" />
      <div
        v-if="Object.keys(customerStore.errors).length"
        class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg"
      >
        <p class="font-semibold">Wystąpiły błędy:</p>
        <ul class="list-disc list-inside">
          <li v-for="(errorMessages, field) in customerStore.errors" :key="field">
            <span v-for="(message, index) in errorMessages" :key="index">{{ message }}</span>
          </li>
        </ul>
      </div>
      <div
        class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300"
      >
        <table class="w-full border-separate border-spacing-0">
          <thead class="bg-gray-100 sticky top-0 z-10">
            <tr class="bg-gray-100 text-gray-700 uppercase text-xs leading-normal rounded-t-lg">
              <th class="border border-gray-300 p-3 text-center">
                Kod
                <FilterInput :store="customerStore" column="code" placeholder="Filtruj" />
              </th>
              <th class="border border-gray-300 p-3 text-center">
                Nazwa
                <FilterInput :store="customerStore" column="name" placeholder="Filtruj" />
              </th>
              <th class="border border-gray-300 p-3 text-center">
                NIP
                <FilterInput :store="customerStore" column="nip" placeholder="Filtruj" />
              </th>
              <th class="border border-gray-300 p-3 text-center">
                Miasto
                <FilterInput :store="customerStore" column="city" placeholder="Filtruj" />
              </th>
              <th class="border border-gray-300 p-3 text-center">
                Adres
                <FilterInput :store="customerStore" column="address" placeholder="Filtruj" />
              </th>
              <th class="border border-gray-300 p-3 text-center">
                Znacznik
                <FilterInput :store="customerStore" column="saler_marker" placeholder="Filtruj" />
              </th>
              <th class="border border-gray-300 p-3 text-center">
                Uwagi
                <FilterInput :store="customerStore" column="description" placeholder="Filtruj" />
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
              <td class="border border-gray-300 p-3 max-h-12 overflow-y-auto">
                <div class="max-h-24 overflow-y-auto">
                  {{ customer.description }}
                </div>
              </td>
              <td class="border border-gray-300 p-3 text-center">
                <button
                  @click="customerStore.openModal(customer)"
                  class="m-2 px-2 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition cursor-pointer"
                >
                  Edytuj
                </button>
                <button
                  @click="customerStore.deleteCustomer(customer.id)"
                  class="mx-2 px-2 py-1 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition cursor-pointer"
                >
                  Usuń
                </button>
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="8" class="text-center text-gray-500 p-4">
                Brak klientów do wyświetlenia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <CustomerModal />
  </div>
</template>

<style scoped></style>
