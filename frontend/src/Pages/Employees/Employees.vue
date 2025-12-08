<script setup lang="ts">
  import { onMounted } from 'vue';
  import { useUserStore } from '../../store/user';
  import UserModal from './UserModal.vue';
  import FilterInput from '../../components/FilterInput.vue';
  import Button from '@/components/Button.vue';
  import SkeletonLoader from '@/components/SkeletonLoader.vue';
  import { useToast } from '@/composables/useToast';

  const userStore = useUserStore();
  const { success, error } = useToast();

  onMounted(() => {
    userStore.fetchUsers();
  });

  const handleDelete = async (id: number) => {
    try {
      await userStore.deleteUser(id);
      success('Użytkownik został usunięty');
    } catch (err) {
      error('Błąd podczas usuwania użytkownika');
    }
  };
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
        <tbody v-if="userStore.isLoading" class="text-gray-600 text-sm">
          <tr v-for="i in 5" :key="i" class="border-b border-gray-300">
            <td class="border border-gray-300 p-3" colspan="5">
              <SkeletonLoader type="table-row" :columns="5" />
            </td>
          </tr>
        </tbody>
        <tbody v-else-if="userStore.filteredUsers.length > 0" class="text-gray-600 text-sm">
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
              <div class="flex items-center gap-2">
                <button
                  @click="userStore.openModal(user)"
                  class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                  title="Edytuj"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                    />
                  </svg>
                </button>
                <button
                  @click="userStore.deleteUser(user.id)"
                  class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                  title="Usuń"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                    />
                  </svg>
                </button>
              </div>
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
