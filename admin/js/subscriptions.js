// Function to load data via AJAX
function loadSubscriptionData() {
    $.ajax({
        url: 'api/subscriptions_handler.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                // Update stats
                $('[data-stat="total-revenue"]').text('KSh ' + parseFloat(data.total_revenue || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('[data-stat="paid-count"]').text(data.paid_count || 0);
                $('[data-stat="success-rate"]').text((data.success_rate || 0) + '%');
                
                // Update transactions table
                updateTransactionsTable(data.transactions || []);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading subscription data:', error);
        }
    });
}

// Function to update transactions table
function updateTransactionsTable(transactions) {
    const tbody = $('#transactions-table');
    tbody.empty();
    
    if (!transactions || transactions.length === 0) {
        tbody.append('<tr><td colspan="7" class="py-4 text-center text-gray-500">No transactions found</td></tr>');
        return;
    }
    
    transactions.forEach(function(transaction) {
        const statusClass = transaction.ResultCode == 0 ? 'status-paid' : 'status-failed';
        const statusText = transaction.ResultCode == 0 ? 'Paid' : 'Failed';
        const statusIcon = transaction.ResultCode == 0 ? 
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
        
        const phoneNumber = transaction.PhoneNumber ? '+254' + transaction.PhoneNumber.slice(-9) : 'N/A';
        const receiptNumber = transaction.MpesaReceiptNumber || 'N/A';
        const createdAt = transaction.created_at ? new Date(transaction.created_at).toLocaleString() : 'N/A';
        
        const row = `
            <tr class="border-b border-gray-800 hover:bg-gray-800/30">
                <td class="py-4 px-4 sm:px-6 font-medium text-white">#${transaction.id}</td>
                <td class="hidden sm:table-cell py-4 px-4 sm:px-6 text-gray-300">${phoneNumber}</td>
                <td class="py-4 px-4 sm:px-6 text-green-400 font-semibold">KSh ${parseFloat(transaction.Amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                <td class="py-4 px-4 sm:px-6">
                    <span class="status-badge ${statusClass}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            ${statusIcon}
                        </svg>
                        ${statusText}
                    </span>
                </td>
                <td class="hidden md:table-cell py-4 px-4 sm:px-6 text-gray-300">${createdAt}</td>
                <td class="hidden lg:table-cell py-4 px-4 sm:px-6 text-gray-300">M-Pesa</td>
                <td class="hidden xl:table-cell py-4 px-4 sm:px-6 text-gray-400 font-mono text-sm">${receiptNumber}</td>
            </tr>
        `;
        
        tbody.append(row);
    });
}

// Load data when page is ready
$(document).ready(function() {
    // Initial load
    loadSubscriptionData();
    
    // Refresh data every 30 seconds
    setInterval(loadSubscriptionData, 30000);
});
