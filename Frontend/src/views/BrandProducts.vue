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
  <div class="row">
    <div v-for="item in items" :key="item.id" class="col-md-3 mb-3">
      <div class="card">
        <img v-if="item.img" :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image">
        <div class="card-body">
          <h5 class="card-title">{{ item.name }}</h5>
          <h5 class="card-title">{{ item.price }}€</h5>
          <router-link :to="{path: '/product/'+item.id+''}" class="btn btn-success float-middle">Skatīt</router-link>
        </div>
      </div>
    </div>
  </div>
</template>
