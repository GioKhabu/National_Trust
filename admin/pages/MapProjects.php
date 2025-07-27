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

        if ($success) {
            // Redirect to avoid duplicate POST on refresh
            header("Location: ?Action=MapProjects");
            exit;
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


    <link rel="stylesheet" href="/admin/pages/MapProjects.css">
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
    <div class="manage-lookups">

<button type="button" onclick="openCategoryModal()">Manage Categories</button>
<button type="button" onclick="openAwardModal()">Manage Awards</button>
    </div>

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
    <select name="category" id="category" required>
        <option value="">-- Select Category --</option>
        <?php
        $categories = mysqli_query($baza, "SELECT * FROM category ORDER BY Name ASC");
        while ($cat = mysqli_fetch_assoc($categories)) {
            $selected = ($edit['category'] ?? '') == $cat['Name'] ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($cat['Name']) . "\" $selected>" . htmlspecialchars($cat['Name']) . "</option>";
        }
        ?>
    </select>
</div>
    <div class="form-group">
    <label for="award">Award</label>
    <select name="award" id="award" required>
        <option value="">-- Select Award --</option>
        <?php
        $awards = mysqli_query($baza, "SELECT * FROM award ORDER BY Name ASC");
        while ($cat = mysqli_fetch_assoc($awards)) {
            $selected = ($edit['award'] ?? '') == $cat['Name'] ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($cat['Name']) . "\" $selected>" . htmlspecialchars($cat['Name']) . "</option>";
        }
        ?>
    </select>
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
    <select name="year" id="year" required>
        <option value="">-- Select Year --</option>
        <?php
        $yearsResult = mysqli_query($baza, "SELECT Name FROM award_years ORDER BY Name ASC");
        while ($yearRow = mysqli_fetch_assoc($yearsResult)) {
            $yearVal = $yearRow['Name'];
            $selected = ($edit['year'] ?? '') == $yearVal ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($yearVal) . "\" $selected>" . htmlspecialchars($yearVal) . "</option>";
        }
        ?>
    </select>
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
<div id="categoryModal" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border:1px solid #ccc; z-index:9999;">
    <h3>Manage Categories</h3>
    <div id="categoryList"></div>

    <h4>Add New Category</h4>
    <input type="text" id="newCategoryName" placeholder="New category name">
    <button onclick="addCategory()">Add</button>

    <br><br>
    <button onclick="closeCategoryModal()">Close</button>
</div>
<div id="awardModal" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border:1px solid #ccc; z-index:9999;">
    <h3>Manage Awards</h3>
    <div id="awardList"></div>

    <h4>Add New Award</h4>
    <input type="text" id="newAwardName" placeholder="New award name">
    <button onclick="addAward()">Add</button>

    <br><br>
    <button onclick="closeAwardModal()">Close</button>
</div>
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

<script>
function openCategoryModal() {
    document.getElementById('categoryModal').style.display = 'block';
    fetchCategories();
}

function closeCategoryModal() {
    document.getElementById('categoryModal').style.display = 'none';
}

function fetchCategories() {
    fetch('/admin/ajax/categories.php?action=list')
        .then(res => res.text())
        .then(data => {
            document.getElementById('categoryList').innerHTML = data;
        });
}

function addCategory() {
    const name = document.getElementById('newCategoryName').value.trim();
    if (name === '') return;

    fetch('/admin/ajax/categories.php?action=add', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'name=' + encodeURIComponent(name)
    }).then(() => {
        document.getElementById('newCategoryName').value = '';
        fetchCategories();
        refreshCategorySelect(); // NEW
    });
}
function refreshCategorySelect() {
    fetch('/admin/ajax/categories.php?action=list')
        .then(res => res.text())
        .then(data => {
            // Parse names from the modal output
            const container = document.createElement('div');
            container.innerHTML = data;
            const options = Array.from(container.querySelectorAll('div'))
                .map(div => div.textContent.split('Edit')[0].trim());

            const select = document.getElementById('category');
            select.innerHTML = '<option value="">-- Select Category --</option>';
            options.forEach(name => {
                const opt = document.createElement('option');
                opt.value = name;
                opt.textContent = name;
                select.appendChild(opt);
            });
        });
}

function updateCategory(id) {
    const name = prompt("Edit category name:");
    if (!name) return;

    fetch('/admin/ajax/categories.php?action=edit', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id + '&name=' + encodeURIComponent(name)
    }).then(fetchCategories);
}

function deleteCategory(id) {
    if (!confirm("Delete this category?")) return;

    fetch('/admin/ajax/categories.php?action=delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
    }).then(fetchCategories);
}
</script>
<script>
function openAwardModal() {
    document.getElementById('awardModal').style.display = 'block';
    fetchAwards();
}

function closeAwardModal() {
    document.getElementById('awardModal').style.display = 'none';
}

function fetchAwards() {
    fetch('/admin/ajax/awards.php?action=list')
        .then(res => res.text())
        .then(data => {
            document.getElementById('awardList').innerHTML = data;
        });
}

function addAward() {
    const name = document.getElementById('newAwardName').value.trim();
    if (name === '') return;

    fetch('/admin/ajax/awards.php?action=add', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'name=' + encodeURIComponent(name)
    }).then(() => {
        document.getElementById('newAwardName').value = '';
        fetchAwards();
        refreshAwardSelect(); // Refresh dropdown if needed
    });
}

function refreshAwardSelect() {
    fetch('/admin/ajax/awards.php?action=list')
        .then(res => res.text())
        .then(data => {
            const container = document.createElement('div');
            container.innerHTML = data;
            const options = Array.from(container.querySelectorAll('div'))
                .map(div => div.textContent.split('Edit')[0].trim());

            const select = document.getElementById('award');
            select.innerHTML = '<option value="">-- Select Award --</option>';
            options.forEach(name => {
                const opt = document.createElement('option');
                opt.value = name;
                opt.textContent = name;
                select.appendChild(opt);
            });
        });
}

function updateAward(id) {
    const name = prompt("Edit award name:");
    if (!name) return;

    fetch('/admin/ajax/awards.php?action=edit', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id + '&name=' + encodeURIComponent(name)
    }).then(fetchAwards);
}

function deleteAward(id) {
    if (!confirm("Delete this award?")) return;

    fetch('/admin/ajax/awards.php?action=delete', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + id
    }).then(fetchAwards);
}
</script>
</body>
</html>

