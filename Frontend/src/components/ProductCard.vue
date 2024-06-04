<template>
    <div class="card">
      <router-link :to="{ path: '/product/' + item.id }" class="card-link">
        <div class="img-container">
          <img v-if="item.img" :src="getImageUrl(item.img)" alt="Item Image" class="img">
        </div>
        <div class="card-body">
          <button class="badge badge-pill badge-secondary">{{ item.category_name }}</button>
          <h5 class="card-title">{{ truncateName(item.name) }}</h5>
          <h5 class="card-title">{{ item.price }}€</h5>
        </div>
      </router-link>
      <div class="button-container">
        <button v-if="user && item.isInCart" class="btn" @click.stop="removeFromCart(item.id)">
          <i class="bi bi-cart"></i>
          Remove
        </button>
        <button v-else class="btn" @click.stop="handleCartClick(item.id)" :disabled="item.amount === 0">
          <i v-if="item.amount !== 0" class="bi bi-cart"></i>
          {{ item.amount === 0 ? 'Out of stock' : 'Cart' }}
        </button>
        <button v-if="user && item.isFavorite" class="btn" @click.stop="removeFromFavoritesByItemId(item.id)">
          <i class="bi bi-star-fill"></i>
          Remove
        </button>
        <button v-else class="btn" @click.stop="handleFavoriteClick(item.id)">
          <i class="bi bi-star"></i>
          Favorite
        </button>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'ProductCard',
    props: {
      item: {
        type: Object,
        required: true
      },
      user: {
        type: Object,
        default: null
      }
    },
    methods: {
      getImageUrl(image) {
        return `http://localhost:8000/storage/uploads/${image}`;
      },
      truncateName(name) {
        const maxLength = 33;
        if (name.length > maxLength) {
          return name.substring(0, maxLength) + '...';
        }
        return name;
      },
      handleCartClick(itemId) {
        this.$emit('cart-click', itemId);
      },
      handleFavoriteClick(itemId) {
        this.$emit('favorite-click', itemId);
      },
      removeFromCart(itemId) {
        this.$emit('remove-cart', itemId);
      },
      removeFromFavoritesByItemId(itemId) {
        this.$emit('remove-favorite', itemId);
      }
    }
  };
  </script>
  
  <style scoped>
  .card {
    overflow: hidden;
    width: 100%;
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
  
  .button-container {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    text-align: center;
    opacity: 1;
  }
  
  .badge {
    color: #000;
    background-color: #f3f3f3;
    border: 1px none;
    text-decoration: none;
  }
  </style>
  