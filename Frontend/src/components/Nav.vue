<script>
import axios from 'axios';
export default {
    name: 'Register',
    methods: {
        async logout() {
          await axios.get('/logout').then((res) => {
            localStorage.removeItem('access_token');
            this.$router.push('/login');
          }).catch((error) => {
            console.log(error);
          });
        },
    },
  };
</script>
<template>
    <header>
    <div class="wrapper">
      <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
          <RouterLink to="/" class="navbar-brand">Nosaukums</RouterLink>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                  data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                  aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          
          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <RouterLink to="/" class="nav-link">Home</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink to="/test" class="nav-link">Test</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink to="/login" class="nav-link">Login</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink to="/register" class="nav-link">Register</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink to="/profile" class="nav-link">Profile</RouterLink>
              </li>
              <li class="nav-item">
                <button class="nav-link" @click="logout">Logout</button>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </header>
</template>