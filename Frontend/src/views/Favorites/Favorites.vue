<template>
  <div class="header">
    <h1>Favorites</h1>
  </div>
  <div class="container">
    <div v-if="loading" class="row">
      <div class="col-12 text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
    <div v-else>
      <div v-if="favoriteItems.length > 0" class="col-12 mb-4 text-end">
        <button class="btn btn-danger" @click="clearAllFavorites">Clear All Favorites</button>
      </div>
      <div v-else class="col-12 text-center">
        <img alt="no favorites" class="no_favorites_img" src="/no_favorite.png">
        <h2>There's no favorites here, try
          <router-link class="toProducts" to="/products">browsing</router-link>
          for something.
        </h2>
      </div>
      <div class="row">
        <div v-for="item in favoriteItems" :key="item.item.id" class="col-auto mb-4">
          <div class="card">
            <router-link :to="{ path: '/product/' + item.item.id }" class="card-link">
              <div class="img-container">
                <img v-if="item.item.img" :src="getImageUrl(item.item.img)" alt="Item Image" class="img">
              </div>
              <div class="card-body">
                <button class="badge badge-pill badge-secondary">{{ item.item.category_name }}</button>
                <h5 class="card-title">{{ truncateName(item.item.name) }}</h5>
                <h5 class="card-title">{{ item.item.price }}€</h5>
              </div>
            </router-link>
            <div class="button-container">
              <button v-if="item.isInCart" class="btn" @click="removeFromCart(item.item.id)">
                <i class="bi bi-cart"></i>
                Remove
              </button>
              <button v-else class="btn" @click="addToCart(item.item.id)">
                <i class="bi bi-cart"></i>
                Cart
              </button>
              <button v-if="item.isFavorite" class="btn" @click="removeFromFavoritesByItemId(item.item.id)">
                <i class="bi bi-star-fill"></i>
                Remove
              </button>
              <button v-else class="btn" @click="addToFavorites(item.item.id)">
                <i class="bi bi-star"></i>
                Favorite
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      favoriteItems: [],
      loggedInUserId: null,
      loading: true,
      user: null,
      categories: []
    };
  },
  async mounted() {
    await this.fetchLoggedInUserId();
    await this.fetchCategories();
    this.fetchFavoriteItems();
    this.fetchUserData();
  },
  methods: {
    async fetchLoggedInUserId() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/user');
        this.loggedInUserId = response.data.id;
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    },

    async fetchUserData() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/user');
        this.user = response.data;
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    },

    async fetchCategories() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/categories');
        this.categories = response.data.categories;
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },

    fetchFavoriteItems() {
      axios
          .get(`http://127.0.0.1:8000/api/favorites/user/${this.loggedInUserId}`)
          .then((response) => {
            const categoryMap = {};
            this.categories.forEach(category => {
              categoryMap[category.id] = category.category_name;
            });

            this.favoriteItems = response.data.favorites || [];
            this.favoriteItems.forEach(item => {
              item.isFavorite = true;
              item.isInCart = item.isInCart || false;
              item.item.category_name = categoryMap[item.item.categories_id] || 'Unknown';
            });
          })
          .catch((error) => {
            console.error('Error fetching favorite items:', error);
          })
          .finally(() => {
            this.loading = false;
          });
    },

    getImageUrl(image) {
      return `http://127.0.0.1:8000/storage/uploads/${image}`;
    },

    async addToCart(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.post('http://127.0.0.1:8000/api/cart', {user_id: userId, item_id: itemId});
        console.log(response.data.message);
        this.updateItemCartStatus(itemId, true);
        document.dispatchEvent(new CustomEvent('cart-updated'));
      } catch (error) {
        if (error.response && error.response.status === 409) {
          console.error('Item already in cart');
        } else {
          console.error('Error adding to cart:', error);
        }
      }
    },

    async removeFromCart(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.delete(`http://localhost:8000/api/cart/item/${itemId}-${userId}`);
        console.log(response.data.message);
        this.updateItemCartStatus(itemId, false);
        document.dispatchEvent(new CustomEvent('cart-updated'));
      } catch (error) {
        if (error.response) {
          console.error('Error removing from cart:', error.response.data.message);
          console.error('Detailed error:', error.response.data.error);
        } else {
          console.error('Error removing from cart:', error.message);
        }
      }
    },

    async addToFavorites(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.post('http://127.0.0.1:8000/api/favorites', {user_id: userId, item_id: itemId});
        console.log(response.data.message);
        this.updateItemFavoriteStatus(itemId, true);
        document.dispatchEvent(new CustomEvent('favorites-updated'));
      } catch (error) {
        if (error.response && error.response.status === 409) {
          console.error('Item already in favorites');
        } else {
          console.error('Error adding to favorites:', error);
        }
      }
    },

    async removeFromFavoritesByItemId(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.delete(`http://localhost:8000/api/favorites/item/${itemId}-${userId}`);
        console.log(response.data.message);
        this.favoriteItems = this.favoriteItems.filter(item => item.item.id !== itemId);
        document.dispatchEvent(new CustomEvent('favorites-updated'));
        if (this.favoriteItems.length === 0) {
          this.loading = true;
          await this.fetchFavoriteItems();
          this.loading = false;
        }
      } catch (error) {
        if (error.response) {
          console.error('Error removing from favorites:', error.response.data.message);
          console.error('Detailed error:', error.response.data.error);
        } else {
          console.error('Error removing from favorites:', error.message);
        }
      }
    },

    clearAllFavorites() {
      axios.delete(`http://127.0.0.1:8000/api/favorites/user/${this.loggedInUserId}/clear`)
          .then(response => {
            console.log(response.data.message);
            this.favoriteItems = [];
            document.dispatchEvent(new CustomEvent('favorites-updated'));
          })
          .catch(error => {
            console.error('Error clearing all favorites:', error);
          });
    },

    updateItemCartStatus(itemId, isInCart) {
      const item = this.favoriteItems.find(item => item.item.id === itemId);
      if (item) {
        item.isInCart = isInCart;
      }
    },

    updateItemFavoriteStatus(itemId, isFavorite) {
      const item = this.favoriteItems.find(item => item.item.id === itemId);
      if (item) {
        item.isFavorite = isFavorite;
      }
    },

    truncateName(name) {
      const maxLength = 33;
      if (name.length > maxLength) {
        return name.substring(0, maxLength) + '...';
      }
      return name;
    },
  },
};
</script>


<style scoped>
.header {
  text-align: center;
  margin: 20px 0;
}

.container {
  padding: 0;
}

.toProducts {
  color: #000;
}

.row {
  margin-top: 40px;
}

.img {
  max-width: 215px;
  max-height: 160px;
}

.card-link {
  text-decoration: none;
  color: inherit;
}

.button-container {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  text-align: center;
  opacity: 1;
  z-index: 100;
}

.badge {
  color: #000;
  background-color: #f3f3f3;
  border: none;
  border-width: 1px;
  text-decoration: none;
}

.btn {
  margin: 0 5px;
}

.no_favorites_img {
  width: 300px;
  height: 300px;
  margin: 0 auto;
  user-select: none;
}

@media screen and (min-width: 768px) {
  .card:hover .button-container {
    opacity: 1;
  }

  .card {
    transition: transform 0.3s;

    &:hover {
      transform: scale(1.1);
    }
  }

  .no_favorites_img {
    width: 400px;
    height: 400px;
  }
}

.spinner-border {
  width: 3rem;
  height: 3rem;
  margin-top: 50px;
}

.header {
  text-align: center;
  margin: 20px 0 20px 0;
}

.container {
  padding: 0;

  .row {
    margin-top: 40px;
  }
}

.card {
  overflow: hidden;
  width: 220px;
  height: 340px;
  border: 1px solid #ccc;

  .img-container {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 160px;
    margin: 5px;
  }

  .card-body {
    padding-top: 0;
  }
}

.card-link {
  text-decoration: none;
  color: inherit;
}

.button-container {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  text-align: center;
  opacity: 1;
  z-index: 100;
}

.badge {
  color: #000;
  background-color: #f3f3f3;
  border: none;
  border-width: 1px;
  text-decoration: none;
}

@media screen and (max-width: 575px) {
  .row {
    margin: 0;
  }
}

@media screen and (max-width: 767px) {
  .row {
    justify-content: center;
  }
}

@media screen and (min-width: 768px) {
  .card:hover .button-container {
    opacity: 1;
  }

  .card {
    transition: transform 0.3s;

    &:hover {
      transform: scale(1.1);
    }
  }
}
</style>