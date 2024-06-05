<template>
    <div class="wrapper">
      <div class="content-wrapper">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Finances</h4>
          </div>
          <div class="card-body">
            <div class="filters mb-3">
              <label for="start-date">Start Date:</label>
              <input type="date" v-model="startDate" @change="fetchFinanceData" class="form-control me-2" />
              <label for="end-date">End Date:</label>
              <input type="date" v-model="endDate" @change="fetchFinanceData" class="form-control me-2" />
            </div>
            <div v-if="loading" class="text-center">
              <span>Loading...</span>
            </div>
            <div v-else>
              <div class="finances-info">
                <h5>Total Amount Earned: {{ financeData.totalAmount.toFixed(2) }}€</h5>
                <h5>Total VAT: {{ financeData.amountWithVAT.toFixed(2) }}€</h5>
                <h5>Total Amount Without VAT: {{ financeData.amountWithoutVAT.toFixed(2) }}€</h5>
              </div>
              <PieChart
                :totalAmount="financeData.totalAmount"
                :amountWithVAT="financeData.amountWithVAT"
                :amountWithoutVAT="financeData.amountWithoutVAT"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import axios from 'axios';
  import PieChart from '@/components/PieChart.vue';
  const financeData = ref({
    totalAmount: 0,
    amountWithVAT: 0,
    amountWithoutVAT: 0,
  });
  const loading = ref(true);
  const startDate = ref('');
  const endDate = ref('');
  
  const fetchFinanceData = async () => {
    loading.value = true;
    try {
      const res = await axios.get('/finances', {
        params: {
          start_date: startDate.value || undefined,
          end_date: endDate.value || undefined,
        },
      });
      financeData.value = res.data;
    } catch (error) {
      console.error('Error fetching finance data:', error);
    } finally {
      loading.value = false;
    }
  };
  
  onMounted(() => {
    fetchFinanceData();
  });
  </script>
  
  <style scoped>
  .wrapper {
    display: flex;
  }
  
  .content-wrapper {
    flex: 1;
    margin: 20px;
  }
  
  .card {
    margin-top: 20px;
  }
  
  .finances-info {
    margin-top: 20px;
  }
  
  .text-center {
    text-align: center;
  }
  
  .filters {
    display: flex;
    gap: 10px;
    align-items: center;
  }
  </style>
  