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
  import useUserStore from '../store/user';
  
  const userStore = useUserStore();
  
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

  const filterValue = ref(userStore.filters[props.column]);
  
  const onInputChange = () => {
    userStore.setFilter(props.column, filterValue.value);
  };
  </script>
  
  <style scoped>
  </style>
  