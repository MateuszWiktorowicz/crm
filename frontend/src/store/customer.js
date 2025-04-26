import { defineStore } from 'pinia';
import axiosClient from '../axios';
import useUserStore from './user';

const useCustomerStore = defineStore('customer', {
  state: () => ({
    customers: [],
    customer: {
      code: '',
      name: '',
      nip: '',
      zip_code: '',
      city: '',
      address: '',
      saler_marker: '',
      description: '',
    },
    selectedCustomersFile: null,
    isModalOpen: false,
    filteredCustomers: [],
    filters: {
      code: '',
      name: '',
      nip: '',
      city: '',
      address: '',
      saler_marker: '',
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
      } catch {
        console.error('Błąd pobierania klientów', error);
      }
    },
    async saveCustomer() {
      try {
        if (this.customer.id) {
          await axiosClient.put(`/api/klienci/${this.customer.id}`, this.customer);
        } else {
          await axiosClient.post('/api/klienci', this.customer);
        }
        this.isModalOpen = false;
        await this.fetchCustomers();
        this.errors = {};
      } catch (error) {
        this.errors = error.response?.data?.errors ?? {};
      }
    },
    async deleteCustomer(customerId) {
      try {
        if (confirm('Czy na pewno chcesz usunąć tego klienta?')) {
          await axiosClient.delete(`/api/klienci/${customerId}`);
          await this.fetchCustomers();
        }
        this.errors = {};
      } catch (error) {
        this.errors = error.response?.data?.errors ?? {};
      }
    },
    async importCustomers(fileData) {
      await axiosClient.post('/api/klienci/import', fileData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      this.fetchCustomers();
    },
    resetCustomer() {
      const userStore = useUserStore();
    
      this.customer = {
        code: '',
        name: '',
        nip: '',
        zip_code: '',
        city: '',
        address: '',
        saler_marker: '',
        description: '',
      };
    
      if (userStore.user?.roles.includes('saler')) {
        this.customer.saler_marker = userStore.user.marker;
      }
    },
    
    openModal(customer = null) {
      customer ? this.customer = customer : this.resetCustomer();
      this.errors = {};
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
      this.errors = {};
    },
    setSelectedFile(file) {
      this.selectedFile = file;
    },
    clearSelectedFile() {
      this.selectedFile = null;
    },
    setFilter(column, value) {
      this.filters[column] = value;
      this.filterCustomers();
    },
    filterCustomers() {
      if (!Array.isArray(this.customers)) return;

      this.filteredCustomers = this.customers.filter((customer) =>
        Object.entries(this.filters).every(
          ([key, value]) =>
            !value || (customer[key] || '').toLowerCase().includes(value.toLowerCase())
        )
      );
    },
  },
});

export default useCustomerStore;
