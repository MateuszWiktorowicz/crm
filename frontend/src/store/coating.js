import { defineStore } from 'pinia';
import axiosClient from '../axios.js';

const useCoatingStore = defineStore('coating', {
  state: () => ({
    coatings: [],
    coatingTypes: [],
    selectedCoatings: [],
    filteredCoatings: [],
    filters: {
      mastermet_name: '',
      mastermet_code: '',
      diameter: '',
    },
  }),
  actions: {
    async fetchCoatings() {
      try {
        const response = await axiosClient.get('/api/coatings');
        this.coatings = response.data.coatings;
        this.coatingTypes = response.data.coatingTypes;
        this.filteredCoatings = this.coatings;
      } catch (error) {}
    },
    setFilter(column, value) {
      this.filters[column] = value;
      this.filterCoatings();
    },
    filterCoatings() {
      if (!Array.isArray(this.coatings)) return;

      this.filteredCoatings = this.coatings.filter((coating) =>
        Object.entries(this.filters).every(([key, value]) => {
          if (!value) return true;

          const coatingValue = coating[key];

          if (typeof coatingValue === 'string') {
            return coatingValue.toLowerCase().includes(value.toLowerCase());
          }
          if (typeof coatingValue === 'number') {
            return coatingValue.toString().includes(value.toString());
          }
          return false;
        })
      );
    },
  },
  getters: {
    findCoatingByDiameterAndCode: (state) => {
      return (diameter, code) => {
        if (diameter === '' || code === 'none') {
          return null;
        }
        const foundCoating = state.coatings.find(
          (coating) => coating.diameter === parseInt(diameter) && coating.mastermet_code === code
        );
        return foundCoating;
      };
    },
  },
});

export default useCoatingStore;
