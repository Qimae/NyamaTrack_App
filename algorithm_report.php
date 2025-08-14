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
  <link rel="stylesheet" href="utils/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
  <link rel="stylesheet" href="utils/becken.css">

</head>

<body>
  <div class="bg" aria-hidden="true">
    <div class="orb red"></div>
    <div class="orb amber"></div>
    <div class="grid-overlay"></div>
  </div>
  <?php include 'includes/left-sidebar.php'; ?>
  <?php include 'includes/bottom-sidebar.php'; ?>

  <main class="py-4 dashboard">
    <div class="container-fluid">
      <section class="hero">
        <h1>Advanced Analytics & Projections</h1>
        <p class="muted">Leverage our predictive algorithms to forecast sales trends, optimize inventory, and maximize your butchery's profitability.</p>
      </section>

      <div class="row g-4 mb-4">
        <!-- KPI Cards -->
        <div class="col-md-6 col-lg-3">
          <div class="card h-100" id="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-3">Monthly Sales</h5>
                <i class="fas fa-shopping-cart text-primary fs-3"></i>
              </div>
              <h2 id="k-sales" class="mb-0">KES 0.00</h2>
              <p id="k-sales-ch" class="text-white mb-0">0% vs last month</p>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card h-100" id="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-3">Monthly Expenses</h5>
                <i class="fas fa-money-bill text-danger fs-3"></i>
              </div>
              <h2 id="k-expenses" class="mb-0">KES 0.00</h2>
              <p id="k-expenses-ch" class="text-white mb-0">0% vs last month</p>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card h-100" id="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-3">Monthly Profit</h5>
                <i class="fas fa-wallet text-success fs-3"></i>
              </div>
              <h2 id="k-profit" class="mb-0">KES 0.00</h2>
              <p id="k-profit-ch" class="text-white">
                <i class="fas fa-arrow-up text-success"></i>
                <span class="text-success">+0.0%</span> vs last month
              </p>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card h-100" id="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-3">Avg. Daily Kilos</h5>
                <i class="fas fa-weight text-warning fs-3"></i>
              </div>
              <h2 id="k-avg-kilos" class="mb-0">0.00</h2>
              <p class="text-white mb-0">Total kilos sold</p>
            </div>
          </div>
        </div>
      </div>

      <section class="card" style="margin-top:16px">
        <div class="row" style="justify-content:space-between;align-items:center">
          <span class="muted" id="chart-range">Current Month Projection</span>
        </div>
        <canvas id="barChart" role="img" aria-label="Bar chart showing monthly performance"></canvas>
      </section>

      <section class="card" style="margin-top:16px">
        <div class="row" style="justify-content:space-between;align-items:center">
          <strong class="muted">Recent Transactions</strong>
          <span class="muted" id="tx-count" style="display: none;">0 transactions</span>
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
    </div>
  </main>

  <footer>
    © <span id="year"></span> NyamaTrack — Nyamatrack.co.ke
  </footer>
  <script src="js/algorithm_script.js"></script>
</body>

</html>