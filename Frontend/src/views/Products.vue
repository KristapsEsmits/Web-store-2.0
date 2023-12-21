<script>
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
        axios.get('/items').then((res) => {
          this.items = res.data.items;
        });
      },
    },
  };
</script>

<template>
  <div class="header">
    <h1>All Products</h1>
  </div>
  <div class="container">
    <div class="filter">
      <h1>Filters</h1>
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
  .header {
    text-align: center;
    margin-top: 20px;
  }
  .container {
    margin-top: 60px;
  }

  .filter {
    float:left;
  }
  .card {
    position: relative;
    overflow: hidden;
    transition: transform 0.3s;
    width: 280px;
    height: 340px;
    justify-content: flex-end;
    border: none; 
  }

  .card:hover {
    transform: scale(1.1);
  }

  .card-link {
    text-decoration: none;
    color: inherit;
  }

  .img{
    max-width: 280px;
    max-height: 200px;
  }
</style>