<template>
  <div class="wrapper">
    <div class="content-wrapper">
      <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
        <span>{{ successMessage }}</span>
        <button class="btn-close" type="button" @click="dismissSuccessMessage"></button>
      </div>
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="card-title mb-0">Brands</h4>
          <div class="d-flex align-items-center">
            <input v-model="searchQuery" class="form-control me-2" placeholder="Search by ID or Name"
                   style="width: 200px;"
                   type="text" @input="searchBrands"/>
            <router-link class="btn btn-primary btn-round btn-fill me-2" to="/admin/brands/create">
              Add Brand
            </router-link>
            <button :disabled="!isAnyRowSelected" class="btn btn-warning btn-round btn-fill"
                    @click="exportSelectedRows">Export Selected Rows
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive d-none d-md-block">
            <table id="brands-table" class="table table-bordered table-auto">
              <thead>
              <tr>
                <th><input v-model="selectAll" type="checkbox" @change="toggleSelectAll"></th>
                <th>Brand ID</th>
                <th>Brand Name</th>
                <th>Brand Image Path</th>
                <th>Brand Image</th>
                <th>Item Count</th>
                <th>Actions</th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="(brand, index) in filteredBrands" :key="index">
                <td><input v-model="selectedRows" :value="brand" type="checkbox"></td>
                <td>{{ brand.id }}</td>
                <td>{{ brand.name }}</td>
                <td>{{ brand.img }}</td>
                <td class="image-cell">
                  <img :src="'http://localhost:8000/storage/uploads/' + brand.img" alt="Brand Image"
                       style="max-width: 90px; max-height: 70px;">
                </td>
                <td>{{ brand.items_count }}</td>
                <td class="justify-content-center">
                  <router-link :to="{ path: '/admin/brands/' + brand.id + '/edit' }" class="btn btn-success me-2">Edit
                  </router-link>
                  <button :disabled="brand.items_count > 0" class="btn btn-danger" type="button"
                          @click="openDeleteModal(brand.id)">Delete
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
            <div v-for="(brand, index) in filteredBrands" :key="index" class="card mb-3">
              <div class="card-body">
                <h5 class="card-title"><input v-model="selectedRows" :value="brand" class="checkbox-btn"
                                              type="checkbox">{{ brand.name }}</h5>
                <p class="card-text"><strong>ID:</strong> {{ brand.id }}</p>
                <p class="card-text"><strong>Image Path:</strong> {{ brand.img }}</p>
                <img :src="'http://localhost:8000/storage/uploads/' + brand.img" alt="Brand Image"
                     style="max-width: 90px; max-height: 70px;">
                <p class="card-text"><strong>Item Count:</strong> {{ brand.items_count }}</p>
                <div class="action-btns">
                  <router-link :to="{ path: '/admin/brands/' + brand.id + '/edit' }" class="btn btn-success me-2">Edit
                  </router-link>
                  <button :disabled="brand.items_count > 0" class="btn btn-danger" type="button"
                          @click="openDeleteModal(brand.id)">Delete
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

const brands = ref([]);
const successMessage = ref('');
const route = useRoute();
const selectedRows = ref([]);
const selectAll = ref(false);
const brandIdToDelete = ref(null);
const isDeleteModalOpen = ref(false);
const searchQuery = ref('');

const getBrands = async () => {
  try {
    const res = await axios.get('/brands');
    brands.value = res.data.brands;
  } catch (error) {
    console.error('Error fetching brands:', error);
  }
};

const openDeleteModal = (brandId) => {
  brandIdToDelete.value = brandId;
  isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false;
};

const confirmDelete = async () => {
  try {
    const res = await axios.delete(`/brands/${brandIdToDelete.value}/delete`);
    successMessage.value = res.data.message;
    await getBrands();
    closeDeleteModal();
  } catch (error) {
    if (error.response && error.response.status === 422) {
      console.error('Validation errors:', error.response.data.errors);
    } else {
      console.error('Error deleting brand:', error.message);
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

const filteredBrands = computed(() => {
  return brands.value.filter(brand => {
    return brand.id.toString().includes(searchQuery.value) || brand.name.toLowerCase().includes(searchQuery.value.toLowerCase());
  });
});

const searchBrands = async () => {
  try {
    const res = await axios.get('/brands', {params: {search: searchQuery.value}});
    brands.value = res.data.brands;
  } catch (error) {
    console.error('Error searching brands:', error);
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
