<template>
  <div class="home-wrapper">
    <div id="carouselExampleIndicators" class="carousel slide mb-4" data-bs-ride="carousel"
       style="width: 50%; margin: auto;">
      <div class="carousel-inner">
      <div class="carousel-item active">
        <img alt="First slide" class="d-block w-100" src="/1.webp">
      </div>
      <div class="carousel-item">
        <img alt="Second slide" class="d-block w-100" src="/2.webp">
      </div>
      <div class="carousel-item">
        <img alt="Third slide" class="d-block w-100" src="/3.webp">
      </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </a>
  </div>

  <div class="container">
    <div class="text mb-2">
      <h1>New Products</h1>
      <router-link to="/products" class="viewAll">View all</router-link>
    </div>
    <div class="row">
      <div v-for="item in items" :key="item.id" class="col-md-3 mb-4">
        <router-link :to="{path: '/product/'+item.id+''}" class="card-link">
          <div class="card">
            <img class="img" v-if="item.img" :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image">
            <div class="card-body">
              <button class="badge badge-pill badge-secondary">{{ item.categories_id }}</button>
              <h5 class="card-title">{{ item.name }}</h5>
              <h5 class="card-title">{{ item.price }}€</h5>
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
        </router-link>
      </div>
    </div>
  </div>
  </div>

</template>

<script>
import HomeView from './HomeView.js';

export default {
  mixins: [HomeView],
};
</script>

<style lang="scss" scoped>
@import "./HomeView.scss"; // Import SCSS file
</style>
