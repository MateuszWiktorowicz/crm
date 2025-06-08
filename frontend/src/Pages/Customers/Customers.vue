<script setup lang="ts">
  import { onMounted, ref } from 'vue';
  import useCustomerStore from '../../store/customer';
  import CustomerModal from './CustomerModal.vue';
  import useUserStore from '../../store/user';
  import FilterInput from '../../components/FilterInput.vue';
  import Button from '@/components/Button.vue';
  import { useConfirmationDialog } from '@/composables/useConfirmationDialog';
  import { Customer } from '@/types/types';

  /*
Początek nowej logiki
*/

  const customerStore = useCustomerStore();
  const userStore = useUserStore();

  const isModalOpen = ref(false);

  const { showConfirmationDialog, dialogRef, ConfirmationDialog } = useConfirmationDialog();

  const handleDelete = async (id: number | null) => {
    if (id === null) return;

    const confirmed = await showConfirmationDialog('Czy na pewno chcesz usunąć klienta?');
    if (confirmed) {
      await customerStore.deleteCustomer(id);
    }
  };

  const handleEdit = (customer: Customer) => {
    customerStore.editCustomer(customer);
    openModal();
  };

  const openModal = () => {
    isModalOpen.value = true;
  };

  const closeModal = () => {
    customerStore.resetCustomer();
    isModalOpen.value = false;
  };

  /*
Koniec nowej logiki
*/

  onMounted(() => {
    customerStore.fetchCustomers();
  });

  // const selectFile = () => {
  //   const fileInput = document.querySelector('#fileInput');
  //   if (fileInput) fileInput.click();
  // };

  // const handleFileUpload = async (event) => {
  //   const file = event.target.files[0];
  //   if (!file) return;

  //   customerStore.setSelectedFile(file); // Ustawienie pliku w Pinia

  //   const formData = new FormData();
  //   formData.append('file', file);

  //   try {
  //     await customerStore.importCustomers(formData); // Wywołanie importu
  //     alert('Import zakończony sukcesem!');
  //     customerStore.clearSelectedFile(); // Czyszczenie stanu pliku
  //   } catch (error) {
  //     console.error('Błąd importu:', error);
  //     alert('Wystąpił błąd podczas importu.');
  //   }
  // };
</script>

<template>
  <ConfirmationDialog ref="dialogRef" />
  <div>
    <div class="container mx-auto p-2">
      <h1 class="text-2xl font-bold mb-6 text-gray-800">Lista klientów</h1>

      <div class="flex gap-3 mb-3">
        <Button variant="success" @click="openModal"> + Dodaj Klienta </Button>
        <!-- <Button @click="selectFile" :disabled="!userStore.isCreator()">
          📂 Importuj Klientów
        </Button>-->
      </div>
      <!-- <input type="file" id="fileInput" @change="handleFileUpload" class="hidden" /> -->
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
        <table class="table-fixed w-full border-separate border-spacing-0">
          <thead class="bg-gray-100 sticky top-0 z-10">
            <tr class="bg-gray-100 text-gray-700 uppercase text-xs leading-normal rounded-t-lg">
              <th class="border border-gray-300 p-3 text-center w-[100px]">
                Kod
                <FilterInput :store="customerStore" column="code" placeholder="Filtruj" />
              </th>
              <th
                class="border border-gray-300 p-3 text-center w-[300px] break-words whitespace-normal"
              >
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
                <FilterInput :store="customerStore" column="salerMarker" placeholder="Filtruj" />
              </th>
              <th class="border border-gray-300 p-3 text-center w-[300px]">
                Uwagi
                <FilterInput :store="customerStore" column="description" placeholder="Filtruj" />
              </th>
              <th class="border border-gray-300 p-3 text-center">Akcje</th>
            </tr>
          </thead>
          <tbody v-if="customerStore.filteredCustomers.length > 0" class="text-gray-600 text-xs">
            <tr v-for="customer in customerStore.filteredCustomers" :key="customer.code">
              <td class="border border-gray-300 p-3">{{ customer.code }}</td>
              <td class="border border-gray-300 p-3">{{ customer.name }}</td>
              <td class="border border-gray-300 p-3">{{ customer.nip }}</td>
              <td class="border border-gray-300 p-3">{{ customer.city }}</td>
              <td class="border border-gray-300 p-3">{{ customer.address }}</td>
              <td class="border border-gray-300 p-3">{{ customer.salerMarker }}</td>
              <td class="border border-gray-300 p-3 max-h-12 overflow-y-auto">
                <div class="max-h-24 overflow-y-auto">
                  {{ customer.description }}
                </div>
              </td>
              <td class="border border-gray-300 p-3 text-center">
                <Button @click="handleEdit(customer)" size="small" variant="warning">
                  Edytuj
                </Button>
                <Button
                  @click="handleDelete(customer.id)"
                  size="small"
                  variant="danger"
                  class="mt-2"
                >
                  Usuń
                </Button>
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
    <CustomerModal :isModalOpen="isModalOpen" :closeModal="closeModal" />
  </div>
</template>

<style scoped></style>
