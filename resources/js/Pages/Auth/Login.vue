<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-600 via-primary-500 to-indigo-600 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl backdrop-blur mb-4">
          <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-white">HaushaltsHub</h1>
        <p class="text-primary-100 mt-1">Gemeinsam Finanzen im Griff</p>
      </div>

      <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl p-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Anmelden</h2>

        <form @submit.prevent="submit" class="space-y-4">
          <InputField
            label="E-Mail"
            type="email"
            v-model="form.email"
            placeholder="deine@email.de"
            :error="form.errors.email"
            required
            autocomplete="email"
          />
          <InputField
            label="Passwort"
            type="password"
            v-model="form.password"
            placeholder="••••••••"
            :error="form.errors.password"
            required
            autocomplete="current-password"
          />

          <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 cursor-pointer">
              <input type="checkbox" v-model="form.remember" class="rounded border-gray-300 text-primary-500 focus:ring-primary-500" />
              Angemeldet bleiben
            </label>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-3 px-4 bg-primary-500 hover:bg-primary-600 disabled:opacity-60 text-white font-semibold rounded-xl transition-colors shadow-sm"
          >
            <span v-if="form.processing" class="flex items-center justify-center gap-2">
              <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
              Anmelden...
            </span>
            <span v-else>Anmelden</span>
          </button>
        </form>

        <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-6">
          Noch kein Konto?
          <Link :href="route('register')" class="text-primary-500 hover:text-primary-600 font-medium">Registrieren</Link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import InputField from '@/Components/InputField.vue';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

function submit() {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
}
</script>
