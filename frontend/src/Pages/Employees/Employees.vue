<script setup>
  import { onMounted } from 'vue';
  import useUserStore from '../../store/user';
  import UserModal from './UserModal.vue';
  import FilterInput from '../../components/FilterInput.vue';
  import Button from '@/components/Button.vue';

  const userStore = useUserStore();

  onMounted(() => {
    userStore.fetchUsers();
  });
</script>

<template>
  <div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Lista użytkowników</h1>
    <Button @click="userStore.openModal()" variant="success" class="mb-2">
      + Dodaj użytkownika
    </Button>
    <div
      class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300"
    >
      <table class="w-full border-separate border-spacing-0">
        <thead class="bg-gray-100 sticky top-0 z-10">
          <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal rounded-t-lg">
            <th class="border border-gray-300 p-3 text-left">
              Imię
              <FilterInput :store="userStore" column="name" placeholder="Filtruj" />
            </th>
            <th class="border border-gray-300 p-3 text-left">
              E-mail
              <FilterInput :store="userStore" column="email" placeholder="Filtruj" />
            </th>
            <th class="border border-gray-300 p-3 text-left">Uprawnienia</th>
            <th class="border border-gray-300 p-3 text-left">
              Znacznik
              <FilterInput :store="userStore" column="marker" placeholder="Filtruj" />
            </th>
            <th class="border border-gray-300 p-3 text-left">Akcje</th>
          </tr>
        </thead>
        <tbody v-if="userStore.filteredUsers.length > 0" class="text-gray-600 text-sm">
          <tr
            v-for="user in userStore.filteredUsers"
            :key="user.id"
            class="border-b border-gray-300 hover:bg-gray-50 transition"
          >
            <!-- <td class="border border-gray-300 p-3">{{ user.id }}</td> -->
            <td class="border border-gray-300 p-3">{{ user.name }}</td>
            <td class="border border-gray-300 p-3">{{ user.email }}</td>
            <td class="border border-gray-300 p-3">
              {{ Array.isArray(user.roles) ? user.roles.join(', ') : '-' }}
            </td>
            <td class="border border-gray-300 p-3">{{ user.marker }}</td>
            <td class="border border-gray-300 p-3">
              <Button @click="userStore.openModal(user)" variant="warning" class="mb-2 mr-2">
                Edytuj
              </Button>
              <Button @click="userStore.deleteUser(user.id)" variant="danger"> Usuń </Button>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <tr>
            <td colspan="8" class="text-center text-gray-500 p-4">
              Brak użytkowników do wyświetlenia.
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <UserModal />
  </div>
</template>
