<template>
  <div class="header">
    <h1>All Products</h1>
  </div>

  <div class="container">
    <div class="filter">
      <h1>Filters</h1>
      <select v-model="filters.category">
        <option value="">All Categories</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">{{
            category.category_name
          }}
        </option>
      </select>
      <select v-model="filters.brand">
        <option value="">All Brands</option>
        <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
      </select>
      <input v-model.number="filters.minPrice" placeholder="Min Price" type="number">
      <input v-model.number="filters.maxPrice" placeholder="Max Price" type="number">
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div v-for="item in filteredItems" :key="item.id" class="col-auto mb-4">
        <div class="card">
          <router-link :to="{ path: '/product/' + item.id }" class="card-link">
            <div class="img-container">
              <img v-if="item.img" :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image"
                   class="img">
            </div>
            <div class="card-body">
              <button class="badge badge-pill badge-secondary">{{ item.category_name }}</button>
              <h5 class="card-title">{{ item.name }}</h5>
              <h5 class="card-title">{{ item.price }}€</h5>
            </div>
          </router-link>
          <div class="button-container">
            <button class="btn">
              <i class="bi bi-cart"></i>
              Cart
            </button>
            <button class="btn" @click="addToFavorites(item.id)">
              <i class="bi bi-star"></i>
              Favorites
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
  name: 'items',
  data() {
    return {
      items: [],
      categories: [],
      brands: [],
      filters: {
        category: '',
        brand: '',
        minPrice: null,
        maxPrice: null
      }
    };
  },

  mounted() {
    this.getItemsAndCategories();
  },

  computed: {
    filteredItems() {
      return this.items.filter(item => {
        return (
            (!this.filters.category || item.categories_id === this.filters.category) &&
            (!this.filters.brand || item.brand_id === this.filters.brand) &&
            (!this.filters.minPrice || item.price >= this.filters.minPrice) &&
            (!this.filters.maxPrice || item.price <= this.filters.maxPrice)
        );
      });
    }
  },

  methods: {
    async getItemsAndCategories() {
      try {
        const [itemsResponse, categoriesResponse, brandsResponse] = await Promise.all([
          axios.get('/front-page-items'),
          axios.get('/categories'),
          axios.get('/brands')
        ]);

        const items = itemsResponse.data.items;
        const categories = categoriesResponse.data.categories;
        const brands = brandsResponse.data.brands;

        // Create a map of category IDs to category names for quick lookup
        const categoryMap = {};
        categories.forEach(category => {
          categoryMap[category.id] = category.category_name;
        });

        this.items = items.map(item => {
          return {
            ...item,
            category_name: categoryMap[item.categories_id] || 'Unknown'
          };
        });

        this.categories = categories;
        this.brands = brands;
      } catch (error) {
        console.error('Error fetching items, categories, or brands:', error);
      }
    },

    async getItems() {
      try {
        const params = {
          category: this.filters.category,
          brand: this.filters.brand,
          minPrice: this.filters.minPrice,
          maxPrice: this.filters.maxPrice
        };

        const response = await axios.get('/front-page-items', {params});

        const items = response.data.items;

        const categoryMap = {};
        this.categories.forEach(category => {
          categoryMap[category.id] = category.category_name;
        });

        this.items = items.map(item => {
          return {
            ...item,
            category_name: categoryMap[item.categories_id] || 'Unknown'
          };
        });
      } catch (error) {
        console.error('Error fetching filtered items:', error);
      }
    },

    addToFavorites(itemId) {
      const userId = this.loggedInUserId;
      axios.post('/favorite-item', {user_id: userId, item_id: itemId})
          .then(response => {
            console.log(response.data.message);
          })
          .catch(error => {
            console.error('Error:', error);
          });
    }
  }
};
</script>


<style scoped>
@import "./Products.scss";
</style>
