<template>
  <div v-if="purchaseGroup">
    <h1 class="success_msg">Congratulations!</h1>
    <div class="card-container">
      <div class="card mt-4">
        <div class="card-body">
          <h1>Your order:</h1>
          <div>
            <ul class="list-group">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <strong>Purchased At:</strong> {{ purchaseGroup.time }}
                </div>
                <button class="btn btn-primary" @click="generatePDF(time, purchaseGroup, user)">Print PDF</button>
              </div>
              <ul>
                <li v-for="purchase in purchaseGroup.items" :key="purchase.id">
                  <div>
                    <strong>Item:</strong> {{ purchase.item.name }}
                  </div>
                  <div>
                    <strong>Price:</strong> {{ purchase.total_price }}€
                  </div>
                </li>
              </ul>
              <h2>Total price: {{ purchaseGroup.totalPrice.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') }}€</h2>
            </ul>
          </div>
        </div>
      </div>
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

onMounted(async () => {
  try {
    const userData = await axios.get('http://127.0.0.1:8000/api/user');
    user.value = userData.data;

    purchaseGroup.value = JSON.parse(localStorage.getItem('recent_purchase'));
    if (purchaseGroup.value) {
      purchaseGroup.value.time = new Date(purchaseGroup.value.time).toLocaleString();
    }

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

@media screen and (min-width: 768px) {
  .basket_img {
    width: 300px;
    height: 250px;
  }
}
</style>
