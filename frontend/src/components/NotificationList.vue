<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useNotificationStore } from '../store/notification';
import { useRouter } from 'vue-router';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/pl';

dayjs.extend(relativeTime);
dayjs.locale('pl');

const emit = defineEmits<{
  close: [];
}>();

const notificationStore = useNotificationStore();
const router = useRouter();
const currentPage = ref(1);
const pagination = ref<any>(null);

const notifications = computed(() => notificationStore.notifications);
const isLoading = computed(() => notificationStore.isLoading);

onMounted(async () => {
  await loadNotifications();
});

async function loadNotifications(page: number = 1) {
  try {
    const response = await notificationStore.fetchNotifications(page);
    pagination.value = response.meta;
    currentPage.value = page;
  } catch (error) {
    console.error('Błąd ładowania powiadomień', error);
  }
}

function formatDate(date: string) {
  return dayjs(date).fromNow();
}

function isUnread(notification: any) {
  return !notification.read_at;
}

async function handleNotificationClick(notification: any) {
  if (!notification.read_at) {
    await notificationStore.markAsRead(notification.id);
  }

  // Jeśli powiadomienie dotyczy oferty, przekieruj do oferty
  if (notification.data.offer_id) {
    emit('close');
    router.push({ name: 'Offers', query: { offerId: notification.data.offer_id } });
  }
}

async function markAllAsRead() {
  await notificationStore.markAllAsRead();
}

const hasUnread = computed(() => {
  return notifications.value.some((n) => !n.read_at);
});
</script>

<template>
  <div class="max-h-96 overflow-y-auto">
    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
      <h3 class="text-lg font-semibold text-gray-900">Powiadomienia</h3>
      <button
        v-if="hasUnread"
        @click="markAllAsRead"
        class="text-sm text-blue-600 hover:text-blue-800"
      >
        Oznacz wszystkie jako przeczytane
      </button>
    </div>

    <div v-if="isLoading" class="p-4 text-center text-gray-500">
      Ładowanie...
    </div>

    <div v-else-if="notifications.length === 0" class="p-4 text-center text-gray-500">
      Brak powiadomień
    </div>

    <div v-else class="divide-y divide-gray-200">
      <button
        v-for="notification in notifications"
        :key="notification.id"
        @click="handleNotificationClick(notification)"
        :class="[
          'w-full px-4 py-3 text-left hover:bg-gray-50 transition-colors',
          isUnread(notification) ? 'bg-blue-50' : '',
        ]"
      >
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <p
              :class="[
                'text-sm font-medium',
                isUnread(notification) ? 'text-gray-900' : 'text-gray-600',
              ]"
            >
              {{ notification.data.title }}
            </p>
            <p class="mt-1 text-sm text-gray-500">
              {{ notification.data.message }}
            </p>
            <p class="mt-1 text-xs text-gray-400">
              {{ formatDate(notification.created_at) }}
            </p>
          </div>
          <div v-if="isUnread(notification)" class="ml-2">
            <span class="h-2 w-2 rounded-full bg-blue-600"></span>
          </div>
        </div>
      </button>
    </div>

    <!-- Pagination -->
    <div
      v-if="pagination && pagination.last_page > 1"
      class="flex items-center justify-between border-t border-gray-200 px-4 py-3"
    >
      <button
        @click="loadNotifications(currentPage - 1)"
        :disabled="currentPage === 1"
        class="text-sm text-gray-600 hover:text-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Poprzednia
      </button>
      <span class="text-sm text-gray-600">
        Strona {{ currentPage }} z {{ pagination.last_page }}
      </span>
      <button
        @click="loadNotifications(currentPage + 1)"
        :disabled="currentPage === pagination.last_page"
        class="text-sm text-gray-600 hover:text-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Następna
      </button>
    </div>
  </div>
</template>

