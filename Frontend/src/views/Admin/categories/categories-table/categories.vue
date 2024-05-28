<script setup>
import AdminNav from '../../../../components/AdminNav.vue';
</script>

<script>

import axios from 'axios';

export default {
  name: 'categories',
  data() {
    return {
      categories: [],
      successMessage: '',
    };
  },

  created() {
    this.successMessage = this.$route.query.successMessage;
    this.getCategories();
  },

  mounted() {
    this.getCategories();
  },

  methods: {
    getCategories() {
      axios.get('/categories').then((res) => {
        this.categories = res.data.categories;
      });
    },

    deleteTest(categoriestId) {
      if (confirm('Are you sure?')) {
        axios.delete(`/categories/${categoriestId}/delete`)
            .then((res) => {
              this.successMessage = res.data.message;
              this.getCategories();
            })
            .catch((error) => {
              if (error.response && error.response.status === 422) {
                console.error('Validation errors:', error.response.data.errors);
              } else {
                console.error('Error deleting test:', error.message);
              }
            });
      }
    },

    dismissSuccessMessage() {
      this.successMessage = '';
    },
  },
};
</script>

<template>
  <div class="wrapper">
    <AdminNav :isSidebarOpen="isSidebarOpen" @toggleSidebar="toggleSidebar"/>
    <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
      <span>{{ successMessage }}</span>
      <button class="btn-close" type="button" @click="dismissSuccessMessage"></button>
    </div>
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Categories
          <router-link class="btn btn-primary btn-round btn-fill float-end" to="/admin/categories/create">Add Category
          </router-link>
        </h4>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <thead>
          <tr>
            <th>Category ID</th>
            <th>Category Name</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(categories, index) in this.categories" :key="index">
            <td>{{ categories.id }}</td>
            <td>{{ categories.category_name }}</td>
            <td class="d-flex justify-content-center">
              <router-link :to="{path: '/admin/categories/'+categories.id+'/edit'}" class="btn btn-success float-middle">Edit</router-link>
              <button class="btn btn-danger" type="button" @click="deleteTest(categories.id)">Delete</button>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
.card {
  margin-top: 20px;
  margin-left: 20px;
}

.wrapper {
  display: flex;
}
</style>
