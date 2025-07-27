<?php
header('Content-Type: application/json');
require_once('conf.php');

$query = "SELECT id, region, municipality, project_name, category, award, year, link, x, y, author FROM places";
$result = mysqli_query($baza, $query);

$places = [];

while ($row = mysqli_fetch_assoc($result)) {
    $places[] = $row;
}

echo json_encode($places);
