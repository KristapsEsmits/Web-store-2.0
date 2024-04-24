<script setup>
import {onMounted, ref} from 'vue';
import {useRouter} from 'vue-router';
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
      await router.push({name: 'login'});
    }
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <main>
    <div class="container">
      <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
        <span>{{ successMessage }}</span>
        <button class="btn-close" type="button" @click="dismissSuccessMessage"></button>
      </div>
      <div class="card">
        <div class="card-body">
          <div>
            <router-link :to="{ path: '/profile/change-password' }" class="float-end nav-link">Change password
            </router-link>
            <router-link :to="{ path: '/profile/edit' }" class="actionBtn float-end nav-link">Update data</router-link>
            <h1>{{ users?.name }} {{ users?.surname }}</h1>
            <h2>Number {{ users?.phone }}</h2>
            <h2>Email {{ users?.email }}</h2>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
@import './Profile.scss';
</style>