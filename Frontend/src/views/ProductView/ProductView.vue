<template>
  <div>
    <div class="container mock-container mt100">
      <div class="image-container">
        <img :src="getImageUrl(items.img)" alt="Item Image" class="product-image">
        <p class="small-print">*The product may differ from the one shown in the picture. The picture may show parts
          that are not included in the delivery package.</p>
        <h2 class="ml-left">Description:</h2>
        <p class="description">{{ items.description }}</p>
      </div>
      <div class="details-container">
        <h5 class="product-name">{{ items.name }}</h5>
        <p class="product-id">ID: {{items.id }}</p>
        <h2 class="price">Price: {{ items.price }}€</h2>
        <p class="without-vat">Price without VAT: {{ calculatePriceWithoutVAT(items.price) }}€</p>
        <p :class="{'out-of-stock': items.amount === 0}">
          {{ items.amount === 0 ? 'Out of stock' : `Items remaining: ${items.amount}` }}
        </p>
        <div class="action-btn-container">
          <button v-if="user && items.isInCart" class="btn btn-new" @click="removeFromCart(items.id)">
            <i class="bi bi-cart"></i>
            Remove
          </button>
          <button v-else class="btn btn-new" @click="handleCartClick(items.id)" :disabled="items.amount === 0">
            {{ items.amount === 0 ? 'Out of stock' : 'Add to cart' }}
          </button>
          <button v-if="user && items.isFavorite" class="btn btn-new" @click="removeFromFavoritesByItemId(items.id)">
            <i class="bi bi-star-fill"></i>
            Remove
          </button>
          <button v-else class="btn btn-new" @click="handleFavoriteClick(items.id)">
            <i class="bi bi-star"></i>
            Favorite
          </button>
        </div>
      </div>
    </div>

    <div class="container mt40">
      <h1>Similar Products</h1>
    </div>

    <div class="container">
        <template v-if="isLoading">
          <div v-for="n in 4" :key="n" class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <SkeletonItemCard/>
          </div>
        </template>
        <template v-else>
          <div class="row">
          <div v-for="item in similarItems" :key="item.id" class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card">
              <router-link :to="{ path: '/product/' + item.id }" class="card-link">
                <div class="img-container">
                  <img v-if="item.img" :src="getImageUrl(item.img)" alt="Item Image" class="img">
                </div>
                <div class="card-body">
                  <button class="badge badge-pill badge-secondary">{{ item.category_name }}</button>
                  <h5 class="card-title">{{ truncateName(item.name) }}</h5>
                  <h5 class="card-title">{{ item.price }}€</h5>
                </div>
              </router-link>
              <div class="button-container">
                <button v-if="user && item.isInCart" class="btn" @click="removeFromCart(item.id)">
                  <i class="bi bi-cart"></i>
                  Remove
                </button>
                <button v-else class="btn" @click="handleCartClick(item.id)" :disabled="item.amount === 0">
                  <i v-if="item.amount !== 0" class="bi bi-cart"></i>
                  {{ item.amount === 0 ? 'Out of stock' : 'Cart' }}
                </button>
                <button v-if="user && item.isFavorite" class="btn" @click="removeFromFavoritesByItemId(item.id)">
                  <i class="bi bi-star-fill"></i>
                  Remove
                </button>
                <button v-else class="btn" @click="handleFavoriteClick(item.id)">
                  <i class="bi bi-star"></i>
                  Favorite
                </button>
              </div>
            </div>
          </div>
        </div>
        </template>
      </div>
    </div>
</template>

<script>
import axios from 'axios';
import SkeletonItemCard from '@/components/SkeletonItemCard.vue';

export default {
  name: 'items',
  components: {
    SkeletonItemCard,
  },
  data() {
    return {
      items: {},
      similarItems: [],
      user: null,
      isLoading: true,
    };
  },

  mounted() {
    this.getItems();
    this.fetchUserData();
  },

  watch: {
    '$route.params.id': 'getItems',
  },

  methods: {
    async getItems() {
      const itemId = this.$route.params.id;
      try {
        const response = await axios.get(`/items/${itemId}`);
        this.items = response.data.items;
        await this.fetchSimilarItems(itemId);
      } catch (error) {
        console.error('Error fetching item:', error);
      }
    },

    async fetchSimilarItems(itemId) {
      try {
        const response = await axios.get(`/items/similar/${itemId}`);
        this.similarItems = response.data.items;
      } catch (error) {
        console.error('Error fetching similar items:', error);
      } finally {
        this.isLoading = false;
      }
    },

    calculatePriceWithoutVAT(price) {
      return (price / 1.21).toFixed(2);
    },

    async fetchUserData() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/user', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('access_token')}`,
          },
        });
        this.user = response.data;
      } catch (error) {
        if (error.response && error.response.status === 401) {
          this.user = null;
        } else {
          console.error('Error fetching user data:', error);
        }
      }
    },

    async handleCartClick(itemId) {
      if (this.user) {
        await this.addToCart(itemId);
      } else {
        this.$router.push({ path: '/login' });
      }
    },

    getImageUrl(image) {
      return `http://localhost:8000/storage/uploads/${image}`;
    },

    async handleFavoriteClick(itemId) {
      if (this.user) {
        await this.addToFavorites(itemId);
      } else {
        this.$router.push({ path: '/login' });
      }
    },

    async addToCart(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.post('http://127.0.0.1:8000/api/cart', { user_id: userId, item_id: itemId });
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

    truncateName(name) {
      const maxLength = 20;
      if (name.length > maxLength) {
        return name.substring(0, maxLength) + '...';
      }
      return name;
    },

    async removeFromCart(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.delete(`http://127.0.0.1:8000/api/cart/item/${itemId}-${userId}`);
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
        const response = await axios.post('http://127.0.0.1:8000/api/favorites', { user_id: userId, item_id: itemId });
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
        this.updateItemFavoriteStatus(itemId, false);
        document.dispatchEvent(new CustomEvent('favorites-updated'));
      } catch (error) {
        if (error.response) {
          console.error('Error removing from favorites:', error.response.data.message);
          console.error('Detailed error:', error.response.data.error);
        } else {
          console.error('Error removing from favorites:', error.message);
        }
      }
    },

    updateItemFavoriteStatus(itemId, isFavorite) {
      if (this.items && this.items.id === itemId) {
        this.items.isFavorite = isFavorite;
      }
      this.similarItems = this.similarItems.map(item => {
        if (item.id === itemId) {
          return { ...item, isFavorite };
        }
        return item;
      });
    },

    updateItemCartStatus(itemId, isInCart) {
      if (this.items && this.items.id === itemId) {
        this.items.isInCart = isInCart;
      }
      this.similarItems = this.similarItems.map(item => {
        if (item.id === itemId) {
          return { ...item, isInCart };
        }
        return item;
      });
    },
  },
};
</script>


<style scoped>
.mt40 {
  margin-top: 40px;
}

.container {
  padding: 0;
}

.mock-container {
  display: flex;
}

.mt100 {
  margin-top: 100px;
}

.small-print {
  font-size: 10px;
  color: #666;
}

.image-container {
  flex: 1 1 40%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

.product-image {
  max-width: 250px;
  height: auto;
  margin-bottom: 10px;
}

.description {
  font-size: 16px;
  color: #666;
}

.product-id {
  font-size: 14px;
  color: #666;
  margin-bottom: 0;
  margin-top: 20px;
}

.details-container {
  flex: 1 1 60%;
  margin-left: 40px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.product-name {
  font-size: 24px;
  margin-bottom: 10px;
}

.price {
  font-size: 18px;
  margin-bottom: 5px;
  margin-top: 0;
}

.without-vat {
  margin-bottom: 5px;
}

.out-of-stock {
  color: red;
}

.action-btn-container {
  display: flex;
  gap: 10px;
  margin-top: 10px;
}

.btn-new {
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
  border: solid .1px black;
}

.row {
  margin-top: 40px;
}

.card {
  overflow: hidden;
  width: 100%;
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

.card-body {
  padding-top: 0;
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
}

.badge {
  color: #000;
  background-color: #f3f3f3;
  border: 1px none;
  text-decoration: none;
}

.ml-left {
  align-self: flex-start;
}

@media screen and (max-width: 575px) {
  .row {
    margin: 0;
  }
}

@media screen and (max-width: 490px) {
  .col-12 {
    flex: 0 0 100%;
    max-width: 100%;
  }
}

@media screen and (min-width: 491px) and (max-width: 767px) {
  .col-sm-6 {
    flex: 0 0 50%;
    max-width: 50%;
  }
}

@media screen and (min-width: 768px) and (max-width: 991px) {
  .col-md-4 {
    flex: 0 0 33.333%;
    max-width: 33.333%;
  }
}

@media screen and (min-width: 992px) {
  .col-lg-3 {
    flex: 0 0 25%;
    max-width: 25%;
  }

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
