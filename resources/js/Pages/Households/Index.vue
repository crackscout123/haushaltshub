<template>
  <AppLayout title="Meine Haushalte">
    <template #header-actions>
      <div class="flex items-center gap-3">
        <button @click="showJoin = true"
          class="flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
          Haushalt beitreten
        </button>
        <Link :href="route('households.create')"
          class="flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition">
          <IconPlus /> Neuer Haushalt
        </Link>
      </div>
    </template>

    <!-- Empty state -->
    <div v-if="!households.length" class="flex flex-col items-center justify-center py-20 text-center">
      <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-3xl mb-4">🏠</div>
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Noch kein Haushalt</h3>
      <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">Erstelle deinen ersten Haushalt oder tritt einem bestehenden bei.</p>
      <Link :href="route('households.create')" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
        Haushalt erstellen
      </Link>
    </div>

    <!-- Household grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <Link
        v-for="h in households"
        :key="h.id"
        :href="route('households.dashboard', h.id)"
        class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-6 hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-md transition-all group"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="w-11 h-11 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-xl">🏠</div>
          <span class="text-xs px-2 py-1 rounded-full font-medium"
            :class="roleBadge(h.pivot?.role)">{{ roleLabel(h.pivot?.role) }}</span>
        </div>
        <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ h.name }}</h3>
        <p v-if="h.description" class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ h.description }}</p>
        <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
          <span class="text-xs text-gray-500 dark:text-gray-400">👥 {{ h.members_count }} {{ h.members_count === 1 ? 'Mitglied' : 'Mitglieder' }}</span>
          <span class="text-xs text-gray-500 dark:text-gray-400">💱 {{ h.currency }}</span>
        </div>
      </Link>
    </div>

    <!-- Join Modal -->
    <Modal :show="showJoin" title="Haushalt beitreten" @close="showJoin = false">
      <form @submit.prevent="joinHousehold" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Einladungscode</label>
          <input v-model="joinCode" type="text" maxlength="8" placeholder="z.B. ABCD1234"
            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-mono uppercase tracking-widest text-center text-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition" />
        </div>
        <div class="flex gap-3 justify-end">
          <button type="button" @click="showJoin = false" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">Abbrechen</button>
          <button type="submit" :disabled="joinForm.processing" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition disabled:opacity-50">Beitreten</button>
        </div>
      </form>
    </Modal>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import IconPlus from '@/Components/Icons/IconPlus.vue';

defineProps({ households: Array });

const showJoin = ref(false);
const joinCode = ref('');
const joinForm = useForm({ invite_code: '' });

function joinHousehold() {
  joinForm.invite_code = joinCode.value.toUpperCase();
  joinForm.post(route('households.join'), {
    onSuccess: () => { showJoin.value = false; joinCode.value = ''; },
  });
}

function roleLabel(role) {
  return { owner: 'Eigentümer', admin: 'Admin', member: 'Mitglied' }[role] || 'Mitglied';
}
function roleBadge(role) {
  return {
    owner: 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300',
    admin: 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
    member: 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400',
  }[role] || 'bg-gray-100 text-gray-600';
}
</script>
