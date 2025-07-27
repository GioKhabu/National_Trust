

<!-- Large screen slider -->
<div class="hero-content">
  <ul class="bxslider">
<?php
$ptk = mysqli_query($baza, 'select * from Slider order by Pos');
while ($row = mysqli_fetch_array($ptk, 1)) {
    $data = json_decode($row['Data'], true);
    if (!is_array($data)) {
    error_log("Invalid JSON for slider: " . $row['Data']);
    continue;
}

    $photo = !empty($data['Photo']) ? $data['Photo'] : 'img/default.jpg'; // change to real fallback path
    $urlKey = 'Url' . $LangChar;
    $headerKey = 'Header' . $LangChar;
    $subHeaderKey = 'SubHeader' . $LangChar;

    $url = isset($data[$urlKey]) ? $data[$urlKey] : '';
    $header = isset($data[$headerKey]) ? $data[$headerKey] : '';
    $subHeader = isset($data[$subHeaderKey]) ? $data[$subHeaderKey] : '';
    ?>
    <li style="background:url(/<?= htmlspecialchars($photo) ?>) center; background-size: cover;">
        <img src="/img/Poster_transparent.png" />
        <?php if ($url != ''): ?>
            <a href="<?= htmlspecialchars($url) ?>" <?= (substr($url, 0, 4) == 'http' ? 'target="_blank"' : '') ?>>
        <?php endif; ?>
            <div class="hero-caption">
                <?= htmlspecialchars($header) ?>
                <div class="hc2"><?= htmlspecialchars($subHeader) ?></div>
            </div>
        <?php if ($url != ''): ?>
            </a>
        <?php endif; ?>
    </li>
    <?php
}
?>
  </ul>
</div>
<link rel="stylesheet" type="text/css" href="/bxslider4/dist/jquery.bxslider.min.css?v<?=$v?>" />
<script src="/bxslider4/dist/jquery.bxslider.min.js?v=<?=$v?>"></script>
  <script>
    $(document).ready(function() {
      if ($('.bxslider').length && typeof $.fn.bxSlider === 'function') {
        $('.bxslider').bxSlider({
          auto: true,
          mode: 'fade',
          pager: false,
          autoHover: true
        });
      }
    });
  </script>
  
<!-- GOOGLE MAP GOES HERE -->
<!-- Filters container -->
 <div class="map-section">
<div id="mapFilters">

  <div class="filter-wrapper" style="position: relative; flex: 1 1 140px; min-width: 140px;">
    <select id="filterCategory" class="filter-select" style="width: 100%; padding-right: 32px;">
      <option value="">კატეგორია</option>
      <!-- options populated by JS -->
    </select>
    <span class="icon-wrap">
      <span class="icon open">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 45.201 46.124" width="20" height="20" fill="#489391">
          <g>
            <path d="M17.208,39.356C16.858,36.345,14.291,34,11.188,34c-3.116,0-5.69,2.363-6.026,5.391l-0.005,0.063H1
            c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h4.292c0,0,0-0.001,0-0.002c0.631,2.674,3.031,4.672,5.896,4.672
            c2.864,0,5.264-1.998,5.895-4.672c0,0.001,0,0.002,0,0.002h27.118c0.552,0,1-0.446,1-1c-0.002-0.553-0.45-1-1.002-1h-26.98
            L17.208,39.356z M11.188,44.124c-2.24,0-4.062-1.822-4.062-4.062S8.947,36,11.188,36c2.239,0,4.062,1.821,4.062,4.062
            S13.428,44.124,11.188,44.124z"/>
            <path d="M28.981,21.454H1c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h28.118c0,0,0-0.001,0-0.002
            c0.631,2.674,3.031,4.672,5.896,4.672c2.863,0,5.265-1.998,5.896-4.672c0,0.001,0,0.002,0,0.002h3.292c0.552,0,1-0.446,1-1
            c-0.002-0.553-0.45-1-1.002-1h-3.156l-0.009-0.096C40.684,18.347,38.116,16,35.013,16c-3.115,0-5.689,2.362-6.025,5.389
            L28.981,21.454z M35.013,26.124c-2.239,0-4.062-1.822-4.062-4.062S32.772,18,35.013,18c2.239,0,4.062,1.821,4.062,4.062
            S37.252,26.124,35.013,26.124z"/>
            <path d="M22.212,5.39C21.877,2.363,19.302,0,16.188,0c-3.1,0-5.664,2.338-6.021,5.343l-0.011,0.111H1
            c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h9.292c0,0,0-0.001,0-0.002c0.631,2.674,3.031,4.672,5.896,4.672
            c2.864,0,5.264-1.998,5.895-4.672c0,0.001,0,0.002,0,0.002h22.118c0.552,0,1-0.446,1-1c-0.002-0.553-0.45-1-1.002-1h-21.98
            L22.212,5.39z M16.188,10.124c-2.24,0-4.062-1.822-4.062-4.062S13.947,2,16.188,2c2.239,0,4.062,1.821,4.062,4.062
            S18.428,10.124,16.188,10.124z"/>
          </g>
        </svg>
      </span>
      <span class="icon close" title="ფილტრის წაშლა">&#x2715;</span>
    </span>
  </div>

  <!-- Repeat same structure for other filters -->

  <div class="filter-wrapper" style="position: relative; flex: 1 1 140px; min-width: 140px;">
    <select id="filterAward" class="filter-select" style="width: 100%; padding-right: 32px;">
      <option value="">ჯილდო</option>
    </select>
    <span class="icon-wrap">
      <span class="icon open"> <!-- same SVG here --> 
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 45.201 46.124" width="20" height="20" fill="#489391"> 
          <!-- paths same as above --> 
          <g>
            <path d="M17.208,39.356C16.858,36.345,14.291,34,11.188,34c-3.116,0-5.69,2.363-6.026,5.391l-0.005,0.063H1
            c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h4.292c0,0,0-0.001,0-0.002c0.631,2.674,3.031,4.672,5.896,4.672
            c2.864,0,5.264-1.998,5.895-4.672c0,0.001,0,0.002,0,0.002h27.118c0.552,0,1-0.446,1-1c-0.002-0.553-0.45-1-1.002-1h-26.98
            L17.208,39.356z M11.188,44.124c-2.24,0-4.062-1.822-4.062-4.062S8.947,36,11.188,36c2.239,0,4.062,1.821,4.062,4.062
            S13.428,44.124,11.188,44.124z"/>
            <path d="M28.981,21.454H1c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h28.118c0,0,0-0.001,0-0.002
            c0.631,2.674,3.031,4.672,5.896,4.672c2.863,0,5.265-1.998,5.896-4.672c0,0.001,0,0.002,0,0.002h3.292c0.552,0,1-0.446,1-1
            c-0.002-0.553-0.45-1-1.002-1h-3.156l-0.009-0.096C40.684,18.347,38.116,16,35.013,16c-3.115,0-5.689,2.362-6.025,5.389
            L28.981,21.454z M35.013,26.124c-2.239,0-4.062-1.822-4.062-4.062S32.772,18,35.013,18c2.239,0,4.062,1.821,4.062,4.062
            S37.252,26.124,35.013,26.124z"/>
            <path d="M22.212,5.39C21.877,2.363,19.302,0,16.188,0c-3.1,0-5.664,2.338-6.021,5.343l-0.011,0.111H1
            c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h9.292c0,0,0-0.001,0-0.002c0.631,2.674,3.031,4.672,5.896,4.672
            c2.864,0,5.264-1.998,5.895-4.672c0,0.001,0,0.002,0,0.002h22.118c0.552,0,1-0.446,1-1c-0.002-0.553-0.45-1-1.002-1h-21.98
            L22.212,5.39z M16.188,10.124c-2.24,0-4.062-1.822-4.062-4.062S13.947,2,16.188,2c2.239,0,4.062,1.821,4.062,4.062
            S18.428,10.124,16.188,10.124z"/>
          </g>
        </svg>
      </span>
      <span class="icon close" title="ფილტრის წაშლა">&#x2715;</span>
    </span>
  </div>

  <div class="filter-wrapper" style="position: relative; flex: 1 1 140px; min-width: 140px;">
    <select id="filterYear" class="filter-select" style="width: 100%; padding-right: 32px;">
      <option value="">წელი</option>
    </select>
    <span class="icon-wrap">
      <span class="icon open">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 45.201 46.124" width="20" height="20" fill="#489391"> 
          <g>
            <path d="M17.208,39.356C16.858,36.345,14.291,34,11.188,34c-3.116,0-5.69,2.363-6.026,5.391l-0.005,0.063H1
            c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h4.292c0,0,0-0.001,0-0.002c0.631,2.674,3.031,4.672,5.896,4.672
            c2.864,0,5.264-1.998,5.895-4.672c0,0.001,0,0.002,0,0.002h27.118c0.552,0,1-0.446,1-1c-0.002-0.553-0.45-1-1.002-1h-26.98
            L17.208,39.356z M11.188,44.124c-2.24,0-4.062-1.822-4.062-4.062S8.947,36,11.188,36c2.239,0,4.062,1.821,4.062,4.062
            S13.428,44.124,11.188,44.124z"/>
            <path d="M28.981,21.454H1c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h28.118c0,0,0-0.001,0-0.002
            c0.631,2.674,3.031,4.672,5.896,4.672c2.863,0,5.265-1.998,5.896-4.672c0,0.001,0,0.002,0,0.002h3.292c0.552,0,1-0.446,1-1
            c-0.002-0.553-0.45-1-1.002-1h-3.156l-0.009-0.096C40.684,18.347,38.116,16,35.013,16c-3.115,0-5.689,2.362-6.025,5.389
            L28.981,21.454z M35.013,26.124c-2.239,0-4.062-1.822-4.062-4.062S32.772,18,35.013,18c2.239,0,4.062,1.821,4.062,4.062
            S37.252,26.124,35.013,26.124z"/>
            <path d="M22.212,5.39C21.877,2.363,19.302,0,16.188,0c-3.1,0-5.664,2.338-6.021,5.343l-0.011,0.111H1
            c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h9.292c0,0,0-0.001,0-0.002c0.631,2.674,3.031,4.672,5.896,4.672
            c2.864,0,5.264-1.998,5.895-4.672c0,0.001,0,0.002,0,0.002h22.118c0.552,0,1-0.446,1-1c-0.002-0.553-0.45-1-1.002-1h-21.98
            L22.212,5.39z M16.188,10.124c-2.24,0-4.062-1.822-4.062-4.062S13.947,2,16.188,2c2.239,0,4.062,1.821,4.062,4.062
            S18.428,10.124,16.188,10.124z"/>
          </g>
        </svg>
      </span>
      <span class="icon close" title="ფილტრის წაშლა">&#x2715;</span>
    </span>
  </div>

  <div class="filter-wrapper" style="position: relative; flex: 1 1 140px; min-width: 140px;">
    <select id="filterRegion" class="filter-select" style="width: 100%; padding-right: 32px;">
      <option value="">რეგიონი</option>
    </select>
    <span class="icon-wrap">
      <span class="icon open">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 45.201 46.124" width="20" height="20" fill="#489391"> 
          <g>
            <path d="M17.208,39.356C16.858,36.345,14.291,34,11.188,34c-3.116,0-5.69,2.363-6.026,5.391l-0.005,0.063H1
            c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h4.292c0,0,0-0.001,0-0.002c0.631,2.674,3.031,4.672,5.896,4.672
            c2.864,0,5.264-1.998,5.895-4.672c0,0.001,0,0.002,0,0.002h27.118c0.552,0,1-0.446,1-1c-0.002-0.553-0.45-1-1.002-1h-26.98
            L17.208,39.356z M11.188,44.124c-2.24,0-4.062-1.822-4.062-4.062S8.947,36,11.188,36c2.239,0,4.062,1.821,4.062,4.062
            S13.428,44.124,11.188,44.124z"/>
            <path d="M28.981,21.454H1c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h28.118c0,0,0-0.001,0-0.002
            c0.631,2.674,3.031,4.672,5.896,4.672c2.863,0,5.265-1.998,5.896-4.672c0,0.001,0,0.002,0,0.002h3.292c0.552,0,1-0.446,1-1
            c-0.002-0.553-0.45-1-1.002-1h-3.156l-0.009-0.096C40.684,18.347,38.116,16,35.013,16c-3.115,0-5.689,2.362-6.025,5.389
            L28.981,21.454z M35.013,26.124c-2.239,0-4.062-1.822-4.062-4.062S32.772,18,35.013,18c2.239,0,4.062,1.821,4.062,4.062
            S37.252,26.124,35.013,26.124z"/>
            <path d="M22.212,5.39C21.877,2.363,19.302,0,16.188,0c-3.1,0-5.664,2.338-6.021,5.343l-0.011,0.111H1
            c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h9.292c0,0,0-0.001,0-0.002c0.631,2.674,3.031,4.672,5.896,4.672
            c2.864,0,5.264-1.998,5.895-4.672c0,0.001,0,0.002,0,0.002h22.118c0.552,0,1-0.446,1-1c-0.002-0.553-0.45-1-1.002-1h-21.98
            L22.212,5.39z M16.188,10.124c-2.24,0-4.062-1.822-4.062-4.062S13.947,2,16.188,2c2.239,0,4.062,1.821,4.062,4.062
            S18.428,10.124,16.188,10.124z"/>
          </g>
        </svg>
      </span>
      <span class="icon close" title="ფილტრის წაშლა">&#x2715;</span>
    </span>
  </div>

  <div class="filter-wrapper" style="position: relative; flex: 1 1 140px; min-width: 140px;">
    <select id="filterMunicipality" class="filter-select" style="width: 100%; padding-right: 32px;">
      <option value="">მუნიციპალიტეტი</option>
    </select>
    <span class="icon-wrap">
      <span class="icon open">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 45.201 46.124" width="20" height="20" fill="#489391"> 
          <g>
            <path d="M17.208,39.356C16.858,36.345,14.291,34,11.188,34c-3.116,0-5.69,2.363-6.026,5.391l-0.005,0.063H1
            c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h4.292c0,0,0-0.001,0-0.002c0.631,2.674,3.031,4.672,5.896,4.672
            c2.864,0,5.264-1.998,5.895-4.672c0,0.001,0,0.002,0,0.002h27.118c0.552,0,1-0.446,1-1c-0.002-0.553-0.45-1-1.002-1h-26.98
            L17.208,39.356z M11.188,44.124c-2.24,0-4.062-1.822-4.062-4.062S8.947,36,11.188,36c2.239,0,4.062,1.821,4.062,4.062
            S13.428,44.124,11.188,44.124z"/>
            <path d="M28.981,21.454H1c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h28.118c0,0,0-0.001,0-0.002
            c0.631,2.674,3.031,4.672,5.896,4.672c2.863,0,5.265-1.998,5.896-4.672c0,0.001,0,0.002,0,0.002h3.292c0.552,0,1-0.446,1-1
            c-0.002-0.553-0.45-1-1.002-1h-3.156l-0.009-0.096C40.684,18.347,38.116,16,35.013,16c-3.115,0-5.689,2.362-6.025,5.389
            L28.981,21.454z M35.013,26.124c-2.239,0-4.062-1.822-4.062-4.062S32.772,18,35.013,18c2.239,0,4.062,1.821,4.062,4.062
            S37.252,26.124,35.013,26.124z"/>
            <path d="M22.212,5.39C21.877,2.363,19.302,0,16.188,0c-3.1,0-5.664,2.338-6.021,5.343l-0.011,0.111H1
            c-0.552,0-1,0.447-1,1c0,0.554,0.448,1,1,1h9.292c0,0,0-0.001,0-0.002c0.631,2.674,3.031,4.672,5.896,4.672
            c2.864,0,5.264-1.998,5.895-4.672c0,0.001,0,0.002,0,0.002h22.118c0.552,0,1-0.446,1-1c-0.002-0.553-0.45-1-1.002-1h-21.98
            L22.212,5.39z M16.188,10.124c-2.24,0-4.062-1.822-4.062-4.062S13.947,2,16.188,2c2.239,0,4.062,1.821,4.062,4.062
            S18.428,10.124,16.188,10.124z"/>
          </g>
        </svg>
      </span>
      <span class="icon close" title="ფილტრის წაშლა">&#x2715;</span>
    </span>
  </div>

</div>
<div id="googleMap" style="width: 100%; height: 520px; margin-top: 20px;"></div>
</div>



<div class="home-heading-container">
	<div>
		<h2 class="home-heading">
			<?=_Interface('ჩვენს შესახებ')?>
		</h2>
		<div>
            <?=_Interface('საქართველოს ეროვნული ფონდი არასამთავრობო, არაკომერციული, საქველმოქმედო ორგანიზაციაა, რომლის მიზანია საქართველოს კულტურული და ბუნებრივი მემკვიდრეობის მომავალი თაობებისთვის შენახვა...')?>
		</div>
		<div align="right">
			<a href="/<?=$Lang?>/mission" class="btn btn-primary"><?=_Interface('დეტალურად')?></a>
		</div>
	</div>
</div>




<div class="home-heading-container">
	<div>
		<h2 class="home-heading">
			<?=_Interface('სიახლეები')?>
		</h2>
		<div class="row news"><?
		if(isset($_GET['st'])) $st=(int)$_GET['st']; else $st=0;
		$ptk=mysqli_query($baza,'select * from News  order by Pos desc limit 4');
		while($row=mysqli_fetch_array($ptk,1)){?> 
			<div class="col-sm-3 post">
				<div class="news_img" >
					<a href="/<?=$Lang?>/news/<?=$row['ID']?>">
						<img src="/<?=$row['Photo']?>" alt="" class="picture img-responsive">
					</a>
				</div>
				<div class="dates">
					<span><?=BlogDTime($row['DTime'])?></span>
				</div>
				<h4><a href="/<?=$Lang?>/news/<?=$row['ID']?>"><?=LangPart($row['Header'])?></a></h4>
				<p><?=smallText(LangPart($row['Text']),100)?></p>
			</div> <!-- end post -->
		   <? }	?>
		</div>
		<div align="right">
			<a href="/<?=$Lang?>/news" class="btn btn-primary"><?=_Interface('ყველა სიახლე')?></a>
		</div>
	</div>
</div>




<div class="home-heading-container">
    <div class="row">
	<div class="col-md-4">
        <div class="homeBlock">
		<h2 class="home-heading">
			<?=_Interface('გაწევრიანება')?>
		</h2>
		<div class="HBText">
            <?=_Interface('საქართველოს ეროვნული ფონდი ქმნის წევრობის სქემას, რომელიც საშუალებას მოგცემთ უფრო აქტიურად ჩაერთოთ მის საქმიანობაში')?>
		</div>
		<div >
			<a href="/<?=$Lang?>/become_a_member" class="btn btn-primary"><?=_Interface('დეტალურად')?></a>
		</div>
        </div>
	</div>

    
	<div class="col-md-4">
        <div class="homeBlock">
		<h2 class="home-heading">
			<?=_Interface('გახდი მოხალისე')?>
		</h2>
		<div class="HBText">
            <?=_Interface('გახდი ჩვენი ორგანიზაციის მოხალისე და მიიღე დაუვიწყარი გამოცდილება კულტურული მემვიდრეობის დაცვისა და შენარჩუნების პროექტებში')?>
		</div>
		<div >
			<a href="/<?=$Lang?>/become_a_volunteer" class="btn btn-primary"><?=_Interface('დეტალურად')?></a>
		</div>
		</div>
	</div>

    
	<div class="col-md-4">
        <div class="homeBlock">
		<h2 class="home-heading">
			<?=_Interface('შემოწირულობა')?>
		</h2>
		<div class="HBText">
            <?=_Interface('თუ გსურს წინაპრების ცოდნის, ხელობისა თუ სხვა ტრადიციების შენარჩუნება, მიიღეთ მონაწილეობა ამაში ფინანსური მხარდაჭერით')?>
		</div>
		<div >
			<a href="/<?=$Lang?>/donation" class="btn btn-primary"><?=_Interface('დეტალურად')?></a>
		</div>
	</div>
	</div>
    </div>
    
</div>








<div class="home-heading-container">
	<div>
		<h2 class="home-heading">
			<?=_Interface('პარტნიორები/დონორები')?>
		</h2>
		<div class="row news">
			  <section class="regular slider">
				 <?
				$i=1;
				$ptk = mysqli_query($baza, 'select * from Partners order by Pos');
while ($row = mysqli_fetch_array($ptk, 1)) {
    $NameRaw = $row['Name'];
    $NameArray = json_decode($NameRaw, true);

    if (!is_array($NameArray) || !isset($NameArray[$LangChar])) {
        error_log("Invalid Name JSON or missing language key at Partner ID: " . $row['ID']);
        $Name = 'Unnamed Partner';
    } else {
        $Name = $NameArray[$LangChar];
    }

    $Url = $row['Url'];
    ?>
    <div class="h200" title="<?= htmlspecialchars($Name) ?>">
        <a href="<?= htmlspecialchars($Url) ?>" target="_blank">
            <img src="/<?= htmlspecialchars($row['Logo']) ?>" alt="<?= htmlspecialchars($Name) ?>">
        </a>
    </div>
    <?
}?>
			  </section>
		</div>
	
	</div>
</div>


<script>
let map;
  let markers = [];
  let allPlaces = [];

  function initMap() {
    map = new google.maps.Map(document.getElementById("googleMap"), {
      center: { lat: 42.4, lng: 43.5 },
      zoom: 7,
      mapTypeControl: false,
      streetViewControl: false,
      zoomControl: false,
      fullscreenControl: true,
    });

    fetch('get_places.php')
      .then(response => response.json())
      .then(data => {
        allPlaces = data;
        populateFilters(data);
        setupFilterIcons();
        showMarkers(data);
      })
      .catch(console.error);
  }

  function populateFilters(data) {
    function uniqueSorted(field) {
      return [...new Set(data.map(p => p[field]).filter(Boolean))].sort();
    }

    const mappings = {
      'filterCategory': 'category',
      'filterAward': 'award',
      'filterYear': 'year',
      'filterRegion': 'region',
      'filterMunicipality': 'municipality'
    };

    for (const [selectId, field] of Object.entries(mappings)) {
      const select = document.getElementById(selectId);
      uniqueSorted(field).forEach(value => {
        const opt = document.createElement('option');
        opt.value = value;
        opt.textContent = value;
        select.appendChild(opt);
      });
    }
  }

  function clearMarkers() {
    markers.forEach(m => m.setMap(null));
    markers = [];
  }

  function showMarkers(data) {
    clearMarkers();

    data.forEach(place => {
      const marker = new google.maps.Marker({
        position: { lat: parseFloat(place.x), lng: parseFloat(place.y) },
        map: map,
        title: place.project_name,
        icon: {
          path: google.maps.SymbolPath.CIRCLE,
          fillColor: 'red',
          fillOpacity: 1,
          strokeColor: 'darkred',
          strokeWeight: 1,
          scale: 8
        }
      });

      const content = `
        <div class="custom-infowindow">
          <h3>${place.project_name}</h3>
          <p><strong>რეგიონი:</strong> ${place.region}</p>
          <p><strong>მუნიციპალიტეტი:</strong> ${place.municipality}</p>
          <p><strong>კატეგორია:</strong> ${place.category}</p>
          <p><strong>ჯილდო:</strong> ${place.award}</p>
          <p><strong>წელი:</strong> ${place.year}</p>
          <p><strong>ავტორი:</strong> ${place.author}</p>
          <p><a href="${place.link}" target="_blank" rel="noopener" class="infowindow-btn">ბმული რუკაზე</a></p>
        </div>
      `;

      const infowindow = new google.maps.InfoWindow({ content });

      marker.addListener('click', () => {
        infowindow.open(map, marker);
      });

      markers.push(marker);
    });
  }

  // Apply filters and update markers
  function applyFilters() {
    const cat = document.getElementById('filterCategory').value;
    const award = document.getElementById('filterAward').value;
    const year = document.getElementById('filterYear').value;
    const region = document.getElementById('filterRegion').value;
    const muni = document.getElementById('filterMunicipality').value;

    const filtered = allPlaces.filter(p => {
      return (!cat || p.category === cat)
        && (!award || p.award === award)
        && (!year || p.year === year)
        && (!region || p.region === region)
        && (!muni || p.municipality === muni);
    });

    showMarkers(filtered);
  }

  // Setup filter icons and their toggle logic
function setupFilterIcons() {
  ['filterCategory', 'filterAward', 'filterYear', 'filterRegion', 'filterMunicipality'].forEach(id => {
    const select = document.getElementById(id);
    const wrapper = select.closest('.filter-wrapper');

    // On change: add or remove "filtered" class to wrapper
    select.addEventListener('change', () => {
      if (select.value) {
        wrapper.classList.add('filtered');
      } else {
        wrapper.classList.remove('filtered');
      }
      applyFilters();
    });

    // On click close icon: clear select, remove "filtered" class
    const closeIcon = wrapper.querySelector('.icon.close');
    closeIcon.addEventListener('click', () => {
      select.value = '';
      wrapper.classList.remove('filtered');
      applyFilters();
    });

    // Initialize wrapper class on page load
    if (select.value) {
      wrapper.classList.add('filtered');
    }
  });
}

</script>


<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDb1MxdyujxUcDebKShsFN-3DyCY4KhbMA&callback=initMap"></script>






<style>
.regular.slider .h200 {
    display: inline-block;
    width: 25%;
    margin: 10px;
	height: 70px;
}
.regular.slider .h200 img {
    max-width: 80%;
    filter: grayscale(1) opacity(.5);
    transition: all .5s;
	vertical-align: middle;
}
.regular.slider .h200:hover img{
	filter:none;
	transform: scale(1.2);
	}
.news .post {
    font-size: 14px;
	text-align: left;
}
.news .dates {
    color: #0008;
    font-style: italic;
}
.news h4 a {
    color: #284949;
}
h2.home-heading {
    margin-bottom: 20px;
}

    
.reconstruction {
    background-image: url(/img/works_pano.png);
    background-size: contain;
}

    
    .homeBlock {
        background: #2D5454;
        color: #fff;
        padding: 20px;
        border-radius: 10px;
    }
    .homeBlock h2.home-heading {
        color: #fff;
    }
    .HBText {
        margin-bottom: 15px;
        font-size: 14px;
        line-height: 130%;
    }
    .homeBlock .btn.btn-primary {
        border: 1px solid #fff;
    }


    .map-section {
        width: 100%;
        height: 520px;
        position: relative;
    }

    @media (max-width: 768px) {
  #googleMap {
    height: 520px;
  }
}
.custom-infowindow {
  font-family: inherit;
  font-size: 14px;
  max-width: 280px;
  color: #222;
  line-height: 1.4;
}

.custom-infowindow h3 {
  margin-top: 0;
  margin-bottom: 8px;
  font-size: 16px;
  color: #489391;
  border-bottom: 1px solid #ddd;
  padding-bottom: 4px;
}

.custom-infowindow p {
  margin: 4px 0;
}


.infowindow-btn {
  display: inline-block;
  margin-top:5px;
  padding: 6px 14px;
  background-color: #489391;
  color: white !important;
  text-decoration: none;
  border-radius: 4px;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.infowindow-btn:hover,
.infowindow-btn:focus {
  background-color: #fa0;
  color: black !important;
  text-decoration: none;
  outline: none;
}

#mapFilters {
    position: absolute;
    top: 17px;
    display: flex; 
    gap: 12px; 
    flex-wrap: wrap;
    justify-content: center;
    align-items:center;
    width: 90%;
    margin-bottom: -15px;
    z-index: 3;
    padding-left: 20px;
}



/* Hide default arrow */
.filter-wrapper {
    display: flex;
    flex-wrap: wrap;
    position: relative;
  }

  .filter-select {
    width: 100%;
    height: 40px;
    display: flex;
    justufy-content: center;
    padding-right: 32px;
    padding-left: 5px;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: white;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    margin-bottom: 0px;
  }


  .icon-wrap {
  position: absolute;
  top: 45%;
  right: 9px;
  transform: translateY(-50%);
  display: flex;
  align-items: center;
  gap: 6px;
  pointer-events: none;
}

/* Filter icon visible only when no filter is selected */
.icon.open {
  pointer-events: none;
  display: inline-flex;
}

/* X icon hidden by default */
.icon.close {
  pointer-events: auto;
  display: none;
  cursor: pointer;
  font-weight: bold;
  font-size: 18px;
  color: #B22222;
  user-select: none;
  line-height: 1;
}

/* When a filter is selected: hide filter icon, show close icon */
.filter-wrapper.filtered .icon.open {
  display: none !important;
}

.filter-wrapper.filtered .icon.close {
  display: inline !important;
}


</style>