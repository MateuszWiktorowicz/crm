<script setup lang="ts">
  import { onMounted, ref } from 'vue';
  import Header from '../../components/Header.vue';
  import { useOfferStore } from '@/store/offer';
  import OfferModal from './OfferModal/OfferModal.vue';
  import Button from '@/components/Button.vue';
  import SkeletonLoader from '@/components/SkeletonLoader.vue';
  import { useConfirmationDialog } from '@/composables/useConfirmationDialog';
  import { useToast } from '@/composables/useToast';
  import { Offer, OfferFilters } from '@/types/types';
  import { formatDate } from '@/utils/formatDate';

  const { showConfirmationDialog, dialogRef, ConfirmationDialog } = useConfirmationDialog();
  const { success, error } = useToast();
  const offerStore: ReturnType<typeof useOfferStore> = useOfferStore();

  // Lokalne filtry w komponencie
  const localFilters = ref<Partial<OfferFilters>>({
    offerNumber: '',
    customerName: '',
    employeeName: '',
    statusName: '',
    createdAt: '',
  });

  // Debounce timer dla filtrów
  let filterTimeout: ReturnType<typeof setTimeout> | null = null;

  const handleFilterChange = (column: keyof OfferFilters, value: string) => {
    localFilters.value[column] = value;

    // Debounce - czekamy 500ms po ostatniej zmianie
    if (filterTimeout) {
      clearTimeout(filterTimeout);
    }

    filterTimeout = setTimeout(() => {
      // Reset do strony 1 przy zmianie filtrów
      offerStore.fetchOffers(1, localFilters.value);
    }, 500);
  };

  const handlePageChange = (page: number) => {
    offerStore.fetchOffers(page, localFilters.value);
  };

  const handleDelete = async (id: number | null) => {
    if (!id) return;

    const confirmed = await showConfirmationDialog('Czy na pewno chcesz usunąć ofertę?');
    if (confirmed) {
      try {
        await offerStore.destroyOffer(id);
        success('Oferta została usunięta');
      } catch (err) {
        error('Błąd podczas usuwania oferty');
      }
    }
  };

  const handleEdit = (offer: Offer) => {
    offerStore.editOffer(offer);
    openModal();
  };

  const isModalOpen = ref(false);

  const openModal = () => {
    isModalOpen.value = true;
  };

  const closeModal = () => {
    offerStore.resetOffer();
    isModalOpen.value = false;
  };

  const handleClone = (offer: Offer) => {
    offerStore.cloneOffer(offer);
    openModal();
    success('Oferta została sklonowana');
  };

  // Oblicz numer Lp na podstawie strony
  const getRowNumber = (index: number) => {
    if (!offerStore.pagination) return index + 1;
    return (offerStore.pagination.current_page - 1) * offerStore.pagination.per_page + index + 1;
  };

  onMounted(() => {
    offerStore.fetchOffers(1);
  });
</script>

<template>
  <ConfirmationDialog ref="dialogRef" />
  <div>
    <Header title="Oferty" />
    <div v-if="Object.keys(offerStore.errors).length > 0" class="mb-4">
      <ul class="bg-red-100 text-red-800 border border-red-400 p-4 rounded">
        <template v-for="(error, key) in offerStore.errors" :key="key">
          <li v-if="Array.isArray(error)">
            <span v-for="(err, idx) in error" :key="idx">{{ err }}</span>
          </li>
          <li v-else>{{ error }}</li>
        </template>
      </ul>
    </div>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <Button @click="openModal" variant="success" class="mb-2"> + Stwórz ofertę </Button>
      <div
        class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300"
      >
        <table class="w-full border-separate border-spacing-0">
          <thead class="bg-gray-100 sticky top-0 z-10">
            <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal rounded-t-lg">
              <th class="border border-gray-300 p-3 text-left">Lp</th>
              <th class="border border-gray-300 p-3 text-left">
                Nr Oferty
                <input
                  :value="localFilters.offerNumber"
                  @input="handleFilterChange('offerNumber', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj numer oferty"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th class="border border-gray-300 p-3 text-left">
                Klient
                <input
                  :value="localFilters.customerName"
                  @input="handleFilterChange('customerName', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj klientów"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th class="border border-gray-300 p-3 text-left">
                Pracownik
                <input
                  :value="localFilters.employeeName"
                  @input="handleFilterChange('employeeName', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj pracownika"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th class="border border-gray-300 p-3 text-left">
                Status
                <input
                  :value="localFilters.statusName"
                  @input="handleFilterChange('statusName', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj status"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th class="border border-gray-300 p-3 text-left">Cena całkowita netto</th>
              <th class="border border-gray-300 p-3 text-left">Cena całkowita brutto</th>
              <th class="border border-gray-300 p-3 text-left">
                Data stworzenia
                <input
                  :value="localFilters.createdAt"
                  @input="handleFilterChange('createdAt', ($event.target as HTMLInputElement).value)"
                  placeholder="Filtruj datę"
                  class="p-2 text-xs border border-gray-300 rounded w-full mt-1"
                />
              </th>
              <th class="border border-gray-300 p-3 text-left min-w-[150px]">Actions</th>
            </tr>
          </thead>
          <tbody v-if="offerStore.isLoading" class="text-gray-600 text-sm">
            <tr v-for="i in 5" :key="i" class="border-b border-gray-300">
              <td class="border border-gray-300 p-3" colspan="9">
                <SkeletonLoader type="table-row" :columns="9" />
              </td>
            </tr>
          </tbody>
          <tbody v-else-if="offerStore.offers.length > 0" class="text-gray-600 text-sm">
            <tr
              v-for="(offer, index) in offerStore.offers as Offer[]"
              :key="offer.id ?? undefined"
              :class="[
                'border-b border-gray-300 transition text-gray-700',
                {
                  'bg-green-100': offer.status.name === 'Zamówienie',
                  'bg-yellow-100': offer.status.name === 'Do sprawdzenia',
                  'bg-blue-100': offer.status.name === 'Wysłana',
                  'bg-gray-100': offer.status.name === 'Robocza',
                  'bg-red-100': offer.status.name === 'Odrzucona',
                },
              ]"
            >
              <td class="border border-gray-300 p-3">{{ getRowNumber(index) }}</td>
              <td class="border border-gray-300 p-3">{{ offer.offerNumber }}</td>
              <td class="border border-gray-300 p-3">{{ offer.customer?.name ?? '-' }}</td>
              <td class="border border-gray-300 p-3">{{ offer.createdBy?.name ?? '-' }}</td>
              <td class="border border-gray-300 p-3">{{ offer.status.name }}</td>
              <td class="border border-gray-300 p-3">{{ offer.totalPrice.toFixed(2) }} PLN</td>
              <td class="border border-gray-300 p-3">
                {{ (offer.totalPrice * 1.23).toFixed(2) }}
                PLN
              </td>
              <td class="border border-gray-300 p-3">{{ formatDate(offer.createdAt ?? '-') }}</td>
              <td class="border border-gray-300 p-3">
                <div class="flex items-center gap-2">
                  <button
                    @click="handleEdit(offer)"
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
                    @click="handleClone(offer)"
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                    title="Klonuj"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
                      />
                    </svg>
                  </button>
                  <button
                    @click="handleDelete(offer.id)"
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
              <td colspan="8" class="text-center text-gray-500 p-4">Brak ofert do wyświetlenia.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginacja -->
      <div v-if="offerStore.pagination" class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Strona {{ offerStore.pagination.current_page }} z {{ offerStore.pagination.last_page }}
          ({{ offerStore.pagination.total }} ofert)
        </div>
        <div class="flex gap-2">
          <Button
            @click="handlePageChange(offerStore.pagination!.current_page - 1)"
            :disabled="offerStore.pagination.current_page === 1 || offerStore.isLoading"
            variant="secondary"
            size="small"
          >
            Poprzednia
          </Button>
          <Button
            @click="handlePageChange(offerStore.pagination!.current_page + 1)"
            :disabled="offerStore.pagination.current_page === offerStore.pagination.last_page || offerStore.isLoading"
            variant="secondary"
            size="small"
          >
            Następna
          </Button>
        </div>
      </div>
    </div>
    <OfferModal :isModalOpen="isModalOpen" :closeModal="closeModal" />
  </div>
</template>

<style scoped></style>
