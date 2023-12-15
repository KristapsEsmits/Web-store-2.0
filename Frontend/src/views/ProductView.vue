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
      // Fetch item details based on the specified item ID
      axios.get(`/items/${itemId}`).then((res) => {
        this.items = res.data.items;
      });
    },
  },
};
</script>

<template>
    <div class="card" style="width: 18rem;">
        <img :src="'http://localhost:8000/storage/uploads/' + items.img" style="max-width: 90px; max-height: 70px;" alt="Item Image">
        <div class="card-body">
            <h5 class="card-title">{{ items.name }}</h5>
            <h5 class="card-title">{{ items.price }}€</h5>
            <p class="card-text">{{ items.description }}</p>
        </div>
    </div>
  </template>
  