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
      await router.push({name: 'login'});
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
    await router.push({name: 'profile', query: {successMessage: response.data.message}});

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
  <div v-if="user" class="container mt-5">
    <div class="logo">
      <img alt="Logo" src="/favicon.ico" style="height: 60px; width: 60px;">
    </div>
    <div class="logoText">
      <h1>Profile information</h1>
    </div>
    <div class="card">
      <div class="card-body">
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ error[0] }}
          </li>
        </ul>
        <div class="form-floating mb-3">
          <input v-model="user.name" class="form-control" placeholder="Name" type="text"/>
          <label for="Name">Name</label>
        </div>
        <div class="form-floating mb-3">
          <input v-model="user.surname" class="form-control" placeholder="Surname" type="text"/>
          <label for="Surname">Surname</label>
        </div>
        <div class="form-floating mb-3">
          <input v-model="user.phone" class="form-control" placeholder="Phone number" type="text"/>
          <label for="Phone number">Phone number</label>
        </div>
        <div class="form-floating mb-3">
          <input v-model="user.email" class="form-control" placeholder="Email" type="text"/>
          <label for="Email">Email</label>
        </div>
        <div class="mb-3">
          <button class="btn btn-primary updateBtn" type="button" @click="updateUser">Update</button>
          <button class="btn btn-danger" type="button" @click="updateExit">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.logo, .logoText {
  display: flex;
  justify-content: center;
}

.container {
  margin-top: 20px;
}

.card {
  display: flex;
  justify-content: center;
  width: 380px;
  margin: 0 auto;
}

.updateBtn {
  margin-right: 5px;
}
</style>