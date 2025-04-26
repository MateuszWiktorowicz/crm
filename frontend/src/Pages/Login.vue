<script setup>
  import GuestLayout from '../Layouts/GuestLayout.vue';
  import { ref } from 'vue';
  import axiosClient from '../axios.js';
  import router from '../router.js';
  import InputError from '../components/InputError.vue';
  import ApplicationLogo from '../components/ApplicationLogo.vue';

  const data = ref({
    email: '',
    password: '',
  });

  const errors = ref('');

  function submit() {
    axiosClient.get('/sanctum/csrf-cookie').then((response) => {
      axiosClient
        .post('/login', data.value)
        .then((response) => {
          router.push({ name: 'Dashboard' });
        })
        .catch((error) => {
          errors.value = error.response.data.errors;
        });
    });
  }
</script>

<template>
  <GuestLayout>
    <div class="flex items-center justify-center bg-gray-100">
      <div class="w-full max-w-md rounded-2xl bg-white p-10 shadow-2xl">
        <div class="flex justify-center mb-6">
          <ApplicationLogo />
        </div>

        <h1 class="text-center text-3xl font-bold text-gray-800">CRM - Regeneracja</h1>
        <p class="mt-2 text-center text-gray-500">Zaloguj się do swojego konta</p>

        <form @submit.prevent="submit" class="mt-8 space-y-6">
          <!-- E-mail -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
            <input
              id="email"
              name="email"
              type="email"
              v-model="data.email"
              autocomplete="email"
              class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-300 focus:outline-none"
            />
            <InputError :message="errors.email" class="mt-1" />
          </div>

          <!-- Hasło -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Hasło</label>
            <input
              id="password"
              name="password"
              type="password"
              v-model="data.password"
              autocomplete="current-password"
              class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-300 focus:outline-none"
            />
            <InputError :message="errors.password" class="mt-1" />
          </div>

          <!-- Przycisk logowania -->
          <div>
            <button
              type="submit"
              class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-white font-semibold hover:bg-indigo-500 transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-300"
            >
              Zaloguj się
            </button>
          </div>
        </form>
      </div>
    </div>
  </GuestLayout>
</template>


<style scoped></style>
