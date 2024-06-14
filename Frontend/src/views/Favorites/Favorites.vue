<template>
  <div class="container">
    <div v-if="loading" class="row">
      <div class="col-12 text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
    <div v-else>
      <div v-if="favoriteItems.length > 0" class="col-12 text-end">
        <div class="header">
          <h1>Favorites</h1>
        </div>
        <button class="btn btn-danger" @click="clearAllFavorites">Clear All Favorites</button>
      </div>
      <div v-else class="col-12 text-center no-favorites-center">
        <img alt="no favorites" class="no_favorites_img" src="/no_favorite.png">
        <h2>There's no favorites here, try
          <router-link class="toProducts" to="/products">browsing</router-link>
          for something.
        </h2>
      </div>
      <div v-if="favoriteItems.length > 0" class="row">
        <ul class="nav nav-tabs">
          <li v-for="(category, index) in favoriteCategories" :key="index" class="nav-item">
            <a :class="{ active: index === 0 }" :href="'#category-' + index" class="nav-link"
               data-bs-toggle="tab">{{ category.name }}</a>
          </li>
        </ul>
        <div class="tab-content">
          <div v-for="(category, index) in favoriteCategories" :id="'category-' + index" :key="index"
               :class="{ show: index === 0, active: index === 0 }" class="tab-pane fade">
            <div class="row">
              <div v-for="item in category.items" :key="item.item.id" class="col-md-4 mb-4">
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
                    <button v-else :disabled="item.item.amount === 0" class="btn"
                            @click="handleCartClick(item.item.id)">
                      <i v-if="item.item.amount !== 0" class="bi bi-cart"></i>
                      {{ item.item.amount === 0 ? 'Out of stock' : 'Cart' }}
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
            <h3 class="mt-4">Compare Specifications</h3>
            <table class="table">
              <thead>
              <tr>
                <th>Specification</th>
                <th v-for="item in category.items" :key="item.item.id">{{ truncateName(item.item.name) }}</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="specId in Object.keys(category.specifications)" :key="specId">
                <td>{{ category.specifications[specId] }}</td>
                <td v-for="item in category.items" :key="item.item.id">{{ item.specifications[specId] || 'N/A' }}</td>
              </tr>
              </tbody>
            </table>
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
      categories: [],
      favoriteCategories: []
    };
  },
  async mounted() {
    await this.fetchLoggedInUserId();
    await this.fetchCategories();
    await this.fetchFavoriteItems();
    await this.fetchUserData();
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

    async fetchFavoriteItems() {
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/favorites/user/${this.loggedInUserId}`);
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

        this.organizeFavoritesByCategory();
      } catch (error) {
        console.error('Error fetching favorite items:', error);
      } finally {
        this.loading = false;
      }
    },

    async fetchSpecifications(itemId) {
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/items/${itemId}/specifications`);
        return response.data.specifications;
      } catch (error) {
        console.error('Error fetching specifications:', error);
        return [];
      }
    },

    organizeFavoritesByCategory() {
      const categories = {};

      this.favoriteItems.forEach(item => {
        const categoryId = item.item.categories_id;
        if (!categories[categoryId]) {
          categories[categoryId] = {
            name: item.item.category_name,
            items: [],
            specifications: {}
          };
        }
        categories[categoryId].items.push(item);
      });

      const promises = Object.values(categories).map(async category => {
        for (const item of category.items) {
          const specifications = await this.fetchSpecifications(item.item.id);
          specifications.forEach(spec => {
            if (!category.specifications[spec.specification_title_id]) {
              category.specifications[spec.specification_title_id] = spec.specification_title.specification_title;
            }
            if (!item.specifications) {
              item.specifications = {};
            }
            item.specifications[spec.specification_title_id] = spec.description;
          });
        }
      });

      Promise.all(promises).then(() => {
        this.favoriteCategories = Object.values(categories);
      });
    },

    getImageUrl(image) {
      return `http://127.0.0.1:8000/storage/uploads/${image}`;
    },

    async addToCart(itemId) {
      try {
        const userId = this.user.id;
        await axios.post('http://127.0.0.1:8000/api/cart', {user_id: userId, item_id: itemId});
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

    handleCartClick(itemId) {
      if (this.user) {
        this.addToCart(itemId);
      } else {
        this.$router.push({path: '/login'});
      }
    },

    async removeFromCart(itemId) {
      try {
        const userId = this.user.id;
        await axios.delete(`http://localhost:8000/api/cart/item/${itemId}-${userId}`);
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
        await axios.post('http://127.0.0.1:8000/api/favorites', {user_id: userId, item_id: itemId});
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
        await axios.delete(`http://localhost:8000/api/favorites/item/${itemId}-${userId}`);
        this.favoriteItems = this.favoriteItems.filter(item => item.item.id !== itemId);
        this.organizeFavoritesByCategory();
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

    async clearAllFavorites() {
      try {
        await axios.delete(`http://127.0.0.1:8000/api/favorites/user/${this.loggedInUserId}/clear`);
        this.favoriteItems = [];
        this.favoriteCategories = [];
        document.dispatchEvent(new CustomEvent('favorites-updated'));
      } catch (error) {
        console.error('Error clearing all favorites:', error);
      }
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
    }
  }
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

.img {
  max-width: 215px;
  max-height: 160px;
}

.card-link {
  text-decoration: none;
  color: inherit;
}

.no-favorites-center {
  padding-top: 150px;
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
  border: 1px none;
  text-decoration: none;
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
  border: 1px none;
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

