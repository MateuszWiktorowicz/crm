<!-- components/ReusableModal.vue -->
<template>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" class="relative z-50" @close="onClose">
      <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="onClose" />

      <div class="fixed inset-0 flex items-center justify-center p-4">
        <DialogPanel
          class="relative z-10 w-full max-w-2xl bg-white rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]"
          ref="modalRef"
        >
          <!-- Header -->
          <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-teal-600 to-teal-700 text-white">
            <DialogTitle class="text-xl font-semibold">{{ title }}</DialogTitle>
            <button
              @click="onClose"
              class="text-white hover:text-gray-200 transition-colors p-1 rounded-full hover:bg-white/20"
              aria-label="Zamknij"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Wyszukiwanie -->
          <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <input
              ref="inputRef"
              v-model="searchQuery"
              type="text"
              :placeholder="searchPlaceholder"
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all"
            />
          </div>

          <!-- Lista -->
          <ul class="flex-1 overflow-y-auto divide-y divide-gray-200">
            <li v-if="isLoading" class="px-6 py-8 text-center text-gray-500">
              <div class="flex items-center justify-center">
                <svg
                  class="animate-spin h-5 w-5 mr-2 text-teal-600"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                Ładowanie...
              </div>
            </li>
            <template v-else>
              <li
                v-for="item in filteredItems"
                :key="item.id"
                class="px-6 py-4 hover:bg-teal-50 cursor-pointer transition-colors"
                @click="onSelect(item)"
              >
                <div class="font-medium text-gray-900">{{ item.name }}</div>
              </li>
              <li v-if="filteredItems.length === 0 && !isLoading" class="px-6 py-8 text-center text-gray-500">
                Brak wyników
              </li>
            </template>
          </ul>

          <!-- Paginacja -->
          <div
            v-if="pagination && pagination.last_page > 1"
            class="px-6 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between"
          >
            <div class="text-sm text-gray-700">
              Strona {{ pagination.current_page }} z {{ pagination.last_page }}
            </div>
            <div class="flex gap-2">
              <Button
                @click="handlePageChange(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1 || isLoading"
                variant="secondary"
                size="small"
              >
                Poprzednia
              </Button>
              <Button
                @click="handlePageChange(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page || isLoading"
                variant="secondary"
                size="small"
              >
                Następna
              </Button>
            </div>
          </div>

          <!-- Footer -->
          <div class="px-6 py-4 bg-gray-100 border-t border-gray-200 flex justify-end">
            <Button @click="onClose" variant="secondary"> Anuluj </Button>
          </div>
        </DialogPanel>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
  import { ref, computed, watch } from 'vue';
  import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
  import Button from './Button.vue';

  const props = defineProps({
    isOpen: Boolean,
    title: String,
    searchPlaceholder: String,
    items: {
      type: Array,
      default: () => [],
    },
    fetchFunction: {
      type: Function,
      default: null,
    },
    store: {
      type: Object,
      default: null,
    },
    onSelect: Function,
    onClose: Function,
  });

  const searchQuery = ref('');
  const inputRef = ref(null);
  const currentPage = ref(1);

  // Debounce timer
  let searchTimeout = null;

  // Computed dla items - jeśli jest store, użyj store.items, w przeciwnym razie props.items
  const itemsSource = computed(() => {
    if (props.store && props.store.customers) {
      return props.store.customers;
    }
    return props.items;
  });

  // Computed dla paginacji
  const pagination = computed(() => {
    if (props.store && props.store.pagination) {
      return props.store.pagination;
    }
    return null;
  });

  // Computed dla isLoading
  const isLoading = computed(() => {
    if (props.store && props.store.isLoading !== undefined) {
      return props.store.isLoading;
    }
    return false;
  });

  // Funkcja do pobierania danych
  const fetchItems = async (page = 1, search = '') => {
    if (!props.fetchFunction) {
      return;
    }

    try {
      const filters = search ? { name: search } : {};
      await props.fetchFunction(page, filters);
    } catch (error) {
      console.error('Błąd pobierania danych:', error);
    }
  };

  // Obserwuj zmiany wyszukiwania z debounce
  watch(searchQuery, (newValue) => {
    if (searchTimeout) {
      clearTimeout(searchTimeout);
    }
    searchTimeout = setTimeout(() => {
      currentPage.value = 1;
      if (props.fetchFunction) {
        fetchItems(1, newValue);
      }
    }, 300);
  });

  // Obserwuj otwieranie modala
  watch(
    () => props.isOpen,
    (open) => {
      if (open) {
        searchQuery.value = '';
        currentPage.value = 1;
        if (props.fetchFunction) {
          fetchItems(1, '');
        }
        setTimeout(() => inputRef.value?.focus(), 50);
      }
    }
  );

  const handlePageChange = (page) => {
    currentPage.value = page;
    fetchItems(page, searchQuery.value);
  };

  // Computed dla wyświetlanych elementów
  const filteredItems = computed(() => {
    const items = itemsSource.value;
    if (!props.fetchFunction) {
      // Lokalne filtrowanie jeśli nie ma fetchFunction
      return items.filter((item) =>
        item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    }
    // Jeśli jest fetchFunction, zwróć wszystkie items (filtrowanie po stronie serwera)
    return items;
  });
</script>
