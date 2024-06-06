<script setup>
import {onMounted, ref} from 'vue';
import {useRouter} from 'vue-router';
import axios from 'axios';
import {generatePDF} from '@/utils/pdfUtils';

const user = ref(null);
const isLoading = ref(true);
const router = useRouter();
const successMessage = ref(router.currentRoute.value.query.successMessage || '');
const purchaseHistory = ref([]);

const dismissSuccessMessage = () => {
  successMessage.value = '';
};

const groupPurchasesByTime = (purchases) => {
  purchases.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

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

const getStatusText = (status) => {
  switch (status) {
    case 'closed':
      return 'Received';
    case 'canceled':
      return 'Canceled';
    case 'active':
      return 'Ready for Pick Up';
    default:
      return '';
  }
};

const getStatusClass = (status) => {
  switch (status) {
    case 'closed':
      return 'badge-success';
    case 'canceled':
      return 'badge-warning';
    case 'active':
      return 'badge-info';
    default:
      return 'badge-secondary';
  }
};

const getImageUrl = (image) => {
  return `http://localhost:8000/storage/uploads/${image}`;
};

onMounted(async () => {
  try {
    const userData = await axios.get('http://127.0.0.1:8000/api/user');
    user.value = userData.data;

    const purchaseData = await axios.get(`http://127.0.0.1:8000/api/purchases/user/${user.value.id}`);
    console.log('Fetched purchase data:', purchaseData.data.purchases);
    purchaseHistory.value = groupPurchasesByTime(purchaseData.data.purchases);
    console.log('Grouped purchase data:', purchaseHistory.value);
  } catch (error) {
    console.error('Error fetching purchase data:', error);
    if (error.response && error.response.status === 401) {
      await router.push({name: 'login'});
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
                <li v-for="(purchaseGroup, time) in purchaseHistory" :key="time" class="list-group-item">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <strong>Purchased At:</strong> {{ time }} -
                      <button :class="['badge', 'badge-pill', getStatusClass(purchaseGroup.status)]">
                        {{ getStatusText(purchaseGroup.status) }}
                      </button>
                    </div>
                    <button class="btn btn-primary" @click="() => generatePDF(time, purchaseGroup, user)">Print PDF
                    </button>
                  </div>
                  <ul class="order-details">
                    <li v-for="purchase in purchaseGroup.items" :key="purchase.id" class="order-item">
                      <img :src="getImageUrl(purchase.item.img)" alt="product image" class="item-image">
                      <div class="item-details">
                        <div>
                          <strong>Item:</strong> {{ purchase.item.name }}
                        </div>
                        <div>
                          <strong>Quantity:</strong> {{ purchase.quantity }}
                        </div>
                        <div>
                          <strong>Price:</strong> {{ purchase.total_price }}€
                        </div>
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

.order-details {
  margin-top: 20px;
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

.badge {
  color: #000;
  background-color: #f3f3f3;
  border: 1px none;
  text-decoration: none;
  cursor: auto;
}
</style>
