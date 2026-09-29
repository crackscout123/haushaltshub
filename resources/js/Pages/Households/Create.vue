<template>
  <AppLayout title="Neuer Haushalt" subtitle="Erstelle einen Haushalt für dich und deine Mitbewohner">
    <div class="max-w-lg">
      <Card>
        <form @submit.prevent="submit" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name *</label>
            <input v-model="form.name" type="text" required placeholder="z.B. WG Musterstraße"
              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm" />
            <p v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Beschreibung</label>
            <textarea v-model="form.description" rows="3" placeholder="Optional..."
              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm resize-none" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Währung</label>
            <select v-model="form.currency"
              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm">
              <option value="EUR">€ EUR – Euro</option>
              <option value="USD">$ USD – US-Dollar</option>
              <option value="CHF">CHF – Schweizer Franken</option>
              <option value="GBP">£ GBP – Britisches Pfund</option>
              <option value="PLN">zł PLN – Polnischer Zloty</option>
            </select>
          </div>

          <div class="flex gap-3 justify-end pt-2">
            <Link :href="route('households.index')" class="px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">Abbrechen</Link>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition">
              {{ form.processing ? 'Erstellen...' : 'Haushalt erstellen' }}
            </button>
          </div>
        </form>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from '@/Components/Card.vue';

const form = useForm({
  name: '',
  description: '',
  currency: 'EUR',
});

function submit() {
  form.post(route('households.store'));
}
</script>
