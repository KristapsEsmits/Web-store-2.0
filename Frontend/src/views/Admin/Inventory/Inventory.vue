<template>
  <div class="wrapper">
    <div class="content-wrapper">
      <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
        <span>{{ successMessage }}</span>
        <button class="btn-close" type="button" @click="dismissSuccessMessage"></button>
      </div>
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title mb-0">Inventory</h4>
          <div class="d-flex align-items-center">
            <input v-model="searchQuery" class="form-control me-2" placeholder="Search by ID or Name"
                   style="width: 200px;" type="text" @input="searchItems"/>
            <button :disabled="!isAnyRowSelected" class="btn btn-warning btn-round btn-fill"
                    @click="exportSelectedRows">Export Selected Rows
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive d-none d-md-block">
            <table class="table table-bordered table-auto">
              <thead>
              <tr>
                <th><input v-model="selectAll" type="checkbox" @change="toggleSelectAll"></th>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Vat</th>
                <th>Amount</th>
                <th>Reserved</th>
                <th>Sold</th>
                <th>Actions</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(item, index) in filteredItems" :key="index">
                <td><input v-model="selectedRows" :value="item" type="checkbox"></td>
                <td>{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.price }}€</td>
                <td>{{ item.vat }}%</td>
                <td>{{ item.amount }}</td>
                <td>{{ item.reserved }}</td>
                <td>{{ item.sold }}</td>
                <td class="d-flex justify-content-center">
                  <router-link :to="{ path: '/admin/inventory-edit/' + item.id + '/edit' }"
                               class="btn btn-success me-2">Edit
                  </router-link>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="d-block d-md-none">
            <div class="select-all-mobile">
              <input v-model="selectAll" type="checkbox" @change="toggleSelectAll"> Select All
            </div>
            <div v-for="(item, index) in filteredItems" :key="index" class="card mb-3">
              <div class="card-body">
                <h5 class="card-title"><input v-model="selectedRows" :value="item" class="checkbox-btn" type="checkbox">{{
                    item.name
                  }}</h5>
                <p class="card-text"><strong>Price:</strong> {{ item.price }}€</p>
                <p class="card-text"><strong>Amount:</strong> {{ item.amount }}</p>
                <p class="card-text"><strong>Reserved:</strong> {{ item.reserved }}</p>
                <p class="card-text"><strong>Sold:</strong> {{ item.sold }}</p>
                <div class="action-btns">
                  <router-link :to="{ path: '/admin/inventory-edit/' + item.id + '/edit' }"
                               class="btn btn-success me-2">Edit
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header">
          <h4 class="card-title mb-0">Financial Overview</h4>
        </div>
        <div class="card-body">
          <div class="tabs">
            <button :class="{ active: selectedTab === 'category' }" @click="selectedTab = 'category'">By Category
            </button>
            <button :class="{ active: selectedTab === 'item' }" @click="selectedTab = 'item'">By Item</button>
          </div>

          <div v-if="selectedTab === 'category'">
            <h2>By Category</h2>
            <div class="table-container">
              <table>
                <thead>
                <tr>
                  <th @click="sortBy('category_id')">Category ID</th>
                  <th @click="sortBy('category')">Category</th>
                  <th @click="sortBy('total_earned_with_vat')">Total Earned (with VAT)</th>
                  <th @click="sortBy('total_vat')">Total VAT</th>
                  <th @click="sortBy('total_earned_without_vat')">Total Earned (without VAT)</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(finance, category) in sortedCategoryFinances" :key="category.id">
                  <td>{{ finance.category_id }}</td>
                  <td>{{ category }}</td>
                  <td>{{ finance.total_earned_with_vat.toFixed(2) }}</td>
                  <td>{{ finance.total_vat.toFixed(2) }}</td>
                  <td>{{ finance.total_earned_without_vat.toFixed(2) }}</td>
                </tr>
                <tr>
                  <td colspan="2"><strong>Total</strong></td>
                  <td><strong>{{ totalCategoryEarnedWithVat.toFixed(2) }}</strong></td>
                  <td><strong>{{ totalCategoryVat.toFixed(2) }}</strong></td>
                  <td><strong>{{ totalCategoryEarnedWithoutVat.toFixed(2) }}</strong></td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div v-if="selectedTab === 'item'">
            <h2>By Item</h2>
            <div class="table-container">
              <table>
                <thead>
                <tr>
                  <th @click="sortByItem('item_id')">Item ID</th>
                  <th @click="sortByItem('item')">Item</th>
                  <th @click="sortByItem('total_earned_with_vat')">Total Earned (with VAT)</th>
                  <th @click="sortByItem('total_vat')">Total VAT</th>
                  <th @click="sortByItem('total_earned_without_vat')">Total Earned (without VAT)</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(finance, item) in sortedItemFinances" :key="item.id">
                  <td>{{ finance.item_id }}</td>
                  <td>{{ item }}</td>
                  <td>{{ finance.total_earned_with_vat.toFixed(2) }}</td>
                  <td>{{ finance.total_vat.toFixed(2) }}</td>
                  <td>{{ finance.total_earned_without_vat.toFixed(2) }}</td>
                </tr>
                <tr>
                  <td colspan="2"><strong>Total</strong></td>
                  <td><strong>{{ totalItemEarnedWithVat.toFixed(2) }}</strong></td>
                  <td><strong>{{ totalItemVat.toFixed(2) }}</strong></td>
                  <td><strong>{{ totalItemEarnedWithoutVat.toFixed(2) }}</strong></td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {computed, onMounted, ref} from 'vue';
import axios from 'axios';
import {useRoute} from 'vue-router';
import * as XLSX from 'xlsx';

const items = ref([]);
const successMessage = ref('');
const route = useRoute();
const selectedRows = ref([]);
const selectAll = ref(false);
const searchQuery = ref('');

const categoryFinances = ref({});
const itemFinances = ref({});
const sortKey = ref('');
const sortAsc = ref(true);
const sortKeyItem = ref('');
const sortAscItem = ref(true);
const selectedTab = ref('category');

const getItems = async () => {
  try {
    const res = await axios.get('/items');
    items.value = res.data.items;
  } catch (error) {
    console.error('Error fetching items:', error);
  }
};

const dismissSuccessMessage = () => {
  successMessage.value = '';
};

const exportSelectedRows = () => {
  const data = selectedRows.value.map(item => ({
    id: item.id,
    name: item.name,
    price: item.price,
    amount: item.amount,
    reserved: item.reserved,
    sold: item.sold
  }));

  const worksheet = XLSX.utils.json_to_sheet(data);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Selected Items');
  XLSX.writeFile(workbook, 'selected_items.xlsx');
};

const isAnyRowSelected = computed(() => selectedRows.value.length > 0);

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedRows.value = [...items.value];
  } else {
    selectedRows.value = [];
  }
};

const filteredItems = computed(() => {
  return items.value.filter(item => {
    return item.id.toString().includes(searchQuery.value) || item.name.toLowerCase().includes(searchQuery.value.toLowerCase());
  });
});

const searchItems = async () => {
  try {
    const res = await axios.get('/items', {params: {search: searchQuery.value}});
    items.value = res.data.items;
  } catch (error) {
    console.error('Error searching items:', error);
  }
};

const fetchFinances = async () => {
  try {
    const response = await axios.get('/finances');
    categoryFinances.value = response.data.categories;
    itemFinances.value = response.data.items;
  } catch (error) {
    console.error("There was an error fetching the financial data:", error);
  }
};

const sortBy = (key) => {
  if (sortKey.value === key) {
    sortAsc.value = !sortAsc.value;
  } else {
    sortKey.value = key;
    sortAsc.value = true;
  }
};

const sortByItem = (key) => {
  if (sortKeyItem.value === key) {
    sortAscItem.value = !sortAscItem.value;
  } else {
    sortKeyItem.value = key;
    sortAscItem.value = true;
  }
};

const sortedCategoryFinances = computed(() => {
  const sorted = Object.entries(categoryFinances.value).sort((a, b) => {
    const [keyA, valueA] = a;
    const [keyB, valueB] = b;

    if (sortKey.value === 'category' || sortKey.value === 'category_id') {
      if (keyA < keyB) return sortAsc.value ? -1 : 1;
      if (keyA > keyB) return sortAsc.value ? 1 : -1;
      return 0;
    } else {
      if (valueA[sortKey.value] < valueB[sortKey.value]) return sortAsc.value ? -1 : 1;
      if (valueA[sortKey.value] > valueB[sortKey.value]) return sortAsc.value ? 1 : -1;
      return 0;
    }
  });

  return Object.fromEntries(sorted);
});

const sortedItemFinances = computed(() => {
  const sorted = Object.entries(itemFinances.value).sort((a, b) => {
    const [keyA, valueA] = a;
    const [keyB, valueB] = b;

    if (sortKeyItem.value === 'item' || sortKeyItem.value === 'item_id') {
      if (keyA < keyB) return sortAscItem.value ? -1 : 1;
      if (keyA > keyB) return sortAscItem.value ? 1 : -1;
      return 0;
    } else {
      if (valueA[sortKeyItem.value] < valueB[sortKeyItem.value]) return sortAscItem.value ? -1 : 1;
      if (valueA[sortKeyItem.value] > valueB[sortKeyItem.value]) return sortAscItem.value ? 1 : -1;
      return 0;
    }
  });

  return Object.fromEntries(sorted);
});

const totalCategoryEarnedWithVat = computed(() => {
  return Object.values(categoryFinances.value).reduce((sum, finance) => sum + finance.total_earned_with_vat, 0);
});

const totalCategoryVat = computed(() => {
  return Object.values(categoryFinances.value).reduce((sum, finance) => sum + finance.total_vat, 0);
});

const totalCategoryEarnedWithoutVat = computed(() => {
  return Object.values(categoryFinances.value).reduce((sum, finance) => sum + finance.total_earned_without_vat, 0);
});

const totalItemEarnedWithVat = computed(() => {
  return Object.values(itemFinances.value).reduce((sum, finance) => sum + finance.total_earned_with_vat, 0);
});

const totalItemVat = computed(() => {
  return Object.values(itemFinances.value).reduce((sum, finance) => sum + finance.total_vat, 0);
});

const totalItemEarnedWithoutVat = computed(() => {
  return Object.values(itemFinances.value).reduce((sum, finance) => sum + finance.total_earned_without_vat, 0);
});

onMounted(() => {
  successMessage.value = route.query.successMessage || '';
  getItems();
  fetchFinances();
});
</script>

<style scoped>
.wrapper {
  display: flex;
}

.content-wrapper {
  flex: 1;
  margin: 20px;
}

.card {
  margin-top: 20px;
}

.table-responsive {
  overflow-x: auto;
}

.table-auto {
  table-layout: auto;
  width: 100%;
}

.table-auto th,
.table-auto td {
  white-space: normal;
  word-wrap: break-word;
  overflow-wrap: break-word;
  max-width: 200px;
}

.tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}

.tabs button {
  padding: 10px 15px;
  cursor: pointer;
  border: none;
  background-color: #f0f0f0;
  border-radius: 5px;
}

.tabs button.active {
  background-color: #007bff;
  color: #fff;
}

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

th, td {
  padding: 8px;
  text-align: left;
  border: 1px solid #ddd;
}

th {
  cursor: pointer;
}

@media (max-width: 768px) {
  .card {
    margin-left: 10px;
    margin-right: 10px;
  }

  .checkbox-btn {
    align-self: flex-start;
  }

  .card-title {
    margin-bottom: 10px;
  }

  .btn-round {
    width: 100%;
    text-align: center;
  }

  .select-all-mobile {
    margin-bottom: 10px;
    display: flex;
    align-items: center;
  }

  .select-all-mobile input {
    margin-right: 10px;
  }
}

.card.mb-3 {
  margin-bottom: 1rem !important;
}

.card .card-body {
  display: flex;
  flex-direction: column;
}

.card .card-body .card-title {
  margin-bottom: 0.75rem;
}

.card .card-body .card-text {
  white-space: normal;
  word-wrap: break-word;
  overflow-wrap: break-word;
  margin-bottom: 0.75rem;
}

.card .card-body .d-flex {
  margin-top: auto;
}

.action-btns {
  display: flex;
  justify-content: flex-end;
}
</style>
