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
      const itemId = this.$route.params.id;
      axios.get(`/items/${itemId}`).then((res) => {
        this.items = res.data.items;
      });
    },
  },
};
</script>

<template>
  <div class="card" style="width: 18rem;">
    <img :src="'http://localhost:8000/storage/uploads/' + items.img" alt="Item Image"
         style="max-width: 250px; max-height: 200px;">
    <div class="card-body">
      <h5 class="card-title">{{ items.name }}</h5>
      <h5 class="card-title">{{ items.price }}€</h5>
      <p class="card-text">{{ items.description }}</p>
    </div>
  </div>
</template>
  