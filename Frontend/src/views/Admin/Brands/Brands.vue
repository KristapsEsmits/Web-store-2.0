<template>
  <div class="wrapper">
    <div class="content-wrapper">
      <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
        <span>{{ successMessage }}</span>
        <button class="btn-close" type="button" @click="dismissSuccessMessage"></button>
      </div>
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Brands
            <router-link class="btn btn-primary btn-round btn-fill float-end excel-btn" to="/admin/brands/create">Add
              Brand
            </router-link>
            <button class="btn btn-warning btn-round btn-fill float-end excel-btn" @click="exportSelectedRows" :disabled="!isAnyRowSelected">Export Selected Rows</button>
          </h4>
        </div>
        <div class="card-body">
          <div class="table-responsive d-none d-md-block">
            <table class="table table-bordered table-auto" id="brands-table">
              <thead>
              <tr>
                <th><input type="checkbox" @change="toggleSelectAll" v-model="selectAll"></th>
                <th>Brand ID</th>
                <th>Brand Name</th>
                <th>Brand Image Path</th>
                <th>Brand Image</th>
                <th>Actions</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(brand, index) in brands" :key="index">
                <td><input type="checkbox" v-model="selectedRows" :value="brand"></td>
                <td>{{ brand.id }}</td>
                <td>{{ brand.name }}</td>
                <td>{{ brand.img }}</td>
                <td class="image-cell">
                  <img :src="'http://localhost:8000/storage/uploads/' + brand.img" alt="Brand Image"
                       style="max-width: 90px; max-height: 70px;">
                </td>
                <td class="d-flex justify-content-center">
                  <router-link :to="{ path: '/admin/brands/' + brand.id + '/edit' }" class="btn btn-success me-2">Edit
                  </router-link>
                  <button class="btn btn-danger" type="button" @click="deleteBrands(brand.id)">Delete</button>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="d-block d-md-none">
            <div class="select-all-mobile">
              <input type="checkbox" @change="toggleSelectAll" v-model="selectAll"> Select All
            </div>
            <div v-for="(brand, index) in brands" :key="index" class="card mb-3">
              <div class="card-body">
                <h5 class="card-title"> <input class="checkbox-btn" type="checkbox" v-model="selectedRows" :value="brand">{{ brand.name }}</h5>
                <p class="card-text"><strong>ID:</strong> {{ brand.id }}</p>
                <p class="card-text"><strong>Image Path:</strong> {{ brand.img }}</p>
                <img :src="'http://localhost:8000/storage/uploads/' + brand.img" alt="Brand Image"
                     style="max-width: 90px; max-height: 70px;">
                <div class="action-btns">
                  <router-link :to="{ path: '/admin/brands/' + brand.id + '/edit' }" class="btn btn-success me-2">Edit
                  </router-link>
                  <button class="btn btn-danger" type="button" @click="deleteBrands(brand.id)">Delete</button>
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
import { onMounted, ref, computed } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';
import * as XLSX from 'xlsx';

const brands = ref([]);
const successMessage = ref('');
const route = useRoute();
const selectedRows = ref([]);
const selectAll = ref(false);

const getBrands = async () => {
  try {
    const res = await axios.get('/brands');
    brands.value = res.data.brands;
  } catch (error) {
    console.error('Error fetching brands:', error);
  }
};

const deleteBrands = async (brandId) => {
  if (confirm('Are you sure?')) {
    try {
      const res = await axios.delete(`/brands/${brandId}/delete`);
      successMessage.value = res.data.message;
      getBrands();
    } catch (error) {
      if (error.response && error.response.status === 422) {
        console.error('Validation errors:', error.response.data.errors);
      } else {
        console.error('Error deleting brand:', error.message);
      }
    }
  }
};

const dismissSuccessMessage = () => {
  successMessage.value = '';
};

const exportSelectedRows = () => {
  const data = selectedRows.value.map(brand => ({
    id: brand.id,
    name: brand.name,
    imgPath: brand.img
  }));

  const worksheet = XLSX.utils.json_to_sheet(data);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, 'Selected Brands');
  XLSX.writeFile(workbook, 'selected_brands.xlsx');
};

const isAnyRowSelected = computed(() => selectedRows.value.length > 0);

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedRows.value = [...brands.value];
  } else {
    selectedRows.value = [];
  }
};

onMounted(() => {
  successMessage.value = route.query.successMessage || '';
  getBrands();
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
  font-size: 1.25rem;
  margin-bottom: 0.75rem;
}

.checkbox-btn {
  align-self: flex-start;
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
