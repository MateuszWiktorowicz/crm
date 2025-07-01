import { defineStore } from 'pinia';
import axiosClient from '../axios';
import { Customer } from '@/types/types';

interface CustomerFilters {
  code: string;
  name: string;
  nip: string;
  city: string;
  address: string;
  salerMarker: string;
  description: string;
}

interface CustomerStoreState {
  customers: Customer[];
  customer: Customer;
  filteredCustomers: Customer[];
  filters: CustomerFilters;
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
    filteredCustomers: [],
    filters: {
      code: '',
      name: '',
      nip: '',
      city: '',
      address: '',
      salerMarker: '',
      description: '',
    },
    errors: {},
  }),
  actions: {
    async fetchCustomers() {
      try {
        const response = await axiosClient.get('/api/klienci');
        this.customers = response.data;
        this.filteredCustomers = this.customers;
      } catch (error: any) {
        console.error('Błąd pobierania klientów', error);
      }
    },
    async saveCustomer(customer: Customer) {
      try {
        if (customer.id) {
          await axiosClient.put(`/api/klienci/${this.customer.id}`, customer);
        } else {
          await axiosClient.post('/api/klienci', customer);
        }
        await this.fetchCustomers();
        this.errors = {};
      } catch (error: any) {
        this.errors = error.response?.data?.errors ?? {};
      }
    },
    async deleteCustomer(customerId: number) {
      try {
        await axiosClient.delete(`/api/klienci/${customerId}`);
        await this.fetchCustomers();

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
      this.fetchCustomers();
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
    setFilter(column: keyof CustomerFilters, value: string) {
      this.filters[column] = value;
      this.filterCustomers();
    },
    filterCustomers() {
      this.filteredCustomers = this.customers.filter((customer) => {
        return (Object.keys(this.filters) as (keyof CustomerFilters)[]).every((key) => {
          const filterValue = this.filters[key]?.toLowerCase().trim();
          const customerValue = customer[key]?.toLowerCase?.() || '';

          return !filterValue || customerValue.includes(filterValue);
        });
      });
    },
  },
  getters: {},
});

export default useCustomerStore;
