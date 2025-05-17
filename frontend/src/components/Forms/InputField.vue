<template>
  <div class="flex flex-col">
    <label :for="inputId">{{ label }}</label>
    <input
      class="w-full p-2 border rounded"
      :id="inputId"
      type="text"
      :value="inputValue"
      @input="updateValue"
      :disabled="disabled"
      :class="{ 'bg-gray-200': disabled, 'bg-white': !disabled }"
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

<style scoped>
  input {
    padding: 8px;
    margin: 10px 0;
    width: 200px;
  }

  .bg-gray-200 {
    background-color: #e2e8f0; /* Jasnoszary kolor tła dla disabled */
    cursor: not-allowed;
  }

  .bg-white {
    background-color: #ffffff; /* Biały kolor tła dla aktywnego */
  }
</style>
