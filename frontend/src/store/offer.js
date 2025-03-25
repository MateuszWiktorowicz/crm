import { defineStore } from 'pinia';
import useToolsStore from './tools';
import axiosClient from '../axios';
import useCoatingStore from './coating';

const useOfferStore = defineStore('offer', {
  state: () => ({
    offers: [],
    offer: {
      customer_id: '',
      status_id: '',
      total_net_price: 0,
      changed_by: '',
      created_at: '',
      updated_at: '',
    },
    offerDetails: [
      {
        radius: 0,
        regrinding_option: 'face_regrinding',
        toolType: '',
        flutesNumber: '',
        diameter: '',
        tool_net_price: 0,
        quantity: 1,
        discount: 0,
        tool_geometry_id: null,
        coatingCode: 'none',
        coating_price_id: null,
        coating_net_price: 0,
      },
    ],
    isModalOpen: false,
  }),
  actions: {
    async fetchOffers() {
      try {
        const response = await axiosClient.get('/api/offers');
        this.offers = response.data.offers;
        console.log(this.offers)
      } catch (error) {}
    },
    async saveOffer() {
      const payload = {
        customer_id: this.offer.customer_id,
        status_id: this.offer.status_id || 1,
        total_net_price: this.offer.total_net_price,
        offer_details: this.offerDetails,
      };
      try {
        if (this.offer.id) {
          await axiosClient.put(`/api/offers/${this.offer.id}`, payload);
        } else {
          await axiosClient.post('/api/offers', payload);
        }

        this.closeModal();
        this.fetchOffers();
      } catch (error) {}
    },
    openModal() {
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
      this.resetOffer();
      // window.location.reload(); 
    },
    addToolRow() {
      this.offerDetails.push({
        radius: 0,
        regrinding_option: 'face_regrinding',
        toolType: '',
        flutesNumber: '',
        diameter: '',
        tool_net_price: 0,
        quantity: 1,
        discount: 0,
        tool_geometry_id: null,
        coatingCode: 'none',
        coating_price_id: null,
        coating_net_price: 0,
      });
    },
    removeToolRow(index) {
      this.offerDetails.splice(index, 1);
    },
    async destroyOffer(id) {
      try {
        if (confirm('Czy na pewno chcesz usunąć tę ofertę?')) {
        const response = await axiosClient.delete(`/api/offers/${id}`);
        this.offers = this.offers.filter((offer) => offer.id !== id);
        return response.data;
        }
      } catch (error) {
        throw new Error('Failed to delete offer');
      }
    },
    resetDetail(index) {
      const detail = this.offerDetails[index];

      if (detail.toolType !== this.offerDetails[index].toolType) {
        detail.flutesNumber = '';
      }
      detail.radius = 0;
      detail.quantity = 1;
      detail.discount = 0;
      detail.tool_net_price = 0;
      detail.tool_geometry_id = null;
      detail.coating_price_id = null;
      detail.coating_net_price = 0;
      detail.coatingCode = 'none';
      detail.diameter = 0;
      detail.regrinding_option = 'face_regrinding';
    },
    editOffer(offer) {
      console.log("🟢 Dane w editOffer:", JSON.parse(JSON.stringify(offer)));
      this.offer = { ...offer };
      this.offerDetails = offer.offer_details;
      this.offer.customer_id = offer.customer_id;
      this.isModalOpen = true;
      console.log("🟢 Dane na koniec:", JSON.parse(JSON.stringify(this.offerDetails)));

    },
    resetOffer() {
      this.offer.customer_id = '';
      this.offer.status_id = '';
      this.offer.total_net_price = 0;
      this.offer.changed_by = '';
      this.offer.created_at = '';
      this.offer.updated_at = '';
        this.offerDetails.forEach((detail, index) => {
          detail.flutesNumber = '';
          detail.toolType = '';
          this.resetDetail(index);
        });
    },
    updateToolNetPrice(index) {
      const toolStore = useToolsStore();
      const detail = this.offerDetails[index];

      let price = 0;
                 
      if (detail.toolType && detail.flutesNumber && detail.diameter && detail.regrinding_option) {

        const tool = toolStore.getSelectedTool(detail.toolType, detail.flutesNumber, detail.diameter);
        detail.regrinding_option === "face_regrinding" ? price = parseFloat(tool.face_grinding_price) : price = (parseFloat(tool.face_grinding_price) + parseFloat(tool.periphery_grinding_price));

        if (parseFloat(detail.radius) < 1) {
          price -= 5;
          console.log("mniejszy: od 1", detail.radius);
        } else if(parseFloat(detail.radius) >= 2.5) {
          price += 5;
          console.log("wiekszy od 2,5: ", detail.radius);
        }

        detail.tool_net_price = price.toFixed(2);
      } else {
        detail.tool_net_price = 0;
      }

      this.calculateOfferTotalNetPrice();
    },
      updateCoatingNetPrice(index) {
        const coatingStore = useCoatingStore();
        const detail = this.offerDetails[index];

        if (detail.diameter && detail.coatingCode && detail.coatingCode !== "none") {

          const coating = coatingStore.findCoatingByDiameterAndCode(detail.diameter, detail.coatingCode);
          detail.coating_price_id = coating.id;
          detail.coating_net_price = coating.price;
        } else {
          detail.coating_net_price = 0;
        }

        this.calculateOfferTotalNetPrice();
      },
      isRadiusEndMill(detail) {
        return detail.toolType === "Frez Promieniowy";
      },

    calculateOfferTotalNetPrice() {
      let totalNetPrice = 0;
  
      this.offerDetails.forEach(detail => {
        const detailPrice = this.getTotalNetDetailPrice(detail);
        totalNetPrice += parseFloat(detailPrice);
      });
  
      this.offer.total_net_price = totalNetPrice.toFixed(2);
    },
  },
  getters: {
    getRegrindingOptions: (state) => (detail) => {
      const toolStore = useToolsStore();
      const tool = toolStore.getSelectedTool(detail.toolType, detail.flutesNumber, detail.diameter);
    
      if (tool) {
        console.log(tool)
        console.log(tool.regrinding_options )
        detail.tool_geometry_id = tool.id;
        return tool.regrinding_options || [];
      }
    
      return [];
    },
    getTotalNetDetailPrice: (state) => (detail) => {
      if (!detail.tool_net_price || !detail.quantity) return 0;
    
      const priceWithoutDiscount = (parseFloat(detail.tool_net_price) + parseFloat(detail.coating_net_price)) * parseFloat(detail.quantity);

      const discountAmount = (parseFloat(detail.discount ?? 0) / 100) * priceWithoutDiscount;

      return (priceWithoutDiscount - discountAmount).toFixed(2);
    },
  },
});

export default useOfferStore;
