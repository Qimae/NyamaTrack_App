document.addEventListener('DOMContentLoaded', function() {  
  // Initialize charts and load data
  loadTransactionData();
});   

async function fetchTransactions(startDate, endDate) {
  try {
    const response = await fetch(`./api/beef_transactions_handler.php?start_date=${formatDate(startDate)}&end_date=${formatDate(endDate)}`, {
      method: 'GET',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json'
      }
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    return data.success ? data.data : [];
  } catch (error) {
    console.error('Error fetching transactions:', error);
    return [];
  }
}



function updateKPIs(currentMonthData, prevMonthData, currentDate) {
  // Calculate current month totals
  const currentMonth = calculateMonthTotals(currentMonthData, currentDate);
  
  // Calculate previous month totals for comparison
  const prevMonth = calculateMonthTotals(prevMonthData);
  
  // Update KPI elements
  document.getElementById('k-sales').textContent = `KES ${formatNumber(currentMonth.totalSales)}`;
  document.getElementById('k-expenses').textContent = `KES ${formatNumber(currentMonth.totalExpenses)}`;
  document.getElementById('k-profit').textContent = `KES ${formatNumber(currentMonth.totalProfit)}`;
  document.getElementById('k-avg-kilos').textContent = `${formatNumber(currentMonth.avgDailyKilos)} kg`;
  
  // Update percentage changes
  updatePercentageChange('sales', currentMonth.totalSales, prevMonth.totalSales);
  updatePercentageChange('expenses', currentMonth.totalExpenses, prevMonth.totalExpenses);
  updatePercentageChange('profit', currentMonth.totalProfit, prevMonth.totalProfit);
}

function calculateMonthTotals(transactions, currentDate = null) {
  const result = {
    totalSales: 0,
    totalExpenses: 0,
    totalProfit: 0,
    totalKilos: 0,
    daysWithData: 0,
    dailyAverages: {}
  };
  
  // Group transactions by day
  const dailyTotals = {};
  
  transactions.forEach(tx => {
    const date = tx.transaction_date.split(' ')[0]; // Extract date part
    if (!dailyTotals[date]) {
      dailyTotals[date] = {
        sales: 0,
        expenses: 0,
        profit: 0,
        kilos: 0,
        count: 0
      };
    }
    
    dailyTotals[date].sales += parseFloat(tx.total_cash_sales || 0);
    dailyTotals[date].expenses += parseFloat(tx.daily_expense || 0);
    dailyTotals[date].profit += (parseFloat(tx.profit) || 0);
    dailyTotals[date].kilos += (parseFloat(tx.total_kilos) || 0);
    dailyTotals[date].count++;
  });
  
  // Calculate totals and averages
  Object.values(dailyTotals).forEach(day => {
    result.totalSales += day.sales;
    result.totalExpenses += day.expenses;
    result.totalProfit += day.profit;
    result.totalKilos += day.kilos;
    result.daysWithData++;
  });
  
  // Calculate daily averages
  if (result.daysWithData > 0) {
    result.avgDailySales = result.totalSales / result.daysWithData;
    result.avgDailyExpenses = result.totalExpenses / result.daysWithData;
    result.avgDailyProfit = result.totalProfit / result.daysWithData;
    result.avgDailyKilos = result.totalKilos / result.daysWithData;
  }
  
  // If we have a current date, project the rest of the month
  if (currentDate) {
    const currentDay = currentDate.getDate();
    const daysInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
    const remainingDays = daysInMonth - currentDay;
    
    // Only project if we have some data
    if (result.daysWithData > 0) {
      const projectionFactor = daysInMonth / currentDay;
      
      result.projectedSales = result.totalSales * projectionFactor;
      result.projectedExpenses = result.totalExpenses * projectionFactor;
      result.projectedProfit = result.totalProfit * projectionFactor;
      result.projectedKilos = result.totalKilos * projectionFactor;
    }
  }
  
  return result;
}

function updatePercentageChange(metric, currentValue, previousValue) {
  const element = document.getElementById(`k-${metric}-ch`);
  if (!element) return;
  
  if (previousValue === 0 || isNaN(previousValue)) {
    element.textContent = 'N/A';
    return;
  }
  
  const change = ((currentValue - previousValue) / previousValue) * 100;
  const isPositive = change >= 0;
  const changeText = `${isPositive ? '+' : ''}${change.toFixed(1)}% vs last month`;
  
  element.textContent = changeText;
  element.className = isPositive ? 'positive' : 'negative';
}

async function loadTransactionData() {
  try {
    // Get current date and first day of current month
    const now = new Date();
    const firstDayOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
    const lastDayOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    
    // Get first day of previous month
    const firstDayPrevMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
    const lastDayPrevMonth = new Date(now.getFullYear(), now.getMonth(), 0);

    // Fetch transactions for current and previous month
    const [currentMonthData, prevMonthData] = await Promise.all([
      fetchTransactions(firstDayOfMonth, lastDayOfMonth),
      fetchTransactions(firstDayPrevMonth, lastDayPrevMonth)
    ]);

    // Update KPIs
    updateKPIs(currentMonthData, prevMonthData, now);
    
    // Update transactions table
    updateTransactionsTable(currentMonthData);
    
    // Update charts
    updateCharts(currentMonthData, prevMonthData, now);
    
  } catch (error) {
    console.error('Error loading transaction data:', error);
    alert('Failed to load transaction data. Please try again.');
  }
}

function updateTransactionsTable(transactions) {
  const tbody = document.getElementById('transactions-body');
  if (!tbody) return;
  
  // Clear existing rows
  tbody.innerHTML = '';
  
  // Update transaction count
  document.getElementById('tx-count').textContent = `${transactions.length} transactions`;
  
  // Sort transactions by date (newest first)
  const sortedTransactions = [...transactions].sort((a, b) => 
    new Date(b.transaction_date) - new Date(a.transaction_date)
  );
  
  // Add rows to table
  sortedTransactions.slice(0, 10).forEach(tx => { // Show only last 10 transactions
    const row = document.createElement('tr');
    
    // Format date
    const txDate = new Date(tx.transaction_date);
    const formattedDate = txDate.toLocaleDateString('en-US', { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric' 
    });
    
    // Calculate values
    const buyPrice = parseFloat(tx.buy_price || 0);
    const sellPrice = parseFloat(tx.sell_price || 0);
    const totalSales = parseFloat(tx.total_cash_sales || 0);
    const expenses = parseFloat(tx.daily_expense || 0);
    const profit = parseFloat(tx.profit || 0);
    const kilos = parseFloat(tx.total_kilos || 0);
    
    row.innerHTML = `
      <td>${formattedDate}</td>
      <td>KES ${formatNumber(buyPrice)}</td>
      <td>KES ${formatNumber(sellPrice)}</td>
      <td>KES ${formatNumber(totalSales)}</td>
      <td>KES ${formatNumber(expenses)}</td>
      <td>${formatNumber(kilos)} kg</td>
      <td class="${profit >= 0 ? 'positive' : 'negative'}">KES ${formatNumber(profit)}</td>
    `;
    
    tbody.appendChild(row);
  });
}

function updateCharts(currentMonthData, prevMonthData, currentDate) {
  try {
    // Prepare data for charts
    const currentMonth = calculateMonthTotals(currentMonthData, currentDate);
    const prevMonth = calculateMonthTotals(prevMonthData);
    
    // Get chart canvas and context
    const chartCanvas = document.getElementById('barChart');
    if (!chartCanvas) {
      console.error('Chart canvas not found');
      return;
    }
    
    const barCtx = chartCanvas.getContext('2d');
    if (!barCtx) {
      console.error('Could not get 2D context for chart');
      return;
    }
    
    // Destroy existing chart if it exists
    if (window.barChart instanceof Chart) {
      window.barChart.destroy();
      window.barChart = null;
    }
    
    // Calculate projection if available
    const hasProjection = currentMonth.projectedSales !== undefined;
    const currentMonthLabel = currentDate.toLocaleString('default', { month: 'long' });
    const prevMonthLabel = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1).toLocaleString('default', { month: 'long' });
    
    // Prepare chart data
    const chartData = {
  
      labels: ['Sales', 'Expenses', 'Profit'],
      datasets: [
        {
          label: `${prevMonthLabel} (Actual)`,
          data: [
            prevMonth.totalSales || 0,
            prevMonth.totalExpenses || 0,
            prevMonth.totalProfit || 0
          ],
          backgroundColor: 'rgba(99, 102, 241, 0.5)',
          borderColor: 'rgba(99, 102, 241, 1)',
          borderWidth: 1
        },
        {
          label: `${currentMonthLabel} (To Date)`,
          data: [
            currentMonth.totalSales || 0,
            currentMonth.totalExpenses || 0,
            currentMonth.totalProfit || 0
          ],
          backgroundColor: 'rgba(16, 185, 129, 0.5)',
          borderColor: 'rgba(16, 185, 129, 1)',
          borderWidth: 1
        }
      ]
    };
    
    // Add projection data if available
    if (hasProjection) {
      chartData.datasets.push({
        label: `${currentMonthLabel} (Projected)`,
        data: [
          currentMonth.projectedSales,
          currentMonth.projectedExpenses,
          currentMonth.projectedProfit
        ],
        backgroundColor: 'rgba(16, 185, 129, 0.2)',
        borderColor: 'rgba(16, 185, 129, 0.5)',
        borderWidth: 1,
        borderDash: [5, 5]
      });
    }
    
    // Create new chart
    window.barChart = new Chart(barCtx, {
      type: 'bar',
      data: chartData,
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'KES ' + formatNumber(value);
            }
          }
        }
      },
      plugins: {
        tooltip: {
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': KES ' + formatNumber(context.raw);
            }
          }
        }
      }
    }
  });
  
  } catch (error) {
    console.error('Error updating charts:', error);
    const chartContainer = document.getElementById('barChart')?.parentElement;
    if (chartContainer) {
      chartContainer.innerHTML = '<p class="error">Unable to load chart. Please try again later.</p>';
    }
  }
}

// Helper function to format dates as YYYY-MM-DD
function formatDate(date) {
  if (!(date instanceof Date)) {
    date = new Date(date);
  }
  
  // Handle invalid dates
  if (isNaN(date.getTime())) {
    console.error('Invalid date provided to formatDate');
    return '';
  }
  
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  
  return `${year}-${month}-${day}`;
}

// Helper function to format numbers with commas
function formatNumber(num) {
  if (typeof num !== 'number') return '0';
  return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}