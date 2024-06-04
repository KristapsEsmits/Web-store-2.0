<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import { generatePDF } from '@/utils/pdfUtils';

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
    const fullDate = date.toLocaleDateString('en-GB').replace(/\//g, '.');
    if (!grouped[`${fullDate} ${roundedTime}`]) {
      grouped[`${fullDate} ${roundedTime}`] = {
        items: [],
        totalPrice: 0,
        createdAt: `${fullDate} ${roundedTime}`,
        status: purchase.status
      };
    }
    grouped[`${fullDate} ${roundedTime}`].items.push(purchase);
    grouped[`${fullDate} ${roundedTime}`].totalPrice += parseFloat(purchase.total_price);
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
          <div class="profile-actions d-flex justify-content-end">
            <router-link :to="{ path: '/profile/edit' }" class="actionBtn nav-link">Update data</router-link>
            <router-link :to="{ path: '/profile/change-password' }" class="nav-link">Change password</router-link>
          </div>
          <h1>{{ user?.name }} {{ user?.surname }}</h1>
          <h2>Number: {{ user?.phone }}</h2>
          <h2>Email: {{ user?.email }}</h2>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-body">
          <div>
            <h1>Purchase History</h1>
            <div v-if="Object.keys(purchaseHistory).length === 0">No purchases found.</div>
            <div v-else>
              <ul class="list-group">
                <li v-for="(purchaseGroup, time) in purchaseHistory" :key="time"
                    :class="{'bg-lightgreen': purchaseGroup.items.status === 'closed'}" class="list-group-item">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <strong>Purchased At:</strong> {{ time }}
                    </div>
                    <button class="btn btn-primary" @click="() => generatePDF(time, purchaseGroup, user)">Print PDF</button>
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
.bg-lightgreen {
  background-color: rgb(191, 253, 191);
}

.card {
  max-width: 100%;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 20px;
}

.profile-actions {
  margin-bottom: 10px;
}

.actionBtn {
  margin-right: 20px !important;
}

@media (max-width: 768px) {
  .container {
    padding: 10px;
  }

  h1, h2 {
    font-size: 1.2em;
  }

  .card-body {
    padding: 10px;
  }

  .btn {
    font-size: 0.8em;
  }

  .list-group-item {
    padding: 10px;
  }

  .profile-actions {
    flex-direction: column;
    align-items: flex-end;
  }

  .actionBtn {
    margin-right: 0 !important;
  }
}
</style>
