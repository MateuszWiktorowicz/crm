<template>
  <tr>
    <td class="border border-gray-300 p-3">
      {{ index + 1 }}
    </td>

    <!-- Typ narzędzia -->
    <td class="border border-gray-300 p-3">
      <select
        :value="detail.toolType"
        class="w-full p-2 border rounded text-[11px]"
        @change="onToolTypeChange($event.target.value)"
      >
        <option disabled value="">Wybierz typ narzędzia</option>
        <option v-for="toolType in toolTypes" :key="toolType.id" :value="toolType.tool_type_name">
          {{ toolType.tool_type_name }}
        </option>
      </select>
    </td>

    <!-- Symmbol narzędzia -->
    <td class="border border-gray-300 p-3">
      <input
        type="text"
        :disabled="!isCustom"
        class="w-full p-2 border rounded text-[11px]"
        :value="symbol"
        @input="onSymbolInput($event.target.value)"
      />
    </td>

    <!-- Ilość ostrzy -->
    <td class="border border-gray-300 p-3">
      <select
        :value="detail.flutesNumber"
        :class="[
          'w-full p-2 border rounded text-[11px]',
          isCustom
            ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
            : 'bg-white text-black cursor-pointer',
        ]"
        @change="onFlutesNumberChange($event.target.value)"
        :disabled="isCustom"
      >
        <option disabled value="">Ilość ostrzy</option>
        <option v-for="flute in uniqueFlutesNumbers" :key="flute" :value="flute">
          {{ flute }}
        </option>
      </select>
    </td>

    <!-- Średnica -->
    <td class="border border-gray-300 p-3">
      <select
        :value="detail.diameter"
        :class="[
          'w-full p-2 border rounded text-[11px]',
          isCustom
            ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
            : 'bg-white text-black cursor-pointer',
        ]"
        @change="onDiameterChange($event.target.value)"
        :disabled="isCustom"
      >
        <option disabled value="">Średnica</option>
        <option v-for="diameter in uniqueDiameters" :key="diameter" :value="diameter">
          {{ diameter }}
        </option>
      </select>
    </td>

    <!-- Promień -->
    <td class="border border-gray-300 p-3">
      <input
        :disabled="!isRadiusEndMill || isCustom"
        min="0"
        type="number"
        :value="radius"
        :class="[
          'w-full p-2 border rounded text-[11px]',
          isCustom || !isRadiusEndMill
            ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
            : 'bg-white text-black cursor-pointer',
        ]"
        placeholder="Promień"
        @input="onRadiusChange($event.target.value)"
      />
    </td>

    <!-- Wariant ostrzenia -->
    <td class="border border-gray-300 p-3">
      <select
        :value="detail.regrinding_option"
        :class="[
          'w-full p-2 border rounded text-[11px]',
          isCustom
            ? 'bg-gray-100 text-gray-500 cursor-not-allowed'
            : 'bg-white text-black cursor-pointer',
        ]"
        @change="onRegrindingOptionChange($event.target.value)"
        :disabled="isCustom"
      >
        <option v-for="{ key, label } in regrindingOptions" :key="key" :value="key">
          {{ label }}
        </option>
      </select>
    </td>

    <!-- Cena jednostkowa ostrzenia netto -->
    <td class="border border-gray-300 p-3">
      <input
        type="number"
        step="0.01"
        :value="detail.tool_net_price"
        class="w-full p-2 border rounded text-[11px]"
        @input="onManualToolPriceChange($event.target.value)"
      />
    </td>

    <!-- Pokrycie -->
    <td class="border border-gray-300 p-3">
      <select
        :value="detail.coatingCode"
        class="w-full p-2 border rounded text-[11px]"
        @change="onCoatingCodeChange($event.target.value)"
      >
        <option value="none" selected>Brak pokrycia</option>
        <option
          v-for="coatingType in coatingTypes"
          :key="coatingType.mastermet_code"
          :value="coatingType.mastermet_code"
        >
          {{ coatingType.mastermet_code }}
        </option>
      </select>
    </td>

    <!-- Cena jednostkowa pokrycia netto -->
    <td class="border border-gray-300 p-3">
      <input
        type="number"
        step="0.01"
        :value="detail.coating_net_price"
        class="w-full p-2 border rounded text-[11px]"
        @input="onManualCoatingPriceChange($event.target.value)"
      />
    </td>

    <!-- Ilość -->
    <td class="border border-gray-300 p-3">
      <input
        type="number"
        :value="detail.quantity"
        class="w-full p-2 border rounded text-[11px]"
        placeholder="Ilość"
        min="1"
        step="1"
        @change="onQuantityChange($event.target.value)"
      />
    </td>

    <!-- Rabat -->
    <td class="border border-gray-300 p-3">
      <input
        type="number"
        :value="detail.discount"
        class="w-full p-2 border rounded text-[11px]"
        placeholder="Rabat (%)"
        @change="onDiscountChange($event.target.value)"
        :disabled="globalDiscount !== 0"
      />
    </td>

    <!-- Cena całkowita netto -->
    <td class="border border-gray-300 p-3">{{ totalNetPrice }} PLN</td>

    <!-- Cena całkowita brutto -->
    <td class="border border-gray-300 p-3">{{ totalNetPriceBrutto }} PLN</td>

    <!-- Opis -->
    <td class="border border-gray-300 p-3">
      <textarea
        v-model="detail.description"
        class="w-full p-2 border rounded text-left resize-none"
        rows="2"
        placeholder="Opis pozycji"
      ></textarea>
    </td>

    <!-- Akcja -->
    <td class="border border-gray-300 p-3">
      <button @click="onRemoveRow" class="btn btn-danger">Usuń</button>
    </td>
  </tr>
</template>

<script setup>
  import { computed } from 'vue';

  defineProps({
    detail: Object,
    index: Number,
    toolTypes: Array,
    coatingTypes: Array,
    uniqueFlutesNumbers: Array,
    uniqueDiameters: Array,
    regrindingOptions: Array,
    isCustom: Boolean,
    isRadiusEndMill: Boolean,
    symbol: String,
    radius: [Number, String],
    globalDiscount: Number,
    totalNetPrice: Number,
    totalNetPriceBrutto: Number,
  });

  const emit = defineEmits([
    'toolTypeChange',
    'flutesNumberChange',
    'diameterChange',
    'radiusChange',
    'regrindingChange',
    'manualToolPriceChange',
    'manualCoatingPriceChange',
    'quantityChange',
    'discountChange',
    'coatingCodeChange',
    'removeRow',
    'symbolChange',
  ]);

  function onToolTypeChange(value) {
    emit('toolTypeChange', { index: __props.index, value });
  }
  function onFlutesNumberChange(value) {
    emit('flutesNumberChange', { index: __props.index, value });
  }
  function onDiameterChange(value) {
    emit('diameterChange', { index: __props.index, value });
  }
  function onRadiusChange(value) {
    emit('radiusChange', { index: __props.index, value });
  }
  function onRegrindingOptionChange(value) {
    emit('regrindingChange', { index: __props.index, value });
  }
  function onManualToolPriceChange(value) {
    emit('manualToolPriceChange', { index: __props.index, value });
  }
  function onManualCoatingPriceChange(value) {
    emit('manualCoatingPriceChange', { index: __props.index, value });
  }
  function onQuantityChange(value) {
    emit('quantityChange', { index: __props.index, value });
  }
  function onDiscountChange(value) {
    emit('discountChange', { index: __props.index, value });
  }
  function onCoatingCodeChange(value) {
    emit('coatingCodeChange', { index: __props.index, value });
  }
  function onRemoveRow() {
    emit('removeRow', __props.index);
  }
  function onSymbolInput(value) {
    emit('symbolChange', { index: __props.index, value });
  }
</script>
