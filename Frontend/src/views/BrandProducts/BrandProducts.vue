<script>
import axios from 'axios';

export default {
  name: 'items',
  data() {
    return {
      items: [],
      categories: [],
      brands: [],
      brandId: null,
      loading: true
    };
  },

  mounted() {
    this.brandId = this.$route.params.id;
    this.getItemsAndCategories();
    this.getBrands();
  },

  computed: {
    filteredItems() {
      return this.items.filter(item => item.brand_id === parseInt(this.brandId));
    }
  },

  methods: {
    async getItemsAndCategories() {
      try {
        // Fetch items and categories concurrently
        const [itemsResponse, categoriesResponse] = await Promise.all([
          axios.get('/front-page-items'),
          axios.get('/categories')
        ]);

        const items = itemsResponse.data.items;
        const categories = categoriesResponse.data.categories;

        // Create a map of category IDs to category names for quick lookup
        const categoryMap = {};
        categories.forEach(category => {
          categoryMap[category.id] = category.category_name;
        });

        // Assign category names to items
        this.items = items.map(item => {
          return {
            ...item,
            category_name: categoryMap[item.categories_id] || 'Unknown'
          };
        });

        // Filter items by brandId
        this.items = this.items.filter(item => item.brand_id === parseInt(this.brandId));
      } catch (error) {
        console.error('Error fetching items or categories:', error);
      } finally {
        this.loading = false;
      }
    },

    async getBrands() {
      try {
        const response = await axios.get('/brands');
        this.brands = response.data.brands.filter(brand => brand.id === parseInt(this.brandId));
      } catch (error) {
        console.error('Error fetching brands:', error);
      }
    },

    addToFavorites(itemId) {
      const userId = this.loggedInUserId;
      axios.post('/favorite-item', {user_id: userId, item_id: itemId})
          .then(response => {
            console.log(response.data.message);
          })
          .catch(error => {
            console.error('Error:', error);
          });
    }
  }
};
</script>

<template>
  <div class="header">
    <h1 v-if="brands.length"> {{ brands[0].name }} Products</h1>
  </div>

  <div class="container">
    <div v-if="loading" class="loading-message">
      <h2>Loading...</h2>
    </div>
    <div v-else>
      <div v-if="filteredItems.length === 0" class="sorry-message">
        <h2>Sorry, there are no products available for this brand.</h2>
      </div>
      <div v-else class="row">
        <div v-for="item in filteredItems" :key="item.id" class="col-auto mb-4">
          <div class="card">
            <router-link :to="{ path: '/product/' + item.id }" class="card-link">
              <div class="img-container">
                <img v-if="item.img" :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image"
                     class="img">
              </div>
              <div class="card-body">
                <button class="badge badge-pill badge-secondary">{{ item.category_name }}</button>
                <h5 class="card-title">{{ item.name }}</h5>
                <h5 class="card-title">{{ item.price }}€</h5>
              </div>
            </router-link>
            <div class="button-container">
              <button class="btn">
                <i class="bi bi-cart"></i>
                Cart
              </button>
              <button class="btn" @click="addToFavorites(item.id)">
                <i class="bi bi-star"></i>
                Favorites
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<style lang="scss" scoped>
@import "./BrandProducts.scss";
</style>