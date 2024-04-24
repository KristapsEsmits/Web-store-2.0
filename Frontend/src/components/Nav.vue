<script>
import axios from 'axios';

export default {
  name: 'Register',
  data() {
    return {
      isLoggedIn: false,
      user: null,
      categories: [],
    };
  },

  async created() {
    this.isLoggedIn = await this.isUserLoggedIn();
    if (this.isLoggedIn) {
      this.fetchUserData();
    }
  },
  methods: {
    async isUserLoggedIn() {
      return !!localStorage.getItem('access_token');
    },

    async fetchUserData() {
      try {
        const response = await axios.get('/user');
        this.user = response.data;
      } catch (error) {
        console.error('Error fetching user data:', error);
        // Handle error accordingly
      }
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

    getCategories() {
      axios.get('/api/categories').then((res) => {
        this.categories = res.data.categories;
      });
    },
  },

  mounted() {
    this.getCategories();
  },
};
</script>

<template>
  <header class="bar">
    <div class="wrapper">
      <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
          <RouterLink class="navbar-brand" to="/">
            <img alt="Logo" src="/favicon.ico" style="height: 30px; width: 30px;">
            Frenko
          </RouterLink>
          <button aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
                  class="navbar-toggler" data-bs-target="#navbarSupportedContent" data-bs-toggle="collapse"
                  type="button">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div id="navbarSupportedContent" class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <RouterLink class="nav-link" to="/">Home</RouterLink>
              </li>
              <li class="nav-item dropdown">
                <a id="navbarDropdown" aria-expanded="false" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                   href="#"
                   role="button">
                  Products
                </a>
                <ul aria-labelledby="navbarDropdown" class="dropdown-menu">
                  <RouterLink class="nav-link" to="/products">All Products</RouterLink>
                  <li v-for="category in categories" :key="category.id">
                    <RouterLink :to="'/products/' + category.id" class="dropdown-item">{{
                        category.category_name
                      }}
                    </RouterLink>
                  </li>
                </ul>
              </li>
              <li class="nav-item">
                <RouterLink class="nav-link" to="/brands">Brands</RouterLink>
              </li>
            </ul>

            <ul class="navbar-nav ml-auto">
              <li class="nav-item">
                <button class="btn">
                  <i class="bi bi-star"></i>
                  Favorites
                </button>
              </li>

              <li class="nav-item">
                <button class="btn">
                  <i class="bi bi-cart"></i>
                  Cart
                </button>
              </li>

              <li v-if="!isLoggedIn" class="nav-item">
                <RouterLink class="nav-link" to="/login">Login</RouterLink>
              </li>

              <li v-if="isLoggedIn" class="nav-item dropdown">
                <a aria-expanded="false" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"
                   role="button">
                  <i class="bi bi-person-fill"></i>
                  {{ user?.name }} {{ user?.surname }}
                </a>
                <ul aria-labelledby="navbarDropdown" class="dropdown-menu">
                  <li>
                    <RouterLink class="dropdown-item" to="/profile">My Profile</RouterLink>
                  </li>
                  <li class="nav-item">
                    <RouterLink class="dropdown-item" to="/admin">Admin</RouterLink>
                  </li>
                  <li class="dropdown-divider"></li>
                  <li class="nav-item">
                    <button class="dropdown-item" @click="logout">
                      <i class="bi bi-box-arrow-right"></i>
                      Logout
                    </button>
                  </li>
                </ul>
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
