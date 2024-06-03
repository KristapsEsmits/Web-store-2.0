<template>
  <header class="bar">
    <div class="wrapper">
      <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
          <RouterLink class="navbar-brand" to="/" @click="collapseNavbar">
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
                <RouterLink class="nav-link" to="/" @click="collapseNavbar">Home</RouterLink>
              </li>
              <li class="nav-item dropdown">
                <RouterLink class="nav-link" to="/products" @click="collapseNavbar">Products</RouterLink>
              </li>
              <li class="nav-item">
                <RouterLink class="nav-link" to="/brands" @click="collapseNavbar">Brands</RouterLink>
              </li>
            </ul>

            <form id="search-form" class="d-flex position-relative" @submit.prevent>
              <input v-model="searchText" aria-label="Search" class="form-control me-2" placeholder="Search"
                     type="search" @input="searchItems">
              <div v-if="isSearchActive" class="dropdown">
                <ul class="dropdown-menu show">
                  <li v-for="item in searchResults" :key="item.id">
                    <RouterLink :to="'/product/' + item.id" class="dropdown-item" @click="clearSearch">
                      {{ item.name }}
                    </RouterLink>
                  </li>
                </ul>
              </div>
            </form>

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

              <li v-if="!isLoggedIn" id="login-link" class="nav-item">
                <RouterLink class="nav-link" to="/login" @click="collapseNavbar">Login</RouterLink>
              </li>

              <li v-if="isLoggedIn" id="user-dropdown" class="nav-item dropdown">
                <a aria-expanded="false" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"
                   role="button">
                  <i class="bi bi-person-fill"></i>
                  {{ user?.name }} {{ user?.surname }}
                </a>
                <ul aria-labelledby="navbarDropdown" class="dropdown-menu">
                  <li>
                    <RouterLink class="dropdown-item" to="/profile" @click="collapseNavbar">My Profile</RouterLink>
                  </li>
                  <li v-if="isAdmin" class="nav-item">
                    <RouterLink class="dropdown-item" to="/admin" @click="collapseNavbar">Admin</RouterLink>
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
      searchText: '',
      searchResults: [],
      isSearchActive: false,
    };
  },

  async created() {
    await this.checkLoginState();
    document.addEventListener('logout', this.onLogout);
    document.addEventListener('login', this.onLogin);
    document.addEventListener('favorites-updated', this.updateFavoritesCount);
    document.addEventListener('cart-updated', this.updateCartCount);
  },

  beforeUnmount() {
    document.removeEventListener('login', this.onLogin);
    document.removeEventListener('logout', this.onLogout);
    document.removeEventListener('favorites-updated', this.updateFavoritesCount);
    document.removeEventListener('cart-updated', this.updateCartCount);
  },

  computed: {
    isAdmin() {
      return this.user && this.user.admin === 1;
    }
  },

  methods: {
    async checkLoginState() {
      this.isLoggedIn = await this.isUserLoggedIn();
      if (this.isLoggedIn) {
        await this.fetchUserData();
        await this.fetchUserCounts();
      }
    },

    async isUserLoggedIn() {
      return !!localStorage.getItem('access_token');
    },

    async fetchUserData() {
      try {
        const response = await axios.get('/user');
        this.user = response.data;
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    },

    async fetchUserCounts() {
      if (this.user && this.user.id) {
        try {
          const response = await axios.get(`/user/${this.user.id}/counts`);
          this.favoritesCount = response.data.favoritesCount;
          this.cartCount = response.data.cartCount;
        } catch (error) {
          console.error('Error fetching user counts:', error);
        }
      }
    },

    async logout() {
      try {
        await axios.get('/logout');
        localStorage.removeItem('access_token');
        this.isLoggedIn = false;
        this.user = null;
        this.favoritesCount = 0;
        this.cartCount = 0;
        document.dispatchEvent(new Event('logout'));
        this.$router.push('/login');
      } catch (error) {
        console.log(error);
      }
    },

    navigateToCart() {
      if (!this.isLoggedIn) {
        this.$router.push('/login');
      } else {
        this.$router.push('/cart');
      }
      this.collapseNavbar();
    },

    navigateToFavorites() {
      if (!this.isLoggedIn) {
        this.$router.push('/login');
      } else {
        this.$router.push('/favorites');
      }
      this.collapseNavbar();
    },

    async searchItems() {
      if (this.searchText.trim().length > 0) {
        try {
          console.log('Searching for:', this.searchText);
          const response = await axios.get('http://127.0.0.1:8000/api/items/search', {
            params: {
              name: this.searchText,
            },
          });
          console.log('Search response:', response.data);
          this.searchResults = response.data.items.slice(0, 4);
          this.isSearchActive = true;
        } catch (error) {
          console.error('Error searching items:', error);
          if (error.response) {
            console.error('Error response:', error.response.data);
          }
        }
      } else {
        this.isSearchActive = false;
        this.searchResults = [];
      }
    },

    clearSearch() {
      this.searchText = '';
      this.isSearchActive = false;
      this.searchResults = [];
    },

    collapseNavbar() {
      const navbar = document.getElementById('navbarSupportedContent');
      if (navbar.classList.contains('show')) {
        this.$nextTick(() => {
          const button = document.querySelector('.navbar-toggler');
          if (button) {
            button.click();
          }
        });
      }
    },

    async onLogin() {
      this.isLoggedIn = true;
      await this.fetchUserData();
      await this.fetchUserCounts();
    },

    async onLogout() {
      this.isLoggedIn = false;
      this.user = null;
      this.favoritesCount = 0;
      this.cartCount = 0;
    },

    async updateFavoritesCount() {
      if (this.user && this.user.id) {
        try {
          const response = await axios.get(`/user/${this.user.id}/counts`);
          this.favoritesCount = response.data.favoritesCount;
        } catch (error) {
          console.error('Error updating favorites count:', error);
        }
      }
    },

    async updateCartCount() {
      if (this.user && this.user.id) {
        try {
          const response = await axios.get(`/user/${this.user.id}/counts`);
          this.cartCount = response.data.cartCount;
        } catch (error) {
          console.error('Error updating cart count:', error);
        }
      }
    }
  },
};
</script>

<style scoped>
.bar {
  background-color: #f8f9fa;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 100;
}

.position-relative {
  position: relative;
}

.dropdown-menu.show {
  display: block;
  max-height: 200px;
  overflow-y: auto;
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  z-index: 1000;
}

.dropdown-item {
  cursor: pointer;
}

.dropdown-item:hover {
  background-color: #f1f1f1;
}
</style>
