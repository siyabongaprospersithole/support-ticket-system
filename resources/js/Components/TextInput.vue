<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
        v-model="model"
        ref="input"
    />
</template>

<style scoped>
input {
    padding-left: 2.5rem !important; /* Ensure placeholder text is properly indented from icons */
}

input::placeholder {
    color: #9ca3af; /* Gray-400 */
    font-size: 0.875rem;
}
</style>
