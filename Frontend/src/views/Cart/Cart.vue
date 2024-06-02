<template>
  <div class="container">
    <div class="row" v-if="loading">
      <div class="col-12 text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
    <div v-else>
      <div class="container2 col-12 text-center" v-if="cartItems.length === 0">
        <img src="/basket.png" class="basket_img" alt="Basket Image">
        <h2>No items in the cart, try <RouterLink class="toProducts" to="/products">browsing</RouterLink> for something.</h2>
      </div>
      <div v-else class="col-12">
        <div class="card">
          <div class="card-body">
            <h1>Cart</h1>
            <div v-for="cartItem in cartItems" :key="cartItem.id" class="cart-item">
              <router-link :to="`/product/${cartItem.item.id}`">
                <img :src="getImageUrl(cartItem.item.img)" alt="Item Image" class="img"/>
              </router-link>
              <router-link :to="`/product/${cartItem.item.id}`" class="name">
                <h2>{{ truncatedName(cartItem.item.name) }}</h2>
              </router-link>
              <div class="actions">
                  <h3 class="price">Price: {{ cartItem.item.price }}€</h3>
                </div>
              <div class="actions">
                <div class="input-group quantity-control">
                  <div class="input-group-prepend">
                    <button class="btn btn-outline-secondary" type="button" @click="decreaseQuantity(cartItem)">-</button>
                  </div>
                  <input type="text" class="form-control quantity-input" v-model="cartItem.quantity" readonly>
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" @click="increaseQuantity(cartItem)">+</button>
                  </div>
                </div>
                <button class="btn btn-danger remove" @click="removeFromCart(cartItem.item.id)">Remove</button>
              </div>
            </div>
          </div>
          <div class="purchase-container">
            <p>Price without VAT: {{ priceWithoutVat.toFixed(2) }}€</p>
            <p>Total VAT amount: {{ totalVatAmount.toFixed(2) }}€</p>
            <h3>Total price: {{ totalValue.toFixed(2) }}€</h3>
            <button class="btn btn-success purchase" @click="purchaseItems">Purchase</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Cart',
  data() {
    return {
      cartItems: [],
      user: null,
      loading: true,
    };
  },
  async mounted() {
    await this.fetchUserData();
    await this.fetchCartItems();
  },
  methods: {
    async fetchUserData() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/user');
        this.user = response.data;
      } catch (error) {
        console.error('Error fetching user data:', error);
      }
    },
    
    async fetchCartItems() {
      try {
        if (!this.user) {
          console.error('User data is not available');
          return;
        }
        const response = await axios.get(`http://127.0.0.1:8000/api/cart/user/${this.user.id}`);
        this.cartItems = response.data.cartItems.map(item => ({
          ...item,
          quantity: 1
        }));
        console.log('Cart items fetched:', this.cartItems);
      } catch (error) {
        console.error('Error fetching cart items:', error);
      } finally {
        this.loading = false;
      }
    },

    increaseQuantity(cartItem) {
      cartItem.quantity += 1;
    },

    decreaseQuantity(cartItem) {
      if (cartItem.quantity > 1) {
        cartItem.quantity -= 1;
      }
    },

    getImageUrl(image) {
      return `http://localhost:8000/storage/uploads/${image}`;
    },

    async removeFromCart(itemId) {
      try {
        const userId = this.user.id;
        const response = await axios.delete(`http://localhost:8000/api/cart/item/${itemId}-${userId}`);
        console.log(response.data.message);
        this.updateItemCartStatus(itemId, false);
        this.cartItems = this.cartItems.filter(item => item.item.id !== itemId);
        console.log('Updated cart items after removal:', this.cartItems);
        document.dispatchEvent(new CustomEvent('cart-updated'));
      } catch (error) {
        console.error('Error removing item from cart:', error);
      }
    },

    updateItemCartStatus(itemId, isInCart) {
      const item = this.cartItems.find(item => item.item.id === itemId);
      if (item) {
        item.isInCart = isInCart;
      }
    },

    async purchaseItems() {
      try {
        const purchaseGroup = {
          time: new Date().toLocaleString(),
          items: [],
          totalPrice: 0,
        };

        for (const cartItem of this.cartItems) {
          const response = await axios.post('http://127.0.0.1:8000/api/purchases', {
            user_id: this.user.id,
            item_id: cartItem.item.id,
            total_price: cartItem.item.price * cartItem.quantity
          });

          purchaseGroup.items.push({
            ...cartItem,
            total_price: cartItem.item.price * cartItem.quantity,
          });
          purchaseGroup.totalPrice += cartItem.item.price * cartItem.quantity;
        }

        await axios.delete(`http://127.0.0.1:8000/api/cart/clear/${this.user.id}`);
        
        this.cartItems = [];
        
        document.dispatchEvent(new CustomEvent('cart-updated'));

        localStorage.setItem('recent_purchase', JSON.stringify(purchaseGroup));
        localStorage.setItem('purchase_completed', 'true');

        this.$router.push('/thank-you');
      } catch (error) {
        console.error('Error purchasing items:', error);
      }
    },

    truncatedName(name) {
      if (name.length > 40) {
        return name.substring(0, 37) + '...';
      }
      return name;
    }
  },
  computed: {
    totalValue() {
      return this.cartItems.reduce((total, cartItem) => {
        return total + cartItem.item.price * cartItem.quantity;
      }, 0);
    },
    priceWithoutVat() {
      return this.totalValue / 1.21;
    },
    totalVatAmount() {
      return this.totalValue - this.priceWithoutVat;
    }
  }
};
</script>

<style scoped>
.name {
  align-self: center;
  margin-left: 10px;
  color: black;
  text-decoration: none;
}

.cart-item {
  display: flex;
  align-items: center;
  flex-direction: row;
  margin-bottom: 15px;
  border-bottom: 1px solid #ccc;
  padding-bottom: 10px;
  flex-wrap: wrap;
}

.img {
  width: 110px;
  max-height: 60px;
  object-fit: cover;
  border-radius: 1px;
}

.actions {
  display: flex;
  align-items: center;
  margin-left: auto;
}

.quantity-control {
  display: flex;
  align-items: center;
  max-height: 40px;
  margin-right: 10px;
  flex: none;
  width: auto;
}

.quantity-input {
  width: 50px;
  text-align: center;
}

.remove {
  max-height: 40px;
}

.purchase {
  display: flex;
  align-self: flex-end;
  max-height: 40px;
  margin-right: 5px;
}

.purchase-container {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  padding: 10px;
}

.price {
  margin-left: auto;
  margin-right: 20px;
  align-self: center;
}

.basket_img {
  width: 200px;
  height: 150px;
  margin: 0 auto;
  user-select: none;
}

.container {
  padding-top: 50px;
}

.container2 {
  margin: auto;
  padding-top: 250px;
}

.toProducts {
  color: #000000;
}

.card {
  margin-bottom: 20px;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
  margin-top: 50px;
}

p {
  margin: 0;
}

@media screen and (min-width: 768px) {
  .basket_img {
    width: 300px;
    height: 250px;
  }
}

@media screen and (max-width: 576px) {
  .cart-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .img, .name, .price, .quantity-control, .remove {
    margin-bottom: 10px;
  }

  .actions {
    margin-left: 0;
    flex-direction: row;
    justify-content: flex-end;
    width: 100%;
  }

  .quantity-control {
    max-width: 200px;
    margin-left: 0;
  }

  .purchase-container {
    align-items: flex-end;
  }

  .purchase {
    margin-top: 10px;
  }
}
</style>
