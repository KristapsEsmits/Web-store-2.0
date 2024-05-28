<template>
  <div :class="{ 'nav': true, 'active': isSidebarOpen }">
    <ul class="links">
      <li>
        <router-link to="/admin">
          <button v-if="isSidebarOpen" class="btn"><i class="bi bi-house"></i></button>
          <button v-else class="btn">Home</button>
        </router-link>
      </li>

      <li>
        <router-link to="/admin/brands">
          <button v-if="isSidebarOpen" class="btn">B</button>
          <button v-else class="btn">Brands</button>
        </router-link>
      </li>

      <li>
        <router-link to="/admin/items">
          <button v-if="isSidebarOpen" class="btn">I</button>
          <button v-else class="btn">Items</button>
        </router-link>
      </li>

      <li>
        <router-link to="/admin/categories">
          <button v-if="isSidebarOpen" class="btn"><i class="bi bi-box-seam"></i></button>
          <button v-else class="btn">Categories</button>
        </router-link>
      </li>

      <li>
        <router-link to="/admin/purchases">
          <button v-if="isSidebarOpen" class="btn"><i class="bi bi-box-seam"></i></button>
          <button v-else class="btn">Purchases</button>
        </router-link>
      </li>

      <li style="margin-top: auto;">
        <button class="btn toggle-btn" @click="toggleSidebar">
          <span v-if="isSidebarOpen">⯇</span>
          <span v-else>⯈</span>
        </button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import {ref} from 'vue';

const isSidebarOpen = ref(false);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};
</script>

<style scoped>
ul {
  text-decoration: none;
  padding: 0;
}

li {
  list-style-type: none;
}

.nav {
  background-color: #f8f9fa;
  padding-top: 20px;
  height: calc(100vh - 59.7px);
}

.links {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.btn {
  outline: none;
  border: none;
  text-decoration: none;
  box-shadow: none;
  width: 100%;
}

.toggle-btn {
  width: 40px;
  height: 40px;
  cursor: pointer;
  transition: margin-left 0.3s ease, width 0.3s ease;
}
</style>
