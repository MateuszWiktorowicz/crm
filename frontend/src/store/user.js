import { defineStore } from "pinia";
import axiosClient from "../axios.js";

const useUserStore = defineStore('user', {
    state: () => ({
        users: [],
        user: {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            marker: '',
            roles: [],
        },
        isModalOpen: false,
        navigation: [],
        errors: {},
    }),
    actions: {
        async fetchUser() {
            try {
                const response = await axiosClient.get('/api/user');
                this.user = response.data;
            } catch (error) {
                console.error("Błąd pobierania użytkownika", error);
            }
        },
        async fetchDictionaries() {
            try {
                const response = await axiosClient.get('/api/dictionaries');
                this.navigation = response.data.navigation;
            } catch (error) {
                console.log(error);
            }
        },
        async fetchUsers() {
            try {
                const response = await axiosClient.get('api/pracownicy');
                this.users = response.data;
            } catch (error) {
                console.log(error);
            }
        },   
        async saveUser() {
            try {
                if (this.user.id) {
                    await axiosClient.put(`/api/pracownicy/${this.user.id}`, this.user);
                } else {
                    await axiosClient.post("/api/pracownicy", this.user);
                }
                this.isModalOpen = false;
                await this.fetchUsers();
                this.errors = {};
            } catch (error) {
                this.errors = error.response?.data?.errors ?? {};
            }
        },
        async deleteUser(userId) {
            try {
                if (confirm("Czy na pewno chcesz usunąć tego użytkownika?")) {
                    await axiosClient.delete(`/api/pracownicy/${userId}`);
                    await this.fetchUsers();
                }
            } catch (error) {
                console.error("Błąd podczas usuwania użytkownika:", error);
            }
        },
        openModal(user = null) {
            this.user = user || { name: "", email: "", password: "", password_confirmation: "", marker: "", roles: [] };
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
        }
    },
});

export default useUserStore;