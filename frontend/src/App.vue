<script setup lang="ts">
import { RouterView } from 'vue-router'
</script>

<template>
  <div class="min-h-screen bg-slate-100 text-slate-900">
    <RouterView />
  </div>
</template>

<style scoped>
:global(body) {
  margin: 0;
  font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  background-color: #f1f5f9;
}
</style>
