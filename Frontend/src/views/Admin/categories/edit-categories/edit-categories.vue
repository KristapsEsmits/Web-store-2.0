<template>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header">
        <h4>Add Category</h4>
      </div>
      <div class="card-body">
        <ul v-if="Object.keys(errorList).length > 0" class="alert alert-danger">
          <li v-for="(error, index) in errorList" :key="index" class="mb-0 ms-3">
            {{ error[0] }}
          </li>
        </ul>
        <div class="mb-3">
          <label for="Name">Name</label>
          <input v-model="model.categories.category_name" class="form-control" type="text"/>
        </div>
        <div class="mb-3">
          <button class="btn btn-primary" type="button" @click="saveCategories">Save</button>
          <button class="btn btn-danger" type="button" @click="Exit">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

</script>
  