import { defineStore } from 'pinia';
import useToolsStore from './tools';
import axiosClient from '../axios';
import useCoatingStore from './coating';
import useSettingsStore from './settings'; 

const useOfferStore = defineStore('offer', {
  state: () => ({
    offers: [],
    offer: {
      offer_number: '',
      id: null,
      customer_id: '',
      status_id: 1,
      total_net_price: 0,
      changed_by: '',
      created_at: '',
      updated_at: '',
      status_name: 'Robocza',
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
        description: ''
      },
    ],
    $statuses: [],
    isModalOpen: false,
    errors: {},
  }),
  actions: {
    async fetchOffers() {
      try {
        const response = await axiosClient.get('/api/offers');
        this.offers = response.data.offers;
        this.statuses = response.data.statuses;
        console.log(this.offers);
      } catch (error) {}
    },
    async saveOffer() {
      const payload = {
        customer_id: this.offer.customer_id,
        status_id: this.offer.status_id || 1,
        total_net_price: Number(this.offer.total_net_price),
        offer_details: this.offerDetails,
      };
      try {
        this.errors = {};

        if (this.offer.id) {
          await axiosClient.put(`/api/offers/${this.offer.id}`, payload);
        } else {
          await axiosClient.post('/api/offers', payload);
        }

        this.closeModal();
        this.fetchOffers();
      } catch (error) {
        if (error.response && error?.response?.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          console.error("Nieznany błąd:", error);
        }
      }
    },
    async generatePdf() {
      try {
        const settingsStore = useSettingsStore();
        await settingsStore.fetchSettings(); 



        const response = await axiosClient.get(`/api/offers/${this.offer.id}/generate-pdf`, {
          responseType: 'blob', 
        });

        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `offer_${this.offer.id}.pdf`);  
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

      } catch (error) {
        console.error("Failed to generate PDF:", error);
        alert('Wystąpił błąd przy generowaniu PDF.');
      }
    },
    openModal() {
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
      this.resetOffer();
      window.location.reload(); 
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
        description: '',
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
        if (error.response && error.response.data.error) {
          this.errors = [error.response.data.error];
        } else {
          this.errors = ['Wystąpił nieznany błąd.'];
        }
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
      console.log(offer);
    
      // Usuwamy spacje i zamieniamy przecinek na kropkę w 'total_net_price'
      let totalNetPrice = offer.total_net_price.replace(/\s+/g, '').replace(',', '.');
      
      // Konwertujemy na liczbę
      let parsedPrice = parseFloat(totalNetPrice);
    
      // Sprawdzamy, czy przekształcony wynik to prawidłowa liczba
      if (!isNaN(parsedPrice)) {
        this.offer = { ...offer, total_net_price: parsedPrice }; // Przypisujemy liczbę
      } else {
        console.error('Błąd konwersji total_net_price', offer.total_net_price);
      }
    
      this.offerDetails = offer.offer_details;
      this.offer.customer_id = offer.customer_id;
    
      // Dopasowanie statusu
      const matchedStatus = this.statuses.find(status => status.name === offer.status_name);
      this.offer.status_id = matchedStatus ? matchedStatus.id : 1;
    
      this.isModalOpen = true;
      console.log(this.offer);
    },
    formatDate(date) {
      const day = String(date.getDate()).padStart(2, '0');
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const year = date.getFullYear();
    
      return `${day}/${month}/${year}`;
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
        } else if(parseFloat(detail.radius) >= 2.5) {
          price += 5;
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
