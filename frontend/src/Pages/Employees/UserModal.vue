<script setup lang="ts">
  import { useUserStore } from '../../store/user';
  import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
  import InputError from '../../components/InputError.vue';
  import Button from '@/components/Button.vue';
  import { useToast } from '@/composables/useToast';
  import { watch } from 'vue';

  const userStore = useUserStore();
  const { success, error } = useToast();

  const saveUser = async () => {
    try {
      await userStore.saveUser();
      if (!userStore.errors || Object.keys(userStore.errors).length === 0) {
        success(userStore.user?.id ? 'Użytkownik został zaktualizowany' : 'Użytkownik został dodany');
      }
    } catch (err) {
      error('Błąd podczas zapisywania użytkownika');
    }
  };
</script>

<template>
  <TransitionRoot appear :show="userStore.isModalOpen" as="template">
    <Dialog as="div" class="relative z-10" @close="userStore.closeModal">
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

      <div class="fixed inset-0 flex items-center justify-center p-4">
        <DialogPanel class="w-full max-w-lg bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white">
            <DialogTitle class="text-xl font-semibold">
              {{ userStore.user?.id ? 'Edytuj Użytkownika' : 'Dodaj Użytkownika' }}
            </DialogTitle>
            <button
              @click="userStore.closeModal"
              class="text-white hover:text-gray-200 transition-colors p-1 rounded-full hover:bg-white/20"
              aria-label="Zamknij"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Form Content -->
          <form @submit.prevent="saveUser" class="flex-1 overflow-y-auto px-6 py-6 bg-gray-50 space-y-5">
            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-700">Imię</label>
              <input
                v-model="userStore.user.name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="Wprowadź imię"
              />
              <InputError :message="userStore.errors.name" />
            </div>

            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-700">E-mail</label>
              <input
                type="email"
                v-model="userStore.user.email"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="email@example.com"
              />
              <InputError :message="userStore.errors.email" />
            </div>

            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-700">Hasło</label>
              <input
                type="password"
                v-model="userStore.user.password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="Wprowadź hasło"
              />
              <InputError :message="userStore.errors.password" />
            </div>

            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-700">Potwierdź hasło</label>
              <input
                type="password"
                v-model="userStore.user.password_confirmation"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="Potwierdź hasło"
              />
            </div>

            <div class="space-y-1">
              <label class="block text-sm font-medium text-gray-700">Znacznik</label>
              <input
                type="text"
                v-model="userStore.user.marker"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                placeholder="Wprowadź znacznik"
              />
              <InputError :message="userStore.errors.marker" />
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Uprawnienia</label>
              <div class="flex flex-wrap gap-4">
                <label
                  v-for="role in userStore.rolesToAssign"
                  :key="role"
                  class="flex items-center space-x-2 cursor-pointer p-2 rounded-lg hover:bg-gray-100 transition-colors"
                >
                  <input
                    type="checkbox"
                    :id="role"
                    :value="role"
                    v-model="userStore.user.roles"
                    class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                  />
                  <span class="text-sm text-gray-700">{{ role }}</span>
                </label>
              </div>
              <InputError :message="userStore.errors.roles" />
            </div>
          </form>

          <!-- Footer -->
          <div class="px-6 py-4 bg-gray-100 border-t border-gray-200 flex justify-end gap-3">
            <Button @click="userStore.closeModal" variant="secondary"> Anuluj </Button>
            <Button type="submit" @click="saveUser"> Zapisz </Button>
          </div>
        </DialogPanel>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
