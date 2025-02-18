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
        rolesToAssign: [],
        filteredUsers: [],
        filters: {
            name: '',
            email: '',
            roles: '',
            marker: '',
          },
        errors: {},
    }),
    actions: {
        async fetchUser() {
            try {
                const response = await axiosClient.get('/api/user');
                this.user = response.data;
            } catch (error) {
                console.error("Błąd pobierania użytkownika", error);
                throw error;
            }
        },
        async fetchDictionaries() {
            try {
                const response = await axiosClient.get('/api/dictionaries');
                this.navigation = response.data.navigation;
                this.rolesToAssign = response.data.rolesToAssign;
                console.log(this.rolesToAssign);
            } catch (error) {
                console.log(error);
            }
        },
        async fetchUsers() {
            try {
                const response = await axiosClient.get('api/pracownicy');
                this.users = response.data;
                this.filteredUsers = this.users;
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
        },
        isCreator() {
            if (!this.user) return false;
            return this.user.roles && (this.user.roles.includes('admin') || this.user.roles.includes('regeneration'));
        },
        setFilter(column, value) {

            this.filters[column] = value;
            this.filterUsers();
          },
          filterUsers() {
            if (!Array.isArray(this.users)) return;
            
            this.filteredUsers = this.users.filter(user =>
                Object.entries(this.filters).every(([key, value]) => 
                    !value || (user[key] || '').toLowerCase().includes(value.toLowerCase())
                )
            );
        }        
    },
});

export default useUserStore;