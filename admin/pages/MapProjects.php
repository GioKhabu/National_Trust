<?php
$message = '';
$success = false;

// Handle deletion via AJAX
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = mysqli_prepare($baza, "DELETE FROM places WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    
    // Redirect back to list page to avoid resubmission on refresh
    header("Location: ?Action=MapProjects");
    exit;
}

// Handle add or update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $region = $_POST['region'];
    $municipality = $_POST['municipality'];
    $project_name = $_POST['project_name'];
    $category = $_POST['category'];
    $award = $_POST['award'];
    $author = $_POST['author'];
    $year = $_POST['year'];
    $link = $_POST['link'];
    $x = $_POST['x'];
    $y = $_POST['y'];

    if (!$region || !$municipality || !$project_name || !$x || !$y) {
        $message = 'Please fill in all required fields.';
    } else {
        if (!empty($_POST['edit_id'])) {
            $edit_id = intval($_POST['edit_id']);
          $stmt = mysqli_prepare($baza, "UPDATE places SET region=?, municipality=?, project_name=?, category=?, award=?, year=?, link=?, x=?, y=?, author=? WHERE id=?");
mysqli_stmt_bind_param($stmt, "sssssisddsi", $region, $municipality, $project_name, $category, $award, $year, $link, $x, $y, $author, $edit_id);
$success = mysqli_stmt_execute($stmt);

$message = $success ? 'Project updated successfully.' : 'Failed to update project.';

        } else {
           $stmt = mysqli_prepare($baza, "INSERT INTO places (region, municipality, project_name, category, award, year, link, x, y, author) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sssssisdds", $region, $municipality, $project_name, $category, $award, $year, $link, $x, $y, $author);

            $success = mysqli_stmt_execute($stmt);
            $message = $success ? 'Project added successfully.' : 'Failed to add project.';
        }
    }
}

// Load record for editing if needed
$edit = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $res = mysqli_query($baza, "SELECT * FROM places WHERE id = $edit_id");
    if ($res) {
        $edit = mysqli_fetch_assoc($res);
    }
}

// Fetch all projects for the list
$result = mysqli_query($baza, "SELECT * FROM places ORDER BY id DESC");
$projects = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $projects[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Map Projects</title>


    <style>
        .message { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
        th { background: #eee; }
        button { cursor: pointer; }
    </style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
    <h1>
    <?php if (isset($edit['id'])): ?>
        Edit Project ID: <?= $edit['id'] ?>
        <a href="?Action=MapProjects" style="margin-left: 20px; font-size: 14px; padding: 5px 10px; background: #28a745; color: #fff; border-radius: 4px; text-decoration: none;">Add New Project</a>
    <?php else: ?>
        Add New Project
    <?php endif; ?>
</h1>

    <?php if ($message): ?>
        <div class="message <?= $success ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <script>
            setTimeout(() => document.querySelector('.message').remove(), 4000);
        </script>
    <?php endif; ?>

    <form id="projectForm" method="post" action="">
    <div class="form-group">
    <label for="region">Region</label>
    <select name="region" id="region" required>
        <option value="">-- Select Region --</option>
        <?php
        $regions = mysqli_query($baza, "SELECT * FROM regions ORDER BY name_ge ASC");
        while ($r = mysqli_fetch_assoc($regions)) {
            $selected = ($edit['region'] ?? '') == $r['name_ge'] ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($r['name_ge']) . "\" $selected>" . htmlspecialchars($r['name_ge']) . "</option>";
        }
        ?>
    </select>
</div>

    <div class="form-group">
    <label for="municipality">Municipality</label>
    <select name="municipality" id="municipality" required disabled>
        <option value="">-- Select Municipality --</option>
    </select>
</div>

    <div class="form-group">
        <label for="project">Project</label>
        <input type="text" name="project_name" id="project_name" value="<?= htmlspecialchars($edit['project_name'] ?? '') ?>" required />
    </div>

    <div class="form-group">
        <label for="category">Category</label>
        <input type="text" name="category" id="category" value="<?= htmlspecialchars($edit['category'] ?? '') ?>" required />
    </div>

    <div class="form-group">
        <label for="award">Award</label>
        <input type="text" name="award" id="award" value="<?= htmlspecialchars($edit['award'] ?? '') ?>" />
    </div>

    <div class="form-group">
    <label for="author">Author</label>
    <input type="text" name="author" id="author" value="<?= htmlspecialchars($edit['author'] ?? '') ?>" />
</div>


    <div class="form-group">
        <label for="link">Link</label>
        <input type="url" name="link" id="link" value="<?= htmlspecialchars($edit['link'] ?? '') ?>" />
    </div>

    <div class="form-group">
        <label for="x">X Coordinate</label>
        <input type="number" step="any" name="x" id="x" value="<?= htmlspecialchars($edit['x'] ?? '') ?>" required />
    </div>

    <div class="form-group">
        <label for="y">Y Coordinate</label>
        <input type="number" step="any" name="y" id="y" value="<?= htmlspecialchars($edit['y'] ?? '') ?>" required />
    </div>

    <div class="form-group">
    <label for="year">Year</label>
    <input type="number" name="year" id="year" value="<?= htmlspecialchars($edit['year'] ?? '') ?>" required />
    </div>

    <?php if (isset($edit['id'])): ?>
        <input type="hidden" name="edit_id" value="<?= $edit['id'] ?>">
    <?php endif; ?>

    <button type="submit" name="save"><?= isset($edit) ? 'Update' : 'Add' ?> Project</button>
</form>


    <h2>Project List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th><th>Region</th><th>Municipality</th><th>Project</th><th>Category</th><th>Award</th><th>Author</th><th>Year</th><th>Link</th><th>X</th><th>Y</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($projects as $project): ?>
            <tr data-id="<?= htmlspecialchars($project['id'] ?? '') ?>">
    <td><?= htmlspecialchars($project['id'] ?? '') ?></td>
    <td><?= htmlspecialchars($project['region'] ?? '') ?></td>
    <td><?= htmlspecialchars($project['municipality'] ?? '') ?></td>
    <td><?= htmlspecialchars($project['project_name'] ?? '') ?></td>
    <td><?= htmlspecialchars($project['category'] ?? '') ?></td>
    <td><?= htmlspecialchars($project['award'] ?? '') ?></td>
    <td><?= htmlspecialchars($project['author'] ?? '') ?></td>
    <td><?= htmlspecialchars($project['year'] ?? '') ?></td>
    <td><?= htmlspecialchars($project['link'] ?? '') ?></td>
    <td><?= htmlspecialchars($project['x'] ?? '') ?></td>
    <td><?= htmlspecialchars($project['y'] ?? '') ?></td>
    <td>
        <a href="?Action=MapProjects&edit=<?= htmlspecialchars($project['id'] ?? '') ?>" class="editBtn">Edit</a>
        <button class="deleteBtn">Delete</button>
    </td>
</tr>

        <?php endforeach; ?>
        </tbody>
    </table>

<script>
$(document).ready(function() {
    $('.deleteBtn').on('click', function() {
        if (!confirm('Are you sure you want to delete this project?')) return;
        const id = $(this).closest('tr').data('id');
        // Redirect with delete param
        location.href = '?Action=MapProjects&delete=' + id;
    });
});
</script>
<script>
    
$(document).ready(function() {
    $('#region').on('change', function() {
        const selectedRegion = $(this).val();
        $('#municipality').prop('disabled', true).html('<option>Loading...</option>');

        if (!selectedRegion) {
            $('#municipality').html('<option value="">-- Select Municipality --</option>').prop('disabled', true);
            return;
        }

        $.ajax({
            url: '/admin/ajax/get_municipalities.php',
            method: 'POST',
            data: { region: selectedRegion },
            success: function(response) {
                $('#municipality').html(response).prop('disabled', false);
            },
            error: function() {
                $('#municipality').html('<option>Error loading</option>');
            }
        });
    });

    <?php if (isset($edit['region'], $edit['municipality'])): ?>
    // Preload municipalities for edit mode
    $.ajax({
        url: '/admin/ajax/get_municipalities.php',
        method: 'POST',
        data: { 
            region: '<?= addslashes($edit['region']) ?>', 
            selected: '<?= addslashes($edit['municipality']) ?>' 
        },
        success: function(response) {
            console.log("AJAX response:", response); // <-- See what it prints
            $('#municipality').html(response).prop('disabled', false);
        }
    });
    <?php endif; ?>
});
</script>



</body>
</html>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        padding: 0;
    }
h1 {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 24px;
    margin-bottom: 20px;
}

h1 a {
    font-size: 14px;
    padding: 6px 12px;
    background: #28a745;
    color: #fff;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.3s;
}

h1 a:hover {
    background: #218838;
}
    form#projectForm {
        width: 90%;
        height: auto;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 15px;
        padding: 20px;
        margin-bottom: 30px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: #f9f9f9;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        margin-bottom: 15px;
    }

    .form-group label {
        margin-bottom: 5px;
        font-weight: bold;
    }

    .form-group input {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 14px;
    }

    button[type="submit"] {
        padding: 10px 10px;
        background-color: #007bff;
        height: 40px;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button[type="submit"]:hover {
        background-color: #0056b3;
    }
.editBtn {
    background: #28a745;   /* Bootstrap green */
    color: white;
    padding: 6px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
    display: inline-block;
    transition: background-color 0.3s;
}

.editBtn:hover {
    background: #218838;  /* Darker green on hover */
}
    .message {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .success {
        background-color: #d4edda;
        color: #155724;
    }

    .error {
        background-color: #f8d7da;
        color: #721c24;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        overflow-x: auto;
    }

    th, td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: left;
        font-size: 14px;
    }

    th {
        background-color: #f0f0f0;
    }

    .deleteBtn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 6px 10px;
        border-radius: 4px;
        cursor: pointer;
    }

    .deleteBtn:hover {
        background: #c82333;
    }

    @media (max-width: 600px) {
        form#projectForm {
            padding: 15px;
        }

        .form-group {
            flex-direction: column;
        }

        table, thead, tbody, th, td, tr {
            display: block;
        }

        thead {
            display: none;
        }

        tr {
            margin-bottom: 15px;
        }

        td {
            position: relative;
            padding-left: 50%;
        }

        td::before {
            position: absolute;
            left: 10px;
            top: 10px;
            white-space: nowrap;
            font-weight: bold;
        }

        td:nth-of-type(1)::before { content: "ID"; }
        td:nth-of-type(2)::before { content: "Region"; }
        td:nth-of-type(3)::before { content: "Municipality"; }
        td:nth-of-type(4)::before { content: "Project"; }
        td:nth-of-type(5)::before { content: "Category"; }
        td:nth-of-type(6)::before { content: "Award"; }
        td:nth-of-type(7)::before { content: "Year"; }
        td:nth-of-type(8)::before { content: "Link"; }
        td:nth-of-type(9)::before { content: "X"; }
        td:nth-of-type(10)::before { content: "Y"; }
        td:nth-of-type(11)::before { content: "Actions"; }
    }
</style>
