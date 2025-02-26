import { defineStore } from 'pinia';
import useToolsStore from './tools'
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
                tool_total_net_price: 0, 
                tool_total_gross_price: 0,
                tool_geometry_id: null,
                coating_price_id: null,
                coating_quantity: 0,
                coating_discount: 0,
                coating_total_net_price: 0,
                coating_total_gross_price: 0
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
                    total_price: this.calculateOfferTotalNetGrossPrice,
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
        async destroyOffer(id) {
            try {
                const response = await axiosClient.delete(`/api/offers/${id}`);
    
                this.offers = this.offers.filter(offer => offer.id !== id);

                return response.data;
            } catch (error) {
                console.log(error);
                throw new Error('Failed to delete offer');
            }
        },
        setToolGeometry(index) {
            const detail = this.offerDetails[index];
            detail.tool_geometry_id = this.getToolId(index);

            console.log(detail.tool_geometry_id)
        },
        resetDetail(index) {
            const detail = this.offerDetails[index];

            if (detail.toolType !== this.offerDetails[index].toolType) {
            detail.flutesNumber = '';
            }
            detail.diameter = '';
            detail.tool_quantity = 0;
            detail.tool_discount = 0;
            detail.tool_total_net_price = 0;
            detail.tool_total_gross_price = 0;
            detail.tool_geometry_id = null;
            detail.coating_price_id = null;
            detail.coating_quantity = 0;
            detail.coating_discount = 0;
            detail.coating_total_net_price = 0;
            detail.coating_total_gross_price = 0;
        }
    },
    getters: {
        getToolId: (state) => (index) => {
            const detail = state.offerDetails[index];
            const tool = state.getSelectedTool(detail.toolType, detail.flutesNumber, detail.diameter)

            return tool?.id ?? null;
        },
        getSelectedTool: (state) => (toolType, flutesNumber, diameter) => {
            const toolsStore = useToolsStore(); // Dostęp do store z narzędziami
    
            // Sprawdzenie, czy dane są dostępne
            if (!toolType || !flutesNumber || !diameter || !toolsStore.tools) return null;
    
            // Znalezienie narzędzia na podstawie typu, liczby wierteł i średnicy
            return toolsStore.tools.find(tool =>
                tool.tool_type_name === toolType &&
                tool.flutes_number === flutesNumber &&
                tool.diameter === diameter
            ) || null;
        },
        calculateDetailToolNetPrice: (state) => (index) => {
            const detail = state.offerDetails[index];
            if (!detail) return 0;

            const toolsStore = useToolsStore();

            const tool = state.getSelectedTool(detail.toolType, detail.flutesNumber, detail.diameter);
    
            if (!tool) return 0;
               console.log("Rabaty przed parsowaniem:", detail.tool_discount);

            const discount = ((100 - parseFloat(detail.tool_discount)) / 100);
            
            // Sprawdzenie, co wychodzi z parseFloat
            console.log("Parsed rabat:", discount);

            return (parseFloat(tool.face_grinding_price) * discount).toFixed(2);
        },
        calculateTotalToolNetPrice: (state) => (index) => {
            const detail = state.offerDetails[index];

            const netPrice = state.calculateDetailToolNetPrice(index);
            return (parseFloat(netPrice) * detail.tool_quantity).toFixed(2);
        },
        calculateTotalToolGrossPrice: (state) => (index) => {
            
            const netPrice = state.calculateTotalToolNetPrice(index);

            return (parseFloat(netPrice) * 1.23).toFixed(2);
        },
        calculateOfferTotalNetGrossPrice: (state) => {
            let totalNet = 0;
            state.offerDetails.forEach(detail => {
                totalNet += parseFloat(detail.tool_total_net_price);
            });

            return totalNet.toFixed(2);
        }
    } 
});

export default useOfferStore;