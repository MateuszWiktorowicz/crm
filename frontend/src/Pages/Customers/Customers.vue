<script setup lang="ts">
  import { onMounted, ref } from 'vue';
  import useCustomerStore from '../../store/customer';
  import CustomerModal from './CustomerModal.vue';
  import { useUserStore } from '../../store/user';
  import Button from '@/components/Button.vue';
  import SkeletonLoader from '@/components/SkeletonLoader.vue';
  import { useConfirmationDialog } from '@/composables/useConfirmationDialog';
  import { useToast } from '@/composables/useToast';
  import { Customer, CustomerFilters } from '@/types/types';

  const customerStore = useCustomerStore();
  const userStore = useUserStore();
  const { success, error } = useToast();

  const isModalOpen = ref(false);

  const { showConfirmationDialog, dialogRef, ConfirmationDialog } = useConfirmationDialog();

  // Lokalne filtry w komponencie
  const localFilters = ref<Partial<CustomerFilters>>({
    code: '',
    name: '',
    nip: '',
    city: '',
    address: '',
    salerMarker: '',
    description: '',
  });

  // Debounce timer dla filtrów
  let filterTimeout: ReturnType<typeof setTimeout> | null = null;

  const handleFilterChange = (column: keyof CustomerFilters, value: string) => {
    localFilters.value[column] = value;

    // Debounce - czekamy 500ms po ostatniej zmianie
    if (filterTimeout) {
      clearTimeout(filterTimeout);
    }

    filterTimeout = setTimeout(() => {
      // Reset do strony 1 przy zmianie filtrów
      customerStore.fetchCustomers(1, localFilters.value);
    }, 500);
  };

  const handlePageChange = (page: number) => {
    customerStore.fetchCustomers(page, localFilters.value);
  };

  const handleDelete = async (id: number | null) => {
    if (id === null) return;

    const confirmed = await showConfirmationDialog('Czy na pewno chcesz usunąć klienta?');
    if (confirmed) {
      try {
        await customerStore.deleteCustomer(id);
        success('Klient został usunięty');
      } catch (err) {
        error('Błąd podczas usuwania klienta');
      }
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

  onMounted(() => {
    customerStore.fetchCustomers(1);
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
                <input
                  :value="localFilters.code"
                  @input="handleFilterChange('code', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th
                class="border border-gray-300 p-3 text-center w-[300px] break-words whitespace-normal"
              >
                Nazwa
                <input
                  :value="localFilters.name"
                  @input="handleFilterChange('name', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th class="border border-gray-300 p-3 text-center">
                NIP
                <input
                  :value="localFilters.nip"
                  @input="handleFilterChange('nip', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th class="border border-gray-300 p-3 text-center">
                Miasto
                <input
                  :value="localFilters.city"
                  @input="handleFilterChange('city', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th class="border border-gray-300 p-3 text-center">
                Adres
                <input
                  :value="localFilters.address"
                  @input="handleFilterChange('address', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th class="border border-gray-300 p-3 text-center">
                Znacznik
                <input
                  :value="localFilters.salerMarker"
                  @input="handleFilterChange('salerMarker', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th class="border border-gray-300 p-3 text-center w-[300px]">
                Uwagi
                <input
                  :value="localFilters.description"
                  @input="handleFilterChange('description', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th class="border border-gray-300 p-3 text-center">Akcje</th>
            </tr>
          </thead>
          <tbody v-if="customerStore.isLoading" class="text-gray-600 text-xs">
            <tr v-for="i in 5" :key="i" class="border-b border-gray-300">
              <td class="border border-gray-300 p-3" colspan="8">
                <SkeletonLoader type="table-row" :columns="8" />
              </td>
            </tr>
          </tbody>
          <tbody v-else-if="customerStore.customers.length > 0" class="text-gray-600 text-xs">
            <tr v-for="customer in customerStore.customers" :key="customer.code">
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
                <div class="flex items-center justify-center gap-2">
                  <button
                    @click="handleEdit(customer)"
                    class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                    title="Edytuj"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                      />
                    </svg>
                  </button>
                  <button
                    @click="handleDelete(customer.id)"
                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                    title="Usuń"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                      />
                    </svg>
                  </button>
                </div>
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

      <!-- Paginacja -->
      <div v-if="customerStore.pagination" class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Strona {{ customerStore.pagination.current_page }} z {{ customerStore.pagination.last_page }}
          ({{ customerStore.pagination.total }} klientów)
        </div>
        <div class="flex gap-2">
          <Button
            @click="handlePageChange(customerStore.pagination!.current_page - 1)"
            :disabled="customerStore.pagination.current_page === 1 || customerStore.isLoading"
            variant="secondary"
            size="small"
          >
            Poprzednia
          </Button>
          <Button
            @click="handlePageChange(customerStore.pagination!.current_page + 1)"
            :disabled="customerStore.pagination.current_page === customerStore.pagination.last_page || customerStore.isLoading"
            variant="secondary"
            size="small"
          >
            Następna
          </Button>
        </div>
      </div>
    </div>
    <CustomerModal :isModalOpen="isModalOpen" :closeModal="closeModal" />
  </div>
</template>

<style scoped></style>
