<script>
import axios from 'axios';

export default {
  name: 'brands',
  data() {
    return {
      brands: [],
    };
  },
  mounted() {
    this.getBrands();
  },
  methods: {
    getBrands() {
      axios.get('/brands').then((res) => {
        this.brands = res.data.brands;
      });
    },
    goToBrandProducts(brandId) {
      this.$router.push({name: 'brand-products', params: {id: brandId}});
    },
  },
};
</script>

<template>
  <div class="header">
    <h1>All Brands</h1>
  </div>
  <div class="container">
    <div class="row">
      <div v-for="brand in brands" :key="brand.id" class="col-auto mb-4">
        <router-link :to="{path: '/brand/'+brand.id+'/products'}" class="card-link">
          <div class="card">
            <img v-if="brand.img" :src="'http://localhost:8000/storage/uploads/' + brand.img" alt="Brand Image"
                 class="img">
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@import "./Brands.scss";
</style>
