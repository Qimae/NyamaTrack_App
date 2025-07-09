<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" href="/NyamaTrack_App/utils/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    window.onload = function() {
      const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']; // Months
      // You can add more months and data points if needed
      const data = {
        labels: labels,
        datasets: [
          {
            label: '',
            data: [20500, 23500, 19200, 27800, 22100, 26500], // Updated fake data (KES)
            borderColor: 'rgba(255,255,255,0.6)',
            borderWidth: 2.5,
            tension: 0.55,
            fill: false,
            pointRadius: 0,
            pointHoverRadius: 0,
            backgroundColor: 'rgba(255,255,255,0.1)'
          }
        ]
      };
      const config = {
        type: 'line',
        data: data,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            title: { display: false },
            tooltip: { enabled: false }
          },
          scales: {
            x: {
              grid: { display: false, drawBorder: false },
              ticks: {
                color: 'rgba(255,255,255,0.7)',
                font: { size: 14 }
              }
            },
            y: {
              display: true,
              grid: { display: false, drawBorder: false },
              ticks: {
                color: 'rgba(255,255,255,0.7)',
                font: { size: 14 }
              }
            }
          }
        }
      };
      const ctx = document.getElementById('financialChart').getContext('2d');
      if (ctx) {
        new Chart(ctx, config);
      }
    };
  </script>
</head>
<body>
  <main class="py-4 dashboard">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <h1 class="mb-4">Dashboard</h1>
        </div>
      </div>
      <div class="row g-4">
        <!-- First Row - Two Cards Side by Side -->
        <div class="col-md-6">
          <div class="card h-100" id="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-3">Total Sales</h5>
                <i class="fas fa-shopping-cart text-primary fs-3"></i>
              </div>
              <h2 class="mb-0">256</h2>
              <p class="mb-0">Total sales processed this month</p>
            </div>
          </div>
        </div>

      <div class="col-md-6">
        <div class="card h-100" id="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-3">Expenses</h5>
              <i class="fas fa-money-bill text-danger fs-3"></i>
            </div>
            <h2 class="mb-0">KES 12,456</h2>
            <p class="mb-0">Total expenses this month</p>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card h-100" id="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-3">Profit</h5>
              <i class="fas fa-wallet text-success fs-3"></i>
            </div>
            <h2 class="mb-0">KES 12,456</h2>
            <p class="mb-0">Total profit this month</p>
          </div>
        </div>
      </div>
      <div class="col-md-6" style="background-color: var(--primary-color);">
        <div class="card h-100" id="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-3">Inventory</h5>
              <i class="fas fa-box text-warning fs-3"></i>
            </div>
            <h2 class="mb-0">12,456</h2>
            <p class="mb-0">Total inventory this month</p>
          </div>
        </div>
      </div>

      <!-- Second Row - Full Width Card -->
      <div class="col-12">
        <div class="card" id="chart-card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title mb-0">Financial Overview</h5>
              <div class="text-end">
                <h3 class="mb-0 text-success">+25%</h3>
                <p class="mb-0">Revenue Growth (YoY)</p>
              </div>
            </div>
            <div class="chart-container" style="height: 400px; position: relative;">
              <canvas id="financialChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include 'includes/left-sidebar.php'; ?>
  <?php include 'includes/bottom-sidebar.php'; ?>
</body>
</html>