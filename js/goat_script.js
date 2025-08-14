// Beef Transactions JavaScript
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
            <span class="text-primary me-1 edit-btn" data-id="${transaction.id}" title="Edit" style="cursor: pointer; font-size: 1.1em;">
              <i class="fas fa-edit"></i>
            </span>
            <span class="text-danger delete-btn" data-id="${transaction.id}" title="Delete" style="cursor: pointer; font-size: 1.1em;">
              <i class="fas fa-trash"></i>
            </span>
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

  // Load transactions
  function loadTransactions() {
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();

    // Show loading state
    const refreshBtn = $('#refreshTable');
    const originalBtnText = refreshBtn.html();
    refreshBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

    $.ajax({
      url: 'api/goat_transactions_handler.php',
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
      url: 'api/goat_transactions_handler.php',
      method: 'GET',
      data: {
        id: transactionId
      },
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
  }

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
      showError('Please select a date');
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
    let url = 'api/goat_transactions_handler.php';

    // For updates, include the ID in the URL and add it to formData
    if (isUpdate) {
      url += '?id=' + encodeURIComponent(transactionId);
      formData.id = transactionId; // Ensure ID is included in the request body
    }

    console.log(`Submitting ${method} request to:`, url);
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
          showSuccess(transactionId ? 'Transaction updated successfully' : 'Transaction added successfully');
          resetForm();
          loadTransactions();
        } else {
          const errorMsg = response && response.error ? 
            response.error : 
            'An unknown error occurred while ' + (isUpdate ? 'updating' : 'creating') + ' the transaction.';
          showError(errorMsg);
        }
      },
      error: function(xhr, status, error) {
        console.error('Error saving transaction:', error);
        showError('Failed to save transaction. Please try again.');
      },
      complete: function() {
        submitBtn.prop('disabled', false).html(originalBtnText);
      }
    });
  });

  // Handle delete button click
  $(document).on('click', '.delete-btn', function() {
    const transactionId = $(this).data('id');
    if (!transactionId) return;

    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        // Show loading state
        const deleteBtn = $(this);
        const originalBtnText = deleteBtn.html();
        deleteBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

        // Send delete request
        $.ajax({
          url: 'api/goat_transactions_handler.php',
          method: 'DELETE',
          data: JSON.stringify({ id: transactionId }),
          contentType: 'application/json',
          dataType: 'json',
          success: function(response) {
            if (response && response.success) {
              showSuccess('Transaction deleted successfully');
              loadTransactions();
            } else {
              showError(response.error || 'Failed to delete transaction');
            }
          },
          error: function(xhr, status, error) {
            console.error('Error deleting transaction:', error);
            showError('Failed to delete transaction. Please try again.');
          },
          complete: function() {
            deleteBtn.html(originalBtnText);
          }
        });
      }
    });
  });

  // Handle filter button click
  $('#applyFilter').on('click', function() {
    loadTransactions();
  });

  // Handle refresh button click
  $('#refreshTable').on('click', function() {
    loadTransactions();
  });

  // Initial load of transactions
  loadTransactions();
});