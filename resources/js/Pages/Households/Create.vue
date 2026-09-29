<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-600 via-primary-500 to-indigo-600 flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
      <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-white">Neuen Haushalt erstellen</h1>
        <p class="text-primary-100 mt-1">Lade dann andere Personen per Einladungscode ein</p>
      </div>

      <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-8">
        <form @submit.prevent="submit" class="space-y-5">
          <InputField label="Haushalt-Name" v-model="form.name" placeholder="z.B. WG Musterstraße" :error="form.errors.name" required />
          <InputField label="Beschreibung (optional)" type="textarea" v-model="form.description" placeholder="Kurze Beschreibung..." :error="form.errors.description" />
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Währung</label>
            <InputField type="select" v-model="form.currency" :error="form.errors.currency">
              <option value="EUR">EUR – Euro</option>
              <option value="USD">USD – US-Dollar</option>
              <option value="CHF">CHF – Schweizer Franken</option>
              <option value="GBP">GBP – Britisches Pfund</option>
              <option value="PLN">PLN – Polnischer Złoty</option>
            </InputField>
          </div>

          <div class="flex gap-3 pt-2">
            <Link :href="route('households.index')" class="flex-1 py-3 px-4 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-center">
              Abbrechen
            </Link>
            <button type="submit" :disabled="form.processing" class="flex-1 py-3 px-4 bg-primary-500 hover:bg-primary-600 disabled:opacity-60 text-white font-semibold rounded-xl transition-colors">
              {{ form.processing ? 'Erstellen...' : 'Haushalt erstellen' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import InputField from '@/Components/InputField.vue';

const form = useForm({ name: '', description: '', currency: 'EUR' });
function submit() { form.post(route('households.store')); }
</script>
