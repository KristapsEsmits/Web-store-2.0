<template>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header">
        <h4>Add Category</h4>
      </div>
      <div class="card-body">
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ error[0] }}
          </li>
        </ul>
        <div class="mb-3">
          <label for="Name">Name</label>
          <input v-model="category_name" class="form-control" type="text"/>
        </div>
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
          <button class="btn btn-primary" type="button" @click="saveCategories">Save</button>
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
      errorList: {},
      category_name: '',
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
    async saveCategories() {
      try {
        const response = await axios.post('/categories', {
          category_name: this.category_name,
          specification_titles: this.specification_titles,
        });
        const successMessage = response.data.message;
        this.$router.push('/admin/categories?successMessage=' + successMessage);
        this.category_name = '';
        this.specification_titles = [{title: ''}];
        this.errorList = {};
      } catch (error) {
        if (error.response.status === 422) {
          this.errorList = error.response.data.errors;
        } else {
          console.error('Error:', error.message);
        }
      }
    },
    Exit() {
      this.$router.push('/admin/categories');
    },
  },
};
</script>
