<script setup>
// import { ref, onMounted } from 'vue';
// import axios from 'axios';
// import router from '../../router';

// const user = ref(null);
// const newPassword = ref('');
// const passwordConfirmation = ref('');
// const errorList = ref({});

// onMounted(async () => {
//   try {
//     const response = await axios.get('/user');
//     user.value = response.data;
//   } catch (error) {
//     if (error.response && error.response.status === 401) {
//       await router.push({ name: 'login' });
//     }
//   }
// });

// const changePassword = async () => {
//   try {
//     const response = await axios.put(`profile/change-password/${user.value.id}`, {
//       current_password: user.value.password,
//       new_password: newPassword.value,
//       password_confirmation: passwordConfirmation.value,
//     });
    
//     router.push({ name: 'profile', query: { successMessage: response.data.message } });

//   } catch (error) {
//     if (error.response && error.response.status === 422) {
//       errorList.value = error.response.data.errors;
//       console.error('Validation errors:', errorList.value);
//     } else {
//       console.error('Error updating password:', error.message);
//     }
//   }
// };

// const updateExit = () => {
//   router.push('/profile');
// };

</script>

<template>
  <div class="container mt-5" v-if="user">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Change Password</h4>
      </div>
      <div class="card-body">
        <ul class="alert alert-danger" v-if="Object.keys(errorList).length > 0">
          <li class="mb-0 ms-3" v-for="(error, index) in errorList" :key="index">
            {{ Array.isArray(error) ? error[0] : error }}
          </li>
        </ul>
        <div class="mb-3">
          <label for="currentPassword">Current Password</label>
          <input type="password" v-model="currentPassword" class="form-control" />
        </div>
        <div class="mb-3">
          <label for="newPassword">New Password</label>
          <input type="password" v-model="newPassword" class="form-control" />
        </div>
        <div class="mb-3">
          <label for="confirmPassword">Confirm New Password</label>
          <input type="password" v-model="newPasswordConfirmation" class="form-control" />
        </div>
        <div class="mb-3">
          <button type="button" @click="changePassword" class="btn btn-primary">Change Password</button>
          <button type="button" @click="updateExit" class="btn btn-danger">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  @import './ChangePassword.scss';
</style>