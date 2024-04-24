<script setup>
import {ref} from 'vue';
import axios from 'axios';
import {useRouter} from 'vue-router';

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
    if (error.response && error.response.status === 401) {
      errorMessage.value = 'Incorrect email or password. Please try again';
    } else {
      errorMessage.value = 'An unexpected error occurred. Please try again later.';
    }
  }
};

</script>

<template>
    <div class="attribute-container">
      <div class="logo">
        <img src="\favicon.ico" alt="Logo" style="height: 60px; width: 60px;">
      </div>
      <div class="logoText">
        <h1>Sign in to Frenko</h1>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div v-if="errorMessage" class="alert alert-danger">
          {{ errorMessage }}
        </div>
        <form>
          <div class="form-floating mb-3">
            <input type="email" id="floatingInput" class="form-control" v-model="form.email"
                   placeholder="name@example.com">
            <label for="floatingInput">Email address</label>
          </div>
          <div class="form-floating mb-2">
            <input type="password" id="floatingInput" class="form-control" v-model="form.password"
                   placeholder="Password">
            <label for="floatingInput">Password</label>
          </div>
          <div class="text mb-3">
            <a href="#!">Forgot password?</a>
          </div>
          <button type="button" @click="loginUser" class="btn btn-primary loginbtn">Login</button>
        </form>
      </div>
    </div>

    <div class="noMember">
      <p>Not a member? <RouterLink to="/register">Register</RouterLink></p>
    </div>
</template>

<style scoped>
@import './Login.scss';
</style>
