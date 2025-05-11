<script setup>
  import { onMounted } from 'vue';
  import Header from '../../components/Header.vue';
  import useCoatingStore from '../../store/coating';
  import FilterInput from '../../components/FilterInput.vue';

  const coatingStore = useCoatingStore();

  onMounted(() => {
    coatingStore.fetchCoatings();
  });
</script>

<template>
  <div>
  <Header title="Baza pokryć" />
  <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
    <div
      class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300"
    >
      <table class="w-full border-separate border-spacing-0">
        <thead class="bg-gray-100 sticky top-0 z-10">
          <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal rounded-t-lg">
            <th class="border border-gray-300 p-3 text-left">
              Pokrycie
              <FilterInput
              :store="coatingStore"
              column="mastermet_name"
              placeholder="Filtruj"
            />
            </th>
            <th class="border border-gray-300 p-3 text-left">
              Kod
              <FilterInput
              :store="coatingStore"
              column="mastermet_code"
              placeholder="Filtruj"
            />
            </th>
            <th class="border border-gray-300 p-3 text-left">
              Średnica
              <FilterInput
              :store="coatingStore"
              column="diameter"
              placeholder="Filtruj"
            />
            </th>
            <th class="border border-gray-300 p-3 text-left">Cena pokrywania</th>
          </tr>
        </thead>
        <tbody v-if="coatingStore.filteredCoatings.length > 0" class="text-gray-600 text-sm">
          <tr
            v-for="coating in coatingStore.filteredCoatings"
            :key="coating.id"
            class="border-b border-gray-300 hover:bg-gray-50 transition"
          >
            <!-- <td class="border border-gray-300 p-3">{{ user.id }}</td> -->
            <td class="border border-gray-300 p-3">{{ coating.mastermet_name }}</td>
            <td class="border border-gray-300 p-3">{{ coating.mastermet_code }}</td>
            <td class="border border-gray-300 p-3">{{ coating.diameter }}</td>
            <td class="border border-gray-300 p-3">{{ Number(coating.price) }} PLN</td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td colspan="8" class="text-center text-gray-500 p-4">Brak pokryć do wyświetlenia.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
</template>

<style scoped></style>
