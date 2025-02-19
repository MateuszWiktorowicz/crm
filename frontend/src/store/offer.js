import { defineStore } from 'pinia';
import axiosClient from '../axios';

const useOfferStore = defineStore('offer', {
    state: () => ({
        offers: [],
        offerDetails: [
            { toolType: '', flutesNumber: '', diameter: '' }
        ],
        isModalOpen: false,
        
    }),
    actions: {
        async fetchOffers() {
            try {
                const response  = await axiosClient.get('/api/offers');
                this.offers = response.data.offers;
            } catch (error) {
                console.log(error);
            }
        },
        openModal() {
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
        addToolRow() {
            this.offerDetails.push({ toolType: '', flutesNumber: '', diameter: '' });
        },
        removeToolRow(index) {
            this.offerDetails.splice(index, 1);
        },
    }
});

export default useOfferStore;