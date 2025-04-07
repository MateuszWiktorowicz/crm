<script setup>
  import useCustomerStore from '../../store/customer';
  import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
  import InputError from '../../components/InputError.vue';
  import useUserStore from '../../store/user';

  const customerStore = useCustomerStore();
  const userStore = useUserStore();

  const saveCustomer = async () => {
    await customerStore.saveCustomer(customerStore.customer);
  };
</script>

<template>
  <TransitionRoot appear :show="customerStore.isModalOpen" as="template">
    <Dialog as="div" class="relative z-10" @close="customerStore.closeModal">
      <!-- Tło modalu -->
      <div class="fixed inset-0 bg-black bg-opacity-30"></div>

      <div class="fixed inset-0 flex items-center justify-center">
        <DialogPanel class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg overflow-hidden">
          <DialogTitle class="text-lg font-semibold">
            {{ customerStore.customer?.id ? 'Edytuj Klienta' : 'Dodaj Klienta' }}
          </DialogTitle>

          <form @submit.prevent="saveCustomer" class="space-y-4 overflow-y-auto max-h-[80vh]">
            <div
              v-if="Object.keys(customerStore.errors).length > 0"
              class="mb-4 max-h-48 overflow-auto"
            >
              <ul class="bg-red-100 text-red-800 border border-red-400 p-4 rounded">
                <li v-for="(error, index) in customerStore.errors" :key="index">
                  {{ error }}
                </li>
              </ul>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium">Kod</label>
              <input v-model="customerStore.customer.code" class="w-full p-2 border rounded" />

              <div v-if="customerStore.errors.code" class="text-red-600 text-sm">
                <p>{{ customerStore.errors.code[0] }}</p>
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium">Nazwa</label>
              <input
                type="text"
                v-model="customerStore.customer.name"
                class="w-full p-2 border rounded"
              />
              <!-- Wyświetlanie błędów dla pola name -->
              <div v-if="customerStore.errors.name" class="text-red-600 text-sm">
                <p>{{ customerStore.errors.name[0] }}</p>
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium">NIP</label>
              <input
                type="text"
                v-model="customerStore.customer.nip"
                class="w-full p-2 border rounded"
              />
              <!-- Wyświetlanie błędów dla pola nip -->
              <div v-if="customerStore.errors.nip" class="text-red-600 text-sm">
                <p>{{ customerStore.errors.nip[0] }}</p>
              </div>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium">Kod pocztowy</label>
              <input
                type="text"
                v-model="customerStore.customer.zip_code"
                class="w-full p-2 border rounded"
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium">Miasto</label>
              <input
                type="text"
                v-model="customerStore.customer.city"
                class="w-full p-2 border rounded"
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium">Adres</label>
              <input
                type="text"
                v-model="customerStore.customer.address"
                class="w-full p-2 border rounded"
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium">Znacznik</label>
              <input
                type="text"
                v-model="customerStore.customer.saler_marker"
                class="w-full p-2 border rounded"
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium">Uwagi</label>
              <input
                type="text"
                v-model="customerStore.customer.description"
                class="w-full p-2 border rounded"
              />
            </div>

            <!-- Przyciski -->
            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="customerStore.closeModal"
                class="px-4 py-2 bg-gray-300 rounded"
              >
                Anuluj
              </button>
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Zapisz</button>
            </div>
          </form>
        </DialogPanel>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
