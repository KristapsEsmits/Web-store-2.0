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
        const brandId = this.$route.params.id; 
        // Fetching items based on the specified brand_id
        axios.get(`/items?brand_id=${brandId}`).then((res) => {
          // Filter items to display only those with the specified brand_id
          this.items = res.data.items.filter(item => item.brand_id === parseInt(brandId));
        });
      },
    },
  };
</script>

<template>
  <div class="header">
    <h1> Products</h1>
  </div>
  <div class="container">
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