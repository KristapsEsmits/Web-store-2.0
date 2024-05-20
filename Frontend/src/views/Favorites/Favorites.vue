<template>
    <div class="container">
      <h1>Your Favorite Items</h1>
      <div class="row">
        <div v-for="item in favoriteItems" :key="item.id" class="col-auto mb-4">
          <div class="card">
            <router-link :to="{ path: '/product/' + item.id }" class="card-link">
              <div class="img-container">
                <img v-if="item.img" :src="getImageUrl(item.img)" alt="Item Image" class="img">
              </div>
              <div class="card-body">
                <button class="badge badge-pill badge-secondary">{{ item.category_name }}</button>
                <h5 class="card-title">{{ item.name }}</h5>
                <h5 class="card-title">{{ item.price }}€</h5>
              </div>
            </router-link>
            <div class="button-container">
              <button class="btn" @click="removeFromFavorites(item.id)">
                <i class="bi bi-x"></i>
                Remove from Favorites
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        favoriteItems: [],
      };
    },
    mounted() {
      this.fetchFavoriteItems();
    },
    methods: {
      fetchFavoriteItems() {
        axios.get('http://127.0.0.1:8000/api/favorites')
          .then(response => {
            this.favoriteItems = response.data.favorites || [];
          })
          .catch(error => {
            console.error('Error fetching favorite items:', error);
          });
      },
      getImageUrl(image) {
        return `http://127.0.0.1:8000/storage/uploads/${image}`;
      },
      removeFromFavorites(itemId) {
        const userId = this.loggedInUserId; // Ensure this is defined
        axios.delete(`http://127.0.0.1:8000/api/favorites/${itemId}`, { data: { user_id: userId } })
          .then(response => {
            console.log(response.data.message);
            this.favoriteItems = this.favoriteItems.filter(item => item.id !== itemId);
          })
          .catch(error => {
            console.error('Error removing favorite item:', error);
          });
      }
    }
  };
  </script>
  
  <style scoped>
  .img-container {
    width: 100%;
    height: 200px;
    overflow: hidden;
  }
  
  .img {
    width: 100%;
    height: auto;
  }
  
  .card-body {
    text-align: center;
  }
  
  .button-container {
    display: flex;
    justify-content: center;
    margin-top: 10px;
  }
  
  .btn {
    margin: 0 5px;
  }
  </style>
  