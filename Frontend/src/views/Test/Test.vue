<template>
  <div class="container">
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
              <td>
                <router-link to="/" class="btn btn-success">Edit</router-link>
                <button type="button" class="btn btn-danger">Delete</button>
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
  name: 'test',
  data() {
    return {
      test: [],
    };
  },
  mounted() {
    this.getTest();
  },
  methods: {
    getTest() {
      axios.get('http://127.0.0.1:8000/api/test').then((res) => {
        this.test = res.data.test;
        console.log(this.test)
      });
    },
  },
};
</script>
