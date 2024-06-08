<template>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header">
        <h4>Select Category and Add Specification Titles</h4>
      </div>
      <div class="card-body">
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ error[0] }}
          </li>
        </ul>
        <div class="mb-3">
          <label for="category">Select Category</label>
          <select id="category" v-model="selectedCategory" class="form-control">
            <option v-for="category in categories" :key="category.id" :value="category.id">{{
                category.category_name
              }}
            </option>
          </select>
        </div>
        <div v-if="selectedCategory">
          <div v-for="(spec, index) in specification_titles" :key="index" class="mb-3">
            <label :for="'spec_title_' + index">Specification Title {{ index + 1 }}</label>
            <input :id="'spec_title_' + index" v-model="spec.title" class="form-control" type="text"/>
          </div>
          <div class="mb-3">
            <button class="btn btn-secondary" type="button" @click="addSpecificationTitle">+ Add more</button>
            <button :disabled="specification_titles.length <= 1" class="btn btn-secondary" type="button"
                    @click="removeLastSpecificationTitle">- Remove last
            </button>
          </div>
          <div class="mb-3">
            <button class="btn btn-primary" type="button" @click="saveSpecifications">Save</button>
            <button class="btn btn-danger" type="button" @click="exit">Cancel</button>
          </div>
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
      errorList: {},
      categories: [],
      selectedCategory: null,
      specification_titles: [{title: ''}],
    };
  },
  methods: {
    addSpecificationTitle() {
      this.specification_titles.push({title: ''});
    },
    removeLastSpecificationTitle() {
      if (this.specification_titles.length > 1) {
        this.specification_titles.pop();
      }
    },
    async fetchCategories() {
      try {
        const response = await axios.get('/categories');
        this.categories = response.data.categories;
      } catch (error) {
        console.error('Error fetching categories:', error.message);
      }
    },
    async saveSpecifications() {
      try {
        const response = await axios.post('/specification-titles', {
          category_id: this.selectedCategory,
          specification_titles: this.specification_titles.map(spec => spec.title),
        });
        const successMessage = response.data.message;
        this.$router.push('/admin/categories?successMessage=' + successMessage);
        this.selectedCategory = null;
        this.specification_titles = [{title: ''}];
        this.errorList = {};
      } catch (error) {
        if (error.response && error.response.status === 422) {
          this.errorList = error.response.data.errors;
        } else {
          console.error('Error:', error.message);
        }
      }
    },
    exit() {
      this.$router.push('/admin/categories');
    },
  },
  mounted() {
    this.fetchCategories();
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
