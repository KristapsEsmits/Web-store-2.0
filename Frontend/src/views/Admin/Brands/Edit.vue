<script>
import axios from 'axios';

export default {
    name: 'Edit',
    data() {
      return {
        brandsId: '',
        errorList: {},
        model: {
          brands: {
            name: '',
            img: null,
          },
        },
      };
    },

    mounted() {
      this.brandsId = this.$route.params.id;
      this.getBrandsData(this.$route.params.id);
    },

    methods: {
      getBrandsData(brandsId) {
        axios.get(`/brands/${brandsId}/edit`).then((res) => {
          this.model.brands = res.data.brands;
        })

        .catch((error) => { 
          if (error.response) {
            if (error.response.status == 404) {
              this.$router.push('/brands');
            }
          }
        });
      },

      handleImageUpload(event) {
        this.model.brands.img = event.target.files[0];
      },

      updateBrands() {
        var list = this;
        const formData = new FormData();
        formData.append('name', this.model.brands.name);
        formData.append('img', this.model.brands.img);
        console.log(formData);

        axios.put(`/brands/${this.brandsId}/edit`, formData)
            .then((res) => {
          this.$router.push('/admin/brands');
          alert(res.data.message);
          this.errorList = '';
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
      this.$router.push('/admin/brands'); 
    },
    },
  };
</script>

<template>
    <div class="container mt-5">
      <div class="card">
        <div class="card-header">
          <h4>Edit Product</h4>
        </div>
        <div class="card-body">
            <ul class="alert alert-danger" v-if="Object.keys(errorList).length > 0">
            <li class="mb-0 ms-3" v-for="(error, index) in errorList" :key="index">
              {{ error[0] }}
            </li>
            </ul>
        <div class="mb-3">
            <label for="Name">Name</label>
          <input type="text" v-model="model.brands.name" class="form-control"/>
        </div>
        <div class="mb-3">
            <label for="Image">Image</label>
          <input type="file" @change="handleImageUpload" class="form-control"/>
        </div>
          <div class="mb-3">
            <button type="button" @click="updateBrands" class="btn btn-primary">Update</button>
            <button type="button" @click="Exit" class="btn btn-danger">Cancel</button>
          </div>
        </div>
      </div>
    </div>
</template>

  