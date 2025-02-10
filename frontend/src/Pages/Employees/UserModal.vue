<script setup>
import useUserStore from "../../store/user";
import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from "@headlessui/vue";
import InputError from "../../components/InputError.vue";

const userStore = useUserStore();

const saveUser = async () => {
  await userStore.saveUser(userStore.user);
};
</script>

<template>
    <TransitionRoot appear :show="userStore.isModalOpen" as="template">
      <Dialog as="div" class="relative z-10" @close="userStore.closeModal">
        <div class="fixed inset-0 bg-black bg-opacity-30"></div>
  
        <div class="fixed inset-0 flex items-center justify-center">
          <DialogPanel class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg">
            <DialogTitle class="text-lg font-semibold">
              {{ userStore.user?.id ? "Edytuj Użytkownika" : "Dodaj Użytkownika" }}
            </DialogTitle>
  
            <form @submit.prevent="saveUser">
              <div class="mb-4">
                <label class="block text-sm font-medium">Imię</label>
                <input v-model="userStore.user.name" class="w-full p-2 border rounded" />
              </div>
  
              <div class="mb-4">
                <label class="block text-sm font-medium">E-mail</label>
                <input type="email" v-model="userStore.user.email" class="w-full p-2 border rounded" />
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium">Hasło</label>
                <input type="password" v-model="userStore.user.password" class="w-full p-2 border rounded" />
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium">Potwierdź hasło</label>
                <input type="password" v-model="userStore.user.password_confirmation" class="w-full p-2 border rounded" />
              </div>
  
              <div class="mb-4">
                <label class="block text-sm font-medium">Znacznik</label>
                <input type="text" v-model="userStore.user.marker" class="w-full p-2 border rounded" />
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium">Uprawnienia</label>
                <input type="text" v-model="userStore.user.roles" class="w-full p-2 border rounded" />
              </div>

              <div class="flex justify-end space-x-2">
                <button type="button" @click="userStore.closeModal" class="px-4 py-2 bg-gray-300 rounded">
                  Anuluj
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                  Zapisz
                </button>
              </div>
            </form>
          </DialogPanel>
        </div>
      </Dialog>
    </TransitionRoot>
  </template>
  

  