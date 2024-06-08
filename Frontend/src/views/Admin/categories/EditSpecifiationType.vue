<template>
    <div class="container mt-5">
      <div class="card">
        <div class="card-header">
          <h4>Edit Specification Title</h4>
        </div>
        <div class="card-body">
          <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
            <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
              {{ error[0] }}
            </li>
          </ul>
          <div class="mb-3">
            <label for="title">Specification Title</label>
            <input v-model="title" id="title" class="form-control" type="text"/>
          </div>
          <div class="mb-3">
            <button class="btn btn-primary" type="button" @click="updateSpecificationTitle">Save</button>
            <button class="btn btn-danger" type="button" @click="cancelEdit">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { useRoute, useRouter } from 'vue-router';
  import axios from 'axios';
  
  const route = useRoute();
  const router = useRouter();
  const title = ref('');
  const errorList = ref({});
  const specificationTitleId = route.params.id;
  
  const getSpecificationTitle = async () => {
    try {
      const res = await axios.get(`/specification-titles/${specificationTitleId}`);
      title.value = res.data.specification_title.specification_title;
    } catch (error) {
      console.error('Error fetching specification title:', error);
    }
  };
  
  const updateSpecificationTitle = async () => {
    try {
      await axios.put(`/specification-titles/${specificationTitleId}`, { specification_title: title.value });
      router.push('/admin/categories?successMessage=Specification title updated successfully');
    } catch (error) {
      if (error.response && error.response.status === 422) {
        errorList.value = error.response.data.errors;
      } else {
        console.error('Error updating specification title:', error);
      }
    }
  };
  
  const cancelEdit = () => {
    router.push('/admin/categories');
  };
  
  onMounted(() => {
    getSpecificationTitle();
  });
  </script>
  