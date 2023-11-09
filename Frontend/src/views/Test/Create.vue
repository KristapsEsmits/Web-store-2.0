<template>
    <div class="container mt-5">
      <div class="card">
        <div class="card-header">
          <h4>Add Product</h4>
        </div>
        <div class="card-body">
          <ul class="alert alert-danger" v-if="Object.keys(errorList).length > 0">
            <li class="mb-0 ms-3" v-for="(error, index) in errorList" :key="index">
              {{ error[0] }}
            </li>
          </ul>
          <div class="mb-3">
            <label for="Name">Name</label>
            <input type="text" v-model="model.test.name" class="form-control" />
          </div>
          <div class="mb-3">
            <label for="Name">Description</label>
            <input type="text" v-model="model.test.desc" class="form-control" />
          </div>
          <div class="mb-3">
            <label for="Name">Price</label>
            <input type="text" v-model="model.test.price" class="form-control" />
          </div>
          <div class="mb-3">
            <button type="button" @click="saveTest" class="btn btn-primary">Save</button>
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
          test: {
            name: '',
            desc: '',
            price: '',
          },
        },
      };
    },

    methods: {
      saveTest() {
        var list = this;
        axios.post('/test', this.model.test).then((res) => {
          console.log(res.data);
          // alert(res.data.message);
          // this.$router.push('/test');
          const successMessage = res.data.message;
          this.$router.push('/test?successMessage=' + successMessage);
  
          this.model.test.name = '';  
          this.model.test.desc = '';
          this.model.test.price = '';
  
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
    },
  };
  </script>
  