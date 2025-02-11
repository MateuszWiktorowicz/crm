<script setup>
import useCustomerStore from "../../store/customer";
import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from "@headlessui/vue";
import InputError from "../../components/InputError.vue";

const customerStore = useCustomerStore();

const saveCustomer = async () => {
  await customerStore.saveCustomer(customerStore.customer);
};
</script>

<template>
    <TransitionRoot appear :show="customerStore.isModalOpen" as="template">
      <Dialog as="div" class="relative z-10" @close="customerStore.closeModal">
        <div class="fixed inset-0 bg-black bg-opacity-30"></div>
  
        <div class="fixed inset-0 flex items-center justify-center">
          <DialogPanel class="w-full max-w-md bg-white p-6 rounded-lg shadow-lg">
            <DialogTitle class="text-lg font-semibold">
              {{ customerStore.customer?.id ? "Edytuj Klienta" : "Dodaj Klienta" }}
            </DialogTitle>
  
            <form @submit.prevent="saveCustomer">
              <div class="mb-4">
                <label class="block text-sm font-medium">Kod</label>
                <input v-model="customerStore.customer.code" class="w-full p-2 border rounded" />
              </div>
  
              <div class="mb-4">
                <label class="block text-sm font-medium">Nazwa</label>
                <input type="text" v-model="customerStore.customer.name" class="w-full p-2 border rounded" />
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium">NIP</label>
                <input type="text" v-model="customerStore.customer.nip" class="w-full p-2 border rounded" />
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium">Kod pocztowy</label>
                <input type="text" v-model="customerStore.customer.zip_code" class="w-full p-2 border rounded" />
              </div>
  
              <div class="mb-4">
                <label class="block text-sm font-medium">Miasto</label>
                <input type="text" v-model="customerStore.customer.city" class="w-full p-2 border rounded" />
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium">Adres</label>
                <input type="text" v-model="customerStore.customer.address" class="w-full p-2 border rounded" />
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium">Znacznik</label>
                <input type="text" v-model="customerStore.customer.saler_marker" class="w-full p-2 border rounded" />
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium">Uwagu</label>
                <input type="text" v-model="customerStore.customer.description" class="w-full p-2 border rounded" />
              </div>

              <div class="flex justify-end space-x-2">
                <button type="button" @click="customerStore.closeModal" class="px-4 py-2 bg-gray-300 rounded">
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
  

  