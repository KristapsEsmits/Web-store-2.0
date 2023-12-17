<script setup>
import AdminNav from '../../../components/AdminNav.vue';
</script>

<script>
import axios from 'axios';

export default {
  name: 'items',
  data() {
    return {
      items: [],
      successMessage: '',
    };
  },

  created() {
    this.successMessage = this.$route.query.successMessage;
    this.getItems();
  },

  methods: {
    getItems() {
      axios.get('/items').then((res) => {
        this.items = res.data.items;
      });
    },

    deleteItems(itemId) {
      if (confirm('Are you sure?')) {
        axios.delete(`/items/${itemId}/delete`)
          .then((res) => {
            this.successMessage = res.data.message;
            this.getItems();
          })
          .catch((error) => {
            if (error.response.status == 422) {
              this.errorList = error.response.data.errors;
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

<template>
  <div class="container">
    <AdminNav />
      <router-view />
    <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
      <span>{{ successMessage }}</span>
      <button type="button" class="btn-close" @click="dismissSuccessMessage"></button>
    </div>
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Items
          <router-link to="/admin/items/create" class="btn btn-primary btn-round btn-fill float-end">Add Item</router-link>
        </h4>
      </div>
      <div class="card-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Item ID</th>
              <th>Name</th>
              <th>Description</th>
              <th>Price</th>
              <th>Img path</th>
              <th>Img</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in items" :key="index">
              <td>{{ item.id }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.description }}</td>
              <td>{{ item.price }}</td>
              <td>{{ item.img }}</td>
              <td class="image-cell">
                <img :src="'http://localhost:8000/storage/uploads/' + item.img" style="max-width: 90px; max-height: 70px;" alt="Item Image">
              </td>
              <td class="d-flex justify-content-center">
                <!-- <router-link :to="{ path: '/admin/items/' + item.id + '/edit' }" class="btn btn-success float-middle">Edit</router-link> -->
                <button type="button" @click="deleteItems(item.id)" class="btn btn-danger">Delete</button>
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
  }
</style>
