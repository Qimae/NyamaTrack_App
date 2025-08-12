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
  return 'KES ' + formatNumber(amount);
}

// Calculate percentage change
function calculatePercentageChange(current, previous) {
  if (previous === 0) return 0;
  return ((current - previous) / previous) * 100;
}

// Update KPI cards
function updateKPICards(data) {
  // Update total sales
  document.querySelector('#card h2').textContent = formatCurrency(data.total_sales);

  // Update total expenses
  document.querySelectorAll('#card')[1].querySelector('h2').textContent = formatCurrency(data.total_expenses);

  // Update total profit
  const profitCard = document.querySelectorAll('#card')[2];
  const profitElement = profitCard.querySelector('h2');
  const profitChangeElement = profitCard.querySelector('.text-white');

  profitElement.textContent = formatCurrency(data.total_profit);

  // Update profit trend
  const profitChange = calculatePercentageChange(data.total_profit, data.total_profit / 2); // Simplified example
  const isProfitPositive = profitChange >= 0;

  profitCard.querySelector('i').className = isProfitPositive ? 'fas fa-arrow-up text-success' : 'fas fa-arrow-down text-danger';
  profitChangeElement.innerHTML = `
        <span class="${isProfitPositive ? 'text-success' : 'text-danger'}">
          ${isProfitPositive ? '+' : ''}${profitChange.toFixed(1)}%
        </span> from last period
      `;

  // Update total kilos
  document.querySelectorAll('#card')[3].querySelector('h2').textContent = formatNumber(data.total_kilos);
}

// Initialize the chart with data
function initChart(chartData) {
  try {
    console.log('Initializing chart with data:', chartData);

    const canvas = document.getElementById('financialChart');
    if (!canvas) {
      console.error('Chart canvas element not found');
      return;
    }

    const ctx = canvas.getContext('2d');
    if (!ctx) {
      console.error('Could not get 2D context for canvas');
      return;
    }

    // Safely destroy existing chart if it exists
    if (window.financialChart) {
      try {
        if (typeof window.financialChart.destroy === 'function') {
          window.financialChart.destroy();
        }
      } catch (e) {
        console.error('Error destroying existing chart:', e);
      }
      window.financialChart = null;
    }

    // Ensure we have valid data
    if (!chartData || !chartData.labels || !chartData.sales || !chartData.expenses || !chartData.profit) {
      console.error('Invalid chart data format:', chartData);
      return;
    }

    const config = {
      type: 'line',
      data: {
        labels: Array.isArray(chartData.labels) ? chartData.labels : [],
        datasets: [{
          label: 'Sales',
          data: Array.isArray(chartData.sales) ? chartData.sales.map(Number) : [],
          borderColor: 'rgba(75, 192, 192, 1)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderWidth: 2,
          tension: 0.4,
          fill: true
        }, {
          label: 'Expenses',
          data: Array.isArray(chartData.expenses) ? chartData.expenses.map(Number) : [],
          borderColor: 'rgba(255, 99, 132, 1)',
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          borderWidth: 2,
          tension: 0.4,
          fill: true
        }, {
          label: 'Profit',
          data: Array.isArray(chartData.profit) ? chartData.profit.map(Number) : [],
          borderColor: 'rgba(54, 162, 235, 1)',
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          borderWidth: 2,
          tension: 0.4,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              color: 'rgba(255, 255, 255, 0.7)'
            }
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                return context.dataset.label + ': ' + formatCurrency(context.raw);
              }
            }
          }
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
              font: { size: 14 },
              callback: function (value) {
                return 'KES ' + value.toLocaleString();
              }
            }
          }
        }
      }
    };

    // Create and store the chart instance
    window.financialChart = new Chart(ctx, config);

    return true;
  } catch (error) {
    console.error('Error in initChart:', error);
    throw error;
  }
}

// Show error message to user
function showError(message) {
  console.error('Showing error:', message);
  const errorDisplay = document.getElementById('error-display');
  if (errorDisplay) {
    errorDisplay.textContent = `Error: ${message || 'An error occurred'}`;
    errorDisplay.style.display = 'block';

    // Auto-hide error after 5 seconds
    setTimeout(() => {
      if (errorDisplay) {
        errorDisplay.style.display = 'none';
      }
    }, 5000);
  } else {
    console.error('Error display element not found');
    alert(`Error: ${message || 'An error occurred'}`);
  }
}

// Handle fetch errors
function handleFetchError(error, context = 'fetching dashboard data') {
  const errorMessage = error.message || `Error ${context}`;
  console.error(`Error ${context}:`, error);
  showError(errorMessage);
}

// Fetch dashboard data
async function fetchDashboardData() {
  const loadingElement = document.getElementById('loading');

  try {
    console.log('Fetching dashboard data...');

    // Ensure error display exists
    ensureErrorDisplay();

    // Show loading state
    if (loadingElement) {
      loadingElement.style.display = 'flex';
      loadingElement.style.visibility = 'visible';
      console.log('Loading overlay shown');
    } else {
      console.error('Loading element not found');
    }

    const errorDisplay = document.getElementById('error-display');
    if (errorDisplay) {
      errorDisplay.style.display = 'none';
    }

    const response = await fetch('api/dashboard_handler.php');
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result = await response.json();
    console.log('Dashboard data received:', result);

    if (result.success && result.data) {
      const data = result.data;

      // Update KPI cards
      updateKPICards(data);

      // Update chart if we have chart data
      if (data.chart_data) {
        console.log('Updating chart with data:', data.chart_data);
        try {
          initChart(data.chart_data);
        } catch (chartError) {
          console.error('Error initializing chart:', chartError);
          showError('Error displaying chart data');
        }
      } else {
        console.warn('No chart data available');
      }

      // Update revenue growth if we have the data
      const growthElement = document.querySelector('.text-end h3');
      const growthText = document.querySelector('.text-end p');

      if (growthElement && growthText && data.daily_averages && data.daily_averages.profit !== undefined) {
        try {
          const growth = calculatePercentageChange(
            data.daily_averages.profit * 30,
            (data.daily_averages.profit * 30) / 1.25 // Example: 25% growth
          );

          const isGrowthPositive = growth >= 0;
          growthElement.className = `mb-0 ${isGrowthPositive ? 'text-success' : 'text-danger'}`;
          growthElement.innerHTML = `${isGrowthPositive ? '+' : ''}${Math.abs(growth).toFixed(0)}%`;
          growthText.textContent = isGrowthPositive ? 'Revenue Growth (YoY)' : 'Revenue Decline (YoY)';
        } catch (growthError) {
          console.error('Error calculating growth:', growthError);
        }
      }
    } else {
      const errorMsg = result.error || 'Unknown error occurred';
      console.error('Failed to load dashboard data:', errorMsg);
      throw new Error(errorMsg);
    }
  } catch (error) {
    handleFetchError(error, 'fetching dashboard data');
  } finally {
    // Hide loading state
    console.log('Hiding loading overlay...');
    if (loadingElement) {
      loadingElement.style.display = 'none';
      loadingElement.style.visibility = 'hidden';
      console.log('Loading overlay hidden');
    } else {
      console.error('Could not hide loading: element not found');
    }
  }
}

// Initialize the dashboard when the document is ready
document.addEventListener('DOMContentLoaded', function () {
  // Ensure error display is set up
  ensureErrorDisplay();

  // Load dashboard data
  fetchDashboardData();

  // Set up refresh button
  const refreshBtn = document.getElementById('refresh-dashboard');
  if (refreshBtn) {
    refreshBtn.addEventListener('click', fetchDashboardData);
  }
});

// Add error display element if it doesn't exist
function ensureErrorDisplay() {
  if (!document.getElementById('error-display') && document.body) {
    const errorDisplay = document.createElement('div');
    errorDisplay.id = 'error-display';
    errorDisplay.style.display = 'none';
    errorDisplay.style.position = 'fixed';
    errorDisplay.style.bottom = '20px';
    errorDisplay.style.left = '50%';
    errorDisplay.style.transform = 'translateX(-50%)';
    errorDisplay.style.backgroundColor = '#dc3545';
    errorDisplay.style.color = 'white';
    errorDisplay.style.padding = '10px 20px';
    errorDisplay.style.borderRadius = '4px';
    errorDisplay.style.zIndex = '9999';
    document.body.appendChild(errorDisplay);
  }
}