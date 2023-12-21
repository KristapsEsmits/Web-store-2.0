<template>
  <div :class="{ 'nav': true, 'active': isSidebarOpen }">
    <div class="links">
      <router-link to="/admin">
        <button class="btn">Home</button>
      </router-link>
      <router-link to="/test">
        <button class="btn">Test</button>
      </router-link>
      <router-link to="/admin/brands">
        <button class="btn">Brands</button>
      </router-link>
      <router-link to="/admin/items">
        <button class="btn">Items</button>
      </router-link>
    </div>
    
    <button @click="toggleSidebar" class="btn toggle-btn">Toggle Sidebar</button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const isSidebarOpen = ref(falsegf);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<style scoped>
/* Add your existing styles */
.nav {
  position: fixed;
  top: 59.7px;
  left: 0;
  height: 100%;
  background-color: #f8f9fa; /* Add your preferred background color */
  padding-top: 20px; /* Add padding as needed */
}

.nav.active {
  margin-left: -250px; /* Adjust the width of your sidebar */
}

.btn {
  outline: none !important;
  border: none !important;
  text-decoration: none !important;
  box-shadow: none !important;
}

.toggle-btn {
  /* Style your toggle button as needed */
  position: fixed;
  bottom: 20px;
  left: 10px;
}

.links {
  display: flex;
  flex-direction: column;
  align-items: center;
}
</style>
