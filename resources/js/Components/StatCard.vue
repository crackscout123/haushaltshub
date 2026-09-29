<template>
  <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
    <div class="flex items-center justify-between mb-4">
      <div :class="[iconBg, 'w-12 h-12 rounded-xl flex items-center justify-center']">
        <slot name="icon" />
      </div>
      <span v-if="trend !== null" class="text-xs font-medium px-2 py-1 rounded-full"
        :class="trend >= 0 ? 'text-green-700 bg-green-100 dark:text-green-300 dark:bg-green-900/40' : 'text-red-700 bg-red-100 dark:text-red-300 dark:bg-red-900/40'"
      >
        {{ trend >= 0 ? '+' : '' }}{{ trend }}%
      </span>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ label }}</p>
    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ value }}</p>
    <p v-if="sub" class="text-xs text-gray-400 mt-1">{{ sub }}</p>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  value: String,
  sub: String,
  iconBg: { type: String, default: 'bg-primary-100 dark:bg-primary-900/40' },
  trend: { type: Number, default: null },
});
</script>
