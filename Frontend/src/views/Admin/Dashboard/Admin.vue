<script setup>
import AdminNav from '../../../components/AdminNav.vue';
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { Chart, registerables } from 'chart.js';
import 'chartjs-adapter-date-fns';

Chart.register(...registerables);

const isSidebarOpen = ref(false);
const numberOfItems = ref(0);
const numberOfBrands = ref(0);
const numberOfUsers = ref(0);
const userRegistrationData = ref([]);
const categoryItemCounts = ref([]);
const dailySpendingData = ref([]);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const fetchNumberOfItems = () => {
  axios.get('/items')
    .then((res) => {
      numberOfItems.value = res.data.items.length;
    })
    .catch((error) => {
      console.error('Error fetching items:', error);
    });
};

const fetchNumberOfBrands = () => {
  axios.get('/brands')
    .then((res) => {
      numberOfBrands.value = res.data.brands.length;
    })
    .catch((error) => {
      console.error('Error fetching brands:', error);
    });
};

const fetchNumberOfUsers = () => {
  axios.get('/user-amount')
    .then((res) => {
      numberOfUsers.value = res.data.user_count;
    })
    .catch((error) => {
      console.error('Error fetching registered users:', error);
    });
};

const fetchUserRegistrationData = () => {
  axios.get('/user-registrations')
    .then((res) => {
      userRegistrationData.value = res.data.registrations;
      renderUserRegistrationChart();
    })
    .catch((error) => {
      console.error('Error fetching user registration data:', error);
    });
};

const fetchCategoryItemCounts = () => {
  axios.get('/items-category-count')
    .then((res) => {
      categoryItemCounts.value = res.data.category_counts;
      renderCategoryItemCountChart();
    })
    .catch((error) => {
      console.error('Error fetching category item counts:', error);
    });
};

const fetchDailySpendingData = () => {
  axios.get('/total-spending-per-day')
    .then((res) => {
      dailySpendingData.value = res.data.data;
      renderDailySpendingChart();
    })
    .catch((error) => {
      console.error('Error fetching daily spending data:', error);
    });
};

const renderUserRegistrationChart = () => {
  const ctx = document.getElementById('userRegistrationChart').getContext('2d');
  const labels = userRegistrationData.value.map(entry => entry.date);
  const data = userRegistrationData.value.map(entry => entry.count);

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Registered Users',
        data: data,
        borderColor: 'rgba(75, 192, 192, 1)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        fill: true,
      }],
    },
    options: {
      scales: {
        x: {
          type: 'time',
          time: {
            unit: 'day',
            tooltipFormat: 'PP',
          },
          ticks: {
            display: false,
          },
        },
        y: {
          beginAtZero: true,
        },
      },
    },
  });
};

const renderCategoryItemCountChart = () => {
  const ctx = document.getElementById('categoryItemCountChart').getContext('2d');
  const labels = categoryItemCounts.value.map(entry => entry.category_name);
  const data = categoryItemCounts.value.map(entry => entry.item_count);

  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: labels,
      datasets: [{
        data: data,
        backgroundColor: [
          'rgba(75, 192, 192, 0.2)',
          'rgba(255, 99, 132, 0.2)',
          'rgba(255, 206, 86, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(255, 159, 64, 0.2)',
        ],
        borderColor: [
          'rgba(75, 192, 192, 1)',
          'rgba(255, 99, 132, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(54, 162, 235, 1)',
          'rgba(153, 102, 255, 1)',
          'rgba(255, 159, 64, 1)',
        ],
        borderWidth: 1,
      }],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top',
        },
      },
    },
  });
};

const renderDailySpendingChart = () => {
  const ctx = document.getElementById('dailySpendingChart').getContext('2d');
  const labels = dailySpendingData.value.map(entry => entry.date);
  const data = dailySpendingData.value.map(entry => entry.total_spent);

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Total Money Spent',
        data: data,
        borderColor: 'rgba(255, 99, 132, 1)',
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        fill: true,
      }],
    },
    options: {
      scales: {
        x: {
          type: 'time',
          time: {
            unit: 'day',
            tooltipFormat: 'PP',
          },
          ticks: {
            display: true,
          },
        },
        y: {
          beginAtZero: true,
        },
      },
    },
  });
};

onMounted(() => {
  fetchNumberOfItems();
  fetchNumberOfBrands();
  fetchNumberOfUsers();
  fetchUserRegistrationData();
  fetchCategoryItemCounts();
  fetchDailySpendingData();
});
</script>

<template>
  <div class="wrapper">
    <AdminNav :isSidebarOpen="isSidebarOpen" @toggleSidebar="toggleSidebar" />
    <div class="title">
      <h1>Admin Dashboard</h1>
      <div class="stats-wrapper">
        <div class="card">
          <h2 class="card-title">Number of listings</h2>
          <p class="card-desc">{{ numberOfItems }}</p>
        </div>
        <div class="card">
          <h2 class="card-title">Number of Brands</h2>
          <p class="card-desc">{{ numberOfBrands }}</p>
        </div>
        <div class="card">
          <h2 class="card-title">Registered Users</h2>
          <p class="card-desc">{{ numberOfUsers }}</p>
        </div>
      </div>
      <div class="chart-container">
        <canvas id="userRegistrationChart"></canvas>
      </div>
      <div class="chart-container">
        <canvas id="categoryItemCountChart"></canvas>
      </div>
      <div class="chart-container">
        <canvas id="dailySpendingChart"></canvas>
      </div>
    </div>
  </div>
</template>

<style scoped>
@import './Dashboard.scss';

.chart-container {
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
  margin-top: 20px;
}
</style>
