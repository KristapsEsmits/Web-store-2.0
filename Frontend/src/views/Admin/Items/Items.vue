<template>
  <div class="wrapper">
    <div class="content-wrapper">
      <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
        <span>{{ successMessage }}</span>
        <button class="btn-close" type="button" @click="dismissSuccessMessage"></button>
      </div>
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Items
            <router-link class="btn btn-primary btn-round btn-fill float-end add_btn" to="/admin/items/create">Add
              Item
            </router-link>
          </h4>
        </div>
        <div class="card-body">
          <div class="table-responsive d-none d-md-block">
            <table class="table table-bordered table-auto">
              <thead>
              <tr>
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
              <tr v-for="(item, index) in items" :key="index">
                <td>{{ item.id }}</td>
                <td>{{ item.name }}</td>
                <td>{{ item.description }}</td>
                <td>{{ item.price }}</td>
                <td>{{ item.img }}</td>
                <td class="image-cell">
                  <img :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image"
                       style="max-width: 90px; max-height: 70px;">
                </td>
                <td class="d-flex justify-content-center">
                  <router-link :to="{ path: '/admin/items/' + item.id + '/edit' }" class="btn btn-success me-2">Edit
                  </router-link>
                  <button class="btn btn-danger" type="button" @click="deleteItems(item.id)">Delete</button>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="d-block d-md-none">
            <div v-for="(item, index) in items" :key="index" class="card mb-3">
              <div class="card-body">
                <h5 class="card-title">{{ item.name }}</h5>
                <p class="card-text">ID: {{ item.id }}</p>
                <p class="card-text">Description: {{ item.description }}</p>
                <p class="card-text">Price: {{ item.price }}</p>
                <p class="card-text">Img path: {{ item.img }}</p>
                <img :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image"
                     style="max-width: 90px; max-height: 70px;">
                <div class="action-btns">
                  <router-link :to="{ path: '/admin/items/' + item.id + '/edit' }" class="btn btn-success me-2">Edit
                  </router-link>
                  <button class="btn btn-danger" type="button" @click="deleteItems(item.id)">Delete</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import axios from 'axios';
import {useRoute} from 'vue-router';

const items = ref([]);
const successMessage = ref('');
const route = useRoute();

const getItems = async () => {
  try {
    const res = await axios.get('/items');
    items.value = res.data.items;
  } catch (error) {
    console.error('Error fetching items:', error);
  }
};

const deleteItems = async (itemId) => {
  if (confirm('Are you sure?')) {
    try {
      const res = await axios.delete(`/items/${itemId}/delete`);
      successMessage.value = res.data.message;
      getItems();
    } catch (error) {
      if (error.response && error.response.status === 422) {
        console.error('Validation errors:', error.response.data.errors);
      } else {
        console.error('Error deleting item:', error.message);
      }
    }
  }
};

const dismissSuccessMessage = () => {
  successMessage.value = '';
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
  white-space: nowrap;
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

  .card-header {
    display: flex;
    flex-direction: column;
    align-items: start;
  }

  .card-title {
    margin-bottom: 10px;
  }

  .btn-round {
    width: 100%;
    text-align: center;
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
  font-size: 1.25rem;
  margin-bottom: 0.75rem;
}

.card .card-body .card-text {
  margin-bottom: 0.75rem;
}

.card .card-body .d-flex {
  margin-top: auto;
}

.add_btn {
  margin-top: 10px;
}

.action-btns {
  display: flex;
  justify-content: flex-end;
}
</style>
