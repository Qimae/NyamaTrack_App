<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include configuration and access token
require_once 'config.php';
require_once 'access-token.php';

date_default_timezone_set('Africa/Nairobi');

// M-Pesa API endpoints
$processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$callbackurl = 'https://abenaenterprises.com/daraja/callback.php';
$passkey = "";  // enter passkey
$BusinessShortCode = ''; // enter business short code
$Timestamp = date('YmdHis');
$Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

// Validate and get phone number from session
if (!isset($_SESSION['phone'])) {
    header('Content-Type: application/json');
    die(json_encode([
        'error' => true,
        'message' => 'Phone number not provided'
    ]));
}

// Format phone number (ensure it starts with 254 and has 12 digits)
$phone = trim($_SESSION['phone']);
$originalPhone = $phone; // Keep the original for error reporting

// Remove any non-digit characters first
$phone = preg_replace('/\D/', '', $phone);

// If it's 9 digits (7XXXXXXXX), add 254 prefix
if (preg_match('/^7(\d{8})$/', $phone, $matches)) {
    $phone = '254' . $matches[1];
} 
// If it's 10 digits (07XXXXXXXX), remove the leading 0 and add 254
elseif (preg_match('/^0(7\d{8})$/', $phone, $matches)) {
    $phone = '254' . $matches[1];
}
// If it's already in 254 format, use as is
elseif (preg_match('/^254(7\d{8})$/', $phone, $matches)) {
    // Already in correct format
    $phone = '254' . $matches[1];
}

// Validate phone number format (12 digits starting with 2547)
if (strlen($phone) !== 12 || !preg_match('/^2547\d{8}$/', $phone)) {
    header('Content-Type: application/json');
    die(json_encode([
        'error' => true,
        'message' => 'Invalid phone number format. Please use a valid Kenyan number (e.g., 07XXXXXXXX or 2547XXXXXXXX)',
        'phone' => $phone,
        'original' => $originalPhone,
        'formatted' => $phone,
        'length' => strlen($phone)
    ]));
}

// Set payment details
// Get the amount from the subscription data
$money = isset($_POST['amount']) ? $_POST['amount'] : '1';
$PartyA = $phone;
$AccountReference = 'Payment System';
$TransactionDesc = 'Payment';
$Amount = $money;
// Set request headers
$stkpushheader = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
];

// Prepare the request payload
$curl_post_data = [
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $Password,
    'Timestamp' => $Timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $Amount,
    'PartyA' => $PartyA,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $PartyA,
    'CallBackURL' => $callbackurl,
    'AccountReference' => $AccountReference,
    'TransactionDesc' => $TransactionDesc
];

// Initialize cURL
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $processrequestUrl,
    CURLOPT_HTTPHEADER => $stkpushheader,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($curl_post_data),
    CURLOPT_SSL_VERIFYPEER => false, // Only for development, remove in production
    CURLOPT_SSL_VERIFYHOST => false  // Only for development, remove in production
]);

// Execute the request
$response = curl_exec($curl);
$error = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// Set the content type header
// Handle the response
if ($error) {
    $responseData = [
        'success' => false,
        'message' => 'Connection error. Please try again later.',
        'error' => $error,
        'http_code' => $httpCode
    ];
    return $responseData;
}

// Decode the response
$responseData = json_decode($response, true);

// Check if we got a valid JSON response
if (json_last_error() !== JSON_ERROR_NONE) {
    $responseData = [
        'success' => false,
        'message' => 'Invalid response from M-Pesa. Please try again.',
        'error' => 'Invalid JSON response',
        'raw_response' => $response
    ];
    return $responseData;
}

// Check for M-Pesa API errors
if (isset($responseData['errorCode'])) {
    $errorMessage = 'Payment request failed. ';
    $errorMessage .= $responseData['errorMessage'] ?? 'Please try again later.';
    
    $responseData = [
        'success' => false,
        'message' => $errorMessage,
        'error_code' => $responseData['errorCode'],
        'request_id' => $responseData['requestId'] ?? null
    ];
    return $responseData;
}

// Check for successful response
if (isset($responseData['ResponseCode']) && $responseData['ResponseCode'] === '0') {
    // Store the checkout request ID in session for status checking
    $_SESSION['checkout_request_id'] = $responseData['CheckoutRequestID'];
    
    $responseData = [
        'success' => true,
        'message' => 'Please check your phone to complete the payment',
        'checkout_request_id' => $responseData['CheckoutRequestID'],
        'phone' => $phone,
        'amount' => $Amount
    ];
    return $responseData;
} else {
    $errorMsg = $responseData['errorMessage'] ?? 'Payment request failed. Please try again.';
    
    $responseData = [
        'success' => false,
        'message' => $errorMsg,
        'response_code' => $responseData['ResponseCode'] ?? 'UNKNOWN',
        'response_description' => $responseData['ResponseDescription'] ?? 'No description provided'
    ];
    return $responseData;
}

exit();