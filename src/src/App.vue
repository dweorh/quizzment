<script setup lang="ts">
import { RouterView } from 'vue-router'
import { useSessionStore, loadSessionStore } from "@/stores/session.ts"
import router from './router';
import IconTwitter from './components/icons/IconTwitter.vue'
const session = useSessionStore()
loadSessionStore(session)

if (!session.id) {
  session.newId()
}
</script>

<template>
  <RouterView/>
  <div class="text-right pr-2 mt-4">
    <a href="https://twitter.com/dweorh" target="_blank"><IconTwitter class="icon mr-1" />@dweorh</a>
  </div>
</template>

<style>
@import '@/assets/main.css';

#app {
  max-width: 1024px;
  margin: 0 auto;
  font-weight: normal;
}

header {
  line-height: 1.5;
  max-height: 100vh;
}

a,
.green {
  text-decoration: none;
  color: hsla(160, 100%, 37%, 1);
  transition: 0.4s;
}
</style>
