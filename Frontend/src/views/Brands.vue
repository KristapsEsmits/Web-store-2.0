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
        this.$router.push({ name: 'brand-products', params: { id: brandId } });
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
      <div v-for="brand in brands" :key="brand.id" class="col-md-3 mb-3 izmers">
        <div class="card">
          <img class="img" v-if="brand.img" :src="'http://localhost:8000/storage/uploads/' + brand.img" alt="Brand Image">
          <div class="card-body">
            <h5 class="card-title">{{ brand.name }}</h5>
            <router-link :to="{path: '/brand/'+brand.id+'/products'}" class="btn btn-primary float-center">Products</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .header {
    text-align: center;
    margin-top: 20px;
  }
  .card {
    width: 280px;
    height: 250px;
    justify-content: flex-end;
  }
  .img{
    max-width: 280px;
    max-height: 200px;
  }
</style>