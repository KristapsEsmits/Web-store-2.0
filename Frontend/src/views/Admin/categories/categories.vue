<template>
  <div class="wrapper">
    <div class="content-wrapper">
      <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
        <span>{{ successMessage }}</span>
        <button class="btn-close" type="button" @click="dismissSuccessMessage"></button>
      </div>
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Categories
            <router-link class="btn btn-primary btn-round btn-fill float-end excel-btn" to="/admin/categories/create">
              Add Category
            </router-link>
            <button :disabled="!isAnyRowSelected" class="btn btn-warning btn-round btn-fill float-end excel-btn"
                    @click="exportSelectedRows">Export Selected Rows
            </button>
          </h4>
        </div>
        <div class="card-body">
          <div class="table-responsive d-none d-md-block">
            <table class="table table-bordered table-auto">
              <thead>
              <tr>
                <th><input v-model="selectAll" type="checkbox" @change="toggleSelectAll"></th>
                <th>Category ID</th>
                <th>Category Name</th>
                <th>Actions</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(category, index) in categories" :key="index">
                <td><input v-model="selectedRows" :value="category" type="checkbox"></td>
                <td>{{ category.id }}</td>
                <td>{{ category.category_name }}</td>
                <td class="d-flex justify-content-center">
                  <router-link :to="{ path: '/admin/categories/' + category.id + '/edit' }"
                               class="btn btn-success me-2">Edit
                  </router-link>
                  <button class="btn btn-danger" type="button" @click="deleteTest(category.id)">Delete</button>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="d-block d-md-none">
            <div class="select-all-mobile">
              <input v-model="selectAll" type="checkbox" @change="toggleSelectAll"> Select All
            </div>
            <div v-for="(category, index) in categories" :key="index" class="card mb-3">
              <div class="card-body">
                <h5 class="card-title"><input v-model="selectedRows" :value="category" class="checkbox-btn"
                                              type="checkbox">{{ category.category_name }}</h5>
                <p class="card-text"><strong>ID:</strong> {{ category.id }}</p>
                <div class="action-btns">
                  <router-link :to="{ path: '/admin/categories/' + category.id + '/edit' }"
                               class="btn btn-success me-2">Edit
                  </router-link>
                  <button class="btn btn-danger" type="button" @click="deleteTest(category.id)">Delete</button>
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
import {computed, onMounted, ref} from 'vue';
import axios from 'axios';
import {useRoute} from 'vue-router';
import * as XLSX from 'xlsx';

const categories = ref([]);
const successMessage = ref('');
const route = useRoute();
const selectedRows = ref([]);
const selectAll = ref(false);

const getCategories = async () => {
  try {
    const res = await axios.get('/categories');
    categories.value = res.data.categories;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const deleteTest = async (categoryId) => {
  if (confirm('Are you sure?')) {
    try {
      const res = await axios.delete(`/categories/${categoryId}/delete`);
      successMessage.value = res.data.message;
      await getCategories();
    } catch (error) {
      if (error.response && error.response.status === 422) {
        console.error('Validation errors:', error.response.data.errors);
      } else {
        console.error('Error deleting category:', error.message);
      }
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

.excel-btn {
  margin-right: 10px;
  margin-top: 10px;
  max-width: 98%;
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

.card .card-body .d-flex {
  margin-top: auto;
}

.action-btns {
  display: flex;
  justify-content: flex-end;
}
</style>
