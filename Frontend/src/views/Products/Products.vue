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
      <button @click="getItems">Apply Filters</button>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div v-for="item in items" :key="item.id" class="col-auto mb-4">
        <div class="card">
          <router-link :to="{path: '/product/'+item.id+''}" class="card-link">
            <div class="img-container">
              <img v-if="item.img" :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image"
                   class="img">
            </div>
            <div class="card-body">
              <button class="badge badge-pill badge-secondary">{{ item.categories_id }}</button>
              <h5 class="card-title">{{ item.name }}</h5>
              <h5 class="card-title">{{ item.price }}€</h5>
            </div>
          </router-link>
          <div class="button-container">
            <button class="btn">
              <i class="bi bi-cart"></i>
              Cart
            </button>
            <button class="btn">
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
      filters: {
        category: '',
        brand: '',
        minPrice: null,
        maxPrice: null
      }
    };
  },

  mounted() {
    this.getItems();
  },

  methods: {
    getItems() {
      axios.get('/front-page-items').then((res) => {
        this.items = res.data.items;
        this.items.forEach(item => {
          const relatedTableId = item.categories_id;
          axios.get(`/categories`).then((response) => {
            const categories = response.data.categories.filter(category => category.id === parseInt(relatedTableId));

            const categoriesName = categories.map(category => category.category_name);

            item.categories_id = categoriesName[0];

          }).catch((error) => {
            console.error('Error fetching related data:', error);
          });
        });
      });
    },
  },
};
</script>

<style scoped>
@import "./Products.scss";
</style>
