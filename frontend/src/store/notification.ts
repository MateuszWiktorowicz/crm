import { defineStore } from 'pinia';
import axiosClient from '../axios.js';
import { useToast } from '../composables/useToast';

interface Notification {
  id: string;
  type: string;
  data: {
    type: string;
    title: string;
    message: string;
    offer_id?: number;
    offer_number?: string;
    customer_name?: string;
    salesperson_name?: string;
    old_status?: string;
    new_status?: string;
  };
  read_at: string | null;
  created_at: string;
}

interface NotificationState {
  notifications: Notification[];
  unreadCount: number;
  isLoading: boolean;
  isPolling: boolean;
  lastPollTime: number | null;
}

export const useNotificationStore = defineStore('notification', {
  state: (): NotificationState => ({
    notifications: [],
    unreadCount: 0,
    isLoading: false,
    isPolling: false,
    lastPollTime: null,
  }),

  actions: {
    async fetchNotifications(page: number = 1) {
      this.isLoading = true;
      try {
        const response = await axiosClient.get('/api/notifications', {
          params: { per_page: 15, page },
        });
        this.notifications = response.data.data;
        return response.data;
      } catch (error) {
        console.error('Błąd pobierania powiadomień', error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async fetchUnreadCount() {
      try {
        const response = await axiosClient.get('/api/notifications/unread-count');
        const previousCount = this.unreadCount;
        this.unreadCount = response.data.count;

        // Jeśli liczba nieprzeczytanych wzrosła, wyświetl toast
        if (this.unreadCount > previousCount && previousCount > 0) {
          const { info } = useToast();
          const newCount = this.unreadCount - previousCount;
          info(`Masz ${newCount} now${newCount === 1 ? 'e' : 'ych'} powiadom${newCount === 1 ? 'ienie' : 'eń'}`);
        }
      } catch (error) {
        console.error('Błąd pobierania liczby nieprzeczytanych powiadomień', error);
      }
    },

    async markAsRead(id: string) {
      try {
        await axiosClient.put(`/api/notifications/${id}/read`);
        const notification = this.notifications.find((n) => n.id === id);
        if (notification) {
          notification.read_at = new Date().toISOString();
        }
        if (this.unreadCount > 0) {
          this.unreadCount--;
        }
      } catch (error) {
        console.error('Błąd oznaczania powiadomienia jako przeczytane', error);
        throw error;
      }
    },

    async markAllAsRead() {
      try {
        await axiosClient.put('/api/notifications/read-all');
        this.notifications.forEach((notification) => {
          if (!notification.read_at) {
            notification.read_at = new Date().toISOString();
          }
        });
        this.unreadCount = 0;
      } catch (error) {
        console.error('Błąd oznaczania wszystkich powiadomień jako przeczytane', error);
        throw error;
      }
    },

    startPolling(interval: number = 30000) {
      if (this.isPolling) {
        return;
      }

      this.isPolling = true;
      this.lastPollTime = Date.now();

      const poll = async () => {
        if (!this.isPolling) {
          return;
        }

        try {
          await this.fetchUnreadCount();
        } catch (error) {
          console.error('Błąd podczas polling powiadomień', error);
        }

        setTimeout(poll, interval);
      };

      poll();
    },

    stopPolling() {
      this.isPolling = false;
    },
  },
});

