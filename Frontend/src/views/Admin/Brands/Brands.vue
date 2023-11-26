<template>
    <div class="container">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title">Brands
            <router-link to="/brands/create" class="btn btn-primary btn-round btn-fill float-end">Add Brand</router-link>
          </h4>
        </div>
        <div class="card-body">
          <div v-if="successMessage" class="alert alert-success">
            {{ successMessage }}
            <button type="button" class="close" @click="dismissSuccessMessage">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
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
              <tr v-for="(brands, index) in this.brands" :key="index">
                <td>{{ brands.id}}</td>
                <td>{{ brands.name }}</td>
                <td>{{ brands.img }}</td>
                <td>
                    
                </td>
                <td class="d-flex justify-content-center">
                  <router-link :to="{path: '/brands/'+brands.id+'/edit'}" class="btn btn-success float-middle">Edit</router-link>
                  <button type="button" @click="deleteBrands(brands.id)" class="btn btn-danger">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </template>
  
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
          console.log(this.brands)
        });
      },
  
      deleteBrands(brandsId) {
      if (confirm('Are you sure?')) {
        axios.delete(`/brands/${brandsId}/delete`).then((res) => {
          alert(res.data.message);
          this.getBrands();
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
        }
      },
  
      dismissSuccessMessage() {
        this.successMessage = ''; 
      },
    },
  };
  </script>
  