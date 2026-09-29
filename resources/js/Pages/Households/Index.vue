<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-600 via-primary-500 to-indigo-600 p-4 md:p-8">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-3xl font-bold text-white">Meine Haushalte</h1>
          <p class="text-primary-100 mt-1">Wähle einen Haushalt aus oder erstelle einen neuen</p>
        </div>
        <div class="flex items-center gap-3">
          <button @click="showJoinModal = true" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl text-sm font-medium transition-colors backdrop-blur">
            Beitreten
          </button>
          <Link :href="route('households.create')" class="px-4 py-2 bg-white text-primary-600 rounded-xl text-sm font-semibold hover:bg-primary-50 transition-colors shadow">
            + Neu erstellen
          </Link>
        </div>
      </div>

      <!-- Households grid -->
      <div v-if="households.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <Link
          v-for="h in households"
          :key="h.id"
          :href="route('households.dashboard', h.id)"
          class="bg-white dark:bg-gray-900 rounded-2xl p-6 hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5 group"
        >
          <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/40 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
            </div>
            <span class="text-xs font-medium px-2 py-1 rounded-full"
              :class="h.pivot.role === 'owner' ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-600'">
              {{ roleLabel(h.pivot.role) }}
            </span>
          </div>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">{{ h.name }}</h3>
          <p v-if="h.description" class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ h.description }}</p>
          <div class="flex items-center gap-4 mt-4 text-sm text-gray-400">
            <span class="flex items-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              {{ h.members_count }} Mitglieder
            </span>
            <span>{{ h.currency }}</span>
          </div>
        </Link>
      </div>

      <div v-else class="bg-white dark:bg-gray-900 rounded-2xl p-12 text-center">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Noch kein Haushalt</h3>
        <p class="text-gray-500 mb-6">Erstelle deinen ersten Haushalt oder tritt einem bestehenden bei.</p>
        <Link :href="route('households.create')" class="inline-flex px-6 py-3 bg-primary-500 text-white rounded-xl font-medium hover:bg-primary-600 transition-colors">
          Haushalt erstellen
        </Link>
      </div>

      <!-- Logout -->
      <div class="text-center mt-6">
        <Link :href="route('logout')" method="post" as="button" class="text-sm text-white/70 hover:text-white transition-colors">
          Abmelden
        </Link>
      </div>
    </div>

    <!-- Join Modal -->
    <Modal :show="showJoinModal" title="Haushalt beitreten" @close="showJoinModal = false">
      <form @submit.prevent="joinHousehold" class="space-y-4">
        <InputField
          label="Einladungscode"
          v-model="joinCode"
          placeholder="z.B. ABCD1234"
          :error="joinError"
          required
        />
        <p class="text-sm text-gray-500">Den Einladungscode bekommst du vom Haushalt-Inhaber.</p>
        <div class="flex gap-3 justify-end">
          <button type="button" @click="showJoinModal = false" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">Abbrechen</button>
          <button type="submit" class="px-5 py-2 bg-primary-500 text-white rounded-xl text-sm font-medium hover:bg-primary-600 transition-colors">Beitreten</button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputField from '@/Components/InputField.vue';

const props = defineProps({ households: Array });

const showJoinModal = ref(false);
const joinCode = ref('');
const joinError = ref('');

function roleLabel(role) {
  return { owner: 'Inhaber', admin: 'Admin', member: 'Mitglied' }[role] ?? role;
}

function joinHousehold() {
  joinError.value = '';
  router.post(route('households.join'), { invite_code: joinCode.value }, {
    onError: (errors) => { joinError.value = errors.invite_code || 'Ungültiger Code'; },
    onSuccess: () => { showJoinModal.value = false; joinCode.value = ''; },
  });
}
</script>
