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
      <div v-for="brand in brands" :key="brand.id" class="col-md-3 mb-4">
        <router-link :to="{path: '/brand/'+brand.id+'/products'}" class="card-link">
          <div class="card">
            <img class="img" v-if="brand.img" :src="'http://localhost:8000/storage/uploads/' + brand.img" alt="Brand Image">
          </div>
        </router-link>
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
    position: relative;
    overflow: hidden;
    transition: transform 0.3s;
    width: 280px;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none; 
  }

  .card:hover {
    transform: scale(1.1);
    background-color: rgb(250, 250, 250);
  }

  .card-link {
    text-decoration: none;
    color: inherit;
  }

  .img {
    max-width: 100%;
    max-height: 100%;
  }
</style>
