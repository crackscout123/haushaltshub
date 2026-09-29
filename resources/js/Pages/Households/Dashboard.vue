<template>
  <AppLayout :household="household">
    <div class="space-y-8">
      <!-- Page header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ household.name }}</h1>
          <p class="text-gray-500 dark:text-gray-400 mt-0.5">Dashboard · {{ currentMonth }}</p>
        </div>
        <div class="flex items-center gap-3">
          <button @click="showInviteModal = true" class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
            Einladen
          </button>
          <button @click="showTransactionModal = true" class="flex items-center gap-2 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Transaktion
          </button>
        </div>
      </div>

      <!-- KPI Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <StatCard label="Einnahmen" :value="formatCurrency(monthlyIncome)" sub="Dieser Monat" iconBg="bg-green-100 dark:bg-green-900/30">
          <template #icon>
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" /></svg>
          </template>
        </StatCard>
        <StatCard label="Ausgaben" :value="formatCurrency(monthlyExpenses)" sub="Dieser Monat" iconBg="bg-red-100 dark:bg-red-900/30">
          <template #icon>
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" /></svg>
          </template>
        </StatCard>
        <StatCard :label="balance >= 0 ? 'Überschuss' : 'Defizit'" :value="formatCurrency(Math.abs(balance))" sub="Einnahmen - Ausgaben"
          :iconBg="balance >= 0 ? 'bg-primary-100 dark:bg-primary-900/30' : 'bg-orange-100 dark:bg-orange-900/30'">
          <template #icon>
            <svg class="w-6 h-6" :class="balance >= 0 ? 'text-primary-500' : 'text-orange-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" /></svg>
          </template>
        </StatCard>
      </div>

      <!-- Charts Row -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Line/Bar chart -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
          <div class="flex items-center justify-between mb-6">
            <h3 class="font-semibold text-gray-900 dark:text-white">Einnahmen vs. Ausgaben</h3>
            <div class="flex gap-4 text-xs">
              <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span>Einnahmen</span>
              <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>Ausgaben</span>
            </div>
          </div>
          <div class="h-56">
            <Bar :data="barChartData" :options="barChartOptions" />
          </div>
        </div>

        <!-- Donut chart -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
          <h3 class="font-semibold text-gray-900 dark:text-white mb-6">Ausgaben nach Kategorie</h3>
          <div v-if="categoryBreakdown.length > 0">
            <div class="h-40 flex items-center justify-center">
              <Doughnut :data="donutData" :options="donutOptions" />
            </div>
            <div class="mt-4 space-y-2">
              <div v-for="cat in categoryBreakdown.slice(0, 5)" :key="cat.name" class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                  <span class="w-3 h-3 rounded-full inline-block shrink-0" :style="{ background: cat.color }"></span>
                  <span class="text-gray-600 dark:text-gray-400 truncate max-w-24">{{ cat.name }}</span>
                </div>
                <span class="font-medium text-gray-900 dark:text-white">{{ formatCurrency(cat.total) }}</span>
              </div>
            </div>
          </div>
          <div v-else class="h-48 flex items-center justify-center text-gray-400 text-sm text-center">
            Noch keine Ausgaben in diesem Monat
          </div>
        </div>
      </div>

      <!-- Budgets + Members -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Budget bars -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
          <h3 class="font-semibold text-gray-900 dark:text-white mb-5">Budgets</h3>
          <div v-if="budgets.length > 0" class="space-y-4">
            <div v-for="b in budgets" :key="b.id">
              <div class="flex items-center justify-between mb-1.5">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ b.name }}</span>
                <span class="text-sm text-gray-500">{{ formatCurrency(b.spent) }} / {{ formatCurrency(b.amount) }}</span>
              </div>
              <div class="h-2.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-700"
                  :style="{ width: b.percentage + '%', background: b.percentage >= 90 ? '#ef4444' : b.percentage >= 70 ? '#f97316' : b.color }"
                />
              </div>
              <p class="text-xs text-gray-400 mt-1">{{ b.percentage }}% verbraucht</p>
            </div>
          </div>
          <p v-else class="text-sm text-gray-400 text-center py-8">Keine Budgets konfiguriert</p>
        </div>

        <!-- Member stats -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-800 shadow-sm">
          <h3 class="font-semibold text-gray-900 dark:text-white mb-5">Mitglieder</h3>
          <div class="space-y-3">
            <div v-for="m in memberStats" :key="m.id" class="flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl">
              <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-medium text-sm shrink-0">
                {{ m.name.charAt(0).toUpperCase() }}
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                  <span class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ m.name }}</span>
                  <span class="text-xs px-1.5 py-0.5 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                    {{ roleLabel(m.role) }}
                  </span>
                </div>
                <div class="flex items-center gap-4 text-xs">
                  <span class="text-green-500 font-medium">+{{ formatCurrency(m.income) }}</span>
                  <span class="text-red-500 font-medium">-{{ formatCurrency(m.expenses) }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Transactions -->
      <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-800">
          <h3 class="font-semibold text-gray-900 dark:text-white">Letzte Transaktionen</h3>
          <Link :href="route('transactions.index', household.id)" class="text-sm text-primary-500 hover:text-primary-600 font-medium">Alle ansehen →</Link>
        </div>
        <div v-if="recentTransactions.length > 0">
          <div v-for="t in recentTransactions" :key="t.id"
            class="flex items-center gap-4 px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors border-b border-gray-100 dark:border-gray-800 last:border-0">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
              :class="t.type === 'income' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30'">
              <svg class="w-4 h-4" :class="t.type === 'income' ? 'text-green-500' : 'text-red-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="t.type === 'income' ? 'M7 11l5-5m0 0l5 5m-5-5v12' : 'M17 13l-5 5m0 0l-5-5m5 5V6'" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ t.description }}</p>
              <p class="text-xs text-gray-400">{{ t.user?.name }} · {{ t.category?.name ?? 'Sonstige' }} · {{ formatDate(t.date) }}</p>
            </div>
            <span class="text-sm font-semibold shrink-0" :class="t.type === 'income' ? 'text-green-500' : 'text-red-500'">
              {{ t.type === 'income' ? '+' : '-' }}{{ formatCurrency(t.amount) }}
            </span>
          </div>
        </div>
        <p v-else class="text-sm text-gray-400 text-center py-10">Noch keine Transaktionen</p>
      </div>
    </div>

    <!-- Invite Modal -->
    <Modal :show="showInviteModal" title="Einladungscode" @close="showInviteModal = false">
      <div class="space-y-4">
        <p class="text-sm text-gray-600 dark:text-gray-300">Teile diesen Code mit Personen, die dem Haushalt beitreten sollen:</p>
        <div class="flex items-center gap-3">
          <code class="flex-1 bg-gray-100 dark:bg-gray-800 px-4 py-3 rounded-xl text-lg font-mono font-bold text-center tracking-widest text-primary-600 dark:text-primary-400">
            {{ household.invite_code }}
          </code>
          <button @click="copyCode" class="px-3 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition-colors">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
          </button>
        </div>
        <p class="text-xs text-gray-400">Der Code kann jederzeit erneuert werden.</p>
        <button @click="regenerateCode" class="text-sm text-red-500 hover:text-red-600">Code erneuern</button>
      </div>
    </Modal>

    <!-- Add Transaction Modal -->
    <Modal :show="showTransactionModal" title="Transaktion hinzufügen" @close="showTransactionModal = false">
      <form @submit.prevent="submitTransaction" class="space-y-4">
        <div class="grid grid-cols-2 gap-3">
          <button type="button" @click="txForm.type = 'income'"
            class="py-2.5 rounded-xl text-sm font-medium border-2 transition-colors"
            :class="txForm.type === 'income' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'">
            ↑ Einnahme
          </button>
          <button type="button" @click="txForm.type = 'expense'"
            class="py-2.5 rounded-xl text-sm font-medium border-2 transition-colors"
            :class="txForm.type === 'expense' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'">
            ↓ Ausgabe
          </button>
        </div>

        <InputField label="Betrag (€)" type="number" step="0.01" min="0.01" v-model="txForm.amount" placeholder="0.00" :error="txForm.errors.amount" required />
        <InputField label="Beschreibung" v-model="txForm.description" placeholder="z.B. Einkauf Rewe" :error="txForm.errors.description" required />
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kategorie</label>
          <InputField type="select" v-model="txForm.category_id">
            <option value="">Keine Kategorie</option>
            <option v-for="c in filteredCategories" :key="c.id" :value="c.id">{{ c.name }}</option>
          </InputField>
        </div>
        <InputField label="Datum" type="date" v-model="txForm.date" :error="txForm.errors.date" required />
        <InputField label="Notizen" type="textarea" v-model="txForm.notes" placeholder="Optional..." />

        <div class="flex gap-3 justify-end pt-2">
          <button type="button" @click="showTransactionModal = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">Abbrechen</button>
          <button type="submit" :disabled="txForm.processing"
            class="px-5 py-2 text-sm font-semibold rounded-xl transition-colors text-white"
            :class="txForm.type === 'income' ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600'">
            {{ txForm.processing ? 'Speichern...' : 'Hinzufügen' }}
          </button>
        </div>
      </form>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router, useForm, Link } from '@inertiajs/vue3';
import { Bar, Doughnut } from 'vue-chartjs';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import Modal from '@/Components/Modal.vue';
import InputField from '@/Components/InputField.vue';

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

const showInviteModal = ref(false);
const showTransactionModal = ref(false);

const txForm = useForm({
  type: 'expense',
  amount: '',
  description: '',
  category_id: '',
  date: new Date().toISOString().split('T')[0],
  notes: '',
});

const filteredCategories = computed(() =>
  (props.household.categories ?? []).filter(c => c.type === txForm.type || c.type === 'both')
);

function formatCurrency(val) {
  return new Intl.NumberFormat('de-DE', { style: 'currency', currency: props.household.currency ?? 'EUR' }).format(val ?? 0);
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('de-DE', { day: '2-digit', month: 'short', year: 'numeric' });
}

function roleLabel(role) {
  return { owner: 'Inhaber', admin: 'Admin', member: 'Mitglied' }[role] ?? role;
}

function copyCode() {
  navigator.clipboard.writeText(props.household.invite_code);
}

function regenerateCode() {
  router.post(route('households.regenerate-code', props.household.id));
}

function submitTransaction() {
  txForm.post(route('transactions.store', props.household.id), {
    onSuccess: () => { showTransactionModal.value = false; txForm.reset(); },
  });
}

// Bar chart
const barChartData = computed(() => ({
  labels: props.chartData.map(d => d.month),
  datasets: [
    {
      label: 'Einnahmen',
      data: props.chartData.map(d => d.income),
      backgroundColor: 'rgba(34,197,94,0.8)',
      borderRadius: 6,
      borderSkipped: false,
    },
    {
      label: 'Ausgaben',
      data: props.chartData.map(d => d.expense),
      backgroundColor: 'rgba(239,68,68,0.8)',
      borderRadius: 6,
      borderSkipped: false,
    },
  ],
}));

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } },
    y: { grid: { color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 11 }, callback: v => '€' + v.toLocaleString('de-DE') } },
  },
};

// Donut chart
const donutData = computed(() => ({
  labels: props.categoryBreakdown.map(c => c.name),
  datasets: [{
    data: props.categoryBreakdown.map(c => c.total),
    backgroundColor: props.categoryBreakdown.map(c => c.color),
    borderWidth: 2,
    borderColor: '#ffffff',
    hoverBorderWidth: 0,
  }],
}));

const donutOptions = {
  responsive: true,
  maintainAspectRatio: false,
  cutout: '70%',
  plugins: { legend: { display: false } },
};
</script>
