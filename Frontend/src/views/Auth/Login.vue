<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const form = ref({
  email: '',
  password: '',
});

const getToken = async () => {
  await axios.get('/sanctum/csrf-cookie');
}

const loginUser = async () => {
  await getToken();
  await axios.post('/login', { 
    email: form.value.email, 
    password: form.value.password
  });
  router.push('/');
}
</script>

<template>
    <div class="container">
      <div class="card">
        <div class="card-header">
          <h4>Login</h4>
        </div>
        <div class="card-body">
          <div v-if="successMessage" class="alert alert-success">
            {{ successMessage }}
        </div>
            <!-- <ul class="alert alert-danger" v-if="Object.keys(errorList).length > 0">
                <li class="mb-0 ms-3" v-for="(error, index) in errorList" :key="index">
                {{ error[0] }}
                </li>
            </ul>  -->
            <form @submit.prevent="loginUser">
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
              <p>Not a member? <a href="#!">Register</a></p>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
          </form>
        </div>
      </div>
    </div>
</template>