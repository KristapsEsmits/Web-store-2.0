<template>
  <div>
    <div :class="{ 'nav': true, 'active': isSidebarOpen }">
      <div class="links">
        <router-link to="/admin">
          <button class="btn" v-if="isSidebarOpen">H</button>
          <button class="btn" v-else>Home</button>
        </router-link>
        <router-link to="/test">
          <button class="btn" v-if="isSidebarOpen">T</button>
          <button class="btn" v-else>Test</button>
        </router-link>
        <router-link to="/admin/brands">
          <button class="btn" v-if="isSidebarOpen">B</button>
          <button class="btn" v-else>Brands</button>
        </router-link>
        <router-link to="/admin/items">
          <button class="btn" v-if="isSidebarOpen">I</button>
          <button class="btn" v-else>Items</button>
        </router-link>
      </div>
    </div>
    <button @click="toggleSidebar" class="btn toggle-btn">
      <span v-if="isSidebarOpen">⯇</span>
      <span v-else>⯈</span>
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const isSidebarOpen = ref(false);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<style scoped>
.nav {
  position: fixed;
  top: 59.7px;
  left: 0;
  background-color: #f8f9fa;
  padding-top: 20px;
  height: calc(100vh - 213px);
  transition: margin-left 0.3s ease, width 0.3s ease; /* Add transition for sidebar animation */
}

.nav.active {
  margin-left: 0;
}

.links {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.btn {
  outline: none !important;
  border: none !important;
  text-decoration: none !important;
  box-shadow: none !important;
  width: 100%;
}

.toggle-btn {
  position: fixed;
  bottom: 153px;
  width: 40px;
  height: 40px;
  cursor: pointer;
  transition: margin-left 0.3s ease, width 0.3s ease; /* Add transition for sidebar animation */
}
</style>
