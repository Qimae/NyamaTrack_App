<?php
session_start();
require_once 'db/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NyamaTrack | Manage Products</title>
    <link rel="stylesheet" href="/NyamaTrack_App/utils/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Then Popper.js, then Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <!-- DataTables CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Other JS libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="utils/becken.css">

</head>

<body>
    <?php include 'includes/left-sidebar.php'; ?>
    <?php include 'includes/bottom-sidebar.php'; ?>

    <div class="bg" aria-hidden="true">
        <div class="orb red"></div>
        <div class="orb amber"></div>
        <div class="grid-overlay"></div>
    </div>
    <main class="py-4 transactions">
        <!-- Alert Container -->
        <div id="alertContainer" class="container mt-3" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;"></div>

        <div class="container-fluid">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="mb-4">Manage Products</h1>
                    <div class="card mb-4" style="background-color: var(--secondary-color); color: var(--text-color);">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-box me-2"></i>Products</h5>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                                <i class="fas fa-plus me-1"></i> Add New Product
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Date Filter Form -->
                            <div class="mb-4">
                                <form id="filterForm" class="row g-3">
                                    <div class="col-md-3">
                                        <label for="searchName" class="form-label">Search by Name</label>
                                        <input type="text" class="form-control bg-dark text-light border-secondary" id="searchName" placeholder="Product name...">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" id="resetFilters" class="btn btn-outline-secondary">
                                            <i class="fas fa-sync-alt me-1"></i> Reset
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Products Table -->
                            <div class="table-responsive">
                                <table class="table table-border" id="productsTable" style="width:100%; color: white; background-color: transparent; --bs-table-bg: transparent; --bs-table-striped-bg: transparent; --bs-table-color: white;">
                                    <thead>
                                        <tr>
                                            <th class="text-start">Product ID</th>
                                            <th class="text-center">Image</th>
                                            <th class="text-start">Name</th>
                                            <th class="text-start">Description</th>
                                            <th class="text-end">Price (Ksh)</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Products will be loaded via JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Product Modal -->
        <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-light">
                    <form id="productForm" enctype="multipart/form-data">
                        <div class="modal-header border-secondary">
                            <h5 class="modal-title" id="productModalLabel">Add New Product</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">X</button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="productId" name="product_id">

                            <div class="mb-3">
                                <label for="productName" class="form-label">Product Name *</label>
                                <input type="text" class="form-control bg-dark text-light border-secondary" id="productName" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="productDescription" class="form-label">Description</label>
                                <textarea class="form-control bg-dark text-light border-secondary" id="productDescription" name="description" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="productPrice" class="form-label">Price (Ksh) *</label>
                                <input type="number" step="0.01" min="0" class="form-control bg-dark text-light border-secondary" id="productPrice" name="price" required>
                            </div>

                            <div class="mb-3">
                                <label for="productImage" class="form-label">Product Image</label>
                                <input class="form-control bg-dark text-light border-secondary" type="file" id="productImage" name="image" accept="image/*">
                                <small class="text-muted">Max size: 2MB. Allowed formats: JPG, PNG, GIF</small>
                                <div id="imagePreview" class="mt-2 text-center"></div>
                            </div>
                        </div>
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="saveProductBtn">Save Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-light">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                        <input type="hidden" id="deleteProductId">
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- Custom JavaScript -->
    <script src="js/products_script.js"></script>

    <script>
        // Initialize modals after document is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modals
            const productModalEl = document.getElementById('productModal');
            const deleteModalEl = document.getElementById('deleteModal');

            if (productModalEl) {
                productModal = new bootstrap.Modal(productModalEl, {
                    backdrop: true,
                    keyboard: false
                });
            }

            if (deleteModalEl) {
                deleteModal = new bootstrap.Modal(deleteModalEl, {
                    backdrop: true,
                    keyboard: false
                });
            }

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
</body>

</html>