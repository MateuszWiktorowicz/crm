import { defineStore } from 'pinia';
import useSettingsStore from './settings';
import { OfferService } from '@/services/OfferService';
import { Offer, OfferDetail, OfferFilters, Status } from '@/types/types';
import axiosClient from '@/axios';

interface OfferState {
  isEditing: boolean;
  isInitialEditPhase: boolean;
  offers: Offer[];
  offer: Offer;
  filteredOffers: Offer[];
  filters: OfferFilters;
  statuses: Status[];
  errors: Record<string, any>;
}
export const useOfferStore = defineStore('offer', {
  state: (): OfferState => ({
    isEditing: false,
    isInitialEditPhase: false,
    offers: [],
    offer: {
      id: null,
      globalDiscount: 0,

      offerNumber: '',
      customer: null,
      status: { id: 1, name: 'Robocza' },
      totalPrice: 0,
      createdBy: null,
      changedBy: null,
      createdAt: '',
      updatedAt: '',
      pdfInfo: {
        deliveryTime: '12–15 dni roboczych',
        offerValidity: '7 dni',
        paymentTerms: 'przelew 14 dni',
        displayDiscount: false,
      },
      offerDetails: [
        {
          id: null,
          offerId: null,
          symbol: '',
          radius: 0,
          regrindingOption: 'face_regrinding',
          toolType: { id: 1, toolTypeName: 'Frez Walcowy' },
          toolNetPrice: 0,
          quantity: 1,
          discount: 0,
          toolGeometry: null,
          coatingPrice: {
            id: 0,
            diameter: 0,
            price: 0,
            coatingType: {
              id: 0,
              mastermetCode: '',
            },
          },
          coatingNetPrice: 0,
          description: '',
          tool: null,
          flutesNumber: null,
          diameter: null,
          isToolPriceManual: false,
          isCoatingPriceManual: false,
        },
      ],
    },
    filteredOffers: [],
    filters: {
      offerNumber: '',
      customerName: '',
      employeeName: '',
      statusName: '',
      createdAt: '',
    },
    statuses: [],
    errors: {},
  }),
  actions: {
    /*
Początek nowej logiki
*/

    async destroyOffer(this: OfferState & ReturnType<typeof useOfferStore>, id: number) {
      try {
        await OfferService.destroy(id);
        await this.fetchOffers();
      } catch (error: any) {
        this.errors = [error?.response?.data?.error || 'Wystąpił nieznany błąd.'];
      }
    },

    /*
Koniec nowej logiki
*/

    async fetchOffers(this: OfferState & ReturnType<typeof useOfferStore>) {
      try {
        const { offers, statuses } = await OfferService.fetchAll();

        this.offers = offers;
        this.filteredOffers = offers;
        this.statuses = statuses;
        console.log(this.offers);
      } catch (error: any) {}
    },
    async saveOffer(this: OfferState & ReturnType<typeof useOfferStore>) {
      this.formatDescriptions();

      try {
        this.errors = {};
        const response = await OfferService.save(this.offer);
        this.editOffer(response.offer);
        this.fetchOffers();
      } catch (error: any) {
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

        const pdfBlob = await OfferService.generatePdf(this.offer.id ?? 0, this.offer.pdfInfo);
        const response = await axiosClient.get(`/api/offers/${this.offer.id}`);
        console.log(response.data);
        this.offer.offerNumber = response.data.offer.offerNumber;

        const url = window.URL.createObjectURL(new Blob([pdfBlob]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute(
          'download',
          `Reg_${this.offer.customer?.code}_${this.offer.offerNumber}.pdf`
        );
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      } catch (error) {
        console.error('Failed to generate PDF:', error);
        alert('Wystąpił błąd przy generowaniu PDF.');
      }
    },
    applyGlobalDiscount(discount: number) {
      if (discount === 0 || discount === null) return;
      this.offer.globalDiscount = discount;
      this.offer.offerDetails.forEach((detail) => {
        detail.discount = discount;
      });
    },
    addToolRow() {
      this.offer.offerDetails.push({
        id: null,
        offerId: null,
        symbol: '',
        radius: 0,
        regrindingOption: 'face_regrinding',
        toolType: { id: 1, toolTypeName: 'Frez Walcowy' },
        toolNetPrice: 0,
        quantity: 1,
        discount: 0,
        toolGeometry: null,
        coatingPrice: {
          id: 0,
          diameter: 0,
          price: 0,
          coatingType: {
            id: 0,
            mastermetCode: '',
          },
        },
        coatingNetPrice: 0,
        description: '',
        tool: null,
        diameter: null,
        flutesNumber: null,
        isToolPriceManual: false,
      });
    },
    removeToolRow(index: number) {
      this.offer.offerDetails.splice(index, 1);
    },
    editOffer(offer: Offer) {
      this.isEditing = true;
      this.isInitialEditPhase = true;
      this.offer = offer;
      this.offer.globalDiscount = 0;
      this.offer.offerDetails.forEach((detail) => {
        detail.flutesNumber = detail.toolGeometry?.flutesNumber ?? null;
        detail.diameter = detail.toolGeometry?.diameter ?? null;
      });
    },
    finishEdit() {
      this.isEditing = false;
      this.isInitialEditPhase = false;
    },
    resetOffer() {
      this.finishEdit();
      this.offer = {
        id: null,
        globalDiscount: 0,
        offerNumber: '',
        customer: null,
        status: { id: 1, name: 'Robocza' },
        totalPrice: 0,
        createdBy: null,
        changedBy: null,
        createdAt: '',
        updatedAt: '',
        pdfInfo: {
          deliveryTime: '12–15 dni roboczych',
          offerValidity: '7 dni',
          paymentTerms: 'przelew 14 dni',
          displayDiscount: false,
        },
        offerDetails: [
          {
            id: null,
            offerId: null,
            symbol: '',
            radius: 0,
            regrindingOption: 'face_regrinding',
            toolType: { id: 1, toolTypeName: 'Frez Walcowy' },
            toolNetPrice: 0,
            quantity: 1,
            discount: 0,
            toolGeometry: null,
            diameter: null,
            flutesNumber: null,
            isToolPriceManual: false,
            coatingPrice: {
              id: 0,
              diameter: 0,
              price: 0,
              coatingType: {
                id: 0,
                mastermetCode: '',
              },
            },
            coatingNetPrice: 0,
            description: '',
            tool: null,
          },
        ],
      };
    },

    isRadiusEndMill(detail: OfferDetail) {
      return detail.toolType.toolTypeName === 'Frez Promieniowy';
    },

    calculateOfferTotalNetPrice() {
      let totalNetPrice = 0;

      this.offer.offerDetails.forEach((detail) => {
        const detailPrice = this.getTotalNetDetailPrice(detail);
        totalNetPrice += parseFloat(detailPrice);
      });

      this.offer.totalPrice = totalNetPrice;
    },
    setFilter(column: keyof OfferFilters, value: string) {
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
    // applyFileDataToDetail(index: number) {
    //   const toolStore = useToolsStore();
    //   const detail = this.offer.offerDetails[index];

    //   if (detail.tool?.id !== null) {
    //     const file = toolStore.files.find((f) => f.id === detail.fileId);

    //     if (file) {
    //       detail.diameter = file.diameter || 0;
    //       detail.toolNetPrice = file.price || 0;
    //       detail.flutesNumber = file.flutesNumber ?? null;
    //       detail.fileId = file.id ?? null;
    //       detail.symbol = file.name ?? '';
    //     } else {
    //       console.warn(`Nie znaleziono pliku o ID: ${detail.fileId}`);
    //     }

    //     this.updateCoatingNetPrice(index);
    //     this.calculateOfferTotalNetPrice();
    //   }
    // },
    formatDescriptions() {
      this.offer.offerDetails.forEach((detail) => {
        if (
          ['Frez Walcowy', 'Frez Promieniowy', 'Frez Kulowy', 'Fazownik', 'Wiertlo Krete'].includes(
            detail.toolType.toolTypeName
          )
        ) {
          let prefix = '';

          if (detail.regrindingOption === 'face_regrinding') {
            prefix = 'ostrzenie czoła';
          } else if (detail.regrindingOption === 'full_regrinding') {
            prefix = 'ostrzenie kpl.';
          }

          if (detail.coatingPrice?.coatingType?.mastermetCode) {
            prefix += ` + powłoka ${detail.coatingPrice.coatingType.mastermetCode}`;
          }

          if (prefix) {
            if (!detail.description || detail.description.trim() === '') {
              detail.description = prefix;
            } else if (!detail.description.startsWith(prefix)) {
              detail.description = `${prefix} ${detail.description}`;
            }
          }
        }
      });
    },
  },
  getters: {
    getTotalNetDetailPrice: (state) => (detail: OfferDetail) => {
      return (
        (detail.toolNetPrice + detail.coatingNetPrice) *
        detail.quantity *
        ((100 - (detail.discount ?? 0)) / 100)
      ).toFixed(2);
    },
    getRadius: (state) => (detail: OfferDetail) => {
      switch (detail.toolType.toolTypeName) {
        case 'Frez Promieniowy':
          return detail.radius ?? 0;
        case 'Frez Kulowy':
          return (detail?.diameter ?? 0) / 2;
        default:
          return 0;
      }
    },
  },
});

export default useOfferStore;
