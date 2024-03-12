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
    this.getBrands(); // Call the getBrands method here
  },

  methods: {
    getItems() {
      const brandId = this.$route.params.id;
      // Fetching items based on the specified brand_id
      axios.get(`/items?brand_id=${brandId}`).then((res) => {
        // Filter items to display only those with the specified brand_id
        this.items = res.data.items.filter(item => item.brand_id === parseInt(brandId));
      });
    },

    getBrands() {
      const brandId = this.$route.params.id;
      axios.get(`/brands?brand_id=${brandId}`).then((res) => {
        // Filter brands to display only the one with the specified brand_id
        this.brands = res.data.brands.filter(brand => brand.id === parseInt(brandId));
      }).catch(error => {
        console.error('Error fetching brand:', error);
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

<style lang="scss" scoped>
@import "./BrandProducts.scss";
</style>