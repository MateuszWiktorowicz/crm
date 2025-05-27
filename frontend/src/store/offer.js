import { defineStore } from 'pinia';
import useToolsStore from './tools';
import axiosClient from '../axios';
import useCoatingStore from './coating';
import useSettingsStore from './settings';
import useCustomerStore from './customer';

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
      globalDiscount: 0,
      pdfInfo: {
        deliveryTime: '12–15 dni roboczych',
        offerValidity: '7 dni',
        paymentTerms: 'przelew 14 dni',
        displayDiscount: false,
      },
    },
    offerDetails: [
      {
        symbol: '',
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
        fileId: null,
      },
    ],
    filteredOffers: [],
    filters: {
      offer_number: '',
      customer_name: '',
      employee_name: '',
      status_name: '',
      created_at: '',
    },
    $statuses: [],
    isModalOpen: false,
    errors: {},
  }),
  actions: {
    async fetchOffers() {
      try {
        const response = await axiosClient.get('/api/offers');
        const rawOffers = response.data.offers;

        // Dodaj toolType="Kartoteka" tam, gdzie jest file_id
        this.offers = rawOffers.map((offer) => {
          return {
            ...offer,
            offer_details: offer.offer_details.map((detail) => ({
              ...detail,
              toolType: detail.fileId ? 'Kartoteka' : detail.toolType || '',
            })),
          };
        });

        this.statuses = response.data.statuses;
        this.filteredOffers = this.offers;

        console.log(this.offers);
      } catch (error) {
        console.error('Błąd przy pobieraniu ofert', error);
      }
    },
    async saveOffer() {
      this.formatDescriptions();
      this.offerDetails.forEach((detail) => {
        3;
        if (!this.isCustom(detail)) {
          detail.symbol = this.getSymbolForDetail(detail);
        }
      });
      const payload = {
        customer_id: this.offer.customer_id,
        status_id: this.offer.status_id || 1,
        total_net_price: Number(this.offer.total_net_price),
        offer_details: this.offerDetails,
        pdf_info: this.offer.pdfInfo,
      };
      try {
        this.errors = {};

        if (this.offer.id) {
          await axiosClient.put(`/api/offers/${this.offer.id}`, payload);
        } else {
          await axiosClient.post('/api/offers', payload);
        }
        this.fetchOffers();
        this.closeModal();
      } catch (error) {
        if (error.response && error?.response?.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          console.error('Nieznany błąd:', error);
        }
      }
    },
    async generatePdf() {
      try {
        const settingsStore = useSettingsStore();
        await settingsStore.fetchSettings();

        const response = await axiosClient.post(
          `/api/offers/${this.offer.id}/generate-pdf`,
          {
            ...this.offer.pdfInfo,
          },
          { responseType: 'blob' }
        );

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `Reg_${this.customer?.code}_${this.offer.offer_number}.pdf`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } catch (error) {
        console.error('Failed to generate PDF:', error);
        alert('Wystąpił błąd przy generowaniu PDF.');
      }
    },
    setToolGeometryIdIfCustom(index) {
      const detail = this.offerDetails[index];
      console.log(detail.toolType);

      // Sprawdzamy, czy toolType to "Niestandardowe"
      if (detail.toolType === 'Niestandardowe') {
        // Ustawiamy tool_geometry_id na 157
        detail.tool_geometry_id = 157;
        console.log('Tool Geometry ID zaktualizowane na: ', detail.tool_geometry_id);
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
    isCustom(detail) {
      return detail.toolType === 'Niestandardowe' || detail.toolType === 'Kartoteka';
    },
    applyGlobalDiscount() {
      this.offerDetails.forEach((detail) => {
        detail.discount = this.offer.globalDiscount;
        console.log(this.globalDiscount);
      });
      this.calculateOfferTotalNetPrice();
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

          this.fetchOffers();
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
      detail.tool_net_price = 0;
      detail.coating_net_price = 0;
      detail.symbol = '';
      detail.tool_geometry_id = null;
      detail.coating_price_id = null;
      detail.coatingCode = 'none';
      detail.diameter = 0;
      detail.regrinding_option = '';
      detail.fileId = 0;
    },
    editOffer(offer) {
      console.log(offer);

      // Usuwamy spacje i zamieniamy przecinek na kropkę w 'total_net_price'
      let totalNetPrice = offer.total_net_price.replace(/\s+/g, '').replace(',', '.');

      // Konwertujemy na liczbę
      let parsedPrice = parseFloat(totalNetPrice);

      // Sprawdzamy, czy przekształcony wynik to prawidłowa liczba
      if (!isNaN(parsedPrice)) {
        this.offer = { ...offer, total_net_price: parsedPrice, globalDiscount: 0 }; // Przypisujemy liczbę
      } else {
        console.error('Błąd konwersji total_net_price', offer.total_net_price);
      }

      this.offerDetails = offer.offer_details;
      this.offer.pdfInfo = offer.pdf_info;
      this.offer.customer_id = offer.customer_id;

      // Dopasowanie statusu
      const matchedStatus = this.statuses.find((status) => status.name === offer.status_name);
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
      if (this.offerDetails[index].toolType === 'Niestandardowe') return 0;

      const toolStore = useToolsStore();
      const detail = this.offerDetails[index];

      let price = 0;

      if (
        (detail.toolType && detail.flutesNumber && detail.diameter && detail.regrinding_option) ||
        detail.toolType === 'Niestandardowe'
      ) {
        const tool = toolStore.getSelectedTool(
          detail.toolType,
          detail.flutesNumber,
          detail.diameter
        );
        detail.regrinding_option === 'face_regrinding'
          ? (price = parseFloat(tool.face_grinding_price))
          : (price =
              parseFloat(tool.face_grinding_price) + parseFloat(tool.periphery_grinding_price));

        if (detail.toolType === 'Frez Promieniowy') {
          if (parseFloat(detail.radius) < 1) {
            price -= 5;
          } else if (parseFloat(detail.radius) >= 2.5) {
            price += 5;
          }
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

      if (
        (detail.diameter && detail.coatingCode && detail.coatingCode !== 'none') ||
        detail.toolType === 'Niestandardowe'
      ) {
        const coating = coatingStore.findCoatingByDiameterAndCode(
          detail.diameter,
          detail.coatingCode
        );
        detail.coating_price_id = coating.id;
        detail.coating_net_price = coating.price;
      } else {
        detail.coating_net_price = 0;
      }

      this.calculateOfferTotalNetPrice();
    },
    isRadiusEndMill(detail) {
      return detail.toolType === 'Frez Promieniowy';
    },

    calculateOfferTotalNetPrice() {
      let totalNetPrice = 0;

      this.offerDetails.forEach((detail) => {
        const detailPrice = this.getTotalNetDetailPrice(detail);
        totalNetPrice += parseFloat(detailPrice);
      });

      this.offer.total_net_price = totalNetPrice.toFixed(2);
    },
    setFilter(column, value) {
      this.filters[column] = value;
      this.filterOffers();
    },
    filterOffers() {
      if (!Array.isArray(this.offers)) return;

      this.filteredOffers = this.offers.filter((offer) =>
        Object.entries(this.filters).every(
          ([key, value]) => !value || (offer[key] || '').toLowerCase().includes(value.toLowerCase())
        )
      );
    },
    applyFileDataToDetail(index) {
      const toolStore = useToolsStore();
      const detail = this.offerDetails[index];

      if (detail.fileId !== null) {
        const file = toolStore.files.find((f) => f.id === detail.fileId);

        if (file) {
          detail.diameter = file.diameter || 0;
          detail.tool_net_price = file.price || 0;
          detail.flutesNumber = file.flutesNumber ?? null;
          detail.fileId = file.id ?? null;
          detail.symbol = file.name ?? '';
        } else {
          console.warn(`Nie znaleziono pliku o ID: ${detail.fileId}`);
        }

        this.updateCoatingNetPrice(index);
        this.calculateOfferTotalNetPrice();
      }
    },
    formatDescriptions() {
      this.offerDetails.forEach((detail) => {
        if (['Frez Walcowy', 'Frez Promieniowy', 'Frez Kulowy', "Fazownik", "Wiertlo Krete"].includes(detail.toolType)) {
          let prefix = "";
    
          if (detail.regrinding_option === "face_regrinding") {
            prefix = "ostrzenie czoła";
          } else if (detail.regrinding_option === "full_regrinding") {
            prefix = "ostrzenie kpl.";
          }
    
          if (prefix) {
            if (!detail.description || detail.description.trim() === "") {
              detail.description = prefix;
            } else if (!detail.description.startsWith(prefix)) {
              detail.description = `${prefix} ${detail.description}`;
            }
          }
        }
      });
    }
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
      const priceWithoutDiscount =
        (parseFloat(detail.tool_net_price) + parseFloat(detail.coating_net_price)) *
        parseFloat(detail.quantity);

      const discountAmount = (parseFloat(detail.discount ?? 0) / 100) * priceWithoutDiscount;

      return (priceWithoutDiscount - discountAmount).toFixed(2);
    },
    getRadius: (state) => (detail) => {
      switch (detail.toolType) {
        case 'Frez Promieniowy':
          return detail.radius ?? 0;
        case 'Frez Kulowy':
          return detail.diameter / 2 ?? 0;
        default:
          return 0;
      }
    },
    getSymbolForDetail: (state) => (detail) => {
      switch (detail.toolType) {
        case 'Niestandardowe':
          return detail.symbol || ''; // Wpisujemy ręcznie

        case 'Kartoteka':
          const toolStore = useToolsStore();
          const file = toolStore.files.find((f) => f.id === detail.fileId);
          return file ? file.name : ''; // Pokazujemy nazwę pliku

        default:
          // Łączymy toolType, diameter, flutesNumber i coating
          let symbol = `${detail.toolType} D${detail.diameter} Z-${detail.flutesNumber}`;

          // Dodajemy coatingCode tylko, jeśli nie jest 'none'
          if (detail.coatingCode && detail.coatingCode !== 'none') {
            symbol += ` ${detail.coatingCode}`;
          }

          return symbol;
      }
    },
    customer() {
      const customerStore = useCustomerStore();
      return customerStore.customers.find(
        (c) => c.id === this.offer.customer_id
      );
    }
  },
});

export default useOfferStore;
