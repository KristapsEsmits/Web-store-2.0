<script>
import axios from 'axios';

export default {
  name: 'brands',
  data() {
    return {
      brands: [],
      successMessage: '',
    };
  },

  created() {
    this.successMessage = this.$route.query.successMessage;
    this.getBrands();
  },

  mounted() {
    this.getBrands();
  },

  methods: {
    getBrands() {
      axios.get('/brands').then((res) => {
        this.brands = res.data.brands;
      });
    },

    deleteBrands(brandId) {
      if (confirm('Are you sure?')) {
        axios.delete(`/brands/${brandId}/delete`)
          .then((res) => {
            this.successMessage = res.data.message;
            this.getBrands();
          })
          .catch(function (error) {
            if (error.response.status == 422) {
              console.error('Validation error:', error.response.data.errors);
            } else if (error.request) {
              console.error('Request error:', error.request);
            } else {
              console.error('Error', error.message);
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
  <div class="container">
    <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
      <span>{{ successMessage }}</span>
      <button type="button" class="btn-close" @click="dismissSuccessMessage"></button>
    </div>
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Brands
          <router-link to="/admin/brands/create" class="btn btn-primary btn-round btn-fill float-end">Add Brand</router-link>
        </h4>
      </div>
      <div class="card-body">
        <template v-if="brands.length > 0">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Brand ID</th>
                <th>Brand Name</th>
                <th>Brand Image Path</th>
                <th>Brand Image</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(brand, index) in brands" :key="index">
                <td>{{ brand.id }}</td>
                <td>{{ brand.name }}</td>
                <td>{{ brand.img }}</td>
                <td class="image-cell">
                  <img :src="'http://localhost:8000/storage/uploads/' + brand.img" style="max-width: 90px; max-height: 70px;" alt="Brand Image">
                </td>
                <td class="d-flex justify-content-center">
                  <router-link :to="{ path: '/admin/brands/' + brand.id + '/edit' }" class="btn btn-success float-middle">Edit</router-link>
                  <button type="button" @click="deleteBrands(brand.id)" class="btn btn-danger">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </template>
        <template v-else>
          <p>No brands found.</p>
        </template>
      </div>
    </div>
  </div>
</template>
