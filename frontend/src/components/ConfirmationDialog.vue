<!-- components/ConfirmationDialog.vue -->
<template>
  <TransitionRoot appear :show="visible" as="template">
    <Dialog as="div" class="relative z-[100]" @close="cancel">
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity"></div>

      <div class="fixed inset-0 flex items-center justify-center p-4">
        <DialogPanel class="w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden">
          <!-- Header -->
          <div class="px-6 py-4 bg-gradient-to-r from-red-500 to-red-600 text-white">
            <DialogTitle class="text-lg font-semibold">Potwierdzenie</DialogTitle>
          </div>

          <!-- Content -->
          <div class="px-6 py-6">
            <p class="text-gray-700 mb-6">{{ message }}</p>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 bg-gray-100 border-t border-gray-200 flex justify-end gap-3">
            <Button @click="cancel" variant="secondary"> Anuluj </Button>
            <Button @click="confirm" variant="danger"> Potwierdź </Button>
          </div>
        </DialogPanel>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
  import { ref, defineExpose } from 'vue';
  import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
  import Button from './Button.vue';

  const visible = ref(false);
  const message = ref('');
  let resolveFn;

  function open(msg) {
    message.value = msg;
    visible.value = true;
    return new Promise((resolve) => {
      resolveFn = resolve;
    });
  }

  function confirm() {
    visible.value = false;
    resolveFn(true);
  }

  function cancel() {
    visible.value = false;
    resolveFn(false);
  }

  defineExpose({ open });
</script>
