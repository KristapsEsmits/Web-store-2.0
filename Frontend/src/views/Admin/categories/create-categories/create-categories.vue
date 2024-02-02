<template>
    <div class="container mt-5">
      <div class="card">
        <div class="card-header">
          <h4>Add Category</h4>
        </div>
        <div class="card-body">
          <ul class="alert alert-danger" v-if="Object.keys(errorList).length > 0">
            <li class="mb-0 ms-3" v-for="(error, index) in errorList" :key="index">
              {{ error[0] }}
            </li>
          </ul>
          <div class="mb-3">
            <label for="Name">Name</label>
            <input type="text" v-model="model.categories.category_name" class="form-control" />
          </div>
          <div class="mb-3">
            <button type="button" @click="saveCategories" class="btn btn-primary">Save</button>
            <button type="button" @click="Exit" class="btn btn-danger">Cancel</button>
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
  