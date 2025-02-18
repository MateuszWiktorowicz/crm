import { defineStore } from "pinia";
import axiosClient from "../axios.js";

const useToolsStore = defineStore('tools', {
    state: () => ({ 
        tools: [],
        toolTypes: [],
        selectedTypes: [],
        filteredTools: [],
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
            } catch (error) {
                console.log('Error: ', error);
            }
        },
        setFilter(column, value) {

            this.filters[column] = value;
            this.filterTools();
          },
          filterTools() {
            if (!Array.isArray(this.tools)) return;
         
            this.filteredTools = this.tools.filter(tool =>
              Object.entries(this.filters).every(([key, value]) => {
                if (!value) return true; // Jeśli filtr pusty, akceptujemy wszystko
         
                const toolValue = tool[key];
         
                // Jeśli toolValue to string, porównujemy z małymi literami
                if (typeof toolValue === "string") {

                  return toolValue.toLowerCase().includes(value.toLowerCase());
                }
         
                // Jeśli toolValue to liczba, konwertujemy na string
                if (typeof toolValue === "number") {

                  return toolValue.toString().includes(value.toString());
                }

                return false;
              })
            );
          }
    }
});

export default useToolsStore;