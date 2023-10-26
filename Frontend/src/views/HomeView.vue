<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const users = ref([]);
onMounted(async () => {
  const data = await axios.get('/api/user');
  console.log(data);
});
</script>

<template>
  <main>
    <h1>Welcome to aaaaas</h1>
  </main>
</template>
