import { defineStore } from 'pinia';
import axiosClient from '../axios.js';

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
        if (error.response?.data?.errors) {
          this.errors = error.response.data.errors;
        } else if (error.response?.data?.message) {
          this.errors = {
            offerNumber: [error.response.data.message], // ⬅️ Wrzuć `message` do errors.offerNumber
          };
        } else {
          this.errors = {
            offerNumber: ['Wystąpił nieoczekiwany błąd.'],
          };
        }
      }
    },
  },
});

export default useSettingsStore;
