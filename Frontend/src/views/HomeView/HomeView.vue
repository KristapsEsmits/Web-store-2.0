<template>
  <div class="home-wrapper">
    <div id="carouselExampleIndicators" class="carousel slide mb-4" data-bs-ride="carousel"
         style="margin: auto;">
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
      <a class="carousel-control-prev" data-bs-slide="prev" href="#carouselExampleIndicators" role="button">
        <span aria-hidden="true" class="carousel-control-prev-icon"></span>
      </a>
      <a class="carousel-control-next" data-bs-slide="next" href="#carouselExampleIndicators" role="button">
        <span aria-hidden="true" class="carousel-control-next-icon"></span>
      </a>
    </div>
  </div>

  <div class="newProducts">
    <h1>New Products</h1>
    <router-link class="viewAll" to="/products">View all</router-link>
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
import HomeView from './HomeView.js';

export default {
  mixins: [HomeView],
};
</script>

<style lang="scss" scoped>
@import "./HomeView.scss";
</style>
