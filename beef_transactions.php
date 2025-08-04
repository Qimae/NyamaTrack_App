<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beef Transactions - NyamaTrack</title>
  <link rel="stylesheet" href="/NyamaTrack_App/utils/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

  <main class="py-4 transactions">
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col-12">
          <h1 class="mb-4">Beef Transactions</h1>
          
       
          
        
          <!-- Add/Edit Transaction Form -->
          <div class="card mb-4">
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
                  <input type="number" step="0.01" min="0" class="form-control" id="buy_price" required>
                </div>
                <div class="col-md-2">
                  <label for="sell_price" class="form-label">Sell Price (Ksh/kg) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0" class="form-control" id="sell_price" required>
                </div>
                <div class="col-md-2">
                  <label for="total_cash_sales" class="form-label">Cash Sales (Ksh) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0" class="form-control" id="total_cash_sales" required>
                </div>
                <div class="col-md-2">
                  <label for="daily_expense" class="form-label">Daily Expense (Ksh) <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" min="0" class="form-control" id="daily_expense" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                  <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-save me-1"></i> Save Transaction
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- transaction filter -->
          <form id="dateFilterForm" class="row g-3 align-items-end mb-4">
            <div class="col-md-3">
              <div class="form-group">
                <label for="startDate">Start Date</label>
                <input type="date" class="form-control" id="startDate">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="endDate">End Date</label>
                <input type="date" class="form-control" id="endDate">
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group">
                <label for="statusFilter">Status</label>
                <select class="form-select" id="statusFilter">
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="partial">Partial</option>
                  <option value="paid">Paid</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <button type="button" id="applyFilter" class="btn btn-primary w-100">
                <i class="fas fa-filter me-2"></i>Apply Filter
              </button>
            </div>
          </form>
          <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
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
            <div class="card-body">
              <div class="table-responsive">
                <table id="transactionsTable" class="table table-striped table-hover nowrap" style="width:100%">
                  <thead class="table-light">
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
  <script>
    $(document).ready(function() {
      // Set default dates for filter
      const today = new Date();
      const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
      
      $('#startDate').val(formatDate(firstDay));
      $('#endDate').val(formatDate(today));
      
      // Format date to YYYY-MM-DD
      function formatDate(date) {
        const d = new Date(date);
        let month = '' + (d.getMonth() + 1);
        let day = '' + d.getDate();
        const year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
      }
      
      // Calculate derived values
      function calculateDerivedValues() {
        const buyPrice = parseFloat($('#buy_price').val()) || 0;
        const sellPrice = parseFloat($('#sell_price').val()) || 0;
        const cashSales = parseFloat($('#total_cash_sales').val()) || 0;
        const expense = parseFloat($('#daily_expense').val()) || 0;
        
        // Total Cash = Total Cash Sales + Daily Expense
        const totalCash = cashSales + expense;
        
        // Total Kilos = Total Cash / Sell Price
        const totalKilos = sellPrice > 0 ? totalCash / sellPrice : 0;
        
        // Profit per KG = Sell Price - Buy Price
        const profitPerKg = sellPrice - buyPrice;
        
        // Profit = (Profit per KG * Total Kilos) - Daily Expense
        const profit = (profitPerKg * totalKilos) - expense;
        
        // Update any calculated fields in the form if needed
        // Note: The actual calculations are done on the server side
      }
      
      // Recalculate when any input changes
      $('#buy_price, #sell_price, #total_cash_sales, #daily_expense').on('input', calculateDerivedValues);
      
      // Format number to 2 decimal places
      function formatNumber(num) {
        return parseFloat(num || 0).toFixed(2);
      }

      // Format date to DD/MM/YYYY
      function formatDisplayDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB');
      }

      // Render transactions in the table
      function renderTransactions(transactions) {
        const tbody = $('#transactionsTable tbody');
        tbody.empty();

        if (!transactions || transactions.length === 0) {
          tbody.append('<tr><td colspan="10" class="text-center">No transactions found</td></tr>');
          return;
        }

        // Calculate totals
        let totals = {
          cash_sales: 0,
          daily_expense: 0,
          total_cash: 0,
          total_kilos: 0,
          profit: 0
        };

        // Add rows for each transaction
        transactions.forEach(transaction => {
          const row = `
            <tr data-id="${transaction.id}">
              <td>${formatDisplayDate(transaction.transaction_date)}</td>
              <td class="text-end">${formatNumber(transaction.buy_price)}</td>
              <td class="text-end">${formatNumber(transaction.sell_price)}</td>
              <td class="text-end">${formatNumber(transaction.total_cash_sales)}</td>
              <td class="text-end">${formatNumber(transaction.daily_expense)}</td>
              <td class="text-end">${formatNumber(transaction.total_cash)}</td>
              <td class="text-end">${formatNumber(transaction.total_kilos)}</td>
              <td class="text-end">${formatNumber(transaction.profit_per_kg)}</td>
              <td class="text-end fw-bold ${transaction.profit >= 0 ? 'text-success' : 'text-danger'}">
                ${formatNumber(transaction.profit)}
              </td>
              <td class="text-center">
                <button class="btn btn-sm btn-outline-primary edit-btn me-1" data-id="${transaction.id}" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${transaction.id}" title="Delete">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          `;
          tbody.append(row);

          // Update totals
          totals.cash_sales += parseFloat(transaction.total_cash_sales || 0);
          totals.daily_expense += parseFloat(transaction.daily_expense || 0);
          totals.total_cash += parseFloat(transaction.total_cash || 0);
          totals.total_kilos += parseFloat(transaction.total_kilos || 0);
          totals.profit += parseFloat(transaction.profit || 0);
        });

        // Update footer totals
        $('#totalCashSales').text(formatNumber(totals.cash_sales));
        $('#totalDailyExpense').text(formatNumber(totals.daily_expense));
        $('#grandTotalCash').text(formatNumber(totals.total_cash));
        $('#grandTotalKilos').text(formatNumber(totals.total_kilos));
        $('#avgProfitPerKg').text(formatNumber(totals.profit / (transactions.length || 1)));
        $('#grandTotalProfit').text(formatNumber(totals.profit));
      }

      // Show error message
      function showError(message) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: message,
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        });
      }

      // Load transactions
      function loadTransactions() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        
        // Show loading state
        const refreshBtn = $('#refreshTable');
        const originalBtnText = refreshBtn.html();
        refreshBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
        
        $.ajax({
          url: 'api/beef_transactions_handler.php',
          method: 'GET',
          data: { 
            start_date: startDate,
            end_date: endDate
          },
          dataType: 'json',
          success: function(response) {
            refreshBtn.prop('disabled', false).html(originalBtnText);
            if (response.success) {
              renderTransactions(response.data);
            } else {
              showError('Failed to load transactions: ' + (response.error || 'Unknown error'));
            }
          },
          error: function(xhr, status, error) {
            refreshBtn.prop('disabled', false).html(originalBtnText);
            console.error('Error loading transactions:', error);
            showError('Failed to load transactions. Please try again.');
          }
        });
      }
      
      // Load transaction details for editing
      function loadTransactionDetails(transactionId) {
        if (!transactionId) {
          console.error('No transaction ID provided');
          return;
        }
        
        console.log('Loading transaction details for ID:', transactionId);
        
        // Show loading state
        const editBtn = $(`button[data-id="${transactionId}"]`);
        const originalBtnText = editBtn.html();
        editBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        
        // First, get the transaction details
        $.ajax({
          url: 'api/beef_transactions_handler.php',
          method: 'GET',
          data: { id: transactionId },
          dataType: 'json',
          success: function(response, status, xhr) {
            console.group('Transaction Details Response');
            console.log('Status:', status);
            console.log('Raw Response:', response);
            console.log('Response Type:', typeof response);
            
            // Reset button state
            editBtn.prop('disabled', false).html('<i class="fas fa-edit"></i>');
            
            try {
              let transaction = null;
              
              // Check different possible response formats
              if (response && response.success && response.data) {
                // Format: {success: true, data: {...}}
                transaction = response.data;
                console.log('Processing transaction data from response.data:', transaction);
              } else if (response && (response.id || response.transaction_id)) {
                // Format: {id: 1, ...} (direct transaction object)
                transaction = response;
                console.log('Processing direct transaction object:', transaction);
              } else if (response && response.transaction) {
                // Format: {transaction: {...}}
                transaction = response.transaction;
                console.log('Processing transaction from response.transaction:', transaction);
              } else {
                console.error('Unexpected response format:', response);
                throw new Error(response.error || 'Invalid response format');
              }
              
              if (!transaction) {
                throw new Error('Transaction data is empty');
              }
              
              // Fill the form with transaction data
              fillTransactionForm(transaction);
              
              // Update UI
              updateFormForEditing();
              
              // Don't show error if we got here
              return;
              
              console.groupEnd();
              
            } catch (error) {
              console.error('Error processing transaction data:', error);
              showError(error.message || 'Failed to process transaction details');
            }
          },
          error: function(xhr, status, error) {
            console.group('AJAX Error');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Response Text:', xhr.responseText);
            console.groupEnd();
            
            editBtn.prop('disabled', false).html('<i class="fas fa-edit"></i>');
            showError('Failed to load transaction details. Please check the console for more information.');
          }
        });
        
        // Helper function to fill the form with transaction data
        function fillTransactionForm(transaction) {
          console.log('Filling form with transaction data:', transaction);
          
          // Ensure we have the transaction ID (handle both 'id' and 'transaction_id')
          const transactionId = transaction.id || transaction.transaction_id;
          if (!transactionId) {
            console.error('No transaction ID found in response');
            throw new Error('Invalid transaction data: missing ID');
          }
          
          // Fill the form fields
          $('#transactionId').val(transactionId);
          $('#transaction_date').val(transaction.transaction_date || '');
          $('#buy_price').val(transaction.buy_price ? parseFloat(transaction.buy_price).toFixed(2) : '0.00');
          $('#sell_price').val(transaction.sell_price ? parseFloat(transaction.sell_price).toFixed(2) : '0.00');
          $('#total_cash_sales').val(transaction.total_cash_sales ? parseFloat(transaction.total_cash_sales).toFixed(2) : '0.00');
          $('#daily_expense').val(transaction.daily_expense ? parseFloat(transaction.daily_expense).toFixed(2) : '0.00');
          
          console.log('Form filled successfully');
        }
        
        // Helper function to update the UI for editing
        function updateFormForEditing() {
          console.log('Updating UI for editing');
          
          // Update form header
          $('.card-header h5').html('<i class="fas fa-edit me-2"></i>Edit Transaction');
          
          // Show cancel button
          $('#cancelEdit').show();
          
          // Scroll to form
          $('html, body').animate({
            scrollTop: $('#transactionForm').offset().top - 20
          }, 500);
          
          console.log('UI updated for editing');
        }
      }
      
      // Handle edit button click
      $(document).on('click', '.edit-btn', function() {
        const transactionId = $(this).data('id');
        if (transactionId) {
          loadTransactionDetails(transactionId);
        }
      });
      
      // Handle cancel edit
      $('#cancelEdit').on('click', function() {
        resetForm();
        $('html, body').animate({
          scrollTop: 0
        }, 500);
      });
      
      // Reset form
      function resetForm() {
        $('#transactionForm')[0].reset();
        $('#transactionId').val('');
        $('.card-header h5').html('<i class="fas fa-plus-circle me-2"></i>Add New Transaction');
        $('#cancelEdit').hide();
      }
      
      // Handle form submission
      $('#transactionForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
          user_id: 1, // Replace with actual user ID from session
          transaction_date: $('#transaction_date').val(),
          buy_price: parseFloat($('#buy_price').val()) || 0,
          sell_price: parseFloat($('#sell_price').val()) || 0,
          total_cash_sales: parseFloat($('#total_cash_sales').val()) || 0,
          daily_expense: parseFloat($('#daily_expense').val()) || 0
        };
        
        // Basic validation
        if (!formData.transaction_date) {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Please select a date',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          });
          return;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalBtnText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
        
        // Determine if this is an update or create
        const transactionId = $('#transactionId').val();
        const isUpdate = Boolean(transactionId);
        const method = isUpdate ? 'PUT' : 'POST';
        let url = 'api/beef_transactions_handler.php';
        
        // For updates, include the ID in the URL and add it to formData
        if (isUpdate) {
          url += '?id=' + encodeURIComponent(transactionId);
          formData.id = transactionId; // Ensure ID is included in the request body
        }
        
        console.log(`Submitting ${method} request to:`, url);
        console.log('Request data:', formData);
        
        console.log(`Sending ${method} request to:`, url);
        console.log('Request data:', formData);
        
        $.ajax({
          url: url,
          method: method,
          contentType: 'application/json',
          data: JSON.stringify(formData),
          dataType: 'json',
          success: function(response) {
            console.log('Response received:', response);
            if (response && response.success) {
              Swal.fire({
                icon: 'success',
                title: 'Success',
                text: transactionId ? 'Transaction updated successfully' : 'Transaction added successfully',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
              });
              resetForm();
              loadTransactions();
            } else {
              const errorMsg = response && response.error 
                ? response.error 
                : 'Unknown error occurred';
                
              console.error('Error in response:', errorMsg);
              
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMsg,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
              });
            }
          },
          error: function(xhr, status, error) {
            console.error('AJAX Error:', {
              status: xhr.status,
              statusText: xhr.statusText,
              responseText: xhr.responseText,
              error: error
            });
            
            let errorMessage = 'An error occurred while processing your request.';
            
            try {
              if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
              } else if (xhr.responseText) {
                const errorResponse = JSON.parse(xhr.responseText);
                errorMessage = errorResponse.error || xhr.responseText;
              } else if (xhr.statusText) {
                errorMessage = xhr.statusText;
              }
            } catch (e) {
              console.error('Error parsing error response:', e);
              errorMessage = 'Failed to process server response';
            }
            
            if (xhr.status === 0) {
              errorMessage = 'Network error: Could not connect to the server. Please check your internet connection.';
            } else if (xhr.status === 404) {
              errorMessage = 'Requested resource not found. Please try again.';
            } else if (xhr.status >= 500) {
              errorMessage = 'Server error: Please try again later or contact support.';
            }
            
            Swal.fire({
              icon: 'error',
              title: `Error ${xhr.status || ''}`.trim(),
              text: errorMessage,
              toast: true,
              position: 'top-end',
              showConfirmButton: true,
              timer: 10000
            });
          },
          complete: function() {
            submitBtn.prop('disabled', false).html(originalBtnText);
          }
        });
      });
      
      // Edit transaction
      $(document).on('click', '.edit-btn', function() {
        const transactionId = $(this).data('id');
        
        // Show loading state
        const editBtn = $(this);
        const originalBtnText = editBtn.html();
        editBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        
        $.ajax({
          url: `api/beef_transactions_handler.php?id=${transactionId}`,
          type: 'GET',
          dataType: 'json',
          success: function(response) {
            editBtn.prop('disabled', false).html(originalBtnText);
            
            if (response.success && response.data) {
              const transaction = response.data;
              
              // Populate form with transaction data
              $('#transactionId').val(transaction.id);
              $('#transaction_date').val(transaction.transaction_date);
              $('#buy_price').val(parseFloat(transaction.buy_price).toFixed(2));
              $('#sell_price').val(parseFloat(transaction.sell_price).toFixed(2));
              $('#total_cash_sales').val(parseFloat(transaction.total_cash_sales).toFixed(2));
              $('#daily_expense').val(parseFloat(transaction.daily_expense).toFixed(2));
              
              // Scroll to form
              $('html, body').animate({
                scrollTop: $('#transactionForm').offset().top - 20
              }, 500);
              
              // Recalculate derived values
              calculateDerivedValues();
              
              // Change button text to Update
              $('#transactionForm button[type="submit"]').html('<i class="fas fa-save me-1"></i> Update Transaction');
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.message || 'Failed to load transaction details. Please try again.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
              });
            }
          },
          error: function(xhr, status, error) {
            editBtn.prop('disabled', false).html(originalBtnText);
            console.error('Error loading transaction:', error);
            
            let errorMessage = 'Failed to load transaction details. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
              errorMessage = xhr.responseJSON.message;
            }
            
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: errorMessage,
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 5000
            });
          }
        });
      });
      
      // Delete transaction
      $(document).on('click', '.delete-btn', function() {
        const transactionId = $(this).data('id');
        const transactionDate = $(this).closest('tr').find('td:first').text();
        
        Swal.fire({
          title: 'Delete Transaction',
          html: `Are you sure you want to delete the transaction for <b>${transactionDate}</b>?<br><br>
                 <span class="text-danger">This action cannot be undone!</span>`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel',
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: `api/beef_transactions_handler.php?id=${id}`,
              method: 'DELETE',
              dataType: 'json',
              success: function(response) {
                if (response.success) {
                  showSuccess('Transaction deleted successfully');
                  loadTransactions();
                } else {
                  showError('Failed to delete transaction: ' + (response.error || 'Unknown error'));
                }
              },
              error: function(xhr, status, error) {
                showError('Error deleting transaction: ' + error);
              }
            });
          }
        });
      });
      
      // Handle cancel edit
      $('#cancelEdit').on('click', function() {
        resetForm();
      });
      
      // Handle apply filter
      $('#applyFilter').on('click', function() {
        loadTransactions();
      });
      // Reset form
      function resetForm() {
        $('#transactionForm')[0].reset();
        $('#transactionId').val('');
        $('button[type="submit"]').html('<i class="fas fa-save me-1"></i> Save Transaction');
        $('#cancelEdit').hide();
      }
      
      // Cancel edit and reset form
      $('#cancelEdit').on('click', function() {
        resetForm();
        $('html, body').animate({
          scrollTop: 0
        }, 500);
      });
      
      // Show success message
      function showSuccess(message) {
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: message,
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        });
      }
      
      // Show error message
      function showError(message) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: message,
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 5000
        });
      }
      
      // Set current date as default
      $('#transaction_date').val(formatDate(today));
      
      // Initial load of transactions
      loadTransactions();
    });
  </script>
</body>

</html>