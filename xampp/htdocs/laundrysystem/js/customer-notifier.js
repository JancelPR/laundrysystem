/**
 * LaundryEase Customer Notification Dispatcher
 * Allows 1-click WhatsApp, SMS, and clipboard message dispatch for real-time customer alerts.
 */

const CustomerNotifier = {
  openNotifyModal(order) {
    const modalId = "customerNotifyModal";
    let modal = document.getElementById(modalId);

    if (!modal) {
      modal = document.createElement("div");
      modal.id = modalId;
      modal.className = "notify-modal-backdrop";
      document.body.appendChild(modal);
    }

    const orderCode = order.order_code || "ORD-0000";
    const custName = order.full_name || order.customer_name || "Valued Customer";
    const custPhone = order.phone || order.customer_phone || "";
    const services = order.services_registered || "Laundry Service";
    const weight = parseFloat(order.weight_kg || 0).toFixed(2);
    const totalPrice = parseFloat(order.total_price || 0).toFixed(2);
    const orderStatus = order.order_status || "Ready for Pickup";
    const paymentStatus = order.payment_status || "Unpaid";

    // Format Philippine or international phone number
    let cleanPhone = custPhone.replace(/[^0-9]/g, "");
    if (cleanPhone.startsWith("09") && cleanPhone.length === 11) {
      cleanPhone = "63" + cleanPhone.substring(1);
    }

    const messageTemplate = `Hi ${custName}! 🧺 Your laundry order #${orderCode} (${services}, ${weight}kg) is now ${orderStatus.toUpperCase()} at LaundryEase. Total: ₱${totalPrice} (${paymentStatus}). Store Hours: 7:00 AM - 8:00 PM. Thank you for choosing us! ✨`;

    const encodedMsg = encodeURIComponent(messageTemplate);
    const waUrl = cleanPhone ? `https://wa.me/${cleanPhone}?text=${encodedMsg}` : `https://wa.me/?text=${encodedMsg}`;
    const smsUrl = `sms:${custPhone}?body=${encodedMsg}`;

    modal.innerHTML = `
      <div class="notify-dialog-container">
        <div class="notify-dialog-header">
          <div class="notify-title-box">
            <span class="notify-icon">📲</span>
            <div>
              <h3>Notify Customer via SMS / WhatsApp</h3>
              <p>Direct Message Dispatch • Order #${orderCode}</p>
            </div>
          </div>
          <button type="button" class="notify-close-btn" onclick="CustomerNotifier.closeModal()">&times;</button>
        </div>

        <div class="notify-dialog-body">
          <div class="notify-field-group">
            <label>Customer Contact:</label>
            <div class="notify-contact-info">
              <strong>${custName}</strong> &bull; <span>${custPhone || 'No Phone Registered'}</span>
            </div>
          </div>

          <div class="notify-field-group">
            <label>Notification Message Preview:</label>
            <textarea id="notifyMessageContent" class="notify-textarea" rows="4">${messageTemplate}</textarea>
          </div>

          <div class="notify-actions-grid">
            <a href="${waUrl}" target="_blank" class="btn-notify-channel btn-whatsapp">
              <span>💬</span> <span>Open in WhatsApp</span>
            </a>
            <a href="${smsUrl}" class="btn-notify-channel btn-sms">
              <span>✉️</span> <span>Send via SMS</span>
            </a>
          </div>
        </div>

        <div class="notify-dialog-footer">
          <button type="button" class="btn-notify-secondary" onclick="CustomerNotifier.closeModal()">Dismiss</button>
          <button type="button" class="btn-notify-copy" onclick="CustomerNotifier.copyMessage()">
            <span>📋</span> <span id="copyBtnText">Copy Message Text</span>
          </button>
        </div>
      </div>
    `;

    modal.classList.add("active");
  },

  closeModal() {
    const modal = document.getElementById("customerNotifyModal");
    if (modal) {
      modal.classList.remove("active");
    }
  },

  copyMessage() {
    const textarea = document.getElementById("notifyMessageContent");
    if (textarea) {
      textarea.select();
      navigator.clipboard.writeText(textarea.value).then(() => {
        const btnText = document.getElementById("copyBtnText");
        if (btnText) {
          btnText.textContent = "Copied to Clipboard! ✓";
          setTimeout(() => {
            btnText.textContent = "Copy Message Text";
          }, 2500);
        }
      });
    }
  }
};

window.CustomerNotifier = CustomerNotifier;
