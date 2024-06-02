<script setup>
import { onMounted, ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

const isLoading = ref(true);
const router = useRouter();
const purchaseHistory = ref([]);
const userId = ref(null);
const user = ref({});
const groupStatus = ref({});
const filterStatus = ref('all');
const searchEmail = ref('');
const searchPhone = ref('');

const groupPurchasesByTime = (purchases) => {
  const grouped = {};
  purchases.forEach(purchase => {
    const date = new Date(purchase.created_at);
    const roundedTime = new Date(Math.round(date.getTime() / 10000) * 10000).toLocaleTimeString();
    const fullDate = date.toLocaleDateString();
    if (!grouped[`${fullDate} ${roundedTime}`]) {
      grouped[`${fullDate} ${roundedTime}`] = {
        items: [],
        totalPrice: 0,
        createdAt: purchase.created_at,
        user: purchase.user
      };
      groupStatus.value[`${fullDate} ${roundedTime}`] = purchase.status === 'closed'; // Initialize group status
    }
    grouped[`${fullDate} ${roundedTime}`].items.push(purchase);
    grouped[`${fullDate} ${roundedTime}`].totalPrice += parseFloat(purchase.total_price);
  });
  return grouped;
};

const formatDateTime = (datetime) => {
  const date = new Date(datetime);
  const formattedDate = date.toLocaleDateString('en-GB').replace(/\//g, '.'); // DD.MM.YYYY
  const formattedTime = date.toLocaleTimeString(); // HH:MM:SS
  return `${formattedDate} ${formattedTime}`;
};

const generatePDF = async (time, purchaseGroup, user) => {
  const doc = new jsPDF();
  let yPosition = 20;

  const logoUrl = '/favicon.ico';
  const logoBase64 = await fetch(logoUrl)
    .then(res => res.blob())
    .then(blob => new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onloadend = () => resolve(reader.result);
      reader.onerror = reject;
      reader.readAsDataURL(blob);
    }));

  const logoHeight = 15; 
  const logoWidth = 15;  
  const logoX = 80;    
  const logoY = 10;    
  doc.addImage(logoBase64, 'PNG', logoX, logoY, logoWidth, logoHeight);

  yPosition += 20;

  doc.setFontSize(18);
  doc.text('Frenko', 105, logoY + logoHeight / 2 + 4, null, null, 'center');

  yPosition += 10;

  doc.setFontSize(12);
  doc.text(`Purchased At: ${formatDateTime(purchaseGroup.createdAt)}`, 10, yPosition);
  yPosition += 10;

  doc.setFontSize(12);
  doc.text(`Buyer: ${user.name} ${user.surname}`, 10, yPosition);
  yPosition += 5;
  doc.text(`Email: ${user.email}`, 10, yPosition);
  yPosition += 5;
  doc.text(`Phone: ${user.phone}`, 10, yPosition);
  yPosition += 10;

  doc.setLineWidth(0.5);
  doc.line(10, yPosition, 200, yPosition);
  yPosition += 10;

  const tableColumn = ["Item Name", "Price without VAT (€)", "VAT Amount (€)", "VAT %", "Total Price (€)"];
  const tableRows = [];

  purchaseGroup.items.forEach(purchase => {
    const { priceWithoutVAT, vatAmount, vatPercentage } = calculateVAT(parseFloat(purchase.total_price));
    const purchaseData = [
      purchase.item.name,
      priceWithoutVAT,
      vatAmount,
      vatPercentage,
      purchase.total_price
    ];
    tableRows.push(purchaseData);
  });

  doc.autoTable({
    startY: yPosition,
    head: [tableColumn],
    body: tableRows,
    theme: 'grid',
    headStyles: {
      fillColor: [220, 220, 220],
      textColor: [0, 0, 0],
      fontStyle: 'bold'
    }
  });

  yPosition = doc.lastAutoTable.finalY + 10;

  const totalPrice = purchaseGroup.totalPrice;
  const { priceWithoutVAT: totalWithoutVAT, vatAmount: totalVAT } = calculateVAT(totalPrice);

  const totalTableColumn = ["Description", "Amount (€)"];
  const totalTableRows = [
    ["Total Price without VAT", totalWithoutVAT],
    ["Total VAT Amount", totalVAT],
    ["Total Price", totalPrice.toFixed(2)]
  ];

  doc.autoTable({
    startY: yPosition,
    head: [totalTableColumn],
    body: totalTableRows,
    theme: 'grid',
    headStyles: {
      fillColor: [220, 220, 220],
      textColor: [0, 0, 0],
      fontStyle: 'bold'
    }
  });

  yPosition = doc.lastAutoTable.finalY + 20;

  doc.setFontSize(12);
  doc.text('Payment Terms and Conditions', 10, yPosition);
  yPosition += 5;
  doc.setFontSize(10);
  doc.text('Payment is due within 30 days from the invoice date. Late payments may be subject to a late fee.', 10, yPosition);
  yPosition += 10;

  doc.setFontSize(12);
  doc.text('Issuer:', 10, yPosition);
  doc.text('Receiver:', 120, yPosition);
  yPosition += 10;
  doc.text('Name Surname: ____________________', 10, yPosition);
  doc.text('Name Surname: ____________________', 120, yPosition);
  yPosition += 5;
  doc.text('Date:                  ____________________', 10, yPosition);
  doc.text('Date:                  ____________________', 120, yPosition);
  yPosition += 5;
  doc.text('Signature:           ____________________', 10, yPosition);

  doc.text('Signature:           ____________________', 120, yPosition);

  doc.save(`purchase_history_${time}.pdf`);
};

const calculateVAT = (price) => {
  const priceWithoutVAT = price / 1.21;
  const vatAmount = price - priceWithoutVAT;
  const vatPercentage = 21;
  return {
    priceWithoutVAT: priceWithoutVAT.toFixed(2),
    vatAmount: vatAmount.toFixed(2),
    vatPercentage: vatPercentage.toFixed(2)
  };
};

const changeStatus = async (purchaseGroup, time) => {
  try {
    console.log('Changing status for purchases at time:', time);
    const updatePromises = purchaseGroup.items.map(purchase => {
      console.log(`Updating purchase ID: ${purchase.id}`);
      return axios.patch(`http://127.0.0.1:8000/api/purchases/${purchase.id}/status`, { status: 'closed' });
    });
    await Promise.all(updatePromises);
    groupStatus.value[time] = true;
  } catch (error) {
    console.error('Error updating purchase status:', error.response ? error.response.data : error.message);
  }
};

onMounted(async () => {
  try {
    const userData = await axios.get('http://127.0.0.1:8000/api/user');
    userId.value = userData.data.id;
    user.value = userData.data;

    const purchaseData = await axios.get(`http://127.0.0.1:8000/api/purchases/user/${userId.value}`);
    const sortedPurchases = purchaseData.data.purchases.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    purchaseHistory.value = groupPurchasesByTime(sortedPurchases);

    Object.keys(purchaseHistory.value).forEach(time => {
      const purchaseGroup = purchaseHistory.value[time];
      groupStatus.value[time] = purchaseGroup.items.every(purchase => purchase.status === 'closed');
    });

  } catch (error) {
    if (error.response && error.response.status === 401) {
      await router.push({ name: 'login' });
    }
  } finally {
    isLoading.value = false;
  }
});

const filteredPurchaseHistory = computed(() => {
  return Object.keys(purchaseHistory.value).filter(time => {
    const purchaseGroup = purchaseHistory.value[time];
    const matchesStatus = filterStatus.value === 'all' || purchaseGroup.items.every(purchase => purchase.status === filterStatus.value);
    const matchesEmail = searchEmail.value === '' || (purchaseGroup.user.email && purchaseGroup.user.email.includes(searchEmail.value));
    const matchesPhone = searchPhone.value === '' || (purchaseGroup.user.phone && purchaseGroup.user.phone.toString().includes(searchPhone.value));
    return matchesStatus && matchesEmail && matchesPhone;
  }).reduce((result, time) => {
    result[time] = purchaseHistory.value[time];
    return result;
  }, {});
});
</script>

<template>
  <main>
    <div class="container">
      <div class="card mt-4">
        <div class="card-body">
          <div>
            <h1>Purchase History</h1>
            <div class="filter-section mb-4">
              <div class="form-group">
                <label for="statusFilter">Filter by Status:</label>
                <select id="statusFilter" v-model="filterStatus" class="form-control">
                  <option value="all">All</option>
                  <option value="active">Active</option>
                  <option value="closed">Closed</option>
                </select>
              </div>
              <div class="form-group">
                <label for="searchEmail">Search by Email:</label>
                <input id="searchEmail" type="text" v-model="searchEmail" class="form-control" placeholder="Enter email">
              </div>
              <div class="form-group">
                <label for="searchPhone">Search by Phone:</label>
                <input id="searchPhone" type="text" v-model="searchPhone" class="form-control" placeholder="Enter phone">
              </div>
            </div>
            <div v-if="isLoading" class="d-flex justify-content-center my-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <div v-else-if="Object.keys(filteredPurchaseHistory).length === 0">
              No purchases found.
            </div>
            <div v-else>
              <ul class="list-group">
                <li v-for="(purchaseGroup, time) in filteredPurchaseHistory" :key="time" :class="{'bg-lightgreen': groupStatus[time]}" class="list-group-item">
                  <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                      <strong>Purchased At:</strong> {{ time }}
                    </div>
                    <div class="btn-group">
                      <button class="btn btn-primary mb-2 mb-sm-0 me-0" @click="generatePDF(time, purchaseGroup, user)">Print PDF</button>
                      <button class="btn mb-2 mb-sm-0 me-0" :class="groupStatus[time] ? 'btn-danger' : 'btn-secondary'" @click="changeStatus(purchaseGroup, time)" :disabled="groupStatus[time]">
                        {{ groupStatus[time] ? 'Closed' : 'Mark as closed' }}
                      </button>
                    </div>
                  </div>
                  <div>
                    <strong>User:</strong> {{ purchaseGroup.user.name }} {{ purchaseGroup.user.surname }} - {{ purchaseGroup.user.email }} - {{ purchaseGroup.user.phone }}
                  </div>
                  <ul>
                    <li v-for="purchase in purchaseGroup.items" :key="purchase.id">
                      <div>
                        <strong>Item:</strong> {{ purchase.item.name }}
                      </div>
                      <div>
                        <strong>Price:</strong> {{ purchase.total_price }}€
                      </div>
                    </li>
                  </ul>
                  <h2>Total price: {{ purchaseGroup.totalPrice.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') }}€</h2>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>



<style scoped>
.bg-lightgreen {
  background-color: lightgreen;
}

.filter-section {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}

@media (min-width: 768px) {
  .filter-section {
    flex-direction: row;
    justify-content: space-between;
  }

  .form-group {
    flex: 1;
    margin-right: 20px;
  }
}

.container {
  margin-bottom: 20px;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
}

.btn-group {
  display: flex;
  flex-wrap: wrap;
  flex-direction: row;
}

.btn {
  padding: 6px 7px 6px 7px;
}

@media (max-width: 767px) {
  .btn-group {
    gap: 0;
  }
}
</style>
