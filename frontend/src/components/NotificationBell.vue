<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { BellIcon } from '@heroicons/vue/24/outline';
import { useNotificationStore } from '../store/notification';
import NotificationList from './NotificationList.vue';

const notificationStore = useNotificationStore();
const isOpen = ref(false);

const unreadCount = computed(() => notificationStore.unreadCount);
const hasUnread = computed(() => unreadCount.value > 0);

onMounted(() => {
  notificationStore.fetchUnreadCount();
  notificationStore.fetchNotifications();
  notificationStore.startPolling(30000); // Poll co 30 sekund
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  notificationStore.stopPolling();
  document.removeEventListener('click', handleClickOutside);
});

function toggleDropdown() {
  isOpen.value = !isOpen.value;
  if (isOpen.value) {
    notificationStore.fetchNotifications();
  }
}

function handleClickOutside(event: MouseEvent) {
  const target = event.target as HTMLElement;
  if (!target.closest('.notification-dropdown')) {
    isOpen.value = false;
  }
}
</script>

<template>
  <div class="relative notification-dropdown">
    <button
      @click="toggleDropdown"
      class="relative flex items-center justify-center rounded-full bg-gray-800 p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden"
    >
      <span class="absolute -inset-1.5" />
      <span class="sr-only">Powiadomienia</span>
      <BellIcon class="h-6 w-6" aria-hidden="true" />
      <span
        v-if="hasUnread"
        class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white"
      >
        {{ unreadCount > 9 ? '9+' : unreadCount }}
      </span>
    </button>

    <Transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 z-50 mt-2 w-96 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-hidden"
      >
        <NotificationList @close="isOpen = false" />
      </div>
    </Transition>
  </div>
</template>

