<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import router from '../../router';

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
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Update data</h4>
      </div>
      <div class="card-body">
        <ul class="alert alert-danger" v-if="Object.keys(errorList).length > 0">
          <li class="mb-0 ms-3" v-for="(error, index) in errorList" :key="index">
            {{ error[0] }}
          </li>
        </ul>
        <div class="mb-3">
          <label for="Name">Name</label>
          <input type="text" v-model="user.name" class="form-control" />
        </div>
        <div class="mb-3">
          <label for="Name">Surname</label>
          <input type="text" v-model="user.surname" class="form-control" />
        </div>
        <div class="mb-3">
          <label for="Name">Phone number</label>
          <input type="text" v-model="user.phone" class="form-control" />
        </div>
        <div class="mb-3">
          <label for="Name">Email</label>
          <input type="text" v-model="user.email" class="form-control" />
        </div>
        <div class="mb-3">
          <button type="button" @click="updateUser" class="btn btn-primary">Update</button>
          <button type="button" @click="updateExit" class="btn btn-danger">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>
