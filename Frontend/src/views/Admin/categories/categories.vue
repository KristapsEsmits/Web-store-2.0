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
            <input v-model="searchQuery" class="form-control me-2" placeholder="Search by ID or Name" style="width: 200px;" type="text" @input="searchItems"/>
            <router-link :to="buttonLink" class="btn btn-primary btn-round btn-fill me-2">{{ buttonText }}</router-link>
            <button :disabled="!isAnyRowSelected" class="btn btn-warning btn-round btn-fill" @click="exportSelectedRows">Export Selected Rows</button>
          </div>
        </div>
        <div class="card-body">
          <ul id="myTab" class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a id="categories-tab" :class="{ active: activeTab === 'categories' }" aria-controls="categories" aria-selected="activeTab === 'categories'" class="nav-link" data-bs-toggle="tab" href="#categories" role="tab" @click="switchTab('categories')">Categories</a>
            </li>
            <li class="nav-item" role="presentation">
              <a id="specification-titles-tab" :class="{ active: activeTab === 'specification-titles' }" aria-controls="specification-titles" aria-selected="activeTab === 'specification-titles'" class="nav-link" data-bs-toggle="tab" href="#specification-titles" role="tab" @click="switchTab('specification-titles')">Specification Titles</a>
            </li>
          </ul>
          <div id="myTabContent" class="tab-content">
            <div id="categories" :class="{ show: activeTab === 'categories', active: activeTab === 'categories' }" aria-labelledby="categories-tab" class="tab-pane fade" role="tabpanel">
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
                      <router-link :to="{ path: '/admin/categories/' + category.id + '/edit' }" class="btn btn-success me-2">Edit</router-link>
                      <button :disabled="category.items_count > 0" class="btn btn-danger" type="button" @click="openDeleteModal(category.id)">Delete</button>
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
                    <h5 class="card-title">
                      <input v-model="selectedRows" :value="category" class="checkbox-btn" type="checkbox">
                      {{ category.category_name }}
                    </h5>
                    <p class="card-text"><strong>ID:</strong> {{ category.id }}</p>
                    <p class="card-text"><strong>Item Count:</strong> {{ category.items_count }}</p>
                    <div class="action-btns">
                      <router-link :to="{ path: '/admin/categories/' + category.id + '/edit' }" class="btn btn-success me-2">Edit</router-link>
                      <button :disabled="category.items_count > 0" class="btn btn-danger" type="button" @click="openDeleteModal(category.id)">Delete</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="specification-titles" :class="{ show: activeTab === 'specification-titles', active: activeTab === 'specification-titles' }" aria-labelledby="specification-titles-tab" class="tab-pane fade" role="tabpanel">
              <ul id="categoryTabs" class="nav nav-tabs" role="tablist">
                <li v-for="(category, index) in categories" :key="index" class="nav-item" role="presentation">
                  <a :class="{ active: selectedCategoryId === category.id }" class="nav-link" role="tab" @click="selectCategory(category.id)">{{ category.category_name }}</a>
                </li>
              </ul>
              <div v-if="subcategories.length" class="mt-3">
                <h5>Subcategories</h5>
                <ul class="nav nav-pills">
                  <li v-for="(subcategory, index) in subcategories" :key="index" class="nav-item">
                    <a :class="{ 'nav-link active': selectedSubcategoryId === subcategory.id, 'nav-link': selectedSubcategoryId !== subcategory.id }" @click="selectSubcategory(subcategory.id)">{{ subcategory.name }}</a>
                  </li>
                </ul>
              </div>
              <div class="table-responsive d-none d-md-block mt-3">
                <table class="table table-bordered table-auto">
                  <thead>
                  <tr>
                    <th><input v-model="selectAll" type="checkbox" @change="toggleSelectAll"></th>
                    <th>Specification Title ID</th>
                    <th>Specification Title</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  <tr v-for="(title, index) in filteredSpecificationTitles" :key="index">
                    <td><input v-model="selectedRows" :value="title" type="checkbox"></td>
                    <td>{{ title.id }}</td>
                    <td>{{ title.specification_title }}</td>
                    <td class="justify-content-center">
                      <router-link :to="{ path: '/admin/specification-titles/' + title.id + '/edit' }" class="btn btn-success me-2">Edit</router-link>
                      <button class="btn btn-danger" type="button" @click="openDeleteSpecificationModal(title.id)">Delete</button>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <div class="d-block d-md-none mt-3">
                <div class="select-all-mobile">
                  <input v-model="selectAll" type="checkbox" @change="toggleSelectAll"> Select All
                </div>
                <div v-for="(title, index) in filteredSpecificationTitles" :key="index" class="card mb-3">
                  <div class="card-body">
                    <h5 class="card-title">
                      <input v-model="selectedRows" :value="title" class="checkbox-btn" type="checkbox">
                      {{ title.specification_title }}
                    </h5>
                    <p class="card-text"><strong>ID:</strong> {{ title.id }}</p>
                    <div class="action-btns">
                      <router-link :to="{ path: '/admin/specification-titles/' + title.id + '/edit' }" class="btn btn-success me-2">Edit</router-link>
                      <button class="btn btn-danger" type="button" @click="openDeleteSpecificationModal(title.id)">Delete</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <Modal :isOpen="isDeleteModalOpen" title="Confirm Delete" @close="closeDeleteModal" @confirm="confirmDelete">
        Are you sure you want to delete this Category?
      </Modal>
      <Modal :isOpen="isDeleteSpecificationModalOpen" title="Confirm Delete" @close="closeDeleteSpecificationModal" @confirm="confirmDeleteSpecification">
        Are you sure you want to delete this Specification Title?
      </Modal>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';
import * as XLSX from 'xlsx';
import Modal from '@/components/Modal.vue';

const categories = ref([]);
const specificationTitles = ref([]);
const subcategories = ref([]);
const successMessage = ref('');
const route = useRoute();
const selectedRows = ref([]);
const selectAll = ref(false);
const categoryIdToDelete = ref(null);
const specificationTitleIdToDelete = ref(null);
const isDeleteModalOpen = ref(false);
const isDeleteSpecificationModalOpen = ref(false);
const searchQuery = ref('');
const selectedCategoryId = ref(null);
const selectedSubcategoryId = ref(null);
const activeTab = ref('categories');

const getCategories = async () => {
  try {
    const res = await axios.get('/categories');
    categories.value = res.data.categories;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const getSpecificationTitles = async () => {
  try {
    const res = await axios.get('/specification-titles');
    specificationTitles.value = res.data.specification_titles;
  } catch (error) {
    console.error('Error fetching specification titles:', error);
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

const openDeleteSpecificationModal = (specificationTitleId) => {
  specificationTitleIdToDelete.value = specificationTitleId;
  isDeleteSpecificationModalOpen.value = true;
};

const closeDeleteSpecificationModal = () => {
  isDeleteSpecificationModalOpen.value = false;
};

const confirmDeleteSpecification = async () => {
  try {
    const res = await axios.delete(`/specification-titles/${specificationTitleIdToDelete.value}/delete`);
    successMessage.value = res.data.message;
    await getSpecificationTitles();
    closeDeleteSpecificationModal();
  } catch (error) {
    if (error.response && error.response.status === 422) {
      console.error('Validation errors:', error.response.data.errors);
    } else {
      console.error('Error deleting specification title:', error.message);
    }
  }
};

const dismissSuccessMessage = () => {
  successMessage.value = '';
};

const exportSelectedRows = () => {
  const data = selectedRows.value.map(row => {
    return activeTab.value === 'categories'
        ? {id: row.id, name: row.category_name}
        : {id: row.id, name: row.specification_title};
  });

  const worksheet = XLSX.utils.json_to_sheet(data);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, activeTab.value === 'categories' ? 'Selected Categories' : 'Selected Specification Titles');
  XLSX.writeFile(workbook, activeTab.value === 'categories' ? 'selected_categories.xlsx' : 'selected_specification_titles.xlsx');
};

const isAnyRowSelected = computed(() => selectedRows.value.length > 0);

const toggleSelectAll = () => {
  if (selectAll.value) {
    selectedRows.value = activeTab.value === 'categories'
        ? [...categories.value]
        : [...filteredSpecificationTitles.value];
  } else {
    selectedRows.value = [];
  }
};

const filteredCategories = computed(() => {
  return categories.value.filter(category => {
    return category.id.toString().includes(searchQuery.value) || category.category_name.toLowerCase().includes(searchQuery.value.toLowerCase());
  });
});

const filteredSpecificationTitles = computed(() => {
  return specificationTitles.value.filter(title => title.category_id === selectedCategoryId.value).filter(title => {
    return title.id.toString().includes(searchQuery.value) || title.specification_title.toLowerCase().includes(searchQuery.value.toLowerCase());
  });
});

const searchItems = async () => {
  if (activeTab.value === 'categories') {
    try {
      const res = await axios.get('/categories', {params: {search: searchQuery.value}});
      categories.value = res.data.categories;
    } catch (error) {
      console.error('Error searching categories:', error);
    }
  } else {
    try {
      const res = await axios.get('/specification-titles', {params: {search: searchQuery.value}});
      specificationTitles.value = res.data.specification_titles;
    } catch (error) {
      console.error('Error searching specification titles:', error);
    }
  }
};

const switchTab = (tab) => {
  activeTab.value = tab;
  searchQuery.value = '';
  selectedRows.value = [];
  selectAll.value = false;
};

const selectCategory = (categoryId) => {
  selectedCategoryId.value = categoryId;
  searchQuery.value = '';
  selectedRows.value = [];
  selectAll.value = false;
};

const selectSubcategory = (subcategoryId) => {
  selectedSubcategoryId.value = subcategoryId;
  searchQuery.value = '';
  selectedRows.value = [];
  selectAll.value = false;
};

const buttonText = computed(() => activeTab.value === 'categories' ? 'Add Category' : 'Add Specification Title');
const buttonLink = computed(() => activeTab.value === 'categories' ? '/admin/categories/create' : '/admin/specification-titles/create');

onMounted(() => {
  successMessage.value = route.query.successMessage || '';
  getCategories();
  getSpecificationTitles();
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
