import { defineStore } from "pinia";
import axiosClient from "../axios.js";

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
            } catch (error) {
                console.log('Error: ', error);
            }
        },
        setFilter(column, value) {

            this.filters[column] = value;
            this.filterCoatings();
          },
          filterCoatings() {
            if (!Array.isArray(this.coatings)) return;
         
            this.filteredCoatings = this.coatings.filter(coating =>
              Object.entries(this.filters).every(([key, value]) => {
                if (!value) return true; // Jeśli filtr pusty, akceptujemy wszystko
         
                const coatingValue = coating[key];
         
                // Jeśli toolValue to string, porównujemy z małymi literami
                if (typeof coatingValue === "string") {

                  return coatingValue.toLowerCase().includes(value.toLowerCase());
                }
         
                // Jeśli toolValue to liczba, konwertujemy na string
                if (typeof coatingValue === "number") {

                  return coatingValue.toString().includes(value.toString());
                }

                return false;
              })
            );
          }
        
        
    }
});

export default useCoatingStore;