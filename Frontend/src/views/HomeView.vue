<script>
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import axios from 'axios';

export default {
  name: 'items',
  data() {
    return {
      items: [],
    };
  },
  mounted() {
    this.getItems();
  },
  methods: {
  getItems() {
    axios.get('/front-page-items').then((res) => {
      this.items = res.data.items;
    });
  },
},

};
</script>


<template>
  <div id="carouselExampleIndicators" class="carousel slide mb-4" data-bs-ride="carousel" style="width: 50%; margin: auto;">
    <ol class="carousel-indicators">
      <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></li>
      <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></li>
      <li data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img class="d-block w-100" src="/1.png" alt="First slide">
      </div>
      <div class="carousel-item">
        <img class="d-block w-100" src="/2.png" alt="Second slide">
      </div>
      <div class="carousel-item">
        <img class="d-block w-100" src="/3.png" alt="Third slide">
      </div>
    </div>
    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
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
  .card {
    position: relative;
    overflow: hidden;
    transition: transform 0.3s;
    width: 280px;
    height: 340px;
    justify-content: flex-end;
    border: none; 
  }
  .img{
    max-width: 280px;
    max-height: 200px;
  }
  
  .text{
    display: flex;
    justify-content: space-between;;
  }

  .card:hover {
    transform: scale(1.1);
  }

  .card-link {
    text-decoration: none;
    color: inherit;
  }

  .viewAll {
    text-decoration: none;
    color: inherit;
    font-size: 18px;
  }
</style>


