<template>
  <AppLayout
    title="Transaktionen"
    :subtitle="household.name"
    :currentHousehold="household"
  >
    <template #header-actions>
      <button @click="openAdd"
        class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
        <IconPlus /> Hinzufügen
      </button>
    </template>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-5">
      <select v-model="filter.type"
        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Alle Typen</option>
        <option value="income">Einnahmen</option>
        <option value="expense">Ausgaben</option>
      </select>

      <select v-model="filter.category"
        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Alle Kategorien</option>
        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>

      <input v-model="filter.search" type="text" placeholder="Suchen..."
        class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 min-w-48" />

      <div class="ml-auto flex items-center gap-2">
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ filteredTransactions.length }} Einträge</span>
        <span class="text-sm font-semibold" :class="totalBalance >= 0 ? 'text-green-600' : 'text-red-500'">
          Summe: {{ formatCurrency(totalBalance) }}
        </span>
      </div>
    </div>

    <!-- Table -->
    <Card :noPadding="true">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200 dark:border-gray-800">
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Datum</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Beschreibung</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategorie</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Person</th>
              <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Betrag</th>
              <th class="px-6 py-3 w-20"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            <tr v-if="!filteredTransactions.length">
              <td colspan="6" class="px-6 py-12 text-center text-gray-400 dark:text-gray-500 text-sm">Keine Transaktionen gefunden</td>
            </tr>
            <tr v-for="t in filteredTransactions" :key="t.id" class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
              <td class="px-6 py-3.5 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ formatDate(t.date) }}</td>
              <td class="px-6 py-3.5">
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ t.description }}</p>
                <p v-if="t.notes" class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ t.notes }}</p>
              </td>
              <td class="px-6 py-3.5">
                <span v-if="t.category" class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-xs font-medium"
                  :style="{ backgroundColor: t.category.color + '20', color: t.category.color }">
                  {{ t.category.name }}
                </span>
                <span v-else class="text-xs text-gray-400 dark:text-gray-500">—</span>
              </td>
              <td class="px-6 py-3.5 text-sm text-gray-600 dark:text-gray-400">{{ t.user?.name }}</td>
              <td class="px-6 py-3.5 text-right">
                <span class="text-sm font-bold" :class="t.type === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                  {{ t.type === 'income' ? '+' : '-' }}{{ formatCurrency(t.amount) }}
                </span>
              </td>
              <td class="px-6 py-3.5">
                <div class="flex items-center gap-1 justify-end">
                  <button @click="openEdit(t)" class="p-1.5 rounded text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition">
                    <IconPencil />
                  </button>
                  <button @click="deleteTransaction(t)" class="p-1.5 rounded text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                    <IconTrash />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="transactions.last_page > 1" class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex items-center justify-between">
        <span class="text-sm text-gray-500 dark:text-gray-400">Seite {{ transactions.current_page }} von {{ transactions.last_page }}</span>
        <div class="flex gap-2">
          <Link v-if="transactions.prev_page_url" :href="transactions.prev_page_url" class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">&larr;</Link>
          <Link v-if="transactions.next_page_url" :href="transactions.next_page_url" class="px-3 py-1.5 rounded-lg border border-gray-300 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">&rarr;</Link>
        </div>
      </div>
    </Card>

    <!-- Add/Edit Modal -->
    <Modal :show="showModal" :title="editing ? 'Transaktion bearbeiten' : 'Neue Transaktion'" @close="closeModal">
      <form @submit.prevent="submitTransaction" class="space-y-4">
        <!-- Type toggle -->
        <div class="flex rounded-lg overflow-hidden border border-gray-300 dark:border-gray-700">
          <button type="button" @click="form.type = 'expense'"
            class="flex-1 py-2 text-sm font-semibold transition"
            :class="form.type === 'expense' ? 'bg-red-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'">
            💸 Ausgabe
          </button>
          <button type="button" @click="form.type = 'income'"
            class="flex-1 py-2 text-sm font-semibold transition"
            :class="form.type === 'income' ? 'bg-green-500 text-white' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'">
            💰 Einnahme
          </button>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Betrag *</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 font-semibold">{{ household.currency === 'EUR' ? '€' : household.currency }}</span>
            <input v-model="form.amount" type="number" step="0.01" min="0.01" required
              class="w-full pl-8 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm" />
          </div>
          <p v-if="form.errors.amount" class="text-red-500 text-xs mt-1">{{ form.errors.amount }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beschreibung *</label>
          <input v-model="form.description" type="text" required placeholder="z.B. Rewe Einkauf"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm" />
          <p v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategorie</label>
            <select v-model="form.category_id"
              class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm">
              <option :value="null">Keine</option>
              <option v-for="c in filteredCategories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Datum *</label>
            <input v-model="form.date" type="date" required
              class="w-full px-3 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notizen</label>
          <textarea v-model="form.notes" rows="2" placeholder="Optional..."
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm resize-none" />
        </div>

        <div class="flex gap-3 justify-end pt-1">
          <button type="button" @click="closeModal" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">Abbrechen</button>
          <button type="submit" :disabled="form.processing"
            class="px-5 py-2 rounded-lg text-white text-sm font-semibold transition disabled:opacity-50"
            :class="form.type === 'income' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-500 hover:bg-red-600'">
            {{ form.processing ? 'Speichern...' : (editing ? 'Aktualisieren' : 'Hinzufügen') }}
          </button>
        </div>
      </form>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';
import Modal from '@/Components/Modal.vue';
import IconPlus from '@/Components/Icons/IconPlus.vue';
import IconPencil from '@/Components/Icons/IconPencil.vue';
import IconTrash from '@/Components/Icons/IconTrash.vue';

const props = defineProps({
  household: Object,
  transactions: Object,
  categories: Array,
});

const showModal = ref(false);
const editing = ref(null);
const filter = ref({ type: '', category: '', search: '' });

const form = useForm({
  type: 'expense',
  amount: '',
  description: '',
  notes: '',
  category_id: null,
  date: new Date().toISOString().split('T')[0],
});

const filteredCategories = computed(() => {
  return props.categories.filter(c => c.type === form.type || c.type === 'both');
});

const filteredTransactions = computed(() => {
  return (props.transactions.data || []).filter(t => {
    if (filter.value.type && t.type !== filter.value.type) return false;
    if (filter.value.category && t.category_id != filter.value.category) return false;
    if (filter.value.search) {
      const s = filter.value.search.toLowerCase();
      if (!t.description.toLowerCase().includes(s) && !t.user?.name.toLowerCase().includes(s)) return false;
    }
    return true;
  });
});

const totalBalance = computed(() => {
  return filteredTransactions.value.reduce((sum, t) => {
    return sum + (t.type === 'income' ? parseFloat(t.amount) : -parseFloat(t.amount));
  }, 0);
});

function openAdd() {
  editing.value = null;
  form.reset();
  form.date = new Date().toISOString().split('T')[0];
  showModal.value = true;
}

function openEdit(t) {
  editing.value = t;
  form.type = t.type;
  form.amount = t.amount;
  form.description = t.description;
  form.notes = t.notes || '';
  form.category_id = t.category_id;
  form.date = t.date.split('T')[0];
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  editing.value = null;
  form.reset();
}

function submitTransaction() {
  if (editing.value) {
    form.put(route('transactions.update', [props.household.id, editing.value.id]), {
      onSuccess: closeModal,
    });
  } else {
    form.post(route('transactions.store', props.household.id), {
      onSuccess: closeModal,
    });
  }
}

function deleteTransaction(t) {
  if (!confirm(`"${t.description}" wirklich löschen?`)) return;
  router.delete(route('transactions.destroy', [props.household.id, t.id]));
}

function formatCurrency(v) {
  return new Intl.NumberFormat('de-DE', { style: 'currency', currency: props.household.currency || 'EUR' }).format(v || 0);
}
function formatDate(d) {
  return new Date(d).toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
}
</script>
