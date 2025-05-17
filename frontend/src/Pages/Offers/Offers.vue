<script setup>
  import { onMounted } from 'vue';
  import Header from '../../components/Header.vue';
  import useOfferStore from '../../store/offer';
  import OfferModal from './OfferModal.vue';
  import FilterInput from '../../components/FilterInput.vue';

  const offerStore = useOfferStore();

  onMounted(() => {
    offerStore.fetchOffers();
  });
</script>

<template>
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
      <button
        @click="offerStore.openModal()"
        class="mb-4 px-5 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition cursor-pointer"
      >
        + Stwórz ofertę
      </button>
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
              v-for="offer in offerStore.filteredOffers"
              :key="offer.id"
              :class="[
                'border-b border-gray-300 transition text-gray-700',
                {
                  'bg-green-100': offer.status_name === 'Zamówienie',
                  'bg-yellow-100': offer.status_name === 'Do sprawdzenia',
                  'bg-blue-100': offer.status_name === 'Wysłana',
                  'bg-gray-100': offer.status_name === 'Robocza',
                  'bg-red-100': offer.status_name === 'Odrzucona',
                },
              ]"
            >
              <!-- <td class="border border-gray-300 p-3">{{ user.id }}</td> -->
              <td class="border border-gray-300 p-3">{{ offer.offer_number }}</td>
              <td class="border border-gray-300 p-3">{{ offer.customer_name }}</td>
              <td class="border border-gray-300 p-3">{{ offer.employee_name }}</td>
              <td class="border border-gray-300 p-3">{{ offer.status_name }}</td>
              <td class="border border-gray-300 p-3">
                {{ offer.total_net_price.replace(/\s+/g, '').replace(',', '.') }}
              </td>
              <td class="border border-gray-300 p-3">
                {{
                  (offer.total_net_price.replace(/\s+/g, '').replace(',', '.') * 1.23).toFixed(2)
                }}
                PLN
              </td>
              <td class="border border-gray-300 p-3">{{ offer.created_at }}</td>
              <td class="border border-gray-300 p-3">
                <button
                  @click="offerStore.editOffer(offer)"
                  class="px-2 py-1 bg-yellow-500 text-white rounded cursor-pointer"
                >
                  Edytuj
                </button>
                <button
                  @click="offerStore.destroyOffer(offer.id)"
                  class="mx-2 px-2 py-1 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition cursor-pointer"
                >
                  Usuń
                </button>
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
    <OfferModal />
  </div>
</template>

<style scoped></style>
