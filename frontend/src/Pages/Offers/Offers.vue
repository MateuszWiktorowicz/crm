<script setup>
import { onMounted } from 'vue';
import Header from '../../components/Header.vue';
import useOfferStore from '../../store/offer';
import OfferModal from './OfferModal.vue';

const offerStore = useOfferStore();

onMounted(() => {
    offerStore.fetchOffers();
})
</script>

<template>

<Header title='Oferty' />
<div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <button
      @click="offerStore.openModal()"
      class="mb-4 px-5 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition"
    >
      + Stwórz ofertę
    </button>
    <div class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300">
      <table class="w-full border-separate border-spacing-0">
        <thead class="bg-gray-100 sticky top-0 z-10">
          <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal rounded-t-lg">
            <!-- <th class="border border-gray-300 p-3 text-left">ID</th> -->
            <th class="border border-gray-300 p-3 text-left">
              Klient

            </th>
            <th class="border border-gray-300 p-3 text-left">
              Pracownik

            </th>
            <th class="border border-gray-300 p-3 text-left">
              Status

            </th>
            <th class="border border-gray-300 p-3 text-left">
              Cena całkowita netto

            </th>
            <th class="border border-gray-300 p-3 text-left">
              Cena całkowita brutto

            </th>
            <th class="border border-gray-300 p-3 text-left">
              Data stworzenia

            </th>
            <th class="border border-gray-300 p-3 text-left">
              Actions

            </th>
          </tr>
        </thead>
        <tbody v-if="offerStore.offers.length > 0" class="text-gray-600 text-sm">
          <tr
            v-for="offer in offerStore.offers"
            :key="offer.id"
            class="border-b border-gray-300 hover:bg-gray-50 transition"
          >
            <!-- <td class="border border-gray-300 p-3">{{ user.id }}</td> -->
            <td class="border border-gray-300 p-3">{{ offer.customer_name }}</td>
            <td class="border border-gray-300 p-3">{{ offer.employee_name }}</td>
            <td class="border border-gray-300 p-3">{{ offer.status_name }}</td>
            <td class="border border-gray-300 p-3">{{ offer.total_net_price }}</td>
            <td class="border border-gray-300 p-3">{{ (parseFloat(offer.total_net_price) * 1.23).toLocaleString('pl-PL')}} PLN</td>
            <td class="border border-gray-300 p-3">{{ offer.created_at }}</td>
            <td class="border border-gray-300 p-3">
              <button 
                @click="offerStore.editOffer(offer)" 
                class="px-2 py-1 bg-yellow-500 text-white rounded"
              >
                Edytuj
              </button>
              <button
                  @click="offerStore.destroyOffer(offer.id)"
                  class="mx-2 px-2 py-1 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition"
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
</template>

<style scoped>

</style>