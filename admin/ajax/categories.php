<?php
require_once('../../conf.php');
$action = $_GET['action'] ?? '';
if (!$action) exit;

switch ($action) {
    case 'list':
        $res = mysqli_query($baza, "SELECT * FROM category ORDER BY Name ASC");
        while ($row = mysqli_fetch_assoc($res)) {
            echo '<div style="margin-bottom: 8px;">';
            echo htmlspecialchars($row['Name']);
            echo ' <button onclick="updateCategory(' . $row['ID'] . ')">Edit</button>';
            echo ' <button onclick="deleteCategory(' . $row['ID'] . ')">Delete</button>';
            echo '</div>';
        }
        break;

    case 'add':
        $name = trim($_POST['name'] ?? '');
        if ($name) {
            $stmt = mysqli_prepare($baza, "INSERT INTO category (Name) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
        }
        break;

    case 'edit':
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($id && $name) {
            $stmt = mysqli_prepare($baza, "UPDATE category SET Name = ? WHERE ID = ?");
            mysqli_stmt_bind_param($stmt, "si", $name, $id);
            mysqli_stmt_execute($stmt);
        }
        break;

    case 'delete':
        $id = intval($_POST['id'] ?? 0);
        if ($id) {
            $stmt = mysqli_prepare($baza, "DELETE FROM category WHERE ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
        }
        break;
}
?>
