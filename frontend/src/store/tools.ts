import { defineStore } from 'pinia';
import axiosClient from '../axios.js';
import { Tool, ToolGeometry, ToolType } from '@/types/types.js';

export interface ToolFilters {
  toolTypeName: string;
  flutesNumber: string;
  diameter: string;
}

export interface ToolStoreState {
  tools: ToolGeometry[];
  toolTypes: ToolType[];
  selectedTypes: string[];
  filteredTools: ToolGeometry[];
  files: Tool[]; // Możesz otypować jeśli znasz strukturę
  filters: ToolFilters;
}

// === STORE ===

export const useToolsStore = defineStore('tools', {
  state: (): ToolStoreState => ({
    tools: [],
    toolTypes: [],
    selectedTypes: [],
    filteredTools: [],
    files: [],
    filters: {
      toolTypeName: '',
      flutesNumber: '',
      diameter: '',
    },
  }),
  actions: {
    async fetchTools() {
      try {
        const response = await axiosClient.get('/api/tools');
        this.tools = response.data.tools;
        this.toolTypes = response.data.toolTypes;
        this.filteredTools = this.tools;
        this.files = response.data.files;
      } catch (error) {}
    },
    setFilter(column: keyof ToolFilters, value: string) {
      this.filters[column] = value;
      this.filterTools();
    },

    filterTools() {
      if (!Array.isArray(this.tools)) return;

      this.filteredTools = this.tools.filter((tool) => {
        const { toolTypeName, flutesNumber, diameter } = this.filters;

        const matchesType = toolTypeName
          ? tool.toolType?.toolTypeName?.toLowerCase().includes(toolTypeName.toLowerCase())
          : true;

        const matchesFlutes = flutesNumber
          ? tool.flutesNumber?.toString().includes(flutesNumber)
          : true;

        const matchesDiameter = diameter ? tool.diameter?.toString().includes(diameter) : true;

        return matchesType && matchesFlutes && matchesDiameter;
      });
    },
  },
  getters: {
    getUniqueFlutesNumbers: (state) => (toolType: ToolType) => {
      if (!toolType || !state.tools) return;

      const filtered = state.tools.filter(
        (tool) => tool.toolType.toolTypeName === toolType.toolTypeName
      );
      return [...new Set(filtered.map((tool) => tool.flutesNumber))];
    },
    getDiameterOptions:
      (state) =>
      (toolType: ToolType, flutesNumber: number | null): { value: number; label: string }[] => {
        if (!toolType || !flutesNumber || !state.tools) return [];

        // 1) filtrujemy i wyciągamy unikalne średnice
        const unique = [
          ...new Set(
            state.tools
              .filter(
                (t) =>
                  t.toolType.toolTypeName === toolType.toolTypeName &&
                  t.flutesNumber === flutesNumber
              )
              .map((t) => t.diameter)
          ),
        ].sort((a, b) => a - b); // ważne: sort rosnący!

        // 2) sprawdzamy, czy to "Wiertlo Krete / Wiertło Kręte"
        const isTwistDrill =
          toolType.toolTypeName.toLowerCase().replace('ó', 'o').trim() === 'wiertlo krete';

        // 3) jeśli NIE wiertło kręte: klasyczne etykiety „6 mm”, „8 mm”, …
        if (!isTwistDrill) {
          return unique.map((d) => ({
            value: d,
            label: `${d} mm`,
          }));
        }

        // 4) dla wiertła krętego budujemy przedziały
        return unique.map((d, i) => {
          if (i === 0) {
            return { value: d, label: `>=${d}` };
          }
          const lower = +(unique[i - 1] + 0.1).toFixed(1); // 4 → 4.1
          return { value: d, label: `${lower} - ${d}` };
        });
      },
    getRegrindingOptions:
      (state) => (toolType: ToolType, flutesNumber: number | null, diameter: number | null) => {
        if (!toolType || !flutesNumber || !diameter || !state.tools) return [];

        const selectedTool = state.tools.find(
          (tool) =>
            tool.toolType.id === toolType.id &&
            tool.flutesNumber === flutesNumber &&
            tool.diameter === diameter
        );

        return selectedTool ? selectedTool.regrindingOptions : [];
      },
    getSelectedTool: (state) => (toolType: ToolType, flutesNumber: number, diameter: number) => {
      if (!toolType || !flutesNumber || !diameter || !state.tools) return null;

      return (
        state.tools.find(
          (tool) =>
            tool.toolType.id === toolType.id &&
            tool.flutesNumber === flutesNumber &&
            tool.diameter === diameter
        ) || null
      );
    },
  },
});

export default useToolsStore;
