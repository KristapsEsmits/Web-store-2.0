<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const user = ref(null);
const isLoading = ref(true);
const router = useRouter();
const successMessage = ref(router.currentRoute.value.query.successMessage || '');
const purchaseHistory = ref([]);

const dismissSuccessMessage = () => {
  successMessage.value = '';
};

const groupPurchasesByTime = (purchases) => {
  const grouped = {};
  purchases.forEach(purchase => {
    const date = new Date(purchase.created_at);
    const roundedTime = new Date(Math.round(date.getTime() / 10000) * 10000).toLocaleTimeString();
    if (!grouped[roundedTime]) {
      grouped[roundedTime] = {
        items: [],
        totalPrice: 0
      };
    }
    grouped[roundedTime].items.push(purchase);
    grouped[roundedTime].totalPrice += parseFloat(purchase.total_price);
  });
  return grouped;
};

onMounted(async () => {
  try {
    const userData = await axios.get('http://127.0.0.1:8000/api/user');
    user.value = userData.data;

    const purchaseData = await axios.get(`http://127.0.0.1:8000/api/purchases/user/${user.value.id}`);
    purchaseHistory.value = groupPurchasesByTime(purchaseData.data.purchases);
  } catch (error) {
    if (error.response && error.response.status === 401) {
      await router.push({ name: 'login' });
    }
  } finally {
    isLoading.value = false;
  }
});

</script>

<template>
  <main>
    <div class="container">
      <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
        <span>{{ successMessage }}</span>
        <button class="btn-close" type="button" @click="dismissSuccessMessage"></button>
      </div>
      <div class="card">
        <div class="card-body">
          <router-link :to="{ path: '/profile/change-password' }" class="float-end nav-link">Change password</router-link>
          <router-link :to="{ path: '/profile/edit' }" class="actionBtn float-end nav-link">Update data</router-link>
          <h1>{{ user?.name }} {{ user?.surname }}</h1>
          <h2>Number {{ user?.phone }}</h2>
          <h2>Email {{ user?.email }}</h2>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-body">
          <div>
            <h1>Purchase History</h1>
            <div v-if="Object.keys(purchaseHistory).length === 0">No purchases found.</div>
            <div v-else>
              <ul class="list-group">
                <li v-for="(purchaseGroup, time) in purchaseHistory" :key="time" class="list-group-item">
                  <div>
                    <strong>Purchased At:</strong> {{ time }}
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
                  <h2>Total price: {{ purchaseGroup.totalPrice }}€</h2>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>


<style scoped>
@import './Profile.scss';
</style>

<style scoped>
@import './Profile.scss';
</style>
