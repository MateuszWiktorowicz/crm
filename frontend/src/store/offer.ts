import { defineStore } from 'pinia';
import useSettingsStore from './settings';
import { OfferService } from '@/services/OfferService';
import { Offer, OfferDetail, OfferFilters, Status } from '@/types/types';
import axiosClient from '@/axios';

interface PaginationMeta {
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;
}

interface OfferState {
  isLoading: boolean;
  isSaving: boolean;
  isPdfGenerating: boolean;
  isEditing: boolean;
  isInitialEditPhase: boolean;
  offers: Offer[];
  offer: Offer;
  pagination: PaginationMeta | null;
  currentFilters: Partial<OfferFilters>;
  statuses: Status[];
  errors: Record<string, any>;
}
export const useOfferStore = defineStore('offer', {
  state: (): OfferState => ({
    isSaving: false,
    isPdfGenerating: false,
    isLoading: false,
    isEditing: false,
    isInitialEditPhase: false,
    offers: [],
    pagination: null,
    currentFilters: {},
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
    statuses: [],
    errors: {},
  }),
  actions: {
    /*
Początek nowej logiki
*/

    async destroyOffer(id: number) {
      try {
        await OfferService.destroy(id);
        await this.fetchOffers(this.pagination?.current_page ?? 1, this.currentFilters);
        this.errors = {};
      } catch (error: any) {
        this.errors = { general: [error?.response?.data?.error || 'Wystąpił nieznany błąd.'] };
      }
    },

    /*
Koniec nowej logiki
*/

    async fetchOffers(page: number = 1, filters?: Partial<OfferFilters>) {
      this.isLoading = true;
      try {
        if (filters) {
          this.currentFilters = filters;
        }
        const response = await OfferService.fetchOffers(page, this.currentFilters);

        this.offers = response.data;
        this.pagination = response.meta;
        this.statuses = response.statuses;
      } catch (error: any) {
        this.errors = error?.response?.data?.errors || {};
        console.error('Błąd pobierania ofert:', error);
      } finally {
        this.isLoading = false;
      }
    },
    async saveOffer() {
      this.isLoading = true;
      this.isSaving = true;

      this.formatDescriptions();

      try {
        this.errors = {};
        const delay = new Promise((resolve) => setTimeout(resolve, 1000));

        const [response] = await Promise.all([OfferService.save(this.offer), delay]);

        this.editOffer(response.offer);
        await this.fetchOffers(this.pagination?.current_page ?? 1, this.currentFilters);
      } catch (error: any) {
        if (error.response && error?.response?.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          console.error('Nieznany błąd:', error);
        }
      } finally {
        this.isLoading = false;
        this.isSaving = false;
      }
    },

    async generatePdf() {
      this.isLoading = true;
      this.isPdfGenerating = true;
      try {
        const settingsStore = useSettingsStore();
        await settingsStore.fetchSettings();

        const pdfBlob = await OfferService.generatePdf(this.offer.id ?? 0, this.offer.pdfInfo);
        const response = await axiosClient.get(`/api/offers/${this.offer.id}`);

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
      } finally {
        this.isLoading = false;
        this.isPdfGenerating = false;
      }
    },
    applyGlobalDiscount(discount: number) {
      if (discount === 0 || discount === null) return;
      this.offer.globalDiscount = discount;
      this.offer.offerDetails.forEach((detail) => {
        detail.discount = discount;
      });
    },

    cloneOffer(originalOffer: Offer) {
      const cloned = JSON.parse(JSON.stringify(originalOffer));

      cloned.id = null;
      cloned.offerNumber = '';
      cloned.status = { id: 1, name: 'Robocza' };
      cloned.createdAt = '';
      cloned.updatedAt = '';
      cloned.createdBy = null;
      cloned.changedBy = null;

      cloned.offerDetails = cloned.offerDetails.map((detail: any) => ({
        ...detail,
        id: null,
        offerId: null,
      }));

      this.editOffer(cloned);
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
    cloneToolRow(index: number) {
      const originalDetail = this.offer.offerDetails[index];
      const clonedDetail = JSON.parse(JSON.stringify(originalDetail));
      
      // Resetuj ID-y, żeby była to nowa pozycja
      clonedDetail.id = null;
      clonedDetail.offerId = null;
      
      // Wstaw sklonowaną pozycję zaraz po oryginalnej
      this.offer.offerDetails.splice(index + 1, 0, clonedDetail);
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

    formatDescriptions() {
      this.offer.offerDetails.forEach((detail) => {
        if (
          ['Frez Walcowy', 'Frez Promieniowy', 'Frez Kulowy', 'Fazownik', 'Wiertlo Krete', 'Frez Zgrubny'].includes(
            detail.toolType.toolTypeName
          )
        ) {
          let prefix = '';

          if (detail.description && detail.description.trim() !== '') return;

          if (detail.regrindingOption === 'face_regrinding') {
            prefix = 'ostrzenie czoła';
          } else if (detail.regrindingOption === 'full_regrinding') {
            prefix = 'ostrzenie kpl.';
          } else if (detail.regrindingOption === 'periphery_regrinding') {
            prefix = 'ostrzenie pod zębem'
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
