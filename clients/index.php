<?php
session_start();
require_once 'db/config.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch all clients from the database
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching products: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NyamaTrack - Clients</title>
    <meta name="theme-color" content="#007bff" />
    <link rel="stylesheet" href="utils/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/cart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize cart after a small delay to ensure all elements are available
            setTimeout(() => {
                window.cart = new Cart();
            }, 100);
        });

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('service-worker.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
    <link rel="stylesheet" href="utils/clients.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid px-4">
            <a class="navbar-brand me-auto" href="#">NyamaTrack</a>

            <!-- Mobile Buttons -->
            <div class="d-flex d-lg-none align-items-center gap-3">
                <!-- Cart Button (Mobile) -->
                <button class="btn position-relative px-3 border-0 cart-button" type="button" id="cart-button">
                    <i class="fas fa-shopping-cart text-light"></i>
                    <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="opacity: 0; transition: opacity 0.3s ease;">
                        0
                        <span class="visually-hidden">items in cart</span>
                    </span>
                </button>

                <!-- User Account Button (Mobile) -->
                <div class="dropdown">
                    <button class="btn d-flex align-items-center px-3 border-0" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fas fa-user text-light"></i>
                        <span class="d-none d-sm-inline text-light"><?php echo $isLoggedIn ? 'My Account' : 'Account'; ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if ($isLoggedIn): ?>
                            <li><a class="dropdown-item" href="account.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="orders.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="login.php"><i class="fas fa-sign-in-alt me-2"></i>Sign In</a></li>
                            <li><a class="dropdown-item" href="register.php"><i class="fas fa-user-plus me-2"></i>Sign Up</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Desktop View -->
            <div class="d-none d-lg-flex w-100 justify-content-between">
                <!-- Empty div to balance the flex layout -->
                <div style="width: 200px;"></div>

                <!-- Centered Search Bar -->
                <div class="d-flex justify-content-center" style="flex-grow: 1; max-width: 600px;">
                    <div class="input-group" style="width: 100%; max-width: 500px;">
                        <input type="search" class="form-control search-box" placeholder="Search products...">
                        <button class="btn btn-dark" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Right Side Buttons -->
                <div class="d-flex align-items-center justify-content-end" style="width: 200px;">
                    <!-- Cart Button -->
                    <button class="btn position-relative border-0 desktop-cart-button" type="button" id="desktop-cart-button">
                        <i class="fas fa-shopping-cart text-light"></i>
                        <span id="desktop-cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="opacity: 0; transition: opacity 0.3s ease;">
                            0
                        </span>
                    </button>

                    <!-- User Account (Desktop) -->
                    <div class="dropdown d-none d-lg-block">
                        <button class="btn d-flex align-items-center px-3 border-0" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-user me-2 text-light"></i>
                            <span class="text-light"><?php echo $isLoggedIn ? 'My Account' : 'Account'; ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($isLoggedIn): ?>
                                <li><a class="dropdown-item" href="account.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                                <li><a class="dropdown-item" href="orders.php"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href="login.php"><i class="fas fa-sign-in-alt me-2"></i>Sign In</a></li>
                                <li><a class="dropdown-item" href="register.php"><i class="fas fa-user-plus me-2"></i>Sign Up</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>


    <!-- Mobile Search Bar (Always visible on mobile) -->
    <div class="d-block d-lg-none bg-dark py-2">
        <div class="container">
            <div class="input-group">
                <input type="search" class="form-control search-box" placeholder="Search products...">
                <button class="btn btn-dark" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Product Details Modal -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="productModalLabel">Product Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img id="productImage" src="" class="img-fluid rounded mb-3"
                            style="max-height: 200px; width: auto;" alt="Product Image">
                        <h4 id="productName" class="mb-2"></h4>
                        <h5 id="productPrice" class="text-accent mb-3"></h5>
                        <p id="productDescription" class="text-muted"></p>
                    </div>
                    <div class="mb-4">
                        <label for="quantity" class="form-label">Quantity (KGs)</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" id="decreaseQty">-</button>
                            <input type="number" class="form-control text-center bg-dark text-light border-secondary"
                                id="quantity" value="1" min="1">
                            <button class="btn btn-outline-secondary" type="button" id="increaseQty">+</button>
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-accent" id="addToCartBtn">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                        </button>
                        <button class="btn btn-outline-light" id="makeOrderBtn">
                            <i class="fas fa-shopping-bag me-2"></i>Make Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="main-content">
                    <!-- Header -->
                    <div class="d-flex flex-column w-100">
                        <h2 class="mb-3 text-center text-md-start">NyamaTrack Products</h2>

                        <!-- Category Navigation -->
                        <div class="d-flex flex-wrap gap-3 mb-4 justify-content-center justify-content-md-start">
                            <button class="btn btn-dark px-4 py-2">
                                <i class="fas fa-cow me-2"></i>Goat
                            </button>
                            <button class="btn btn-outline-light px-4 py-2">
                                <i class="fas fa-cow me-2"></i>Beef
                            </button>
                            <button class="btn btn-outline-light px-4 py-2">
                                <i class="fas fa-cow me-2"></i>Matumbo
                            </button>
                            <button class="btn btn-outline-light px-4 py-2">
                                <i class="fa-solid fa-drumstick-bite me-2"></i>Chicken
                            </button>
                            <button class="btn btn-outline-light px-4 py-2">
                                <i class="fas fa-fish me-2"></i>Fish
                            </button>
                        </div>

                    </div>

                    <!-- Product Catalog -->
                    <div class="row mb-4 justify-content-center">
                        <div class="col-12">
                            <div class="dashboard-card">
                                <div
                                    class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 text-center text-md-start w-100">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <select class="form-select bg-dark text-light border-secondary">
                                            <option>Sort by: Featured</option>
                                            <option>Price: Low to High</option>
                                            <option>Price: High to Low</option>
                                            <option>Newest First</option>
                                        </select>
                                        <select class="form-select bg-dark text-light border-secondary">
                                            <option>Sort by: Latest</option>
                                            <option>Price: Low to High</option>
                                            <option>Price: High to Low</option>
                                            <option>Name: A to Z</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                    <?php elseif (empty($products)): ?>
                                        <div class="alert alert-info">No products available at the moment.</div>
                                    <?php else: ?>
                                        <!-- Product Card -->
                                        <?php foreach ($products as $product): ?>

                                            <div class="col-md-3 col-sm-6">
                                                <div class="product-card p-3 rounded" style="background: var(--bg-secondary);">
                                                    <div class="position-relative mb-3">
                                                        <img src="../butcheries/<?php echo htmlspecialchars($product['image_path'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                            class="img-fluid rounded">
                                                        <span class="badge bg-success position-absolute top-0 end-0 m-2">In Stock</span>
                                                    </div>
                                                    <h6 class="text-light mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                                    <p class="text-light small mb-2"><?php echo htmlspecialchars($product['business_name']); ?></p>
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <div class="h5 mb-0">KSh <?php echo number_format($product['price'], 2); ?></div>
                                                        <small class="text-light">Available</small>
                                                    </div>
                                                    <div class="d-grid gap-2">
                                                        <button class="btn btn-accent w-100 add-to-cart"
                                                            data-product-id="<?php echo $product['id']; ?>"
                                                            data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                                            data-product-price="<?php echo $product['price']; ?>"
                                                            data-product-image="<?php echo htmlspecialchars($product['image_path'] ?? 'default.jpg'); ?>">
                                                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="cartModalLabel">Your Cart</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="cart-items">
                        <!-- Cart items will be dynamically inserted here -->
                        <div class="text-center p-4 empty-cart-msg">
                            <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                            <p class="mb-0">Your cart is empty</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 cart-footer d-none">
                    <div class="w-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Total:</h5>
                            <h4 class="mb-0 cart-total">KSh 0.00</h4>
                        </div>
                        <button class="btn btn-primary w-100 py-2 checkout-btn">
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script>
        // Initialize cart when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event listeners to all 'Add to Cart' buttons
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.dataset.productId;
                    const productName = this.dataset.productName;
                    const productPrice = parseFloat(this.dataset.productPrice);

                    // Show loading state
                    const originalText = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';

                    // Add to cart
                    if (window.cart) {
                        window.cart.addToCart(productId, 1).then(success => {
                            if (success) {
                                // Success - button will be reset by the cart class
                            } else {
                                // Reset button on error
                                this.disabled = false;
                                this.innerHTML = originalText;
                            }
                        });
                    } else {
                        console.error('Cart not initialized');
                        this.disabled = false;
                        this.innerHTML = originalText;
                        alert('Cart system not available. Please try again later.');
                    }
                });
            });

            // Initialize product modal
            const productModal = new bootstrap.Modal(document.getElementById('productModal'));

            // Product details functionality
            function showProductDetails(product) {
                document.getElementById('productName').textContent = product.name;
                document.getElementById('productPrice').textContent = product.price;
                document.getElementById('productDescription').textContent = product.description || 'No description available';
                document.getElementById('productImage').src = product.image || 'https://via.placeholder.com/300x200';
                document.getElementById('quantity').value = 1;
                productModal.show();
            }

            // Quantity controls
            document.getElementById('increaseQty').addEventListener('click', () => {
                const qtyInput = document.getElementById('quantity');
                qtyInput.value = parseInt(qtyInput.value) + 1;
            });

            document.getElementById('decreaseQty').addEventListener('click', () => {
                const qtyInput = document.getElementById('quantity');
                if (qtyInput.value > 1) {
                    qtyInput.value = parseInt(qtyInput.value) - 1;
                }
            });

            // Add to cart functionality
            document.getElementById('addToCartBtn').addEventListener('click', () => {
                const product = {
                    name: document.getElementById('productName').textContent,
                    price: document.getElementById('productPrice').textContent,
                    quantity: document.getElementById('quantity').value
                };
                // Add to cart logic here
                console.log('Added to cart:', product);
                alert('Added to cart: ' + product.quantity + 'x ' + product.name);
                productModal.hide();
            });

            // Make order functionality
            document.getElementById('makeOrderBtn').addEventListener('click', () => {
                const product = {
                    name: document.getElementById('productName').textContent,
                    price: document.getElementById('productPrice').textContent,
                    quantity: document.getElementById('quantity').value
                };
                // Make order logic here
                console.log('Making order:', product);
                alert('Order placed for ' + product.quantity + 'x ' + product.name);
                productModal.hide();
            });

            // Initialize tooltips
            document.addEventListener('DOMContentLoaded', function() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        });
    </script>
</body>

</html>