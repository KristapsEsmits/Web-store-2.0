import axios from 'axios';

export default {
  name: 'items',
  data() {
    return {
      items: [],
      categories: {},
      carouselImages: [
        { src: '/1.webp', alt: 'First slide' },
        { src: '/2.webp', alt: 'Second slide' },
        { src: '/3.webp', alt: 'Third slide' }
      ],
      user: null
    };
  },
  async mounted() {
    await this.fetchUserData();
    await this.getItemsAndCategories();
  },
  methods: {
    async getItemsAndCategories() {
      try {
        // Fetch user, items, and categories in parallel
        const [userResponse, itemsResponse, categoriesResponse] = await Promise.all([
          axios.get('http://127.0.0.1:8000/api/user'),
          axios.get('http://127.0.0.1:8000/api/front-page-items'),
          axios.get('http://127.0.0.1:8000/api/categories')
        ]);

        this.user = userResponse.data;
        const items = itemsResponse.data.items;
        const categories = categoriesResponse.data.categories;

        const categoryMap = {};
        categories.forEach(category => {
          categoryMap[category.id] = category.category_name;
        });

        // Fetch favorite and cart statuses in batch
        if (this.user) {
          const itemIds = items.map(item => item.id);
          const [favoritesResponse, cartResponse] = await Promise.all([
            axios.post('http://127.0.0.1:8000/api/user/favorites-status', { userId: this.user.id, itemIds }),
            axios.post('http://127.0.0.1:8000/api/user/cart-status', { userId: this.user.id, itemIds })
          ]);

          const favoriteStatuses = favoritesResponse.data;
          const cartStatuses = cartResponse.data;

          this.items = items.map(item => ({
            ...item,
            category_name: categoryMap[item.categories_id] || 'Unknown',
            isFavorite: favoriteStatuses[item.id] || false,
            isInCart: cartStatuses[item.id] || false
          }));
        } else {
          this.items = items.map(item => ({
            ...item,
            category_name: categoryMap[item.categories_id] || 'Unknown'
          }));
        }
      } catch (error) {
        console.error('Error fetching items or categories:', error);
      }
    },

    async fetchUserData() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/user');
        this.user = response.data;
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    },

    getImageUrl(image) {
      return `http://localhost:8000/storage/uploads/${image}`;
    },

    async addToFavorites(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.post('http://127.0.0.1:8000/api/favorites', { user_id: userId, item_id: itemId });
        console.log(response.data.message);
        this.updateItemFavoriteStatus(itemId, true);
      } catch (error) {
        if (error.response && error.response.status === 409) {
          console.error('Item already in favorites');
        } else {
          console.error('Error adding to favorites:', error);
        }
      }
    },

    async removeFromFavoritesByItemId(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.delete(`http://127.0.0.1:8000/api/favorites/item`, {
          data: { user_id: userId, item_id: itemId }
        });
        console.log(response.data.message);
        this.updateItemFavoriteStatus(itemId, false);
      } catch (error) {
        if (error.response) {
          console.error('Error removing from favorites:', error.response.data.message);
          console.error('Detailed error:', error.response.data.error);
        } else {
          console.error('Error removing from favorites:', error.message);
        }
      }
    },

    updateItemFavoriteStatus(itemId, isFavorite) {
      const item = this.items.find(item => item.id === itemId);
      if (item) {
        item.isFavorite = isFavorite;
      }
    },

    async addToCart(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.post('http://127.0.0.1:8000/api/cart', { user_id: userId, item_id: itemId });
        console.log(response.data.message);
        this.updateItemCartStatus(itemId, true);
      } catch (error) {
        if (error.response && error.response.status === 409) {
          console.error('Item already in cart');
        } else {
          console.error('Error adding to cart:', error);
        }
      }
    },

    async removeFromCart(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.delete(`http://127.0.0.1:8000/api/cart/item`, {
          data: { user_id: userId, item_id: itemId }
        });
        console.log(response.data.message);
        this.updateItemCartStatus(itemId, false);
      } catch (error) {
        if (error.response) {
          console.error('Error removing from cart:', error.response.data.message);
          console.error('Detailed error:', error.response.data.error);
        } else {
          console.error('Error removing from cart:', error.message);
        }
      }
    },

    updateItemCartStatus(itemId, isInCart) {
      const item = this.items.find(item => item.id === itemId);
      if (item) {
        item.isInCart = isInCart;
      }
    }
  }
};
