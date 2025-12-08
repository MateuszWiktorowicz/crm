// types/offer.ts

export interface Employee {
  id: number;
  name: string;
  email: string;
  roles: string[];
  marker: string;
}

export interface CoatingType {
  id: number;
  mastermetCode: string;
  mastermetName?: string;
  purpose?: string;
  description?: string;
}

export interface CoatingPrice {
  id: number;
  diameter: number;
  price: number;
  coatingType: CoatingType;
}

export interface Tool {
  id: number;
  code: string;
  diameter: number;
  name: string;
  price: number;
}

export interface ToolGeometry {
  id: number;
  toolType: ToolType;
  diameter: number;
  flutesNumber: number;
  faceGrindingPrice: number;
  peripheryGrindingPrice: number;
  regrindingOptions: { key: string; label: string }[];
}

export interface ToolType {
  id: number;
  toolTypeName: string;
}

export interface Status {
  id: number;
  name: string;
}
export interface PdfInfo {
  deliveryTime: string;
  offerValidity: string;
  paymentTerms: string;
  displayDiscount: boolean;
}

export interface Customer {
  id: number | null;
  code: string;
  name: string;
  nip: string;
  salerMarker: string;
  zipCode: string;
  address: string;
  city: string;
  description: string;
}

export interface CustomerFilters {
  code: string;
  name: string;
  nip: string;
  city: string;
  address: string;
  salerMarker: string;
  description: string;
}

export interface OfferDetail {
  id: number | null;
  offerId: number | null;
  tool?: Tool | null;
  toolGeometry?: ToolGeometry | null;
  toolType: ToolType;
  toolNetPrice: number;
  symbol: string;
  regrindingOption: 'face_regrinding' | 'full_regrinding' | null;
  radius: number;
  quantity: number;
  discount: number;
  description?: string;
  coatingPrice: CoatingPrice;
  coatingNetPrice: number;
  flutesNumber: number | null;
  diameter: number | null;
  isToolPriceManual?: boolean;
  isCoatingPriceManual?: boolean;
}

export interface Offer {
  offerNumber: string;
  globalDiscount: number;
  id: number | null;
  customer: Customer | null;
  status: Status;
  totalPrice: number;
  changedBy: Employee | null;
  createdAt?: string;
  createdBy: Employee | null;
  updatedAt?: string;
  pdfInfo: PdfInfo;
  offerDetails: OfferDetail[];
}

export interface OfferFilters {
  offerNumber: string;
  customerName: string;
  employeeName: string;
  statusName: string;
  createdAt: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
}

export interface OfferResponse {
  offers: Offer[];
  statuses: Status[];
}

export interface PaginatedOfferResponse {
  data: Offer[];
  meta: {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
  statuses: Status[];
}

export interface PaginatedCustomerResponse {
  data: Customer[];
  meta: {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
}
