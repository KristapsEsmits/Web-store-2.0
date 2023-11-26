<script>
import axios from 'axios';

export default {
  name: 'Create',
  data() {
    return {
      errorList: {},
      model: {
        brands: {
          name: '',
          img: null, 
        },
      },
    };
  },
  methods: {
    handleImageUpload(event) {
      this.model.brands.img = event.target.files[0];
    },
    
    saveBrands() {
      const formData = new FormData();
      formData.append('name', this.model.brands.name);
      formData.append('img', this.model.brands.img);

      axios.post('/brands', formData)
        .then((res) => {
          console.log(res.data);
          const successMessage = res.data.message;
          this.$router.push('/brands?successMessage=' + successMessage);

          this.model.brands.name = '';
          this.model.brands.img = null;

          this.errorList = {};
        })
        .catch((error) => {
          if (error.response && error.response.status === 422) {
            this.errorList = error.response.data.errors;
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

<template>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header">
        <h4>Add Brand</h4>
      </div>
      <div class="card-body">
        <ul class="alert alert-danger" v-if="Object.keys(errorList).length > 0">
          <li class="mb-0 ms-3" v-for="(error, index) in errorList" :key="index">
            {{ error[0] }}
          </li>
        </ul>
        <div class="mb-3">
          <label for="Name">Name</label>
          <input type="text" v-model="model.brands.name" class="form-control" />
        </div>
        <div class="mb-3">
          <label for="Image">Image</label>
          <input type="file" @change="handleImageUpload" class="form-control" />
        </div>
        <div class="mb-3">
          <button type="button" @click="saveBrands" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
</template>
