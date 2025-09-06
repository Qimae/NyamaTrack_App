// Format number with commas
function formatNumber(num) {
  if (typeof num !== 'number') return '0';
  return num.toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
}

// Format currency
function formatCurrency(amount) {
  return 'Ksh ' + formatNumber(amount);
}

// Process report data from API
function processReportData(apiResponse) {
  const { beef, goat, summary } = apiResponse.data;
  
  // Prepare result object
  const result = {
    totalSales: parseFloat(summary.total_sales) || 0,
    totalExpenses: parseFloat(summary.total_expenses) || 0,
    totalProfit: parseFloat(summary.total_profit) || 0,
    totalKilos: parseFloat(summary.total_kilos) || 0,
    chartData: {
      labels: [],
      sales: []
    },
    tableData: {}
  };
  
  // Combine and group transactions by date
  const allTransactions = [
    ...(beef || []).map(tx => ({ ...tx, type: 'beef' })),
    ...(goat || []).map(tx => ({ ...tx, type: 'goat' }))
  ];
  
  const dailyTotals = {};
  
  // Process each transaction
  allTransactions.forEach(tx => {
    const date = new Date(tx.transaction_date);
    const dateKey = date.toISOString().split('T')[0];
    
    // Initialize day if not exists
    if (!dailyTotals[dateKey]) {
      dailyTotals[dateKey] = {
        date: date,
        sales: 0,
        expenses: 0,
        profit: 0,
        kilos: 0
      };
    }
    
    // Add transaction to daily totals
    const day = dailyTotals[dateKey];
    const sales = parseFloat(tx.total_cash_sales || 0);
    const expense = parseFloat(tx.daily_expense || 0);
    
    day.sales += sales;
    day.expenses += expense;
  });
  
  // Prepare chart data (last 30 days)
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  
  for (let i = 29; i >= 0; i--) {
    const date = new Date(today);
    date.setDate(today.getDate() - i);
    
    const dateKey = date.toISOString().split('T')[0];
    const day = date.getDate();
    const month = date.getMonth() + 1;
    
    result.chartData.labels.push(`${day}/${month}`);
    
    if (dailyTotals[dateKey]) {
      result.chartData.sales.push(dailyTotals[dateKey].sales);
    } else {
      result.chartData.sales.push(0);
    }
  }
  
  // Prepare table data
  result.tableData = {
    total_sales: {
      value: formatCurrency(result.totalSales),
      change: '0.0%',
      trend: 'up'
    },
    total_expenses: {
      value: formatCurrency(result.totalExpenses),
      change: '0.0%',
      trend: 'up'
    },
    total_profit: {
      value: formatCurrency(result.totalProfit),
      change: '0.0%',
      trend: result.totalProfit >= 0 ? 'up' : 'down'
    },
    total_kilos: {
      value: result.totalKilos.toFixed(2) + ' kg',
      change: '0.0%',
      trend: 'up'
    }
  };
  
  return result;
}


// Format percentage
function formatPercentage(value) {
  // Convert to number if it's a string
  const numValue = typeof value === 'string' ? parseFloat(value) : Number(value);
  
  // Check if the value is a valid number
  if (isNaN(numValue)) return '0.00%';
  if (numValue === 0) return '0.00%';
  
  const prefix = numValue > 0 ? '+' : '';
  return `${prefix}${numValue.toFixed(2)}%`;
}

// Show loading state
function showLoading() {
  const loadingElement = document.getElementById('loading');
  if (loadingElement) {
    loadingElement.style.display = 'flex';
    loadingElement.style.visibility = 'visible';
  }
}

// Hide loading state
function hideLoading() {
  const loadingElement = document.getElementById('loading');
  if (loadingElement) {
    loadingElement.style.display = 'none';
    loadingElement.style.visibility = 'hidden';
  }
}

// Show error message
function showError(message) {
  const errorElement = document.getElementById('error-message');
  if (errorElement) {
    errorElement.textContent = message;
    errorElement.style.display = 'block';
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
      errorElement.style.display = 'none';
    }, 5000);
  } else {
    alert(message); // Fallback if error element doesn't exist
  }
}

// Update the reports table with data
function updateReportsTable(data) {
  console.log('Updating table with data:', JSON.parse(JSON.stringify(data)));
  if (!data) {
    console.error('No data provided to updateReportsTable');
    return;
  }
  
  const table = document.querySelector('#reports-table');
  if (!table) {
    console.error('Reports table not found');
    return;
  }
  
  // Get or create tbody
  let tbody = table.querySelector('tbody');
  if (!tbody) {
    console.log('Creating new tbody element');
    tbody = document.createElement('tbody');
    table.appendChild(tbody);
  }
  
  // Clear existing rows
  tbody.innerHTML = '';
  
  // Helper function to create a table row
  const createTableRow = (label, value, change) => {
    console.log(`Creating row for ${label}:`, { value, change });
    
    const row = document.createElement('tr');
    
    // Label cell
    const labelCell = document.createElement('td');
    labelCell.textContent = label;
    
    // Value cell
    const valueCell = document.createElement('td');
    valueCell.className = 'text-end';
    
    // Debug log the value before formatting
    console.log(`Formatting value for ${label}:`, value);
    
    // Use the pre-formatted value from our data structure
    const displayValue = typeof value === 'object' ? value.formattedValue : value;
    valueCell.textContent = displayValue;
    
    // Change cell
    const changeCell = document.createElement('td');
    changeCell.className = 'text-end';
    
    if (change !== null && change !== undefined) {
      console.log(`Processing change for ${label}:`, change);
      const isPositive = parseFloat(change) >= 0;
      changeCell.className += ` text-${isPositive ? 'success' : 'danger'}`;
      
      const icon = document.createElement('i');
      icon.className = `fas fa-arrow-${isPositive ? 'up' : 'down'}`;
      
      const formattedChange = formatPercentage(change);
      console.log(`Formatted change for ${label}:`, formattedChange);
      
      changeCell.innerHTML = `${formattedChange} `;
      changeCell.appendChild(icon);
    } else {
      console.log(`No change value for ${label}, using dash`);
      changeCell.textContent = '-';
    }
    
    // Append cells to row
    row.appendChild(labelCell);
    row.appendChild(valueCell);
    row.appendChild(changeCell);
    
    return row;
  };
  
  // Add rows for each metric
  console.log('Creating table rows with data:', {
    sales: data.total_sales,
    expenses: data.total_expenses,
    profit: data.total_profit,
    kilos: data.total_kilos
  });
  
  try {
    // Clear existing rows
    tbody.innerHTML = '';
    
    // Helper function to add a row
    const addRow = (label, value, change) => {
      const row = document.createElement('tr');
      
      // Label cell
      const labelCell = document.createElement('td');
      labelCell.textContent = label;
      
      // Value cell
      const valueCell = document.createElement('td');
      valueCell.className = 'text-end';
      valueCell.textContent = value;
      
      // Change cell
      const changeCell = document.createElement('td');
      changeCell.className = 'text-end';
      
      if (change) {
        const isPositive = !change.startsWith('-');
        changeCell.className += ` text-${isPositive ? 'success' : 'danger'}`;
        
        const icon = document.createElement('i');
        icon.className = `fas fa-arrow-${isPositive ? 'up' : 'down'}`;
        
        changeCell.innerHTML = `${change} `;
        changeCell.appendChild(icon);
      } else {
        changeCell.textContent = '-';
      }
      
      // Append cells to row
      row.appendChild(labelCell);
      row.appendChild(valueCell);
      row.appendChild(changeCell);
      
      // Add row to table
      tbody.appendChild(row);
    };
    
    // Add rows directly
    addRow('Total Sales', data.total_sales.formattedValue, data.total_sales.change);
    addRow('Total Expenses', data.total_expenses.formattedValue, data.total_expenses.change);
    addRow('Total Profit', data.total_profit.formattedValue, data.total_profit.change);
    addRow('Total Kilos', data.total_kilos.formattedValue, data.total_kilos.change);
    
    console.log('Table rows added successfully');
  } catch (error) {
    console.error('Error adding table rows:', error);
  }
}

// Format date as YYYY-MM-DD
function formatDate(date) {
  if (!(date instanceof Date)) {
    date = new Date(date);
  }
  return date.toISOString().split('T')[0];
}

// Get default date range (last 30 days)
function getDefaultDateRange() {
  const endDate = new Date();
  const startDate = new Date();
  startDate.setDate(endDate.getDate() - 30);
  return { startDate, endDate };
}

// Fetch reports data from the API
async function fetchReportsData(startDate, endDate) {
  showLoading();
  
  try {
    console.log('Fetching reports data...');
    
    // Use provided dates or default to last 30 days
    const dates = startDate && endDate 
      ? { startDate: new Date(startDate), endDate: new Date(endDate) }
      : getDefaultDateRange();
    
    // Format dates for API
    const startDateStr = formatDate(dates.startDate);
    const endDateStr = formatDate(dates.endDate);
    
    const apiUrl = `/NyamaTrack_App/butcheries/api/reports_handler.php?start_date=${startDateStr}&end_date=${endDateStr}`;
    console.log('Making request to:', apiUrl);
    
    // Fetch transactions data
    const response = await fetch(apiUrl, {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'Accept': 'application/json',
        'Cache-Control': 'no-cache',
        'Pragma': 'no-cache'
      }
    });
    
    console.log('Response status:', response.status);
    
    if (!response.ok) {
      const errorText = await response.text();
      console.error('API Error:', errorText);
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result = await response.json();
    console.log('API Response:', result);
    
    if (!result.success) {
      throw new Error(result.error || 'Failed to fetch report data');
    }
    
    // Process the response data
    const reportData = processReportData(result);
    
    // Log the data being passed to the table
    console.log('Table data to be rendered:', {
      total_sales: reportData.totalSales,
      total_expenses: reportData.totalExpenses,
      total_profit: reportData.totalProfit,
      total_kilos: reportData.totalKilos
    });
    
    // Check if we have any data
    if (reportData.totalSales === 0 && reportData.totalExpenses === 0) {
      console.warn('No transaction data found');
      showError('No transaction data found for the selected period');
      return;
    }
    
    // Prepare table data using the API summary
    const tableData = {
      total_sales: {
        value: reportData.totalSales,
        formattedValue: formatCurrency(reportData.totalSales),
        change: '0.0%',
        trend: 'up',
        isCurrency: true
      },
      total_expenses: {
        value: reportData.totalExpenses,
        formattedValue: formatCurrency(reportData.totalExpenses),
        change: '0.0%',
        trend: 'up',
        isCurrency: true
      },
      total_profit: {
        value: reportData.totalProfit,
        formattedValue: formatCurrency(reportData.totalProfit),
        change: '0.0%',
        trend: reportData.totalProfit >= 0 ? 'up' : 'down',
        isCurrency: true
      },
      total_kilos: {
        value: reportData.totalKilos,
        formattedValue: formatNumber(reportData.totalKilos) + ' kg',
        change: '0.0%',
        trend: 'up',
        isCurrency: false
      }
    };
    
    // Debug: Log the table data before updating the UI
    console.log('Table data to be rendered:', JSON.stringify(tableData, null, 2));
    console.log('Data types:', {
      total_sales: typeof tableData.total_sales.value,
      total_expenses: typeof tableData.total_expenses.value,
      total_profit: typeof tableData.total_profit.value,
      total_kilos: typeof tableData.total_kilos.value
    });
    
    // Update the UI with the processed data
    updateReportsTable(tableData);
    
    // Update the chart if we have chart data
    if (reportData.chartData && reportData.chartData.labels && reportData.chartData.sales) {
      updateChart(reportData.chartData.labels, reportData.chartData.sales);
    }
    
  } catch (error) {
    console.error('Error in fetchReportsData:', error);
    showError('Error loading reports: ' + (error.message || 'Unknown error'));
  } finally {
    hideLoading();
  }
}

// Chart instance variable
let salesChart = null;

// Function to update the chart with real data
function updateChart(labels, salesData) {
  const ctx = document.getElementById('salesChart').getContext('2d');
  
  // Destroy existing chart if it exists
  if (salesChart) {
    salesChart.destroy();
  }
  
  // Create new chart
  salesChart = new Chart(ctx, {
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
}

// Function to handle date filter form submission
function setupDateFilter() {
  const dateFilterForm = document.getElementById('dateFilterForm');
  if (!dateFilterForm) return;
  
  const startDateInput = document.getElementById('startDate');
  const endDateInput = document.getElementById('endDate');
  const applyFilterBtn = dateFilterForm.querySelector('button[type="button"]');
  
  if (!startDateInput || !endDateInput || !applyFilterBtn) return;
  
  applyFilterBtn.addEventListener('click', function(e) {
    e.preventDefault();
    
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;
    
    if (!startDate || !endDate) {
      alert('Please select both start and end dates');
      return;
    }
    
    if (new Date(startDate) > new Date(endDate)) {
      alert('Start date cannot be after end date');
      return;
    }
    
    // Fetch data with selected date range
    fetchReportsData(startDate, endDate);
  });
}

// Initialize the reports page
document.addEventListener('DOMContentLoaded', function() {
  // Set up date filter
  setupDateFilter();
  
  // Add loading overlay if it doesn't exist
  if (!document.getElementById('loading')) {
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'loading';
    loadingDiv.className = 'position-fixed w-100 h-100 bg-dark bg-opacity-75 d-flex justify-content-center align-items-center';
    loadingDiv.style.zIndex = '9999';
    loadingDiv.style.top = '0';
    loadingDiv.style.left = '0';
    loadingDiv.style.display = 'none';
    loadingDiv.innerHTML = `
      <div class="text-center text-white">
        <div class="spinner-border mb-3" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <h4>Loading Reports Data...</h4>
      </div>
    `;
    document.body.appendChild(loadingDiv);
  }
  
  // Add error message element if it doesn't exist
  if (!document.getElementById('error-message')) {
    const errorDiv = document.createElement('div');
    errorDiv.id = 'error-message';
    errorDiv.className = 'alert alert-danger';
    errorDiv.style.display = 'none';
    errorDiv.style.position = 'fixed';
    errorDiv.style.bottom = '20px';
    errorDiv.style.left = '50%';
    errorDiv.style.transform = 'translateX(-50%)';
    errorDiv.style.zIndex = '9999';
    document.body.appendChild(errorDiv);
  }
  
  // Set up refresh button
  const refreshBtn = document.getElementById('refresh-reports');
  if (refreshBtn) {
    refreshBtn.addEventListener('click', function() {
      const startDateInput = document.getElementById('startDate');
      const endDateInput = document.getElementById('endDate');
      
      if (startDateInput && endDateInput) {
        fetchReportsData(startDateInput.value, endDateInput.value);
      } else {
        fetchReportsData();
      }
    });
  }
  
  // Initial data load with current form values or default range
  const startDateInput = document.getElementById('startDate');
  const endDateInput = document.getElementById('endDate');
  
  if (startDateInput && endDateInput && startDateInput.value && endDateInput.value) {
    fetchReportsData(startDateInput.value, endDateInput.value);
  } else {
    fetchReportsData();
  }
});
