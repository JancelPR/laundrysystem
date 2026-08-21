<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard — LaundryEase</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/aquatic-theme.css?v=2">
</head>
<body>

  <!-- TOP HEADER BAR -->
  <header class="top-header-bar">
    <div class="top-bar-left">
      <div class="brand-icon-box">🧺</div>
      <div class="brand-text-block">
        <div class="brand-title-row">
          <span class="brand-name-text">LaundryEase</span>
        </div>
        <span class="brand-subtitle-text">Service Tracking and Profit Computation Platform</span>
      </div>
    </div>

    <div class="top-bar-right">
      <div class="user-role-badge">
        <span>👤</span> <span id="topCustRoleBadge">Customer</span>
      </div>
    </div>
  </header>

  <!-- SIDEBAR NAVIGATION -->
  <aside>
    <div>
      <div class="brand-section">
        <div class="brand-dot"></div>
        <span class="brand-signature">Main Navigation</span>
      </div>

      <nav class="nav-group">
        <a href="#" class="nav-item is-active">
          <span>📦</span> <span>My Tracked Loads</span>
        </a>
        <a href="#" class="nav-item">
          <span>🏷️</span> <span>Pricing Packages</span>
        </a>
        <a href="#" class="nav-item">
          <span>⚙️</span> <span>Preferences</span>
        </a>
      </nav>
    </div>

    <div class="user-profile-summary">
      <div class="user-name" id="displayUserName">Loading...</div>
      <div class="user-phone" id="displayUserPhone">...</div>
      <button class="logout-btn" onclick="logout()">← Sign Out</button>
    </div>
  </aside>

  <main>
    <div class="view-header">
      <h1 class="view-title">Welcome, <span id="welcomeCustomerName" style="color: #38bdf8;">Customer</span></h1>
      <p class="view-subtitle">Real-time status updates generated directly from the workshop desk.</p>
    </div>

    <div class="metrics-row">
      <div class="metric-card">
        <div class="metric-label">Active Orders</div>
        <div class="metric-value" id="metricActiveOrders">0 Loads</div>
      </div>
      <div class="metric-card">
        <div class="metric-label">Next-Day Turnaround</div>
        <div class="metric-value" style="font-size:1.4rem;">Ready Tomorrow</div>
      </div>
      <div class="metric-card">
        <div class="metric-label">Standard Base Rate</div>
        <div class="metric-value">₱50 <span style="font-size:1rem; font-weight:400; color:var(--text-muted);">/ kg</span></div>
      </div>
    </div>

    <div class="data-table-container">
      <div class="table-section-title">Current Laundry Log</div>
      <table>
        <thead>
          <tr>
            <th>Unique Order ID</th>
            <th>Services Registered</th>
            <th>Weight / Pricing</th>
            <th>Special Instructions Given</th>
            <th>Order Status</th>
          </tr>
        </thead>
        <tbody id="ordersTableBody">
          <tr>
            <td colspan="5" style="text-align:center; color: var(--text-muted);">Loading laundry log...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', fetchDashboardData);

    async function fetchDashboardData() {
      const token = localStorage.getItem('authToken');

      if (!token) {
        window.location.href = 'index.html';
        return;
      }

      try {
        const response = await fetch('api/get_customer_orders.php', {
          method: 'GET',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
          }
        });

        if (!response.ok) {
          localStorage.clear();
          window.location.href = 'index.html';
          return;
        }

        const data = await response.json();
        
        const customerName = data.user.fullName || 'Customer';
        document.getElementById('welcomeCustomerName').innerText = customerName;
        document.getElementById('displayUserName').innerText = customerName;
        document.getElementById('topCustRoleBadge').innerText = customerName;
        document.getElementById('displayUserPhone').innerText = data.user.phone || 'N/A';

        const tbody = document.getElementById('ordersTableBody');
        tbody.innerHTML = '';

        let activeCount = 0;

        if (!data.orders || data.orders.length === 0) {
          tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; color:var(--text-muted);">No orders recorded yet.</td></tr>`;
          return;
        }

        data.orders.forEach(order => {
          if (order.order_status !== 'Completed' && order.order_status !== 'Picked Up' && order.order_status !== 'Cancelled') activeCount++;

          const dateFormatted = new Date(order.dropped_off_at).toLocaleDateString('en-US', {
            month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
          });

          const statusBadge = order.order_status === 'Picked Up' || order.order_status === 'Completed'
            ? `<span class="badge badge-completed">✅ Completed</span>`
            : `<span class="badge badge-processing">${order.order_status}</span>`;

          const paymentBadge = order.payment_status === 'Paid'
            ? `<span class="badge badge-paid">Paid</span>`
            : `<span class="badge badge-unpaid">Unpaid</span>`;

          const row = `
            <tr>
              <td>
                <strong style="color:#ffffff;">${order.order_code}</strong>
                <div class="text-small-muted">Dropped off: ${dateFormatted}</div>
              </td>
              <td>
                <div style="color:#f0fdfa;">${order.services_registered}</div>
              </td>
              <td>
                <div>${parseFloat(order.weight_kg).toFixed(1)} Kilograms</div>
                <div class="text-small-muted">Total: ₱${parseFloat(order.total_price).toFixed(2)}</div>
              </td>
              <td>
                <div style="font-size: 0.85rem; color: #cbd5e1; white-space: pre-line;">
                  ${order.special_instructions || 'None'}
                </div>
              </td>
              <td>
                ${statusBadge}
                <div style="margin-top: 8px;">
                  ${paymentBadge}
                </div>
              </td>
            </tr>
          `;
          tbody.innerHTML += row;
        });

        document.getElementById('metricActiveOrders').innerText = `${activeCount} Load${activeCount !== 1 ? 's' : ''}`;

      } catch (err) {
        console.error("Error fetching dashboard data:", err);
      }
    }

    function logout() {
      localStorage.clear();
      sessionStorage.clear();
      window.location.replace('index.html');
    }
  </script>
</body>
</html>
