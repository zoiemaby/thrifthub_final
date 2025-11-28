<?php
/**
 * Paystack Configuration
 * ThriftHub - Paystack Payment Gateway Configuration
 * 
 * Contains Paystack API keys and helper functions for payment processing
 */

// Paystack API Keys
// Test keys for development
define('PAYSTACK_SECRET_KEY', 'sk_test_dcb89c110d15169b5b148221eafcb05790d8c07f');
define('PAYSTACK_PUBLIC_KEY', 'pk_test_e5de7c1af7242e195eeb7305c76919738c991689');

// Base URL of the application
// Update this to match your actual domain
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$basePath = dirname(dirname($_SERVER['SCRIPT_NAME']));
define('APP_BASE_URL', $protocol . '://' . $host . $basePath);

// Callback URL for Paystack to redirect to after payment
define('PAYSTACK_CALLBACK_URL', APP_BASE_URL . '/view/paystack_callback.php');

/**
 * Initialize a Paystack transaction
 * 
 * @param string $email Customer email
 * @param int $amount_kobo Amount in pesewas (kobo)
 * @param string $reference Unique transaction reference
 * @param string $callback_url Callback URL
 * @return array Paystack API response
 */
function paystack_initialize_transaction($email, $amount_kobo, $reference, $callback_url) {
    $url = 'https://api.paystack.co/transaction/initialize';
    
    $fields = [
        'email' => $email,
        'amount' => $amount_kobo,
        'reference' => $reference,
        'callback_url' => $callback_url,
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        return [
            'status' => false,
            'message' => 'CURL Error: ' . $curlError
        ];
    }
    
    $response = json_decode($result, true);
    
    if ($httpCode !== 200) {
        return [
            'status' => false,
            'message' => $response['message'] ?? 'Failed to initialize transaction',
            'http_code' => $httpCode
        ];
    }
    
    return $response;
}

/**
 * Verify a Paystack transaction
 * 
 * @param string $reference Transaction reference
 * @return array Paystack API response
 */
function paystack_verify_transaction($reference) {
    $url = 'https://api.paystack.co/transaction/verify/' . urlencode($reference);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        return [
            'status' => false,
            'message' => 'CURL Error: ' . $curlError
        ];
    }
    
    $response = json_decode($result, true);
    
    if ($httpCode !== 200) {
        return [
            'status' => false,
            'message' => $response['message'] ?? 'Failed to verify transaction',
            'http_code' => $httpCode
        ];
    }
    
    return $response;
}

