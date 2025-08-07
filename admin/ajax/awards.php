<?php
require_once('../../conf.php');

$action = $_GET['action'] ?? '';
if (!$action) exit;

switch ($action) {
case 'list':
    $res = mysqli_query($baza, "SELECT * FROM award ORDER BY name_ge ASC");
    while ($row = mysqli_fetch_assoc($res)) {
        echo '<div style="margin-bottom: 8px;">';
        echo htmlspecialchars($row['name_ge'] ?? '') . ' / ' . htmlspecialchars($row['name_en'] ?? '');
        echo ' <button onclick="updateAward(' . $row['ID'] . ')">Edit</button>';
        echo ' <button onclick="deleteAward(' . $row['ID'] . ')">Delete</button>';
        echo '</div>';
    }
    break;

    case 'add':
        $name_ge = trim($_POST['name_ge'] ?? '');
        $name_en = trim($_POST['name_en'] ?? '');
        if ($name_ge || $name_en) {
            $stmt = mysqli_prepare($baza, "INSERT INTO award (name_ge, name_en) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $name_ge, $name_en);
            mysqli_stmt_execute($stmt);
        }
        break;

    case 'edit':
        $id = intval($_POST['id'] ?? 0);
        $name_ge = trim($_POST['name_ge'] ?? '');
        $name_en = trim($_POST['name_en'] ?? '');
        if ($id && ($name_ge || $name_en)) {
            $stmt = mysqli_prepare($baza, "UPDATE award SET name_ge = ?, name_en = ? WHERE ID = ?");
            mysqli_stmt_bind_param($stmt, "ssi", $name_ge, $name_en, $id);
            mysqli_stmt_execute($stmt);
        }
        break;

    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        if ($id) {
            $stmt = mysqli_prepare($baza, "DELETE FROM award WHERE ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
        }
        break;
}