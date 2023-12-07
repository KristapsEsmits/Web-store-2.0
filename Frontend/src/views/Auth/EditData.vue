<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import router from '../../router';

const user = ref(null);

onMounted(async () => {
  try {
    const response = await axios.get('/user');
    user.value = response.data;
  } catch (error) {
    if (error.response && error.response.status === 401) {
      // Redirect to login page when authentication fails
      await router.push({ name: 'login' });
    }
  }
});

const updateUser = async () => {
  try {
    const response = await axios.put(`/profile/edit/${user.value.id}`, {
      name: user.value.name,
      surname: user.value.surname,
      phone: user.value.phone,
      email: user.value.email,
    });

    // Optionally, you can reload the user data after the update
    const userResponse = await axios.get('/user');
    user.value = userResponse.data;

    // Handle success (e.g., show a success message)
    console.log('User updated successfully!');
    router.push('/profile');
    alert(response.data.message);
  } catch (error) {
    if (error.response && error.response.status === 422) {
      // Handle validation errors, if any
      console.error('Validation errors:', error.response.data.errors);
    } else {
      // Handle other errors
      console.error('Error updating user:', error.message);
    }
  }
};

const updateExit = () => {
  // Redirect to /profile when cancel is clicked
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
