<template>
  <div class="flex flex-col space-y-1">
    <label :for="inputId" class="text-sm font-medium text-gray-700">{{ label }}</label>
    <input
      class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all disabled:bg-gray-100 disabled:cursor-not-allowed disabled:text-gray-500"
      :id="inputId"
      type="text"
      :value="inputValue"
      @input="updateValue"
      :disabled="disabled"
    />
  </div>
</template>

<script>
  import { ref, watch, computed } from 'vue';

  export default {
    props: {
      store: Object, // Store, w którym ma być aktualizowana wartość
      field: String, // Nazwa pola w obiekcie (np. 'code', 'name', itd.)
      label: String, // Etykieta dla inputa
      inputId: String, // Id dla inputa
      object: String, // Obiekt, w którym ma być zaktualizowane pole (np. 'customer', 'offer', 'tools')
      modelValue: String, // Używamy modelValue do bindowania z v-model
      disabled: {
        // Dodajemy prop disabled
        type: Boolean,
        default: false, // Domyślnie ustawiamy disabled na false
      },
    },
    setup(props, { emit }) {
      // Dynamicznie tworzymy ścieżkę do wartości w obiekcie
      const objectRef = props.store[props.object];

      // Używamy computed, by mieć związaną wartość do modelValue
      const inputValue = computed({
        get: () => objectRef[props.field], // Odczytujemy wartość z obiektu store
        set: (newValue) => {
          objectRef[props.field] = newValue; // Ustawiamy nową wartość w obiekcie store
          emit('update:modelValue', newValue); // Emityujemy aktualizację przez v-model
        },
      });

      const updateValue = (event) => {
        inputValue.value = event.target.value; // Zmieniamy wartość w inputValue
      };

      // Obserwujemy zmiany w store i aktualizujemy lokalną wartość
      watch(
        () => objectRef[props.field],
        (newValue) => {
          inputValue.value = newValue;
        }
      );

      return {
        inputValue,
        updateValue,
      };
    },
  };
</script>

<style scoped></style>
