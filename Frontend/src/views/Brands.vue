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
  <div class="row">
    <div v-for="brand in brands" :key="brand.id" class="col-md-3 mb-3">
      <div class="card">
        <img v-if="brand.img" :src="'http://localhost:8000/storage/uploads/' + brand.img" alt="Brand Image">
        <div class="card-body">
          <h5 class="card-title">{{ brand.name }}</h5>
          <router-link :to="{path: '/brand/'+brand.id+'/products'}" class="btn btn-success float-middle">Products</router-link>
        </div>
      </div>
    </div>
  </div>
</template>
