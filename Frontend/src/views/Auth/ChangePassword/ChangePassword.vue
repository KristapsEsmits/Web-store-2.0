<script setup>
import {onMounted, ref} from 'vue';
import axios from 'axios';
import router from '../../../router';

const user = ref(null);
const currentPassword = ref(''); // Define currentPassword
const newPassword = ref('');
const newPasswordConfirmation = ref('');
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

const changePassword = async () => {
  try {
    const response = await axios.put(`/profile/change-password/${user.value.id}`, {
      current_password: currentPassword.value, // Use currentPassword.value
      new_password: newPassword.value,
      password_confirmation: newPasswordConfirmation.value,
    });

    router.push({name: 'profile', query: {successMessage: response.data.message}});

  } catch (error) {
    if (error.response && error.response.status === 422) {
      errorList.value = error.response.data.errors;
      console.error('Validation errors:', errorList.value);
    } else {
      console.error('Error updating password:', error.message);
    }
  }
};

const updateExit = () => {
  router.push('/profile');
};

</script>

<template>
  <div v-if="user" class="container mt-5">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Change Password</h4>
      </div>
      <div class="card-body">
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ Array.isArray(error) ? error[0] : error }}
          </li>
        </ul>
        <div class="mb-3">
          <label for="currentPassword">Current Password</label>
          <input v-model="currentPassword" class="form-control" type="password"/>
        </div>
        <div class="mb-3">
          <label for="newPassword">New Password</label>
          <input v-model="newPassword" class="form-control" type="password"/>
        </div>
        <div class="mb-3">
          <label for="confirmPassword">Confirm New Password</label>
          <input v-model="newPasswordConfirmation" class="form-control" type="password"/>
        </div>
        <div class="mb-3">
          <button class="btn btn-primary" type="button" @click="changePassword">Change Password</button>
          <button class="btn btn-danger" type="button" @click="updateExit">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import './ChangePassword.scss';
</style>