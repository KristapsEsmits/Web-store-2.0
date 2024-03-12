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
        priceRange: '',
      }
    };
  },

  mounted() {
    this.getCategories();
    this.getBrands();
    this.getItems();
  },

  methods: {
    getItems() {
      const queryParams = new URLSearchParams(this.filters).toString();
      const url = `/items?${queryParams}`;

      axios.get(url).then((res) => {
        this.items = res.data.items;
      });
    },

    getCategories() {
      axios.get('/categories').then((res) => {
        this.categories = res.data.categories;
      });
    },

    getBrands() {
      axios.get('/brands').then((res) => {
        this.brands = res.data.brands;
      });
    },
  },
};
</script>

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
      <input v-model="filters.priceRange" placeholder="Price Range" type="text">
      <button @click="getItems">Apply Filters</button>
    </div>
    <div class="row">
      <div v-for="item in items" :key="item.id" class="col-md-3 mb-4">
        <router-link :to="{path: '/product/'+item.id+''}" class="card-link">
          <div class="card">
            <img class="img" v-if="item.img" :src="'http://localhost:8000/storage/uploads/' + item.img"
                 alt="Item Image">
            <div class="card-body">
              <h5 class="card-title">{{ item.name }}</h5>
              <h5 class="card-title">{{ item.price }}€</h5>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import "./Products.scss";
</style>
