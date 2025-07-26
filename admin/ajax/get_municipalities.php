<?php
require_once('../../conf.php');
header('Content-Type: text/html; charset=utf-8');

if (isset($_POST['region'])) {
    $region = mysqli_real_escape_string($baza, $_POST['region']);

    // Log region to check encoding
    error_log("Region received: " . $region);

    $query = "SELECT m.name_ge FROM municipalities m 
              JOIN regions r ON m.region_id = r.id 
              WHERE r.name_ge = '$region' 
              ORDER BY m.name_ge ASC";

    // Log the query for debugging
    error_log("Query: " . $query);

    $result = mysqli_query($baza, $query);

    if (!$result) {
        error_log("Query error: " . mysqli_error($baza));
        echo "<option value=''>Query error</option>";
        exit;
    }

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . htmlspecialchars($row['name_ge']) . '">' . htmlspecialchars($row['name_ge']) . '</option>';
        }
    } else {
        echo '<option value="">No municipalities found</option>';
    }
}
?>
