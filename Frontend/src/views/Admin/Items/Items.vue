<template>
  <div class="wrapper">
    <div class="content-wrapper">
      <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
        <span>{{ successMessage }}</span>
        <button class="btn-close" type="button" @click="dismissSuccessMessage"></button>
      </div>
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title mb-0">Items</h4>
          <div class="d-flex align-items-center">
            <input v-model="searchQuery" class="form-control me-2" placeholder="Search by ID or Name" style="width: 200px;"
                   type="text" @input="searchItems"/>
            <router-link class="btn btn-primary btn-round btn-fill me-2" to="/admin/items/create">
              Add Item
            </router-link>
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
                <th>Item ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Img path</th>
                <th>Img</th>
                <th>Actions</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(item, index) in filteredItems" :key="index">
                <td><input v-model="selectedRows" :value="item" type="checkbox"></td>
                <td>{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.description }}</td>
                <td>{{ item.price }}€</td>
                <td>{{ item.img }}</td>
                <td class="image-cell">
                  <img :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image"
                       style="max-width: 90px; max-height: 70px;">
                </td>
                <td class="justify-content-center">
                  <router-link :to="{ path: '/admin/items/' + item.id + '/edit' }" class="btn btn-success me-2">Edit
                  </router-link>
                  <button class="btn btn-danger" type="button" @click="openDeleteModal(item.id)">Delete</button>
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
                <p class="card-text"><strong>ID:</strong> {{ item.id }}</p>
                <p class="card-text"><strong>Description:</strong> {{ item.description }}</p>
                <p class="card-text"><strong>Price:</strong> {{ item.price }}€</p>
                <p class="card-text"><strong>Img path:</strong> {{ item.img }}</p>
                <img :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image"
                     style="max-width: 90px; max-height: 70px;">
                <div class="action-btns">
                  <router-link :to="{ path: '/admin/items/' + item.id + '/edit' }" class="btn btn-success me-2">Edit
                  </router-link>
                  <button class="btn btn-danger" type="button" @click="openDeleteModal(item.id)">Delete</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <Modal :isOpen="isDeleteModalOpen" title="Confirm Delete" @close="closeDeleteModal" @confirm="confirmDelete">
        Are you sure you want to delete this item?
      </Modal>
    </div>
  </div>
</template>

<script setup>
import {computed, onMounted, ref} from 'vue';
import axios from 'axios';
import {useRoute} from 'vue-router';
import * as XLSX from 'xlsx';
import Modal from '@/components/Modal.vue';

const items = ref([]);
const successMessage = ref('');
const route = useRoute();
const selectedRows = ref([]);
const selectAll = ref(false);
const itemIdToDelete = ref(null);
const isDeleteModalOpen = ref(false);
const searchQuery = ref('');

const getItems = async () => {
  try {
    const res = await axios.get('/items');
    items.value = res.data.items;
  } catch (error) {
    console.error('Error fetching items:', error);
  }
};

const openDeleteModal = (itemId) => {
  itemIdToDelete.value = itemId;
  isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false;
};

const confirmDelete = async () => {
  try {
    const res = await axios.delete(`/items/${itemIdToDelete.value}/delete`);
    successMessage.value = res.data.message;
    await getItems();
    closeDeleteModal();
  } catch (error) {
    if (error.response && error.response.status === 422) {
      console.error('Validation errors:', error.response.data.errors);
    } else {
      console.error('Error deleting item:', error.message);
    }
  }
};

const dismissSuccessMessage = () => {
  successMessage.value = '';
};

const exportSelectedRows = () => {
  const data = selectedRows.value.map(item => ({
    id: item.id,
    name: item.name,
    description: item.description,
    price: item.price,
    img: item.img
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

onMounted(() => {
  successMessage.value = route.query.successMessage || '';
  getItems();
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

.image-cell img {
  max-width: 90px;
  max-height: 70px;
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

.card .card-body {
  margin-top: auto;
}

.action-btns {
  display: flex;
  justify-content: flex-end;
}
</style>
