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
            <input v-model="searchQuery" class="form-control me-2" placeholder="Search by ID or Name" style="width: 200px;" type="text" @input="searchItems" />
            <router-link class="btn btn-primary btn-round btn-fill me-2" to="/admin/items/create">
              Add Item
            </router-link>
            <button :disabled="!isAnyRowSelected" class="btn btn-warning btn-round btn-fill" @click="exportSelectedRows">Export Selected Rows</button>
          </div>
        </div>
        <div class="card-body">
          <ul id="myTab" class="nav nav-tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a id="items-tab" :class="{ active: activeTab === 'items' }" aria-controls="items" aria-selected="activeTab === 'items'" class="nav-link" data-bs-toggle="tab" href="#items" role="tab" @click="switchTab('items')">Items</a>
            </li>
            <li class="nav-item" role="presentation">
              <a id="specifications-tab" :class="{ active: activeTab === 'specifications' }" aria-controls="specifications" aria-selected="activeTab === 'specifications'" class="nav-link" data-bs-toggle="tab" href="#specifications" role="tab" @click="switchTab('specifications')">Specification Descriptions</a>
            </li>
          </ul>
          <div id="myTabContent" class="tab-content">
            <div id="items" :class="{ show: activeTab === 'items', active: activeTab === 'items' }" aria-labelledby="items-tab" class="tab-pane fade" role="tabpanel">
              <!-- Items Table -->
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
                        <img :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image" style="max-width: 90px; max-height: 70px;">
                      </td>
                      <td class="justify-content-center">
                        <router-link :to="{ path: '/admin/items/' + item.id + '/edit' }" class="btn btn-success me-2">Edit</router-link>
                        <button class="btn btn-danger" type="button" @click="openDeleteModal(item.id)">Delete</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- Mobile View for Items -->
              <div class="d-block d-md-none">
                <div class="select-all-mobile">
                  <input v-model="selectAll" type="checkbox" @change="toggleSelectAll"> Select All
                </div>
                <div v-for="(item, index) in filteredItems" :key="index" class="card mb-3">
                  <div class="card-body">
                    <h5 class="card-title"><input v-model="selectedRows" :value="item" class="checkbox-btn" type="checkbox">{{ item.name }}</h5>
                    <p class="card-text"><strong>ID:</strong> {{ item.id }}</p>
                    <p class="card-text"><strong>Description:</strong> {{ item.description }}</p>
                    <p class="card-text"><strong>Price:</strong> {{ item.price }}€</p>
                    <p class="card-text"><strong>Img path:</strong> {{ item.img }}</p>
                    <img :src="'http://localhost:8000/storage/uploads/' + item.img" alt="Item Image" style="max-width: 90px; max-height: 70px;">
                    <div class="action-btns">
                      <router-link :to="{ path: '/admin/items/' + item.id + '/edit' }" class="btn btn-success me-2">Edit</router-link>
                      <button class="btn btn-danger" type="button" @click="openDeleteModal(item.id)">Delete</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="specifications" :class="{ show: activeTab === 'specifications', active: activeTab === 'specifications' }" aria-labelledby="specifications-tab" class="tab-pane fade" role="tabpanel">
              <!-- Sub Tabs for Categories -->
              <ul id="categoryTabs" class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation" v-for="category in categories" :key="category.id">
                  <a :class="{ active: selectedCategoryId === category.id }" class="nav-link" role="tab" @click="selectCategory(category.id)">{{ category.category_name }}</a>
                </li>
              </ul>
              <div v-if="subcategories.length" class="mt-3">
                <h5>Subcategories</h5>
                <ul class="nav nav-pills">
                  <li v-for="subcategory in subcategories" :key="subcategory.id" class="nav-item">
                    <a :class="{ 'nav-link active': selectedSubcategoryId === subcategory.id, 'nav-link': selectedSubcategoryId !== subcategory.id }" @click="selectSubcategory(subcategory.id)">{{ subcategory.name }}</a>
                  </li>
                </ul>
              </div>
              <!-- Specifications Table -->
              <div class="table-responsive">
                <table class="table table-bordered table-auto">
                  <thead>
                    <tr>
                      <th><input v-model="selectAll" type="checkbox" @change="toggleSelectAll"></th>
                      <th>Item ID</th>
                      <th>Item Name</th>
                      <th v-for="title in uniqueSpecificationTitles" :key="title.id">{{ title.specification_title }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="group in groupedSpecifications" :key="group.item.id">
                      <td><input v-model="selectedRows" :value="group.item" type="checkbox"></td>
                      <td>{{ group.item.id }}</td>
                      <td>{{ group.item.name }}</td>
                      <td v-for="title in uniqueSpecificationTitles" :key="title.id">{{ getDescription(group.specifications, title.id) }}</td>
                    </tr>
                  </tbody>
                </table>
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
import { computed, onMounted, ref, watch } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';
import * as XLSX from 'xlsx';
import Modal from '@/components/Modal.vue';

const items = ref([]);
const specifications = ref([]);
const categories = ref([]);
const subcategories = ref([]);
const successMessage = ref('');
const route = useRoute();
const selectedRows = ref([]);
const selectAll = ref(false);
const itemIdToDelete = ref(null);
const isDeleteModalOpen = ref(false);
const searchQuery = ref('');
const activeTab = ref('items');
const selectedCategoryId = ref(null);
const selectedSubcategoryId = ref(null);

const getItems = async () => {
  const res = await axios.get('/items');
  items.value = res.data.items;
};

const getSpecifications = async () => {
  const res = await axios.get('/specification-descriptions');
  specifications.value = res.data.specifications;
};

const getCategories = async () => {
  try {
    const res = await axios.get('/categories');
    categories.value = res.data.categories;
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const openDeleteModal = (id) => {
  itemIdToDelete.value = id;
  isDeleteModalOpen.value = true;
};

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false;
};

const confirmDelete = async () => {
  try {
    if (activeTab.value === 'items') {
      const res = await axios.delete(`/items/${itemIdToDelete.value}/delete`);
      successMessage.value = res.data.message;
      await getItems();
    } else {
      const res = await axios.delete(`/specification-descriptions/${itemIdToDelete.value}/delete`);
      successMessage.value = res.data.message;
      await getSpecifications();
    }
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
  let data = [];

  if (activeTab.value === 'items') {
    data = selectedRows.value.map(row => ({
      id: row.id,
      name: row.name,
      description: row.description,
      price: row.price,
      img: row.img
    }));
  } else {
    data = groupedSpecifications.value.map(group => {
      let rowData = {
        'Item ID': group.item.id,
        'Item Name': group.item.name
      };

      uniqueSpecificationTitles.value.forEach(title => {
        rowData[title.specification_title] = getDescription(group.specifications, title.id);
      });

      return rowData;
    });
  }

  const worksheet = XLSX.utils.json_to_sheet(data);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, activeTab.value === 'items' ? 'Selected Items' : 'Selected Specifications');
  XLSX.writeFile(workbook, activeTab.value === 'items' ? 'selected_items.xlsx' : 'selected_specifications.xlsx');
};

const isAnyRowSelected = computed(() => selectedRows.value.length > 0);

const toggleSelectAll = () => {
  if (selectAll.value) {
    if (activeTab.value === 'items') {
      selectedRows.value = [...items.value];
    } else {
      selectedRows.value = groupedSpecifications.value.reduce((acc, group) => {
        acc.push(group.item);
        return acc;
      }, []);
    }
  } else {
    selectedRows.value = [];
  }
};

const filteredItems = computed(() => {
  return items.value.filter(item => {
    return item.id.toString().includes(searchQuery.value) || item.name.toLowerCase().includes(searchQuery.value.toLowerCase());
  });
});

const filteredDescriptions = computed(() => {
  return specifications.value.filter(description => {
    const categoryId = description.specification_title?.category_id;
    return categoryId === getCategoryID(selectedCategoryId.value) &&
        (description.id.toString().includes(searchQuery.value) || description.description.toLowerCase().includes(searchQuery.value.toLowerCase()));
  });
});

const groupedSpecifications = computed(() => {
  const filteredSpecs = specifications.value.filter(spec => {
    return spec.specification_title.category_id === getCategoryID(selectedCategoryId.value) &&
        (spec.item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            spec.item.id.toString().includes(searchQuery.value));
  });

  const grouped = filteredSpecs.reduce((acc, spec) => {
    const itemId = spec.item_id;
    if (!acc[itemId]) {
      acc[itemId] = {
        item: spec.item,
        specifications: []
      };
    }
    acc[itemId].specifications.push(spec);
    return acc;
  }, {});
  return Object.values(grouped);
});

const uniqueSpecificationTitles = computed(() => {
  const titles = {};
  specifications.value.forEach(spec => {
    if (spec.specification_title.category_id === getCategoryID(selectedCategoryId.value)) {
      if (!titles[spec.specification_title.id]) {
        titles[spec.specification_title.id] = spec.specification_title;
      }
    }
  });
  return Object.values(titles);
});

const getDescription = (specifications, titleId) => {
  const spec = specifications.find(spec => spec.specification_title_id === titleId);
  return spec ? spec.description : '';
};

const getCategoryID = (categoryId) => {
  const category = categories.value.find(cat => cat.id === categoryId);
  return category ? category.id : null;
};

const searchItems = async () => {
  try {
    if (activeTab.value === 'items') {
      const res = await axios.get('/items', { params: { search: searchQuery.value } });
      items.value = res.data.items;
    } else {
      const res = await axios.get('/specification-descriptions', { params: { search: searchQuery.value } });
      specifications.value = res.data.specifications;
    }
  } catch (error) {
    console.error('Error searching items:', error);
  }
};

const switchTab = (tab) => {
  activeTab.value = tab;
  searchQuery.value = '';
  selectedRows.value = [];
  selectAll.value = false;
  if (activeTab.value === 'specifications') {
    getSpecifications();
  }
};

const selectCategory = (categoryId) => {
  selectedCategoryId.value = categoryId;
  selectedSubcategoryId.value = null;
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

onMounted(() => {
  successMessage.value = route.query.successMessage || '';
  getItems();
  getSpecifications();
  getCategories();
});

watch(activeTab, () => {
  if (activeTab.value === 'specifications') {
    getSpecifications();
  }
});

watch(selectedCategoryId, () => {
  if (activeTab.value === 'specifications') {
    getSpecifications();
  }
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
  font-size: 1.25rem;
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
