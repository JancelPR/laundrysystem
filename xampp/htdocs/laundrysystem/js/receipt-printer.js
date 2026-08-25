/**
 * LaundryEase POS Receipt & Claim Stub Engine
 * Generates standard 80mm thermal receipts and claim stubs with QR verification codes.
 */

const ReceiptPrinter = {
  storeInfo: {
    name: "LaundryEase Service Center",
    tagline: "Professional Fabric Care & Laundromat",
    address: "Taculing Main Road, Bacolod City",
    phone: "0912-345-6789 / (034) 432-1098",
    email: "support@laundryease.ph",
    hours: "Open Daily: 7:00 AM - 8:00 PM"
  },

  /**
   * Generates and opens the receipt modal with live thermal print capabilities
   * @param {Object} order - Order record data
   */
  openReceipt(order) {
    const modalId = "thermalReceiptModal";
    let modal = document.getElementById(modalId);

    if (!modal) {
      modal = document.createElement("div");
      modal.id = modalId;
      modal.className = "receipt-modal-backdrop";
      document.body.appendChild(modal);
    }

    const orderCode = order.order_code || "ORD-0000";
    const custName = order.full_name || order.customer_name || "Valued Customer";
    const custPhone = order.phone || order.customer_phone || "N/A";
    const services = order.services_registered || "Standard Laundry";
    const weight = parseFloat(order.weight_kg || 0).toFixed(2);
    const totalPrice = parseFloat(order.total_price || 0).toFixed(2);
    const paymentStatus = order.payment_status || "Unpaid";
    const orderStatus = order.order_status || "Pending";
    const dateFormatted = order.dropped_off_at ? new Date(order.dropped_off_at).toLocaleString() : new Date().toLocaleString();
    
    // Tracking URL for QR Code
    const trackingUrl = encodeURIComponent(`${window.location.origin}/index.html?track=${orderCode}`);
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${trackingUrl}`;

    modal.innerHTML = `
      <div class="receipt-dialog-container">
        <div class="receipt-dialog-header">
          <div class="receipt-title-box">
            <span class="receipt-icon">🧾</span>
            <div>
              <h3>Thermal Receipt & Claim Stub</h3>
              <p>Standard 80mm Roll Format • Order #${orderCode}</p>
            </div>
          </div>
          <button type="button" class="receipt-close-btn" onclick="ReceiptPrinter.closeReceipt()">&times;</button>
        </div>

        <div class="receipt-dialog-body">
          <div class="receipt-paper" id="printableThermalReceipt">
            <!-- Header -->
            <div class="receipt-center-text receipt-brand">
              <h2>${this.storeInfo.name}</h2>
              <p class="receipt-sub">${this.storeInfo.tagline}</p>
              <p class="receipt-address">${this.storeInfo.address}</p>
              <p class="receipt-contact">Tel: ${this.storeInfo.phone}</p>
              <p class="receipt-hours">${this.storeInfo.hours}</p>
            </div>

            <div class="receipt-divider-dashed"></div>

            <!-- Receipt Metadata -->
            <div class="receipt-row-meta">
              <span><strong>CLAIM STUB #:</strong></span>
              <span class="receipt-code-highlight">${orderCode}</span>
            </div>
            <div class="receipt-row-meta">
              <span>Date / Time:</span>
              <span>${dateFormatted}</span>
            </div>
            <div class="receipt-row-meta">
              <span>Customer:</span>
              <span><strong>${custName}</strong></span>
            </div>
            <div class="receipt-row-meta">
              <span>Mobile:</span>
              <span>${custPhone}</span>
            </div>

            <div class="receipt-divider-dashed"></div>

            <!-- Service Itemized Breakdown -->
            <table class="receipt-table">
              <thead>
                <tr>
                  <th align="left">Service Description</th>
                  <th align="center">Qty/Wt</th>
                  <th align="right">Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td align="left">${services}</td>
                  <td align="center">${weight} kg</td>
                  <td align="right">₱${totalPrice}</td>
                </tr>
              </tbody>
            </table>

            <div class="receipt-divider-dashed"></div>

            <!-- Totals -->
            <div class="receipt-summary-box">
              <div class="receipt-row-total">
                <span>TOTAL AMOUNT:</span>
                <span>₱${totalPrice}</span>
              </div>
              <div class="receipt-row-meta" style="margin-top: 4px;">
                <span>Payment Status:</span>
                <span class="receipt-badge ${paymentStatus.toLowerCase() === 'paid' ? 'badge-paid' : 'badge-unpaid'}">${paymentStatus.toUpperCase()}</span>
              </div>
              <div class="receipt-row-meta">
                <span>Order Status:</span>
                <span><strong>${orderStatus}</strong></span>
              </div>
            </div>

            <div class="receipt-divider-dashed"></div>

            <!-- QR Verification & Barcode -->
            <div class="receipt-center-text receipt-qr-section">
              <img src="${qrCodeUrl}" alt="Order QR Code" class="receipt-qr-img" onerror="this.style.display='none'">
              <p class="receipt-qr-caption">Scan QR code to track wash progress</p>
              <div class="receipt-barcode-display">*${orderCode}*</div>
            </div>

            <!-- Terms & Policy -->
            <div class="receipt-terms">
              <p><strong>TERMS & CONDITIONS:</strong></p>
              <p>1. Please present this claim stub upon pickup.</p>
              <p>2. Unclaimed items after 30 days will be disposed according to store policy.</p>
              <p>3. Check all garments before leaving the store premises.</p>
              <p class="receipt-center-text" style="margin-top: 8px; font-weight: bold;">Thank you for choosing LaundryEase!</p>
            </div>
          </div>
        </div>

        <div class="receipt-dialog-footer">
          <button type="button" class="btn-receipt-secondary" onclick="ReceiptPrinter.closeReceipt()">Close</button>
          <button type="button" class="btn-receipt-primary" onclick="ReceiptPrinter.printReceipt()">
            <span>🖨️</span> <span>Print Thermal Receipt</span>
          </button>
        </div>
      </div>
    `;

    modal.classList.add("active");
  },

  closeReceipt() {
    const modal = document.getElementById("thermalReceiptModal");
    if (modal) {
      modal.classList.remove("active");
    }
  },

  printReceipt() {
    window.print();
  }
};

window.ReceiptPrinter = ReceiptPrinter;
