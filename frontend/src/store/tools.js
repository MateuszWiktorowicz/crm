import { defineStore } from 'pinia';
import axiosClient from '../axios.js';

const useToolsStore = defineStore('tools', {
  state: () => ({
    tools: [],
    toolTypes: [],
    selectedTypes: [],
    filteredTools: [],
    files: [],
    filters: {
      tool_type_name: '',
      flutes_number: '',
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
        console.log(this.files);
      } catch (error) {}
    },
    setFilter(column, value) {
      this.filters[column] = value;
      this.filterTools();
    },
    filterTools() {
      if (!Array.isArray(this.tools)) return;

      this.filteredTools = this.tools.filter((tool) =>
        Object.entries(this.filters).every(([key, value]) => {
          if (!value) return true;

          const toolValue = tool[key];

          if (typeof toolValue === 'string') {
            return toolValue.toLowerCase().includes(value.toLowerCase());
          }

          if (typeof toolValue === 'number') {
            return toolValue.toString().includes(value.toString());
          }

          return false;
        })
      );
    },
  },
  getters: {
    getUniqueFlutesNumbers: (state) => (toolType) => {
      if (!toolType || !state.tools) return [];

      const filtered = state.tools.filter((tool) => tool.tool_type_name === toolType);

      return [...new Set(filtered.map((tool) => tool.flutes_number))];
    },
    getUniqueDiameters: (state) => (toolType, flutesNumber) => {
      if (!toolType || !flutesNumber || !state.tools) return [];
      const filtered = state.tools.filter(
        (tool) => tool.tool_type_name === toolType && tool.flutes_number === flutesNumber
      );
      return [...new Set(filtered.map((tool) => tool.diameter))];
    },
    getRegrindingOptions: (state) => (toolType, flutesNumber, diameter) => {
      if (!toolType || !flutesNumber || !diameter || !state.tools) return [];

      const selectedTool = state.tools.find(
        (tool) =>
          tool.tool_type_name === toolType &&
          tool.flutes_number === flutesNumber &&
          tool.diameter === diameter
      );

      return selectedTool ? selectedTool.regrinding_options : [];
    },
    getSelectedTool: (state) => (toolType, flutesNumber, diameter) => {
      if (!toolType || !flutesNumber || !diameter || !state.tools) return null;

      return (
        state.tools.find(
          (tool) =>
            tool.tool_type_name === toolType &&
            tool.flutes_number === flutesNumber &&
            tool.diameter === diameter
        ) || null
      );
    },
  },
});

export default useToolsStore;
