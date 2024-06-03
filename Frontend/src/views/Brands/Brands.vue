<script>
import axios from 'axios';
import SkeletonLoader from '@/components/SkeletonBrandCard.vue';

export default {
  name: 'brands',
  components: {
    SkeletonLoader,
  },
  data() {
    return {
      brands: [],
      isLoading: true,
    };
  },
  mounted() {
    this.getBrands();
  },
  methods: {
    getBrands() {
      axios.get('/brands').then((res) => {
        this.brands = res.data.brands;
        this.isLoading = false;
      });
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
      <template v-if="isLoading">
        <div v-for="n in 8" :key="n" class="col-auto mb-4">
          <SkeletonLoader/>
        </div>
      </template>
      <template v-else>
        <div v-for="brand in brands" :key="brand.id" class="col-auto mb-4">
          <router-link :to="{ path: '/brand/' + brand.id + '/products' }" class="card-link">
            <div class="card">
              <img v-if="brand.img" :src="'http://localhost:8000/storage/uploads/' + brand.img" alt="Brand Image"
                   class="img"/>
            </div>
          </router-link>
        </div>
      </template>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.header {
  text-align: center;
  margin-top: 20px;
}

.card {
  position: relative;
  overflow: hidden;
  transition: transform 0.3s;
  width: 280px;
  height: 150px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: solid 1px #ccc;
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

.row {
  margin: 0;
  justify-content: center;
}

@media screen and (max-width: 991px) {
  .row {
    justify-content: center;
  }
}
</style>
