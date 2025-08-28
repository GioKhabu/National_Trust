<?php
$lang = (strpos($_SERVER['REQUEST_URI'], '/en/') !== false) ? 'name_en' : 'name_ge';

$categories = mysqli_query($baza, "
    SELECT DISTINCT c.$lang AS category_name
    FROM category c
    INNER JOIN places p ON p.category = c.id
    WHERE p.category IS NOT NULL AND p.category != ''
    ORDER BY category_name
");
$regions = mysqli_query($baza, "
    SELECT DISTINCT r.$lang AS region_name
    FROM regions r
    INNER JOIN places p ON p.region = r.id
    WHERE p.region IS NOT NULL AND p.region != ''
    ORDER BY region_name
");

$municipalities = mysqli_query($baza, "
    SELECT DISTINCT m.$lang AS municipality_name
    FROM municipalities m
    INNER JOIN places p ON p.municipality = m.id
    WHERE p.municipality IS NOT NULL AND p.municipality != ''
    ORDER BY municipality_name
");

$awards = mysqli_query($baza, "
    SELECT DISTINCT a.$lang AS award_name
    FROM award a
    INNER JOIN places p ON p.award = a.id
    WHERE p.award IS NOT NULL AND p.award != ''
    ORDER BY award_name
");

$years = mysqli_query($baza, "
    SELECT DISTINCT year 
    FROM places 
    WHERE year IS NOT NULL 
    ORDER BY year
");
?>

<!-- MAP SECTION -->
<div class="map-section">

    <!-- Filter Toggle Button (Mobile Only) -->
    <button id="toggleFilterMenu" class="filter-toggle-btn" aria-label="Toggle filters">
        <span class="filter-icon-wrap">
            <span class="icon open"><?php echo file_get_contents(__DIR__ . '/icons/icon-filter.svg'); ?></span>
            <span class="icon close" style="display: none;">&#x2715;</span>
        </span>
    </button>

    <!-- Unified Filter Block -->
    <div id="mapFilters" class="filter-container">
        <!-- CATEGORY -->
        <div class="filter-wrapper">
            <select id="filterCategory" class="filter-select"
                data-label="<?= $lang === 'name_en' ? 'Category' : 'კატეგორია'; ?>">
                <option value=""><?= $lang === 'name_en' ? 'Category' : 'კატეგორია'; ?></option>
                <?php
    $categories = mysqli_query($baza, "
        SELECT DISTINCT c.id, c.$lang AS name
        FROM category c
        INNER JOIN places p ON p.category = c.id
        ORDER BY name
    ");
    while ($row = mysqli_fetch_assoc($categories)): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <span class="icon-wrap">
                <span class="icon open"><?php echo file_get_contents(__DIR__ . '/icons/icon-filter.svg'); ?></span>
                <span class="icon close" title="ფილტრის წაშლა">&#x2715;</span>
            </span>
        </div>

        <!-- AWARD -->
        <div class="filter-wrapper">
            <select id="filterAward" class="filter-select"
                data-label="<?= $lang === 'name_en' ? 'Award' : 'დაჯილდოება'; ?>">
                <option value=""><?= $lang === 'name_en' ? 'Award' : 'დაჯილდოება'; ?></option>
                <?php
    $awards = mysqli_query($baza, "
        SELECT DISTINCT a.id, a.$lang AS name
        FROM award a
        INNER JOIN places p ON p.award = a.id
        ORDER BY name
    ");
    while ($row = mysqli_fetch_assoc($awards)): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <span class="icon-wrap">
                <span class="icon open"><?php echo file_get_contents(__DIR__ . '/icons/icon-filter.svg'); ?></span>
                <span class="icon close" title="ფილტრის წაშლა">&#x2715;</span>
            </span>
        </div>

        <!-- YEAR -->
        <div class="filter-wrapper">
            <select id="filterYear" class="filter-select" data-label="<?= $lang === 'name_en' ? 'Year' : 'წელი'; ?>">
                <option value=""><?= $lang === 'name_en' ? 'Year' : 'წელი'; ?></option>
                <?php while ($row = mysqli_fetch_assoc($years)): ?>
                <option value="<?= htmlspecialchars($row['year']) ?>"><?= htmlspecialchars($row['year']) ?></option>
                <?php endwhile; ?>
            </select>
            <span class="icon-wrap">
                <span class="icon open"><?php echo file_get_contents(__DIR__ . '/icons/icon-filter.svg'); ?></span>
                <span class="icon close" title="ფილტრის წაშლა">&#x2715;</span>
            </span>
        </div <!-- REGION -->
        <div class="filter-wrapper">
            <select id="filterRegion" class="filter-select"
                data-label="<?= $lang === 'name_en' ? 'Region' : 'რეგიონი'; ?>">
                <option value=""><?= $lang === 'name_en' ? 'Region' : 'რეგიონი'; ?></option>
                <?php
    $regions = mysqli_query($baza, "
        SELECT DISTINCT r.id, r.$lang AS name
        FROM regions r
        INNER JOIN places p ON p.region = r.id
        ORDER BY name
    ");
    while ($row = mysqli_fetch_assoc($regions)): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <span class="icon-wrap">
                <span class="icon open"><?php echo file_get_contents(__DIR__ . '/icons/icon-filter.svg'); ?></span>
                <span class="icon close" title="ფილტრის წაშლა">&#x2715;</span>
            </span>
        </div>

        <!-- MUNICIPALITY -->
        <div class="filter-wrapper">
            <select id="filterMunicipality" class="filter-select"
                data-label="<?= $lang === 'name_en' ? 'Municipality' : 'მუნიციპალიტეტი'; ?>">
                <option value=""><?= $lang === 'name_en' ? 'Municipality' : 'მუნიციპალიტეტი'; ?></option>
                <?php
    $municipalities = mysqli_query($baza, "
        SELECT DISTINCT m.id, m.$lang AS name
        FROM municipalities m
        INNER JOIN places p ON p.municipality = m.id
        ORDER BY name
    ");
    while ($row = mysqli_fetch_assoc($municipalities)): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
            <span class="icon-wrap">
                <span class="icon open"><?php echo file_get_contents(__DIR__ . '/icons/icon-filter.svg'); ?></span>
                <span class="icon close" title="ფილტრის წაშლა">&#x2715;</span>
            </span>
        </div>
    </div>

    <!-- MAP -->
    <div id="googleMap" style="width: 100%; height: 520px; margin-top: 20px;"></div>
</div>

<script>
document.getElementById('toggleFilterMenu').addEventListener('click', function() {
    const filters = document.getElementById('mapFilters');
    const openIcon = this.querySelector('.icon.open');
    const closeIcon = this.querySelector('.icon.close');
    const isOpen = filters.classList.toggle('open');
    openIcon.style.display = isOpen ? 'none' : 'inline-flex';
    closeIcon.style.display = isOpen ? 'inline' : 'none';
});
</script>
<script>
let map;
let markers = [];
let allPlaces = [];
const lang = window.location.pathname.includes('/en/') ? 'E' : 'G';

function safeTranslate(jsonString) {
    try {
        const obj = JSON.parse(jsonString);
        return obj[lang] || '';
    } catch (e) {
        return jsonString;
    }
}

function initMap() {
    map = new google.maps.Map(document.getElementById("googleMap"), {
        center: {
            lat: 42.4,
            lng: 43.5
        },
        zoom: 7,
        mapId: "<?php echo $googleMapID; ?>",
        mapTypeControl: false,
        streetViewControl: false,
        zoomControl: false,
        fullscreenControl: true,
    });

    const langParam = window.location.pathname.includes('/en/') ? 'en' : 'ge';
    fetch('/get_places.php?lang=' + langParam)
        .then(response => response.json())
        .then(data => {
            allPlaces = data;
            setupFilterIcons();
            showMarkers(data);
        })
        .catch(console.error);
}


function clearMarkers() {
    markers.forEach(m => m.map = null);
    markers = [];
}

function createRedCircle() {
    const div = document.createElement('div');
    div.style.width = '14px';
    div.style.height = '14px';
    div.style.borderRadius = '50%';
    div.style.backgroundColor = 'red';
    div.style.border = '2px solid white';
    div.style.boxShadow = '0 0 2px rgba(0,0,0,0.6)';
    return div;
}

let activeInfoWindow = null;

function showMarkers(data) {
    clearMarkers();

    data.forEach(place => {
        const {
            AdvancedMarkerElement
        } = google.maps.marker;

        const marker = new AdvancedMarkerElement({
            position: {
                lat: parseFloat(place.x),
                lng: parseFloat(place.y)
            },
            map: map,
            content: createRedCircle(),
            title: place.project_name
        });

        const content = `
          <div class="custom-infowindow">
            <h3>${safeTranslate(place.project_name)}</h3>
            <p><strong>${lang === 'E' ? 'Region' : 'რეგიონი'}:</strong> ${place.region}</p>
            <p><strong>${lang === 'E' ? 'Municipality' : 'მუნიციპალიტეტი'}:</strong> ${place.municipality}</p>
            <p><strong>${lang === 'E' ? 'Category' : 'კატეგორია'}:</strong> ${place.category}</p>
            <p><strong>${lang === 'E' ? 'Award' : 'ჯილდო'}:</strong> ${place.award}</p>
            <p><strong>${lang === 'E' ? 'Year' : 'წელი'}:</strong> ${place.year}</p>
            <p><strong>${lang === 'E' ? 'Author' : 'ავტორი'}:</strong> ${safeTranslate(place.author)}</p>
            <p><a href="${place.link}" target="_blank" rel="noopener" class="infowindow-btn">
              ${lang === 'E' ? 'Link' : 'ბმული'}
            </a></p>
          </div>
        `;

        const infowindow = new google.maps.InfoWindow({
            content
        });

        marker.addListener('click', () => {
            // Close previous infoWindow if still open
            if (activeInfoWindow) {
                activeInfoWindow.close();
            }
            infowindow.open(map, marker);
            activeInfoWindow = infowindow;
        });

        markers.push(marker);
    });
}

function getLabelByValue(allData, fieldKey, displayKey, value) {
    const match = allData.find(p => String(p[fieldKey]) === String(value));
    if (!match) return null;

    // For year, just return the year value directly, no translation needed
    if (displayKey === 'year') {
        return match[displayKey];
    }

    const rawLabel = match[displayKey];
    return typeof rawLabel === 'string' ? safeTranslate(rawLabel) : rawLabel;
}

function updateFilterOptions(selectId, fieldKey, displayKey, data, previousValue, triggeredBy) {
    const select = document.getElementById(selectId);
    const wrapper = select.closest('.filter-wrapper');

    // Clear existing options
    select.innerHTML = '';

    // Create the default option
    let defaultLabel;
    switch (selectId) {
        case 'filterCategory':
            defaultLabel = (lang === 'E' ? 'Category' : 'კატეგორია');
            break;
        case 'filterAward':
            defaultLabel = (lang === 'E' ? 'Award' : 'ჯილდო');
            break;
        case 'filterYear':
            defaultLabel = (lang === 'E' ? 'Year' : 'წელი');
            break;
        case 'filterRegion':
            defaultLabel = (lang === 'E' ? 'Region' : 'რეგიონი');
            break;
        case 'filterMunicipality':
            defaultLabel = (lang === 'E' ? 'Municipality' : 'მუნიციპალიტეტი');
            break;
        default:
            defaultLabel = '';
    }

    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = defaultLabel;
    select.appendChild(defaultOption);

    if (previousValue) {
        // If a filter value is selected, show only that value and disable select
        const label = getLabelByValue(allPlaces, fieldKey, displayKey, previousValue);
        if (label) {
            const opt = document.createElement('option');
            opt.value = previousValue;
            opt.textContent = label;
            opt.selected = true;
            select.appendChild(opt);
        }
        select.disabled = true;
        wrapper.classList.add('filtered'); // add filtered class so close icon shows

        return;
    }

    // Otherwise populate options from filtered data and enable select
    const values = [...new Set(data.map(p => p[fieldKey]).filter(v => v !== null && v !== ''))];
    values.sort();
    for (const value of values) {
        const label = getLabelByValue(allPlaces, fieldKey, displayKey, value);
        if (label) {
            const opt = document.createElement('option');
            opt.value = value;
            opt.textContent = label;
            select.appendChild(opt);
        }
    }
    select.disabled = false;
    wrapper.classList.remove('filtered');
}

function setupFilterIcons() {
    ['filterCategory', 'filterAward', 'filterYear', 'filterRegion', 'filterMunicipality'].forEach(id => {
        const select = document.getElementById(id);
        const wrapper = select.closest('.filter-wrapper');
        const closeIcon = wrapper.querySelector('.icon.close');

        select.addEventListener('change', () => {
            if (select.value) {
                // Disable select after choosing an option
                select.disabled = true;
                wrapper.classList.add('filtered');
            }
            applyFilters(id);
        });

        closeIcon.addEventListener('click', () => {
            // Reset select and re-enable it
            select.value = '';
            select.disabled = false;
            wrapper.classList.remove('filtered');
            applyFilters(id);
        });

        // Initialize filtered class based on initial value (for page reload)
        if (select.value) {
            select.disabled = true;
            wrapper.classList.add('filtered');
        } else {
            select.disabled = false;
            wrapper.classList.remove('filtered');
        }
    });
}

function applyFilters(triggeredBy = null) {
    const cat = document.getElementById('filterCategory').value;
    const award = document.getElementById('filterAward').value;
    const year = document.getElementById('filterYear').value;
    const region = document.getElementById('filterRegion').value;
    const muni = document.getElementById('filterMunicipality').value;

    const fullyFiltered = allPlaces.filter(p =>
        (!cat || p.category_id == cat) &&
        (!award || p.award_id == award) &&
        (!year || p.year == year) &&
        (!region || p.region_id == region) &&
        (!muni || p.municipality_id == muni)
    );

    showMarkers(fullyFiltered);

    // Update dropdown options based on currently visible points
    updateFilterOptions('filterCategory', 'category_id', 'category', fullyFiltered, cat, triggeredBy);
    updateFilterOptions('filterAward', 'award_id', 'award', fullyFiltered, award, triggeredBy);
    updateFilterOptions('filterYear', 'year', 'year', fullyFiltered, year, triggeredBy);
    updateFilterOptions('filterRegion', 'region_id', 'region', fullyFiltered, region, triggeredBy);
    updateFilterOptions('filterMunicipality', 'municipality_id', 'municipality', fullyFiltered, muni, triggeredBy);
}
</script>

<!-- ✅ Updated Google Maps JS loader with AdvancedMarker support -->
<script
    src="https://maps.googleapis.com/maps/api/js?key=<?= $googleMapsApiKey ?>&callback=initMap&loading=async&libraries=marker"
    async defer>
</script>