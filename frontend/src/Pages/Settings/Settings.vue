<script setup lang="ts">
  import Header from '../../components/Header.vue';
  import { ref, onMounted } from 'vue';
  import useSettingsStore from '../../store/settings'; // Importujemy store Pinia
  import Button from '@/components/Button.vue';

  const settingsStore = useSettingsStore(); // Inicjalizujemy store

  // Pobieramy dane ustawienia po załadowaniu komponentu
  onMounted(async () => {
    await settingsStore.fetchSettings();
  });
</script>

<template>
  <div>
    <Header title="Settings" />

    <main>
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-xl font-semibold mb-4">Ustawienia</h1>

        <!-- Wyświetlanie numeru oferty -->
        <p><strong>Numer oferty:</strong></p>

        <!-- Edytowanie numeru oferty -->
        <input
          v-model="settingsStore.setting.offerNumber"
          type="number"
          placeholder="Wpisz nowy numer oferty"
          class="p-2 border rounded mr-2"
        />

        <!-- Przycisk do zapisania ustawienia -->
        <Button @click="settingsStore.saveSetting"> Zapisz </Button>
        <ul v-if="settingsStore.errors.offerNumber" class="text-red-600 mt-2 text-sm">
  <li v-for="(error, index) in settingsStore.errors.offerNumber" :key="index">
    {{ error }}
  </li>
</ul>
      </div>
    </main>
  </div>
</template>

<style scoped></style>
