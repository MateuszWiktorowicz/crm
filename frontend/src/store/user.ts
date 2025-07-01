import { defineStore } from 'pinia';
import axiosClient from '../axios.js';
import { isAxiosError } from 'axios';

interface UserFilters {
  name: string;
  email: string;
  roles: string;
  marker: string;
}

interface User {
  id: number;
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  marker: string;
  roles: string[];
}

interface UserState {
  users: User[];
  user: User;
  loggedInUser: User | null;
  filteredUsers: User[];
  isModalOpen: boolean;
  navigation: any[];
  filters: UserFilters;
  errors: Record<string, any>;
  rolesToAssign: any[];
}

export const useUserStore = defineStore('user', {
  state: (): UserState => ({
    users: [],
    loggedInUser: null,
    user: {
      id: 0,
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
        this.loggedInUser = response.data;
      } catch (error) {
        console.error('Błąd pobierania użytkownika', error);
        throw error;
      }
    },
    async fetchDictionaries() {
      try {
        const response = await axiosClient.get('/api/dictionaries');
        this.navigation = response.data.navigation;
        this.rolesToAssign = response.data.rolesToAssign;
      } catch (error) {}
    },
    async fetchUsers() {
      try {
        const response = await axiosClient.get('api/pracownicy');
        this.users = response.data;
        this.filteredUsers = this.users;
      } catch (error) {}
    },
    async saveUser() {
      try {
        if (this.user.id) {
          await axiosClient.put(`/api/pracownicy/${this.user.id}`, this.user);
        } else {
          await axiosClient.post('/api/pracownicy', this.user);
        }
        this.isModalOpen = false;
        await this.fetchUsers();
        this.errors = {};
      } catch (error: unknown) {
        if (isAxiosError(error)) {
          this.errors = error.response?.data?.errors ?? {};
        } else {
          console.error('Nieoczekiwany błąd:', error);
          this.errors = { general: 'Wystąpił nieoczekiwany błąd.' };
        }
      }
    },
    async deleteUser(userId: number) {
      try {
        if (confirm('Czy na pewno chcesz usunąć tego użytkownika?')) {
          await axiosClient.delete(`/api/pracownicy/${userId}`);
          await this.fetchUsers();
        }
      } catch (error) {
        console.error('Błąd podczas usuwania użytkownika:', error);
      }
    },
    openModal(user: User | null = null) {
      this.user = user || {
        id: 0,
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        marker: '',
        roles: [],
      };
      this.isModalOpen = true;
    },
    closeModal() {
      this.isModalOpen = false;
    },
    isCreator() {
      if (!this.loggedInUser) return false;
      return (
        this.loggedInUser.roles &&
        (this.loggedInUser.roles.includes('admin') ||
          this.loggedInUser.roles.includes('regeneration'))
      );
    },
    setFilter(column: keyof UserFilters, value: string) {
      this.filters[column] = value;
      this.filterUsers();
    },
    filterUsers() {
      if (!Array.isArray(this.users)) return;

      this.filteredUsers = this.users.filter((user) =>
        (Object.entries(this.filters) as [keyof UserFilters, string][]).every(([key, value]) => {
          if (!value) return true;

          const userValue = user[key];

          if (typeof userValue === 'string') {
            return userValue.toLowerCase().includes(value.toLowerCase());
          }

          if (Array.isArray(userValue)) {
            return userValue.some((role) => role.toLowerCase().includes(value.toLowerCase()));
          }

          return false;
        })
      );
    },
  },
});
