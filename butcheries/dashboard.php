<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="utils/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="utils/becken.css">

</head>

<body>
  <div class="bg" aria-hidden="true">
    <div class="orb red"></div>
    <div class="orb amber"></div>
    <div class="grid-overlay"></div>
  </div>
  <!-- Loading Overlay -->
  <div id="loading" class="position-fixed w-100 h-100 bg-dark bg-opacity-75 d-flex justify-content-center align-items-center" style="z-index: 9999; top: 0; left: 0; visibility: hidden;">
    <div class="text-center text-white">
      <div class="spinner-border mb-3" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <h4>Loading Dashboard Data...</h4>
    </div>
  </div>

  <main class="py-4 dashboard">
    <div class="container-fluid">
      <?php if (isset($_SESSION['is_trial'])): ?>
      <div class="row mb-3">
        <div class="col-12">
          <div class="alert alert-<?php echo $_SESSION['trial_expired'] ? 'danger' : 'warning'; ?> d-flex justify-content-between align-items-center" role="alert">
            <div>
              <i class="fas <?php echo $_SESSION['trial_expired'] ? 'fa-exclamation-triangle' : 'fa-info-circle'; ?> me-2"></i>
              <?php if ($_SESSION['trial_expired']): ?>
                Your free trial has expired. Please subscribe to continue using NyamaTrack.
              <?php else: ?>
                You have <?php echo $_SESSION['trial_days_remaining']; ?> days left in your free trial. Subscribe now to continue after trial ends.
              <?php endif; ?>
            </div>
            <?php if ($_SESSION['trial_expired']): ?>
              <a href="./mpesa/subscription.php" class="btn btn-sm btn-primary">Subscribe Now</a>
            <?php else: ?>
              <a href="./mpesa/subscription.php" class="btn btn-sm btn-outline-primary">Upgrade Now</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
          <h1 class="mb-0">Dashboard</h1>
          <button id="refresh-dashboard" class="btn btn-outline-light">
            <i class="fas fa-sync-alt me-2"></i>Refresh Data
          </button>
        </div>
      </div>

      <div class="row g-4 mb-4">
        <!-- KPI Cards -->
        <div class="col-md-6 col-lg-3">
          <div class="card h-100" id="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-3">Total Sales</h5>
                <i class="fas fa-shopping-cart text-primary fs-3"></i>
              </div>
              <h2 class="mb-0">KES 0.00</h2>
              <p class="text-white mb-0">Last 30 days</p>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card h-100" id="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-3">Total Expenses</h5>
                <i class="fas fa-money-bill text-danger fs-3"></i>
              </div>
              <h2 class="mb-0">KES 0.00</h2>
              <p class="text-white mb-0">Last 30 days</p>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card h-100" id="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-3">Total Profit</h5>
                <i class="fas fa-wallet text-success fs-3"></i>
              </div>
              <h2 class="mb-0">KES 0.00</h2>
              <p class="text-white">
                <i class="fas fa-arrow-up text-success"></i>
                <span class="text-success">+0.0%</span> from last period
              </p>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="card h-100" id="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-3">Total Kilos</h5>
                <i class="fas fa-weight text-warning fs-3"></i>
              </div>
              <h2 class="mb-0">0.00</h2>
              <p class="text-white mb-0">Total kilos sold</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Financial Overview Chart -->
      <div class="row">
        <div class="col-12">
          <div class="card" id="chart-card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Financial Overview (Last 7 Days)</h5>
                <div class="text-end">
                  <h3 class="mb-0 text-success">+0%</h3>
                  <p class="mb-0">Revenue Growth</p>
                </div>
              </div>
              <div class="chart-container" style="height: 400px; position: relative;">
                <canvas id="financialChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include 'includes/left-sidebar.php'; ?>
  <?php include 'includes/bottom-sidebar.php'; ?>
  <script src="js/dashboard_script.js"></script>
</body>

</html>