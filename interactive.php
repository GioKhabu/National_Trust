<?php
session_start();
header('Content-Type: application/json');

if (!isset($_POST['f'])) {
    echo json_encode(['error' => 'No function specified']);
    exit;
}

include 'conf.php';
$NoInterface = true;
include 'functions.php';

$f = $_POST['f'];

if ($f == 'genDonateOrder') {
    // Validate Amount
    $Amount = isset($_POST['Amount']) ? intval($_POST['Amount']) : 0;
    if ($Amount <= 0) {
        echo json_encode(['error' => 'Invalid amount']);
        exit;
    }

    // Validate Currency
    $Currency = isset($_POST['Currency']) ? $_POST['Currency'] : '';
    $allowed_currencies = ['USD', 'EUR', 'GEL'];
    if (!in_array($Currency, $allowed_currencies)) {
        echo json_encode(['error' => 'Invalid currency']);
        exit;
    }

    // Validate UserData
    $UserData = isset($_POST['UserData']) ? $_POST['UserData'] : [];
    if (!is_array($UserData)) {
        echo json_encode(['error' => 'Invalid user data']);
        exit;
    }

    // Encode UserData to JSON
    $UserDataJson = json_encode($UserData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($UserDataJson === false) {
        echo json_encode(['error' => 'Failed to encode user data']);
        exit;
    }

    // Insert into DB
    $stmt = $baza->prepare("INSERT INTO Donation (UserData, Currency, Amount) VALUES (?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(['error' => 'Prepare failed: ' . $baza->error]);
        exit;
    }

    $stmt->bind_param("ssi", $UserDataJson, $Currency, $Amount);
    if (!$stmt->execute()) {
        echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
        exit;
    }

    $ID = $stmt->insert_id;
    $stmt->close();

    echo json_encode(['ID' => $ID]);
    exit;
}

echo json_encode(['error' => 'Unknown function']);
exit;
