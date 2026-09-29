<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="$emit('close')" />
        <div class="relative bg-white dark:bg-gray-900 rounded-xl shadow-xl w-full max-w-lg border border-gray-200 dark:border-gray-800 max-h-[90vh] overflow-y-auto">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ title }}</h2>
            <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <div class="p-6">
            <slot />
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
defineProps({ show: Boolean, title: String });
defineEmits(['close']);
</script>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
