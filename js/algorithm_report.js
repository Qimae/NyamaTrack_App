document.addEventListener('DOMContentLoaded', function() {
    // Initialize date picker with today's date
    const dateInput = document.getElementById('date');
    dateInput.valueAsDate = new Date();
    
    // Initialize chart
    const ctx = document.getElementById('barChart').getContext('2d');
    let chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Sales (KES)',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Expenses (KES)',
                    data: [],
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.4,
                    fill: false
                },
                {
                    label: 'Profit (KES)',
                    data: [],
                    borderColor: 'rgb(54, 162, 235)',
                    tension: 0.4,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'KES ' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Load data when page loads
    loadData();

    // Add event listeners
    document.getElementById('apply').addEventListener('click', loadData);
    document.getElementById('export').addEventListener('click', exportToCSV);
    document.getElementById('print').addEventListener('click', window.print);

    // Set current year in footer
    document.getElementById('year').textContent = new Date().getFullYear();
});

async function loadData() {
    try {
        // Show loading state
        const applyBtn = document.getElementById('apply');
        const originalText = applyBtn.textContent;
        applyBtn.disabled = true;
        applyBtn.innerHTML = '<div class="spinner"></div> Loading...';

        // Get filter values
        const date = document.getElementById('date').value;
        const outlet = document.getElementById('outlet').value;
        const category = document.getElementById('category').value;

        // Build query string
        const params = new URLSearchParams({
            date,
            outlet,
            category
        });

        // Fetch data from API
        const response = await fetch(`/NyamaTrack_App/api/algorithm_report_handler.php?${params}`);
        const data = await response.json();

        if (data.status !== 'success') {
            throw new Error(data.message || 'Failed to load data');
        }

        // Update UI with data
        updateSummary(data.data.summary);
        updateChart(chart, data.data.chart_data);
        updateTransactionsTable(data.data.historical, data.data.projections);

    } catch (error) {
        console.error('Error loading data:', error);
        alert('Failed to load data: ' + error.message);
    } finally {
        // Reset button state
        const applyBtn = document.getElementById('apply');
        applyBtn.disabled = false;
        applyBtn.textContent = 'Apply';
    }
}

function updateSummary(summary) {
    // Update KPI cards
    document.getElementById('k-sales').textContent = 'KES ' + Math.round(summary.total_sales).toLocaleString();
    document.getElementById('k-expenses').textContent = 'KES ' + Math.round(summary.total_expenses).toLocaleString();
    document.getElementById('k-profit').textContent = 'KES ' + Math.round(summary.total_profit).toLocaleString();
    document.getElementById('k-inventory').textContent = Math.round(summary.avg_daily_profit / 1000) + ' kg'; // Simplified inventory estimate
    
    // Update change indicators
    document.getElementById('k-sales-ch').textContent = 'Last 30 days';
    document.getElementById('k-expenses-ch').textContent = 'Last 30 days';
    document.getElementById('k-profit-ch').textContent = 'Last 30 days';
}

function updateChart(chart, chartData) {
    // Update chart data
    chart.data.labels = chartData.labels;
    chart.data.datasets[0].data = chartData.sales;
    chart.data.datasets[1].data = chartData.expenses;
    chart.data.datasets[2].data = chartData.profit;
    
    // Update chart range display
    if (chartData.labels.length > 0) {
        const startDate = new Date(chartData.labels[0]).toLocaleDateString();
        const endDate = new Date(chartData.labels[chartData.labels.length - 1]).toLocaleDateString();
        document.getElementById('chart-range').textContent = `${startDate} - ${endDate}`;
    }
    
    chart.update();
}

function updateTransactionsTable(historical, projections) {
    const tbody = document.querySelector('table tbody');
    tbody.innerHTML = ''; // Clear existing rows
    
    // Combine historical and projected data
    const allData = [...historical, ...projections];
    
    allData.forEach(item => {
        const row = document.createElement('tr');
        if (item.is_projection) {
            row.classList.add('projected');
        }
        
        row.innerHTML = `
            <td>${item.transaction_date || item.date} ${item.is_projection ? '<span class="pill">Projected</span>' : ''}</td>
            <td>${formatCurrency(item.buy_price || 0)}</td>
            <td>${formatCurrency(item.sell_price || 0)}</td>
            <td>${formatCurrency(item.total_cash_sales || item.projected_sales || 0)}</td>
            <td>${formatCurrency(item.daily_expense || item.projected_expenses || 0)}</td>
            <td>${formatCurrency(item.total_cash || ((item.total_cash_sales || item.projected_sales || 0) + (item.daily_expense || item.projected_expenses || 0)))}</td>
            <td>${formatNumber(item.total_kilos || 0)} kg</td>
            <td>${formatCurrency((item.sell_price || 0) - (item.buy_price || 0))}</td>
            <td>${formatCurrency(item.profit || item.projected_profit || 0)}</td>
            <td>${item.is_projection ? '—' : '<button class="btn ghost" onclick="viewDetails(${item.id})">Details</button>'}</td>
        `;
        
        tbody.appendChild(row);
    });
    
    // Update transaction count
    document.getElementById('tx-count').textContent = `${allData.length} transactions (${projections.length} projected)`;
}

function formatCurrency(amount) {
    return 'KES ' + parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function formatNumber(num) {
    return parseFloat(num).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function exportToCSV() {
    // This is a simplified version - in a real app, you'd generate a proper CSV
    const table = document.querySelector('table');
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cols = rows[i].querySelectorAll('td, th');
        
        for (let j = 0; j < cols.length; j++) {
            // Remove any HTML tags and trim whitespace
            let text = cols[j].textContent.replace(/<[^>]*>/g, '').trim();
            // Escape quotes and wrap in quotes if contains comma
            if (text.includes(',')) {
                text = `"${text}"`;
            }
            row.push(text);
        }
        
        csv.push(row.join(','));
    }
    
    // Download the CSV file
    const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `nyamatrack_report_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function viewDetails(transactionId) {
    // In a real app, this would show a detailed view of the transaction
    alert('Viewing details for transaction #' + transactionId);
}
