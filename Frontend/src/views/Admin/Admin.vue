<script setup>
import AdminNav from '../../components/AdminNav.vue';
</script>

<template>
    <div class="wrapper">
    <AdminNav />
      <router-view />
    
        <div class="about">
            <h1>Admin Dashboard</h1>
        </div>
    </div>
  </template>
  
<style scoped>
    .wrapper {
        display: flex;
    }
    .about {
        margin-left: 101px;
    }
</style>
  