<!-- components/ReusableModal.vue -->
<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center">
    <!-- Tło -->
    <div class="fixed inset-0 bg-black/50" @click="onClose" />

    <!-- Modal -->
    <div
      class="relative z-10 w-full max-w-2xl bg-white p-6 rounded-lg shadow-lg overflow-y-auto max-h-[80vh] flex flex-col"
      ref="modalRef"
    >
      <!-- Tytuł -->
      <div class="text-lg font-semibold mb-4">{{ title }}</div>

      <!-- Wyszukiwanie -->
      <div class="mb-4">
        <input
          ref="inputRef"
          v-model="searchQuery"
          type="text"
          :placeholder="searchPlaceholder"
          class="w-full p-2 border border-gray-300 rounded"
        />
      </div>

      <!-- Lista -->
      <ul class="divide-y divide-gray-200 flex-1 overflow-y-auto">
        <li
          v-for="item in filteredItems"
          :key="item.id"
          class="p-2 hover:bg-gray-100 cursor-pointer"
          @click="onSelect(item)"
        >
          <div class="font-medium">{{ item.name }}</div>
        </li>
      </ul>

      <Button @click="onClose" variant="secondary"> Anuluj </Button>
    </div>
  </div>
</template>

<script setup>
  import { ref, computed, watch, onMounted } from 'vue';
  import Button from './Button.vue';

  const props = defineProps({
    isOpen: Boolean,
    title: String,
    searchPlaceholder: String,
    items: Array,
    onSelect: Function,
    onClose: Function,
  });

  const searchQuery = ref('');
  const inputRef = ref(null);

  const filteredItems = computed(() =>
    props.items.filter((item) => item.name.toLowerCase().includes(searchQuery.value.toLowerCase()))
  );

  watch(
    () => props.isOpen,
    (open) => {
      if (open) {
        setTimeout(() => inputRef.value?.focus(), 50);
      }
    }
  );
</script>
