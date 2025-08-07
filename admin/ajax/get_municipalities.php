<?php
require_once('../../conf.php');
header('Content-Type: text/html; charset=utf-8');

$lang = $_POST['lang'] ?? 'ge'; // fallback to 'ge' if not set
$selected_id = isset($_POST['selected']) ? (int)$_POST['selected'] : 0;

if (isset($_POST['region'])) {
    $region_id = intval($_POST['region']);

    $query = "SELECT id, name_ge, name_en FROM municipalities 
              WHERE region_id = $region_id 
              ORDER BY name_ge ASC";

    $result = mysqli_query($baza, $query);

    if (!$result) {
        echo "<option value=''>Query error</option>";
        exit;
    }

    echo '<option value="">-- Select Municipality --</option>';

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $display = ($lang === 'en') ? $row['name_en'] : $row['name_ge'];
            $selected = ((int)$row['id'] === $selected_id) ? ' selected' : '';
            echo '<option value="' . (int)$row['id'] . '"' . $selected . '>' . htmlspecialchars($display) . '</option>';
        }
    } else {
        echo '<option value="">No municipalities found</option>';
    }
}
?>