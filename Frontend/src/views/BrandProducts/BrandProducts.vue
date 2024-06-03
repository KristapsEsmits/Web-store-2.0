<template>
  <div>
    <div class="header">
      <h1 v-if="brand">{{ brand.name }} Products</h1>
    </div>

    <div v-if="filteredItems.length > 0" class="container">
      <div class="filter">
        <h2>Filters</h2>
        <select v-model="filters.category" class="form-select">
          <option value="">All Categories</option>
          <option v-for="category in filteredCategories" :key="category.id" :value="category.id">
            {{ category.category_name }}
          </option>
        </select>
        <input v-model.number="filters.minPrice" class="form-control" placeholder="Min Price" type="number">
        <input v-model.number="filters.maxPrice" class="form-control" placeholder="Max Price" type="number">
      </div>
    </div>

    <div class="container">
      <div class="row">
        <template v-if="loading">
          <div v-for="n in 4" :key="n" class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <SkeletonItemCard/>
          </div>
        </template>
        <template v-else>
          <div v-if="filteredItems.length === 0" class="sorry-message col-12 text-center">
            <img alt="box" class="no_favorites_img" src="/no_favorite.png">
            <h2>Sorry, there are no products available for this brand.</h2>
          </div>
          <div v-else>
            <div class="row">
              <div v-for="item in filteredItems" :key="item.id" class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
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
                    <button v-if="item.isInCart" class="btn" @click="removeFromCart(item.id)">
                      <i class="bi bi-cart"></i>
                      Remove
                    </button>
                    <button v-else class="btn" @click="addToCart(item.id)">
                      <i class="bi bi-cart"></i>
                      Cart
                    </button>
                    <button v-if="item.isFavorite" class="btn" @click="removeFromFavoritesByItemId(item.id)">
                      <i class="bi bi-star-fill"></i>
                      Remove
                    </button>
                    <button v-else class="btn" @click="addToFavorites(item.id)">
                      <i class="bi bi-star"></i>
                      Favorite
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import SkeletonItemCard from '@/components/SkeletonItemCard.vue';

export default {
  name: 'BrandProducts',
  components: {
    SkeletonItemCard,
  },
  data() {
    return {
      items: [],
      categories: [],
      brand: null,
      loading: true,
      user: null,
      filters: {
        category: '',
        minPrice: null,
        maxPrice: null
      },
      filteredCategories: []
    };
  },

  mounted() {
    this.brandId = this.$route.params.id;
    this.fetchData();
  },

  computed: {
    filteredItems() {
      return this.items.filter(item => {
        return (
            (!this.filters.category || item.categories_id === parseInt(this.filters.category)) &&
            (!this.filters.minPrice || item.price >= this.filters.minPrice) &&
            (!this.filters.maxPrice || item.price <= this.filters.maxPrice)
        );
      });
    }
  },

  methods: {
    async fetchData() {
      await this.fetchUserData();
      await this.getItemsAndCategories();
      await this.getBrand();
    },

    async fetchUserData() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/user');
        this.user = response.data;
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    },

    async getItemsAndCategories() {
      try {
        const [itemsResponse, categoriesResponse] = await Promise.all([
          axios.get(`http://127.0.0.1:8000/api/brands/${this.brandId}/products`),
          axios.get('http://127.0.0.1:8000/api/categories')
        ]);

        const items = itemsResponse.data.items;
        const categories = categoriesResponse.data.categories;

        const categoryMap = {};
        categories.forEach(category => {
          categoryMap[category.id] = category.category_name;
        });

        // Filter categories based on items
        const uniqueCategoryIds = [...new Set(items.map(item => item.categories_id))];
        this.filteredCategories = categories.filter(category => uniqueCategoryIds.includes(category.id));

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
            category_name: categoryMap[item.categories_id] || 'Unknown',
            isFavorite: favoriteStatuses[item.id] || false,
            isInCart: cartStatuses[item.id] || false
          }));
        } else {
          this.items = items.map(item => ({
            ...item,
            category_name: categoryMap[item.categories_id] || 'Unknown'
          }));
        }

        this.categories = categories;
      } catch (error) {
        console.error('Error fetching items or categories:', error);
      } finally {
        this.loading = false;
      }
    },

    async getBrand() {
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/brands/${this.brandId}`);
        this.brand = response.data.brand;
      } catch (error) {
        console.error('Error fetching brand:', error);
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

    truncateName(name) {
      const maxLength = 33;
      if (name.length > maxLength) {
        return name.substring(0, maxLength) + '...';
      }
      return name;
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

    updateItemFavoriteStatus(itemId, isFavorite) {
      const item = this.items.find(item => item.id === itemId);
      if (item) {
        item.isFavorite = isFavorite;
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
.header {
  text-align: center;
  margin: 20px 0;
}

.container {
  padding: 0 15px;
}

.no_favorites_img {
  width: 300px;
  height: 300px;
  margin: 0 auto;
  user-select: none;
}

.filter {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 10px;
}

.form-select, .form-control {
  margin-bottom: 10px;
}

@media screen and (min-width: 768px) {
  .filter {
    flex-direction: row;
    gap: 20px;
  }

  .form-select, .form-control {
    flex: 1;
  }
}

.row {
  margin-top: 20px;
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
  max-width: 100%;
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

@media screen and (max-width: 490px) {
  .col-12 {
    flex: 0 0 100%;
    max-width: 100%;
    padding-left: 15px;
    padding-right: 15px;
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


@media screen and (min-width: 768px) {
  .no_favorites_img {
    width: 400px;
    height: 400px;
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
