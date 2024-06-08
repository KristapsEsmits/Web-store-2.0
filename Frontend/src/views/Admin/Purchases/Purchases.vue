<script setup>
import {computed, onMounted, ref} from 'vue';
import {useRouter} from 'vue-router';
import axios from 'axios';
import {generatePDF} from '@/utils/pdfUtils';

const isLoading = ref(true);
const router = useRouter();
const purchaseHistory = ref([]);
const userId = ref(null);
const user = ref({});
const groupStatus = ref({});
const filterStatus = ref('all');
const searchEmail = ref('');
const searchPhone = ref('');

const groupPurchasesByTime = (purchases) => {
  const grouped = {};
  purchases.forEach(purchase => {
    const date = new Date(purchase.created_at);
    const roundedTime = new Date(Math.round(date.getTime() / 10000) * 10000).toLocaleTimeString();
    const fullDate = date.toLocaleDateString();
    if (!grouped[`${fullDate} ${roundedTime}`]) {
      grouped[`${fullDate} ${roundedTime}`] = {
        items: [],
        totalPrice: 0,
        createdAt: purchase.created_at,
        user: purchase.user
      };
      groupStatus.value[`${fullDate} ${roundedTime}`] = purchase.status;
    }
    grouped[`${fullDate} ${roundedTime}`].items.push(purchase);
    grouped[`${fullDate} ${roundedTime}`].totalPrice += parseFloat(purchase.total_price);
  });
  return grouped;
};

const getImageUrl = (image) => {
  return `http://localhost:8000/storage/uploads/${image}`;
};

const changeStatus = async (purchaseGroup, time, newStatus) => {
  try {
    const updatePromises = purchaseGroup.items.map(purchase => {
      return axios.patch(`http://127.0.0.1:8000/api/purchases/${purchase.id}/status`, {status: newStatus});
    });
    await Promise.all(updatePromises);

    const itemUpdates = purchaseGroup.items.map(async purchase => {
      const itemResponse = await axios.get(`http://127.0.0.1:8000/api/items/${purchase.item.id}`);
      const item = itemResponse.data.items;

      item.sold = isNaN(item.sold) ? 0 : item.sold;
      item.reserved = isNaN(item.reserved) ? 0 : item.reserved;
      item.amount = isNaN(item.amount) ? 0 : item.amount;

      if (newStatus === 'closed') {
        item.sold += purchase.quantity;
        item.reserved -= purchase.quantity;
      } else if (newStatus === 'canceled') {
        item.reserved -= purchase.quantity;
      }
      item.reserved = Math.max(0, item.reserved);
      item.amount = Math.max(0, item.amount);

      await axios.patch(`http://127.0.0.1:8000/api/items/${item.id}`, {
        sold: item.sold,
        reserved: item.reserved,
        amount: item.amount
      });
    });
    await Promise.all(itemUpdates);

    groupStatus.value[time] = newStatus;
  } catch (error) {
    console.error('Error updating purchase status:', error.response ? error.response.data : error.message);
  }
};

onMounted(async () => {
  try {
    const userData = await axios.get('http://127.0.0.1:8000/api/user');
    userId.value = userData.data.id;
    user.value = userData.data;

    const purchaseData = await axios.get(`http://127.0.0.1:8000/api/purchases/user/${userId.value}`);
    const sortedPurchases = purchaseData.data.purchases.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    purchaseHistory.value = groupPurchasesByTime(sortedPurchases);

    Object.keys(purchaseHistory.value).forEach(time => {
      const purchaseGroup = purchaseHistory.value[time];
      groupStatus.value[time] = purchaseGroup.items[0].status;
    });

  } catch (error) {
    if (error.response && error.response.status === 401) {
      await router.push({name: 'login'});
    }
  } finally {
    isLoading.value = false;
  }
});

const filteredPurchaseHistory = computed(() => {
  return Object.keys(purchaseHistory.value).filter(time => {
    const purchaseGroup = purchaseHistory.value[time];
    const matchesStatus = filterStatus.value === 'all' || purchaseGroup.items.every(purchase => purchase.status === filterStatus.value);
    const matchesEmail = searchEmail.value === '' || (purchaseGroup.user.email && purchaseGroup.user.email.includes(searchEmail.value));
    const matchesPhone = searchPhone.value === '' || (purchaseGroup.user.phone && purchaseGroup.user.phone.toString().includes(searchPhone.value));
    return matchesStatus && matchesEmail && matchesPhone;
  }).reduce((result, time) => {
    result[time] = purchaseHistory.value[time];
    return result;
  }, {});
});
</script>

<template>
  <main>
    <div class="container">
      <div class="card mt-4">
        <div class="card-body">
          <div>
            <h1>Purchase History</h1>
            <div class="filter-section mb-4">
              <div class="form-group">
                <label for="statusFilter">Filter by Status:</label>
                <select id="statusFilter" v-model="filterStatus" class="form-control">
                  <option value="all">All</option>
                  <option value="active">Active</option>
                  <option value="closed">Closed</option>
                  <option value="canceled">Canceled</option>
                </select>
              </div>
              <div class="form-group">
                <label for="searchEmail">Search by Email:</label>
                <input id="searchEmail" v-model="searchEmail" class="form-control" placeholder="Enter email"
                       type="text">
              </div>
              <div class="form-group">
                <label for="searchPhone">Search by Phone:</label>
                <input id="searchPhone" v-model="searchPhone" class="form-control" placeholder="Enter phone"
                       type="text">
              </div>
            </div>
            <div v-if="isLoading" class="d-flex justify-content-center my-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <div v-else-if="Object.keys(filteredPurchaseHistory).length === 0">
              No purchases found.
            </div>
            <div v-else>
              <ul class="list-group">
                <li v-for="(purchaseGroup, time) in filteredPurchaseHistory" :key="time"
                    :class="{'bg-lightgreen': groupStatus[time] === 'closed', 'bg-lightyellow': groupStatus[time] === 'canceled'}"
                    class="list-group-item">
                  <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                      <strong>Purchased At:</strong> {{ time }}
                    </div>
                    <div class="btn-group">
                      <button class="btn btn-primary mb-2 mb-sm-0 me-0"
                              @click="() => generatePDF(time, purchaseGroup, user)">Print PDF
                      </button>
                      <template v-if="groupStatus[time] === 'closed'">
                        <button class="btn btn-danger mb-2 mb-sm-0 me-0" disabled>Closed</button>
                      </template>
                      <template v-else-if="groupStatus[time] === 'canceled'">
                        <button class="btn btn-warning mb-2 mb-sm-0 me-0" disabled>Canceled</button>
                      </template>
                      <template v-else>
                        <button class="btn btn-secondary mb-2 mb-sm-0 me-0"
                                @click="() => changeStatus(purchaseGroup, time, 'closed')">Mark as closed
                        </button>
                        <button class="btn btn-warning mb-2 mb-sm-0 me-0"
                                @click="() => changeStatus(purchaseGroup, time, 'canceled')">Cancel
                        </button>
                      </template>
                    </div>
                  </div>
                  <div>
                    <strong>User:</strong> {{ purchaseGroup.user.name }} {{ purchaseGroup.user.surname }} -
                    {{ purchaseGroup.user.email }} - {{ purchaseGroup.user.phone }}
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
.bg-lightgreen {
  background-color: rgb(186, 255, 186);
}

.bg-lightyellow {
  background-color: lightyellow;
}

.filter-section {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

@media (min-width: 768px) {
  .filter-section {
    flex-direction: row;
    justify-content: space-between;
  }

  .form-group {
    flex: 1;
    margin-right: 20px;
  }
}

.container {
  margin-bottom: 20px;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
}

.btn-group {
  display: flex;
  flex-wrap: wrap;
  flex-direction: row;
}

.btn {
  padding: 6px 7px 6px 7px;
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

@media (max-width: 767px) {
  .btn-group {
    gap: 0;
  }
}
</style>
