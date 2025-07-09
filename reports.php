<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports</title>
  <link rel="stylesheet" href="/NyamaTrack_App/utils/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

</head>
<body>
  <?php include 'includes/left-sidebar.php'; ?>
  <?php include 'includes/bottom-sidebar.php'; ?>
  <main class="py-4 reports">
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col-12">
          <h1 class="mb-3">Reports</h1>
          <form id="dateFilterForm" class="row g-3 align-items-end mb-4">
            <div class="col-md-4">
              <label for="startDate" class="form-label">Start Date</label>
              <input type="date" class="form-control" id="startDate" value="<?php echo date('Y-m-01'); ?>">
            </div>
            <div class="col-md-4">
              <label for="endDate" class="form-label">End Date</label>
              <input type="date" class="form-control" id="endDate" value="<?php echo date('Y-m-t'); ?>">
            </div>
            <div class="col-md-4">
              <button type="button">
                <i class="fas fa-filter me-2"></i>Apply Filter
              </button>
            </div>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col-12">
        <div class="card" id="chart-card">
            <div class="card-body">
              <h5 class="card-title mb-4">Current Month Sales (Fake Data)</h5>
              <div class="chart-container" style="position: relative; height: 400px;">
                <canvas id="salesChart"></canvas>
              </div>
            </div>
          </div>
          
          <!-- Summary Table -->
          <div class="card mt-4" id="card">
            <div class="card-body">
              <h5 class="card-title mb-4">Sales Summary</h5>
              <div class="table-responsive">
                <table class="table table-hover" id="table">
                  <thead>
                    <tr>
                      <th>Metric</th>
                      <th class="text-end">Value</th>
                      <th class="text-end">% Change (vs Previous Period)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Total Sales</td>
                      <td class="text-end">Ksh 245,780</td>
                      <td class="text-end text-success">+12.5% <i class="fas fa-arrow-up"></i></td>
                    </tr>
                    <tr>
                      <td>Number of Orders</td>
                      <td class="text-end">156</td>
                      <td class="text-end text-success">+8.3% <i class="fas fa-arrow-up"></i></td>
                    </tr>
                    <tr>
                      <td>Average Order Value</td>
                      <td class="text-end">Ksh 1,575</td>
                      <td class="text-end text-success">+3.9% <i class="fas fa-arrow-up"></i></td>
                    </tr>
                    <tr>
                      <td>Most Sold Item</td>
                      <td class="text-end">Choma Chops (45)</td>
                      <td class="text-end">-</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- End Summary Table -->
          
        </div>
      </div>
    </div>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Generate labels and fake sales data for the current month
    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const labels = Array.from({length: daysInMonth}, (_, i) => `${i + 1}`);
    const salesData = Array.from({length: daysInMonth}, () => Math.floor(Math.random() * 10000) + 5000);
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Daily Sales (KES)',
          data: salesData,
          borderColor: 'rgba(13,110,253,1)',
          backgroundColor: 'rgba(13,110,253,0.12)',
          borderWidth: 2,
          tension: 0.4,
          fill: true,
          pointRadius: 2,
          pointHoverRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'KES ' + context.parsed.y.toLocaleString();
              }
            }
          }
        },
        scales: {
          x: {
            title: { display: true, text: 'Day of Month' },
            grid: { display: false },
            ticks: { color: '#6c757d', font: { size: 13 } }
          },
          y: {
            title: { display: true, text: 'Sales (KES)' },
            grid: { color: 'rgba(0,0,0,0.04)' },
            ticks: {
              color: '#6c757d',
              callback: function(value) { return 'KES ' + value.toLocaleString(); }
            }
          }
        }
      }
    });
  </script>

</body>
</html>