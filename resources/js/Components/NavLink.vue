<template>
  <Link
    :href="href"
    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150"
    :class="active
      ? 'bg-primary-50 dark:bg-primary-950 text-primary-700 dark:text-primary-300'
      : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'
    "
  >
    <span :class="active ? 'text-primary-500' : 'text-gray-400'">
      <slot name="icon" />
    </span>
    <slot />
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({ href: String, active: Boolean });
</script>
