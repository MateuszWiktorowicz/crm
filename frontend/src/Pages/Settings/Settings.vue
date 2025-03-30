<script setup>
import Header from '../../components/Header.vue';
import { ref, onMounted } from 'vue';
import useSettingsStore from '../../store/settings'; // Importujemy store Pinia

const settingsStore = useSettingsStore(); // Inicjalizujemy store

// Pobieramy dane ustawienia po załadowaniu komponentu
onMounted(async () => {
  await settingsStore.fetchSettings();
});
</script>

<template>
  <Header title="Settings" />

  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-xl font-semibold mb-4">Ustawienia</h1>

      <!-- Wyświetlanie numeru oferty -->
      <p><strong>Numer oferty:</strong> {{ settingsStore.setting.offer_number }}</p>

      <!-- Edytowanie numeru oferty -->
      <input v-model="settingsStore.setting.offer_number" type="number" placeholder="Wpisz nowy numer oferty" class="p-2 border rounded">

      <!-- Przycisk do zapisania ustawienia -->
      <button @click="settingsStore.saveSetting" class="mt-4 p-2 bg-blue-500 text-white rounded">Zapisz</button>
    </div>
  </main>
</template>

<style scoped>
</style>
