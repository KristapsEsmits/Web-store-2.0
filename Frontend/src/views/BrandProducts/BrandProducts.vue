<script>
import axios from 'axios';

export default {
  name: 'items',
  data() {
    return {
      items: [],
      brands: [],
    };
  },

  mounted() {
    this.getItems();
    this.getBrands();
  },

  methods: {
    getItems() {
      const brandId = this.$route.params.id;
      axios.get(`/items`).then((res) => {
        this.items = res.data.items.filter(item => item.brand_id === parseInt(brandId));
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

    getBrands() {
      const brandId = this.$route.params.id;
      axios.get(`/brands`).then((res) => {
        this.brands = res.data.brands.filter(brand => brand.id === parseInt(brandId));
      });
    },
  },
};
</script>

<template>
  <div class="header">
    <h1 v-for="brand in brands"> {{ brand.name }} Products</h1>
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

<style lang="scss" scoped>
@import "./BrandProducts.scss";
</style>