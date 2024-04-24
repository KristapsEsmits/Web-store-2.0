<template>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header">
        <h4>Edit Product</h4>
      </div>
      <div class="card-body">
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ error[0] }}
          </li>
        </ul>
        <div class="mb-3">
          <label for="Name">Name</label>
          <input v-model="model.test.name" class="form-control" type="text"/>
        </div>
        <div class="mb-3">
          <label for="Name">Description</label>
          <input v-model="model.test.desc" class="form-control" type="text"/>
        </div>
        <div class="mb-3">
          <label for="Name">Price</label>
          <input v-model="model.test.price" class="form-control" type="text"/>
        </div>
        <div class="mb-3">
          <button class="btn btn-primary" type="button" @click="updateTest">Update</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Edit',
  data() {
    return {
      testId: '',
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

  mounted() {
    this.testId = this.$route.params.id;
    this.getTestData(this.$route.params.id);
  },

  methods: {
    getTestData(testId) {
      axios.get(`/test/${testId}/edit`).then((res) => {
        this.model.test = res.data.test;
      })

          .catch((error) => {
            if (error.response) {
              if (error.response.status == 404) {
                this.$router.push('/test');
              }
            }
          });
    },

    updateTest() {
      var list = this;
      axios.put(`/test/${this.testId}/edit`, this.model.test).then((res) => {
        console.log(res.data);
        this.$router.push('/test');
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
  },
};
</script>
  