<template>
  <Link
    :href="href"
    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors"
    :class="active
      ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300'
      : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'"
  >
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ href: String, active: Boolean });
</script>
