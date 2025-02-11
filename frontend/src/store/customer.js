import { defineStore } from 'pinia';
import axiosClient from "../axios";

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
        errors: {}
    }),
    actions: {
        async fetchCustomers() {
            try {
                const response = await axiosClient.get('/api/klienci');
                this.customers = response.data
            } catch {
                console.error("Błąd pobierania klientów", error);
            }
        },
        async saveCustomer() {
            try {
                if (this.customer.id) {
                    await axiosClient.put(`/api/klienci/${this.customer.id}`, this.customer);
                } else {
                    await axiosClient.post("/api/klienci", this.customer);
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
                if (confirm("Czy na pewno chcesz usunąć tego klienta?")) {
                    await axiosClient.delete(`/api/klienci/${customerId}`);
                    await this.fetchCustomers();
                }
            } catch (error) {
                console.error("Błąd podczas usuwania klienta:", error);
            }
        },
        async importCustomers(fileData) {
            await axiosClient.post('/api/klienci/import', fileData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            this.fetchCustomers();
        },
        openModal(customer = null) {
            this.customer = customer || { code: "", name: "", nip: "", zip_code: "", city: "", address: "", saler_marker: "", description: "" };
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        },
        setSelectedFile(file) {
            this.selectedFile = file;
        },
        clearSelectedFile() {
            this.selectedFile = null;
        },
    }
});

export default useCustomerStore;