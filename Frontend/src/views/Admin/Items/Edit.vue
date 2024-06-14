<script>
import axios from 'axios';

export default {
  name: 'EditItem',
  data() {
    return {
      itemId: '',
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

  mounted() {
    this.itemId = this.$route.params.id;
    this.getItemData(this.itemId);
    this.getBrands();
    this.getCategories();
  },

  methods: {
    getItemData(itemId) {
      axios.get(`/items/${itemId}/edit`)
          .then((res) => {
            console.log('Item data response:', res.data);
            if (res.data.items) {
              this.model.item = res.data.items;
            } else {
              console.error('Item data not found in response');
            }
          })
          .catch((error) => {
            console.error('Error fetching item data:', error);
            if (error.response && error.response.status === 404) {
              this.$router.push('/items');
            }
          });
    },

    handleImageUpload(event) {
      const file = event.target.files[0];
      const reader = new FileReader();
      reader.onload = (e) => {
        this.model.item.img = e.target.result;
      };
      reader.readAsDataURL(file);
    },

    async updateItem() {
      const data = {
        name: this.model.item.name,
        description: this.model.item.description,
        price: this.model.item.price,
        brand_id: this.model.item.brand_id,
        categories_id: this.model.item.categories_id,
      };

      if (this.model.item.img) {
        data.img = this.model.item.img;
      }

      try {
        const res = await axios.put(`/items/${this.itemId}/edit`, data, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          }
        });
        const successMessage = res.data.message;
        this.$router.push('/admin/items?successMessage=' + successMessage);
        this.errorList = {};
      } catch (error) {
        console.error('Error updating item:', error);
        if (error.response && error.response.status === 422) {
          this.errorList = error.response.data.errors;
        } else if (error.request) {
          console.log(error.request);
        } else {
          console.log('Error', error.message);
        }
      }
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

    Exit() {
      this.$router.push('/admin/items');
    },
  },
};
</script>

<template>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header">
        <h4>Edit Item</h4>
      </div>
      <div class="card-body">
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ error[0] }}
          </li>
        </ul>
        <div class="input-group-prepend mb-3">
          <label for="Category">Category</label>
          <select v-model="model.item.categories_id" class="custom-select form-control">
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.category_name }}
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
          <label for="Description">Description</label>
          <input v-model="model.item.description" class="form-control" type="text"/>
        </div>
        <div class="mb-3">
          <label for="Price">Price</label>
          <input v-model="model.item.price" class="form-control" type="text"/>
        </div>
        <div class="mb-3">
          <label for="Image">Image</label>
          <input class="form-control" type="file" @change="handleImageUpload"/>
        </div>
        <div class="mb-3">
          <button class="btn btn-primary" type="button" @click="updateItem">Update</button>
          <button class="btn btn-danger" type="button" @click="Exit">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>
