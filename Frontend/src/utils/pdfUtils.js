import jsPDF from 'jspdf';
import 'jspdf-autotable';

const formatDateTime = (datetime) => {
  const date = new Date(datetime);
  const formattedDate = date.toLocaleDateString('en-GB').replace(/\//g, '.'); // DD.MM.YYYY
  const formattedTime = date.toLocaleTimeString(); // HH:MM:SS
  return `${formattedDate} ${formattedTime}`;
};

const calculateVAT = (price, vatRate) => {
  const priceWithoutVAT = price / (1 + vatRate / 100);
  const vatAmount = price - priceWithoutVAT;
  return {
    priceWithoutVAT: priceWithoutVAT.toFixed(2),
    vatAmount: vatAmount.toFixed(2),
    vatPercentage: vatRate.toFixed(2)
  };
};

export const generatePDF = async (time, purchaseGroup, user) => {
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

  const tableColumn = ["Item Name", "Quantity", "Price without VAT (€)", "VAT Amount (€)", "VAT %", "Total Price (€)"];
  const tableRows = [];

  purchaseGroup.items.forEach(purchase => {
    const { priceWithoutVAT, vatAmount, vatPercentage } = calculateVAT(parseFloat(purchase.total_price), purchase.item.vat);
    const purchaseData = [
      purchase.item.name,
      purchase.quantity,
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
  const { priceWithoutVAT: totalWithoutVAT, vatAmount: totalVAT } = calculateVAT(totalPrice, purchaseGroup.items[0].item.vat); // Assuming same VAT rate for simplicity

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
  doc.text('Payment is due within 30 days from the purchase date. After 30 days purchases will be canceled!', 10, yPosition);
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
