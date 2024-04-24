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
          <input v-model="model.categories.category_name" class="form-control" type="text"/>
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
      model: {
        categories: {
          category_name: '',
        },
      },
    };
  },

  methods: {
    saveCategories() {
      var list = this;
      axios.post('/categories', this.model.categories).then((res) => {
        const successMessage = res.data.message;
        this.$router.push('/admin/categories?successMessage=' + successMessage);
        console.log(res.data);
        this.model.categories.category_name = '';

        this.errorList = {};
      })

          .catch(function (error) {
            if (error.response.status == 422) {
              list.errorList = error.response.data.errors;
            } else if (error.request) {
              console.log(error.request);
            } else {
              console.log('Error', error.message);
            }
          });
    },

    Exit() {
      this.$router.push('/admin/categories');
    },
  },
};
</script>
  