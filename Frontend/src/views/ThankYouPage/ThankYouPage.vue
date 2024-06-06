<template>
  <div v-if="purchaseGroup">
    <h1 class="success_msg">Congratulations!</h1>
    <div class="card-container">
      <div class="card mt-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <h1>Your order:</h1>
            <button class="btn btn-primary" @click="generatePDF(purchaseGroup.time, purchaseGroup, user)">Print PDF</button>
          </div>
          <div class="order-details">
            <div class="order-summary">
              <div>
                <strong>Date:</strong> {{ purchaseGroup.time }}
              </div>
              <div>
                <strong>Total:</strong> {{ purchaseGroup.totalPrice.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') }}€
              </div>
            </div>
            <ul class="list-group">
              <li v-for="purchase in purchaseGroup.items" :key="purchase.item.id" class="order-item">
                <img :src="getImageUrl(purchase.item.img)" alt="product image" class="item-image">
                <div class="item-details">
                  <div>
                    <strong>Item:</strong> {{ purchase.item.name }}
                  </div>
                  <div>
                    <strong>Quantity:</strong> {{ purchase.quantity }}
                  </div>
                  <div>
                    <strong>Price:</strong> {{ purchase.total_price.toFixed(2) }}€
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <p class="disclaimer">*After closing this page you can manage your orders in my profiles page</p>
    </div>
  </div>
  <div v-else class="didnt-buy">
    <img alt="basket" class="basket_img" src="/basket.png">
    <h1>Hmmm... You didn't purchase anything!</h1>
    <h1>Try
      <RouterLink class="toProducts" to="/products">browsing</RouterLink>
      for something.
    </h1>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { generatePDF } from '@/utils/pdfUtils';

const purchaseGroup = ref(null);
const user = ref(null);

const getImageUrl = (image) => {
  return `http://localhost:8000/storage/uploads/${image}`;
};

onMounted(async () => {
  try {
    const userData = await axios.get('http://127.0.0.1:8000/api/user');
    user.value = userData.data;

    purchaseGroup.value = JSON.parse(localStorage.getItem('recent_purchase'));
    console.log('Fetched purchase data:', purchaseGroup.value);
    purchaseGroup.value.items.forEach(item => {
      console.log('Item:', item);
    });

    localStorage.removeItem('purchase_completed');
    localStorage.removeItem('recent_purchase');
  } catch (error) {
    console.error('Error fetching user or purchase data:', error);
  }
});
</script>


<style scoped>
.card-container {
  margin: 50px;
}

.order-details {
  margin-top: 20px;
}

.order-summary {
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
  padding: 10px;
  background-color: #f8f9fa;
  border-radius: 5px;
}

.list-group {
  padding-left: 0;
}

.order-item {
  display: flex;
  align-items: center;
  padding: 10px;
}

.item-image {
  width: 50px;
  height: 50px;
  margin-right: 10px;
  object-fit: scale-down;
}

.item-details {
  flex-grow: 1;
}

.didnt-buy {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding-top: 250px;
  text-align: center;
}

.basket_img {
  width: 200px;
  height: 150px;
  margin: 0 auto;
  user-select: none;
}

.toProducts {
  color: #000000;
}

.success_msg {
  text-align: center;
  margin-top: 50px;
}

.disclaimer {
  color: gray;
  font-size: small;
}

@media screen and (min-width: 768px) {
  .basket_img {
    width: 300px;
    height: 250px;
  }
}
</style>
