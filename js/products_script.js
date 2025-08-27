// Global variables
let productsDataTable;
let productModal, deleteModal;

// Initialize everything when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const productsTable = document.getElementById('productsTable');
    const productForm = document.getElementById('productForm');
    const productModalEl = document.getElementById('productModal');
    const deleteModalEl = document.getElementById('deleteModal');
    const productModalLabel = document.getElementById('productModalLabel');
    const saveProductBtn = document.getElementById('saveProductBtn');
    
    // Initialize modals if they exist
    if (productModalEl) {
        // Remove aria-hidden from modal element
        productModalEl.removeAttribute('aria-hidden');
        
        productModal = new bootstrap.Modal(productModalEl, {
            backdrop: 'static',
            keyboard: false
        });
        
        productModalEl.addEventListener('show.bs.modal', function() {
            // Ensure modal is accessible when shown
            this.removeAttribute('aria-hidden');
            this.setAttribute('aria-modal', 'true');
        });
        
        productModalEl.addEventListener('hidden.bs.modal', function() {
            if (typeof resetForm === 'function') {
                resetForm();
            }
            // Set aria-hidden when modal is hidden
            this.setAttribute('aria-hidden', 'true');
            this.removeAttribute('aria-modal');
        });
    }
    
    if (deleteModalEl) {
        // Remove aria-hidden from delete modal element
        deleteModalEl.removeAttribute('aria-hidden');
        
        deleteModal = new bootstrap.Modal(deleteModalEl, {
            backdrop: 'static',
            keyboard: false
        });
        
        deleteModalEl.addEventListener('show.bs.modal', function() {
            // Ensure modal is accessible when shown
            this.removeAttribute('aria-hidden');
            this.setAttribute('aria-modal', 'true');
        });
        
        deleteModalEl.addEventListener('hidden.bs.modal', function() {
            // Set aria-hidden when modal is hidden
            this.setAttribute('aria-hidden', 'true');
            this.removeAttribute('aria-modal');
        });
    }
    
    // Initialize DataTable
    if (typeof initializeDataTable === 'function') {
        initializeDataTable();
    }
    
    // Load products
    if (typeof loadProducts === 'function') {
        loadProducts();
    }
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const productImageInput = document.getElementById('productImage');
    const imagePreview = document.getElementById('imagePreview');
    
    // Initialize modals if not already initialized
    if (productModalEl && !productModal) {
        productModal = new bootstrap.Modal(productModalEl, {
            backdrop: true,
            keyboard: false
        });
    }
    
    if (deleteModalEl && !deleteModal) {
        deleteModal = new bootstrap.Modal(deleteModalEl, {
            backdrop: true,
            keyboard: false
        });
    }
    
    let isEditMode = false;
    let currentProductId = null;

    // Event Listeners
    productForm.addEventListener('submit', handleFormSubmit);
    confirmDeleteBtn.addEventListener('click', deleteProduct);
    productImageInput.addEventListener('change', handleImageUpload);
    
    // Initialize DataTable
    function initializeDataTable() {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#productsTable')) {
            $('#productsTable').DataTable().destroy();
        }
        
        // Initialize new DataTable with server-side processing
        window.productsDataTable = $('#productsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'api/products_handler.php?action=get_products',
                type: 'GET'
            },
            columns: [
                { data: 'id', className: 'text-center' },
                {
                    data: 'image_path',
                    className: 'text-center',
                    render: function(data) {
                        return data ? 
                            `<img src="${data}" alt="Product" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">` : 
                            '<i class="fas fa-image text-muted"></i>';
                    },
                    orderable: false
                },
                { 
                    data: 'name',
                    render: function(data) {
                        return `<strong>${data}</strong>`;
                    }
                },
                { 
                    data: 'description',
                    render: function(data) {
                        return data || '<span class="text-muted">No description</span>';
                    }
                },
                { 
                    data: 'price',
                    className: 'text-end',
                    render: function(data) {
                        return `<span class="fw-bold">Ksh ${parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>`;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data) {
                        return `
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary edit-product" data-id="${data.id}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger delete-product" data-id="${data.id}" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']],
            responsive: true,
            drawCallback: function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            language: {
                processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>',
                emptyTable: 'No products found',
                zeroRecords: 'No matching products found'
            }
        });

        // Search by name
        $('#searchName').on('keyup', function() {
            window.productsDataTable.column(2).search(this.value).draw();
        });

        // Reset filters
        $('#resetFilters').on('click', function() {
            $('#searchName').val('');
            window.productsDataTable.search('').columns().search('').draw();
        });
    }
    
    // Initialize event delegation for dynamically added buttons
    $(document).on('click', '.edit-product', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const productId = $(this).data('id');
        editProduct(productId);
        const productModalElement = document.getElementById('productModal');
        if (productModalElement) {
            const modal = bootstrap.Modal.getInstance(productModalElement) || 
                         new bootstrap.Modal(productModalElement);
            modal.show();
        }
    });
    
    $(document).on('click', '.delete-product', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const productId = $(this).data('id');
        $('#deleteProductId').val(productId);
        const deleteModalElement = document.getElementById('deleteModal');
        if (deleteModalElement) {
            const modal = bootstrap.Modal.getInstance(deleteModalElement) || 
                         new bootstrap.Modal(deleteModalElement);
            modal.show();
        }
    });

    // Load products into DataTable
    async function loadProducts() {
        try {
            const response = await fetch('api/products_handler.php?action=get_products');
            const result = await response.json();
            
            if (result.success) {
                // Initialize DataTable if not already initialized
                if (!$.fn.DataTable.isDataTable('#productsTable')) {
                    initializeDataTable();
                }
                
                // Clear and add new data
                if (window.productsDataTable) {
                    window.productsDataTable.clear().rows.add(result.data).draw();
                } else {
                    console.error('DataTable not initialized');
                    showAlert('danger', 'Failed to initialize products table');
                }
            } else {
                showAlert('danger', result.message || 'Failed to load products');
            }
        } catch (error) {
            console.error('Error loading products:', error);
            showAlert('danger', 'An error occurred while loading products');
        }
    }

    // Handle form submission
    async function handleFormSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData();
        const productData = {
            id: document.getElementById('productId').value,
            name: document.getElementById('productName').value.trim(),
            description: document.getElementById('productDescription').value.trim(),
            price: document.getElementById('productPrice').value.trim()
        };

        // Validate required fields
        if (!productData.name || !productData.price) {
            showAlert('warning', 'Please fill in all required fields');
            return;
        }

        // Add form data to FormData
        Object.keys(productData).forEach(key => {
            if (productData[key]) formData.append(key, productData[key]);
        });

        // Add image file if exists
        const imageFile = productImageInput.files[0];
        if (imageFile) {
            formData.append('image', imageFile);
        }

        // Show loading state
        const originalBtnText = saveProductBtn.innerHTML;
        saveProductBtn.disabled = true;
        saveProductBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

        try {
            const response = await fetch('api/products_handler.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('success', result.message || 'Product saved successfully');
                productModal.hide();
                loadProducts();
                resetForm();
            } else {
                throw new Error(result.message || 'Failed to save product');
            }
        } catch (error) {
            console.error('Error saving product:', error);
            showAlert('danger', error.message || 'An error occurred while saving the product');
        } finally {
            // Restore button state
            saveProductBtn.disabled = false;
            saveProductBtn.innerHTML = originalBtnText;
        }
    }

    // Edit product
    async function editProduct(id) {
        try {
            const response = await fetch(`api/products_handler.php?action=get_product&id=${id}`);
            const result = await response.json();
            
            if (result.success) {
                const product = result.data;
                currentProductId = product.id;
                
                // Set form values
                document.getElementById('productId').value = product.id;
                document.getElementById('productName').value = product.name;
                document.getElementById('productDescription').value = product.description || '';
                document.getElementById('productPrice').value = product.price;
                
                // Show current image if exists
                if (product.image_path) {
                    imagePreview.innerHTML = `
                        <p>Current Image:</p>
                        <img src="${product.image_path}" alt="Current Product" class="img-thumbnail" style="max-width: 200px;">
                    `;
                } else {
                    imagePreview.innerHTML = '<p class="text-muted">No image selected</p>';
                }
                
                // Update modal title and show
                productModalLabel.textContent = 'Edit Product';
                isEditMode = true;
                productModal.show();
            } else {
                throw new Error(result.message || 'Failed to load product');
            }
        } catch (error) {
            console.error('Error loading product:', error);
            showAlert('danger', error.message || 'An error occurred while loading the product');
        }
    }

    // Show delete confirmation
    function showDeleteConfirmation(id) {
        currentProductId = id;
        deleteModal.show();
    }

    // Delete product
    async function deleteProduct() {
        if (!currentProductId) return;
        
        try {
            const response = await fetch('api/products_handler.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: currentProductId })
            });
            
            const result = await response.json();
            
            if (result.success) {
                showAlert('success', result.message || 'Product deleted successfully');
                deleteModal.hide();
                loadProducts();
            } else {
                throw new Error(result.message || 'Failed to delete product');
            }
        } catch (error) {
            console.error('Error deleting product:', error);
            showAlert('danger', error.message || 'An error occurred while deleting the product');
        }
    }

    // Handle image upload preview
    function handleImageUpload(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            showAlert('warning', 'Please upload a valid image file (JPG, PNG, GIF)');
            e.target.value = '';
            return;
        }
        
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            showAlert('warning', 'Image size should be less than 2MB');
            e.target.value = '';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.innerHTML = `
                <p>New Image Preview:</p>
                <img src="${e.target.result}" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
            `;
        };
        reader.readAsDataURL(file);
    }

    // Reset form
    function resetForm() {
        productForm.reset();
        imagePreview.innerHTML = '';
        currentProductId = null;
        isEditMode = false;
        productModalLabel.textContent = 'Add New Product';
    }

    // Show alert message
    function showAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        
        // Create alert if container exists
        if (alertContainer) {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type} alert-dismissible fade show`;
            alert.role = 'alert';
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Clear any existing alerts
            alertContainer.innerHTML = '';
            alertContainer.appendChild(alert);
            
            // Auto-remove alert after 5 seconds
            setTimeout(() => {
                const alertInstance = bootstrap.Alert.getOrCreateInstance(alert);
                if (alertInstance) {
                    alertInstance.close();
                }
            }, 5000);
        } else {
            // Fallback to console if container not found
            console.log(`[${type.toUpperCase()}] ${message}`);
        }
    }

});
