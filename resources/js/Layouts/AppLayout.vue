<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-950" :class="{ dark: isDark }">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col">
      <!-- Logo -->
      <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200 dark:border-gray-800">
        <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center">
          <span class="text-white font-bold text-sm">H</span>
        </div>
        <span class="font-semibold text-gray-900 dark:text-white text-lg">HaushaltsHub</span>
      </div>

      <!-- Household selector -->
      <div v-if="currentHousehold" class="px-4 py-3 border-b border-gray-200 dark:border-gray-800">
        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Aktueller Haushalt</p>
        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ currentHousehold.name }}</p>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <template v-if="currentHousehold">
          <NavLink :href="route('households.dashboard', currentHousehold.id)" :active="isRoute('households.dashboard')">
            <IconGrid /> Dashboard
          </NavLink>
          <NavLink :href="route('transactions.index', currentHousehold.id)" :active="isRoute('transactions.index')">
            <IconList /> Transaktionen
          </NavLink>
        </template>
        <NavLink :href="route('households.index')" :active="isRoute('households.index')">
          <IconHome /> Haushalte
        </NavLink>
      </nav>

      <!-- User / bottom -->
      <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-800">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2 min-w-0">
            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center flex-shrink-0">
              <span class="text-indigo-700 dark:text-indigo-300 font-semibold text-xs">{{ userInitials }}</span>
            </div>
            <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $page.props.auth.user.name }}</span>
          </div>
          <div class="flex items-center gap-1">
            <button @click="toggleDark" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
              <IconMoon v-if="!isDark" class="w-4 h-4" />
              <IconSun v-else class="w-4 h-4" />
            </button>
            <Link :href="route('logout')" method="post" as="button" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
              <IconLogout class="w-4 h-4" />
            </Link>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <div class="pl-64 flex flex-col min-h-screen">
      <!-- Top bar -->
      <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4 flex items-center justify-between">
        <div>
          <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ title }}</h1>
          <p v-if="subtitle" class="text-sm text-gray-500 dark:text-gray-400">{{ subtitle }}</p>
        </div>
        <slot name="header-actions" />
      </header>

      <!-- Flash messages -->
      <div v-if="$page.props.flash.success || $page.props.flash.error" class="px-6 pt-4">
        <div v-if="$page.props.flash.success" class="flex items-center gap-2 px-4 py-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 text-sm">
          <span>✓</span> {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash.error" class="flex items-center gap-2 px-4 py-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 text-sm">
          <span>✕</span> {{ $page.props.flash.error }}
        </div>
      </div>

      <!-- Page content -->
      <main class="flex-1 px-6 py-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import IconGrid from '@/Components/Icons/IconGrid.vue';
import IconList from '@/Components/Icons/IconList.vue';
import IconHome from '@/Components/Icons/IconHome.vue';
import IconLogout from '@/Components/Icons/IconLogout.vue';
import IconMoon from '@/Components/Icons/IconMoon.vue';
import IconSun from '@/Components/Icons/IconSun.vue';

const props = defineProps({
  title: String,
  subtitle: String,
  currentHousehold: Object,
});

const page = usePage();
const isDark = ref(localStorage.getItem('theme') === 'dark');

const userInitials = computed(() => {
  const name = page.props.auth.user?.name || '';
  return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});

function toggleDark() {
  isDark.value = !isDark.value;
  localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
}

function isRoute(name) {
  return route().current(name);
}
</script>
