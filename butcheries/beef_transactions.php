<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beef Transactions - NyamaTrack</title>
  <link rel="stylesheet" href="utils/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="utils/becken.css">

</head>

<body>
  <?php
  session_start();
  if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
  }
  include 'includes/left-sidebar.php';
  include 'includes/bottom-sidebar.php';
  ?>

  <div class="bg" aria-hidden="true">
    <div class="orb red"></div>
    <div class="orb amber"></div>
    <div class="grid-overlay"></div>
  </div>

  <main class="py-4 transactions">
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col-12">
          <h1 class="mb-4">Beef Transactions</h1>
          <!-- Add/Edit Transaction Form -->
          <div class="card mb-4" style="background-color: var(--secondary-color); color: var(--text-color);">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Transaction</h5>
              <button type="button" id="cancelEdit" class="btn btn-sm btn-outline-secondary" style="display: none;">
                <i class="fas fa-times me-1"></i> Cancel
              </button>
            </div>
            <div class="card-body">
              <form id="transactionForm" class="row g-3">
                <input type="hidden" id="transactionId">
                <div class="col-md-2">
                  <label for="transaction_date" class="form-label">Date <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" id="transaction_date" required>
                </div>
                <div class="col-md-2">
                  <label for="buy_price" class="form-label">Buy Price (Ksh/kg) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0" class="form-control" id="buy_price" placeholder="Enter buy price" required>
                </div>
                <div class="col-md-2">
                  <label for="sell_price" class="form-label">Sell Price (Ksh/kg) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0" class="form-control" id="sell_price" placeholder="Enter sell price" required>
                </div>
                <div class="col-md-2">
                  <label for="total_cash_sales" class="form-label">Cash Sales (Ksh) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0" class="form-control" id="total_cash_sales" placeholder="Enter cash sales" required>
                </div>
                <div class="col-md-2">
                  <label for="daily_expense" class="form-label">Daily Expense (Ksh) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0" class="form-control" id="daily_expense" placeholder="Enter daily expense" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                  <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-save me-1"></i> Save Transaction
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Transaction History Card with Filter -->
          <div class="card" style="background-color: var(--secondary-color); color: var(--text-color);">
            <!-- Date Filter Form -->
            <div class="card-header">
              <div class="row g-3">
                <div class="col-md-2">
                  <div class="form-group mb-0">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group mb-0">
                    <label for="endDate" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="endDate">
                  </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                  <button type="button" id="applyFilter" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-1"></i>Apply Filter
                  </button>
                </div>

              </div>
            </div>
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="fas fa-table me-2"></i>Transaction History</h5>
                <div class="d-flex">
                  <button type="button" id="refreshTable" class="btn btn-sm btn-outline-primary me-2">
                    <i class="fas fa-sync-alt me-1"></i> Refresh
                  </button>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fas fa-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                      <li><a class="dropdown-item export-btn" href="#" data-type="csv"><i class="fas fa-file-csv me-2"></i>CSV</a></li>
                      <li><a class="dropdown-item export-btn" href="#" data-type="excel"><i class="fas fa-file-excel me-2"></i>Excel</a></li>
                      <li><a class="dropdown-item export-btn" href="#" data-type="pdf"><i class="fas fa-file-pdf me-2"></i>PDF</a></li>
                      <li><a class="dropdown-item export-btn" href="#" data-type="print"><i class="fas fa-print me-2"></i>Print</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="table-wrap">
                <table id="transactionsTable" class="table table-hover" style="width:100%; color: var(--text-color); background-color: inherit;">
                  <thead style="background-color: inherit;">
                    <tr>
                      <th>Date</th>
                      <th>Buy Price (Ksh/kg)</th>
                      <th>Sell Price (Ksh/kg)</th>
                      <th>Cash Sales (Ksh)</th>
                      <th>Daily Expense (Ksh)</th>
                      <th>Total Cash (Ksh)</th>
                      <th>Total Kilos</th>
                      <th>Profit/KG (Ksh)</th>
                      <th>Profit (Ksh)</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Data will be loaded via JavaScript -->
                  </tbody>
                  <tfoot>
                    <tr>
                      <th>Totals:</th>
                      <th></th>
                      <th></th>
                      <th id="totalCashSales">0.00</th>
                      <th id="totalDailyExpense">0.00</th>
                      <th id="grandTotalCash">0.00</th>
                      <th id="grandTotalKilos">0.00</th>
                      <th id="avgProfitPerKg">0.00</th>
                      <th id="grandTotalProfit">0.00</th>
                      <th></th>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </main>
  <!-- JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/beef_script.js"></script>

</body>

</html>