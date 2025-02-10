<script setup>
defineProps({
    message: {
        type: [Array, String],
        default: () => [],
    },
});
</script>

<template>
    <div v-show="message[0]">
        <p class="text-sm text-red-600">
            {{ message[0] }}
        </p>
    </div>
</template>
