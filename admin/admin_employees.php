<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: index.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Processing - NyamaTrack Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="utils/registration.css">
    <link rel="stylesheet" href="utils/emmanuel.css">
</head>

<?php
require_once 'api/admin_employees_logic.php';
$result = getAdminEmployees($pdo);
$employees = $result['employees'] ?? [];
$debug = $result['debug'] ?? [];
$error = $result['error'] ?? null;
?>

<body class="bg-black text-white">
    <?php include 'includes/left-sidebar.php'; ?>
    <?php include 'includes/bottom-sidebar.php'; ?>
    <div class="main-content">
        <div class="p-4 sm:p-6">
            <!-- Page Header -->
            <div class="relative mb-6 sm:mb-8 p-4 sm:p-6 bg-gradient-to-r from-gray-900 to-black rounded-xl border border-gray-800">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="p-2 sm:p-3 rounded-lg bg-gradient-to-br from-red-600 to-orange-600">
                        <svg class="h-6 w-6 sm:h-8 sm:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Admin Employees Management</h1>
                        <p class="text-sm sm:text-base text-gray-400">Manage all admin employees.</p>
                    </div>
                </div>
            </div>
            <!-- Admin Employees Section -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6 mb-8 shadow-xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-white">Admin Employees</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-800">
                                <th class="text-left text-orange-400 font-semibold py-3">Name</th>
                                <th class="text-left text-orange-400 font-semibold py-3">Email</th>
                                <th class="text-left text-orange-400 font-semibold py-3">Role</th>
                                <th class="text-left text-orange-400 font-semibold py-3">Status</th>
                                <th class="text-left text-orange-400 font-semibold py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($employees as $employee): ?>
                                <tr class="border-b border-gray-800 hover:bg-gray-800/50">
                                    <td class="py-4"><?= htmlspecialchars($employee['fullname']) ?></td>
                                    <td class="py-4"><?= htmlspecialchars($employee['email']) ?></td>
                                    <td class="py-4">Admin</td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-900/50 text-green-300">
                                            Active
                                        </span>
                                    </td>
                                    <td class="py-4">
                                        <button onclick="openEditModal(<?= htmlspecialchars(json_encode($employee)) ?>)"
                                            class="text-blue-400 hover:text-blue-300 mr-3">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button onclick="confirmDelete(<?= $employee['id'] ?>, '<?= addslashes($employee['fullname']) ?>')"
                                            class="text-red-400 hover:text-red-300">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($employees)): ?>
                                <tr>
                                    <td colspan="6" class="py-4 text-center text-gray-400">No admin employees found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Employee Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-white">Edit Employee</h3>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-white p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Form -->
            <form id="editEmployeeForm" onsubmit="updateEmployee(event)" class="space-y-4">
                <input type="hidden" id="edit_id" name="id">

                <!-- Full Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Full Name</label>
                    <input type="text" id="edit_fullname" name="fullname" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-gray-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                    <input type="email" id="edit_email" name="email" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-gray-500">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Phone</label>
                    <input type="text" id="edit_phone" name="phone"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-gray-500">
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-700 mt-4">
                    <button type="button" onclick="closeModal('editModal')"
                        class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>



    <!-- Debug Information -->
    <div class="mt-8 p-4 bg-gray-900 rounded-lg border border-gray-800">
        <h3 class="text-lg font-semibold text-orange-400 mb-2">Debug Information</h3>
        <div class="bg-black p-4 rounded overflow-auto max-h-60">
            <?php if ($error): ?>
                <div class="text-red-400 font-mono text-sm">
                    <div class="font-bold">ERROR:</div>
                    <pre><?= htmlspecialchars(print_r($error, true)) ?></pre>
                </div>
            <?php endif; ?>

            <div class="text-green-400 font-mono text-sm mt-2">
                <div class="font-bold">Debug Log:</div>
                <pre><?= htmlspecialchars(implode("\n", $debug)) ?></pre>
            </div>

            <div class="text-blue-400 font-mono text-sm mt-4">
                <div class="font-bold">Employees Data (<?= count($employees) ?>):</div>
                <pre><?= htmlspecialchars(print_r($employees, true)) ?></pre>
            </div>
        </div>
    </div>

    <script>
        // Output debug info to console
        <?php if (!empty($debug)): ?>
            console.log('Debug Information:');
            <?php foreach ($debug as $line): ?>
                console.log('<?= addslashes($line) ?>');
            <?php endforeach; ?>

            <?php if (!empty($employees)): ?>
                console.log('Employees Data:', <?= json_encode($employees) ?>);
            <?php endif; ?>

            <?php if ($error): ?>
                console.error('Error:', <?= json_encode($error) ?>);
            <?php endif; ?>
        <?php endif; ?>

        // Modal functions
        function openEditModal(employee) {
            document.getElementById('edit_id').value = employee.id;
            document.getElementById('edit_fullname').value = employee.fullname;
            document.getElementById('edit_email').value = employee.email;
            document.getElementById('edit_phone').value = employee.phone || '';
            document.getElementById('editModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = 'auto'; // Re-enable scrolling
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('fixed')) {
                closeModal(event.target.id);
            }
        }

        // Handle form submission for updating employee
        function updateEmployee(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            formData.append('action', 'update');

            fetch('api/admin_employees_logic.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            closeModal('editModal');
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'An error occurred', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'An error occurred while updating the employee', 'error');
                });
        }

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete "${name}". This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id', id);

                    fetch('api/admin_employees_logic.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: data.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', data.message || 'An error occurred', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire('Error', 'An error occurred while deleting the employee', 'error');
                        });
                }
            });
        }
    </script>
</body>

</html>