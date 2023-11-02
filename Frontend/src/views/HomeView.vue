<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const users = ref([]);
onMounted(async () => {
  const data = await axios.get('/api/user');
  users.value = data.data;
});


</script>

<template>
  <main>
    <h1> Hello! {{ users?.name }} {{ users?.surname }}!</h1>
  </main>
</template>
