<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import Nav from './components/Nav.vue';
import AdminNav from './components/AdminNav.vue';

const route = useRoute();

const isAdminRoute = computed(() => route.path.startsWith('/admin'));
</script>

<template>
  <main>
    <Nav v-if="!isAdminRoute"/>
    <AdminNav v-else/>
    <router-view/>
  </main>
</template>

<style scoped>
main {
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 60px);
  flex: 1;
  margin-top: 60px;
}
</style>
