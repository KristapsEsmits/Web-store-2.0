<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const users = ref([]);
const isLoading = ref(true);
const router = useRouter(); 
const successMessage = ref(router.currentRoute.value.query.successMessage || ''); 

const dismissSuccessMessage = () => {
  successMessage.value = '';
};

onMounted(async () => {
  try {
    const data = await axios.get('/user');
    users.value = data.data;
  } catch (error) {
    if (error.response && error.response.status === 401) {
      await router.push({ name: 'login' });
    }
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <main>
    <div v-if="isLoading"></div>
    <div class="container" v-else>
      <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
          <span>{{ successMessage }}</span>
          <button type="button" class="btn-close" @click="dismissSuccessMessage"></button>
      </div>
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Profile
            <router-link :to="{ path: '/profile/edit' }" class="btn btn-primary btn-round btn-fill float-end">Update data</router-link>
            <router-link :to="{ path: '/profile/change-password' }" class="btn btn-primary btn-round btn-fill float-end">Change password</router-link>
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

<style scoped>
.card {
  margin-top: 20px;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}
</style>