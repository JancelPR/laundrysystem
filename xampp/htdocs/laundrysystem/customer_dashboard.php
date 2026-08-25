<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard — LaundryEase</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/aquatic-theme.css?v=18">
  <script src="js/user-profile.js"></script>
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

  <!-- Mobile Sidebar Backdrop -->
  <div id="sidebarBackdrop" class="sidebar-backdrop" onclick="toggleSidebar(false)"></div>

  <!-- SIDEBAR NAVIGATION -->
  <aside>
    <!-- Circular Edge Toggle Button -->
    <button type="button" class="sidebar-edge-toggle" id="sidebarEdgeToggle" onclick="toggleSidebar()" aria-label="Toggle Sidebar" title="Collapse / Expand Sidebar">
      <svg class="sidebar-edge-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="15 18 9 12 15 6"></polyline>
      </svg>
    </button>

    <div class="sidebar-inner-wrap">
      <div>
        <div class="brand-section">
          <div class="brand-dot"></div>
          <span class="brand-signature">Main Navigation</span>
        </div>

        <nav class="nav-group">
          <a href="#" class="nav-item is-active" data-title="My Tracked Loads">
            <span class="nav-icon">📦</span> <span class="nav-label">My Tracked Loads</span>
          </a>
          <a href="#" class="nav-item" data-title="Pricing Packages">
            <span class="nav-icon">🏷️</span> <span class="nav-label">Pricing Packages</span>
          </a>
        </nav>
      </div>

      <div class="user-profile-summary">
        <a href="javascript:void(0)" class="nav-item" data-title="Settings" onclick="UserProfileManager.openProfileModal()">
          <span class="nav-icon">⚙️</span> <span class="nav-label">Settings</span>
        </a>
        <button class="logout-btn" onclick="event.stopPropagation(); logout();" title="Sign Out" data-title="Sign Out">
          <span class="logout-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
              <line x1="19" y1="12" x2="5" y2="12"></line>
              <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
          </span>
          <span class="logout-text">Sign Out</span>
        </button>
      </div>
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
    document.addEventListener('DOMContentLoaded', () => {
      initSidebarCollapse();
      fetchDashboardData();
    });

    function toggleSidebar(forceState) {
      const isCurrentlyCollapsed = document.body.classList.contains('sidebar-collapsed');
      const newState = (typeof forceState === 'boolean') ? forceState : !isCurrentlyCollapsed;
      
      document.body.classList.toggle('sidebar-collapsed', newState);
      localStorage.setItem('laundry_sidebar_collapsed', newState ? '1' : '0');
      
      const toggleBtn = document.getElementById('sidebarToggleBtn');
      if (toggleBtn) {
        toggleBtn.classList.toggle('is-collapsed', newState);
        toggleBtn.setAttribute('aria-expanded', !newState);
      }
      
      const backdrop = document.getElementById('sidebarBackdrop');
      if (backdrop) {
        if (window.innerWidth <= 768) {
          backdrop.classList.toggle('active', !newState);
        } else {
          backdrop.classList.remove('active');
        }
      }
    }

    function initSidebarCollapse() {
      const saved = localStorage.getItem('laundry_sidebar_collapsed');
      const shouldCollapse = (saved === '1') || (saved === null && window.innerWidth <= 1024);
      if (shouldCollapse) {
        document.body.classList.add('sidebar-collapsed');
        const toggleBtn = document.getElementById('sidebarToggleBtn');
        if (toggleBtn) {
          toggleBtn.classList.add('is-collapsed');
          toggleBtn.setAttribute('aria-expanded', 'false');
        }
      }
    }

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
        
        const customerName = data.user.fullName || data.user.full_name || 'Customer';
        document.getElementById('welcomeCustomerName').innerText = customerName;
        document.getElementById('displayUserName').innerText = customerName;
        document.getElementById('topCustRoleBadge').innerText = customerName;
        document.getElementById('displayUserPhone').innerText = data.user.phone || 'N/A';

        UserProfileManager.init({
          id: data.user.id,
          full_name: customerName,
          phone: data.user.phone || '',
          address: data.user.address || '',
          role: 'customer'
        });

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
