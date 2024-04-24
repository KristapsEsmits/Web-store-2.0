<script>
import axios from 'axios';

export default {
  name: 'Create',
  data() {
    return {
      errorList: {},
      model: {
        item: {
          name: '',
          description: '',
          price: '',
          brand_id: null,
          categories_id: null,
          img: null,
        },
      },
      brands: [],
      categories: [],
    };
  },
  methods: {
    handleImageUpload(event) {
      this.model.item.img = event.target.files[0];
    },

    async saveItem() {
      const formData = new FormData();
      formData.append('name', this.model.item.name);
      formData.append('description', this.model.item.description);
      formData.append('price', this.model.item.price);
      formData.append('brand_id', this.model.item.brand_id); // Include 'brand_id'
      formData.append('categories_id', this.model.item.categories_id);
      formData.append('img', this.model.item.img);

      try {
        const res = await axios.post('/items', formData);
        const successMessage = res.data.message;
        this.$router.push('/admin/items?successMessage=' + successMessage);

        this.model.item.name = '';
        this.model.item.description = '';
        this.model.item.price = '';
        this.model.item.brand_id = null;
        this.model.item.img = null;

        this.errorList = {};
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errorList = error.response.data.errors;
        } else if (error.request) {
          console.log(error.request);
        } else {
          console.log('Error', error.message);
        }
      }
    },


    Exit() {
      this.$router.push('/admin/items');
    },

    async getBrands() {
      try {
        const res = await axios.get('/brands');
        this.brands = res.data.brands;
      } catch (error) {
        console.error('Error fetching brands:', error);
      }
    },

    async getCategories() {
      try {
        const res = await axios.get('/categories');
        this.categories = res.data.categories;
      } catch (error) {
        console.error('Error fetching categories:', error);
      }
    },
  },
  mounted() {
    this.getBrands();
    this.getCategories();
  },
};
</script>


<template>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header">
        <h4>Add Item</h4>
      </div>
      <div class="card-body">
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ error[0] }}
          </li>
        </ul>
        <div class="input-group-prepend mb-3">
          <label for="Brand">Category</label>
          <select v-model="model.item.categories_id" class="custom-select form-control">
            <option v-for="categories in categories" :key="categories.id" :value="categories.id">
              {{ categories.category_name }}
            </option>
          </select>
        </div>
        <div class="input-group-prepend mb-3">
          <label for="Brand">Brand</label>
          <select v-model="model.item.brand_id" class="custom-select form-control">
            <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="Name">Name</label>
          <input v-model="model.item.name" class="form-control" type="text"/>
        </div>
        <div class="mb-3">
          <label for="Name">Description</label>
          <input v-model="model.item.description" class="form-control" type="text"/>
        </div>
        <div class="mb-3">
          <label for="Name">Price</label>
          <input v-model="model.item.price" class="form-control" type="text"/>
        </div>
        <div class="mb-3">
          <label for="Image">Image</label>
          <input class="form-control" type="file" @change="handleImageUpload"/>
        </div>
        <div class="mb-3">
          <button class="btn btn-primary" type="button" @click="saveItem">Save</button>
          <button class="btn btn-danger" type="button" @click="Exit">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>