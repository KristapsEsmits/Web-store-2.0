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

            <form id="search-form" ref="searchForm" class="d-flex position-relative" @submit.prevent>
              <input v-model="searchText" aria-label="Search" class="form-control me-2" placeholder="Search"
                     type="search" @focus="onFocus" @input="searchItems">
              <div v-if="isSearchActive" class="dropdown show">
                <ul class="dropdown-menu show">
                  <li v-if="isLoading" class="dropdown-item">Loading...</li>
                  <li v-else-if="searchResults.length === 0" class="dropdown-item">Nothing found!</li>
                  <li v-for="item in searchResults" v-else :key="item.id" class="d-flex align-items-center">
                    <img :src="getImageUrl(item.img)" alt="" class="item-image me-2">
                    <RouterLink :to="'/product/' + item.id" class="dropdown-item" @click="clearSearch">
                      {{ truncatedName(item.name) }}
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
      isLoading: false,
      allItems: []
    };
  },

  async created() {
    await this.checkLoginState();
    document.addEventListener('logout', this.onLogout);
    document.addEventListener('login', this.onLogin);
    document.addEventListener('favorites-updated', this.updateFavoritesCount);
    document.addEventListener('cart-updated', this.updateCartCount);
    document.addEventListener('click', this.handleClickOutside);
  },

  beforeUnmount() {
    document.removeEventListener('login', this.onLogin);
    document.removeEventListener('logout', this.onLogout);
    document.removeEventListener('favorites-updated', this.updateFavoritesCount);
    document.removeEventListener('cart-updated', this.updateCartCount);
    document.removeEventListener('click', this.handleClickOutside);
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

    getImageUrl(image) {
      return `http://localhost:8000/storage/uploads/${image}`;
    },

    truncatedName(name) {
      const screenWidth = window.innerWidth;
      let maxLength;

      if (screenWidth < 576) {
        maxLength = 15;
      } else if (screenWidth < 768) {
        maxLength = 20;
      } else if (screenWidth < 375) {
        maxLength = 40;
      }


      if (name.length > maxLength) {
        return name.substring(0, maxLength) + '...';
      }
      return name;
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

    async fetchAllItems() {
      try {
        this.isLoading = true;
        const response = await axios.get('http://127.0.0.1:8000/api/items');
        this.allItems = response.data.items;
        this.searchResults = this.allItems.slice(0, 4);
        this.isSearchActive = true;
      } catch (error) {
        console.error('Error fetching all items:', error);
      } finally {
        this.isLoading = false;
      }
    },

    searchItems() {
      if (this.searchText.trim().length > 0) {
        this.searchResults = this.allItems.filter(item => item.name.toLowerCase().includes(this.searchText.toLowerCase())).slice(0, 4);
        this.isSearchActive = true;
      } else {
        this.searchResults = this.allItems.slice(0, 4);
        this.isSearchActive = true;
      }
    },

    clearSearch() {
      this.searchText = '';
      this.searchResults = this.allItems.slice(0, 4);
      this.isSearchActive = false;
    },

    onFocus() {
      this.isSearchActive = true;
      if (!this.allItems.length) {
        this.fetchAllItems();
      } else {
        this.searchResults = this.allItems.slice(0, 4);
      }
    },

    handleClickOutside(event) {
      if (this.isSearchActive && !this.$refs.searchForm.contains(event.target)) {
        this.isSearchActive = false;
      }
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
    },
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
  position: absolute;
  top: 100%;
  right: 0;
  left: auto;
  width: auto;
  z-index: 1000;
  min-width: 300px;
}

.dropdown-item {
  cursor: pointer;
  display: flex;
  align-items: center;
}

.dropdown-item:hover {
  background-color: #f1f1f1;
}

.item-image {
  width: 80px;
  height: 50px;
  object-fit: scale-down;
  margin-right: 10px;
}

input.form-control:hover, input.form-control:focus {
  box-shadow: none;
  border-color: #ced4da;
}
</style>

