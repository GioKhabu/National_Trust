<?php
header('Content-Type: application/json');
require_once('conf.php');

// Detect language from URL
$lang = isset($_GET['lang']) && $_GET['lang'] === 'en' ? 'en' : 'ge';
$nameField = ($lang === 'en') ? 'name_en' : 'name_ge';

// Build SQL with joins and language-specific column selection
$query = "
SELECT 
  p.id,
  p.project_name,
  p.author,
  p.year,
  p.link,
  p.x, p.y,
  r.id AS region_id, r.$nameField AS region,
  m.id AS municipality_id, m.$nameField AS municipality,
  c.id AS category_id, c.$nameField AS category,
  a.id AS award_id, a.$nameField AS award
FROM places p
LEFT JOIN regions r ON p.region = r.id
LEFT JOIN municipalities m ON p.municipality = m.id
LEFT JOIN category c ON p.category = c.id
LEFT JOIN award a ON p.award = a.id
";

$result = mysqli_query($baza, $query);

$places = [];

while ($row = mysqli_fetch_assoc($result)) {
    $places[] = $row;
}

echo json_encode($places);