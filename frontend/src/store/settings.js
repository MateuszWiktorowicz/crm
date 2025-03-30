import { defineStore } from 'pinia';
import axiosClient from '../axios.js'; // Zakładam, że masz już skonfigurowanego axiosClient

const useSettingsStore = defineStore('settings', {
  state: () => ({
    setting: {
      offer_number: null,
    },
    errors: {},
  }),
  actions: {
    async fetchSettings() {
      try {
        const response = await axiosClient.get('/api/settings');
        this.setting = response.data;
        console.log(this.setting)
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
      } catch (error) {
        this.errors = error.response?.data?.errors ?? {}; // Przechwytywanie błędów
      }
    },
  },
});

export default useSettingsStore;
