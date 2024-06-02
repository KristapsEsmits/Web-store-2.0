<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import jsPDF from 'jspdf';
import 'jspdf-autotable';

const user = ref(null);
const isLoading = ref(true);
const router = useRouter();
const successMessage = ref(router.currentRoute.value.query.successMessage || '');
const purchaseHistory = ref([]);

const dismissSuccessMessage = () => {
  successMessage.value = '';
};

const groupPurchasesByTime = (purchases) => {
  const grouped = {};
  purchases.forEach(purchase => {
    const date = new Date(purchase.created_at);
    const roundedTime = new Date(Math.round(date.getTime() / 10000) * 10000).toLocaleTimeString();
    const fullDate = date.toLocaleDateString('en-GB').replace(/\//g, '.');
    if (!grouped[`${fullDate} ${roundedTime}`]) {
      grouped[`${fullDate} ${roundedTime}`] = {
        items: [],
        totalPrice: 0,
        createdAt: `${fullDate} ${roundedTime}`,
        status: purchase.status
      };
    }
    grouped[`${fullDate} ${roundedTime}`].items.push(purchase);
    grouped[`${fullDate} ${roundedTime}`].totalPrice += parseFloat(purchase.total_price);
  });
  return grouped;
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

const generatePDF = async (time, purchaseGroup) => {
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
  doc.text(`Purchased At: ${purchaseGroup.createdAt}`, 10, yPosition);
  yPosition += 10;

  doc.setFontSize(12);
  doc.text(`Buyer: ${user.value.name} ${user.value.surname}`, 10, yPosition);
  yPosition += 5;
  doc.text(`Email: ${user.value.email}`, 10, yPosition);
  yPosition += 5;
  doc.text(`Phone: ${user.value.phone}`, 10, yPosition);
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

onMounted(async () => {
  try {
    const userData = await axios.get('http://127.0.0.1:8000/api/user');
    user.value = userData.data;

    const purchaseData = await axios.get(`http://127.0.0.1:8000/api/purchases/user/${user.value.id}`);
    purchaseHistory.value = groupPurchasesByTime(purchaseData.data.purchases);
  } catch (error) {
    if (error.response && error.response.status === 401) {
      await router.push({ name: 'login' });
    }
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <main>
    <div class="container">
      <div v-if="successMessage" class="alert alert-success d-flex justify-content-between align-items-center">
        <span>{{ successMessage }}</span>
        <button class="btn-close" type="button" @click="dismissSuccessMessage"></button>
      </div>
      <div class="card">
        <div class="card-body">
          <div class="profile-actions d-flex justify-content-end">
            <router-link :to="{ path: '/profile/edit' }" class="actionBtn nav-link">Update data</router-link>
            <router-link :to="{ path: '/profile/change-password' }" class="nav-link">Change password</router-link>
          </div>
          <h1>{{ user?.name }} {{ user?.surname }}</h1>
          <h2>Number: {{ user?.phone }}</h2>
          <h2>Email: {{ user?.email }}</h2>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-body">
          <div>
            <h1>Purchase History</h1>
            <div v-if="Object.keys(purchaseHistory).length === 0">No purchases found.</div>
            <div v-else>
              <ul class="list-group">
                <li v-for="(purchaseGroup, time) in purchaseHistory" :key="time"
                    :class="{'bg-lightgreen': purchaseGroup.items.status === 'closed'}" class="list-group-item">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <strong>Purchased At:</strong> {{ time }}
                    </div>
                    <button class="btn btn-primary" @click="generatePDF(time, purchaseGroup)">Print PDF</button>
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
  background-color: rgb(191, 253, 191);
}

.card {
  max-width: 100%;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 20px;
}

.profile-actions {
  margin-bottom: 10px;
}

.actionBtn {
  margin-right: 20px !important;
}

@media (max-width: 768px) {
  .container {
    padding: 10px;
  }

  h1, h2 {
    font-size: 1.2em;
  }

  .card-body {
    padding: 10px;
  }

  .btn {
    font-size: 0.8em;
  }

  .list-group-item {
    padding: 10px;
  }

  .profile-actions {
    flex-direction: column;
    align-items: flex-end;
  }

  .actionBtn {
    margin-right: 0 !important;
  }
}
</style>
