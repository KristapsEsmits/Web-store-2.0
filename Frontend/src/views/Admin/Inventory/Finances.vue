<template>
  <div>
    <h1>Financial Overview</h1>

    <div class="tabs">
      <button @click="selectedTab = 'category'">By Category</button>
      <button @click="selectedTab = 'item'">By Item</button>
      <button @click="selectedTab = 'statistics'">Statistics</button>
    </div>

    <div v-if="selectedTab === 'category'">
      <h2>By Category</h2>
      <table>
        <thead>
          <tr>
            <th @click="sortBy('category_id')">Category ID</th>
            <th @click="sortBy('category')">Category</th>
            <th @click="sortBy('total_earned_with_vat')">Total Earned (with VAT)</th>
            <th @click="sortBy('total_vat')">Total VAT</th>
            <th @click="sortBy('total_earned_without_vat')">Total Earned (without VAT)</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(finance, category) in sortedCategoryFinances" :key="category.id">
            <td>{{ finance.category_id }}</td>
            <td>{{ category }}</td>
            <td>{{ finance.total_earned_with_vat.toFixed(2) }}</td>
            <td>{{ finance.total_vat.toFixed(2) }}</td>
            <td>{{ finance.total_earned_without_vat.toFixed(2) }}</td>
          </tr>
          <tr>
            <td colspan="2"><strong>Total</strong></td>
            <td><strong>{{ totalCategoryEarnedWithVat.toFixed(2) }}</strong></td>
            <td><strong>{{ totalCategoryVat.toFixed(2) }}</strong></td>
            <td><strong>{{ totalCategoryEarnedWithoutVat.toFixed(2) }}</strong></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="selectedTab === 'item'">
      <h2>By Item</h2>
      <table>
        <thead>
          <tr>
            <th @click="sortByItem('item_id')">Item ID</th>
            <th @click="sortByItem('item')">Item</th>
            <th @click="sortByItem('total_earned_with_vat')">Total Earned (with VAT)</th>
            <th @click="sortByItem('total_vat')">Total VAT</th>
            <th @click="sortByItem('total_earned_without_vat')">Total Earned (without VAT)</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(finance, item) in sortedItemFinances" :key="item.id">
            <td>{{ finance.item_id }}</td>
            <td>{{ item }}</td>
            <td>{{ finance.total_earned_with_vat.toFixed(2) }}</td>
            <td>{{ finance.total_vat.toFixed(2) }}</td>
            <td>{{ finance.total_earned_without_vat.toFixed(2) }}</td>
          </tr>
          <tr>
            <td colspan="2"><strong>Total</strong></td>
            <td><strong>{{ totalItemEarnedWithVat.toFixed(2) }}</strong></td>
            <td><strong>{{ totalItemVat.toFixed(2) }}</strong></td>
            <td><strong>{{ totalItemEarnedWithoutVat.toFixed(2) }}</strong></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="selectedTab === 'statistics'">
      <h2>Statistics</h2>

      <div class="statistics-cards">
        <div class="card">
          <h3>Most Sold Item</h3>
          <div class="item-card">
            <img :src="getImageUrl(mostSoldItem.image)" alt="Item Image">
            <div>
              <h4>{{ mostSoldItem.name }}</h4>
              <p>{{ mostSoldItem.description }}</p>
              <p>Price: {{ mostSoldItem.price }}</p>
            </div>
          </div>
        </div>
        <div class="card">
          <h3>Order Status</h3>
          <p>Active Orders: {{ activeOrders }}</p>
          <p>Canceled Orders: {{ canceledOrders }}</p>
          <p>Closed Orders: {{ closedOrders }}</p>
        </div>
        <div class="card">
          <h3>Total Amounts</h3>
          <p>Total Earned (with VAT): {{ totalEarnedWithVat.toFixed(2) }}</p>
          <p>Total VAT: {{ totalVat.toFixed(2) }}</p>
          <p>Total Earned (without VAT): {{ totalEarnedWithoutVat.toFixed(2) }}</p>
        </div>
      </div>

      <div class="charts">
        <!-- Insert your chart components here -->
        <canvas id="myChart"></canvas>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { Chart } from 'chart.js';

export default {
  data() {
    return {
      categoryFinances: {},
      itemFinances: {},
      sortKey: '',
      sortAsc: true,
      sortKeyItem: '',
      sortAscItem: true,
      selectedTab: 'category',
      activeOrders: 0,
      canceledOrders: 0,
      closedOrders: 0,
      totalEarnedWithVat: 0,
      totalVat: 0,
      totalEarnedWithoutVat: 0
    };
  },
  computed: {
    sortedCategoryFinances() {
      const sorted = Object.entries(this.categoryFinances).sort((a, b) => {
        const [keyA, valueA] = a;
        const [keyB, valueB] = b;

        if (this.sortKey === 'category' || this.sortKey === 'category_id') {
          if (keyA < keyB) return this.sortAsc ? -1 : 1;
          if (keyA > keyB) return this.sortAsc ? 1 : -1;
          return 0;
        } else {
          if (valueA[this.sortKey] < valueB[this.sortKey]) return this.sortAsc ? -1 : 1;
          if (valueA[this.sortKey] > valueB[this.sortKey]) return this.sortAsc ? 1 : -1;
          return 0;
        }
      });

      return Object.fromEntries(sorted);
    },
    sortedItemFinances() {
      const sorted = Object.entries(this.itemFinances).sort((a, b) => {
        const [keyA, valueA] = a;
        const [keyB, valueB] = b;

        if (this.sortKeyItem === 'item' || this.sortKeyItem === 'item_id') {
          if (keyA < keyB) return this.sortAscItem ? -1 : 1;
          if (keyA > keyB) return this.sortAscItem ? 1 : -1;
          return 0;
        } else {
          if (valueA[this.sortKeyItem] < valueB[this.sortKeyItem]) return this.sortAscItem ? -1 : 1;
          if (valueA[this.sortKeyItem] > valueB[this.sortKeyItem]) return this.sortAscItem ? 1 : -1;
          return 0;
        }
      });

      return Object.fromEntries(sorted);
    },
    totalCategoryEarnedWithVat() {
      return Object.values(this.categoryFinances).reduce((sum, finance) => sum + finance.total_earned_with_vat, 0);
    },
    totalCategoryVat() {
      return Object.values(this.categoryFinances).reduce((sum, finance) => sum + finance.total_vat, 0);
    },
    totalCategoryEarnedWithoutVat() {
      return Object.values(this.categoryFinances).reduce((sum, finance) => sum + finance.total_earned_without_vat, 0);
    },
    totalItemEarnedWithVat() {
      return Object.values(this.itemFinances).reduce((sum, finance) => sum + finance.total_earned_with_vat, 0);
    },
    totalItemVat() {
      return Object.values(this.itemFinances).reduce((sum, finance) => sum + finance.total_vat, 0);
    },
    totalItemEarnedWithoutVat() {
      return Object.values(this.itemFinances).reduce((sum, finance) => sum + finance.total_earned_without_vat, 0);
    }
  },
  methods: {
    async fetchFinances() {
      try {
        const response = await axios.get('/finances');
        this.categoryFinances = response.data.categories;
        this.itemFinances = response.data.items;
        this.mostSoldItem = response.data.mostSoldItem;
        this.activeOrders = response.data.activeOrders;
        this.canceledOrders = response.data.canceledOrders;
        this.closedOrders = response.data.closedOrders;
        this.totalEarnedWithVat = response.data.totalEarnedWithVat;
        this.totalVat = response.data.totalVat;
        this.totalEarnedWithoutVat = response.data.totalEarnedWithoutVat;

        this.renderChart();
      } catch (error) {
        console.error("There was an error fetching the financial data:", error);
      }
    },
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortAsc = !this.sortAsc;
      } else {
        this.sortKey = key;
        this.sortAsc = true;
      }
    },
    sortByItem(key) {
      if (this.sortKeyItem === key) {
        this.sortAscItem = !this.sortAscItem;
      } else {
        this.sortKeyItem = key;
        this.sortAscItem = true;
      }
    },

    getImageUrl(image) {
        return `http://localhost:8000/storage/uploads/${image}`;
      },

    renderChart() {
      const ctx = document.getElementById('myChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Active Orders', 'Canceled Orders', 'Closed Orders'],
          datasets: [{
            label: 'Orders',
            data: [this.activeOrders, this.canceledOrders, this.closedOrders],
            backgroundColor: ['#4caf50', '#f44336', '#2196f3']
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }
  },
  mounted() {
    this.fetchFinances();
  }
};
</script>

<style scoped>
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
}

h1, h2, h3, h4, h5, h6 {
  margin: 0 0 10px;
  padding: 0;
}

p {
  margin: 0 0 10px;
  padding: 0;
}

.tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 20px;
}

.tabs button {
  padding: 10px 15px;
  cursor: pointer;
  border: none;
  background-color: #f0f0f0;
  border-radius: 5px;
}

.tabs button:focus {
  outline: none;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

th, td {
  padding: 8px;
  text-align: left;
  border: 1px solid #ddd;
}

th {
  cursor: pointer;
}

.statistics-cards {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  margin-bottom: 20px;
}

.card {
  border: 1px solid #ddd;
  border-radius: 5px;
  padding: 20px;
  flex: 1 1 300px;
}

.item-card {
  display: flex;
  gap: 20px;
}

.item-card img {
  max-width: 100px;
  border-radius: 5px;
}

.charts {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-bottom: 20px;
}

#myChart {
  max-width: 600px;
  width: 100%;
}

@media (max-width: 600px) {
  .tabs {
    flex-direction: column;
  }

  .tabs button {
    width: 100%;
    margin: 0 0 10px;
  }

  .statistics-cards {
    flex-direction: column;
  }

  .item-card {
    flex-direction: column;
    align-items: center;
  }

  .item-card img {
    max-width: 100%;
  }

  #myChart {
    max-width: 100%;
  }
}
</style>
