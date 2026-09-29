<template>
  <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6">
    <div class="flex items-center justify-between mb-3">
      <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ label }}</span>
      <span class="w-9 h-9 rounded-lg flex items-center justify-center text-lg" :class="iconBg">
        {{ icon }}
      </span>
    </div>
    <p class="text-2xl font-bold" :class="valueClass">{{ formattedValue }}</p>
    <p v-if="sub" class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ sub }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  label: String,
  value: Number,
  icon: String,
  iconBg: { type: String, default: 'bg-indigo-50 dark:bg-indigo-900/30' },
  valueClass: { type: String, default: 'text-gray-900 dark:text-white' },
  currency: { type: String, default: 'EUR' },
  sub: String,
});

const formattedValue = computed(() => {
  if (props.currency === null) {
    return String(props.value || 0);
  }
  return new Intl.NumberFormat('de-DE', { style: 'currency', currency: props.currency || 'EUR' }).format(props.value || 0);
});
</script>
