<script setup lang="ts">
import { ref } from "vue";

const message = ref("Hello from Vue!");
</script>

<template>
  <div class="p-8">
    <h1 class="text-2xl font-bold">{{ message }}</h1>
  </div>
</template>
