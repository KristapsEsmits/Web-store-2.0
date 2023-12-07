<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import router from '../../router';

const users = ref([]);
const isLoading = ref(true);

onMounted(async () => {
  try {
    const data = await axios.get('/user');
    users.value = data.data;
  } catch (error) {
    if (error.response && error.response.status === 401) {
      // Redirect to login page when authentication fails
      await router.push({ name: 'login' });
    }
  } finally {
    // Set loading state to false after the request completes
    isLoading.value = false;
  }
});

</script>

<template>
  <main>
    <div v-if="isLoading"></div>
    <div class="container" v-else>
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Profile
            <router-link :to="{path: '/profile/edit'}" class="btn btn-primary btn-round btn-fill float-end">Update data</router-link>
          </h4>
        </div>
        <div class="card-body">
          <div>
            <h1>Name: {{ users?.name }}</h1>
            <h1>Surname: {{ users?.surname }}</h1>
            <h1>Phone number: {{ users?.phone }}</h1>
            <h1>Email: {{ users?.email }}</h1>
          </div>
        </div>
      </div> 
    </div>
  </main>
</template>
