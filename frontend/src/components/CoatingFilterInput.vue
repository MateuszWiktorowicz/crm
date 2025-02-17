<template>
    <div class="p-1">
      <input
        v-model="filterValue"
        @input="onInputChange"
        :placeholder="placeholder"
        class="p-2 text-xs border border-gray-300 rounded w-full"
      />
    </div>
</template>

<script setup>
import { defineProps, ref } from 'vue';
import useCoatingStore from '../store/coating';

const coatingStore = useCoatingStore();

const props = defineProps({
  column: {
    type: String,
    required: true
  },
  placeholder: {
    type: String,
    default: 'Filtruj'
  }
});

// Inicjalizacja ref z wartości z Pinia
const filterValue = ref(coatingStore.filters[props.column]);

// Funkcja zmieniająca filtr
const onInputChange = () => {
  coatingStore.setFilter(props.column, filterValue.value);
};
</script>
