<script setup>
import Header from '../components/Header.vue';

const users = [
    {
        id: 1,
        name: 'John Doe',
        email: 'john@gmail.com',
        role: 'Admin'
    },
    {
        id: 2,
        name: 'Doe',
        email: 'john@gmail.com',
        role: 'office'
    },
    {
        id: 3,
        name: 'John2',
        email: 'john@gmail.com',
        role: 'saler'
    },
];
</script>

<template>

<Header title='Pracownicy' />
<main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        Pracownicy
    </div>  
    <div class="flex justify-center">
    <div class="max-w-4xl max-h-96 overflow-auto border border-gray-300 rounded-lg shadow-sm">
      <div class="min-w-[600px]">
        <table class="w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Name
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Email
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Role
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in users" :key="user.email">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.role }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

</template>

<style scoped>

</style>