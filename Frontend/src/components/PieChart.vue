<template>
  <div>
    <Pie :data="chartData" :options="chartOptions"/>
  </div>
</template>

<script>
import {Pie} from 'vue-chartjs';
import {ArcElement, CategoryScale, Chart as ChartJS, Legend, Title, Tooltip} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale);

export default {
  components: {
    Pie,
  },
  props: {
    totalAmount: {
      type: Number,
      required: true,
    },
    amountWithVAT: {
      type: Number,
      required: true,
    },
    amountWithoutVAT: {
      type: Number,
      required: true,
    },
  },
  computed: {
    chartData() {
      return {
        labels: ['Total Amount', 'Total VAT', 'Total Amount Without VAT'],
        datasets: [
          {
            label: 'Finance Data',
            backgroundColor: ['#42A5F5', '#66BB6A', '#FFA726'],
            data: [this.totalAmount, this.amountWithVAT, this.amountWithoutVAT],
          },
        ],
      };
    },
    chartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
      };
    },
  },
};
</script>
  