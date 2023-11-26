<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const form = ref({
  email: '',
  password: '',
});

const errorMessage = ref(null);
const successMessage = ref(null);

const loginUser = async () => {
  try {
    const res = await axios.post('/login', {
      email: form.value.email,
      password: form.value.password,
    });

    localStorage.setItem('access_token', res.data.access_token);
    axios.defaults.headers.authorization = `Bearer ${res.data.access_token}`;
    router.push('/');
  } catch (error) {
    console.error(error);

    if (error.response && error.response.status === 401) {
      errorMessage.value = 'Wrong email or password. Please try again.';
    } else {
      errorMessage.value = 'An unexpected error occurred. Please try again later.';
    }
  }
};
</script>

<template>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <h4>Login</h4>
      </div>
      <div class="card-body">
        <div v-if="errorMessage" class="alert alert-danger">
          {{ errorMessage }}
        </div>
        <div v-if="successMessage" class="alert alert-success">
          {{ successMessage }}
        </div>
        <form>
          <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" v-model="form.email" placeholder="Enter email">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" v-model="form.password" placeholder="Password">
          </div>
          <div class="form-group form-check">
            <input type="checkbox" class="form-check-input">
            <label class="form-check-label" for="rememberMe">Remember Me</label>
          </div>
          <div>
            <a href="#!">Forgot password?</a>
          </div>
          <div>
            <p>Not a member? <RouterLink to="/register">Register</RouterLink></p>
          </div>
          <button type="button" @click="loginUser" class="btn btn-primary">Login</button>
        </form>
      </div>
    </div>
  </div>
</template>