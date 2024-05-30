<script>
import axios from 'axios';

export default {
  name: 'items',
  data() {
    return {
      items: [],
      categories: [],
      brands: [],
      brandId: null,
      loading: true,
      user: null,
      filters: {
        category: '',
        minPrice: null,
        maxPrice: null
      },
    };
  },

  mounted() {
    this.brandId = this.$route.params.id;
    this.fetchUserData();
    this.getItemsAndCategories();
    this.getBrands();
  },

  computed: {
    filteredItems() {
      return this.items.filter(item => {
        return (
          item.brand_id === parseInt(this.brandId) &&
          (!this.filters.category || item.categories_id === parseInt(this.filters.category)) &&
          (!this.filters.minPrice || item.price >= this.filters.minPrice) &&
          (!this.filters.maxPrice || item.price <= this.filters.maxPrice)
        );
      });
    }
  },

  methods: {
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
          axios.get('http://127.0.0.1:8000/api/front-page-items'),
          axios.get('http://127.0.0.1:8000/api/categories')
        ]);

        const items = itemsResponse.data.items;
        const categories = categoriesResponse.data.categories;

        const categoryMap = {};
        categories.forEach(category => {
          categoryMap[category.id] = category.category_name;
        });

        if (this.user) {
          const itemIds = items.map(item => item.id);
          const [favoritesResponse, cartResponse] = await Promise.all([
            axios.post('http://127.0.0.1:8000/api/user/favorites-status', { userId: this.user.id, itemIds }),
            axios.post('http://127.0.0.1:8000/api/user/cart-status', { userId: this.user.id, itemIds })
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

        this.categories = categories;  // Make sure categories are assigned to the data property
      } catch (error) {
        console.error('Error fetching items or categories:', error);
      } finally {
        this.loading = false;
      }
    },

    async getBrands() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/brands');
        this.brands = response.data.brands.filter(brand => brand.id === parseInt(this.brandId));
      } catch (error) {
        console.error('Error fetching brands:', error);
      }
    },

    getImageUrl(image) {
      return `http://localhost:8000/storage/uploads/${image}`;
    },

    async addToFavorites(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.post('http://127.0.0.1:8000/api/favorites', { user_id: userId, item_id: itemId });
        console.log(response.data.message);
        this.updateItemFavoriteStatus(itemId, true);
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
      } catch (error) {
        if (error.response) {
          console.error('Error removing from cart:', error.response.data.message);
          console.error('Detailed error:', error.response.data.error);
        } else {
          console.error('Error removing from cart:', error.message);
        }
      }
    },

    async addToCart(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.post('http://127.0.0.1:8000/api/cart', { user_id: userId, item_id: itemId });
        console.log(response.data.message);
        this.updateItemCartStatus(itemId, true);
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

<template>
  <div class="header">
    <h1 v-if="brands.length">{{ brands[0].name }} Products</h1>
  </div>

  <div class="container">
    <div class="filter">
      <h1>Filters</h1>
      <select v-model="filters.category">
        <option value="">All Categories</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.category_name }}</option>
      </select>
      <input v-model.number="filters.minPrice" placeholder="Min Price" type="number">
      <input v-model.number="filters.maxPrice" placeholder="Max Price" type="number">
    </div>
    <div v-if="loading" class="loading-message">
      <h2>Loading...</h2>
    </div>
    <div v-else>
      <div v-if="filteredItems.length === 0" class="sorry-message">
        <h2>Sorry, there are no products available for this brand.</h2>
      </div>
      <div v-else class="row">
        <div v-for="item in filteredItems" :key="item.id" class="col-auto mb-4">
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
  </div>
</template>

<style lang="scss" scoped>
@import "./BrandProducts.scss";
</style>