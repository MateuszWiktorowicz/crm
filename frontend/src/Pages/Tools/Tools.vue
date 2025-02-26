<script setup>
import { onMounted } from 'vue';
import Header from '../../components/Header.vue';
import useToolsStore from '../../store/tools';
import ToolFilterInput from '../../components/ToolFilterInput.vue';

const toolsStore = useToolsStore();

onMounted(() => {
    toolsStore.fetchTools();
});

</script>

<template>

<Header title='Baza narzędzi' />

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div v-for="toolType in toolsStore.toolTypes" :key="toolType.id">
            {{ toolType.tool_type_name }}
        </div>

    <div class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300">
      <table class="w-full border-separate border-spacing-0">
        <thead class="bg-gray-100 sticky top-0 z-10">
          <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal rounded-t-lg">
            <!-- <th class="border border-gray-300 p-3 text-left">ID</th> -->
            <th class="border border-gray-300 p-3 text-left">
              Typ narzędzia
              <ToolFilterInput :column="'tool_type_name'" />
            </th>
            <th class="border border-gray-300 p-3 text-left">
              Ilość ostrzy
              <ToolFilterInput :column="'flutes_number'" />
            </th>
            <th class="border border-gray-300 p-3 text-left">
              Średnica
              <ToolFilterInput :column="'diameter'" />
            </th>
            <th class="border border-gray-300 p-3 text-left">
              Cena ostrzenia czoła
              <!-- <UsersFilterInput :column="'marker'" /> -->
            </th>
            <th class="border border-gray-300 p-3 text-left">
              Cena ostrzenia obwodu
              <!-- <UsersFilterInput :column="'marker'" /> -->
            </th>
            <th class="border border-gray-300 p-3 text-left">
              Cena ostrzenia komplet
              <!-- <UsersFilterInput :column="'marker'" /> -->
            </th>
          </tr>
        </thead>
        <tbody v-if="toolsStore.filteredTools.length > 0" class="text-gray-600 text-sm">
          <tr
            v-for="tool in toolsStore.filteredTools"
            :key="tool.id"
            class="border-b border-gray-300 hover:bg-gray-50 transition"
          >
            <!-- <td class="border border-gray-300 p-3">{{ user.id }}</td> -->
            <td class="border border-gray-300 p-3">{{ tool.tool_type_name }}</td>
            <td class="border border-gray-300 p-3">{{ tool.flutes_number }}</td>
            <td class="border border-gray-300 p-3">{{ tool.diameter }} </td>
            <td class="border border-gray-300 p-3">{{ tool.face_grinding_price }} PLN</td>
            <td class="border border-gray-300 p-3">
            {{ tool.periphery_grinding_price !== null ? tool.periphery_grinding_price + ' PLN' : 'Nie ostrzymy obwodu' }}
            </td>
            <td class="border border-gray-300 p-3">{{ (tool.periphery_grinding_price ? parseFloat(tool.periphery_grinding_price) : 0) + parseFloat(tool.face_grinding_price) }} PLN</td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td colspan="8" class="text-center text-gray-500 p-4">Brak narzędzi do wyświetlenia.</td>
          </tr>
        </tbody>
      </table>
    </div>
</div>

</template>

<style scoped>

</style>