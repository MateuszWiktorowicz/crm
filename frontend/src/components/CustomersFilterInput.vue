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
  import useCustomerStore from '../store/customer';

  const customerStore = useCustomerStore();

  const props = defineProps({
    column: {
      type: String,
      required: true,
    },
    placeholder: {
      type: String,
      default: 'Wpisz wartość do filtrowania',
    },
  });

  const filterValue = ref(customerStore.filters[props.column]);

  const onInputChange = () => {
    customerStore.setFilter(props.column, filterValue.value);
  };
</script>

<style scoped></style>
