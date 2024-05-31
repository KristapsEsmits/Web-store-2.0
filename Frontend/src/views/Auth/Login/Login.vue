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

const loginUser = async () => {
  try {
    const res = await axios.post('/login', {
      email: form.value.email,
      password: form.value.password,
    });

    localStorage.setItem('access_token', res.data.access_token);
    localStorage.setItem('user', JSON.stringify(res.data.user));
    axios.defaults.headers.authorization = `Bearer ${res.data.access_token}`;

    document.dispatchEvent(new Event('login'));

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
      <img alt="Logo" src="/favicon.ico" style="height: 60px; width: 60px;">
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
          <input id="floatingInput" v-model="form.email" class="form-control" placeholder="name@example.com"
                 type="email">
          <label for="floatingInput">Email address</label>
        </div>
        <div class="form-floating mb-2">
          <input id="floatingInput" v-model="form.password" class="form-control" placeholder="Password"
                 type="password">
          <label for="floatingInput">Password</label>
        </div>
        <div class="text mb-3">
          <a href="#!">Forgot password?</a>
        </div>
        <button class="btn btn-primary loginbtn" type="button" @click="loginUser">Login</button>
      </form>
    </div>
  </div>

  <div class="noMember">
    <p>Not a member?
      <RouterLink to="/register">Register</RouterLink>
    </p>
  </div>
</template>

<style scoped>
@import './Login.scss';
</style>
