<template>
  <AppLayout
    :title="household.name"
    :subtitle="currentMonth"
    :currentHousehold="household"
  >
    <template #header-actions>
      <div class="flex items-center gap-2">
        <!-- Invite code -->
        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-lg">
          <span class="text-xs text-gray-500 dark:text-gray-400">Einladung:</span>
          <code class="text-sm font-mono font-bold text-indigo-600 dark:text-indigo-400 tracking-wider">{{ household.invite_code }}</code>
          <button @click="copyCode" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition" title="Kopieren">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
          </button>
        </div>
        <Link :href="route('transactions.index', household.id)"
          class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
          <IconPlus /> Transaktion
        </Link>
      </div>
    </template>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <StatCard label="Einnahmen" :value="monthlyIncome" icon="💰" iconBg="bg-green-50 dark:bg-green-900/20" valueClass="text-green-600 dark:text-green-400" :currency="household.currency" :sub="currentMonth" />
      <StatCard label="Ausgaben" :value="monthlyExpenses" icon="💸" iconBg="bg-red-50 dark:bg-red-900/20" valueClass="text-red-600 dark:text-red-400" :currency="household.currency" :sub="currentMonth" />
      <StatCard label="Bilanz" :value="balance" icon="⚖️"
        :iconBg="balance >= 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20'"
        :valueClass="balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'"
        :currency="household.currency" :sub="currentMonth" />
      <StatCard label="Mitglieder" :value="household.members?.length || 0" icon="👥" :currency="null" sub="im Haushalt" />
    </div>

    <!-- Charts row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
      <!-- Monthly trend line chart -->
      <Card title="Einnahmen &amp; Ausgaben" class="lg:col-span-2">
        <div class="h-64">
          <Line :data="lineChartData" :options="lineChartOptions" />
        </div>
      </Card>

      <!-- Category doughnut -->
      <Card title="Ausgaben nach Kategorie">
        <div v-if="categoryBreakdown.length" class="h-64 flex items-center justify-center">
          <Doughnut :data="doughnutData" :options="doughnutOptions" />
        </div>
        <div v-else class="h-64 flex items-center justify-center text-gray-400 dark:text-gray-600 text-sm">Noch keine Ausgaben</div>
      </Card>
    </div>

    <!-- Budgets + Members row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
      <!-- Budgets -->
      <Card title="Budgetübersicht">
        <div v-if="budgets.length" class="space-y-4">
          <div v-for="b in budgets" :key="b.id">
            <div class="flex justify-between items-center mb-1.5">
              <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ b.name }}</span>
              <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ formatCurrency(b.spent) }} / {{ formatCurrency(b.amount) }}
              </span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
              <div class="h-2 rounded-full transition-all"
                :style="{ width: b.percentage + '%', backgroundColor: b.color }"
                :class="b.percentage >= 90 ? 'animate-pulse' : ''"
              />
            </div>
            <p class="text-xs mt-1" :class="b.percentage >= 100 ? 'text-red-500' : b.percentage >= 80 ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'">
              {{ b.percentage }}% verbraucht
            </p>
          </div>
        </div>
        <p v-else class="text-sm text-gray-400 dark:text-gray-500">Keine Budgets definiert</p>
      </Card>

      <!-- Member stats -->
      <Card title="Mitglieder diesen Monat">
        <div class="space-y-3">
          <div v-for="m in memberStats" :key="m.id" class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
            <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center flex-shrink-0">
              <span class="text-indigo-700 dark:text-indigo-300 font-bold text-xs">{{ initials(m.name) }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ m.name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ roleLabel(m.role) }}</p>
            </div>
            <div class="text-right flex-shrink-0">
              <p class="text-sm font-semibold text-green-600 dark:text-green-400">+{{ formatCurrency(m.income) }}</p>
              <p class="text-sm font-semibold text-red-500 dark:text-red-400">-{{ formatCurrency(m.expenses) }}</p>
            </div>
          </div>
        </div>
      </Card>
    </div>

    <!-- Recent transactions -->
    <Card title="Letzte Transaktionen" :noPadding="true">
      <template #header-action>
        <Link :href="route('transactions.index', household.id)" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Alle anzeigen</Link>
      </template>
      <div v-if="recentTransactions.length" class="divide-y divide-gray-100 dark:divide-gray-800">
        <div v-for="t in recentTransactions" :key="t.id" class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
          <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0 text-base"
            :style="{ backgroundColor: (t.category?.color || '#94a3b8') + '20' }">
            {{ t.type === 'income' ? '💰' : '💸' }}
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ t.description }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ t.user?.name }} &middot; {{ formatDate(t.date) }}<span v-if="t.category"> &middot; {{ t.category.name }}</span></p>
          </div>
          <span class="text-sm font-bold flex-shrink-0" :class="t.type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
            {{ t.type === 'income' ? '+' : '-' }}{{ formatCurrency(t.amount) }}
          </span>
        </div>
      </div>
      <p v-else class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">Noch keine Transaktionen</p>
    </Card>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Line, Doughnut } from 'vue-chartjs';
import {
  Chart as ChartJS,
  CategoryScale, LinearScale, PointElement, LineElement,
  ArcElement, Tooltip, Legend, Filler
} from 'chart.js';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import StatCard from '@/Components/StatCard.vue';
import IconPlus from '@/Components/Icons/IconPlus.vue';

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, ArcElement, Tooltip, Legend, Filler);

const props = defineProps({
  household: Object,
  monthlyIncome: Number,
  monthlyExpenses: Number,
  balance: Number,
  chartData: Array,
  categoryBreakdown: Array,
  memberStats: Array,
  recentTransactions: Array,
  budgets: Array,
  currentMonth: String,
});

const isDark = () => document.documentElement.classList.contains('dark');

const lineChartData = computed(() => ({
  labels: props.chartData.map(d => d.month),
  datasets: [
    {
      label: 'Einnahmen',
      data: props.chartData.map(d => d.income),
      borderColor: '#22c55e',
      backgroundColor: 'rgba(34,197,94,0.1)',
      tension: 0.4,
      fill: true,
      pointBackgroundColor: '#22c55e',
      pointRadius: 4,
    },
    {
      label: 'Ausgaben',
      data: props.chartData.map(d => d.expense),
      borderColor: '#ef4444',
      backgroundColor: 'rgba(239,68,68,0.1)',
      tension: 0.4,
      fill: true,
      pointBackgroundColor: '#ef4444',
      pointRadius: 4,
    },
  ],
}));

const lineChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { size: 12 } } },
    tooltip: {
      callbacks: {
        label: ctx => ` ${new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(ctx.raw)}`,
      },
    },
  },
  scales: {
    x: { grid: { color: 'rgba(148,163,184,0.1)' }, ticks: { font: { size: 11 } } },
    y: {
      grid: { color: 'rgba(148,163,184,0.1)' },
      ticks: {
        font: { size: 11 },
        callback: v => new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR', minimumFractionDigits: 0 }).format(v),
      },
    },
  },
};

const doughnutData = computed(() => ({
  labels: props.categoryBreakdown.map(c => c.name),
  datasets: [{
    data: props.categoryBreakdown.map(c => c.total),
    backgroundColor: props.categoryBreakdown.map(c => c.color),
    borderWidth: 2,
    borderColor: '#ffffff',
  }],
}));

const doughnutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '65%',
  plugins: {
    legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 } } },
    tooltip: {
      callbacks: {
        label: ctx => ` ${ctx.label}: ${new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(ctx.raw)}`,
      },
    },
  },
};

function formatCurrency(v) {
  if (v === null || v === undefined) return '0,00 €';
  return new Intl.NumberFormat('de-DE', { style: 'currency', currency: props.household.currency || 'EUR' }).format(v);
}
function formatDate(d) {
  return new Date(d).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
}
function initials(name) {
  return (name || '').split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
}
function roleLabel(role) {
  return { owner: 'Eigentümer', admin: 'Admin', member: 'Mitglied' }[role] || 'Mitglied';
}
function copyCode() {
  navigator.clipboard.writeText(props.household.invite_code);
}
</script>
