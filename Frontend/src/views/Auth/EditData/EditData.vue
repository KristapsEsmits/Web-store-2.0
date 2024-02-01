<script setup>
import {onMounted, ref} from 'vue';
import axios from 'axios';
import router from '../../../router';

const user = ref(null);
const errorList = ref({});

onMounted(async () => {
  try {
    const response = await axios.get('/user');
    user.value = response.data;
  } catch (error) {
    if (error.response && error.response.status === 401) {
      await router.push({ name: 'login' });
    }
  }
});

const updateUser = async () => {
  try {
    const response = await axios.put(`profile/edit/${user.value.id}`, {
      name: user.value.name,
      surname: user.value.surname,
      phone: user.value.phone,
      email: user.value.email,
    });

    const userResponse = await axios.get('/user');
    user.value = userResponse.data;
    router.push({ name: 'profile', query: { successMessage: response.data.message } });

  } catch (error) {
    if (error.response && error.response.status === 422) {
      errorList.value = error.response.data.errors;
      console.error('Validation errors:', errorList.value);
    } else {
      console.error('Error updating user:', error.message);
    }
  }
};

const updateExit = () => {
  router.push('/profile');
};

</script>

<template>
  <div class="container mt-5" v-if="user">
    <div class="logo">
      <img src="\favicon.ico" alt="Logo" style="height: 60px; width: 60px;">
    </div>
    <div class="logoText">
      <h1>Profile information</h1>
    </div>
    <div class="card">
      <div class="card-body">
        <ul class="alert alert-danger" v-if="Object.keys(errorList).length > 0">
          <li class="mb-0 ms-3" v-for="(error, index) in errorList" :key="index">
            {{ error[0] }}
          </li>
        </ul>
        <div class="form-floating mb-3">
          <input type="text" v-model="user.name" class="form-control" placeholder="Name"/>
          <label for="Name">Name</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" v-model="user.surname" class="form-control" placeholder="Surname"/>
          <label for="Surname">Surname</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" v-model="user.phone" class="form-control" placeholder="Phone number"/>
          <label for="Phone number">Phone number</label>
        </div>
        <div class="form-floating mb-3">
          <input type="text" v-model="user.email" class="form-control" placeholder="Email"/>
          <label for="Email">Email</label>
        </div>
        <div class="mb-3">
          <button type="button" @click="updateUser" class="btn btn-primary updateBtn">Update</button>
          <button type="button" @click="updateExit" class="btn btn-danger">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  @import './EditData.scss';
</style>