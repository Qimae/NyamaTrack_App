<?php
// Start the session
session_start();

// Include configuration
require_once 'config.php';

// Initialize response
$response = null;

// Check if we're processing a payment or showing a result
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['phone'])) {
    // Get and validate phone number
    $phone = trim($_POST['phone']);
    
    // Basic validation
    if (empty($phone)) {
        header('Location: subscription.php?error=' . urlencode('Please enter a phone number'));
        exit();
    }
    
    // Format phone number to 2547XXXXXXXX
    $phone = preg_replace('/^0/', '254', $phone);
    $phone = preg_replace('/\s+/', '', $phone);
    
    // Validate phone number format (2547XXXXXXXX)
    if (!preg_match('/^254[17]\d{8}$/', $phone)) {
        header('Location: subscription.php?error=' . urlencode('Please enter a valid Kenyan phone number starting with 07'));
        exit();
    }
    
    // Store phone in session for this payment
    $_SESSION['phone'] = $phone;
    
    // Include the STK push script and get its return value
    $response = include 'stk_push.php';
    
    // Ensure the response is an object
    if (is_array($response)) {
        $response = (object)$response;
    }
    
    // If we didn't get a valid response, create an error response
    if (!is_object($response) || !isset($response->success)) {
        $response = (object)[
            'success' => false,
            'message' => 'Invalid response from payment processor',
            'details' => 'Please try again or contact support if the problem persists.'
        ];
    }
    
    // Store the response in session for redirects
    $_SESSION['payment_response'] = $response;
    
    // Redirect to prevent form resubmission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
} 
// Check if we have a payment response in session
elseif (isset($_SESSION['payment_response'])) {
    $response = $_SESSION['payment_response'];
    unset($_SESSION['payment_response']);
} 
// Check if we have a checkout ID to check status
elseif (isset($_GET['check_status']) && isset($_SESSION['checkout_request_id'])) {
    // Here you would typically check the payment status with M-Pesa API
    // For now, we'll just show a waiting message
    $response = (object)[
        'success' => true,
        'checking_status' => true,
        'message' => 'Checking payment status...',
        'details' => 'Please wait while we verify your payment.'
    ];
} 
// No valid request, redirect to home
else {
    header('Location: subscription.php');
    exit();
}

// Set up the page content based on the response
$pageTitle = 'Payment Processing';
$icon = '⏳';
$message = 'Processing your request...';
$details = '';
$isSuccess = false;
$checkoutId = $response->checkout_request_id ?? null;

if (isset($response->success)) {
    if ($response->success) {
        if (isset($response->checking_status)) {
            // Still checking status
            $pageTitle = 'Verifying Payment';
            $icon = '⏳';
            $message = 'Verifying your payment...';
            $details = 'Please wait while we confirm your payment. This may take a moment.';
        } else {
            // Payment request sent successfully
            $pageTitle = 'Payment Request Sent';
            $icon = '✓';
            $message = 'Check your phone to complete payment';
            $details = 'We\'ve sent a payment request to ' . 
                      htmlspecialchars($_SESSION['phone'] ?? 'your phone') . 
                      '. Please check your phone to complete the payment.';
            $isSuccess = true;
            
            // Auto-check status after 5 seconds
            if ($checkoutId) {
                $details .= 'We will check the payment status automatically...';
            }
        }
    } else {
        // Error case
        $pageTitle = 'Payment Failed';
        $icon = '❌';
        $message = $response->message ?? 'Payment could not be processed';
        $details = $response->details ?? 'Please try again or contact support if the problem persists.';
    }
}

// Clear the phone from session if we're done
if (!isset($response->checking_status)) {
    unset($_SESSION['phone']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 50px;
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .message-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            border: 1px solid #e9ecef;
        }
        .message-icon {
            font-size: 4.5rem;
            margin-bottom: 1.5rem;
            line-height: 1;
        }
        .success-icon {
            color: #28a745;
        }
        .error-icon {
            color: #dc3545;
        }
        .processing-icon {
            color: #ffc107;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { opacity: 0.7; }
            50% { opacity: 1; }
            100% { opacity: 0.7; }
        }
        .details-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.25rem;
            margin: 1.5rem 0;
            text-align: center;
            font-size: 1.05rem;
            color: #495057;
            border-left: 4px solid #00B300;
        }
        .btn-primary {
            background-color: #00B300;
            border-color: #00B300;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            min-width: 150px;
        }
        .btn-primary:hover, .btn-primary:focus {
            background-color: #008a00;
            border-color: #008a00;
            box-shadow: 0 0 0 0.25rem rgba(0, 179, 0, 0.25);
        }
        .btn-outline-secondary {
            min-width: 150px;
        }
        .spinner-border {
            width: 1.25rem;
            height: 1.25rem;
            margin-right: 0.5rem;
            vertical-align: text-bottom;
            border-width: 0.15em;
        }
        .status-indicator {
            display: inline-flex;
            align-items: center;
            margin-top: 1rem;
            font-size: 0.95rem;
            color: #6c757d;
        }
        .status-spinner {
            margin-right: 0.5rem;
        }
        h2 {
            color: #343a40;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .lead {
            font-size: 1.25rem;
            color: #495057;
            margin-bottom: 1.5rem;
        }
        .reference-code {
            background: #e9ecef;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            font-family: monospace;
            word-break: break-all;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="message-container">
            <div class="message-icon <?php 
                echo $isSuccess ? 'success-icon' : 
                    (isset($response->checking_status) ? 'processing-icon' : 'error-icon'); 
            ?>">
                <?php echo $icon; ?>
            </div>
            
            <h2><?php echo htmlspecialchars($pageTitle); ?></h2>
            <p class="lead"><?php echo htmlspecialchars($message); ?></p>
            
            <?php if (!empty($details)): ?>
                <div class="details-box">
                    <?php echo nl2br(htmlspecialchars($details)); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($checkoutId) && $checkoutId): ?>
                <div class="mb-4">
                    <div class="mb-2">Reference Code:</div>
                    <div class="reference-code"><?php echo htmlspecialchars($checkoutId); ?></div>
                </div>
            <?php endif; ?>
            
            <div class="mt-4 d-flex justify-content-center gap-2 flex-wrap">
                <a href="subscription.php" class="btn btn-primary">
                    <i class="bi bi-house-door"></i> Back to Home
                </a>
                
                <?php if ($isSuccess && $checkoutId): ?>
                    <button id="checkStatusBtn" class="btn btn-outline-secondary">
                        <span class="spinner-border spinner-border-sm d-none" id="statusSpinner" role="status" aria-hidden="true"></span>
                        <span id="statusText">Check Status</span>
                    </button>
                <?php elseif (!$isSuccess && !isset($response->checking_status)): ?>
                    <a href="javascript:window.location.reload()" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Try Again
                    </a>
                <?php endif; ?>
            </div>
            
            <?php if (($isSuccess && $checkoutId) || isset($response->checking_status)): ?>
                <div class="status-indicator mt-3" id="statusIndicator">
                    <?php if (isset($response->checking_status)): ?>
                        <span class="spinner-border spinner-border-sm text-primary status-spinner" role="status"></span>
                        <span>Checking payment status...</span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-muted mt-4 small">
            Having issues? Contact our support at support@example.com
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkStatusBtn = document.getElementById('checkStatusBtn');
            const statusSpinner = document.getElementById('statusSpinner');
            const statusText = document.getElementById('statusText');
            const statusIndicator = document.getElementById('statusIndicator');
            
            // Auto-check status if we're in checking mode
            <?php if (isset($response->checking_status) && $checkoutId): ?>
                checkPaymentStatus('<?php echo $checkoutId; ?>');
            <?php endif; ?>
            
            // Handle manual status check
            if (checkStatusBtn) {
                checkStatusBtn.addEventListener('click', function() {
                    checkPaymentStatus('<?php echo $checkoutId; ?>');
                });
            }
            
            function checkPaymentStatus(checkoutId) {
                if (statusSpinner) statusSpinner.classList.remove('d-none');
                if (statusText) statusText.textContent = 'Checking...';
                if (statusIndicator) {
                    statusIndicator.innerHTML = `
                        <span class="spinner-border spinner-border-sm text-primary status-spinner" role="status"></span>
                        <span>Checking payment status...</span>
                    `;
                    statusIndicator.style.display = 'flex';
                }
                
                // Disable the button while checking
                if (checkStatusBtn) checkStatusBtn.disabled = true;
                
                // In a real implementation, you would make an AJAX call to check the payment status
                // For now, we'll simulate a check with a timeout
                setTimeout(function() {
                    // This is where you would make an AJAX call to your server
                    // to check the actual payment status with M-Pesa
                    
                    // For demo purposes, we'll just show a message
                    if (statusIndicator) {
                        statusIndicator.innerHTML = `
                            <i class="bi bi-info-circle-fill text-primary me-2"></i>
                            <span>Payment status: Pending. Please complete the payment on your phone.</span>
                        `;
                    }
                    
                    // Re-enable the button
                    if (checkStatusBtn) {
                        checkStatusBtn.disabled = false;
                        statusSpinner.classList.add('d-none');
                        statusText.textContent = 'Check Again';
                    }
                }, 2000);
            }
            
            // Auto-scroll to the status indicator if it exists
            if (statusIndicator) {
                statusIndicator.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</body>
</html>
