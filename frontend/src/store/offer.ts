import { defineStore } from 'pinia';
import useSettingsStore from './settings';
import { OfferService } from '@/services/OfferService';
import { Offer, OfferDetail, OfferFilters, Status } from '@/types/types';
import axiosClient from '@/axios';
import dayjs from 'dayjs';

interface OfferState {
  isLoading: boolean;
  isSaving: boolean;
  isPdfGenerating: boolean;
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
    isSaving: false,
    isPdfGenerating: false,
    isLoading: false,
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

    async destroyOffer(id: number) {
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

    async fetchOffers() {
      try {
        const { offers, statuses } = await OfferService.fetchAll();

        this.offers = offers;
        this.filteredOffers = offers;
        this.statuses = statuses;
        console.log(this.offers);
      } catch (error: any) {}
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
        this.fetchOffers();
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

      this.filteredOffers = this.offers.filter((offer) => {
        // Filtr po numerze oferty (prostym polu)
        if (this.filters.offerNumber) {
          const offerNumber = (offer.offerNumber ?? '').toString().toLowerCase();
          const filter = this.filters.offerNumber.toLowerCase();
          if (!offerNumber.includes(filter)) {
            return false;
          }
        }

        // Filtr po nazwie klienta (zagnieżdżone pole customer.name)
        if (this.filters.customerName) {
          if (
            !offer.customer ||
            !offer.customer.name.toLowerCase().includes(this.filters.customerName.toLowerCase())
          ) {
            return false;
          }
        }

        // Filtr po nazwie pracownika (createdBy.name)
        if (this.filters.employeeName) {
          if (
            !offer.createdBy ||
            !offer.createdBy.name.toLowerCase().includes(this.filters.employeeName.toLowerCase())
          ) {
            return false;
          }
        }

        // Filtr po statusie (status.name)
        if (this.filters.statusName) {
          if (
            !offer.status ||
            !offer.status.name.toLowerCase().includes(this.filters.statusName.toLowerCase())
          ) {
            return false;
          }
        }

        if (this.filters.createdAt) {
          const createdAtFull = offer.createdAt ?? '';

          if (!createdAtFull) return false;

          const parsedDate = dayjs(createdAtFull);
          if (!parsedDate.isValid()) return false;

          // Format daty taki sam jak w tabeli
          const formattedDate = parsedDate.format('DD/MM/YYYY'); // np. "11/06/2025"

          // Normalizujemy oba ciągi: usuwamy spacje, zamieniamy na lowercase
          const normalizedFilter = this.filters.createdAt.trim().toLowerCase();
          const normalizedDate = formattedDate.toLowerCase();
          if (!normalizedDate.includes(normalizedFilter)) {
            return false;
          }
        }

        return true;
      });
    },

    formatDescriptions() {
      this.offer.offerDetails.forEach((detail) => {
        if (
          ['Frez Walcowy', 'Frez Promieniowy', 'Frez Kulowy', 'Fazownik', 'Wiertlo Krete'].includes(
            detail.toolType.toolTypeName
          )
        ) {
          let prefix = '';

          if (detail.description && detail.description.trim() !== '') return;

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
