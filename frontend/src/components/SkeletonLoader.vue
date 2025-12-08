<template>
  <div class="animate-pulse">
    <div v-if="type === 'table-row'" class="flex items-center space-x-4">
      <div
        v-for="i in columns"
        :key="i"
        class="h-4 bg-gray-200 rounded"
        :class="columnWidths[i - 1] || 'flex-1'"
      ></div>
    </div>
    <div v-else-if="type === 'table'" class="space-y-3">
      <div v-for="i in rows" :key="i" class="flex items-center space-x-4">
        <div
          v-for="j in columns"
          :key="j"
          class="h-4 bg-gray-200 rounded"
          :class="columnWidths[j - 1] || 'flex-1'"
        ></div>
      </div>
    </div>
    <div v-else-if="type === 'card'" class="space-y-4">
      <div class="h-4 bg-gray-200 rounded w-3/4"></div>
      <div class="h-4 bg-gray-200 rounded"></div>
      <div class="h-4 bg-gray-200 rounded w-5/6"></div>
    </div>
    <div v-else class="h-4 bg-gray-200 rounded" :class="width"></div>
  </div>
</template>

<script setup lang="ts">
  defineProps({
    type: {
      type: String,
      default: 'line',
      validator: (value: string) => ['line', 'table', 'table-row', 'card'].includes(value),
    },
    rows: {
      type: Number,
      default: 1,
    },
    columns: {
      type: Number,
      default: 1,
    },
    width: {
      type: String,
      default: 'w-full',
    },
    columnWidths: {
      type: Array as () => string[],
      default: () => [],
    },
  });
</script>

