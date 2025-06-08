<!-- components/ConfirmationDialog.vue -->
<template>
  <div
    v-if="visible"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100]"
  >
    <div class="bg-white p-4 rounded shadow-lg">
      <p class="mb-4">{{ message }}</p>
      <div class="flex justify-end gap-2">
        <button @click="confirm" class="bg-green-500 px-4 py-2 text-white rounded">OK</button>
        <button @click="cancel" class="bg-gray-300 px-4 py-2 rounded">Anuluj</button>
      </div>
    </div>
  </div>
</template>

<script setup>
  import { ref, defineExpose } from 'vue';

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
