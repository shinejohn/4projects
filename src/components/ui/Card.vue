<template>
  <div
    :class="[
      'rounded-xl border border-slate-200',
      hover && 'hover:shadow-lg transition-shadow duration-200',
      $attrs.class,
    ]"
  >
    <slot />
  </div>
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    hover?: boolean
  }>(),
  {
    hover: false,
  }
)
</script>

