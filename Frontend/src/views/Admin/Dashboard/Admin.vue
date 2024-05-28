<script setup>
import AdminNav from '../../../components/AdminNav.vue';
import axios from 'axios';
import {onMounted, ref} from 'vue';

const isSidebarOpen = ref(false);
const numberOfItems = ref(0);
const numberOfBrands = ref(0);
const numberOfUsers = ref(0);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const fetchNumberOfItems = () => {
  axios.get('/items')
      .then((res) => {
        numberOfItems.value = res.data.items.length;
      })
      .catch((error) => {
        console.error('Error fetching items:', error);
      });
};

const fetchNumberOfBrands = () => {
  axios.get('/brands')
      .then((res) => {
        numberOfBrands.value = res.data.brands.length;
      })
      .catch((error) => {
        console.error('Error fetching brands:', error);
      });
};

const fetchNumberOfUsers = () => {
  axios.get('/user-amount')
      .then((res) => {
        numberOfUsers.value = res.data.user_count; // Access user_count property
      })
      .catch((error) => {
        console.error('Error fetching registered users:', error);
      });
}

onMounted(() => {
  fetchNumberOfItems();
  fetchNumberOfBrands();
  fetchNumberOfUsers();
});
</script>

<template>
  <div class="wrapper">
    <AdminNav :isSidebarOpen="isSidebarOpen" @toggleSidebar="toggleSidebar"/>
    <div class="title">
      <h1>Admin Dashboard</h1>
      <div class="stats-wrapper">

        <div class="card">
          <h2 class="card-title">Number of listings</h2>
          <p class="card-desc">{{ numberOfItems }}</p>
        </div>

        <div class="card">
          <h2 class="card-title">Number of Brands</h2>
          <p class="card-desc">{{ numberOfBrands }}</p>
        </div>

        <div class="card">
          <h2 class="card-title">Registered Users</h2>
          <p class="card-desc">{{ numberOfUsers }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import './Dashboard.scss';
</style>
