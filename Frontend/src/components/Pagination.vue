<template>
    <div class="pagination" v-if="totalPages > 1">
      <button @click="prevPage" :disabled="currentPage === 1">&lt;</button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="nextPage" :disabled="currentPage === totalPages">&gt;</button>
    </div>
  </template>
  
  <script>
  export default {
    name: 'Pagination',
    props: {
      currentPage: {
        type: Number,
        required: true
      },
      totalPages: {
        type: Number,
        required: true
      }
    },
    methods: {
      prevPage() {
        this.$emit('update:currentPage', this.currentPage - 1);
      },
      nextPage() {
        this.$emit('update:currentPage', this.currentPage + 1);
      }
    }
  };
  </script>
  
  <style scoped>
  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
  }
  
  .pagination button {
    margin: 0 10px;
    padding: 5px 10px;
    cursor: pointer;
    background-color: #000;
    color: #fff;
    border: 1px solid #000;
    border-radius: 5px;
    transition: background-color 0.3s;
  }
  
  .pagination button:hover {
    background-color: #333;
  }
  
  .pagination span {
    margin: 0 10px;
    color: #000;
  }
  </style>
  