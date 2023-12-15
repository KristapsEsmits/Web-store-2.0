<template>
  <div class="container">
    <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
      <span>{{ successMessage }}</span>
      <button type="button" class="btn-close" @click="dismissSuccessMessage"></button>
    </div>
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Test
          <router-link to="/test/create" class="btn btn-primary btn-round btn-fill float-end">Add Product</router-link>
        </h4>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Product ID</th>
              <th>Product Name</th>
              <th>Product Description</th>
              <th>Product Price</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(test, index) in this.test" :key="index">
              <td>{{ test.id}}</td>
              <td>{{ test.name }}</td>
              <td>{{ test.desc }}</td>
              <td>{{ test.price }}</td>
              <td class="d-flex justify-content-center">
                <router-link :to="{path: '/test/'+test.id+'/edit'}" class="btn btn-success float-middle">Edit</router-link>
                <button type="button" @click="deleteTest(test.id)" class="btn btn-danger">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: 'test',
  data() {
    return {
      test: [],
      successMessage: '',
    };
  },

  created() {
    this.successMessage = this.$route.query.successMessage;
    this.getTest();
  },

  mounted() {
    this.getTest();
  },

  methods: {
    getTest() {
      axios.get('/test').then((res) => {
        this.test = res.data.test;
        console.log(this.test)
      });
    },

    deleteTest(testId) {
    if (confirm('Are you sure?')) {
      axios.delete(`/test/${testId}/delete`)
        .then((res) => {
          this.successMessage = res.data.message;
          this.getTest();
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
