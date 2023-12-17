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
      <div v-for="item in items" :key="item.id" class="col-md-4 mb-3">
        <div class="card">
          <img class="img" v-if="item.img" :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image">
          <div class="card-body">
            <h5 class="card-title">{{ item.name }}</h5>
            <h5 class="card-title">{{ item.price }}€</h5>
            <router-link :to="{path: '/product/'+item.id+''}" class="btn btn-success float-middle">Skatīt</router-link>
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
  .container {
    margin-top: 60px;
  }

  .filter {
    float:left;
  }
  .card {
    width: 280px;
    height: 400px;
    justify-content: flex-end;
  }
  .img{
    max-width: 280px;
    max-height: 200px;
  }

</style>