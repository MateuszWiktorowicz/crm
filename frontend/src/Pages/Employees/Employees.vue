<script setup>
  import { onMounted } from 'vue';
  import useUserStore from '../../store/user';
  import UserModal from './UserModal.vue';
  import UsersFilterInput from '../../components/UsersFilterInput.vue';

  const userStore = useUserStore();

  onMounted(() => {
    userStore.fetchUsers();
  });
</script>

<template>
  <div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Lista użytkowników</h1>

    <button
      @click="userStore.openModal()"
      class="mb-4 px-5 py-2 bg-green-600 text-white rounded-lg shadow-md hover:bg-green-700 transition"
    >
      + Dodaj użytkownika
    </button>

    <div
      class="overflow-x-auto max-h-114 overflow-y-auto bg-white shadow-lg rounded-lg border border-gray-300"
    >
      <table class="w-full border-separate border-spacing-0">
        <thead class="bg-gray-100 sticky top-0 z-10">
          <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal rounded-t-lg">
            <!-- <th class="border border-gray-300 p-3 text-left">ID</th> -->
            <th class="border border-gray-300 p-3 text-left">
              Imię
              <UsersFilterInput :column="'name'" />
            </th>
            <th class="border border-gray-300 p-3 text-left">
              E-mail
              <UsersFilterInput :column="'email'" />
            </th>
            <th class="border border-gray-300 p-3 text-left">Uprawnienia</th>
            <th class="border border-gray-300 p-3 text-left">
              Znacznik
              <UsersFilterInput :column="'marker'" />
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
              <button
                @click="userStore.openModal(user)"
                class="mx-2 px-2 py-1 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600 transition"
              >
                Edytuj
              </button>
              <button
                @click="userStore.deleteUser(user.id)"
                class="mx-2 px-2 py-1 bg-red-500 text-white rounded-lg shadow hover:bg-red-600 transition"
              >
                Usuń
              </button>
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

    <!-- Modal -->
    <UserModal />
  </div>
</template>
