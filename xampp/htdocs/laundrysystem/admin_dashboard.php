<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — Athena's Laundry Shop</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="css/aquatic-theme.css?v=26">
  <script src="js/receipt-printer.js"></script>
  <script src="js/customer-notifier.js"></script>
  <script src="js/user-profile.js"></script>
</head>
<body>

  <!-- TOP HEADER BAR -->
  <header class="top-header-bar">
    <div class="top-bar-left">
      <a href="index.html" title="Athena's Laundry Shop" style="display: flex; align-items: center; text-decoration: none;">
        <img src="images/athena_logo_hd.png" alt="Athena's Laundry Shop" class="brand-logo-dashboard">
      </a>
      <div class="brand-text-block" style="margin-left: 8px;">
        <span class="brand-subtitle-text">Service Tracking & Profit Platform</span>
      </div>
    </div>

    <div class="top-bar-right">
      <div class="user-role-badge">
        <span>👤</span> <span id="topUserRoleName">Admin Manager</span>
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
          <a href="javascript:void(0)" class="nav-item is-active" data-module="overview" data-title="Master Overview" onclick="switchModule('overview', this)">
            <span class="nav-icon">📊</span> <span class="nav-label">Master Overview</span>
          </a>
          <a href="javascript:void(0)" class="nav-item" data-module="records" data-title="Laundry Records" onclick="switchModule('records', this)">
            <span class="nav-icon">🧺</span> <span class="nav-label">Laundry Records</span>
          </a>
          <a href="javascript:void(0)" class="nav-item" data-module="analytics" data-title="Sales Analytics" onclick="switchModule('analytics', this)">
            <span class="nav-icon">📈</span> <span class="nav-label">Sales Analytics</span>
          </a>
          <a href="javascript:void(0)" class="nav-item" data-module="inventory" data-title="Supplies & Inventory" onclick="switchModule('inventory', this)">
            <span class="nav-icon">🧴</span> <span class="nav-label">Supplies & Inventory</span>
          </a>
          
          <!-- Manage Account Dropdown Menu -->
          <div class="nav-item-dropdown">
            <button type="button" class="nav-item nav-item-btn" data-title="User Management" onclick="toggleManageAccountMenu(event)">
              <div class="nav-item-left">
                <span class="nav-icon">👥</span>
                <span class="nav-label">User Management</span>
              </div>
              <svg id="manageAccChevron" class="chevron-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </button>
            <div class="nav-submenu" id="manageAccSubmenu" style="display: none;">
              <a href="javascript:void(0)" class="submenu-item" data-module="staff" data-title="Staff Accounts" onclick="switchModule('staff', this)">
                <span class="nav-icon">👔</span> <span class="nav-label">Staff</span>
              </a>
              <a href="javascript:void(0)" class="submenu-item" data-module="customers" data-title="Customer Accounts" onclick="switchModule('customers', this)">
                <span class="nav-icon">👥</span> <span class="nav-label">Customers</span>
              </a>
            </div>
          </div>
        </nav>
      </div>

      <div class="user-profile-summary">
        <a href="javascript:void(0)" class="nav-item" data-module="settings" data-title="System Settings" onclick="switchModule('settings', this)">
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
    <!-- ========================================== -->
    <!-- MODULE 1: MASTER OVERVIEW                   -->
    <!-- ========================================== -->
    <div id="module-overview" class="dashboard-module-view" style="display: block;">
      <div class="view-header">
        <div>
          <h1 class="view-title">Master Overview</h1>
          <p class="view-subtitle">High-level operational performance, key laundry metrics, and store snapshot.</p>
        </div>
        <button class="btn-create-order" onclick="window.openNewOrderModal()">
          <span>🧺</span> + New Order
        </button>
      </div>

      <!-- Overview KPI Cards -->
      <div class="metrics-row">
        <div class="metric-card">
          <div class="metric-label">Total Revenue</div>
          <div class="metric-value" id="metricRevenue">₱0.00</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Total Orders</div>
          <div class="metric-value" id="metricTotalOrders">0</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Active Wash Loads</div>
          <div class="metric-value" id="metricActiveLoads">0</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Total Registered Clients</div>
          <div class="metric-value" id="metricTotalCustomers">0</div>
        </div>
      </div>

      <!-- Quick Action & Queue Highlights Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-800 flex items-center justify-between">
          <div>
            <div class="text-xs text-slate-400 font-medium">Pending Intake</div>
            <div class="text-xl font-bold text-amber-400 mt-1" id="overviewPendingCount">0 Orders</div>
          </div>
          <button type="button" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-200 hover:bg-slate-700 text-xs font-semibold" onclick="switchModule('records'); filterByStatus('Pending');">View →</button>
        </div>
        <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-800 flex items-center justify-between">
          <div>
            <div class="text-xs text-slate-400 font-medium">Currently Washing</div>
            <div class="text-xl font-bold text-sky-400 mt-1" id="overviewProgressCount">0 Orders</div>
          </div>
          <button type="button" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-200 hover:bg-slate-700 text-xs font-semibold" onclick="switchModule('records'); filterByStatus('In Progress');">View →</button>
        </div>
        <div class="p-4 rounded-xl bg-slate-800/40 border border-slate-800 flex items-center justify-between">
          <div>
            <div class="text-xs text-slate-400 font-medium">Ready for Pickup</div>
            <div class="text-xl font-bold text-teal-400 mt-1" id="overviewReadyCount">0 Orders</div>
          </div>
          <button type="button" class="px-3 py-1.5 rounded-lg bg-slate-800 text-slate-200 hover:bg-slate-700 text-xs font-semibold" onclick="switchModule('records'); filterByStatus('Ready for Pickup');">View →</button>
        </div>
      </div>

      <!-- Recent Orders Snapshot -->
      <div class="data-table-container">
        <div class="table-section-title flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-lg">⏱️</span>
            <div>
              <div class="font-bold text-sm text-slate-100">Recent Laundry Activity</div>
              <div class="text-[11px] text-slate-400 font-normal">Latest 5 orders recorded in the workshop</div>
            </div>
          </div>
          <button type="button" class="px-3.5 py-1.5 rounded-lg bg-teal-600/20 text-teal-300 border border-teal-500/40 hover:bg-teal-600 hover:text-white text-xs font-semibold transition-all cursor-pointer" onclick="switchModule('records')">
            Open Full Laundry Records Module →
          </button>
        </div>
        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Order Code</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Weight / Cost</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="adminOverviewRecentTableBody">
              <tr><td colspan="6" style="text-align:center; color: var(--text-muted);">Loading recent activity...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- MODULE 2: LAUNDRY RECORDS MODULE            -->
    <!-- ========================================== -->
    <div id="module-records" class="dashboard-module-view" style="display: none;">
      
      <!-- Top Backtrack History Bar with Dropdown, Separated Custom Range & Top Right New Order CTA -->
      <div class="view-header flex flex-wrap items-center justify-between gap-3 p-3.5 mb-4 rounded-2xl bg-slate-900/90 border border-slate-800">
        <!-- LEFT: Dropdown List Option for Backtrack History & Separated Custom Range Beside It -->
        <div class="flex items-center gap-3 flex-wrap">
          <!-- Preset Dropdown -->
          <div class="flex items-center gap-2">
            <select id="datePresetSelect" class="px-3 py-1.5 rounded-xl bg-slate-800 border border-slate-700 text-slate-100 text-xs font-semibold focus:outline-none focus:border-teal-500 cursor-pointer shadow-sm" onchange="setDatePreset(this.value)">
              <option value="all" selected>📅 All Time</option>
              <option value="today">📅 Today</option>
              <option value="yesterday">📅 Yesterday</option>
              <option value="last7">📅 Last 7 Days</option>
              <option value="last30">📅 Last 30 Days</option>
              <option value="thisMonth">📅 This Month</option>
            </select>
          </div>

          <!-- Separated Custom Range Beside the Dropdown -->
          <div class="flex items-center gap-1.5 bg-slate-800/60 px-2.5 py-1 rounded-xl border border-slate-700/70 text-xs">
            <span class="text-slate-400 font-medium">Custom:</span>
            <input type="date" id="dateFilterFrom" class="px-2 py-0.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-teal-500" onchange="applyCustomDateFilter()">
            <span class="text-slate-500">to</span>
            <input type="date" id="dateFilterTo" class="px-2 py-0.5 rounded-lg bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-teal-500" onchange="applyCustomDateFilter()">
            <button type="button" class="px-2 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-slate-200 text-xs transition-colors cursor-pointer border border-slate-700" onclick="resetDateFilter()" title="Reset to All Time">↺</button>
          </div>
        </div>

        <!-- RIGHT: Anchored Top Right New Order Button -->
        <div class="ml-auto flex items-center gap-2">
          <button type="button" class="btn-export-csv" onclick="exportOrdersToCSV()" title="Download Excel-compatible CSV sales report">
            <span>📥</span> Export CSV Report
          </button>
          <button class="btn-create-order" onclick="window.openNewOrderModal()">
            <span>🧺</span> + New Order
          </button>
        </div>
      </div>

      <div class="data-table-container" id="orderManagement">
        <!-- Status Filter Tabs Bar -->
        <div class="status-tab-bar">
          <button class="status-tab active" data-status="All" onclick="filterByStatus('All')">All (<span id="countAll">0</span>)</button>
          <button class="status-tab" data-status="Pending" onclick="filterByStatus('Pending')">Pending (<span id="countPending">0</span>)</button>
          <button class="status-tab" data-status="In Progress" onclick="filterByStatus('In Progress')">In Progress (<span id="countInProgress">0</span>)</button>
          <button class="status-tab" data-status="Ready for Pickup" onclick="filterByStatus('Ready for Pickup')">Ready for Pickup (<span id="countReady">0</span>)</button>
          <button class="status-tab" data-status="Completed" onclick="filterByStatus('Completed')">Completed (<span id="countCompleted">0</span>)</button>
          <button class="status-tab" data-status="Cancelled" onclick="filterByStatus('Cancelled')">Cancelled (<span id="countCancelled">0</span>)</button>
        </div>

        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Order Code & Date</th>
                <th>Customer</th>
                <th>Services & Instruction</th>
                <th>Weight & Cost</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="adminOrdersTableBody">
              <tr>
                <td colspan="6" style="text-align:center; color: var(--text-muted);">Loading laundry records...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- MODULE 3: SALES ANALYTICS MODULE            -->
    <!-- ========================================== -->
    <div id="module-analytics" class="dashboard-module-view" style="display: none;">
      <div class="view-header">
        <div>
          <h1 class="view-title">📈 Sales & Financial Analytics</h1>
          <p class="view-subtitle">Detailed revenue breakdown, payment collections, and laundry sales distribution.</p>
        </div>
      </div>

      <div class="metrics-row">
        <div class="metric-card">
          <div class="metric-label">Gross Revenue</div>
          <div class="metric-value text-teal-400" id="analyticsGrossRevenue">₱0.00</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Paid Collections</div>
          <div class="metric-value text-emerald-400" id="analyticsPaidCollections">₱0.00</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Unpaid Receivables</div>
          <div class="metric-value text-amber-400" id="analyticsUnpaidReceivables">₱0.00</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Avg Ticket Size</div>
          <div class="metric-value" id="analyticsAvgTicket">₱0.00</div>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
          <h3 class="font-bold text-slate-100 text-sm mb-3">Service Distribution</h3>
          <div class="flex flex-col gap-2.5 text-xs" id="analyticsServiceBreakdown">
            <div class="flex items-center justify-between text-slate-300">
              <span>🧺 Wash & Fold</span>
              <span class="font-semibold text-slate-100" id="statWashFold">0 orders</span>
            </div>
            <div class="flex items-center justify-between text-slate-300">
              <span>👗 Dry Cleaning</span>
              <span class="font-semibold text-slate-100" id="statDryClean">0 orders</span>
            </div>
            <div class="flex items-center justify-between text-slate-300">
              <span>👔 Steam Press</span>
              <span class="font-semibold text-slate-100" id="statSteamPress">0 orders</span>
            </div>
          </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
          <h3 class="font-bold text-slate-100 text-sm mb-3">Payment Method Breakdown</h3>
          <div class="flex flex-col gap-2.5 text-xs">
            <div class="flex items-center justify-between text-slate-300">
              <span>💵 Cash on Hand</span>
              <span class="font-semibold text-slate-100" id="statCash">₱0.00</span>
            </div>
            <div class="flex items-center justify-between text-slate-300">
              <span>📱 GCash / E-Wallet</span>
              <span class="font-semibold text-slate-100" id="statGcash">₱0.00</span>
            </div>
            <div class="flex items-center justify-between text-slate-300">
              <span>🏦 Bank Transfer</span>
              <span class="font-semibold text-slate-100" id="statBank">₱0.00</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- MODULE 4: STAFF MANAGEMENT MODULE           -->
    <!-- ========================================== -->
    <div id="module-staff" class="dashboard-module-view" style="display: none;">
      <div class="view-header">
        <div>
          <h1 class="view-title">👔 Staff Management</h1>
          <p class="view-subtitle">Station employee directory, staff credentials, and shift operator records.</p>
        </div>
      </div>

      <div class="data-table-container">
        <div class="table-section-title flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-lg">👔</span>
            <span class="font-bold text-slate-100 text-sm">Station Staff Directory</span>
          </div>
          <div class="flex items-center gap-2">
            <input type="text" id="staffSearchInput" placeholder="🔍 Search staff name or phone..." class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-teal-500 w-64" oninput="renderStaffTable()">
          </div>
        </div>

        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Staff Name</th>
                <th>Login Identifier</th>
                <th>Phone / Contact</th>
                <th>Role</th>
                <th>Date Registered</th>
              </tr>
            </thead>
            <tbody id="adminStaffTableBody">
              <tr>
                <td colspan="5" style="text-align:center; padding: 24px; color: var(--text-muted);">Loading staff accounts...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- MODULE 5: CUSTOMER DIRECTORY MODULE         -->
    <!-- ========================================== -->
    <div id="module-customers" class="dashboard-module-view" style="display: none;">
      <div class="view-header">
        <div>
          <h1 class="view-title">👥 Customer Accounts Directory</h1>
          <p class="view-subtitle">Registered client profiles, contact directory, address records & laundry history.</p>
        </div>
        <button class="btn-create-order" onclick="openAddCustomerModal()">
          <span>👤</span> + Add Customer
        </button>
      </div>

      <div class="data-table-container">
        <div class="table-section-title flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-lg">👥</span>
            <span class="font-bold text-slate-100 text-sm">Client Profiles Directory</span>
          </div>
          <div class="flex items-center gap-2">
            <input type="text" id="customerSearchInput" placeholder="🔍 Search client name, phone or address..." class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-teal-500 w-72" oninput="renderCustomersTable()">
          </div>
        </div>

        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Customer Name</th>
                <th>Contact / Phone</th>
                <th>Residence / Delivery Address</th>
                <th>Total Orders</th>
                <th>Date Registered</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="adminCustomersTableBody">
              <tr>
                <td colspan="6" style="text-align:center; padding: 24px; color: var(--text-muted);">Loading customer directory...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- MODULE 5: SUPPLIES & INVENTORY MODULE       -->
    <!-- ========================================== -->
    <div id="module-inventory" class="dashboard-module-view" style="display: none;">
      <!-- Inventory KPI Row -->
      <div class="metrics-row mb-6">
        <div class="metric-card">
          <div class="metric-label">Tracked Supply Items</div>
          <div class="metric-value text-slate-100" id="invTotalItemsCount">0 Items</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Low Stock Alerts</div>
          <div class="metric-value text-amber-400" id="invLowStockCount">0 Items</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Critical / Out of Stock</div>
          <div class="metric-value text-red-400" id="invCriticalCount">0 Items</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Total Inventory Value</div>
          <div class="metric-value text-teal-400" id="invTotalValue">₱0.00</div>
        </div>
      </div>

      <!-- Inventory Table Container -->
      <div class="data-table-container">
        <div class="table-section-title flex items-center justify-between">
          <div class="flex items-center gap-2">
            <span class="text-lg">📦</span>
            <div>
              <div class="font-bold text-slate-100 text-sm">Store Supply & Consumables Ledger</div>
              <div class="text-[11px] text-slate-400">Current on-hand quantities, reorder thresholds & quick restock</div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <input type="text" id="inventorySearchInput" placeholder="🔍 Search supply name or category..." class="px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-teal-500 w-60" oninput="renderInventoryTable()">
            <button type="button" class="btn-export-csv py-2 px-3 text-xs" onclick="loadInventoryTable()" title="Refresh Stock Data">
              <span>🔄</span>
            </button>
            <button class="btn-create-order py-2 px-3.5 text-xs whitespace-nowrap" onclick="openAddSupplyModal()" title="Register New Supply Item">
              <span>🧴</span> + Add Supply Item
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Item Name</th>
                <th>Category</th>
                <th>Current Stock</th>
                <th>Unit</th>
                <th>Reorder Alert</th>
                <th>Unit Cost</th>
                <th>Status</th>
                <th>Quick Stock Action</th>
              </tr>
            </thead>
            <tbody id="adminInventoryTableBody">
              <tr>
                <td colspan="8" style="text-align:center; padding: 24px; color: var(--text-muted);">Loading inventory records...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ========================================== -->
    <!-- MODULE 6: SYSTEM & STORE SETTINGS MODULE    -->
    <!-- ========================================== -->
    <div id="module-settings" class="dashboard-module-view" style="display: none;">
      <div class="view-header flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
          <h1 class="view-title">System & Store Settings</h1>
          <p class="view-subtitle">Configure store business profile, service rate card, thermal receipt & messaging defaults.</p>
        </div>
        <div class="flex items-center gap-2.5">
          <button type="button" class="btn-export-csv" onclick="loadSettings()">
            <span>↺</span> Discard Changes
          </button>
          <button type="button" class="btn-create-order" onclick="saveSettings()">
            <span>💾</span> Save All Settings
          </button>
        </div>
      </div>

      <!-- Settings 2-Column Grid Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- CARD 1: Store Business Details -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-xl flex flex-col gap-4">
          <div class="flex items-center gap-2.5 border-b border-slate-800 pb-3">
            <span class="text-xl">🏪</span>
            <div>
              <h3 class="font-heading font-bold text-sm text-slate-100 m-0">Store & Branch Profile</h3>
              <p class="text-[11px] text-slate-400 m-0">Store branding displayed on receipts and notifications</p>
            </div>
          </div>

          <div class="flex flex-col gap-3 text-xs">
            <div>
              <label class="block text-slate-300 font-semibold mb-1">Store / Laundromat Name</label>
              <input type="text" id="settingStoreName" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 text-xs focus:outline-none focus:border-teal-500 font-medium" placeholder="e.g. LaundryEase Hub">
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-slate-300 font-semibold mb-1">Official Hotline / WhatsApp</label>
                <input type="text" id="settingStorePhone" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 text-xs focus:outline-none focus:border-teal-500" placeholder="+63 917 123 4567">
              </div>
              <div>
                <label class="block text-slate-300 font-semibold mb-1">Operating Hours</label>
                <input type="text" id="settingOperatingHours" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 text-xs focus:outline-none focus:border-teal-500" placeholder="Mon - Sun: 7am - 8pm">
              </div>
            </div>

            <div>
              <label class="block text-slate-300 font-semibold mb-1">Store / Branch Location Address</label>
              <textarea id="settingStoreAddress" rows="2" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 text-xs focus:outline-none focus:border-teal-500 resize-none" placeholder="123 Coastal Ave, Suite 101, Metro Manila"></textarea>
            </div>
          </div>
        </div>

        <!-- CARD 2: Service Rates & Pricing Card -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-xl flex flex-col gap-4">
          <div class="flex items-center gap-2.5 border-b border-slate-800 pb-3">
            <span class="text-xl">🏷️</span>
            <div>
              <h3 class="font-heading font-bold text-sm text-slate-100 m-0">Service Pricing & Rates (₱ PHP)</h3>
              <p class="text-[11px] text-slate-400 m-0">Base unit pricing and surcharge parameters for POS orders</p>
            </div>
          </div>

          <div class="flex flex-col gap-3 text-xs">
            <div class="grid grid-cols-3 gap-3">
              <div>
                <label class="block text-slate-300 font-semibold mb-1">🧺 Wash & Fold (₱/kg)</label>
                <input type="number" step="0.5" id="settingRateWashFold" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-teal-300 font-bold text-xs focus:outline-none focus:border-teal-500" value="25.00">
              </div>
              <div>
                <label class="block text-slate-300 font-semibold mb-1">👗 Dry Clean (₱/pc)</label>
                <input type="number" step="0.5" id="settingRateDryClean" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-teal-300 font-bold text-xs focus:outline-none focus:border-teal-500" value="150.00">
              </div>
              <div>
                <label class="block text-slate-300 font-semibold mb-1">👔 Steam Press (₱/pc)</label>
                <input type="number" step="0.5" id="settingRateSteamPress" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-teal-300 font-bold text-xs focus:outline-none focus:border-teal-500" value="80.00">
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
              <div>
                <label class="block text-slate-300 font-semibold mb-1">🎓 Student Flat Rate (₱/6kg)</label>
                <input type="number" step="1" id="settingRateStudent" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-amber-300 font-bold text-xs focus:outline-none focus:border-teal-500" value="120.00">
              </div>
              <div>
                <label class="block text-slate-300 font-semibold mb-1">⚡ Express Rush Surcharge (₱)</label>
                <input type="number" step="1" id="settingFeeExpressRush" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-rose-300 font-bold text-xs focus:outline-none focus:border-teal-500" value="150.00">
              </div>
            </div>
          </div>
        </div>

        <!-- CARD 3: Thermal Receipt Customization -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-xl flex flex-col gap-4">
          <div class="flex items-center gap-2.5 border-b border-slate-800 pb-3">
            <span class="text-xl">🧾</span>
            <div>
              <h3 class="font-heading font-bold text-sm text-slate-100 m-0">80mm Thermal Receipt Layout</h3>
              <p class="text-[11px] text-slate-400 m-0">Header, footer and disclaimer text printed on customer claim stubs</p>
            </div>
          </div>

          <div class="flex flex-col gap-3 text-xs">
            <div>
              <label class="block text-slate-300 font-semibold mb-1">Receipt Top Greeting / Tagline</label>
              <input type="text" id="settingReceiptHeader" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 text-xs focus:outline-none focus:border-teal-500" placeholder="Thank you for choosing LaundryEase!">
            </div>

            <div>
              <label class="block text-slate-300 font-semibold mb-1">Claim Policy & Disclaimer Notice</label>
              <textarea id="settingReceiptFooter" rows="2" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 text-xs focus:outline-none focus:border-teal-500 resize-none" placeholder="Please present this claim stub upon pickup. Unclaimed laundry after 30 days will be donated."></textarea>
            </div>
          </div>
        </div>

        <!-- CARD 4: Account Security & Profile Settings -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-xl flex flex-col gap-4">
          <div class="flex items-center gap-2.5 border-b border-slate-800 pb-3">
            <span class="text-xl">🔐</span>
            <div>
              <h3 class="font-heading font-bold text-sm text-slate-100 m-0">Admin Security & Profile</h3>
              <p class="text-[11px] text-slate-400 m-0">Manage administrator account credentials and password security</p>
            </div>
          </div>

          <div class="flex flex-col gap-3 text-xs">
            <div class="p-3.5 rounded-xl bg-slate-800/60 border border-slate-700/60 flex items-center justify-between">
              <div>
                <div class="font-bold text-slate-200 text-xs">Admin Profile & Contact Details</div>
                <div class="text-[11px] text-slate-400">Update system administrator name, phone and address</div>
              </div>
              <button type="button" onclick="UserProfileManager.openProfileModal()" class="px-3 py-1.5 rounded-lg bg-teal-500/10 hover:bg-teal-500 text-teal-300 hover:text-white border border-teal-500/30 text-xs font-semibold transition-all cursor-pointer">
                ✏️ Edit Profile
              </button>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-800/60 border border-slate-700/60 flex items-center justify-between">
              <div>
                <div class="font-bold text-slate-200 text-xs">Account Password & Security</div>
                <div class="text-[11px] text-slate-400">Change your login password with secure bcrypt hashing</div>
              </div>
              <button type="button" onclick="UserProfileManager.openPasswordModal()" class="px-3 py-1.5 rounded-lg bg-sky-500/10 hover:bg-sky-500 text-sky-300 hover:text-white border border-sky-500/30 text-xs font-semibold transition-all cursor-pointer">
                🔑 Change Password
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- MODAL: Add Supply Item Modal -->
  <div class="modal-overlay" id="add-supply-modal">
    <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 w-[92%] max-w-[480px] overflow-hidden transform scale-95 transition-all duration-200 flex flex-col">
      <div class="px-5 py-3.5 border-b border-slate-800 flex items-center justify-between bg-slate-800/40">
        <div class="flex items-center gap-2">
          <span class="text-xl">🧴</span>
          <div>
            <h3 class="font-heading font-bold text-sm text-slate-100 m-0">Add New Supply Item</h3>
            <div class="text-[11px] text-teal-400">Add detergents, packaging or chemical stock</div>
          </div>
        </div>
        <button class="modal-close w-7 h-7 rounded-full bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-lg transition-colors cursor-pointer border-none" type="button" onclick="closeAddSupplyModal()">&times;</button>
      </div>

      <form id="add-supply-form" onsubmit="saveSupplyItem(event)" class="p-4 flex flex-col gap-3 m-0">
        <div>
          <label class="text-xs font-semibold text-slate-300 block mb-1">Item / Brand Name *</label>
          <input type="text" id="supplyItemName" name="item_name" class="w-full px-3 py-2 rounded-lg border border-slate-700 bg-slate-800 text-xs text-slate-100 focus:outline-none focus:border-teal-500" placeholder="e.g. Liquid Detergent (Lavender)" required>
        </div>
        <div class="grid grid-cols-2 gap-2.5">
          <div>
            <label class="text-xs font-semibold text-slate-300 block mb-1">Category</label>
            <select id="supplyCategory" name="category" class="w-full px-2.5 py-2 rounded-lg border border-slate-700 bg-slate-800 text-xs text-slate-100 focus:outline-none focus:border-teal-500">
              <option value="Detergent">Detergent</option>
              <option value="Softener">Softener</option>
              <option value="Bleach">Bleach</option>
              <option value="Packaging">Packaging</option>
              <option value="Chemicals">Chemicals</option>
              <option value="Other">Other</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold text-slate-300 block mb-1">Unit of Measurement</label>
            <input type="text" id="supplyUnit" name="unit" class="w-full px-3 py-2 rounded-lg border border-slate-700 bg-slate-800 text-xs text-slate-100 focus:outline-none focus:border-teal-500" placeholder="Liters / Pcs / Bottles" value="Liters" required>
          </div>
        </div>
        <div class="grid grid-cols-3 gap-2.5">
          <div>
            <label class="text-xs font-semibold text-slate-300 block mb-1">Current Stock</label>
            <input type="number" step="0.1" id="supplyCurrentStock" name="current_stock" class="w-full px-2.5 py-2 rounded-lg border border-slate-700 bg-slate-800 text-xs text-slate-100 focus:outline-none focus:border-teal-500" value="10" required>
          </div>
          <div>
            <label class="text-xs font-semibold text-slate-300 block mb-1">Reorder Level</label>
            <input type="number" step="0.1" id="supplyReorderLevel" name="reorder_level" class="w-full px-2.5 py-2 rounded-lg border border-slate-700 bg-slate-800 text-xs text-slate-100 focus:outline-none focus:border-teal-500" value="5" required>
          </div>
          <div>
            <label class="text-xs font-semibold text-slate-300 block mb-1">Cost / Unit (₱)</label>
            <input type="number" step="0.01" id="supplyCostPerUnit" name="cost_per_unit" class="w-full px-2.5 py-2 rounded-lg border border-slate-700 bg-slate-800 text-xs text-slate-100 focus:outline-none focus:border-teal-500" value="50.00" required>
          </div>
        </div>
        <div class="pt-2 flex justify-end gap-2 border-t border-slate-800 mt-2">
          <button type="button" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-colors cursor-pointer" onclick="closeAddSupplyModal()">Cancel</button>
          <button type="submit" class="btn-create-order" id="saveSupplySubmitBtn">Save Item</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: Modern Clean 2-Column Laundry POS "New Order" Modal -->
  <div class="modal-overlay" id="new-order-modal">
    <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 w-[95%] max-w-[780px] overflow-hidden transform scale-95 transition-all duration-200 flex flex-col">
      
      <!-- Modal Header -->
      <div class="px-5 py-3 border-b border-slate-800 flex items-center justify-between bg-slate-800/40">
        <div class="flex items-center gap-2.5">
          <span class="text-xl">🧺</span>
          <div>
            <h3 class="font-heading font-bold text-base text-slate-100 m-0 leading-tight">New Order</h3>
            <div class="text-[11px] text-teal-400 font-medium">Quick intake & instant claim stub</div>
          </div>
        </div>
        <button class="modal-close w-7 h-7 rounded-full bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-lg transition-colors cursor-pointer border-none" type="button" title="Close dialog" onclick="window.closeNewOrderModal()">&times;</button>
      </div>

      <form id="pos-order-form" class="m-0 flex flex-col">
        <!-- 2-Column Body Layout -->
        <div class="p-4 grid grid-cols-1 md:grid-cols-12 gap-4 items-stretch">
          
          <!-- LEFT COLUMN: ORDER DETAILS -->
          <div class="md:col-span-7 flex flex-col gap-2.5 pr-0 md:pr-4 md:border-r md:border-slate-800">
            
            <div class="text-[11px] font-bold tracking-wider uppercase text-teal-400">ORDER DETAILS</div>

            <!-- 1. Customer Selection -->
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="text-xs font-semibold text-slate-300">Customer</label>
                <div class="inline-flex bg-slate-800 p-0.5 rounded-lg text-[11px]">
                  <button type="button" class="px-2 py-0.5 rounded-md font-semibold text-teal-400 bg-slate-700 shadow-sm transition-all" id="cust-tab-existing">Existing</button>
                  <button type="button" class="px-2 py-0.5 rounded-md font-medium text-slate-400 hover:text-slate-200 transition-all" id="cust-tab-new">+ New</button>
                </div>
              </div>

              <div id="cust-view-existing">
                <select class="w-full px-3 py-1.5 h-8 rounded-lg border border-slate-700 bg-slate-800 text-slate-100 text-xs focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" id="pos-customer-select">
                  <option value="">Select Existing Customer...</option>
                </select>
              </div>

              <div id="cust-view-new" class="grid grid-cols-2 gap-2" style="display:none;">
                <input type="text" class="w-full px-2.5 py-1.5 h-8 rounded-lg border border-slate-700 bg-slate-800 text-xs focus:outline-none focus:border-teal-500 text-slate-100" id="pos-new-cust-name" placeholder="Full Name" />
                <input type="text" class="w-full px-2.5 py-1.5 h-8 rounded-lg border border-slate-700 bg-slate-800 text-xs focus:outline-none focus:border-teal-500 text-slate-100" id="pos-new-cust-phone" placeholder="Phone (09XX)" />
              </div>
            </div>

            <!-- 2. Service Selection -->
            <div>
              <label class="text-xs font-semibold text-slate-300 block mb-1">Service</label>
              <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5" id="modern-service-chips-container">
              </div>
              <input type="hidden" id="pos-service-select" value="wash_fold" />
            </div>

            <!-- 3. Laundry Load & Weight Stepper -->
            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="text-xs font-semibold text-slate-300">Laundry Load</label>
                <span class="text-[10px] text-slate-400">Min: 8.0 kg standard</span>
              </div>
              
              <div class="flex items-center gap-1.5">
                <button type="button" class="w-8 h-8 rounded-lg border border-slate-700 bg-slate-800 hover:bg-teal-600 text-slate-100 font-bold text-sm flex items-center justify-center transition-colors cursor-pointer" id="weight-minus-btn">−</button>
                <div class="relative flex-1">
                  <input type="number" step="0.1" min="0.5" class="w-full h-8 px-2 text-center font-bold text-sm rounded-lg border border-slate-700 bg-slate-800 text-slate-100 focus:outline-none focus:border-teal-500" id="pos-weight-input" value="8.0" oninput="window.updatePOSCalculations()" required />
                  <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[11px] font-semibold text-slate-400 pointer-events-none">kg</span>
                </div>
                <button type="button" class="w-8 h-8 rounded-lg border border-slate-700 bg-slate-800 hover:bg-teal-600 text-slate-100 font-bold text-sm flex items-center justify-center transition-colors cursor-pointer" id="weight-plus-btn">+</button>
              </div>

              <div class="flex items-center gap-1.5 mt-1">
                <span class="text-[10px] font-semibold text-slate-400">Quick:</span>
                <div class="flex gap-1 flex-wrap">
                  <button type="button" class="px-2 py-0.5 rounded-md bg-slate-800 hover:bg-teal-900/50 hover:text-teal-300 text-[10px] font-semibold text-slate-300 transition-colors cursor-pointer" onclick="window.setPOSWeight(6)">6kg</button>
                  <button type="button" class="px-2 py-0.5 rounded-md bg-slate-800 hover:bg-teal-900/50 hover:text-teal-300 text-[10px] font-semibold text-slate-300 transition-colors cursor-pointer" onclick="window.setPOSWeight(8)">8kg</button>
                  <button type="button" class="px-2 py-0.5 rounded-md bg-slate-800 hover:bg-teal-900/50 hover:text-teal-300 text-[10px] font-semibold text-slate-300 transition-colors cursor-pointer" onclick="window.setPOSWeight(10)">10kg</button>
                  <button type="button" class="px-2 py-0.5 rounded-md bg-slate-800 hover:bg-teal-900/50 hover:text-teal-300 text-[10px] font-semibold text-slate-300 transition-colors cursor-pointer" onclick="window.setPOSWeight(12)">12kg</button>
                  <button type="button" class="px-2 py-0.5 rounded-md bg-slate-800 hover:bg-teal-900/50 hover:text-teal-300 text-[10px] font-semibold text-slate-300 transition-colors cursor-pointer" onclick="window.setPOSWeight(15)">15kg</button>
                </div>
              </div>
            </div>

            <!-- 4. Additional Options -->
            <div class="grid grid-cols-2 gap-2">
              <div class="flex items-center justify-between p-2 rounded-lg border border-slate-700 bg-slate-800 hover:border-teal-500 cursor-pointer transition-all select-none" id="student-toggle-card">
                <div>
                  <div class="text-[11px] font-bold text-slate-200">🎓 Student Rate</div>
                  <div class="text-[10px] text-slate-400">₱120 flat / 6kg</div>
                </div>
                <div class="switch-indicator"></div>
                <input type="checkbox" id="pos-student-checkbox" style="display:none;" onchange="window.updatePOSCalculations()" />
              </div>

              <div class="flex items-center justify-between p-2 rounded-lg border border-slate-700 bg-slate-800 hover:border-teal-500 cursor-pointer transition-all select-none" id="rush-toggle-card">
                <div>
                  <div class="text-[11px] font-bold text-slate-200">⚡ Express Rush</div>
                  <div class="text-[10px] text-slate-400">+₱150 same-day</div>
                </div>
                <div class="switch-indicator"></div>
                <input type="checkbox" id="pos-rush-checkbox" style="display:none;" onchange="window.updatePOSCalculations()" />
              </div>
            </div>

            <!-- 5. Payment Details -->
            <div>
              <label class="text-xs font-semibold text-slate-300 block mb-1">Payment</label>
              <div class="grid grid-cols-2 gap-2">
                <select class="w-full px-2.5 py-1.5 h-8 rounded-lg border border-slate-700 bg-slate-800 text-slate-100 text-xs focus:outline-none focus:border-teal-500" id="pos-payment-status">
                  <option value="Paid">Paid in Full</option>
                  <option value="Unpaid" selected>Pay upon Pickup</option>
                </select>
                <select class="w-full px-2.5 py-1.5 h-8 rounded-lg border border-slate-700 bg-slate-800 text-slate-100 text-xs focus:outline-none focus:border-teal-500" id="pos-payment-method">
                  <option value="Cash">Cash</option>
                  <option value="GCash">GCash</option>
                  <option value="Card">Card</option>
                </select>
              </div>
            </div>

            <!-- 6. Special Notes -->
            <div>
              <input type="text" class="w-full px-2.5 py-1.5 h-8 rounded-lg border border-slate-700 bg-slate-800 text-xs focus:outline-none focus:border-teal-500 text-slate-100 placeholder:text-slate-500" id="pos-notes-input" placeholder="Special instructions / care notes (optional)..." />
            </div>

          </div>

          <!-- RIGHT COLUMN: ORDER SUMMARY -->
          <div class="md:col-span-5 flex flex-col justify-between bg-slate-800/50 rounded-xl p-3.5 border border-slate-700/60">
            
            <div class="flex flex-col gap-2.5">
              <div class="text-[11px] font-bold tracking-wider uppercase text-teal-400 border-b border-slate-700 pb-1.5">ORDER SUMMARY</div>

              <div class="bg-slate-800 p-2.5 rounded-lg border border-slate-700 shadow-sm">
                <div class="font-bold text-sm text-slate-100 font-heading flex items-center justify-between" id="summary-service-title">
                  <span>Wash & Fold</span>
                  <span class="text-xs font-semibold text-teal-400 bg-teal-950/60 px-2 py-0.5 rounded-full border border-teal-800" id="summary-unit-rate">₱25/kg</span>
                </div>
                <div class="text-xs text-slate-400 mt-0.5" id="summary-weight-display">8.0 kg (standard min)</div>
              </div>

              <div class="flex flex-col gap-1.5 text-xs text-slate-300 px-1">
                <div class="flex justify-between items-center" id="sum-row-base">
                  <span>Base Wash & Fold</span>
                  <span class="font-semibold text-slate-100" id="sum-val-base">₱200.00</span>
                </div>

                <div class="flex justify-between items-center text-teal-400 font-medium" id="sum-row-student" style="display:none;">
                  <span>🎓 Student Discount Rate</span>
                  <span id="sum-val-student">-₱20.00</span>
                </div>

                <div class="flex justify-between items-center text-amber-400 font-medium" id="sum-row-rush" style="display:none;">
                  <span>⚡ Express Rush Service</span>
                  <span id="sum-val-rush">+₱150.00</span>
                </div>
              </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-700">
              <div class="flex items-baseline justify-between mb-1">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">TOTAL</span>
                <span class="text-2xl font-extrabold font-heading text-teal-400" id="sum-grand-total">₱200.00</span>
              </div>
              <div class="text-[10px] text-slate-400 text-right" id="sum-service-name">Wash & Fold • 8.0 kg</div>
            </div>

          </div>

        </div>

        <!-- Fixed Modal Footer -->
        <div class="px-5 py-2.5 bg-slate-800/80 border-t border-slate-800 flex items-center justify-end gap-2.5">
          <button type="button" class="px-3.5 py-1.5 rounded-lg border border-slate-700 bg-slate-800 text-slate-300 hover:bg-slate-700 text-xs font-semibold transition-all modal-cancel cursor-pointer" onclick="window.closeNewOrderModal()">Cancel</button>
          <button type="submit" class="px-4 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-500 text-white text-xs font-bold shadow-md hover:shadow-lg transition-all cursor-pointer flex items-center gap-1.5">
            🧾 Create Order
          </button>
        </div>

      </form>
    </div>
  </div>


  <!-- MODAL: Comprehensive Order Details & Status Manager -->
  <div class="modal-overlay" id="order-details-modal" style="display:none;">
    <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 w-[95%] max-w-[620px] overflow-hidden transform scale-95 transition-all duration-200 flex flex-col">
      
      <!-- Modal Header -->
      <div class="px-5 py-3.5 border-b border-slate-800 flex items-center justify-between bg-slate-800/50">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center text-xl">🧺</div>
          <div>
            <h3 class="font-heading font-bold text-lg text-slate-100 m-0 leading-tight" id="modalOrderCode">Order #ORD-00125</h3>
            <div class="text-xs text-teal-400 font-medium" id="modalOrderDateReceived">Date Received: --</div>
          </div>
        </div>
        <button class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-lg transition-colors cursor-pointer border-none" type="button" title="Close modal" onclick="closeOrderDetailsModal()">&times;</button>
      </div>

      <!-- Modal Body -->
      <div class="p-5 flex flex-col gap-3.5 text-xs">
        
        <!-- Grid: Customer Info & Expected Completion -->
        <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-slate-800/40 border border-slate-800">
          <div>
            <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block mb-0.5">Customer</span>
            <div class="text-sm font-bold text-slate-100" id="modalCustomerName">Juan Dela Cruz</div>
            <div class="text-slate-400 text-xs mt-0.5" id="modalCustomerPhone">📱 09123456789</div>
          </div>
          <div>
            <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block mb-0.5">Expected Completion</span>
            <div class="text-sm font-bold text-teal-300" id="modalExpectedCompletion">Tomorrow, 02:30 PM</div>
            <div class="text-[11px] text-slate-400 mt-0.5">Standard 24-hr turnaround</div>
          </div>
        </div>

        <!-- Breakdown: Laundry Services, Weight, Service Rate, Subtotal, Discount, Total Amount -->
        <div class="p-3.5 rounded-xl bg-slate-800/30 border border-slate-800 flex flex-col gap-2">
          <div class="flex items-center justify-between text-slate-300">
            <span class="text-slate-400">Laundry Services:</span>
            <span class="font-semibold text-slate-100 text-right" id="modalServices">Wash & Fold</span>
          </div>
          <div class="flex items-center justify-between text-slate-300">
            <span class="text-slate-400">Weight:</span>
            <span class="font-semibold text-slate-200" id="modalWeight">6.5 kg</span>
          </div>
          <div class="flex items-center justify-between text-slate-300">
            <span class="text-slate-400">Service Rate:</span>
            <span class="font-semibold text-slate-200" id="modalServiceRate">₱25.00 / kg</span>
          </div>
          <div class="border-t border-slate-800/80 my-1"></div>
          <div class="flex items-center justify-between text-slate-300">
            <span>Subtotal:</span>
            <span class="font-semibold text-slate-200" id="modalSubtotal">₱162.50</span>
          </div>
          <div class="flex items-center justify-between text-amber-400" id="modalDiscountRow">
            <span>Discount:</span>
            <span class="font-semibold" id="modalDiscount">-₱20.00</span>
          </div>
          <div class="flex items-center justify-between text-sm font-bold text-teal-300 pt-1.5 border-t border-slate-800">
            <span>Total Amount:</span>
            <span class="text-base font-bold text-teal-400" id="modalTotalAmount">₱142.50</span>
          </div>
        </div>

        <!-- Status Management Controls (Payment Status & Order Status) -->
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1">Payment Status</label>
            <select id="modalPaymentStatusSelect" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-semibold text-xs focus:outline-none focus:border-teal-500">
              <option value="Unpaid">Unpaid</option>
              <option value="Paid">Paid</option>
            </select>
          </div>
          <div>
            <label class="block text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1">Order Status</label>
            <select id="modalOrderStatusSelect" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-semibold text-xs focus:outline-none focus:border-teal-500">
              <option value="Pending">Pending</option>
              <option value="In Progress">In Progress</option>
              <option value="Ready for Pickup">Ready for Pickup</option>
              <option value="Completed">Completed</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>
        </div>

        <!-- Notes -->
        <div>
          <label class="block text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-1">Notes / Instructions</label>
          <textarea id="modalOrderNotes" rows="2" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-teal-500 resize-none" placeholder="Add special instructions or customer notes..."></textarea>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="px-5 py-3 border-t border-slate-800 flex items-center justify-end gap-2 bg-slate-800/40">
        <button type="button" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-colors cursor-pointer" onclick="closeOrderDetailsModal()">Close</button>
        <button type="button" class="px-4 py-2 rounded-lg bg-gradient-to-r from-teal-600 to-sky-600 hover:from-teal-500 hover:to-sky-500 text-white font-bold text-xs transition-all shadow-md cursor-pointer" onclick="saveOrderDetailsChanges()">Save Status Updates</button>
      </div>
    </div>
  </div>

  <!-- MODAL: Full Edit Order Modal -->
  <div class="modal-overlay" id="edit-order-modal" style="display:none;">
    <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 w-[95%] max-w-[660px] overflow-hidden transform scale-95 transition-all duration-200 flex flex-col">
      
      <!-- Modal Header -->
      <div class="px-5 py-3.5 border-b border-slate-800 flex items-center justify-between bg-slate-800/50">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center text-xl">✏️</div>
          <div>
            <h3 class="font-heading font-bold text-lg text-slate-100 m-0 leading-tight" id="editModalOrderCode">Edit Order #ORD-00000</h3>
            <div class="text-xs text-teal-400 font-medium" id="editModalCustomerInfo">Client: --</div>
          </div>
        </div>
        <button class="w-8 h-8 rounded-full bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-lg transition-colors cursor-pointer border-none" type="button" title="Close modal" onclick="closeEditOrderModal()">&times;</button>
      </div>

      <form id="edit-order-form" onsubmit="event.preventDefault(); saveEditOrderChanges();" class="m-0 flex flex-col">
        <div class="p-5 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">
          
          <!-- Services & Weight Row -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-1">Primary Laundry Service</label>
              <select id="editOrderServiceSelect" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-semibold text-xs focus:outline-none focus:border-teal-500" onchange="calculateEditOrderTotal()">
                <option value="Wash & Fold" data-rate="25.00" data-unit="/ kg">🧺 Wash & Fold (₱25.00/kg)</option>
                <option value="Dry Cleaning" data-rate="150.00" data-unit="/ pc">👗 Dry Cleaning (₱150.00/pc)</option>
                <option value="Steam Press" data-rate="80.00" data-unit="/ pc">👔 Steam Press (₱80.00/pc)</option>
              </select>
            </div>
            <div>
              <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-1">Weight / Quantity</label>
              <div class="flex items-center gap-2">
                <input type="number" id="editOrderWeightInput" step="0.1" min="0.5" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-semibold text-xs focus:outline-none focus:border-teal-500" oninput="calculateEditOrderTotal()">
                <span class="text-xs text-slate-400 font-medium" id="editOrderUnitLabel">kg</span>
              </div>
            </div>
          </div>

          <!-- Modifiers & Rates -->
          <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800 flex items-center justify-between gap-4 flex-wrap text-xs">
            <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
              <input type="checkbox" id="editOrderStudentRate" class="accent-teal-500 rounded" onchange="calculateEditOrderTotal()">
              <span>🎓 Student Rate (10% Off)</span>
            </label>
            <label class="flex items-center gap-2 text-slate-300 cursor-pointer">
              <input type="checkbox" id="editOrderExpressRush" class="accent-teal-500 rounded" onchange="calculateEditOrderTotal()">
              <span>⚡ Express Rush (+₱50.00)</span>
            </label>
          </div>

          <!-- Pricing Breakdown & Calculation Summary -->
          <div class="p-3.5 rounded-xl bg-slate-800/30 border border-slate-800 flex flex-col gap-1.5 text-xs">
            <div class="flex items-center justify-between text-slate-400">
              <span>Service Rate:</span>
              <span class="font-semibold text-slate-200" id="editOrderRateDisplay">₱25.00 / kg</span>
            </div>
            <div class="flex items-center justify-between text-slate-400">
              <span>Subtotal:</span>
              <span class="font-semibold text-slate-200" id="editOrderSubtotalDisplay">₱0.00</span>
            </div>
            <div class="flex items-center justify-between text-amber-400" id="editOrderDiscountRow" style="display:none;">
              <span>Discount:</span>
              <span class="font-semibold" id="editOrderDiscountDisplay">-₱0.00</span>
            </div>
            <div class="flex items-center justify-between text-sky-400" id="editOrderRushRow" style="display:none;">
              <span>Express Surcharge:</span>
              <span class="font-semibold">+₱50.00</span>
            </div>
            <div class="flex items-center justify-between text-sm font-bold text-teal-300 pt-1.5 border-t border-slate-800">
              <span>Total Price:</span>
              <span class="text-base font-bold text-teal-400" id="editOrderTotalDisplay">₱0.00</span>
            </div>
          </div>

          <!-- Status Controls -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-1">Payment Status</label>
              <select id="editOrderPaymentStatusSelect" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-semibold text-xs focus:outline-none focus:border-teal-500">
                <option value="Unpaid">Unpaid</option>
                <option value="Paid">Paid</option>
              </select>
            </div>
            <div>
              <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-1">Order Status</label>
              <select id="editOrderStatusSelect" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-semibold text-xs focus:outline-none focus:border-teal-500">
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Ready for Pickup">Ready for Pickup</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
              </select>
            </div>
          </div>

          <!-- Instructions / Notes -->
          <div>
            <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-1">Special Instructions & Notes</label>
            <textarea id="editOrderSpecialNotes" rows="2" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-200 text-xs focus:outline-none focus:border-teal-500 resize-none" placeholder="Special fabric care notes, detergent preference..."></textarea>
          </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3 border-t border-slate-800 flex items-center justify-end gap-2.5 bg-slate-800/40">
          <button type="button" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-colors cursor-pointer" onclick="closeEditOrderModal()">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded-lg bg-gradient-to-r from-sky-600 to-teal-600 hover:from-sky-500 hover:to-teal-500 text-white font-bold text-xs transition-all shadow-md cursor-pointer flex items-center gap-1.5">
            💾 Save Order Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: Add Customer Modal -->
  <div class="modal-overlay" id="add-customer-modal" style="display:none;">
    <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 w-[95%] max-w-[500px] overflow-hidden transform scale-95 transition-all duration-200 flex flex-col">
      <div class="px-5 py-3.5 border-b border-slate-800 flex items-center justify-between bg-slate-800/50">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center text-lg">👤</div>
          <div>
            <h3 class="font-heading font-bold text-base text-slate-100 m-0 leading-tight">Register New Customer</h3>
            <div class="text-[11px] text-teal-400 font-medium">Create client account for order tagging</div>
          </div>
        </div>
        <button class="w-7 h-7 rounded-full bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-lg transition-colors cursor-pointer border-none" type="button" onclick="closeAddCustomerModal()">&times;</button>
      </div>

      <form id="add-customer-form" onsubmit="event.preventDefault(); submitAddCustomer();" class="m-0 p-5 flex flex-col gap-3.5 text-xs">
        <div>
          <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-1">Full Name <span class="text-rose-400">*</span></label>
          <input type="text" id="addCustFullName" required class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-medium text-xs focus:outline-none focus:border-teal-500" placeholder="e.g. Maria Santos">
        </div>
        <div>
          <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-1">Mobile Phone Number <span class="text-rose-400">*</span></label>
          <input type="text" id="addCustPhone" required class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-medium text-xs focus:outline-none focus:border-teal-500" placeholder="e.g. 09171234567">
        </div>
        <div>
          <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-1">Residence / Delivery Address</label>
          <textarea id="addCustAddress" rows="2" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-medium text-xs focus:outline-none focus:border-teal-500 resize-none" placeholder="Unit / Street / City address..."></textarea>
        </div>

        <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-800">
          <button type="button" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-colors cursor-pointer" onclick="closeAddCustomerModal()">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-bold text-xs shadow-md transition-all cursor-pointer flex items-center gap-1.5">
            <span>👤</span> Create Customer Account
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: Edit Customer Modal -->
  <div class="modal-overlay" id="edit-customer-modal" style="display:none;">
    <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 w-[95%] max-w-[500px] overflow-hidden transform scale-95 transition-all duration-200 flex flex-col">
      <div class="px-5 py-3.5 border-b border-slate-800 flex items-center justify-between bg-slate-800/50">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center text-lg">✏️</div>
          <div>
            <h3 class="font-heading font-bold text-base text-slate-100 m-0 leading-tight">Edit Client Profile</h3>
            <div class="text-[11px] text-teal-400 font-medium">Update contact info & delivery address</div>
          </div>
        </div>
        <button class="w-7 h-7 rounded-full bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-lg transition-colors cursor-pointer border-none" type="button" onclick="closeEditCustomerModal()">&times;</button>
      </div>

      <form id="edit-customer-form" onsubmit="event.preventDefault(); submitEditCustomer();" class="m-0 p-5 flex flex-col gap-3.5 text-xs">
        <input type="hidden" id="editCustId">
        <div>
          <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-1">Full Name <span class="text-rose-400">*</span></label>
          <input type="text" id="editCustFullName" required class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-medium text-xs focus:outline-none focus:border-teal-500">
        </div>
        <div>
          <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-1">Mobile Phone Number <span class="text-rose-400">*</span></label>
          <input type="text" id="editCustPhone" required class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-medium text-xs focus:outline-none focus:border-teal-500">
        </div>
        <div>
          <label class="block text-[11px] uppercase tracking-wider font-bold text-slate-400 mb-1">Residence / Delivery Address</label>
          <textarea id="editCustAddress" rows="2" class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-slate-100 font-medium text-xs focus:outline-none focus:border-teal-500 resize-none"></textarea>
        </div>

        <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-800">
          <button type="button" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-colors cursor-pointer" onclick="closeEditCustomerModal()">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-bold text-xs shadow-md transition-all cursor-pointer flex items-center gap-1.5">
            💾 Save Profile Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: Customer Order History Modal -->
  <div class="modal-overlay" id="customer-history-modal" style="display:none;">
    <div class="bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 w-[95%] max-w-[720px] overflow-hidden transform scale-95 transition-all duration-200 flex flex-col">
      <div class="px-5 py-3.5 border-b border-slate-800 flex items-center justify-between bg-slate-800/50">
        <div class="flex items-center gap-2.5">
          <div class="w-9 h-9 rounded-xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center text-lg">📜</div>
          <div>
            <h3 class="font-heading font-bold text-base text-slate-100 m-0 leading-tight" id="custHistoryModalName">Client Laundry History</h3>
            <div class="text-[11px] text-teal-400 font-medium" id="custHistoryModalSub">Lifetime order history and receipts</div>
          </div>
        </div>
        <button class="w-7 h-7 rounded-full bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-lg transition-colors cursor-pointer border-none" type="button" onclick="closeCustomerHistoryModal()">&times;</button>
      </div>

      <div class="p-4 max-h-[60vh] overflow-y-auto">
        <div id="custHistorySummary" class="grid grid-cols-3 gap-2.5 mb-3.5 p-3 rounded-xl bg-slate-800/40 border border-slate-800 text-xs">
          <div>
            <div class="text-[10px] uppercase font-bold text-slate-400">Total Visits</div>
            <div class="text-base font-bold text-slate-100" id="histTotalOrders">0</div>
          </div>
          <div>
            <div class="text-[10px] uppercase font-bold text-slate-400">Total Spent</div>
            <div class="text-base font-bold text-teal-300" id="histTotalSpent">₱0.00</div>
          </div>
          <div>
            <div class="text-[10px] uppercase font-bold text-slate-400">Unpaid Balance</div>
            <div class="text-base font-bold text-amber-400" id="histUnpaidBalance">₱0.00</div>
          </div>
        </div>

        <table class="w-full text-left text-xs border-collapse">
          <thead>
            <tr class="border-b border-slate-800 text-teal-400 font-bold uppercase text-[10px]">
              <th class="py-2 px-2.5">Order Code</th>
              <th class="py-2 px-2.5">Service</th>
              <th class="py-2 px-2.5">Weight / Price</th>
              <th class="py-2 px-2.5">Status</th>
              <th class="py-2 px-2.5">Action</th>
            </tr>
          </thead>
          <tbody id="custHistoryTableBody" class="divide-y divide-slate-800/60 text-slate-300">
            <tr><td colspan="5" class="py-4 text-center text-slate-400">Loading order records...</td></tr>
          </tbody>
        </table>
      </div>

      <div class="px-5 py-3 border-t border-slate-800 flex items-center justify-between bg-slate-800/40">
        <button type="button" class="btn-create-order" id="custHistoryCreateOrderBtn">
          <span>➕</span> + New Order for Client
        </button>
        <button type="button" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-colors cursor-pointer" onclick="closeCustomerHistoryModal()">Close</button>
      </div>
    </div>
  </div>

  <script>
    let allOrdersData = [];
    let currentFilterStatus = 'All';
    let currentActiveOrder = null;

    document.addEventListener('DOMContentLoaded', () => {
      initSidebarCollapse();
      fetchAdminData();
      initPOSModalEvents();
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

    const fetchAdminDashboard = fetchAdminData;

    function toggleManageAccountMenu(e) {
      if (e) e.preventDefault();
      const submenu = document.getElementById('manageAccSubmenu');
      const chevron = document.getElementById('manageAccChevron');
      if (submenu.style.display === 'none' || !submenu.style.display) {
        submenu.style.display = 'flex';
        if (chevron) chevron.classList.add('open');
      } else {
        submenu.style.display = 'none';
        if (chevron) chevron.classList.remove('open');
      }
    }

    async function openManageUsersModal(role) {
      const modal = document.getElementById('manage-users-modal');
      if (!modal) return;
      modal.classList.add('active');
      
      const title = document.getElementById('manageUsersTitle');
      const sub = document.getElementById('manageUsersSub');
      const icon = document.getElementById('manageUsersIcon');
      
      if (role === 'staff') {
        title.innerText = 'Staff Accounts';
        sub.innerText = 'System staff operators & laundry handlers';
        icon.innerText = '👔';
      } else {
        title.innerText = 'Customer Accounts';
        sub.innerText = 'Registered client profile records';
        icon.innerText = '👥';
      }

      const token = localStorage.getItem('authToken');
      const tbody = document.getElementById('manageUsersTableBody');
      tbody.innerHTML = '<tr><td colspan="4" class="py-4 text-center text-slate-400">Loading accounts...</td></tr>';

      try {
        const res = await fetch(`api/get_users_by_role.php?role=${role}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!res.ok) {
          tbody.innerHTML = '<tr><td colspan="4" class="py-4 text-center text-red-500">Failed to load users.</td></tr>';
          return;
        }
        const data = await res.json();
        if (!data.users || data.users.length === 0) {
          tbody.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-slate-400">No ${role} accounts found.</td></tr>`;
          return;
        }

        tbody.innerHTML = data.users.map(u => `
          <tr class="hover:bg-slate-800/50">
            <td class="py-2.5 px-3 font-semibold text-slate-100">${u.full_name}</td>
            <td class="py-2.5 px-3"><code class="text-teal-400 font-mono text-[11px]">${u.identifier}</code><br><span class="text-slate-400 text-[11px]">📱 ${u.phone}</span></td>
            <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase ${u.role === 'staff' ? 'bg-amber-900/50 text-amber-300 border border-amber-700' : 'bg-teal-900/50 text-teal-300 border border-teal-700'}">${u.role}</span></td>
            <td class="py-2.5 px-3 text-slate-400">${u.address || 'N/A'}</td>
          </tr>
        `).join('');

      } catch (err) {
        console.error(err);
        tbody.innerHTML = '<tr><td colspan="4" class="py-4 text-center text-red-500">Server error fetching accounts.</td></tr>';
      }
    }

    function closeManageUsersModal() {
      const modal = document.getElementById('manage-users-modal');
      if (modal) modal.classList.remove('active');
    }

    const servicesList = [
      { id: 'wash_fold', name: 'Wash & Fold', rate: 25, unit: '/kg', icon: '🧺' },
      { id: 'dry_clean', name: 'Dry Cleaning', rate: 150, unit: '/pc', icon: '👗' },
      { id: 'steam_press', name: 'Steam Press', rate: 80, unit: '/pc', icon: '👔' }
    ];

    let selectedService = servicesList[0];
    let custType = 'existing';

    window.openNewOrderModal = function() {
      document.getElementById('new-order-modal').classList.add('active');
      loadPOSCustomers();
      renderServiceChips();
      window.updatePOSCalculations();
    };

    window.closeNewOrderModal = function() {
      document.getElementById('new-order-modal').classList.remove('active');
    };

    function renderServiceChips() {
      const container = document.getElementById('modern-service-chips-container');
      if (!container) return;
      container.innerHTML = servicesList.map(s => `
        <div class="service-chip p-2 rounded-lg border ${selectedService.id === s.id ? 'border-teal-500 ring-1 ring-teal-500 bg-teal-950/40' : 'border-slate-700 bg-slate-800 hover:border-slate-600'} cursor-pointer transition-all" onclick="window.selectPOSService('${s.id}')">
          <div class="flex items-center gap-1.5 font-bold text-xs text-slate-100">
            <span>${s.icon}</span> <span>${s.name}</span>
          </div>
          <div class="text-[11px] font-semibold text-teal-400 mt-0.5">₱${s.rate}${s.unit}</div>
        </div>
      `).join('');
    }

    window.selectPOSService = function(serviceId) {
      const found = servicesList.find(s => s.id === serviceId);
      if (found) {
        selectedService = found;
        document.getElementById('pos-service-select').value = serviceId;
        renderServiceChips();
        window.updatePOSCalculations();
      }
    };

    function initPOSModalEvents() {
      const tabExisting = document.getElementById('cust-tab-existing');
      const tabNew = document.getElementById('cust-tab-new');
      if (tabExisting && tabNew) {
        tabExisting.addEventListener('click', () => {
          custType = 'existing';
          tabExisting.className = 'px-2 py-0.5 rounded-md font-semibold text-teal-400 bg-slate-700 shadow-sm transition-all';
          tabNew.className = 'px-2 py-0.5 rounded-md font-medium text-slate-400 hover:text-slate-200 transition-all';
          document.getElementById('cust-view-existing').style.display = 'block';
          document.getElementById('cust-view-new').style.display = 'none';
        });

        tabNew.addEventListener('click', () => {
          custType = 'new';
          tabNew.className = 'px-2 py-0.5 rounded-md font-semibold text-teal-400 bg-slate-700 shadow-sm transition-all';
          tabExisting.className = 'px-2 py-0.5 rounded-md font-medium text-slate-400 hover:text-slate-200 transition-all';
          document.getElementById('cust-view-existing').style.display = 'none';
          document.getElementById('cust-view-new').style.display = 'grid';
        });
      }

      const studentCard = document.getElementById('student-toggle-card');
      if (studentCard) {
        studentCard.addEventListener('click', () => {
          const chk = document.getElementById('pos-student-checkbox');
          chk.checked = !chk.checked;
          studentCard.classList.toggle('toggle-active', chk.checked);
          studentCard.classList.toggle('border-teal-500', chk.checked);
          window.updatePOSCalculations();
        });
      }

      const rushCard = document.getElementById('rush-toggle-card');
      if (rushCard) {
        rushCard.addEventListener('click', () => {
          const chk = document.getElementById('pos-rush-checkbox');
          chk.checked = !chk.checked;
          rushCard.classList.toggle('toggle-active', chk.checked);
          rushCard.classList.toggle('border-teal-500', chk.checked);
          window.updatePOSCalculations();
        });
      }

      const btnMinus = document.getElementById('weight-minus-btn');
      const btnPlus = document.getElementById('weight-plus-btn');
      if (btnMinus && btnPlus) {
        btnMinus.addEventListener('click', () => {
          const input = document.getElementById('pos-weight-input');
          let val = parseFloat(input.value) || 8.0;
          val = Math.max(0.5, val - 0.5);
          input.value = val.toFixed(1);
          window.updatePOSCalculations();
        });
        btnPlus.addEventListener('click', () => {
          const input = document.getElementById('pos-weight-input');
          let val = parseFloat(input.value) || 8.0;
          val = val + 0.5;
          input.value = val.toFixed(1);
          window.updatePOSCalculations();
        });
      }

      const form = document.getElementById('pos-order-form');
      if (form) {
        form.addEventListener('submit', async (e) => {
          e.preventDefault();
          const token = localStorage.getItem('authToken');
          const weightKg = parseFloat(document.getElementById('pos-weight-input').value) || 8.0;
          const isStudent = document.getElementById('pos-student-checkbox').checked;
          const isRush = document.getElementById('pos-rush-checkbox').checked;
          const paymentStatus = document.getElementById('pos-payment-status').value;
          const paymentMethod = document.getElementById('pos-payment-method').value;
          const notes = document.getElementById('pos-notes-input').value;
          const totalStr = document.getElementById('sum-grand-total').innerText.replace('₱', '');

          let payload = {
            customerType: custType,
            service: selectedService.name,
            weightKg: weightKg,
            studentRate: isStudent,
            expressRush: isRush,
            paymentStatus: paymentStatus,
            paymentMethod: paymentMethod,
            specialInstructions: notes,
            totalPrice: parseFloat(totalStr)
          };

          if (custType === 'existing') {
            const userId = document.getElementById('pos-customer-select').value;
            if (!userId) { alert('Please select an existing customer.'); return; }
            payload.userId = userId;
          } else {
            const fullName = document.getElementById('pos-new-cust-name').value.trim();
            const phone = document.getElementById('pos-new-cust-phone').value.trim();
            if (!fullName || !phone) { alert('Please enter customer full name and phone number.'); return; }
            payload.newCustomer = { fullName, phone };
          }

          try {
            const res = await fetch('api/create_order.php', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
              },
              body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!res.ok) { alert(data.message || 'Error creating order.'); return; }
            window.closeNewOrderModal();
            fetchAdminData();
            ReceiptPrinter.openReceipt({
              order_code: data.order_code,
              full_name: (custType === 'existing') ? (document.getElementById('pos-customer-select').selectedOptions[0]?.text || 'Customer') : payload.newCustomer.fullName,
              phone: (custType === 'existing') ? '' : payload.newCustomer.phone,
              services_registered: payload.service + (payload.expressRush ? ', Express Rush' : '') + (payload.studentRate ? ', Student Rate' : ''),
              weight_kg: payload.weightKg,
              total_price: payload.totalPrice,
              payment_status: payload.paymentStatus,
              order_status: 'Pending'
            });
          } catch (err) {
            alert('Server error creating order.');
            console.error(err);
          }
        });
      }
    }

    window.setPOSWeight = function(w) {
      const input = document.getElementById('pos-weight-input');
      if (input) {
        input.value = parseFloat(w).toFixed(1);
        window.updatePOSCalculations();
      }
    };

    window.updatePOSCalculations = function() {
      const weight = parseFloat(document.getElementById('pos-weight-input').value) || 0;
      const isStudent = document.getElementById('pos-student-checkbox').checked;
      const isRush = document.getElementById('pos-rush-checkbox').checked;

      let baseTotal = weight * selectedService.rate;
      let studentDiscount = 0;
      let rushFee = 0;

      if (isStudent) {
        studentDiscount = Math.max(0, baseTotal - 120);
        baseTotal = 120;
        document.getElementById('sum-row-student').style.display = 'flex';
        document.getElementById('sum-val-student').innerText = `-₱${studentDiscount.toFixed(2)}`;
      } else {
        document.getElementById('sum-row-student').style.display = 'none';
      }

      if (isRush) {
        rushFee = 150;
        document.getElementById('sum-row-rush').style.display = 'flex';
        document.getElementById('sum-val-rush').innerText = `+₱150.00`;
      } else {
        document.getElementById('sum-row-rush').style.display = 'none';
      }

      const grandTotal = baseTotal + rushFee;

      document.getElementById('summary-service-title').innerHTML = `<span>${selectedService.name}</span><span class="text-xs font-semibold text-teal-400 bg-teal-950/60 px-2 py-0.5 rounded-full border border-teal-800">₱${selectedService.rate}${selectedService.unit}</span>`;
      document.getElementById('summary-weight-display').innerText = `${weight.toFixed(1)} kg (${selectedService.unit === '/kg' ? 'standard min' : 'per piece'})`;
      document.getElementById('sum-val-base').innerText = `₱${(weight * selectedService.rate).toFixed(2)}`;
      document.getElementById('sum-grand-total').innerText = `₱${grandTotal.toFixed(2)}`;
      document.getElementById('sum-service-name').innerText = `${selectedService.name} • ${weight.toFixed(1)} kg`;
    };

    async function loadPOSCustomers() {
      const token = localStorage.getItem('authToken');
      try {
        const res = await fetch('api/get_customers.php', {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!res.ok) return;
        const data = await res.json();
        const sel = document.getElementById('pos-customer-select');
        if (sel) {
          sel.innerHTML = '<option value="">Select Existing Customer...</option>';
          data.customers.forEach(c => {
            sel.innerHTML += `<option value="${c.id}">${c.full_name} (${c.phone})</option>`;
          });
        }
      } catch (err) { console.error(err); }
    }

    function normalizeOrderStatus(status) {
      if (!status) return 'Pending';
      const s = status.trim().toLowerCase();
      if (s === 'pending' || s === 'received') return 'Pending';
      if (s === 'in progress' || s === 'washing' || s === 'processing' || s === 'dry cleaning') return 'In Progress';
      if (s === 'ready for pickup' || s === 'ready') return 'Ready for Pickup';
      if (s === 'completed' || s === 'picked up' || s === 'done') return 'Completed';
      if (s === 'cancelled' || s === 'canceled') return 'Cancelled';
      return status;
    }

    function getStatusBadgeHtml(status) {
      const norm = normalizeOrderStatus(status);
      switch (norm) {
        case 'Pending':
          return `<span class="badge badge-pending">⏳ Pending</span>`;
        case 'In Progress':
          return `<span class="badge badge-progress">🌀 In Progress</span>`;
        case 'Ready for Pickup':
          return `<span class="badge badge-ready">📦 Ready for Pickup</span>`;
        case 'Completed':
          return `<span class="badge badge-completed">✅ Completed</span>`;
        case 'Cancelled':
          return `<span class="badge badge-cancelled">❌ Cancelled</span>`;
        default:
          return `<span class="badge badge-processing">${status}</span>`;
      }
    }

    function toggleLaundryRecordsMenu(e) {
      if (e) e.preventDefault();
      const sub = document.getElementById('laundryRecSubmenu');
      const chev = document.getElementById('laundryRecChevron');
      if (sub) {
        const isHidden = sub.style.display === 'none' || !sub.style.display;
        sub.style.display = isHidden ? 'flex' : 'none';
        if (chev) chev.classList.toggle('open', isHidden);
      }
    }

    function switchModule(moduleName, navElement = null) {
      document.querySelectorAll('.nav-group .nav-item').forEach(el => el.classList.remove('is-active'));
      document.querySelectorAll('.nav-submenu .submenu-item').forEach(el => el.classList.remove('active-sub'));

      if (navElement) {
        navElement.classList.add('is-active');
        if (navElement.classList.contains('submenu-item')) {
          navElement.classList.add('active-sub');
        }
      } else {
        const target = document.querySelector(`.nav-group .nav-item[data-module="${moduleName}"], .nav-submenu .submenu-item[data-module="${moduleName}"]`);
        if (target) {
          target.classList.add('is-active');
          if (target.classList.contains('submenu-item')) {
            target.classList.add('active-sub');
          }
        }
      }

      document.querySelectorAll('.dashboard-module-view').forEach(v => {
        v.style.display = 'none';
      });

      const active = document.getElementById(`module-${moduleName}`);
      if (active) active.style.display = 'block';

      if (moduleName === 'records') {
        filterByStatus(currentFilterStatus || 'All');
      } else if (moduleName === 'staff') {
        loadStaffTable();
      } else if (moduleName === 'customers') {
        loadCustomersTable();
      }
    }

    let currentDateFilter = {
      preset: 'all',
      from: null,
      to: null
    };

    function onDatePresetSelectChange(preset) {
      const customBox = document.getElementById('customDateRangeBox');
      if (preset === 'custom') {
        if (customBox) customBox.style.display = 'flex';
        return;
      } else {
        if (customBox) customBox.style.display = 'none';
      }

      setDatePreset(preset);
    }

    function setDatePreset(preset) {
      currentDateFilter.preset = preset;
      currentDateFilter.from = null;
      currentDateFilter.to = null;

      const select = document.getElementById('datePresetSelect');
      if (select && select.value !== preset) {
        select.value = preset;
      }

      const customBox = document.getElementById('customDateRangeBox');
      if (customBox) {
        customBox.style.display = (preset === 'custom') ? 'flex' : 'none';
      }

      const fromInput = document.getElementById('dateFilterFrom');
      const toInput = document.getElementById('dateFilterTo');
      if (fromInput) fromInput.value = '';
      if (toInput) toInput.value = '';

      const now = new Date();
      const pad = (n) => String(n).padStart(2, '0');
      const todayStr = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;

      if (preset === 'today') {
        currentDateFilter.from = todayStr;
        currentDateFilter.to = todayStr;
      } else if (preset === 'yesterday') {
        const yest = new Date(now);
        yest.setDate(now.getDate() - 1);
        const yestStr = `${yest.getFullYear()}-${pad(yest.getMonth() + 1)}-${pad(yest.getDate())}`;
        currentDateFilter.from = yestStr;
        currentDateFilter.to = yestStr;
      } else if (preset === 'last7') {
        const l7 = new Date(now);
        l7.setDate(now.getDate() - 6);
        const l7Str = `${l7.getFullYear()}-${pad(l7.getMonth() + 1)}-${pad(l7.getDate())}`;
        currentDateFilter.from = l7Str;
        currentDateFilter.to = todayStr;
      } else if (preset === 'last30') {
        const l30 = new Date(now);
        l30.setDate(now.getDate() - 29);
        const l30Str = `${l30.getFullYear()}-${pad(l30.getMonth() + 1)}-${pad(l30.getDate())}`;
        currentDateFilter.from = l30Str;
        currentDateFilter.to = todayStr;
      } else if (preset === 'thisMonth') {
        const firstDayStr = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-01`;
        currentDateFilter.from = firstDayStr;
        currentDateFilter.to = todayStr;
      }

      updateTabCounts();
      renderOrdersTable();
    }

    function applyCustomDateFilter() {
      const fromVal = document.getElementById('dateFilterFrom').value;
      const toVal = document.getElementById('dateFilterTo').value;

      if (!fromVal && !toVal) return;

      currentDateFilter.preset = 'custom';
      currentDateFilter.from = fromVal || null;
      currentDateFilter.to = toVal || null;

      const select = document.getElementById('datePresetSelect');
      if (select) select.value = 'custom';

      updateTabCounts();
      renderOrdersTable();
    }

    function resetDateFilter() {
      const select = document.getElementById('datePresetSelect');
      if (select) select.value = 'all';
      setDatePreset('all');
    }

    function matchesDateFilter(order) {
      if (!currentDateFilter.from && !currentDateFilter.to) return true;
      if (!order.dropped_off_at) return true;

      const orderDateStr = order.dropped_off_at.split(' ')[0] || order.dropped_off_at.split('T')[0];

      if (currentDateFilter.from && orderDateStr < currentDateFilter.from) return false;
      if (currentDateFilter.to && orderDateStr > currentDateFilter.to) return false;

      return true;
    }

    function filterByStatus(status, element = null) {
      currentFilterStatus = status;

      // Update status tabs UI
      document.querySelectorAll('.status-tab').forEach(tab => {
        if (tab.getAttribute('data-status') === status) {
          tab.classList.add('active');
        } else {
          tab.classList.remove('active');
        }
      });

      renderOrdersTable();
    }

    function updateTabCounts() {
      let countAll = 0;
      let countPending = 0;
      let countInProgress = 0;
      let countReady = 0;
      let countCompleted = 0;
      let countCancelled = 0;

      allOrdersData.forEach(o => {
        if (matchesDateFilter(o)) {
          countAll++;
          const norm = normalizeOrderStatus(o.order_status);
          if (norm === 'Pending') countPending++;
          else if (norm === 'In Progress') countInProgress++;
          else if (norm === 'Ready for Pickup') countReady++;
          else if (norm === 'Completed') countCompleted++;
          else if (norm === 'Cancelled') countCancelled++;
        }
      });

      if (document.getElementById('countAll')) document.getElementById('countAll').innerText = countAll;
      if (document.getElementById('countPending')) document.getElementById('countPending').innerText = countPending;
      if (document.getElementById('countInProgress')) document.getElementById('countInProgress').innerText = countInProgress;
      if (document.getElementById('countReady')) document.getElementById('countReady').innerText = countReady;
      if (document.getElementById('countCompleted')) document.getElementById('countCompleted').innerText = countCompleted;
      if (document.getElementById('countCancelled')) document.getElementById('countCancelled').innerText = countCancelled;

      if (document.getElementById('overviewPendingCount')) document.getElementById('overviewPendingCount').innerText = `${countPending} Orders`;
      if (document.getElementById('overviewProgressCount')) document.getElementById('overviewProgressCount').innerText = `${countInProgress} Orders`;
      if (document.getElementById('overviewReadyCount')) document.getElementById('overviewReadyCount').innerText = `${countReady} Orders`;
    }

    function renderOrdersTable() {
      // 1. Render Full Laundry Records Module Table
      const tbody = document.getElementById('adminOrdersTableBody');
      if (tbody) {
        tbody.innerHTML = '';

        const filtered = allOrdersData.filter(o => {
          const statusMatch = (currentFilterStatus === 'All') || (normalizeOrderStatus(o.order_status) === currentFilterStatus);
          const dateMatch = matchesDateFilter(o);
          return statusMatch && dateMatch;
        });

        if (filtered.length === 0) {
          tbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 24px; color:var(--text-muted);">No laundry records found matching status "${currentFilterStatus}" and selected date range.</td></tr>`;
        } else {
          filtered.forEach(order => {
            const dateFormatted = new Date(order.dropped_off_at).toLocaleDateString('en-US', {
              month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
            });

            const statusBadge = getStatusBadgeHtml(order.order_status);
            const paymentBadge = order.payment_status === 'Paid'
              ? `<span class="badge badge-paid">Paid</span>`
              : `<span class="badge badge-unpaid">Unpaid</span>`;

            const row = `
              <tr style="cursor: pointer;" onclick="openOrderDetailsModal(${order.id})">
                <td>
                  <strong style="color:#ffffff;">${order.order_code}</strong>
                  <div class="text-small-muted">${dateFormatted}</div>
                </td>
                <td>
                  <div><strong style="color:#f0fdfa;">${order.customer_name}</strong></div>
                  <div class="text-small-muted">📱 ${order.customer_phone}</div>
                </td>
                <td>
                  <div>${order.services_registered}</div>
                  <div class="text-small-muted">${order.special_instructions || 'No special notes'}</div>
                </td>
                <td>
                  <div>${parseFloat(order.weight_kg).toFixed(1)} kg</div>
                  <div class="text-small-muted">Total: ₱${parseFloat(order.total_price).toFixed(2)}</div>
                </td>
                <td>
                  ${statusBadge}
                  <div style="margin-top: 6px;">${paymentBadge}</div>
                </td>
                <td onclick="event.stopPropagation();">
                  <div class="flex items-center gap-1.5">
                    <button type="button" onclick="ReceiptPrinter.openReceipt(allOrdersData.find(o => o.id == ${order.id}))" title="Print Thermal Receipt / Claim Stub" class="w-8 h-8 rounded-lg bg-emerald-500/10 hover:bg-emerald-500 text-emerald-300 hover:text-white border border-emerald-500/30 hover:border-emerald-500 flex items-center justify-center transition-all cursor-pointer shadow-sm hover:scale-105" aria-label="Print Receipt">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                      </svg>
                    </button>
                    <button type="button" onclick="CustomerNotifier.openNotifyModal(allOrdersData.find(o => o.id == ${order.id}))" title="Notify Customer (WhatsApp / SMS)" class="w-8 h-8 rounded-lg bg-amber-500/10 hover:bg-amber-500 text-amber-300 hover:text-white border border-amber-500/30 hover:border-amber-500 flex items-center justify-center transition-all cursor-pointer shadow-sm hover:scale-105" aria-label="Notify Customer">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                      </svg>
                    </button>
                    <button type="button" onclick="openOrderDetailsModal(${order.id})" title="View Order Summary" class="w-8 h-8 rounded-lg bg-teal-500/10 hover:bg-teal-500 text-teal-300 hover:text-white border border-teal-500/30 hover:border-teal-500 flex items-center justify-center transition-all cursor-pointer shadow-sm hover:scale-105" aria-label="View Order">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </button>
                    <button type="button" onclick="openEditOrderModal(${order.id})" title="Edit Order Details" class="w-8 h-8 rounded-lg bg-sky-500/10 hover:bg-sky-500 text-sky-300 hover:text-white border border-sky-500/30 hover:border-sky-500 flex items-center justify-center transition-all cursor-pointer shadow-sm hover:scale-105" aria-label="Edit Order">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            `;
            tbody.innerHTML += row;
          });
        }
      }

      // 2. Render Recent 5 Orders in Master Overview
      const recentTbody = document.getElementById('adminOverviewRecentTableBody');
      if (recentTbody) {
        const recentOrders = allOrdersData.slice(0, 5);
        if (recentOrders.length === 0) {
          recentTbody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding: 18px; color:var(--text-muted);">No recent laundry records yet.</td></tr>`;
        } else {
          recentTbody.innerHTML = recentOrders.map(order => {
            const statusBadge = getStatusBadgeHtml(order.order_status);
            return `
              <tr style="cursor: pointer;" onclick="openOrderDetailsModal(${order.id})">
                <td><strong style="color:var(--text-main); font-weight:700;">${order.order_code}</strong></td>
                <td><span style="color:var(--text-main); font-weight:600;">${order.customer_name}</span></td>
                <td><span>${order.services_registered}</span></td>
                <td><span>${parseFloat(order.weight_kg).toFixed(1)} kg (₱${parseFloat(order.total_price).toFixed(2)})</span></td>
                <td>${statusBadge}</td>
                <td onclick="event.stopPropagation();">
                  <div class="flex items-center gap-1.5">
                    <button type="button" onclick="openOrderDetailsModal(${order.id})" title="View Order Summary" class="table-action-btn btn-action-view" aria-label="View Order">
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </button>
                    <button type="button" onclick="openEditOrderModal(${order.id})" title="Edit Order Details" class="table-action-btn btn-action-edit" aria-label="Edit Order">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            `;
          }).join('');
        }
      }
    }

    let allStaffData = [];
    let allCustomerUsersData = [];

    async function loadStaffTable() {
      const tbody = document.getElementById('adminStaffTableBody');
      if (!tbody) return;
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 24px; color:var(--text-muted);">Loading staff accounts...</td></tr>';

      try {
        const token = localStorage.getItem('authToken');
        const res = await fetch('api/get_users_by_role.php?role=staff', {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!res.ok) throw new Error("Failed to load staff");
        const data = await res.json();
        allStaffData = data.users || [];

        renderStaffTable();
      } catch (err) {
        console.error(err);
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 24px; color:#f87171;">Failed to load staff accounts.</td></tr>';
      }
    }

    function renderStaffTable() {
      const tbody = document.getElementById('adminStaffTableBody');
      if (!tbody) return;

      const search = (document.getElementById('staffSearchInput')?.value || '').toLowerCase().trim();
      const filtered = allStaffData.filter(u => {
        return !search || (u.full_name && u.full_name.toLowerCase().includes(search)) || (u.phone && u.phone.includes(search)) || (u.identifier && u.identifier.toLowerCase().includes(search));
      });

      if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 24px; color:var(--text-muted);">No staff records found.</td></tr>';
        return;
      }

      tbody.innerHTML = filtered.map(staff => {
        const dateFormatted = staff.created_at ? new Date(staff.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
        const initial = (staff.full_name || 'S').charAt(0).toUpperCase();
        return `
          <tr>
            <td>
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-teal-500/10 border border-teal-500/30 flex items-center justify-center font-bold text-teal-300 text-xs">
                  ${initial}
                </div>
                <div>
                  <strong style="color:#ffffff;">${staff.full_name}</strong>
                  <div class="text-small-muted">Station Staff</div>
                </div>
              </div>
            </td>
            <td>
              <span class="font-mono text-slate-300 text-xs">${staff.identifier || staff.phone || 'staff'}</span>
            </td>
            <td>
              <span class="text-slate-300">📱 ${staff.phone || 'N/A'}</span>
            </td>
            <td>
              <span class="badge badge-progress uppercase text-[10px] font-bold">STAFF</span>
            </td>
            <td>
              <span class="text-slate-400 text-xs">${dateFormatted}</span>
            </td>
          </tr>
        `;
      }).join('');
    }

    async function loadCustomersTable() {
      const tbody = document.getElementById('adminCustomersTableBody');
      if (!tbody) return;
      tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 24px; color:var(--text-muted);">Loading customer directory...</td></tr>';

      try {
        const token = localStorage.getItem('authToken');
        const res = await fetch('api/get_customers.php', {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!res.ok) throw new Error("Failed to load customers");
        const data = await res.json();
        allCustomerUsersData = data.customers || [];

        renderCustomersTable();
      } catch (err) {
        console.error(err);
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding: 24px; color:#f87171;">Failed to load customer directory.</td></tr>';
      }
    }

    function renderCustomersTable() {
      const tbody = document.getElementById('adminCustomersTableBody');
      if (!tbody) return;

      const search = (document.getElementById('customerSearchInput')?.value || '').toLowerCase().trim();
      const filtered = allCustomerUsersData.filter(c => {
        return !search || (c.full_name && c.full_name.toLowerCase().includes(search)) || (c.phone && c.phone.includes(search)) || (c.address && c.address.toLowerCase().includes(search));
      });

      if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding: 24px; color:var(--text-muted);">No customer records found.</td></tr>';
        return;
      }

      tbody.innerHTML = filtered.map(cust => {
        const dateFormatted = cust.created_at ? new Date(cust.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
        const initial = (cust.full_name || 'C').charAt(0).toUpperCase();
        
        // Count orders placed
        const custOrders = allOrdersData.filter(o => (parseInt(o.user_id) === parseInt(cust.id)) || (o.customer_phone === cust.phone) || (o.customer_name === cust.full_name));
        const orderCount = custOrders.length;

        return `
          <tr>
            <td>
              <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-sky-500/10 border border-sky-500/30 flex items-center justify-center font-bold text-sky-300 text-xs">
                  ${initial}
                </div>
                <div>
                  <strong style="color:#ffffff;">${cust.full_name}</strong>
                  <div class="text-small-muted font-mono text-[11px]">${cust.identifier || 'client'}</div>
                </div>
              </div>
            </td>
            <td>
              <span class="text-slate-200">📱 ${cust.phone || 'N/A'}</span>
            </td>
            <td>
              <span class="text-slate-300 text-xs">${cust.address || 'No address on file'}</span>
            </td>
            <td>
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold ${orderCount > 0 ? 'bg-teal-500/20 text-teal-300 border border-teal-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700'}">
                ${orderCount} order${orderCount !== 1 ? 's' : ''}
              </span>
            </td>
            <td>
              <span class="text-slate-400 text-xs">${dateFormatted}</span>
            </td>
            <td>
              <div class="flex items-center gap-1.5">
                <button type="button" onclick="quickNewOrderForCustomer(${cust.id})" title="+ New Order for this Client" class="px-2.5 py-1 rounded-lg bg-teal-500/10 hover:bg-teal-500 text-teal-300 hover:text-white border border-teal-500/30 hover:border-teal-500 text-xs font-semibold flex items-center gap-1 transition-all cursor-pointer shadow-sm hover:scale-105">
                  <span>➕</span> Order
                </button>
                <button type="button" onclick="openCustomerHistoryModal(${cust.id})" title="View Laundry History" class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 flex items-center justify-center transition-all cursor-pointer shadow-sm" aria-label="Customer Order History">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                </button>
                <button type="button" onclick="openEditCustomerModal(${cust.id})" title="Edit Client Profile" class="w-7 h-7 rounded-lg bg-sky-500/10 hover:bg-sky-500 text-sky-300 hover:text-white border border-sky-500/30 hover:border-sky-500 flex items-center justify-center transition-all cursor-pointer shadow-sm" aria-label="Edit Customer">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                  </svg>
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    function openAddCustomerModal() {
      const form = document.getElementById('add-customer-form');
      if (form) form.reset();
      const modal = document.getElementById('add-customer-modal');
      if (!modal) return;
      modal.style.display = 'flex';
      setTimeout(() => {
        modal.classList.add('active', 'is-active');
        const nameInput = document.getElementById('addCustFullName');
        if (nameInput) nameInput.focus();
      }, 10);
    }

    function closeAddCustomerModal() {
      const modal = document.getElementById('add-customer-modal');
      if (!modal) return;
      modal.classList.remove('active', 'is-active');
      setTimeout(() => modal.style.display = 'none', 200);
    }

    async function submitAddCustomer() {
      const fullName = document.getElementById('addCustFullName').value.trim();
      const phone = document.getElementById('addCustPhone').value.trim();
      const address = document.getElementById('addCustAddress').value.trim();

      if (!fullName || !phone) {
        alert("Please enter customer full name and phone number.");
        return;
      }

      try {
        const token = localStorage.getItem('authToken');
        const res = await fetch('api/create_customer.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({ full_name: fullName, phone: phone, address: address })
        });
        const data = await res.json();
        if (res.ok) {
          alert(data.message || "Customer created successfully!");
          closeAddCustomerModal();
          await loadCustomersTable();
          if (typeof fetchAdminData === 'function') fetchAdminData();
          if (typeof initPOSModalEvents === 'function') initPOSModalEvents();
        } else {
          alert(data.message || "Failed to create customer.");
        }
      } catch (err) {
        console.error(err);
        alert("Server communication error while creating customer.");
      }
    }

    window.openAddCustomerModal = openAddCustomerModal;
    window.closeAddCustomerModal = closeAddCustomerModal;
    window.submitAddCustomer = submitAddCustomer;

    function openEditCustomerModal(custId) {
      const cust = allCustomerUsersData.find(c => parseInt(c.id) === parseInt(custId));
      if (!cust) return;

      document.getElementById('editCustId').value = cust.id;
      document.getElementById('editCustFullName').value = cust.full_name || '';
      document.getElementById('editCustPhone').value = cust.phone || '';
      document.getElementById('editCustAddress').value = cust.address || '';

      const modal = document.getElementById('edit-customer-modal');
      modal.style.display = 'flex';
      setTimeout(() => modal.classList.add('is-active'), 10);
    }

    function closeEditCustomerModal() {
      const modal = document.getElementById('edit-customer-modal');
      modal.classList.remove('is-active');
      setTimeout(() => modal.style.display = 'none', 200);
    }

    async function submitEditCustomer() {
      const custId = document.getElementById('editCustId').value;
      const fullName = document.getElementById('editCustFullName').value.trim();
      const phone = document.getElementById('editCustPhone').value.trim();
      const address = document.getElementById('editCustAddress').value.trim();

      if (!fullName || !phone) {
        alert("Full name and phone number are required.");
        return;
      }

      try {
        const token = localStorage.getItem('authToken');
        const res = await fetch('api/update_customer.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({ customer_id: custId, full_name: fullName, phone: phone, address: address })
        });
        const data = await res.json();
        if (res.ok) {
          alert(data.message || "Customer profile updated!");
          closeEditCustomerModal();
          await loadCustomersTable();
        } else {
          alert(data.message || "Failed to update profile.");
        }
      } catch (err) {
        console.error(err);
        alert("Server communication error while updating customer.");
      }
    }

    let activeHistoryCustomer = null;

    async function openCustomerHistoryModal(custId) {
      const cust = allCustomerUsersData.find(c => parseInt(c.id) === parseInt(custId));
      if (!cust) return;
      activeHistoryCustomer = cust;

      document.getElementById('custHistoryModalName').innerText = `${cust.full_name} - Laundry History`;
      document.getElementById('custHistoryModalSub').innerText = `Phone: ${cust.phone || 'N/A'} • Address: ${cust.address || 'N/A'}`;

      const createBtn = document.getElementById('custHistoryCreateOrderBtn');
      if (createBtn) {
        createBtn.onclick = () => {
          closeCustomerHistoryModal();
          quickNewOrderForCustomer(cust.id);
        };
      }

      const tbody = document.getElementById('custHistoryTableBody');
      tbody.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-slate-400">Loading order records...</td></tr>';

      const modal = document.getElementById('customer-history-modal');
      modal.style.display = 'flex';
      setTimeout(() => modal.classList.add('is-active'), 10);

      try {
        const token = localStorage.getItem('authToken');
        const res = await fetch(`api/get_customer_orders.php?user_id=${cust.id}`, {
          headers: { 'Authorization': `Bearer ${token}` }
        });
        if (!res.ok) throw new Error("Failed to load customer orders");
        const data = await res.json();
        const orders = data.orders || [];

        let totalSpent = 0;
        let unpaidTotal = 0;

        orders.forEach(o => {
          const price = parseFloat(o.total_price) || 0;
          totalSpent += price;
          if (o.payment_status !== 'Paid') unpaidTotal += price;
        });

        document.getElementById('histTotalOrders').innerText = orders.length;
        document.getElementById('histTotalSpent').innerText = `₱${totalSpent.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        document.getElementById('histUnpaidBalance').innerText = `₱${unpaidTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;

        if (orders.length === 0) {
          tbody.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-slate-400">No laundry orders placed by this customer yet.</td></tr>';
          return;
        }

        tbody.innerHTML = orders.map(o => {
          const dateFormatted = o.dropped_off_at ? new Date(o.dropped_off_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
          const statusBadge = getStatusBadgeHtml(o.order_status);
          const payBadge = o.payment_status === 'Paid'
            ? `<span class="badge badge-paid">Paid</span>`
            : `<span class="badge badge-unpaid">Unpaid</span>`;

          return `
            <tr>
              <td class="py-2.5 px-2.5">
                <strong style="color:#ffffff;">${o.order_code}</strong>
                <div class="text-small-muted text-[10px]">${dateFormatted}</div>
              </td>
              <td class="py-2.5 px-2.5">
                <span>${o.services_registered || 'Wash & Fold'}</span>
              </td>
              <td class="py-2.5 px-2.5">
                <div>${parseFloat(o.weight_kg).toFixed(1)} kg</div>
                <div class="text-teal-300 font-semibold">₱${parseFloat(o.total_price).toFixed(2)}</div>
              </td>
              <td class="py-2.5 px-2.5">
                ${statusBadge}
                <div class="mt-1">${payBadge}</div>
              </td>
              <td class="py-2.5 px-2.5">
                <button type="button" onclick="closeCustomerHistoryModal(); openOrderDetailsModal(${o.id});" class="px-2 py-1 rounded bg-teal-600/20 text-teal-300 border border-teal-500/40 hover:bg-teal-600 hover:text-white text-[11px] font-semibold transition-all cursor-pointer">
                  View
                </button>
              </td>
            </tr>
          `;
        }).join('');
      } catch (err) {
        console.error(err);
        tbody.innerHTML = '<tr><td colspan="5" class="py-4 text-center text-rose-400">Failed to load customer order records.</td></tr>';
      }
    }

    function closeCustomerHistoryModal() {
      const modal = document.getElementById('customer-history-modal');
      modal.classList.remove('is-active');
      setTimeout(() => modal.style.display = 'none', 200);
      activeHistoryCustomer = null;
    }

    function quickNewOrderForCustomer(custId) {
      window.openNewOrderModal();
      setTimeout(() => {
        const select = document.getElementById('pos-customer-select');
        const existingTab = document.getElementById('cust-tab-existing');
        if (existingTab) existingTab.click();
        if (select) {
          select.value = custId;
          const evt = new Event('change');
          select.dispatchEvent(evt);
        }
      }, 50);
    }

    async function fetchAdminData() {
      const token = localStorage.getItem('authToken');
      if (!token) {
        window.location.href = 'index.html';
        return;
      }

      try {
        const response = await fetch('api/get_all_orders.php', {
          headers: { 'Authorization': `Bearer ${token}` }
        });

        if (!response.ok) {
          localStorage.clear();
          window.location.href = 'index.html';
          return;
        }

        const data = await response.json();
        
        const adminName = data.user.fullName || data.user.full_name || 'Admin Manager';
        if (document.getElementById('displayAdminName')) {
          document.getElementById('displayAdminName').innerText = adminName;
        }
        if (document.getElementById('topUserRoleName')) {
          document.getElementById('topUserRoleName').innerText = adminName;
        }
        if (document.getElementById('displayAdminPhone')) {
          document.getElementById('displayAdminPhone').innerText = `System Administrator`;
        }

        UserProfileManager.init({
          id: data.user.id,
          full_name: adminName,
          phone: data.user.phone || '',
          address: data.user.address || '',
          role: 'admin'
        });

        const totalRevenue = parseFloat(data.metrics.totalRevenue || 0);
        document.getElementById('metricRevenue').innerText = `₱${totalRevenue.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        
        document.getElementById('metricTotalOrders').innerText = data.metrics.totalOrders || 0;
        document.getElementById('metricTotalCustomers').innerText = data.metrics.totalCustomers || 0;

        allOrdersData = data.orders || [];

        let activeLoads = 0;
        let paidTotal = 0;
        let unpaidTotal = 0;
        let washFoldCount = 0;
        let dryCleanCount = 0;
        let steamPressCount = 0;
        let cashTotal = 0;
        let gcashTotal = 0;
        let bankTotal = 0;

        allOrdersData.forEach(order => {
          const norm = normalizeOrderStatus(order.order_status);
          if (norm !== 'Completed' && norm !== 'Cancelled') activeLoads++;

          const price = parseFloat(order.total_price) || 0;
          if (order.payment_status === 'Paid') paidTotal += price;
          else unpaidTotal += price;

          const srv = order.services_registered || '';
          if (srv.includes('Wash & Fold')) washFoldCount++;
          if (srv.includes('Dry Cleaning')) dryCleanCount++;
          if (srv.includes('Steam Press')) steamPressCount++;

          const method = (order.payment_method || '').toLowerCase();
          if (method.includes('cash')) cashTotal += price;
          else if (method.includes('gcash')) gcashTotal += price;
          else if (method.includes('bank')) bankTotal += price;
          else cashTotal += price;
        });

        document.getElementById('metricActiveLoads').innerText = activeLoads;

        // Populate Analytics module metrics
        if (document.getElementById('analyticsGrossRevenue')) document.getElementById('analyticsGrossRevenue').innerText = `₱${totalRevenue.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        if (document.getElementById('analyticsPaidCollections')) document.getElementById('analyticsPaidCollections').innerText = `₱${paidTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        if (document.getElementById('analyticsUnpaidReceivables')) document.getElementById('analyticsUnpaidReceivables').innerText = `₱${unpaidTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        
        const avgTicket = allOrdersData.length > 0 ? (totalRevenue / allOrdersData.length) : 0;
        if (document.getElementById('analyticsAvgTicket')) document.getElementById('analyticsAvgTicket').innerText = `₱${avgTicket.toLocaleString('en-US', {minimumFractionDigits: 2})}`;

        if (document.getElementById('statWashFold')) document.getElementById('statWashFold').innerText = `${washFoldCount} orders`;
        if (document.getElementById('statDryClean')) document.getElementById('statDryClean').innerText = `${dryCleanCount} orders`;
        if (document.getElementById('statSteamPress')) document.getElementById('statSteamPress').innerText = `${steamPressCount} orders`;

        if (document.getElementById('statCash')) document.getElementById('statCash').innerText = `₱${cashTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        if (document.getElementById('statGcash')) document.getElementById('statGcash').innerText = `₱${gcashTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        if (document.getElementById('statBank')) document.getElementById('statBank').innerText = `₱${bankTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;

        updateTabCounts();
        renderOrdersTable();

      } catch (err) {
        console.error("Error fetching admin dashboard:", err);
      }
    }

    function openOrderDetailsModal(orderId) {
      const order = allOrdersData.find(o => parseInt(o.id) === parseInt(orderId));
      if (!order) return;
      currentActiveOrder = order;

      document.getElementById('modalOrderCode').innerText = `Order #${order.order_code}`;
      
      const dateObj = new Date(order.dropped_off_at);
      const dateFormatted = dateObj.toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
      });
      document.getElementById('modalOrderDateReceived').innerText = `Date Received: ${dateFormatted}`;

      const expObj = new Date(dateObj.getTime() + 24 * 60 * 60 * 1000);
      const expFormatted = expObj.toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
      });
      document.getElementById('modalExpectedCompletion').innerText = expFormatted;

      document.getElementById('modalCustomerName').innerText = order.customer_name || 'Customer';
      document.getElementById('modalCustomerPhone').innerText = `📱 ${order.customer_phone || 'N/A'}`;
      document.getElementById('modalServices').innerText = order.services_registered || 'Wash & Fold';

      const weight = parseFloat(order.weight_kg) || 0;
      const totalPrice = parseFloat(order.total_price) || 0;
      
      let rate = 25.00;
      let unit = '/ kg';
      if (order.services_registered && order.services_registered.includes('Dry Cleaning')) {
        rate = 150.00;
        unit = '/ pc';
      } else if (order.services_registered && order.services_registered.includes('Steam Press')) {
        rate = 80.00;
        unit = '/ pc';
      }

      const subtotal = weight > 0 ? (weight * rate) : totalPrice;
      const discount = Math.max(0, subtotal - totalPrice);

      document.getElementById('modalWeight').innerText = `${weight.toFixed(1)} kg`;
      document.getElementById('modalServiceRate').innerText = `₱${rate.toFixed(2)} ${unit}`;
      document.getElementById('modalSubtotal').innerText = `₱${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;

      if (discount > 0) {
        document.getElementById('modalDiscountRow').style.display = 'flex';
        document.getElementById('modalDiscount').innerText = `-₱${discount.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
      } else {
        document.getElementById('modalDiscountRow').style.display = 'none';
      }

      document.getElementById('modalTotalAmount').innerText = `₱${totalPrice.toLocaleString('en-US', {minimumFractionDigits: 2})}`;

      const normStatus = normalizeOrderStatus(order.order_status);
      document.getElementById('modalOrderStatusSelect').value = normStatus;
      document.getElementById('modalPaymentStatusSelect').value = order.payment_status || 'Unpaid';
      document.getElementById('modalOrderNotes').value = order.special_instructions || '';

      const modal = document.getElementById('order-details-modal');
      modal.style.display = 'flex';
      setTimeout(() => modal.classList.add('is-active'), 10);
    }

    function closeOrderDetailsModal() {
      const modal = document.getElementById('order-details-modal');
      modal.classList.remove('is-active');
      setTimeout(() => modal.style.display = 'none', 200);
    }

    async function saveOrderDetailsChanges() {
      if (!currentActiveOrder) return;

      const newStatus = document.getElementById('modalOrderStatusSelect').value;
      const newPayment = document.getElementById('modalPaymentStatusSelect').value;
      const newNotes = document.getElementById('modalOrderNotes').value;

      try {
        const token = localStorage.getItem('authToken');
        const response = await fetch('api/update_order.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({
            order_id: currentActiveOrder.id,
            order_status: newStatus,
            payment_status: newPayment,
            special_instructions: newNotes
          })
        });

        const res = await response.json();
        if (response.ok) {
          currentActiveOrder.order_status = newStatus;
          currentActiveOrder.payment_status = newPayment;
          currentActiveOrder.special_instructions = newNotes;

          updateTabCounts();
          renderOrdersTable();
          closeOrderDetailsModal();
        } else {
          alert(res.message || 'Could not update order status.');
        }
      } catch (err) {
        console.error("Error updating order:", err);
        alert("Server communication error while saving status update.");
      }
    }

    let editActiveOrder = null;

    function openEditOrderModal(orderId) {
      const order = allOrdersData.find(o => parseInt(o.id) === parseInt(orderId));
      if (!order) return;
      editActiveOrder = order;

      document.getElementById('editModalOrderCode').innerText = `Edit Order #${order.order_code}`;
      document.getElementById('editModalCustomerInfo').innerText = `Client: ${order.customer_name} (📱 ${order.customer_phone || 'N/A'})`;

      const srvSelect = document.getElementById('editOrderServiceSelect');
      const srvRegistered = order.services_registered || '';
      if (srvRegistered.includes('Dry Cleaning')) {
        srvSelect.value = 'Dry Cleaning';
      } else if (srvRegistered.includes('Steam Press')) {
        srvSelect.value = 'Steam Press';
      } else {
        srvSelect.value = 'Wash & Fold';
      }

      document.getElementById('editOrderWeightInput').value = parseFloat(order.weight_kg) || 8.0;
      document.getElementById('editOrderStudentRate').checked = srvRegistered.includes('Student Rate');
      document.getElementById('editOrderExpressRush').checked = srvRegistered.includes('Express Rush');

      document.getElementById('editOrderPaymentStatusSelect').value = order.payment_status || 'Unpaid';
      document.getElementById('editOrderStatusSelect').value = normalizeOrderStatus(order.order_status);
      document.getElementById('editOrderSpecialNotes').value = order.special_instructions || '';

      calculateEditOrderTotal();

      const modal = document.getElementById('edit-order-modal');
      modal.style.display = 'flex';
      setTimeout(() => modal.classList.add('is-active'), 10);
    }

    function closeEditOrderModal() {
      const modal = document.getElementById('edit-order-modal');
      modal.classList.remove('is-active');
      setTimeout(() => modal.style.display = 'none', 200);
      editActiveOrder = null;
    }

    function calculateEditOrderTotal() {
      const srvSelect = document.getElementById('editOrderServiceSelect');
      const opt = srvSelect.options[srvSelect.selectedIndex];
      const rate = parseFloat(opt.getAttribute('data-rate')) || 25.00;
      const unit = opt.getAttribute('data-unit') || '/ kg';

      document.getElementById('editOrderUnitLabel').innerText = unit.replace('/ ', '');
      document.getElementById('editOrderRateDisplay').innerText = `₱${rate.toFixed(2)} ${unit}`;

      const weight = parseFloat(document.getElementById('editOrderWeightInput').value) || 0;
      let subtotal = weight * rate;
      document.getElementById('editOrderSubtotalDisplay').innerText = `₱${subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}`;

      let discount = 0;
      if (document.getElementById('editOrderStudentRate').checked) {
        discount = subtotal * 0.10;
        document.getElementById('editOrderDiscountRow').style.display = 'flex';
        document.getElementById('editOrderDiscountDisplay').innerText = `-₱${discount.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
      } else {
        document.getElementById('editOrderDiscountRow').style.display = 'none';
      }

      let rushFee = 0;
      if (document.getElementById('editOrderExpressRush').checked) {
        rushFee = 50.00;
        document.getElementById('editOrderRushRow').style.display = 'flex';
      } else {
        document.getElementById('editOrderRushRow').style.display = 'none';
      }

      const total = Math.max(0, subtotal - discount + rushFee);
      document.getElementById('editOrderTotalDisplay').innerText = `₱${total.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
      return { subtotal, discount, rushFee, total, rate, unit };
    }

    async function saveEditOrderChanges() {
      if (!editActiveOrder) return;

      const calc = calculateEditOrderTotal();
      const srvSelect = document.getElementById('editOrderServiceSelect');
      const baseSrv = srvSelect.value;
      
      const srvList = [baseSrv];
      if (document.getElementById('editOrderStudentRate').checked) srvList.push('Student Rate');
      if (document.getElementById('editOrderExpressRush').checked) srvList.push('Express Rush (Same-Day)');
      const finalServices = srvList.join(', ');

      const newWeight = parseFloat(document.getElementById('editOrderWeightInput').value) || 1.0;
      const newPrice = calc.total;
      const newPayment = document.getElementById('editOrderPaymentStatusSelect').value;
      const newStatus = document.getElementById('editOrderStatusSelect').value;
      const newNotes = document.getElementById('editOrderSpecialNotes').value;

      try {
        const token = localStorage.getItem('authToken');
        const response = await fetch('api/update_order.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify({
            order_id: editActiveOrder.id,
            order_status: newStatus,
            payment_status: newPayment,
            special_instructions: newNotes,
            services_registered: finalServices,
            weight_kg: newWeight,
            total_price: newPrice
          })
        });

        const res = await response.json();
        if (response.ok) {
          editActiveOrder.order_status = newStatus;
          editActiveOrder.payment_status = newPayment;
          editActiveOrder.special_instructions = newNotes;
          editActiveOrder.services_registered = finalServices;
          editActiveOrder.weight_kg = newWeight;
          editActiveOrder.total_price = newPrice;

          updateTabCounts();
          renderOrdersTable();
          closeEditOrderModal();
        } else {
          alert(res.message || 'Could not save order changes.');
        }
      } catch (err) {
        console.error("Error updating order:", err);
        alert("Server communication error while saving order edit.");
      }
    }

    function logout() {
      localStorage.clear();
      sessionStorage.clear();
      window.location.replace('index.html');
    }

    // ==========================================
    // MODULE SWITCHER LOGIC
    // ==========================================
    function switchModule(moduleName, element) {
      if (moduleName === 'staff') {
        openManageUsersModal('staff');
        return;
      }

      // Hide all module views
      document.querySelectorAll('.dashboard-module-view').forEach(v => {
        v.style.display = 'none';
      });

      // Show target view
      const target = document.getElementById(`module-${moduleName}`);
      if (target) {
        target.style.display = 'block';
      }

      // Update active nav class
      document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('is-active');
      });

      if (element) {
        element.classList.add('is-active');
      } else {
        const matchingLink = document.querySelector(`.nav-item[data-module="${moduleName}"]`);
        if (matchingLink) matchingLink.classList.add('is-active');
      }

      // Load specific module data
      if (moduleName === 'records') {
        renderOrdersTable();
      } else if (moduleName === 'customers') {
        loadCustomersTable();
      } else if (moduleName === 'inventory') {
        loadInventoryTable();
      } else if (moduleName === 'analytics') {
        renderSalesAnalytics();
      } else if (moduleName === 'settings') {
        loadSettings();
      }
    }

    // ==========================================
    // SYSTEM SETTINGS LOGIC
    // ==========================================
    async function loadSettings() {
      try {
        const res = await fetch('api/get_settings.php');
        const json = await res.json();
        if (json.status === 'success' && json.data) {
          const d = json.data;
          if (document.getElementById('settingStoreName')) document.getElementById('settingStoreName').value = d.store_name || '';
          if (document.getElementById('settingStorePhone')) document.getElementById('settingStorePhone').value = d.store_phone || '';
          if (document.getElementById('settingStoreAddress')) document.getElementById('settingStoreAddress').value = d.store_address || '';
          if (document.getElementById('settingOperatingHours')) document.getElementById('settingOperatingHours').value = d.operating_hours || '';
          if (document.getElementById('settingRateWashFold')) document.getElementById('settingRateWashFold').value = d.rate_wash_fold || '25.00';
          if (document.getElementById('settingRateDryClean')) document.getElementById('settingRateDryClean').value = d.rate_dry_clean || '150.00';
          if (document.getElementById('settingRateSteamPress')) document.getElementById('settingRateSteamPress').value = d.rate_steam_press || '80.00';
          if (document.getElementById('settingRateStudent')) document.getElementById('settingRateStudent').value = d.rate_student || '120.00';
          if (document.getElementById('settingFeeExpressRush')) document.getElementById('settingFeeExpressRush').value = d.fee_express_rush || '150.00';
          if (document.getElementById('settingReceiptHeader')) document.getElementById('settingReceiptHeader').value = d.receipt_header || '';
          if (document.getElementById('settingReceiptFooter')) document.getElementById('settingReceiptFooter').value = d.receipt_footer || '';
        }
      } catch (err) {
        console.error('Error loading settings:', err);
      }
    }

    async function saveSettings() {
      const payload = {
        store_name: document.getElementById('settingStoreName')?.value.trim() || '',
        store_phone: document.getElementById('settingStorePhone')?.value.trim() || '',
        store_address: document.getElementById('settingStoreAddress')?.value.trim() || '',
        operating_hours: document.getElementById('settingOperatingHours')?.value.trim() || '',
        rate_wash_fold: document.getElementById('settingRateWashFold')?.value || '25.00',
        rate_dry_clean: document.getElementById('settingRateDryClean')?.value || '150.00',
        rate_steam_press: document.getElementById('settingRateSteamPress')?.value || '80.00',
        rate_student: document.getElementById('settingRateStudent')?.value || '120.00',
        fee_express_rush: document.getElementById('settingFeeExpressRush')?.value || '150.00',
        receipt_header: document.getElementById('settingReceiptHeader')?.value.trim() || '',
        receipt_footer: document.getElementById('settingReceiptFooter')?.value.trim() || ''
      };

      try {
        const res = await fetch('api/save_settings.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const json = await res.json();
        if (json.status === 'success') {
          alert('System & store settings saved successfully!');
        } else {
          alert(json.message || 'Failed to save settings.');
        }
      } catch (err) {
        console.error(err);
        alert('Server communication error while saving settings.');
      }
    }

    // ==========================================
    // CSV EXPORT LOGIC
    // ==========================================
    function exportOrdersToCSV() {
      let url = 'api/export_sales_csv.php?';
      const params = [];

      if (currentDateFilter.from) {
        params.push(`start_date=${encodeURIComponent(currentDateFilter.from)}`);
      }
      if (currentDateFilter.to) {
        params.push(`end_date=${encodeURIComponent(currentDateFilter.to)}`);
      }
      if (currentFilterStatus && currentFilterStatus !== 'All') {
        params.push(`status=${encodeURIComponent(currentFilterStatus)}`);
      }

      url += params.join('&');
      window.location.href = url;
    }

    // ==========================================
    // INVENTORY MANAGEMENT LOGIC
    // ==========================================
    let allInventoryData = [];

    async function loadInventoryTable() {
      const tbody = document.getElementById('adminInventoryTableBody');
      if (!tbody) return;
      tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding: 24px; color: var(--text-muted);">Loading supplies ledger...</td></tr>';

      try {
        const res = await fetch('api/get_inventory.php');
        const data = await res.json();

        if (data.status === 'success' && data.data) {
          allInventoryData = data.data;
          renderInventoryTable();
          updateInventoryKPIs();
        } else {
          tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding: 24px; color: #f87171;">Failed to load inventory.</td></tr>';
        }
      } catch (err) {
        console.error('Error fetching inventory:', err);
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding: 24px; color: #f87171;">Server connection error.</td></tr>';
      }
    }

    function updateInventoryKPIs() {
      let totalItems = allInventoryData.length;
      let lowStock = 0;
      let criticalStock = 0;
      let totalValue = 0;

      allInventoryData.forEach(item => {
        const stock = parseFloat(item.current_stock) || 0;
        const reorder = parseFloat(item.reorder_level) || 5;
        const cost = parseFloat(item.cost_per_unit) || 0;

        totalValue += (stock * cost);

        if (stock <= 0) {
          criticalStock++;
        } else if (stock <= reorder) {
          lowStock++;
        }
      });

      const elTotal = document.getElementById('invTotalItemsCount');
      if (elTotal) elTotal.textContent = `${totalItems} Items`;

      const elLow = document.getElementById('invLowStockCount');
      if (elLow) elLow.textContent = `${lowStock} Item${lowStock !== 1 ? 's' : ''}`;

      const elCrit = document.getElementById('invCriticalCount');
      if (elCrit) elCrit.textContent = `${criticalStock} Item${criticalStock !== 1 ? 's' : ''}`;

      const elVal = document.getElementById('invTotalValue');
      if (elVal) elVal.textContent = `₱${totalValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }

    function renderInventoryTable() {
      const tbody = document.getElementById('adminInventoryTableBody');
      if (!tbody) return;

      const search = (document.getElementById('inventorySearchInput')?.value || '').toLowerCase().trim();
      const filtered = allInventoryData.filter(i => {
        return !search || (i.item_name && i.item_name.toLowerCase().includes(search)) || (i.category && i.category.toLowerCase().includes(search));
      });

      if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center; padding: 24px; color: var(--text-muted);">No supply items found.</td></tr>';
        return;
      }

      tbody.innerHTML = filtered.map(item => {
        const stock = parseFloat(item.current_stock) || 0;
        const reorder = parseFloat(item.reorder_level) || 5;
        const cost = parseFloat(item.cost_per_unit) || 0;

        let badgeClass = 'stock-good';
        let badgeText = 'IN STOCK';
        if (stock <= 0) {
          badgeClass = 'stock-critical';
          badgeText = 'OUT OF STOCK';
        } else if (stock <= reorder) {
          badgeClass = 'stock-low';
          badgeText = 'LOW STOCK';
        }

        return `
          <tr>
            <td>
              <div class="flex items-center gap-2">
                <span class="text-base">${getCategoryIcon(item.category)}</span>
                <strong style="color:#ffffff;">${item.item_name}</strong>
              </div>
            </td>
            <td>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-800 text-slate-300 border border-slate-700">${item.category}</span>
            </td>
            <td>
              <span style="font-size: 0.95rem; font-weight: 700; color: ${stock <= reorder ? '#f87171' : '#2dd4bf'};">${stock.toFixed(1)}</span>
            </td>
            <td>
              <span class="text-slate-300 text-xs">${item.unit}</span>
            </td>
            <td>
              <span class="text-slate-400 text-xs font-mono">&le; ${reorder.toFixed(1)} ${item.unit}</span>
            </td>
            <td>
              <span class="text-slate-200 font-semibold text-xs">₱${cost.toFixed(2)}</span>
            </td>
            <td>
              <span class="stock-badge ${badgeClass}">${badgeText}</span>
            </td>
            <td>
              <div class="flex items-center gap-1.5">
                <button type="button" onclick="adjustSupplyStock(${item.id}, ${stock}, 5)" title="+5 Quick Restock" class="px-2 py-1 rounded bg-teal-500/10 hover:bg-teal-500 text-teal-300 hover:text-white border border-teal-500/30 text-xs font-bold transition-all cursor-pointer">
                  +5
                </button>
                <button type="button" onclick="adjustSupplyStock(${item.id}, ${stock}, -1)" title="-1 Usage Deduction" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs font-bold transition-all cursor-pointer">
                  -1
                </button>
                <button type="button" onclick="promptCustomStock(${item.id}, ${stock})" title="Set Custom Stock Quantity" class="px-2 py-1 rounded bg-sky-500/10 hover:bg-sky-500 text-sky-300 hover:text-white border border-sky-500/30 text-xs font-semibold transition-all cursor-pointer">
                  Set
                </button>
                <button type="button" onclick="deleteSupplyItem(${item.id}, '${item.item_name.replace(/'/g, "\\'")}')" title="Delete Item" class="w-7 h-7 rounded bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/30 flex items-center justify-center text-xs transition-all cursor-pointer">
                  🗑️
                </button>
              </div>
            </td>
          </tr>
        `;
      }).join('');
    }

    function getCategoryIcon(cat) {
      const map = {
        'Detergent': '🫧',
        'Softener': '🌸',
        'Bleach': '✨',
        'Packaging': '🛍️',
        'Chemicals': '🧪',
        'Other': '📦'
      };
      return map[cat] || '📦';
    }

    function openAddSupplyModal() {
      const form = document.getElementById('add-supply-form');
      if (form) form.reset();
      const modal = document.getElementById('add-supply-modal');
      if (modal) modal.classList.add('active');
    }

    function closeAddSupplyModal() {
      const modal = document.getElementById('add-supply-modal');
      if (modal) modal.classList.remove('active');
    }

    async function saveSupplyItem(e) {
      e.preventDefault();
      const form = e.target;
      const btn = document.getElementById('saveSupplySubmitBtn');
      btn.disabled = true;
      btn.textContent = 'Saving...';

      const payload = {
        item_name: form.item_name.value.trim(),
        category: form.category.value,
        unit: form.unit.value.trim(),
        current_stock: parseFloat(form.current_stock.value) || 0,
        reorder_level: parseFloat(form.reorder_level.value) || 5,
        cost_per_unit: parseFloat(form.cost_per_unit.value) || 0
      };

      try {
        const res = await fetch('api/create_inventory_item.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (data.status === 'success') {
          closeAddSupplyModal();
          loadInventoryTable();
        } else {
          alert(data.message || 'Failed to save supply item.');
        }
      } catch (err) {
        console.error('Error saving item:', err);
        alert('Server communication error.');
      } finally {
        btn.disabled = false;
        btn.textContent = 'Save Item';
      }
    }

    async function adjustSupplyStock(id, currentStock, delta) {
      const newStock = Math.max(0, currentStock + delta);
      try {
        const res = await fetch('api/update_inventory_stock.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id, current_stock: newStock })
        });
        const data = await res.json();
        if (data.status === 'success') {
          loadInventoryTable();
        } else {
          alert(data.message || 'Could not update stock.');
        }
      } catch (err) {
        console.error(err);
      }
    }

    function promptCustomStock(id, currentStock) {
      const val = prompt("Enter new current stock count for this item:", currentStock);
      if (val !== null && !isNaN(val)) {
        const num = Math.max(0, parseFloat(val));
        adjustSupplyStock(id, 0, num);
      }
    }

    async function deleteSupplyItem(id, name) {
      if (!confirm(`Are you sure you want to remove "${name}" from inventory?`)) return;

      try {
        const res = await fetch('api/delete_inventory_item.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id })
        });
        const data = await res.json();
        if (data.status === 'success') {
          loadInventoryTable();
        } else {
          alert(data.message || 'Could not delete item.');
        }
      } catch (err) {
        console.error(err);
      }
    }
  </script>
</body>
</html>
