<script>
import axios from 'axios';

export default {
  name: 'NavBar',
  data() {
    return {
      isLoggedIn: false,
      user: null,
      categories: [],
      favoritesCount: 0,
      cartCount: 0,
    };
  },

  async created() {
    this.isLoggedIn = await this.isUserLoggedIn();
    if (this.isLoggedIn) {
      await this.fetchUserData();
      await this.fetchFavoritesCount();
      await this.fetchCartCount();
    }
    this.getCategories();
  },

  computed: {
    isAdmin() {
      console.log('User data:', this.user); // Log user data for debugging
      return this.user && this.user.admin === 1; // Check if the user is an admin
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
        console.log('Fetched user data:', this.user); // Log fetched user data for debugging
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    },

    async fetchFavoritesCount() {
      if (this.user && this.user.id) {
        try {
          const response = await axios.get(`/user/${this.user.id}/favorites-count`);
          this.favoritesCount = response.data.count;
        } catch (error) {
          console.error('Error fetching favorites count:', error);
        }
      }
    },

    async fetchCartCount() {
      if (this.user && this.user.id) {
        try {
          const response = await axios.get(`/cart/user/${this.user.id}/count`);
          this.cartCount = response.data.count;
        } catch (error) {
          console.error('Error fetching cart count:', error);
        }
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
      axios.get('/categories').then((res) => {
        this.categories = res.data.categories;
      });
    },

    navigateToCart() {
      if (!this.isLoggedIn) {
        this.$router.push('/login');
      } else {
        this.$router.push('/cart');
      }
    },

    navigateToFavorites() {
      if (!this.isLoggedIn) {
        this.$router.push('/login');
      } else {
        this.$router.push('/favorites');
      }
    }
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
                </ul>
              </li>
              <li class="nav-item">
                <RouterLink class="nav-link" to="/brands">Brands</RouterLink>
              </li>
            </ul>

            <ul class="navbar-nav ml-auto">
              <li class="nav-item">
                <a class="nav-link" @click.prevent="navigateToFavorites">
                  <i class="bi bi-star"></i>
                  Favorites <span v-if="favoritesCount">({{ favoritesCount }})</span>
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link" @click.prevent="navigateToCart">
                  <i class="bi bi-cart"></i>
                  Cart <span v-if="cartCount">({{ cartCount }})</span>
                </a>
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
                  <li v-if="isAdmin" class="nav-item">
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
