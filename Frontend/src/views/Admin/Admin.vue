<script setup>
import AdminNav from '../../components/AdminNav.vue';
</script>

<template>
    <div class="wrapper">
      <AdminNav :isSidebarOpen="isSidebarOpen" @toggleSidebar="toggleSidebar" />
      <router-view />
      
      <div class="about" :style="{ marginLeft: isSidebarOpen ? '250px' : '101px' }">
        <h1>Admin Dashboard</h1>
        <div class="form-floating mb-3">
          <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
          <label for="floatingInput">Email address</label>
        </div>
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
  