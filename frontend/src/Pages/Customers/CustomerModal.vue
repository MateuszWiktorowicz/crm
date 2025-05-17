<script setup>
  import useCustomerStore from '../../store/customer';
  import { watch } from 'vue';
  import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
  import InputError from '../../components/InputError.vue';
  import InputField from '../../components/Forms/InputField.vue';
  import useUserStore from '../../store/user';

  const customerStore = useCustomerStore();
  const userStore = useUserStore();

  const saveCustomer = async () => {
    await customerStore.saveCustomer(customerStore.customer);
  };
</script>

<template>
  <TransitionRoot appear :show="customerStore.isModalOpen" as="template">
    <Dialog as="div" class="relative z-10">
      <!-- Tło modalu -->
      <div class="fixed inset-0 bg-black/50"></div>

      <div class="fixed inset-0 flex items-center justify-center">
        <DialogPanel
          class="w-full min-h-xl max-w-xl bg-[#D3D3D3] p-8 rounded-lg shadow-lg overflow-y-auto h-full"
        >
          <DialogTitle class="text-lg font-semibold">
            {{ customerStore.customer?.id ? 'Edytuj Klienta' : 'Dodaj Klienta' }}
          </DialogTitle>

          <form @submit.prevent="saveCustomer" class="space-y-4 mt-3 overflow-y-auto max-h-[80vh]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <!-- <label class="block text-sm font-medium">Kod</label>
    <input 
    v-model="customerStore.customer.code" 
    class="w-full p-2 border rounded" 
    :disabled="customerStore.customer?.id"
    /> -->
                <InputField
                  v-model="customerStore.customer.code"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'code'"
                  label="Kod"
                  inputId="code"
                  :disabled="Boolean(customerStore.customer?.id)"
                />
                <div v-if="customerStore.errors.code" class="text-red-600 text-sm">
                  <p>{{ customerStore.errors.code[0] }}</p>
                </div>
              </div>

              <div>
                <!-- <label class="block text-sm font-medium">Nazwa</label> -->
                <!-- <input v-model="customerStore.customer.name" type="text" class="w-full p-2 border rounded" /> -->
                <InputField
                  v-model="customerStore.customer.name"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'name'"
                  label="Nazwa"
                  inputId="name"
                />
                <div v-if="customerStore.errors.name" class="text-red-600 text-sm">
                  <p>{{ customerStore.errors.name[0] }}</p>
                </div>
              </div>

              <div>
                <InputField
                  v-model="customerStore.customer.nip"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'nip'"
                  label="NIP"
                  inputId="nip"
                  :disabled="Boolean(customerStore.customer?.id)"
                />

                <div v-if="customerStore.errors.nip" class="text-red-600 text-sm">
                  <p>{{ customerStore.errors.nip[0] }}</p>
                </div>
              </div>

              <div>
                <!-- <label class="block text-sm font-medium">Kod pocztowy</label>
      <input v-model="customerStore.customer.zip_code" type="text" class="w-full p-2 border rounded" /> -->
                <InputField
                  v-model="customerStore.customer.zip_code"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'zip_code'"
                  label="Kod pocztowy"
                  inputId="zipCode"
                />
              </div>

              <div>
                <!-- <label class="block text-sm font-medium">Miasto</label>
      <input v-model="customerStore.customer.city" type="text" class="w-full p-2 border rounded" /> -->
                <InputField
                  v-model="customerStore.customer.city"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'city'"
                  label="Miasto"
                  inputId="city"
                />
              </div>

              <div>
                <!-- <label class="block text-sm font-medium">Adres</label>
      <input v-model="customerStore.customer.address" type="text" class="w-full p-2 border rounded" /> -->
                <InputField
                  v-model="customerStore.customer.address"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'address'"
                  label="Adres"
                  inputId="address"
                />
              </div>

              <div>
                <!-- <label class="block text-sm font-medium">Znacznik</label>
      <input v-model="customerStore.customer.saler_marker" type="text" class="w-full p-2 border rounded" /> -->
                <InputField
                  v-model="customerStore.customer.saler_marker"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'saler_marker'"
                  label="Znacznik"
                  inputId="salerMarker"
                />
              </div>

              <div>
                <!-- <label class="block text-sm font-medium">Uwagi</label>
      <input v-model="customerStore.customer.description" type="text" class="w-full p-2 border rounded" /> -->
                <InputField
                  v-model="customerStore.customer.description"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'description'"
                  label="Uwagi"
                  inputId="description"
                />
              </div>
            </div>

            <!-- Przyciski -->
            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="customerStore.closeModal"
                class="px-4 py-2 bg-gray-300 rounded cursor-pointer"
              >
                Anuluj
              </button>
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded cursor-pointer">
                Zapisz
              </button>
            </div>
          </form>
        </DialogPanel>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
