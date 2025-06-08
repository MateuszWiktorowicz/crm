<script setup lang="ts">
  import { onMounted } from 'vue';
  import Header from '../../components/Header.vue';
  import useToolsStore from '../../store/tools';
  import FilterInput from '../../components/FilterInput.vue';
  import { ToolGeometry } from '@/types/types';

  const toolsStore = useToolsStore();

  onMounted(() => {
    toolsStore.fetchTools();
  });
</script>

<template>
  <div>
    <Header title="Baza narzędzi" />

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <div
        class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300"
      >
        <table class="w-full border-separate border-spacing-0">
          <thead class="bg-gray-100 sticky top-0 z-10">
            <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal rounded-t-lg">
              <th class="border border-gray-300 p-3 text-left">
                Typ narzędzia
                <FilterInput :store="toolsStore" column="toolTypeName" placeholder="Filtruj" />
              </th>
              <th class="border border-gray-300 p-3 text-left">
                Ilość ostrzy
                <FilterInput :store="toolsStore" column="flutesNumber" placeholder="Filtruj" />
              </th>
              <th class="border border-gray-300 p-3 text-left">
                Średnica
                <FilterInput :store="toolsStore" column="diameter" placeholder="Filtruj" />
              </th>
              <th class="border border-gray-300 p-3 text-left">Cena ostrzenia czoła</th>
              <th class="border border-gray-300 p-3 text-left">Cena ostrzenia obwodu</th>
              <th class="border border-gray-300 p-3 text-left">Cena ostrzenia komplet</th>
            </tr>
          </thead>
          <tbody v-if="toolsStore.filteredTools.length > 0" class="text-gray-600 text-sm">
            <tr
              v-for="tool in toolsStore.filteredTools as ToolGeometry[]"
              :key="tool.id"
              class="border-b border-gray-300 hover:bg-gray-50 transition"
            >
              <!-- <td class="border border-gray-300 p-3">{{ user.id }}</td> -->
              <td class="border border-gray-300 p-3">{{ tool.toolType.toolTypeName }}</td>
              <td class="border border-gray-300 p-3">{{ tool.flutesNumber }}</td>
              <td class="border border-gray-300 p-3">{{ tool.diameter }}</td>
              <td class="border border-gray-300 p-3">{{ tool.faceGrindingPrice }} PLN</td>
              <td class="border border-gray-300 p-3">
                {{
                  tool.peripheryGrindingPrice !== null
                    ? tool.peripheryGrindingPrice + ' PLN'
                    : 'Nie ostrzymy obwodu'
                }}
              </td>
              <td class="border border-gray-300 p-3">
                {{
                  (tool.peripheryGrindingPrice ? tool.peripheryGrindingPrice : 0) +
                  tool.faceGrindingPrice
                }}
                PLN
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="8" class="text-center text-gray-500 p-4">
                Brak narzędzi do wyświetlenia.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
