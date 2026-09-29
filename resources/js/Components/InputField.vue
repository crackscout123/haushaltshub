<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <component
      :is="type === 'select' ? 'select' : type === 'textarea' ? 'textarea' : 'input'"
      v-bind="$attrs"
      :type="type !== 'select' && type !== 'textarea' ? type : undefined"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @change="type === 'select' ? $emit('update:modelValue', $event.target.value) : null"
      class="w-full px-3.5 py-2.5 rounded-xl border text-sm transition-colors"
      :class="[
        error
          ? 'border-red-400 focus:ring-red-500 focus:border-red-500'
          : 'border-gray-300 dark:border-gray-700 focus:ring-primary-500 focus:border-primary-500',
        'bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400',
        'focus:outline-none focus:ring-2 focus:ring-offset-0',
      ]"
    >
      <slot />
    </component>
    <p v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</p>
  </div>
</template>

<script setup>
defineProps({
  label: String,
  modelValue: [String, Number],
  type: { type: String, default: 'text' },
  error: String,
  required: Boolean,
});
defineEmits(['update:modelValue']);
</script>
