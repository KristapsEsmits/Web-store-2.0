<template>
    <div v-if="purchaseGroup">
      <h1 class="success_msg">Congratulations!</h1>
      <div class="card-container">
        <div class="card mt-4">
          <div class="card-body">
            <h1>Your order:</h1>
            <div>
              <ul class="list-group">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <strong>Purchased At:</strong> {{ purchaseGroup.time }}
                  </div>
                  <button class="btn btn-primary" @click="generatePDF(purchaseGroup.time, purchaseGroup)">Print PDF</button>
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
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="didnt-buy" v-else>
      <img src="/basket.png" class="basket_img">
      <h1>Hmmm... You didn't purchase anything!</h1>
      <h1>Try <RouterLink class="toProducts" to="/products">browsing</RouterLink> for something.</h1>
    </div>
  </template>
  
  <script setup>
  import { onMounted, ref } from 'vue';
  import { useRouter } from 'vue-router';
  import jsPDF from 'jspdf';
  import 'jspdf-autotable';
  
  const purchaseGroup = ref(null);
  
  onMounted(() => {
    purchaseGroup.value = JSON.parse(localStorage.getItem('recent_purchase'));
    if (purchaseGroup.value) {
      purchaseGroup.value.time = new Date(purchaseGroup.value.time).toLocaleString();
    }
    localStorage.removeItem('purchase_completed');
    localStorage.removeItem('recent_purchase');
  });
  
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
    doc.text(`Purchased At: ${time}`, 10, yPosition);
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
  </script>
  
  <style scoped>
  .card-container {
    margin: 50px;
  }
  
  .didnt-buy {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding-top: 250px;
    text-align: center;
  }
  
  .basket_img {
    width: 200px;
    height: 150px;
    margin: 0 auto;
    user-select: none;
  }
  
  .toProducts {
    color: #000000;
  }
  
  .success_msg {
    text-align: center;
    margin-top: 50px;
  }
  
  @media screen and (min-width: 768px) {
    .basket_img {
      width: 300px;
      height: 250px;
    }
  }
  </style>
  