
document.addEventListener('DOMContentLoaded', function () {
  // Load blocked users
  function loadBlockedUsers() {
    fetch('api/get_blocked_users.php')
      .then(response => response.json())
      .then(data => {
        if (data.success && data.data.length > 0) {
          const tbody = document.getElementById('blockedUsersTableBody');
          tbody.innerHTML = ''; // Clear loading message

          data.data.forEach(user => {
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-800 hover:bg-gray-800/30';
            row.innerHTML = `
                            <td class="py-4 text-gray-300">${escapeHtml(user.business_name)}</td>
                            <td class="py-4 text-gray-300">${user.blocked_date || 'N/A'}</td>
                            <td class="py-4 text-red-400">${escapeHtml(user.reason || 'No reason provided')}</td>
                            <td class="py-4">
                                <button onclick="unblockUser(${user.id}, this)" 
                                        class="unblock-btn bg-green-600 border-none hover:bg-green-700 text-white px-3 py-1 rounded-lg text-sm flex items-center gap-1">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Unblock
                                </button>
                            </td>
                        `;
            tbody.appendChild(row);
          });
        } else {
          document.getElementById('blockedUsersTableBody').innerHTML = `
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-400">No blocked users found</td>
                        </tr>`;
        }
      })
      .catch(error => {
        console.error('Error loading blocked users:', error);
        document.getElementById('blockedUsersTableBody').innerHTML = `
                    <tr>
                        <td colspan="5" class="py-4 text-center text-red-400">Error loading blocked users. Please try again.</td>
                    </tr>`;
      });
  }

  // Unblock user function
  window.unblockUser = function (id, button) {
    if (!confirm('Are you sure you want to unblock this user?')) {
      return;
    }

    // Disable button and show loading
    button.disabled = true;
    button.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing...`;

    fetch('api/unblock_user.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        id: id
      })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message and reload the list
          alert('User has been unblocked successfully');
          loadBlockedUsers();
        } else {
          throw new Error(data.error || 'Failed to unblock user');
        }
      })
      .catch(error => {
        console.error('Error unblocking user:', error);
        alert('Failed to unblock user. Please try again.');
        button.disabled = false;
        button.innerHTML = `
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Unblock`;
      });
  };

  // Helper function to escape HTML
  function escapeHtml(unsafe) {
    return unsafe
      .toString()
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  // Initial load
  loadBlockedUsers();
});