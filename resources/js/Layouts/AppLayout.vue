<template>
  <div class="min-h-full bg-gray-50 dark:bg-gray-950">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col">
      <!-- Logo -->
      <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200 dark:border-gray-800">
        <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
          <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
        </div>
        <span class="font-bold text-gray-900 dark:text-white text-lg">HaushaltsHub</span>
      </div>

      <!-- Household selector -->
      <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">Haushalt</p>
        <Link :href="route('households.index')" class="flex items-center gap-2 px-2 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4" />
          </svg>
          Haushalt wechseln
        </Link>
      </div>

      <!-- Nav -->
      <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 px-2">Navigation</p>
        <NavLink :href="route('households.dashboard', household.id)" :active="isActive('dashboard')">
          <template #icon>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
          </template>
          Dashboard
        </NavLink>
        <NavLink :href="route('transactions.index', household.id)" :active="isActive('transactions')">
          <template #icon>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
          </template>
          Transaktionen
        </NavLink>
      </nav>

      <!-- User -->
      <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-800">
        <div class="flex items-center gap-3 px-2 py-2">
          <div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center text-white text-sm font-medium">
            {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $page.props.auth.user.name }}</p>
            <p class="text-xs text-gray-500 truncate">{{ $page.props.auth.user.email }}</p>
          </div>
        </div>
        <Link :href="route('logout')" method="post" as="button" class="w-full mt-2 flex items-center gap-2 px-2 py-2 rounded-lg text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-950 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
          Abmelden
        </Link>
      </div>
    </div>

    <!-- Main content -->
    <div class="pl-64">
      <!-- Flash messages -->
      <div v-if="$page.props.flash.success || $page.props.flash.error" class="fixed top-4 right-4 z-50 space-y-2">
        <div v-if="$page.props.flash.success" class="flex items-center gap-3 bg-green-500 text-white px-4 py-3 rounded-xl shadow-lg max-w-sm">
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
          <span class="text-sm">{{ $page.props.flash.success }}</span>
        </div>
        <div v-if="$page.props.flash.error" class="flex items-center gap-3 bg-red-500 text-white px-4 py-3 rounded-xl shadow-lg max-w-sm">
          <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          <span class="text-sm">{{ $page.props.flash.error }}</span>
        </div>
      </div>

      <main class="min-h-screen p-8">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
  household: { type: Object, required: true },
});

const page = usePage();

function isActive(section) {
  const url = page.url;
  return url.includes(section);
}
</script>
