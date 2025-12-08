import { defineStore } from 'pinia';
import axiosClient from '../axios';
import { Customer, PaginatedCustomerResponse, CustomerFilters } from '@/types/types';

interface PaginationMeta {
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;
}

interface CustomerStoreState {
  customers: Customer[];
  customer: Customer;
  pagination: PaginationMeta | null;
  currentFilters: Partial<CustomerFilters>;
  isLoading: boolean;
  errors: Record<string, string[]>;
}

const useCustomerStore = defineStore('customer', {
  state: (): CustomerStoreState => ({
    customers: [],
    customer: {
      id: null,
      code: '',
      name: '',
      nip: '',
      zipCode: '',
      city: '',
      address: '',
      salerMarker: '',
      description: '',
    },
    pagination: null,
    currentFilters: {},
    isLoading: false,
    errors: {},
  }),
  actions: {
    async fetchCustomers(page: number = 1, filters?: Partial<CustomerFilters>) {
      this.isLoading = true;
      try {
        if (filters) {
          this.currentFilters = filters;
        }

        const params: any = {
          page,
          per_page: 10,
        };

        if (this.currentFilters.code) {
          params.code = this.currentFilters.code;
        }
        if (this.currentFilters.name) {
          params.name = this.currentFilters.name;
        }
        if (this.currentFilters.nip) {
          params.nip = this.currentFilters.nip;
        }
        if (this.currentFilters.city) {
          params.city = this.currentFilters.city;
        }
        if (this.currentFilters.address) {
          params.address = this.currentFilters.address;
        }
        if (this.currentFilters.salerMarker) {
          params.saler_marker = this.currentFilters.salerMarker;
        }
        if (this.currentFilters.description) {
          params.description = this.currentFilters.description;
        }

        const response = await axiosClient.get('/api/klienci', { params });
        const data: PaginatedCustomerResponse = response.data;

        this.customers = data.data;
        this.pagination = data.meta;
      } catch (error: any) {
        console.error('Błąd pobierania klientów', error);
        this.errors = error?.response?.data?.errors || {};
      } finally {
        this.isLoading = false;
      }
    },
    async saveCustomer(customer: Customer) {
      try {
        if (customer.id) {
          await axiosClient.put(`/api/klienci/${this.customer.id}`, customer);
        } else {
          await axiosClient.post('/api/klienci', customer);
        }
        await this.fetchCustomers(this.pagination?.current_page ?? 1, this.currentFilters);
        this.errors = {};
      } catch (error: any) {
        this.errors = error.response?.data?.errors ?? {};
      }
    },
    async deleteCustomer(customerId: number) {
      try {
        await axiosClient.delete(`/api/klienci/${customerId}`);
        await this.fetchCustomers(this.pagination?.current_page ?? 1, this.currentFilters);
        this.errors = {};
      } catch (error: any) {
        this.errors = error.response?.data?.errors ?? {};
      }
    },
    editCustomer(customer: Customer) {
      this.customer = customer;
    },
    async importCustomers(fileData: FormData) {
      await axiosClient.post('/api/klienci/import', fileData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      await this.fetchCustomers(this.pagination?.current_page ?? 1, this.currentFilters);
    },
    resetCustomer() {
      this.customer = {
        id: null,
        code: '',
        name: '',
        nip: '',
        zipCode: '',
        city: '',
        address: '',
        salerMarker: '',
        description: '',
      };
    },
  },
  getters: {},
});

export default useCustomerStore;
