<script setup>
import {onMounted, ref} from 'vue';
import axios from 'axios';
import router from '../../../router';

const user = ref(null);
const currentPassword = ref('');
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
      current_password: currentPassword.value,
      password: newPassword.value,
      password_confirmation: newPasswordConfirmation.value,
    });

    await router.push({name: 'profile', query: {successMessage: response.data.message}});
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
    <div class="logo">
      <img alt="Logo" src="\favicon.ico" style="height: 60px; width: 60px;">
    </div>
    <div class="logoText">
      <h1>Change Password</h1>
    </div>
    <div class="card">
      <div class="card-body">
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ Array.isArray(error) ? error[0] : error }}
          </li>
        </ul>
        <div class="mb-3">
          <label for="currentPassword">Current Password</label>
          <input id="currentPassword" v-model="currentPassword" class="form-control" type="password"/>
        </div>
        <div class="mb-3">
          <label for="newPassword">New Password</label>
          <input id="newPassword" v-model="newPassword" class="form-control" type="password"/>
        </div>
        <div class="mb-3">
          <label for="confirmPassword">Confirm New Password</label>
          <input id="confirmPassword" v-model="newPasswordConfirmation" class="form-control" type="password"/>
        </div>
        <div class="action-btns">
          <button class="btn btn-primary change-btn" type="button" @click="changePassword">Change Password</button>
          <button class="btn btn-danger" type="button" @click="updateExit">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.action-btns {
  display: flex;
  justify-content: flex-end;
}

.change-btn {
  margin-right: 10px;
}

.logo, .logoText {
  display: flex;
  justify-content: center;
}

.container {
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
  padding: 15px;
}

.card {
  width: 100%;
  margin: 0 auto;
}

.card-body {
  padding: 1rem;
}

@media (max-width: 768px) {
  .container {
    padding: 0 10px;
  }

  .card-body {
    padding: 10px;
  }
}
</style>
