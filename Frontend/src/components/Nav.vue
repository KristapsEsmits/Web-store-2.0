<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      isLoggedIn: false,
    };
  },

  async created() {
    this.isLoggedIn = await this.isUserLoggedIn();
  },

  methods: {
    async isUserLoggedIn() {
      return !!localStorage.getItem('access_token');
    },
    
    async logout() {
      try {
        await axios.get('/logout');
        localStorage.removeItem('access_token');
        this.isLoggedIn = false; 
        this.$router.push('/login');
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>

<template>
    <header class="bar">
    <div class="wrapper">
      <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
          <RouterLink to="/" class="navbar-brand">
            <img src="\favicon.ico" alt="Logo" style="height: 30px; width: 30px;">
            Frenko
          </RouterLink>
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
                <RouterLink to="/products" class="nav-link">Products</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink to="/brands" class="nav-link">Brands</RouterLink>
              </li>
            </ul>
            <ul class="navbar-nav ml-auto">
              <li class="nav-item" v-if="!isLoggedIn">
                <RouterLink to="/login" class="nav-link">Login</RouterLink>
              </li>
              <li class="nav-item" v-if="!isLoggedIn">
                <RouterLink to="/register" class="nav-link">Register</RouterLink>
              </li>
              <li class="nav-item" v-if="isLoggedIn">
                <RouterLink to="/admin" class="nav-link">Admin</RouterLink>
              </li>
              <li class="nav-item" v-if="isLoggedIn">
                <RouterLink to="/profile" class="nav-link">Profile</RouterLink>
              </li>
              <li class="nav-item" v-if="isLoggedIn">
                <button class="nav-link" @click="logout">Logout</button>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </div>
  </header>
</template>

<style scoped>
  .bar {
    background-color: #f8f9fa;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 100;
  }

</style>
