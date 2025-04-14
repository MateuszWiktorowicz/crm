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
import { defineProps, ref, watch } from 'vue';

const props = defineProps({
  column: {
    type: String,
    required: true,
  },
  placeholder: {
    type: String,
    default: 'Filtruj',
  },
  store: {
    type: Object,
    required: true,
  },
});

const filterValue = ref(props.store.filters[props.column]);

watch(
  () => props.store.filters[props.column],
  (newVal) => {
    filterValue.value = newVal;
  }
);

const onInputChange = () => {
  props.store.setFilter(props.column, filterValue.value);
};
</script>