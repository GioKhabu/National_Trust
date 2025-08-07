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
// error_log("MySQL error: " . mysqli_stmt_error($stmt));

// Handle add or update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $region = $_POST['region'];
    $municipality = $_POST['municipality'];
    $project_name_ge = trim($_POST['project_name_ge']);
    $project_name_en = trim($_POST['project_name_en']);
    $author_ge = trim($_POST['author_ge']);
    $author_en = trim($_POST['author_en']);
    $project_name = json_encode(["G" => $project_name_ge, "E" => $project_name_en], JSON_UNESCAPED_UNICODE);
    $author = json_encode(["G" => $author_ge, "E" => $author_en], JSON_UNESCAPED_UNICODE);
   $category_id = (!empty($_POST['category_id']) && is_numeric($_POST['category_id'])) ? intval($_POST['category_id']) : 0;
    $award_id = (!empty($_POST['award_id']) && is_numeric($_POST['award_id'])) ? intval($_POST['award_id']) : 0;
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
            mysqli_stmt_bind_param($stmt, "sssssisddsi", $region, $municipality, $project_name, $category_id, $award_id, $year, $link, $x, $y, $author, $edit_id);
            $success = mysqli_stmt_execute($stmt);
            $message = $success ? 'Project updated successfully.' : 'Failed to update project.';
        } else {
            $stmt = mysqli_prepare($baza, "INSERT INTO places (region, municipality, project_name, category, award, year, link, x, y, author) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sssssisdds", $region, $municipality, $project_name, $category_id, $award_id, $year, $link, $x, $y, $author);
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
// Preload region names
$regionNames = [];
$res = mysqli_query($baza, "SELECT id, name_ge FROM regions");
while ($row = mysqli_fetch_assoc($res)) {
    $regionNames[$row['id']] = $row['name_ge'];
}

// Preload municipality names
$municipalityNames = [];
$res = mysqli_query($baza, "SELECT id, name_ge FROM municipalities");
while ($row = mysqli_fetch_assoc($res)) {
    $municipalityNames[$row['id']] = $row['name_ge'];
}
// Preload award names
$awardNames = [];
$res = mysqli_query($baza, "SELECT ID, name_ge FROM award");
while ($row = mysqli_fetch_assoc($res)) {
    $awardNames[$row['ID']] = $row['name_ge'];
}
// Preload category names
$categoryNames = [];
$res = mysqli_query($baza, "SELECT ID, name_ge, name_en FROM category");
while ($row = mysqli_fetch_assoc($res)) {
    $categoryNames[$row['ID']] = $row['name_ge'];
}
?>
<?php if ($edit): ?>
<script>
const selectedRegionId = <?= (int)$edit['region'] ?>;
const selectedMunicipalityId = <?= (int)$edit['municipality'] ?>;
</script>
<?php endif; ?>

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
        <a href="?Action=MapProjects"
            style="margin-left: 20px; font-size: 14px; padding: 5px 10px; background: #28a745; color: #fff; border-radius: 4px; text-decoration: none;">Add
            New Project</a>
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
            $lang = 'ge';
            while ($r = mysqli_fetch_assoc($regions)) {
                $label = ($lang === 'en') ? $r['name_en'] : $r['name_ge'];
                $selected = ($edit['region'] ?? '') == $r['id'] ? 'selected' : '';
                echo "<option value=\"{$r['id']}\" $selected>" . htmlspecialchars($label) . "</option>";
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
            <label for="project_name_ge">Project Name (Georgian)</label>
            <input type="text" name="project_name_ge" id="project_name_ge"
                value="<?= isset($edit['project_name']) ? htmlspecialchars(json_decode($edit['project_name'], true)['G'] ?? '') : '' ?>"
                required />
        </div>
        <div class="form-group">
            <label for="project_name_en">Project Name (English)</label>
            <input type="text" name="project_name_en" id="project_name_en"
                value="<?= isset($edit['project_name']) ? htmlspecialchars(json_decode($edit['project_name'], true)['E'] ?? '') : '' ?>"
                required />
        </div>
        <div class="form-group">
            <label for="author_ge">Author (Georgian)</label>
            <input type="text" name="author_ge" id="author_ge"
                value="<?= isset($edit['author']) ? htmlspecialchars(json_decode($edit['author'], true)['G'] ?? '') : '' ?>" />
        </div>
        <div class="form-group">
            <label for="author_en">Author (English)</label>
            <input type="text" name="author_en" id="author_en"
                value="<?= isset($edit['author']) ? htmlspecialchars(json_decode($edit['author'], true)['E'] ?? '') : '' ?>" />
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select name="category_id" id="category" required>
                <option value="">-- Select Category --</option>
                <?php
        $categories = mysqli_query($baza, "SELECT ID, name_ge, name_en FROM category ORDER BY name_ge ASC");
        while ($cat = mysqli_fetch_assoc($categories)) {
            $label = ($lang === 'en') ? $cat['name_en'] : $cat['name_ge'];
            $selected = ($edit['category'] ?? '') == $cat['ID'] ? 'selected' : '';
            echo "<option value=\"{$cat['ID']}\" $selected>" . htmlspecialchars($label) . "</option>";
        }
        ?>
            </select>
        </div>

        <div class="form-group">
            <label for="award">Award</label>
            <select name="award_id" id="award" required>
                <option value="">-- Select Award --</option>
                <?php
        $awards = mysqli_query($baza, "SELECT * FROM award ORDER BY name_ge ASC");
        while ($cat = mysqli_fetch_assoc($awards)) {
            $selected = ($edit['award'] ?? '') == $cat['ID'] ? 'selected' : '';
            echo "<option value=\"" . $cat['ID'] . "\" $selected>" . htmlspecialchars($cat['name_ge']) . "</option>";
        }
        ?>
            </select>
        </div>


        <div class="form-group">
            <label for="link">Link</label>
            <input type="url" name="link" id="link" value="<?= htmlspecialchars($edit['link'] ?? '') ?>" />
        </div>

        <div class="form-group">
            <label for="x">X Coordinate</label>
            <input type="number" step="any" name="x" id="x" value="<?= htmlspecialchars($edit['x'] ?? '') ?>"
                required />
        </div>

        <div class="form-group">
            <label for="y">Y Coordinate</label>
            <input type="number" step="any" name="y" id="y" value="<?= htmlspecialchars($edit['y'] ?? '') ?>"
                required />
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
                <th>ID</th>
                <th>Region</th>
                <th>Municipality</th>
                <th>Project</th>
                <th>Author</th>
                <th>Category</th>
                <th>Award</th>
                <th>Year</th>
                <th>Link</th>
                <th>X</th>
                <th>Y</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($projects as $project): ?>
            <tr data-id="<?= htmlspecialchars($project['id'] ?? '') ?>">
                <td><?= htmlspecialchars($project['id'] ?? '') ?></td>

                <td><?= htmlspecialchars($regionNames[$project['region']] ?? '') ?></td>
                <td><?= htmlspecialchars($municipalityNames[$project['municipality']] ?? '') ?></td>
                <td>
                    <?= htmlspecialchars(json_decode($project['project_name'], true)['G'] ?? '') ?><br>
                    <small><?= htmlspecialchars(json_decode($project['project_name'], true)['E'] ?? '') ?></small>
                </td>
                <td>
                    <?= htmlspecialchars(json_decode($project['author'], true)['G'] ?? '') ?><br>
                    <small><?= htmlspecialchars(json_decode($project['author'], true)['E'] ?? '') ?></small>
                </td>
                <td><?= htmlspecialchars($categoryNames[$project['category']] ?? '') ?></td>
                <td><?= htmlspecialchars($awardNames[$project['award']] ?? '') ?></td>
                <td><?= htmlspecialchars($project['year'] ?? '') ?></td>
                <td><?= htmlspecialchars($project['link'] ?? '') ?></td>
                <td><?= htmlspecialchars($project['x'] ?? '') ?></td>
                <td><?= htmlspecialchars($project['y'] ?? '') ?></td>
                <td>
                    <a href="?Action=MapProjects&edit=<?= htmlspecialchars($project['id'] ?? '') ?>"
                        class="editBtn">Edit</a>
                    <button class="deleteBtn">Delete</button>
                </td>
            </tr>

            <?php endforeach; ?>
        </tbody>
    </table>
    <div id="categoryModal"
        style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border:1px solid #ccc; z-index:9999; max-width:400px;">
        <h3>Manage Categories</h3>
        <div id="categoryList"></div>

        <div id="categoryEditForm" style="display:none; margin-top:20px; border-top:1px solid #ccc; padding-top:10px;">
            <h4>Edit Category</h4>
            <input type="hidden" id="editCategoryId" />
            <label>
                Georgian name:<br>
                <input type="text" id="editCategoryNameGe" style="width:100%;" />
            </label><br><br>
            <label>
                English name:<br>
                <input type="text" id="editCategoryNameEn" style="width:100%;" />
            </label><br><br>
            <button onclick="saveCategoryEdit()">Save</button>
            <button onclick="cancelCategoryEdit()" style="margin-left:10px;">Cancel</button>
        </div>

        <h4>Add New Category</h4>
        <input type="text" id="newCategoryNameGe" placeholder="New category name (Georgian)"
            style="width:100%; margin-bottom:8px;">
        <input type="text" id="newCategoryNameEn" placeholder="New category name (English)"
            style="width:100%; margin-bottom:8px;">
        <button onclick="addCategory()">Add</button>

        <br><br>
        <button onclick="closeCategoryModal()">Close</button>
    </div>

    <div id="awardModal"
        style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#fff; padding:20px; border:1px solid #ccc; z-index:9999; max-width:400px;">
        <h3>Manage Awards</h3>
        <div id="awardList"></div>

        <div id="awardEditForm" style="display:none; margin-top:20px; border-top:1px solid #ccc; padding-top:10px;">
            <h4>Edit Award</h4>
            <input type="hidden" id="editAwardId" />
            <label>
                Georgian name:<br>
                <input type="text" id="editAwardNameGe" style="width:100%;" />
            </label><br><br>
            <label>
                English name:<br>
                <input type="text" id="editAwardNameEn" style="width:100%;" />
            </label><br><br>
            <button onclick="saveAwardEdit()">Save</button>
            <button onclick="cancelAwardEdit()" style="margin-left:10px;">Cancel</button>
        </div>

        <h4>Add New Award</h4>
        <input type="text" id="newAwardNameGe" placeholder="New award name (Georgian)"
            style="width:100%; margin-bottom:8px;">
        <input type="text" id="newAwardNameEn" placeholder="New award name (English)"
            style="width:100%; margin-bottom:8px;">
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
                $('#municipality').html('<option value="">-- Select Municipality --</option>').prop(
                    'disabled', true);
                return;
            }

            $.ajax({
                url: '/admin/ajax/get_municipalities.php',
                method: 'POST',
                data: {
                    region: selectedRegion,
                    lang: 'ge' // or 'en'
                },
                success: function(response) {
                    $('#municipality').html(response).prop('disabled', false);
                }
            });
        });

        <?php if (isset($edit['region'], $edit['municipality'])): ?>
        $.ajax({
            url: '/admin/ajax/get_municipalities.php',
            method: 'POST',
            data: {
                region: '<?= (int)$edit['region'] ?>',
                selected: '<?= (int)$edit['municipality'] ?>',
                lang: 'ge' // or 'en' if your interface is in English
            },
            success: function(response) {
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
        cancelCategoryEdit();
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
        const name_ge = document.getElementById('newCategoryNameGe').value.trim();
        const name_en = document.getElementById('newCategoryNameEn').value.trim();
        if (name_ge === '' && name_en === '') return;

        const body = new URLSearchParams();
        body.append('name_ge', name_ge);
        body.append('name_en', name_en);

        fetch('/admin/ajax/categories.php?action=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body.toString()
        }).then(() => {
            document.getElementById('newCategoryNameGe').value = '';
            document.getElementById('newCategoryNameEn').value = '';
            fetchCategories();
            refreshCategorySelect();
        });
    }

    function refreshCategorySelect() {
        fetch('/admin/ajax/categories.php?action=list')
            .then(res => res.text())
            .then(data => {
                const container = document.createElement('div');
                container.innerHTML = data;
                const options = Array.from(container.querySelectorAll('div'))
                    .map(div => div.textContent.split('Edit')[0].trim());

                const select = document.getElementById('category');
                if (!select) return;
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
        const categoryDiv = document.querySelector(`#categoryList div button[onclick="updateCategory(${id})"]`)
            ?.parentNode;
        if (!categoryDiv) return alert("Category not found");

        let text = categoryDiv.textContent || '';
        text = text.replace(/\bEdit\b/, '').replace(/\bDelete\b/, '').trim();

        const parts = text.split(' / ');
        const name_ge = parts[0] || '';
        const name_en = parts[1] || '';

        document.getElementById('categoryEditForm').style.display = 'block';
        document.getElementById('editCategoryId').value = id;
        document.getElementById('editCategoryNameGe').value = name_ge;
        document.getElementById('editCategoryNameEn').value = name_en;
    }

    function saveCategoryEdit() {
        const id = document.getElementById('editCategoryId').value;
        const name_ge = document.getElementById('editCategoryNameGe').value.trim();
        const name_en = document.getElementById('editCategoryNameEn').value.trim();

        if (!id) return alert("No category selected");
        if (name_ge === '' && name_en === '') return alert("Enter at least one name");

        const body = new URLSearchParams();
        body.append('id', id);
        body.append('name_ge', name_ge);
        body.append('name_en', name_en);

        fetch('/admin/ajax/categories.php?action=edit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body.toString()
        }).then(() => {
            fetchCategories();
            cancelCategoryEdit();
        });
    }

    function cancelCategoryEdit() {
        document.getElementById('categoryEditForm').style.display = 'none';
        document.getElementById('editCategoryId').value = '';
        document.getElementById('editCategoryNameGe').value = '';
        document.getElementById('editCategoryNameEn').value = '';
    }

    function deleteCategory(id) {
        if (!confirm("Delete this category?")) return;

        fetch('/admin/ajax/categories.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + id
        }).then(fetchCategories);
    }
    </script>


    <script>
    function openAwardModal() {
        document.getElementById('awardModal').style.display = 'block';
        fetchAwards();
        cancelAwardEdit();
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
        const name_ge = document.getElementById('newAwardNameGe').value.trim();
        const name_en = document.getElementById('newAwardNameEn').value.trim();
        if (name_ge === '' && name_en === '') return;

        const body = new URLSearchParams();
        body.append('name_ge', name_ge);
        body.append('name_en', name_en);

        fetch('/admin/ajax/awards.php?action=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body.toString()
        }).then(() => {
            document.getElementById('newAwardNameGe').value = '';
            document.getElementById('newAwardNameEn').value = '';
            fetchAwards();
            refreshAwardSelect();
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
        // Find the award div content to get current names
        // Better to fetch fresh from server or parse from list div
        // For simplicity, let's parse from displayed text
        const awardDiv = document.querySelector('#awardList div button[onclick="updateAward(' + id + ')"]')?.parentNode;


        if (!awardDiv) return alert("Award not found");

        // The text looks like "GeorgianName / EnglishName"
        let text = awardDiv.textContent || '';
        // Remove Edit and Delete button text from it:
        text = text.replace(/\bEdit\b/, '').replace(/\bDelete\b/, '').trim();

        // Split by ' / ' to separate ge and en
        const parts = text.split(' / ');
        const name_ge = parts[0] || '';
        const name_en = parts[1] || '';

        // Show edit form
        document.getElementById('awardEditForm').style.display = 'block';
        document.getElementById('editAwardId').value = id;
        document.getElementById('editAwardNameGe').value = name_ge;
        document.getElementById('editAwardNameEn').value = name_en;
    }


    function deleteAward(id) {
        if (!confirm("Delete this award?")) return;

        fetch('/admin/ajax/awards.php?action=delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + id
        }).then(fetchAwards);
    }

    function saveAwardEdit() {
        const id = document.getElementById('editAwardId').value;
        const name_ge = document.getElementById('editAwardNameGe').value.trim();
        const name_en = document.getElementById('editAwardNameEn').value.trim();

        if (!id) return alert("No award selected");
        if (name_ge === '' && name_en === '') return alert("Enter at least one name");

        const body = new URLSearchParams();
        body.append('id', id);
        body.append('name_ge', name_ge);
        body.append('name_en', name_en);

        fetch('/admin/ajax/awards.php?action=edit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body.toString()
        }).then(() => {
            fetchAwards();
            cancelAwardEdit();
        });
    }

    function cancelAwardEdit() {
        document.getElementById('awardEditForm').style.display = 'none';
        document.getElementById('editAwardId').value = '';
        document.getElementById('editAwardNameGe').value = '';
        document.getElementById('editAwardNameEn').value = '';
    }
    </script>

</body>

</html>