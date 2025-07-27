<?php
require_once('../../conf.php');

$action = $_GET['action'] ?? '';
if (!$action) exit;

switch ($action) {
    case 'list':
        $res = mysqli_query($baza, "SELECT * FROM award ORDER BY Name ASC");
        while ($row = mysqli_fetch_assoc($res)) {
            echo '<div style="margin-bottom: 8px;">';
            echo htmlspecialchars($row['Name']);
            echo ' <button onclick="updateAward(' . $row['ID'] . ')">Edit</button>';
            echo ' <button onclick="deleteAward(' . $row['ID'] . ')">Delete</button>';
            echo '</div>';
        }
        break;

    case 'add':
        $name = trim($_POST['name'] ?? '');
        if ($name) {
            $stmt = mysqli_prepare($baza, "INSERT INTO award (Name) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $name);
            mysqli_stmt_execute($stmt);
        }
        break;

    case 'edit':
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($id && $name) {
            $stmt = mysqli_prepare($baza, "UPDATE award SET Name = ? WHERE ID = ?");
            mysqli_stmt_bind_param($stmt, "si", $name, $id);
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
?>
