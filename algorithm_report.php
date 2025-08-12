<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>NyamaTrack — Algorithm Reports | Nyamatrack.co.ke</title>
  <meta name="description" content="View your butchery's performance metrics and projections based on historical data. Nyamatrack.co.ke" />
  <link rel="stylesheet" href="utils/becken.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
 
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

    <section class="grid" aria-label="Key metrics">
      <div class="card kpi">
        <div class="muted">Monthly Sales</div>
        <div class="value" id="k-sales">KES 0</div>
        <small class="muted" id="k-sales-ch">0% vs last month</small>
      </div>
      <div class="card kpi">
        <div class="muted">Monthly Expenses</div>
        <div class="value" id="k-expenses">KES 0</div>
        <small class="muted" id="k-expenses-ch">0% vs last month</small>
      </div>
      <div class="card kpi">
        <div class="muted">Monthly Profit</div>
        <div class="value" id="k-profit">KES 0</div>
        <small class="muted" id="k-profit-ch">0% vs last month</small>
      </div>
      <div class="card kpi">
        <div class="muted">Avg. Daily Kilos</div>
        <div class="value" id="k-avg-kilos">0 kg</div>
        <small class="muted">This month</small>
      </div>
    </section>

    <section class="card" style="margin-top:16px">
      <div class="row" style="justify-content:space-between;align-items:center">
        <strong>Monthly Performance</strong>
        <span class="muted" id="chart-range">Current Month Projection</span>
      </div>
      <canvas id="barChart" role="img" aria-label="Bar chart showing monthly performance"></canvas>
    </section>

    <section class="card" style="margin-top:16px">
      <div class="row" style="justify-content:space-between;align-items:center">
        <strong>Recent Transactions</strong>
        <span class="muted" id="tx-count">0 transactions</span>
      </div>
      <div class="table-wrap">
        <table aria-describedby="tx-count">
          <thead>
            <tr>
              <th>Date</th>
              <th>Buy Price</th>
              <th>Sell Price</th>
              <th>Total Sales</th>
              <th>Expenses</th>
              <th>Kilos</th>
              <th>Profit</th>
            </tr>
          </thead>
          <tbody id="transactions-body">
            <!-- Data will be loaded via JavaScript -->
          </tbody>
        </table>
      </div>
    </section>
  </main>

  <footer>
    © <span id="year"></span> NyamaTrack — Nyamatrack.co.ke
  </footer>
  <script src="js/algorithm_script.js"></script> 
</body>

</html>