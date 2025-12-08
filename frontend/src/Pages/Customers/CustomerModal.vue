<script setup lang="ts">
  import useCustomerStore from '../../store/customer';
  import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
  import InputField from '../../components/Forms/InputField.vue';
  import { useUserStore } from '../../store/user';
  import Button from '@/components/Button.vue';
  import { useToast } from '@/composables/useToast';
  import { Customer } from '@/types/types';
  import { watch } from 'vue';

  const props = defineProps<{
    isModalOpen: boolean;
    closeModal: () => void;
  }>();

  const userStore = useUserStore();
  const customerStore = useCustomerStore();
  const { success, error } = useToast();

  const handleClose = () => {
    customerStore.resetCustomer();
    customerStore.errors = {};
    props.closeModal();
  };

  const saveCustomer = async (customer: Customer) => {
    try {
      // Sprawdź czy są błędy walidacji przed zapisem
      if (customerStore.errors && Object.keys(customerStore.errors).length > 0) {
        return;
      }

      await customerStore.saveCustomer(customer);
      
      // Sprawdź czy po zapisie nie ma błędów
      if (!customerStore.errors || Object.keys(customerStore.errors).length === 0) {
        success(customer.id ? 'Klient został zaktualizowany' : 'Klient został dodany');
        customerStore.resetCustomer();
        customerStore.errors = {};
        props.closeModal();
      }
    } catch (err) {
      // Błędy walidacji są już w store.errors, więc nie pokazujemy ogólnego błędu
      if (!customerStore.errors || Object.keys(customerStore.errors).length === 0) {
        error('Błąd podczas zapisywania klienta');
      }
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
  <TransitionRoot appear :show="props.isModalOpen" as="template">
    <Dialog as="div" class="relative z-10" @close="handleClose">
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

      <div class="fixed inset-0 flex items-center justify-center p-4">
        <DialogPanel
          class="w-full max-w-2xl bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]"
        >
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white">
            <DialogTitle class="text-xl font-semibold">
              {{ customerStore.customer?.id ? 'Edytuj Klienta' : 'Dodaj Klienta' }}
            </DialogTitle>
            <button
              @click="handleClose"
              type="button"
              class="text-white hover:text-gray-200 transition-colors p-1 rounded-full hover:bg-white/20"
              aria-label="Zamknij"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Form Content -->
          <form class="flex-1 overflow-y-auto px-6 py-6 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-1">
                <InputField
                  v-model="customerStore.customer.code"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'code'"
                  label="Kod"
                  inputId="code"
                  :disabled="Boolean(customerStore.customer?.id)"
                />
                <div v-if="customerStore.errors.code" class="text-red-600 text-sm mt-1">
                  <p>{{ customerStore.errors.code[0] }}</p>
                </div>
              </div>
              <div class="space-y-1">
                <InputField
                  v-model="customerStore.customer.name"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'name'"
                  label="Nazwa"
                  inputId="name"
                />
                <div v-if="customerStore.errors.name" class="text-red-600 text-sm mt-1">
                  <p>{{ customerStore.errors.name[0] }}</p>
                </div>
              </div>
              <div class="space-y-1">
                <InputField
                  v-model="customerStore.customer.nip"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'nip'"
                  label="NIP"
                  inputId="nip"
                  :disabled="Boolean(customerStore.customer?.id)"
                />
                <div v-if="customerStore.errors.nip" class="text-red-600 text-sm mt-1">
                  <p>{{ customerStore.errors.nip[0] }}</p>
                </div>
              </div>
              <div class="space-y-1">
                <InputField
                  v-model="customerStore.customer.zipCode"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'zipCode'"
                  label="Kod pocztowy"
                  inputId="zipCode"
                />
              </div>

              <div class="space-y-1">
                <InputField
                  v-model="customerStore.customer.city"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'city'"
                  label="Miasto"
                  inputId="city"
                />
              </div>
              <div class="space-y-1">
                <InputField
                  v-model="customerStore.customer.address"
                  :store="customerStore"
                  :object="'customer'"
                  :field="'address'"
                  label="Adres"
                  inputId="address"
                />
              </div>
              <div class="space-y-1">
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
              <div class="space-y-1 md:col-span-2">
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
          </form>

          <!-- Footer -->
          <div class="px-6 py-4 bg-gray-100 border-t border-gray-200 flex justify-end gap-3">
            <Button @click="handleClose" variant="secondary" type="button"> Anuluj </Button>
            <Button @click="saveCustomer(customerStore.customer)" type="button"> Zapisz </Button>
          </div>
        </DialogPanel>
      </div>
    </Dialog>
  </TransitionRoot>
</template>
