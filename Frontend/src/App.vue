<script setup>
import Nav from './components/Nav.vue';
import Footer from './components/Footer.vue';
</script>

<template>
  <main>
    <Nav />
      <router-view />
    <!-- <Footer /> -->
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