<template>
  <div class="wrapper">
    <div class="content-wrapper">
      <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
        <span>{{ successMessage }}</span>
        <button class="btn-close" type="button" @click="dismissSuccessMessage"></button>
      </div>
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title mb-0">Categories</h4>
          <div class="d-flex align-items-center">
            <input v-model="searchQuery" class="form-control me-2" placeholder="Search by ID or Name" style="width: 200px;"
                   type="text" @input="searchCategories"/>
            <router-link class="btn btn-primary btn-round btn-fill me-2" to="/admin/categories/create">
              Add Category
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
                <th>Category ID</th>
                <th>Category Name</th>
                <th>Item Count</th>
                <th>Actions</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(category, index) in filteredCategories" :key="index">
                <td><input v-model="selectedRows" :value="category" type="checkbox"></td>
                <td>{{ category.id }}</td>
                <td>{{ category.category_name }}</td>
                <td>{{ category.items_count }}</td>
                <td class="justify-content-center">
                  <router-link :to="{ path: '/admin/categories/' + category.id + '/edit' }"
                               class="btn btn-success me-2">Edit
                  </router-link>
                  <button :disabled="category.items_count > 0" class="btn btn-danger" type="button"
                          @click="openDeleteModal(category.id)">Delete
                  </button>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="d-block d-md-none">
            <div class="select-all-mobile">
              <input v-model="selectAll" type="checkbox" @change="toggleSelectAll"> Select All
            </div>
            <div v-for="(category, index) in filteredCategories" :key="index" class="card mb-3">
              <div class="card-body">
                <h5 class="card-title"><input v-model="selectedRows" :value="category" class="checkbox-btn"
                                              type="checkbox">{{ category.category_name }}</h5>
                <p class="card-text"><strong>ID:</strong> {{ category.id }}</p>
                <p class="card-text"><strong>Item Count:</strong> {{ category.items_count }}</p>
                <div class="action-btns">
                  <router-link :to="{ path: '/admin/categories/' + category.id + '/edit' }"
                               class="btn btn-success me-2">Edit
                  </router-link>
                  <button :disabled="category.items_count > 0" class="btn btn-danger" type="button"
                          @click="openDeleteModal(category.id)">Delete
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <Modal :isOpen="isDeleteModalOpen" title="Confirm Delete" @close="closeDeleteModal" @confirm="confirmDelete">
        Are you sure you want to delete this Category?
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

const categories = ref([]);
const successMessage = ref('');
const route = useRoute();
const selectedRows = ref([]);
const selectAll = ref(false);
const categoryIdToDelete = ref(null);
const isDeleteModalOpen = ref(false);
const searchQuery = ref('');

const getCategories = async () => {
  try {
    const res = await axios.get('/categories');
    categories.value = res.data.categories;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const openDeleteModal = (categoryId) => {
  categoryIdToDelete.value = categoryId;
  isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false;
};

const confirmDelete = async () => {
  try {
    const res = await axios.delete(`/categories/${categoryIdToDelete.value}/delete`);
    successMessage.value = res.data.message;
    await getCategories();
    closeDeleteModal();
  } catch (error) {
    if (error.response && error.response.status === 422) {
      console.error('Validation errors:', error.response.data.errors);
    } else {
      console.error('Error deleting category:', error.message);
    }
  }
};

const dismissSuccessMessage = () => {
  successMessage.value = '';
};

const exportSelectedRows = () => {
  const data = selectedRows.value.map(category => ({
    id: category.id,
    name: category.category_name
  }));

  const worksheet = XLSX.utils.json_to_sheet(data);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Selected Categories');
  XLSX.writeFile(workbook, 'selected_categories.xlsx');
};

const isAnyRowSelected = computed(() => selectedRows.value.length > 0);

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedRows.value = [...categories.value];
  } else {
    selectedRows.value = [];
  }
};

const filteredCategories = computed(() => {
  return categories.value.filter(category => {
    return category.id.toString().includes(searchQuery.value) || category.category_name.toLowerCase().includes(searchQuery.value.toLowerCase());
  });
});

const searchCategories = async () => {
  try {
    const res = await axios.get('/categories', {params: {search: searchQuery.value}});
    categories.value = res.data.categories;
  } catch (error) {
    console.error('Error searching categories:', error);
  }
};

onMounted(() => {
  successMessage.value = route.query.successMessage || '';
  getCategories();
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

@media (max-width: 768px) {
  .card {
    margin-left: 10px;
    margin-right: 10px;
  }

  .card-title {
    margin-bottom: 10px;
  }

  .btn-round {
    width: 100%;
    text-align: center;
  }

  .checkbox-btn {
    align-self: flex-start;
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
  font-size: 1.25rem;
  margin-bottom: 0.75rem;
}

.card .card-body .card-text {
  margin-bottom: 0.75rem;
}
</style>
