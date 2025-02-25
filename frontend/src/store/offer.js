import { defineStore } from 'pinia';
import axiosClient from '../axios';

const useOfferStore = defineStore('offer', {
    state: () => ({
        offers: [],
        offer: {
            customer_id: '',
            status_id: '',
            total_price: 0,
            changed_by: '',
            created_at: '',
            updated_at: '',
        },
        offerDetails: [
            { 
                toolType: '', 
                flutesNumber: '', 
                diameter: '', 
                tool_quantity: 0, 
                tool_discount: 0, 
                tool_net_price: 0, 
                tool_gross_price: 0,
                tool_geometry_id: null,
                coating_price_id: null,
                coating_quantity: 0,
                coating_discount: 0,
                coating_net_price: 0,
                coating_gross_price: 0
            }
        ],
        isModalOpen: false,
        
    }),
    actions: {
        async fetchOffers() {
            try {
                const response  = await axiosClient.get('/api/offers');
                this.offers = response.data.offers;
                console.log(this.offers);
            } catch (error) {
                console.log(error);
            }
        },
        async createOffer() {
            try {
                const payload = {
                    customer_id: this.offer.customer_id,
                    status_id: 1, // np. domyślny status "nowa oferta"
                    total_price: this.offerDetails.reduce((sum, detail) => sum + detail.tool_gross_price, 0),
                    offer_details: this.offerDetails
                };

                await axiosClient.post('/api/offers', payload);
                this.closeModal();
                this.fetchOffers();
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
            this.offerDetails.push({ 
                toolType: '', 
                flutesNumber: '', 
                diameter: '', 
                tool_quantity: 0, 
                tool_discount: 0, 
                tool_net_price: 0, 
                tool_gross_price: 0,
                tool_geometry_id: null,
                coating_price_id: null,
                coating_quantity: 0,
                coating_discount: 0,
                coating_net_price: 0,
                coating_gross_price: 0
            });
        },
        removeToolRow(index) {
            this.offerDetails.splice(index, 1);
        },
        updateToolDetail(index, selectedTool) {
            if (selectedTool) {
                this.offerDetails[index].tool_geometry_id = selectedTool.id;
                this.offerDetails[index].tool_net_price = selectedTool.face_grinding_price || 0;
                this.offerDetails[index].tool_gross_price = selectedTool.face_grinding_price * 1.23; // VAT 23%
            }
        },
        async destroyOffer(id) {
            try {
                // Send DELETE request to your backend to delete the offer
                const response = await axiosClient.delete(`/api/offers/${id}`);
    
                // After successful deletion, remove the offer from the local state
                this.offers = this.offers.filter(offer => offer.id !== id);
    
                return response.data;
            } catch (error) {
                console.log(error);
                throw new Error('Failed to delete offer');
            }
        }
    }
});

export default useOfferStore;