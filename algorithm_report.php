<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>NyamaTrack — Algorithm Reports | Nyamatrack.co.ke</title>
  <meta name="description" content="Upload historical monthly sales (KES) or use demo data, then forecast future income with confidence bands. Nyamatrack.co.ke" />
  <link rel="stylesheet" href="utils/becken.css">
</head>

<body>
  <div class="bg" aria-hidden="true">
    <div class="orb red"></div>
    <div class="orb amber"></div>
    <div class="grid-overlay"></div>
  </div>
  <header>
    <div class="container">
      <nav aria-label="Primary">
        <div class="brand">
          <div class="brand-badge" aria-hidden="true"></div>
          <a href="subscription.php" aria-label="NyamaTrack home">NyamaTrack</a>
          <span class="pill">Nyamatrack.co.ke</span>
        </div>
        <div class="nav-links">
          <a href="subscription.php">Subscription</a>
          <a href="algorithm_report.php" aria-current="page">Algorithm Reports</a>
        </div>
      </nav>
    </div>
  </header>

  <main class="container">
    <section class="hero">
      <h1>Advanced Analytics & Projections</h1>
      <p class="muted">Leverage our predictive algorithms to forecast sales trends, optimize inventory, and maximize your butchery's profitability.</p>
    </section>

    <section class="controls" aria-label="Filters">
      <div>
        <label for="date">Date</label>
        <input id="date" type="date" />
      </div>
      <div>
        <label for="outlet">Outlet</label>
        <select id="outlet">
          <option value="all">All Outlets</option>
          <option>Nairobi CBD</option>
          <option>Westlands</option>
          <option>Kayole</option>
        </select>
      </div>
      <div>
        <label for="category">Category</label>
        <select id="category">
          <option value="all">All</option>
          <option value="beef">Beef</option>
          <option value="goat">Goat</option>
          <option value="chicken">Chicken</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div class="row" style="align-items:flex-end">
        <button class="btn primary" id="apply">Apply</button>
        <button class="btn" id="export">Export CSV</button>
        <button class="btn" id="print">Print</button>
      </div>
    </section>

    <section class="grid" aria-label="Key metrics">
      <div class="card kpi">
        <div class="muted">Total Sales</div>
        <div class="value" id="k-sales">KES 0</div>
        <small class="muted" id="k-sales-ch">0% vs yesterday</small>
      </div>
      <div class="card kpi">
        <div class="muted">Expenses</div>
        <div class="value" id="k-expenses">KES 0</div>
        <small class="muted" id="k-expenses-ch">0% vs yesterday</small>
      </div>
      <div class="card kpi">
        <div class="muted">Profit</div>
        <div class="value" id="k-profit">KES 0</div>
        <small class="muted" id="k-profit-ch">0% vs yesterday</small>
      </div>
      <div class="card kpi">
        <div class="muted">Inventory (kg)</div>
        <div class="value" id="k-inventory">0 kg</div>
        <small class="muted">End of day estimate</small>
      </div>
    </section>

    <section class="card" style="margin-top:16px">
      <div class="row" style="justify-content:space-between;align-items:center">
        <strong>Sales vs Expenses</strong>
        <span class="muted" id="chart-range"></span>
      </div>
      <canvas id="barChart" role="img" aria-label="Bar chart showing sales vs expenses"></canvas>
    </section>

    <section class="card" style="margin-top:16px">
      <div class="row" style="justify-content:space-between;align-items:center">
        <strong>Transactions</strong>
        <span class="muted" id="tx-count">0 transactions</span>
      </div>
      <div class="table-wrap">
        <table aria-describedby="tx-count">
          <thead>
            <tr>
              <th>Date</th>
              <th>Buy Price</th>
              <th>Sell Price</th>
              <th>Total Cash Sales</th>
              <th>Daily Expense</th>
              <th>Total Cash</th>
              <th>Total Kilos</th>
              <th>Profit per KG</th>
              <th>Profit</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Data will be loaded via JavaScript -->
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <footer>
    © <span id="year"></span> NyamaTrack — Nyamatrack.co.ke
  </footer>

  <!-- Include Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <!-- Include our custom JavaScript -->
  <script src="js/algorithm_report.js"></script>

  <style>
    /* Add some styling for projected rows */
    tr.projected {
      opacity: 0.8;
      background-color: rgba(75, 192, 192, 0.1) !important;
    }
    
    /* Make sure the chart has a defined size */
    #barChart {
      width: 100%;
      height: 400px;
    }
    
    /* Style the loading spinner */
    .spinner {
      display: inline-block;
      width: 1em;
      height: 1em;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 50%;
      border-top-color: #fff;
      animation: spin 1s ease-in-out infinite;
      margin-right: 0.5em;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  </style>
</body>

</html>