<script setup>
  import { onMounted, ref } from 'vue';
  import Header from '../components/Header.vue';
  import useSettingsStore from '../store/settings.js';

  const settingsStore = useSettingsStore();

  onMounted(async () => {
    await settingsStore.fetchSettings();
  });
</script>

<template>
  <Header title="Dashboard" />
  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">Dashboard</div>
  </main>
</template>

<style scoped></style>
