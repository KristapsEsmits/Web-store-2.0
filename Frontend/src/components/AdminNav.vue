<template>
  <header class="bar">
    <div class="wrapper">
      <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
          <RouterLink class="navbar-brand" to="/admin" @click="collapseNavbar">
            <img alt="Logo" src="/favicon.ico" style="height: 30px; width: 30px;">
            Admin Panel
          </RouterLink>
          <button aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
                  class="navbar-toggler" data-bs-target="#navbarSupportedContent" data-bs-toggle="collapse"
                  type="button">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div id="navbarSupportedContent" class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <RouterLink class="nav-link" to="/admin" @click="collapseNavbar">Dashboard</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink class="nav-link" to="/admin/brands" @click="collapseNavbar">Brands</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink class="nav-link" to="/admin/items" @click="collapseNavbar">Items</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink class="nav-link" to="/admin/categories" @click="collapseNavbar">Categories</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink class="nav-link" to="/admin/purchases" @click="collapseNavbar">Purchases</RouterLink>
              </li>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
              <li v-if="isLoggedIn" class="nav-item dropdown" id="user-dropdown">
                <a aria-expanded="false" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"
                   role="button">
                  <i class="bi bi-person-fill"></i>
                  {{ user?.name }} {{ user?.surname }}
                </a>
                <ul aria-labelledby="navbarDropdown" class="dropdown-menu">
                  <li>
                    <RouterLink class="dropdown-item" to="/profile" @click="collapseNavbar">My Profile</RouterLink>
                  </li>
                  <li v-if="isAdmin" class="nav-item">
                    <RouterLink class="dropdown-item" to="/" @click="collapseNavbar">Store</RouterLink>
                  </li>
                  <li class="dropdown-divider"></li>
                  <li class="nav-item">
                    <button class="dropdown-item" @click="logout">
                      <i class="bi bi-box-arrow-right"></i>
                      Logout
                    </button>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const user = ref(null);
const isLoggedIn = ref(false);
const route = useRoute();
const router = useRouter();

const isAdmin = computed(() => user.value && user.value.admin === 1);

const collapseNavbar = () => {
  const navbar = document.getElementById('navbarSupportedContent');
  if (navbar.classList.contains('show')) {
    const button = document.querySelector('.navbar-toggler');
    if (button) {
      button.click();
    }
  }
};

const checkLoginState = async () => {
  const token = localStorage.getItem('access_token');
  isLoggedIn.value = !!token;
  if (isLoggedIn.value) {
    await fetchUserData();
  }
};

const fetchUserData = async () => {
  try {
    const response = await axios.get('/user', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('access_token')}`
      }
    });
    user.value = response.data;
  } catch (error) {
    console.error('Error fetching user data:', error);
  }
};

const logout = async () => {
  try {
    await axios.get('/logout', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('access_token')}`
      }
    });
    localStorage.removeItem('access_token');
    isLoggedIn.value = false;
    user.value = null;
    router.push('/login');
  } catch (error) {
    console.error('Error logging out:', error);
  }
};

onMounted(() => {
  checkLoginState();
});
</script>

<style scoped>
.bar {
  background-color: #f8f9fa;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 100;
}

.wrapper {
  width: 100%;
}

.navbar-nav .nav-item .nav-link {
  color: #000;
  transition: color 0.3s ease;
}

.navbar-nav .nav-item .nav-link:hover {
  color: #007bff;
}
</style>
