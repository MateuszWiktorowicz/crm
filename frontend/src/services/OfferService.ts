// services/offerService.ts
import axiosClient from '@/axios';
import { Offer, OfferResponse, OfferFilters, OfferDetail, PdfInfo, Status } from '@/types/types';
type SaveOfferResponse = { offer: Offer };

export const OfferService = {
  async fetchAll(): Promise<OfferResponse> {
    const response = await axiosClient.get('/api/offers');
    const offers: Offer[] = response.data.offers;

    return {
      offers,
      statuses: response.data.statuses as Status[],
    };
  },

  async save(offer: Offer): Promise<SaveOfferResponse> {
    const payload = {
      customer_id: offer.customer?.id ?? null,
      status_id: offer.status?.id ?? 1,
      total_net_price: Number(offer.totalPrice),
      global_discount: offer.globalDiscount,
      offer_number: offer.offerNumber || '',
      offer_details: offer.offerDetails.map((detail) => ({
        tool_geometry_id: detail.toolGeometry?.id ?? null,
        tool_type_id: detail.toolType?.id ?? null,
        quantity: detail.quantity,
        discount: detail.discount,
        tool_net_price: detail.toolNetPrice,
        coating_price_id:
          !detail.coatingPrice || detail.coatingPrice.id === 0 ? null : detail.coatingPrice.id,
        coating_net_price: detail.coatingNetPrice,
        radius: detail.radius,
        regrinding_option: detail.regrindingOption,
        description: detail.description,
        symbol: detail.symbol,
        file_id: detail.tool?.id ?? null,
      })),
      pdf_info: {
        deliveryTime: offer.pdfInfo?.deliveryTime ?? '',
        offerValidity: offer.pdfInfo?.offerValidity ?? '',
        paymentTerms: offer.pdfInfo?.paymentTerms ?? '',
        displayDiscount: offer.pdfInfo?.displayDiscount ?? false,
      },
    };
    let response;
    if (offer.id) {
      response = await axiosClient.put(`/api/offers/${offer.id}`, payload);
    } else {
      response = await axiosClient.post('/api/offers', payload);
    }
    return response.data;
  },

  async destroy(id: number): Promise<void> {
    await axiosClient.delete(`/api/offers/${id}`);
  },

  async generatePdf(id: number, pdfInfo: PdfInfo): Promise<Blob> {
    const response = await axiosClient.post(`/api/offers/${id}/generate-pdf`, pdfInfo, {
      responseType: 'blob',
    });

    return response.data;
  },

  async getDashboardStats(params?: {
    customer_id?: number;
    employee_marker?: string;
    period?: 'week' | 'month' | 'year' | 'custom';
    start_date?: string;
    end_date?: string;
  }): Promise<any> {
    const response = await axiosClient.get('/api/offers/dashboard/stats', { params });
    return response.data;
  },
};
