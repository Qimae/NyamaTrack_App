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

  <main class="py-4 transactions">
    <!-- Transactions form -->
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <h1 class="mb-4">Beef Transactions</h1>
          <!-- daily transactions form -->
          <form method="POST" action="" class="row g-3 align-items-end mb-4">
            <div class="col-md-2">
              <input type="date" class="form-control" name="date" id="date" required>
            </div>
            <div class="col-md-3">
              <input type="text" class="form-control" placeholder="Purchase Price (Ksh)" name="purchase_price" id="purchase_price" required>
            </div>
            <div class="col-md-2">
              <input type="number" class="form-control" placeholder="Sell Price (Ksh)" name="sell_price" id="sell_price" required>
            </div>
            <div class="col-md-2">
              <input type="number" step="0.01" class="form-control" placeholder="Total Cash Sales (Ksh)" name="total_cash_sales" id="total_cash_sales" required>
            </div>
            <div class="col-md-2">
              <input type="number" step="0.01" class="form-control" placeholder="Daily Expense" name="daily_expense" id="daily_expense" required>
            </div>
            <div class="col-md-1 d-grid">
              <button type="submit" class="btn btn-success w-100">
                <i class="fas fa-save me-1"></i>Save
              </button>
            </div>
          </form>
          </br>

          <!-- transaction filter -->
          <form id="dateFilterForm" class="row g-3 align-items-end mb-4">
            <div class="col-md-4">
              <div class="form-group">
                <label for="startDate">Start Date</label>
                <input type="date" class="form-control" id="startDate">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="endDate">End Date</label>
                <input type="date" class="form-control" id="endDate">
              </div>
            </div>
            <div class="col-md-4">
              <button type="button" class="btn btn-primary">
                <i class="fas fa-filter me-2"></i>Apply Filter
              </button>
            </div>
          </form>
          <div class="card" id="card">
            <div class="card-body">
              <h5 class="card-title mb-4">Beef Transactions</h5>
              <div class="table-responsive">
                <table class="table table-hover" id="table">
                  <thead>
                    <tr>
                      <th>Transaction ID</th>
                      <th>Beef Type</th>
                      <th>Quantity</th>
                      <th>Price</th>
                      <th>Total</th>
                      <th>Transaction Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>1</td>
                      <td>Beef</td>
                      <td>1</td>
                      <td>100</td>
                      <td>100</td>
                      <td>2022-01-01</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </main>
</body>

</html>