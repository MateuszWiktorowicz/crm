<script setup lang="ts">
  import useCustomerStore from '../../store/customer';
  import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
  import InputField from '../../components/Forms/InputField.vue';
  import { useUserStore } from '../../store/user';
  import Button from '@/components/Button.vue';
  import { Customer } from '@/types/types';
  import { watch } from 'vue';

  const { isModalOpen, closeModal } = defineProps<{
    isModalOpen: boolean;
    closeModal: () => void;
  }>();

  const userStore = useUserStore();
  const customerStore = useCustomerStore();

  const saveCustomer = async (customer: Customer) => {
    const response = await customerStore.saveCustomer(customer);
    if (response !== undefined) {
      closeModal();
    }
  };

  watch(
    () => userStore.isCreator(),
    (isCreator) => {
      if (!isCreator && userStore.loggedInUser?.marker) {
        customerStore.customer.salerMarker = userStore.loggedInUser.marker;
      }
    },
    { immediate: true }
  );
</script>

<template>
  <TransitionRoot appear :show="isModalOpen" as="template">
    <Dialog as="div" class="relative z-10">
      <div class="fixed inset-0 bg-black/50"></div>

      <div class="fixed inset-0 flex items-center justify-center">
        <DialogPanel
          class="w-full min-h-xl max-w-xl bg-[#D3D3D3] p-8 rounded-lg shadow-lg overflow-y-auto h-full"
        >
          <DialogTitle class="text-lg font-semibold">
            {{ customerStore.customer?.id ? 'Edytuj Klienta' : 'Dodaj Klienta' }}
          </DialogTitle>

          <form class="space-y-4 mt-3 overflow-y-auto max-h-[80vh]">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
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
                <InputField
                  v-model="customerStore.customer.zipCode"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'zipCode'"
                  label="Kod pocztowy"
                  inputId="zipCode"
                />
              </div>

              <div>
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
                <InputField
                  v-model="customerStore.customer.salerMarker"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'salerMarker'"
                  label="Znacznik"
                  inputId="salerMarker"
                  :disabled="!userStore.isCreator()"
                />
              </div>
              <div>
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
            <div class="flex gap-3">
              <Button @click="closeModal" variant="secondary"> Anuluj </Button>
              <Button @click="saveCustomer(customerStore.customer)"> Zapisz </Button>
            </div>
          </form>
        </DialogPanel>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
