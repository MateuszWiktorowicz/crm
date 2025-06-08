<script setup lang="ts">
  import { onMounted, ref } from 'vue';
  import Header from '../../components/Header.vue';
  import { useOfferStore } from '@/store/offer';
  import OfferModal from './OfferModal/OfferModal.vue';
  import FilterInput from '../../components/FilterInput.vue';
  import Button from '@/components/Button.vue';
  import { useConfirmationDialog } from '@/composables/useConfirmationDialog';
  import { Offer } from '@/types/types';
  import { formatDate } from '@/utils/formatDate';

  /*
Początek nowej logiki
*/

  const { showConfirmationDialog, dialogRef, ConfirmationDialog } = useConfirmationDialog();
  const offerStore: ReturnType<typeof useOfferStore> = useOfferStore();

  const handleDelete = async (id: number | null) => {
    if (!id) return;

    const confirmed = await showConfirmationDialog('Czy na pewno chcesz usunąć ofertę?');
    if (confirmed) {
      await offerStore.destroyOffer(id);
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

  /*
Koniec nowej logiki
*/

  onMounted(() => {
    offerStore.fetchOffers();
  });
</script>

<template>
  <ConfirmationDialog ref="dialogRef" />
  <div>
    <Header title="Oferty" />
    <div v-if="offerStore.errors.length > 0" class="mb-4">
      <ul class="bg-red-100 text-red-800 border border-red-400 p-4 rounded">
        <li v-for="(error, index) in offerStore.errors" :key="index">
          {{ error }}
        </li>
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
              <th class="border border-gray-300 p-3 text-left">
                Nr Oferty
                <FilterInput
                  :store="offerStore"
                  column="offer_number"
                  placeholder="Filtruj numer oferty"
                />
              </th>
              <th class="border border-gray-300 p-3 text-left">
                Klient
                <FilterInput
                  :store="offerStore"
                  column="customer_name"
                  placeholder="Filtruj klientów"
                />
              </th>
              <th class="border border-gray-300 p-3 text-left">
                Pracownik
                <FilterInput
                  :store="offerStore"
                  column="employee_name"
                  placeholder="Filtruj pracownika"
                />
              </th>
              <th class="border border-gray-300 p-3 text-left">
                Status
                <FilterInput
                  :store="offerStore"
                  column="status_name"
                  placeholder="Filtruj status"
                />
              </th>
              <th class="border border-gray-300 p-3 text-left">Cena całkowita netto</th>
              <th class="border border-gray-300 p-3 text-left">Cena całkowita brutto</th>
              <th class="border border-gray-300 p-3 text-left">
                Data stworzenia
                <FilterInput :store="offerStore" column="created_at" placeholder="Filtruj datę" />
              </th>
              <th class="border border-gray-300 p-3 text-left min-w-[150px]">Actions</th>
            </tr>
          </thead>
          <tbody v-if="offerStore.filteredOffers.length > 0" class="text-gray-600 text-sm">
            <tr
              v-for="offer in offerStore.filteredOffers as Offer[]"
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
              <!-- <td class="border border-gray-300 p-3">{{ user.id }}</td> -->
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
                <Button @click="handleEdit(offer)" variant="warning" class="mr-2 mb-2">
                  Edytuj
                </Button>
                <Button @click="handleDelete(offer.id)" variant="danger"> Usuń </Button>
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
    </div>
    <OfferModal :isModalOpen="isModalOpen" :closeModal="closeModal" />
  </div>
</template>

<style scoped></style>
