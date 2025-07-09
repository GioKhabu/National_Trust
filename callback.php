<?php
// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include config (contains DB and merchant_id)
include_once('conf.php');

$merchant_id = MERCHANT_ID;

// Use raw POST data
$post_data = $_POST;

// Check merchant_id
if (!isset($post_data['merchant_id']) || $post_data['merchant_id'] != $merchant_id) {
    http_response_code(400);
    exit('Invalid merchant_id');
}

// Extract data safely
$order_id_full    = $post_data['order_id'] ?? '';
$order_status     = $post_data['order_status'] ?? '';
$response_status  = $post_data['response_status'] ?? '';
$amount           = ($post_data['amount'] ?? 0) / 100; // Convert from cents
$currency         = $post_data['currency'] ?? '';

// Parse real order ID
$order_id_parts = explode('_', $order_id_full);
$order_id = isset($order_id_parts[1]) ? (int)$order_id_parts[1] : 0;

if ($order_id <= 0) {
    http_response_code(400);
    exit('Invalid order_id');
}

// Update donation status
$query = sprintf(
    'UPDATE Donation SET Status="%s", Response="%s" WHERE ID=%d',
    mysqli_real_escape_string($baza, $response_status),
    mysqli_real_escape_string($baza, json_encode($post_data, JSON_UNESCAPED_UNICODE)),
    $order_id
);

$result = mysqli_query($baza, $query);

// Log result (optional but highly recommended)
$log_message = date('Y-m-d H:i:s') . " - Order ID: $order_id | Status: $response_status";

if ($result) {
    $log_message .= " | Update: SUCCESS\n";
} else {
    $log_message .= " | Update: FAILED - " . mysqli_error($baza) . "\n";
}

file_put_contents('callback.log', $log_message, FILE_APPEND);

// Always respond with 200 to Flitt
http_response_code(200);
echo 'OK';
