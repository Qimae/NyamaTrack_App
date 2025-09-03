class Cart {
    constructor() {
        this.cart = { items: [], total: 0, itemCount: 0 };
        this.cartModalElement = null;
        this.cartModal = null;
        this.cartCountElement = null;
        this.desktopCartCountElement = null;
        this.isInitialized = false;

        // Bind methods to maintain proper 'this' context
        this.handleModalHidden = this.handleModalHidden.bind(this);

        this.initialize();
    }

    async initialize() {
        try {
            console.log('Initializing cart...');

            // Initialize DOM elements
            this.cartModalElement = document.getElementById('cartModal');
            console.log('Cart modal element:', this.cartModalElement);

            if (this.cartModalElement) {
                this.cartModal = new bootstrap.Modal(this.cartModalElement, {
                    backdrop: true,
                    keyboard: true
                });
                // Add event listener for when the modal is fully hidden
                this.cartModalElement.addEventListener('hidden.bs.modal', this.handleModalHidden);
            }

            // Ensure cart count element is found
            this.cartCountElement = document.getElementById('cart-count');
            console.log('Cart count element:', this.cartCountElement);

            if (!this.cartCountElement) {
                console.error('Cart count element not found in the DOM');
                // Try to find it again with a different selector
                const cartButton = document.getElementById('cart-button');
                if (cartButton) {
                    this.cartCountElement = cartButton.querySelector('.badge');
                    console.log('Found cart count via cart button:', this.cartCountElement);
                }
            }

            // Ensure cart count element is found
            this.desktopCartCountElement = document.getElementById('desktop-cart-count');
            console.log('Cart count element:', this.desktopCartCountElement);

            if (!this.desktopCartCountElement) {
                console.error('Cart count element not found in the DOM');
                // Try to find it again with a different selector
                const cartButton = document.getElementById('cart-button');
                if (cartButton) {
                    this.desktopCartCountElement = cartButton.querySelector('.badge');
                    console.log('Found cart count via cart button:', this.desktopCartCountElement);
                }
            }

            this.setupEventListeners();
            await this.loadCart();
            this.isInitialized = true;
            this.updateCartUI();
        } catch (error) {
            console.error('Error initializing cart:', error);
        }
    }

    setupEventListeners() {
        // Toggle cart modal
        document.addEventListener('click', (e) => {
            const cartButton = e.target.closest('.cart-button') || e.target.closest('.fa-shopping-cart');
            if (cartButton) {
                e.preventDefault();
                this.toggleCart();
            }
        });

        // Delegate events for cart operations
        document.addEventListener('click', async (e) => {
            const target = e.target.closest('[data-action]') || e.target.closest('.remove-item');
            if (!target) return;

            e.preventDefault();
            const action = target.dataset.action || 'remove';
            const itemId = target.dataset.itemId;

            if (action === 'increase' || action === 'decrease') {
                const item = this.cart.items.find(i => i.id == itemId);
                if (item) {
                    const newQuantity = action === 'increase' ? item.quantity + 1 : Math.max(1, item.quantity - 1);
                    await this.updateQuantity(itemId, newQuantity);
                }
            } else if (action === 'remove' || target.classList.contains('remove-item')) {
                await this.removeItem(itemId);
            }
        });

        // Handle checkout button
        document.addEventListener('click', (e) => {
            if (e.target.closest('.checkout-btn')) {
                this.checkout();
            }
        });
    }

    async loadCart() {
        try {
            const response = await fetch('api/cart_handler.php');
            const data = await response.json();

            if (data.success) {
                this.cart = data.cart;
                this.updateCartUI();
            } else {
                console.error('Failed to load cart:', data.error);
            }
        } catch (error) {
            console.error('Error loading cart:', error);
        }
    }

    async addToCart(productId, quantity = 1, event) {
        try {
            // Show loading state
            const addToCartBtn = event?.target?.closest('.add-to-cart');
            if (addToCartBtn) {
                const originalText = addToCartBtn.innerHTML;
                addToCartBtn.disabled = true;
                addToCartBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';

                // Store the original button state
                addToCartBtn.setAttribute('data-original-html', originalText);
            }

            const response = await fetch('api/cart_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update local cart state
                this.cart = data.cart;
                // Update UI
                this.updateCartUI();
                // Show success message
                this.showNotification('Item added to cart!', 'success');

                // Reset button state
                if (addToCartBtn) {
                    addToCartBtn.disabled = false;
                    addToCartBtn.innerHTML = 'Add to Cart';
                }

                // Refresh the page to show updated cart
                window.location.reload();
                return true;
            } else {
                throw new Error(data.error || 'Failed to add item to cart');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            this.showNotification(error.message || 'Failed to add item to cart', 'error');

            // Reset button state on error
            if (addToCartBtn) {
                addToCartBtn.disabled = false;
                const originalHtml = addToCartBtn.getAttribute('data-original-html') || 'Add to Cart';
                addToCartBtn.innerHTML = originalHtml;
            }

            return false;
        }
    }

    async updateQuantity(itemId, newQuantity) {
        try {
            const response = await fetch('api/cart_handler.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_id: itemId,
                    quantity: newQuantity
                })
            });

            const data = await response.json();

            if (data.success) {
                this.cart = data.cart;
                this.updateCartUI();
                return true;
            } else {
                throw new Error(data.error || 'Failed to update quantity');
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
            this.showNotification(error.message || 'Failed to update quantity', 'error');
            return false;
        }
    }

    async removeItem(itemId) {
        try {
            const response = await fetch('api/cart_handler.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    item_id: itemId
                })
            });

            const data = await response.json();

            if (data.success) {
                this.cart = data.cart;
                // Update the cart UI without closing the modal
                this.updateCartUI();
                this.showNotification('Item removed from cart', 'success');
                return true;
            } else {
                throw new Error(data.error || 'Failed to remove item');
            }
        } catch (error) {
            console.error('Error removing item:', error);
            this.showNotification(error.message || 'Failed to remove item', 'error');
            return false;
        }
    }

    async checkout() {
        try {
            const response = await fetch('api/checkout_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                this.cart = { items: [], total: 0 };
                this.updateCartUI();
                this.showNotification('Order placed successfully!', 'success');

                // Redirect to order confirmation page
                window.location.href = `/order_confirmation.php?order_id=${data.order_id}`;
                return true;
            } else {
                throw new Error(data.error || 'Checkout failed');
            }
        } catch (error) {
            console.error('Error during checkout:', error);
            this.showNotification(error.message || 'Checkout failed', 'error');
            return false;
        }
    }

    updateCartUI() {
        console.log('Updating cart UI...');

        if (!this.isInitialized) {
            console.warn('Cart is not fully initialized yet');
            return;
        }

        // Ensure cart count element is available
        if (!this.cartCountElement || !document.body.contains(this.cartCountElement)) {
            console.log('Cart count element not found, trying to find it...');
            this.cartCountElement = document.getElementById('cart-count');
            if (!this.cartCountElement) {
                // Try alternative selectors
                this.cartCountElement = document.querySelector('.cart-button .badge') ||
                    document.querySelector('.cart-count');
                console.warn('Cart count element not found with standard ID, using:', this.cartCountElement);
            }
        }
        if (!this.desktopCartCountElement || !document.body.contains(this.desktopCartCountElement)) {
            console.log('Cart count element not found, trying to find it...');
            this.desktopCartCountElement = document.getElementById('desktop-cart-count');
            if (!this.desktopCartCountElement) {
                // Try alternative selectors
                this.desktopCartCountElement = document.querySelector('.desktop-cart-button .badge') ||
                    document.querySelector('.desktop-cart-count');
                console.warn('Cart count element not found with standard ID, using:', this.desktopCartCountElement);
            }
        }

        if (!this.cart) {
            console.warn('Cart data is not available');
            return;
        }

        // Update cart count
        if (this.cartCountElement) {
            try {
                const itemCount = this.cart.items?.reduce((total, item) => {
                    return total + (parseInt(item.quantity) || 0);
                }, 0) || 0;

                console.log('Updating cart count:', itemCount, 'Element:', this.cartCountElement);
                this.cartCountElement.textContent = itemCount.toString();
                // Use opacity and visibility for smoother transitions
                if (itemCount > 0) {
                    this.cartCountElement.style.opacity = '1';
                    this.cartCountElement.style.visibility = 'visible';
                } else {
                    this.cartCountElement.style.opacity = '0';
                    this.cartCountElement.style.visibility = 'hidden';
                }

                // Also update any mobile cart counts
                const mobileCartCounts = document.querySelectorAll('.cart-count');
                mobileCartCounts.forEach(countEl => {
                    countEl.textContent = itemCount.toString();
                    countEl.style.display = itemCount > 0 ? 'inline-flex' : 'none';
                });
            } catch (error) {
                console.error('Error updating cart count:', error);
            }
        }

        // Update desktop cart count
        if (this.desktopCartCountElement) {
            try {
                const itemCount = this.cart.items?.reduce((total, item) => {
                    return total + (parseInt(item.quantity) || 0);
                }, 0) || 0;

                console.log('Updating cart count:', itemCount, 'Element:', this.desktopCartCountElement);
                this.desktopCartCountElement.textContent = itemCount.toString();
                // Use opacity and visibility for smoother transitions
                if (itemCount > 0) {
                    this.desktopCartCountElement.style.opacity = '1';
                    this.desktopCartCountElement.style.visibility = 'visible';
                } else {
                    this.desktopCartCountElement.style.opacity = '0';
                    this.desktopCartCountElement.style.visibility = 'hidden';
                }

                // Also update any mobile cart counts
                const desktopCartCounts = document.querySelectorAll('.desktop-cart-count');
                desktopCartCounts.forEach(countEl => {
                    countEl.textContent = itemCount.toString();
                    countEl.style.display = itemCount > 0 ? 'inline-flex' : 'none';
                });
            } catch (error) {
                console.error('Error updating cart count:', error);
            }
        }

        // Ensure required elements exist
        if (!this.cartModalElement) {
            this.cartModalElement = document.getElementById('cartModal');
            if (this.cartModalElement) {
                this.cartModal = new bootstrap.Modal(this.cartModalElement);
            } else {
                console.warn('Cart modal element not found');
                return;
            }
        }

        const cartItemsContainer = this.cartModalElement.querySelector('.cart-items');
        const cartTotal = this.cartModalElement.querySelector('.cart-total');
        const emptyCartMsg = this.cartModalElement.querySelector('.empty-cart-msg');
        const cartFooter = this.cartModalElement.querySelector('.cart-footer');

        if (!cartItemsContainer || !cartTotal || !emptyCartMsg || !cartFooter) {
            console.warn('One or more cart elements not found');
            return;
        }


        if (this.cart.items && this.cart.items.length > 0) {
            // Populate cart items
            cartItemsContainer.innerHTML = this.cart.items.map(item => `
                <div class="cart-item d-flex align-items-center py-3 border-bottom">
                    <img src="../butcheries/${item.image_path || 'default.jpg'}" alt="${item.name}" class="img-thumbnail me-3" style="width: 60px; height: 60px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 text-light">${item.name}</h6>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-light quantity-btn" data-action="decrease" data-item-id="${item.id}">-</button>
                            <span class="mx-2">${item.quantity}</span>
                            <button class="btn btn-sm btn-outline-light quantity-btn" data-action="increase" data-item-id="${item.id}">+</button>
                            <span class="ms-3 fw-bold">KSh ${(item.price * item.quantity).toFixed(2)}</span>
                        </div>
                    </div>
                    <button class="btn btn-link text-danger remove-item" data-item-id="${item.id}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `).join('');

            // Update total
            if (cartTotal) {
                cartTotal.textContent = `KSh ${(this.cart.total || 0).toFixed(2)}`;
            }

            // Show cart with items
            if (emptyCartMsg) emptyCartMsg.classList.add('d-none');
            if (cartFooter) cartFooter.classList.remove('d-none');

            // Add padding to center the cart content
            const modalContent = this.cartModalElement.querySelector('.modal-content');
            if (modalContent) {
                modalContent.style.padding = '20px';
            }

            // Add event listeners to quantity buttons
            const quantityButtons = cartItemsContainer.querySelectorAll('.quantity-btn');
            if (quantityButtons.length > 0) {
                quantityButtons.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const itemId = parseInt(btn.dataset.itemId);
                        const action = btn.dataset.action;
                        const quantitySpan = btn.parentElement?.querySelector('span');
                        if (!quantitySpan) return;

                        let newQuantity = parseInt(quantitySpan.textContent) || 1;

                        if (action === 'increase') {
                            newQuantity += 1;
                        } else if (action === 'decrease' && newQuantity > 1) {
                            newQuantity -= 1;
                        }

                        if (newQuantity !== parseInt(quantitySpan.textContent)) {
                            this.updateQuantity(itemId, newQuantity);
                        }
                    });
                });
            }

            // Add event listeners to remove buttons
            const removeButtons = cartItemsContainer.querySelectorAll('.remove-item');
            if (removeButtons.length > 0) {
                removeButtons.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const itemId = parseInt(btn.dataset.itemId);
                        if (!isNaN(itemId)) {
                            this.removeItem(itemId);
                        }
                    });
                });
            }

            // Add event listener to checkout button
            const checkoutBtn = this.cartModalElement.querySelector('.checkout-btn');
            if (checkoutBtn) {
                checkoutBtn.onclick = (e) => {
                    e.preventDefault();
                    this.checkout();
                };
            }
        } else {
            // Show empty cart message
            cartItemsContainer.innerHTML = '';
            emptyCartMsg.classList.remove('d-none');
            cartFooter.classList.add('d-none');
        }
    }

    createCartModal() {
        // Create modal if it doesn't exist
        if (document.getElementById('cartModal')) {
            this.cartModal = document.getElementById('cartModal');
            return;
        }

        const modalHTML = `
        <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-light">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="cartModalLabel">Your Cart</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="cart-items">
                            <!-- Cart items will be inserted here -->
                        </div>
                        <div class="empty-cart-msg text-center py-5 d-none">
                            <i class="fas fa-shopping-cart fa-3x mb-3 text-muted"></i>
                            <p class="mb-0">Your cart is empty</p>
                        </div>
                    </div>
                    <div class="modal-footer flex-column border-0 cart-footer d-none">
                        <div class="d-flex justify-content-between w-100 mb-3">
                            <h5 class="mb-0">Total:</h5>
                            <h5 class="mb-0 cart-total">KSh 0.00</h5>
                        </div>
                        <button class="btn btn-primary w-100 checkout-btn">
                            Proceed to Checkout
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.cartModal = document.getElementById('cartModal');

        // Initialize Bootstrap modal
        this.cartModal = new bootstrap.Modal(this.cartModal, {
            backdrop: true,
            keyboard: true
        });
    }

    toggleCart() {
        if (!this.cartModal) return;

        // Toggle the modal
        if (this.cartModal._isShown) {
            // When hiding, also remove the modal backdrop
            this.cartModal.hide();
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        } else {
            // When showing, ensure proper modal state
            this.cartModal.show();
            // Ensure the modal is properly positioned
            this.cartModal.handleUpdate();
        }
    }

    handleModalHidden() {
        // Clean up any remaining backdrop or modal-related classes
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }

    showNotification(message, type = 'info') {
        // You can implement a notification system here
        // For now, we'll use a simple alert
        const alertClass = type === 'error' ? 'danger' : type;
        const alert = document.createElement('div');
        alert.className = `alert alert-${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
        alert.role = 'alert';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(alert);

        // Auto remove after 3 seconds
        setTimeout(() => {
            alert.remove();
        }, 3000);
    }
}

// Initialize cart when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded, initializing cart...');

    // Check if cart is already initialized
    if (!window.cart) {
        window.cart = new Cart();
    }

    // Add event delegation for add to cart buttons
    document.addEventListener('click', (e) => {
        const addToCartBtn = e.target.closest('.add-to-cart');
        if (addToCartBtn) {
            e.preventDefault();
            const productId = addToCartBtn.dataset.productId;
            if (productId) {
                console.log('Add to cart clicked for product:', productId);
                window.cart.addToCart(productId, 1, e);
            }
        }
    });

    // Initial cart count update
    if (window.cart && typeof window.cart.updateCartUI === 'function') {
        window.cart.updateCartUI();
    }
});
