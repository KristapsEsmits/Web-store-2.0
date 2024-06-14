<script setup>
import axios from 'axios';
import {onMounted, ref} from 'vue';
import {Chart, registerables} from 'chart.js';
import 'chartjs-adapter-date-fns';

Chart.register(...registerables);

const numberOfItems = ref(0);
const numberOfBrands = ref(0);
const numberOfUsers = ref(0);
const userRegistrationData = ref([]);
const categoryItemCounts = ref([]);
const dailySpendingData = ref([]);
const mostSoldItem = ref({});
const activeOrders = ref(0);
const canceledOrders = ref(0);
const closedOrders = ref(0);
const totalEarnedWithVat = ref(0);
const totalVat = ref(0);
const totalEarnedWithoutVat = ref(0);

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

const fetchStatisticsData = () => {
  axios.get('/finances')
      .then((res) => {
        mostSoldItem.value = res.data.mostSoldItem;
        activeOrders.value = res.data.activeOrders;
        canceledOrders.value = res.data.canceledOrders;
        closedOrders.value = res.data.closedOrders;
        totalEarnedWithVat.value = res.data.totalEarnedWithVat;
        totalVat.value = res.data.totalVat;
        totalEarnedWithoutVat.value = res.data.totalEarnedWithoutVat;
      })
      .catch((error) => {
        console.error('Error fetching statistics data:', error);
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
      maintainAspectRatio: false,
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
          ticks: {
            stepSize: 1,
          },
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
      maintainAspectRatio: false,
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
      maintainAspectRatio: false,
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

const getImageUrl = (image) => {
  return `http://localhost:8000/storage/uploads/${image}`;
};

onMounted(() => {
  fetchNumberOfItems();
  fetchNumberOfBrands();
  fetchNumberOfUsers();
  fetchUserRegistrationData();
  fetchCategoryItemCounts();
  fetchDailySpendingData();
  fetchStatisticsData();
});
</script>

<template>
  <div class="wrapper">
    <div class="content">
      <h1>Dashboard</h1>
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

      <div class="statistics-wrapper">
        <div class="card">
          <h2 class="card-title">Most Sold Item</h2>
          <div class="item-card">
            <img :src="getImageUrl(mostSoldItem.image)" alt="Item Image">
            <div>
              <h3>{{ mostSoldItem.name }}</h3>
              <p>{{ mostSoldItem.description }}</p>
              <p>Price: {{ mostSoldItem.price }}</p>
            </div>
          </div>
        </div>
        <div class="card">
          <h1 class="">Order Status</h1>
          <p>Active Orders: {{ activeOrders }}</p>
          <p>Canceled Orders: {{ canceledOrders }}</p>
          <p>Completed Orders: {{ closedOrders }}</p>
        </div>
        <div class="card">
          <h2 class="card-title">Total Amounts</h2>
          <p>Total Earned (with VAT): {{ totalEarnedWithVat.toFixed(2) }}</p>
          <p>Total VAT: {{ totalVat.toFixed(2) }}</p>
          <p>Total Earned (without VAT): {{ totalEarnedWithoutVat.toFixed(2) }}</p>
        </div>
      </div>

      <div class="chart-wrapper">
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
  </div>
</template>

<style scoped>
.wrapper {
  display: flex;
  flex-direction: column;
}

.content {
  flex: 1;
  padding: 20px;
}

.stats-wrapper, .statistics-wrapper {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
  margin-bottom: 20px;
}

.card {
  flex: 1 1 calc(33.333% - 40px);
  max-width: calc(33.333% - 40px);
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.card-title {
  font-size: 1.2em;
  margin-bottom: 10px;
}

.card-desc {
  font-size: 2em;
  font-weight: bold;
}

.item-card {
  display: flex;
  gap: 20px;
  align-items: center;
  text-align: left;
}

.item-card img {
  max-width: 100px;
  border-radius: 5px;
}

.chart-wrapper {
  display: flex;
  flex-direction: column;
  gap: 20px;
  justify-content: center;
}

.chart-container {
  position: relative;
  width: 100%;
  height: 400px;
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

@media (min-width: 768px) {
  .chart-wrapper {
    flex-direction: row;
    flex-wrap: wrap;
  }

  .chart-container {
    flex: 1 1 calc(33.333% - 40px);
    max-width: calc(33.333% - 40px);
    height: 400px;
  }
}

@media (max-width: 767px) {
  .stats-wrapper, .statistics-wrapper {
    flex-direction: column;
  }

  .card {
    flex: 1 1 100%;
    max-width: 100%;
  }

  .chart-wrapper {
    flex-direction: column;
  }

  .chart-container {
    flex: 1 1 100%;
    max-width: 100%;
    height: auto;
  }
}
</style>
