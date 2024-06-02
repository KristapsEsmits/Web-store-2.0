<template>
  <div class="home-wrapper">
    <div class="row no-gutters-carousel">
      <div id="carouselExampleIndicators" class="carousel slide mb-4" data-bs-ride="carousel"
           style="margin: auto !important;">
        <div class="carousel-inner">
          <div v-for="(image, index) in carouselImages" :key="index"
               :class="['carousel-item', { active: index === 0 }]">
            <img :alt="image.alt" :src="image.src" class="d-block w-100">
          </div>
        </div>
        <a class="carousel-control-prev" data-bs-slide="prev" href="#carouselExampleIndicators" role="button">
          <span aria-hidden="true" class="carousel-control-prev-icon"></span>
        </a>
        <a class="carousel-control-next" data-bs-slide="next" href="#carouselExampleIndicators" role="button">
          <span aria-hidden="true" class="carousel-control-next-icon"></span>
        </a>
      </div>
    </div>
  </div>

  <div class="newProducts">
    <h1>New Products</h1>
    <router-link class="viewAll" to="/products">View all</router-link>
  </div>

  <div class="container">
    <div class="row">
      <template v-if="isLoading">
        <div v-for="n in 4" :key="n" class="col-auto mb-4">
          <SkeletonItemCard/>
        </div>
      </template>
      <template v-else>
        <div v-for="item in items" :key="item.id" class="col-auto mb-4">
          <div class="card">
            <router-link :to="{ path: '/product/' + item.id }" class="card-link">
              <div class="img-container">
                <img v-if="item.img" :src="getImageUrl(item.img)" alt="Item Image" class="img">
              </div>
              <div class="card-body">
                <button class="badge badge-pill badge-secondary">{{ item.category_name }}</button>
                <h5 class="card-title">{{ item.name }}</h5>
                <h5 class="card-title">{{ item.price }}€</h5>
              </div>
            </router-link>
            <div class="button-container">
              <button v-if="user && item.isInCart" class="btn" @click="removeFromCart(item.id)">
                <i class="bi bi-cart"></i>
                Remove
              </button>
              <button v-else class="btn" @click="handleCartClick(item.id)">
                <i class="bi bi-cart"></i>
                Cart
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
      items: [],
      categories: [],
      isLoading: true,
      carouselImages: [
        {src: '/1.webp', alt: 'First slide'},
        {src: '/2.webp', alt: 'Second slide'},
        {src: '/3.webp', alt: 'Third slide'}
      ],
      user: null
    };
  },
  async mounted() {
    await this.fetchUserData();
    await this.getItemsAndCategories();
  },
  methods: {
    async getItemsAndCategories() {
      try {
        const [itemsResponse, categoriesResponse] = await Promise.all([
          axios.get('http://127.0.0.1:8000/api/front-page-items'),
          axios.get('http://127.0.0.1:8000/api/categories')
        ]);

        const items = itemsResponse.data.items;
        const categories = categoriesResponse.data.categories;

        if (this.user) {
          const itemIds = items.map(item => item.id);
          const [favoritesResponse, cartResponse] = await Promise.all([
            axios.post('http://127.0.0.1:8000/api/user/favorites-status', {userId: this.user.id, itemIds}),
            axios.post('http://127.0.0.1:8000/api/user/cart-status', {userId: this.user.id, itemIds})
          ]);

          const favoriteStatuses = favoritesResponse.data;
          const cartStatuses = cartResponse.data;

          this.items = items.map(item => ({
            ...item,
            category_name: this.getCategoryName(categories, item.categories_id),
            isFavorite: favoriteStatuses[item.id] || false,
            isInCart: cartStatuses[item.id] || false
          }));
        } else {
          this.items = items.map(item => ({
            ...item,
            category_name: this.getCategoryName(categories, item.categories_id),
            isFavorite: false,
            isInCart: false
          }));
        }

        this.categories = categories;
      } catch (error) {
        console.error('Error fetching items or categories:', error);
      } finally {
        this.isLoading = false;
      }
    },

    getCategoryName(categories, categoryId) {
      const category = categories.find(cat => cat.id === categoryId);
      return category ? category.category_name : 'Unknown';
    },

    async fetchUserData() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/user', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('access_token')}`
          }
        });
        this.user = response.data;
      } catch (error) {
        if (error.response && error.response.status === 401) {
          // User is not authenticated
          this.user = null;
        } else {
          console.error('Error fetching user data:', error);
        }
      }
    },

    getImageUrl(image) {
      return `http://localhost:8000/storage/uploads/${image}`;
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

    updateItemFavoriteStatus(itemId, isFavorite) {
      const item = this.items.find(item => item.id === itemId);
      if (item) {
        item.isFavorite = isFavorite;
      }
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

    handleCartClick(itemId) {
      if (this.user) {
        this.addToCart(itemId);
      } else {
        this.$router.push({path: '/login'});
      }
    },

    handleFavoriteClick(itemId) {
      if (this.user) {
        this.addToFavorites(itemId);
      } else {
        this.$router.push({path: '/login'});
      }
    },

    updateItemCartStatus(itemId, isInCart) {
      const item = this.items.find(item => item.id === itemId);
      if (item) {
        item.isInCart = isInCart;
      }
    }
  }
};
</script>

<style scoped>
.container {
  display: flex;
  justify-content: center;
  justify-self: center;
  padding: 0;
}

.no-gutters-carousel {
  --bs-gutter-x: 0;
}

.category-list li {
  margin-bottom: 10px;
}

.category-list a {
  text-decoration: none;
  color: inherit;
  display: flex;
  align-items: center;
}

.category-list i {
  margin-right: 10px;
}

.newProducts {
  display: flex;
  justify-content: space-between;
  margin: 30px;
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

.img {
  max-width: 215px;
  max-height: 160px;
}

.card-link {
  text-decoration: none;
  color: inherit;
}

.viewAll {
  text-decoration: none;
  color: inherit;
  font-size: 18px;

  &:hover {
    text-decoration: underline;
  }
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

.row {
  justify-content: center;
}

@media screen and (max-width: 425px) {
  .newProducts {
    margin: 5px;
  }
}

@media screen and (max-width: 575px) {
  .row {
    margin: 0;
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

@media screen and (min-width: 1020px) {
  .newProducts {
    margin: 30px 100px 30px 100px;
  }

  .carousel {
    max-width: 80%;
    justify-content: center;
  }
}
</style>
