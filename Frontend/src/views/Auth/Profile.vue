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
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4>Profile</h4>
            </div>
            <div class="card-body">
                <h1>Name: {{ users?.name }}</h1>
                <h1>Surname: {{ users?.surname }}</h1>
                <h1>Phone number: {{ users?.phone }}</h1>
                <h1>Email: {{ users?.email }}</h1>
            </div>
        </div> 
    </div>
  </main>
</template>
