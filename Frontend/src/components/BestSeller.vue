<template>
  <div class="best-seller-card">
    <router-link :to="{ path: '/product/' + item.id }" class="card-link">
      <div class="img-container">
        <img v-if="item.img" :src="getImageUrl(item.img)" alt="Item Image" class="img">
      </div>
      <div class="card-body">
        <button class="badge badge-pill badge-secondary">{{ item.category_name }}</button>
        <h5 class="card-title">{{ item.name }}</h5>
        <p class="card-desc">{{ item.desc }}</p>
        <h5 class="card-title">{{ item.price }}€</h5>
      </div>
    </router-link>
  </div>
</template>

<script>
export default {
  name: 'BestSellerItem',
  props: {
    item: {
      type: Object,
      required: true,
    }
  },
  methods: {
    getImageUrl(image) {
      return `http://localhost:8000/storage/uploads/${image}`;
    }
  }
};
</script>

<style scoped>
.best-seller-card {
  overflow: hidden;
  width: 220px;
  height: 340px;
  border: 1px solid #ccc;
}

.img-container {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 160px;
  margin: 5px;
}

.card-body {
  padding-top: 0;
}

.img {
  max-width: 215px;
  max-height: 160px;
}

.card-link {
  text-decoration: none;
  color: inherit;
}

.badge {
  color: #000;
  background-color: #f3f3f3;
  border: 1px none;
  text-decoration: none;
}
</style>
  