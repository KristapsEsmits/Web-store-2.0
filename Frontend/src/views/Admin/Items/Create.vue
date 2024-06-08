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

        <div v-if="step === 1">
          <div class="mb-3">
            <label for="Category">Category</label>
            <select v-model="model.item.categories_id" class="form-control">
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.category_name }}
              </option>
            </select>
          </div>
          <div class="mb-3">
            <label for="Brand">Brand</label>
            <select v-model="model.item.brand_id" class="form-control">
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
        </div>

        <div v-if="step === 2">
          <div v-for="(title, index) in specificationTitles" :key="title.id" class="mb-3">
            <label :for="'spec_desc_' + title.id">{{ title.specification_title }}</label>
            <input :id="'spec_desc_' + title.id" v-model="specificationDescriptions[index].description"
                   class="form-control" type="text"/>
          </div>
        </div>

        <div class="mb-3">
          <button v-if="step > 1" class="btn btn-secondary" type="button" @click="prevStep">Previous</button>
          <button v-if="step < 2" class="btn btn-primary" type="button" @click="nextStep">Next</button>
          <button v-if="step === 2" class="btn btn-primary" type="button" @click="saveItemWithSpecifications">Save
          </button>
          <button class="btn btn-danger" type="button" @click="Exit">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Create',
  data() {
    return {
      step: 1,
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
      specificationTitles: [],
      specificationDescriptions: [],
    };
  },
  methods: {
    handleImageUpload(event) {
      this.model.item.img = event.target.files[0];
    },
    nextStep() {
      if (this.step === 1) {
        this.fetchSpecificationTitles();
      }
      this.step++;
    },
    prevStep() {
      this.step--;
    },
    async fetchSpecificationTitles() {
      const categoryId = this.model.item.categories_id;
      try {
        const res = await axios.get(`/specification_titles/${categoryId}`);
        this.specificationTitles = res.data.specification_titles;
        this.specificationDescriptions = this.specificationTitles.map(title => ({
          specification_title_id: title.id,
          description: ''
        }));
      } catch (error) {
        console.error('Error fetching specification titles:', error);
      }
    },
    async saveItemWithSpecifications() {
      const formData = new FormData();
      formData.append('name', this.model.item.name);
      formData.append('description', this.model.item.description);
      formData.append('price', this.model.item.price);
      formData.append('brand_id', this.model.item.brand_id);
      formData.append('categories_id', this.model.item.categories_id);
      formData.append('img', this.model.item.img);

      formData.append('specifications', JSON.stringify(this.specificationDescriptions));

      try {
        const res = await axios.post('/items', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        const successMessage = res.data.message;
        this.$router.push('/admin/items?successMessage=' + successMessage);
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errorList = error.response.data.errors;
        } else {
          console.error('Error:', error.message);
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

<style scoped>
.container {
  max-width: 600px;
}

.card-header {
  background-color: #f8f9fa;
}

.btn-secondary {
  margin-right: 10px;
}
</style>
