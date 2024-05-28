<template>
  <h1 class="title">Your Favorite Items</h1>
  <div class="container">
    <div class="row">
      <div class="col-12 mb-4 text-end" v-if="favoriteItems.length > 0">
        <button class="btn btn-danger" @click="clearAllFavorites">Clear All Favorites</button>
      </div>
      <div v-else class="col-12 text-center">
        <p>There's nothing here, try browsing for something.</p>
      </div>
      <div v-for="item in favoriteItems" :key="item.item.id" class="col-auto mb-4">
        <div class="card">
          <router-link :to="{ path: '/product/' + item.item.id }" class="card-link">
            <div class="img-container">
              <img v-if="item.item.img" :src="getImageUrl(item.item.img)" alt="Item Image" class="img">
            </div>
            <div class="card-body">
              <button class="badge badge-pill badge-secondary">{{ item.item.category_name }}</button>
              <h5 class="card-title">{{ item.item.name }}</h5>
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
            <button v-if="item.isFavorite" class="btn" @click="removeFromFavorites(item.item.id)">
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
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      favoriteItems: [],
      loggedInUserId: null,
    };
  },
  async mounted() {
    await this.fetchLoggedInUserId();
    this.fetchFavoriteItems();
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
    fetchFavoriteItems() {
      axios
        .get(`http://127.0.0.1:8000/api/favorites/user/${this.loggedInUserId}`)
        .then((response) => {
          this.favoriteItems = response.data.favorites || [];
          this.favoriteItems.forEach(item => {
            item.isFavorite = true;
            item.isInCart = false;
          });
        })
        .catch((error) => {
          console.error('Error fetching favorite items:', error);
        });
    },
    getImageUrl(image) {
      return `http://127.0.0.1:8000/storage/uploads/${image}`;
    },
    addToCart(itemId) {
      axios.post('http://127.0.0.1:8000/api/cart', { user_id: this.loggedInUserId, item_id: itemId })
        .then(response => {
          const item = this.favoriteItems.find(fav => fav.item.id === itemId);
          if (item) {
            item.isInCart = true;
          }
        })
        .catch(error => {
          console.error('Error adding to cart:', error);
        });
    },
    removeFromCart(itemId) {
      axios.delete(`http://127.0.0.1:8000/api/cart/${itemId}`, { data: { user_id: this.loggedInUserId } })
        .then(response => {
          const item = this.favoriteItems.find(fav => fav.item.id === itemId);
          if (item) {
            item.isInCart = false;
          }
        })
        .catch(error => {
          console.error('Error removing from cart:', error);
        });
    },
    addToFavorites(itemId) {
      axios.post('http://127.0.0.1:8000/api/favorites', { user_id: this.loggedInUserId, item_id: itemId })
        .then(response => {
          const item = this.favoriteItems.find(fav => fav.item.id === itemId);
          if (item) {
            item.isFavorite = true;
          }
        })
        .catch(error => {
          console.error('Error adding to favorites:', error);
        });
    },
    removeFromFavorites(itemId) {
      axios.delete('http://127.0.0.1:8000/api/favorites/item', { data: { user_id: this.loggedInUserId, item_id: itemId } })
        .then(response => {
          this.favoriteItems = this.favoriteItems.filter(item => item.item.id !== itemId);
        })
        .catch(error => {
          console.error('Error removing favorite item:', error);
        });
    },
    clearAllFavorites() {
      axios.delete(`http://127.0.0.1:8000/api/favorites/user/${this.loggedInUserId}/clear`)
        .then(response => {
          console.log(response.data.message);
          this.favoriteItems = [];
        })
        .catch(error => {
          console.error('Error clearing all favorites:', error);
        });
    },
  },
};
</script>

<style scoped>
.container {
  display: flex;
  justify-content: center;
  justify-self: center;
  max-width: auto !important;
  padding: 0;
}

.card {
  overflow: hidden;
  width: 220px;
  height: 340px;
  border: 1px solid #ccc;
}

.img-container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 160px;
  margin: 5px;
}

.img {
  max-width: 215px;
  max-height: 160px;
}

.card-body {
  padding-top: 0;
  text-align: center;
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

.title {
  text-align: center;
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
