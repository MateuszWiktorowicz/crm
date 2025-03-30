<script setup>
import GuestLayout from '../Layouts/GuestLayout.vue';
import { onMounted, ref } from 'vue';
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
  axiosClient.get('/sanctum/csrf-cookie').then(response => {
    axiosClient.post('/login', data.value)
      .then(response => {
        router.push({ name: 'Dashboard' });
    })
    .catch(error => {
        errors.value = error.response.data.errors;
    });
  });
}


</script>

<template>
<GuestLayout>
  <div class="bg-gray-100 p-8 rounded-lg shadow-lg">
    <ApplicationLogo />
    <h1 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-700">CRM - Regeneracja</h1>
    <h2 class="mt-2 text-center text-2xl/9 tracking-tight text-gray-600">Zaloguj się</h2>
    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label for="email" class="block text-sm/6 font-medium text-gray-900">E-mail</label>
          <div class="mt-2">
            <input 
            type="email" 
            name="email" 
            id="email" 
            v-model="data.email"
            autocomplete="email" 
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
          </div>
          <InputError :message="errors.email" />
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm/6 font-medium text-gray-900">Hasło</label>
          </div>
          <div class="mt-2">
            <input 
            type="password" 
            name="password" 
            id="password"
            v-model="data.password"
            autocomplete="current-password" 
            class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
          </div>
          <InputError :message="errors.password" />
        </div>

        <div>
          <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Zaloguj się</button>
        </div>
      </form>
    </div>
  </div>
</GuestLayout>
</template>

<style scoped>

</style>