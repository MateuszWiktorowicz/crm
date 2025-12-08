// services/offerService.ts
import axiosClient from '@/axios';
import { Offer, OfferResponse, OfferFilters, OfferDetail, PdfInfo, Status, PaginatedOfferResponse } from '@/types/types';
type SaveOfferResponse = { offer: Offer };

export const OfferService = {
  async fetchOffers(
    page: number = 1,
    filters?: Partial<OfferFilters>
  ): Promise<PaginatedOfferResponse> {
    const params: any = {
      page,
      per_page: 10,
    };

    if (filters) {
      if (filters.offerNumber) {
        params.offer_number = filters.offerNumber;
      }
      if (filters.customerName) {
        params.customer_name = filters.customerName;
      }
      if (filters.employeeName) {
        params.employee_name = filters.employeeName;
      }
      if (filters.statusName) {
        params.status_name = filters.statusName;
      }
      if (filters.createdAt) {
        params.created_at = filters.createdAt;
      }
    }

    const response = await axiosClient.get('/api/offers', { params });

    return {
      data: response.data.data,
      meta: response.data.meta,
      statuses: response.data.statuses,
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

  async getEmployeeMarkers(): Promise<string[]> {
    const response = await axiosClient.get('/api/offers/dashboard/markers');
    return response.data.markers;
  },

  async getPopularTools(params?: {
    customer_id?: number;
    employee_marker?: string;
    period?: 'week' | 'month' | 'year' | 'custom';
    start_date?: string;
    end_date?: string;
  }): Promise<{
    popularFiles: Array<{
      id: number;
      code: string;
      name: string;
      totalQuantity: number;
      usageCount: number;
    }>;
    popularCombinations: Array<{
      toolType: string;
      flutes: number | null;
      diameter: number | null;
      totalQuantity: number;
      usageCount: number;
    }>;
    popularCoatings: Array<{
      code: string;
      name: string;
      totalQuantity: number;
      usageCount: number;
    }>;
  }> {
    const response = await axiosClient.get('/api/offers/dashboard/popular-tools', { params });
    return response.data;
  },
};
