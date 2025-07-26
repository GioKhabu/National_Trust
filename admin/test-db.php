<?php
header_remove('Location');
include_once('../conf.php');

$result = mysqli_query($baza, "SHOW TABLES LIKE 'places'");
if ($result && mysqli_num_rows($result) > 0) {
    echo "Table 'places' exists.";
} else {
    echo "Table 'places' does NOT exist.";
}
?>