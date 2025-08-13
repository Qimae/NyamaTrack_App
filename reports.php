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
  <!-- Loading Overlay -->
  <div id="loading" class="position-fixed w-100 h-100 bg-dark bg-opacity-75 d-flex justify-content-center align-items-center" style="z-index: 9999; top: 0; left: 0; display: none;">
    <div class="text-center text-white">
      <div class="spinner-border mb-3" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <h4>Loading Reports Data...</h4>
    </div>
  </div>

  <?php include 'includes/left-sidebar.php'; ?>
  <?php include 'includes/bottom-sidebar.php'; ?>
  <script src="js/reports_script.js"></script>
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
              <h5 class="card-title mb-4">Sales Overview</h5>
              <div class="chart-container" style="position: relative; height: 400px;">
                <canvas id="salesChart"></canvas>
              </div>
            </div>
          </div>
          
          <!-- Summary Table -->
          <div class="card mt-4" id="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Sales Summary (Last 30 Days)</h5>
                <button id="refresh-reports" class="btn btn-outline-light">
                  <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
              </div>
              <div class="table-responsive">
                <table class="table table-hover bg-custom" id="reports-table">
                  <thead class="table-light">
                    <tr>
                      <th>Metric</th>
                      <th class="text-end">Value</th>
                      <th class="text-end">Change (vs Previous Period)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Data will be populated by JavaScript -->
                    <tr>
                      <td colspan="3" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2">Loading data...</div>
                      </td>
                    </tr>
                  </tbody>
              </div>
            </div>
          </div>
          <!-- End Summary Table -->
          
        </div>
      </div>
    </div>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</body>
</html>