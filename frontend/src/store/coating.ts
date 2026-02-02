import { defineStore } from 'pinia';
import axiosClient from '../axios.js';
import { CoatingPrice, CoatingType } from '@/types/types.js';

interface CoatingFilter {
  mastermetName: string;
  mastermetCode: string;
  diameter: string;
}

interface CoatingStoreState {
  coatings: CoatingPrice[];
  coatingTypes: CoatingType[];
  selectedCoatings: string[];
  filteredCoatings: CoatingPrice[];
  filters: CoatingFilter;
}

const useCoatingStore = defineStore('coating', {
  state: (): CoatingStoreState => ({
    coatings: [],
    coatingTypes: [],
    selectedCoatings: [],
    filteredCoatings: [],
    filters: {
      mastermetName: '',
      mastermetCode: '',
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
    setFilter(column: keyof CoatingFilter, value: string) {
      this.filters[column] = value;
      this.filterCoatings();
    },
    filterCoatings() {
      if (!Array.isArray(this.coatings)) return;

      const nameFilter = this.filters.mastermetName.trim().toLowerCase();
      const codeFilter = this.filters.mastermetCode.trim().toLowerCase();
      const diameterFilter = this.filters.diameter.trim();

      this.filteredCoatings = this.coatings.filter((coating) => {
        const matchesName = nameFilter
          ? coating.coatingType?.mastermetName?.toLowerCase().includes(nameFilter)
          : true;

        const matchesCode = codeFilter
          ? coating.coatingType?.mastermetCode?.toLowerCase().includes(codeFilter)
          : true;

        const matchesDiameter = diameterFilter
          ? coating.diameter?.toString().includes(diameterFilter)
          : true;

        return matchesName && matchesCode && matchesDiameter;
      });
    },
  },
  getters: {
    findCoatingByDiameterAndCode: (state) => {
      return (diameter: number | null, code: string, toolType: string) => {
        if (toolType === "Niestandardowe" || toolType === "Kartoteka" || toolType === "Złom" || toolType === "Zwrot") diameter = 20;
        if (diameter === null || code === 'none') {
          return null;
        }
        const foundCoating = state.coatings.find(
          (coating) => coating.diameter === diameter && coating.coatingType.mastermetCode === code
        );
        return foundCoating;
      };
    },
  },
});

export default useCoatingStore;
