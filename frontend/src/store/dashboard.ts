import { defineStore } from 'pinia';
import { OfferService } from '@/services/OfferService';

interface DashboardStats {
  stats: {
    inPreparation: number;
    sent: number;
    accepted: number;
    rejected: number;
  };
  wonValue: number;
  lostValue: number;
  totalValue: number;
  periodValue: number;
  monthlyValue: number;
  quarterlyValue: number;
  yearlyValue: number;
  successRate: number;
  period: string;
  startDate?: string;
  endDate?: string;
}

interface DashboardFilters {
  customerId: number | null;
  employeeMarker: string | null;
  period: 'week' | 'month' | 'year' | 'custom' | 'all';
  startDate: string;
  endDate: string;
}

interface PopularFile {
  id: number;
  code: string;
  name: string;
  totalQuantity: number;
  usageCount: number;
}

interface PopularCombination {
  toolType: string;
  flutes: number | null;
  diameter: number | null;
  totalQuantity: number;
  usageCount: number;
}

interface PopularCoating {
  code: string;
  name: string;
  totalQuantity: number;
  usageCount: number;
}

interface DashboardState {
  isLoading: boolean;
  stats: DashboardStats | null;
  popularFiles: PopularFile[];
  popularCombinations: PopularCombination[];
  popularCoatings: PopularCoating[];
  filters: DashboardFilters;
  errors: string[];
}

export const useDashboardStore = defineStore('dashboard', {
  state: (): DashboardState => ({
    isLoading: false,
    stats: null,
    popularFiles: [],
    popularCombinations: [],
    popularCoatings: [],
      filters: {
        customerId: null,
        employeeMarker: null,
        period: 'all',
        startDate: '',
        endDate: '',
      },
    errors: [],
  }),

  actions: {
    async fetchStats() {
      this.isLoading = true;
      this.errors = [];

      try {
        const params: any = {
          period: this.filters.period,
        };

        if (this.filters.customerId) {
          params.customer_id = this.filters.customerId;
        }

        if (this.filters.employeeMarker) {
          params.employee_marker = this.filters.employeeMarker;
        }

        if (this.filters.period === 'custom') {
          if (this.filters.startDate) {
            params.start_date = this.filters.startDate;
          }
          if (this.filters.endDate) {
            params.end_date = this.filters.endDate;
          }
        }

        const data = await OfferService.getDashboardStats(params);
        this.stats = data;
        
        // Pobierz najpopularniejsze narzędzia z tymi samymi filtrami
        const popularTools = await OfferService.getPopularTools(params);
        this.popularFiles = popularTools.popularFiles;
        this.popularCombinations = popularTools.popularCombinations;
        this.popularCoatings = popularTools.popularCoatings;
      } catch (error: any) {
        this.errors = [error?.response?.data?.error || 'Wystąpił błąd podczas pobierania statystyk.'];
        console.error('Błąd pobierania statystyk:', error);
      } finally {
        this.isLoading = false;
      }
    },

    setFilter(filter: Partial<DashboardFilters>) {
      this.filters = { ...this.filters, ...filter };
    },

    resetFilters() {
      this.filters = {
        customerId: null,
        employeeMarker: null,
        period: 'month',
        startDate: '',
        endDate: '',
      };
    },
  },
});

