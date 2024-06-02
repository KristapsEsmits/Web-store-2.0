<script>
import axios from 'axios';

export default {
  name: 'EditCategory',
  data() {
    return {
      categoryId: '',
      errorList: {},
      model: {
        category: {
          category_name: '',
        },
      },
      successMessage: '',
    };
  },

  mounted() {
    this.categoryId = this.$route.params.id;
    this.getCategoryData(this.categoryId);
  },

  methods: {
    getCategoryData(categoryId) {
      axios.get(`/categories/${categoryId}/edit`)
        .then((res) => {
          console.log('Category data response:', res.data); // Log the response data
          if (res.data.categories) {
            this.model.category = res.data.categories;
          } else {
            console.error('Category data not found in response');
          }
        })
        .catch((error) => {
          console.error('Error fetching category data:', error);
          if (error.response && error.response.status === 404) {
            this.$router.push('/categories');
          }
        });
    },

    async updateCategory() {
      const data = {
        category_name: this.model.category.category_name,
      };

      try {
        const res = await axios.put(`/categories/${this.categoryId}/edit`, data, {
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
          },
        });
        this.successMessage = res.data.message;
        this.$router.push(`/admin/categories?successMessage=${this.successMessage}`);
        this.errorList = {};
      } catch (error) {
        console.error('Error updating category:', error);
        if (error.response && error.response.status === 422) {
          this.errorList = error.response.data.errors;
        } else if (error.request) {
          console.log(error.request);
        } else {
          console.log('Error', error.message);
        }
      }
    },

    Exit() {
      this.$router.push('/admin/categories');
    },
  },
};
</script>

<template>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header">
        <h4>Edit Category</h4>
      </div>
      <div class="card-body">
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ error[0] }}
          </li>
        </ul>
        <div class="mb-3">
          <label for="CategoryName">Category Name</label>
          <input v-model="model.category.category_name" class="form-control" type="text" />
        </div>
        <div class="mb-3">
          <button class="btn btn-primary" type="button" @click="updateCategory">Update</button>
          <button class="btn btn-danger" type="button" @click="Exit">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>
