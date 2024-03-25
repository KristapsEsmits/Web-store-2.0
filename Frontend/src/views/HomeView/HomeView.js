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
      axios.get('/front-page-items').then((res) => {
        this.items = res.data.items;
      });
    },

    addToFavorites(itemId) {
      const userId = this.loggedInUserId; // You need to define the logged-in user ID
      axios.post('/favorite-item', { user_id: userId, item_id: itemId })
        .then(response => {
          console.log(response.data.message);
          // Optionally, show a success message or update UI
        })
        .catch(error => {
          console.error('Error:', error);
          // Optionally, show an error message or handle the error
        });
    }
  },
};