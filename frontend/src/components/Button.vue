<template>
  <button
    :type="type"
    :disabled="disabled"
    :class="[
      'rounded-lg shadow-md transition font-bold border-0',
      disabled
        ? 'bg-gray-400 text-gray-600 cursor-not-allowed'
        : 'cursor-pointer text-white',
      variantClass,
      sizeClass
    ]"
    @click="onClick"
  >
    <slot />
  </button>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  type: {
    type: String,
    default: 'button',
    validator: (value) => ['button', 'submit', 'reset'].includes(value),
  },
  size: {
    type: String,
    default: 'normal',
    validator: (value) => ['normal', 'small'].includes(value),
  },
})

const emit = defineEmits(['click'])

function onClick(event) {
  if (!props.disabled) {
    emit('click', event)
  }
}

const variantClass = computed(() => {
  if (props.disabled) return ''
  switch (props.variant) {
    case 'secondary':
      return 'bg-gray-500 hover:bg-gray-600'
    case 'success':
      return 'bg-green-600 hover:bg-green-700'
    case 'danger':
      return 'bg-red-600 hover:bg-red-700'
    case 'warning':
      return 'bg-yellow-500 hover:bg-yellow-600'
    case 'primary':
    default:
      return 'bg-blue-600 hover:bg-blue-700'
  }
})

const sizeClass = computed(() => {
  switch (props.size) {
    case 'small':
      return 'px-2 py-1 text-sm'
    case 'normal':
    default:
      return 'px-5 py-2'
  }
})
</script>
