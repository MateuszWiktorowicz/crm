import { defineStore } from 'pinia';
import axiosClient from '../axios.js'; // Zakładam, że masz już skonfigurowanego axiosClient

interface SettingsStoreState {
  setting: {
    id: number;
    offerNumber: number;
  };
  errors: Record<string, string[]>;
}

const useSettingsStore = defineStore('settings', {
  state: (): SettingsStoreState => ({
    setting: {
      id: 1,
      offerNumber: 1,
    },
    errors: {},
  }),
  actions: {
    async fetchSettings() {
      try {
        const response = await axiosClient.get('/api/settings');
        this.setting = response.data;
      } catch (error) {
        console.error('Błąd pobierania ustawień:', error);
        throw error;
      }
    },

    async saveSetting() {
      try {
        if (this.setting.id) {
          await axiosClient.put(`/api/settings/${this.setting.id}`, this.setting);
        } else {
          await axiosClient.post('/api/settings', this.setting);
        }
        await this.fetchSettings();
        this.errors = {};
      } catch (error: any) {
        this.errors = error.response?.data?.errors ?? {}; // Przechwytywanie błędów
      }
    },
  },
});

export default useSettingsStore;
