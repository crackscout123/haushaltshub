<template>
  <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm" :class="$attrs.class">
    <div v-if="title" class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
      <h3 class="font-semibold text-gray-900 dark:text-white">{{ title }}</h3>
      <slot name="header-action" />
    </div>
    <div :class="noPadding ? '' : 'p-6'">
      <slot />
    </div>
  </div>
</template>

<script setup>
defineProps({ title: String, noPadding: Boolean });
</script>
