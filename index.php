<?php
session_start();
$v = '1.04_'.time();

error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once('conf.php');
include_once('functions.php');

$localPrefix = '';

$url = parse_url($_SERVER['REQUEST_URI']);
if (!$url || !isset($url['path'])) $url = ['path' => ''];
$url = $url['path'];

// Remove local prefix if present
if (strpos($url, $localPrefix) === 0) {
    $url = substr($url, strlen($localPrefix));
}
$url = trim($url, '/');

// Explode into parts: now $Lang is first part, no unused $nul
list($Lang, $menu, $page, $param1, $param2, $param3) = explode('/', $url . '//////');

// Set defaults if missing
if ($Lang == '') {
    $Lang = $Langs[1]['Name2'];  // default language, e.g. "ge"
}
if ($menu == '') {
    $menu = 'home';
}

$LangID = in_multi_array($Lang, $Langs, 'Name2');
if ($LangID === false) {
    if ($_SERVER['HTTP_HOST'] == 'localhost') {
        header('Location: ' . $localPrefix . '/' . $Langs[1]['Name2']);
    } else {
        header('Location: /' . $Langs[1]['Name2']);
    }
    die();
} else {
    $LangChar = $Langs[$LangID]['Char'];
    $Lid = $LangID + 1;
    if (!isset($Langs[$Lid])) $Lid = 1;
    $Lang2 = $Langs[$Lid]['Name2'];
}
?>

<!doctype html> 
<html lang="en" class="no-js">
<head>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/cookieconsent@3/build/cookieconsent.min.css" />
	<meta charset="utf-8" />
	<title><?=_Interface('Home Page')?> | <?=_Interface('საქართველოს ეროვნული ფონდი')?></title>
	<meta name="description" content="" />

	<?
    $title=_Interface('საქართველოს ეროვნული ფონდი');
    $description= _Interface('საქართველოს ეროვნული ფონდის ოფიციალური ვებგვერდი');
    $image="img/save-logo-192.png";
    $NID=(int)$page;
    if(($menu=='news' || $menu=='media_announcements' || $menu=='press_releases') && $NID>0){
		$Tables=array('news'=>'News', 'media_announcements'=>'MediaAnnouncements', 'press_releases'=>'PressReleases');
        $ptk=mysqli_query($baza,'select * from '.$Tables[$menu].' where ID='.$NID);
        if($row=mysqli_fetch_array($ptk,1)){
			$Photo=$row['Photo'];
			if($Photo!='')
            	$image=$Photo;
            $title=normalQuotes(LangPart($row['Header']));
            $description=normalQuotes(smallText(strip_tags(LangPart($row['Text'])),100));
            }
        }
    ?>
	<meta property="og:title" content="<?=$title?>"/>
    <meta property="og:description" content="<?=$description?>"/>
    <meta property="og:site_name" content="<?=_Interface('საქართველოს ეროვნული ფონდი')?>" />
    <meta property="og:image"  itemprop="image primaryImageOfPage" content="/<?=$image?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>"/>

	
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />
	
	<link rel="stylesheet" href="/css/bootstrap.min.css?v<?=$v?>" />
	<link rel="stylesheet" href="/css/style.css?v<?=$v?>" />
	<link rel="stylesheet" type="text/css" href="/css/megamenu.css?v<?=$v?>" /> 
	<link rel="stylesheet" type="text/css" href="/css/bpgmc.css?v<?=$v?>" /> 
	<link rel="stylesheet" type="text/css" href="/css/bpg_ing_arial.css?v<?=$v?>" /> 
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
	<link rel="stylesheet" type="text/css" href="/css/font-awesome.css"> 
	<link rel="stylesheet" type="text/css" href="/css/styles_main.css?v<?=$v?>" />
	
	<link rel="icon" href="/img/save-logo-32.png" sizes="32x32" />
	<link rel="icon" href="/img/save-logo-192.png" sizes="192x192" />
	<link rel="apple-touch-icon" href="/img/save-logo-180.png" /> 
	<meta name="msapplication-TileImage" content="/img/save-logo-270.png" />

	<style>
		body {font-family: 'arial'}
		body.ge {font-family: 'BPGIngiriArial'}
		.body_content{min-height: 300px; margin-bottom: 30px;}
		
	</style>
	
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js" crossorigin="anonymous"></script>


    <!-- fancyBox Scripts -->
    <link href="/fancybox357/dist/jquery.fancybox.min.css" rel="stylesheet" />
    <script src="/fancybox357/dist/jquery.fancybox.min.js"></script>

    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=67dc145d59793500196aaf4d&product=inline-share-buttons&source=platform" async="async"></script>
	
</head>
<body class="<?=$Lang?>">


<div id="search-container">
    <div id="search-content">
        <div id="logo-link">
            <a href="/<?=$Lang?>"><img src="/img/save-logo-white.png" class="logo-img" /></a>
        </div>
        <a href="/<?=$Lang?>/donation/" class="donate-button"><?=_Interface('შემოწირულობა')?></a>
        <div class="header-buttons">
            <a href="#" class="butt lang" id="search-trigger"><i class="fa fa-search"></i></a>
            <a href="/<?=$Lang2?><?=$menu!=''?'/'.$menu:''?><?=$page!=''?'/'.$page:''?>" id="lang" class="butt lang"><?=$Lang2?></a>
        </div>
    </div>
</div>

<div id="header-container">
    <div id="header-content"></div>
</div>

	
<div id="search-form-wrapper">
	<div id="search-form-content">
		<div action="/<?=$Lang2?>/search" class="search-form" method="get" accept-charset="utf-8" enctype="multipart/form-data">
		<input type="text" id="src" name="src" placeholder="<?=_Interface('ჩაწერეთ საძებნი ტექსტი')?>" />&nbsp;&nbsp;
		<button type="submit" class="butt" onClick="srcText()"><i class="fa fa-search"></i> <?=_Interface('ძებნა')?></button>
		</div>
	</div>
</div>
<!--// Navbar Ends--> 
<script>
	$('#src').on('keypress',function(e){
		if(e.keyCode==13) srcText();
		});

	function srcText(){
		var q=$('#src').val();
		q=q.trim();
		if(q!='') window.location='/<?=$Lang?>/search//'+q;
	}
</script>	
<div id="menu-wrapper" >
	<div class="megamenu_container"><!-- Full width wrapper --> 
<?
if(isset($_SESSION['Menus']))
	$MENU=$_SESSION['Menus'];
	else
	$MENU=$_SESSION['Menus']=getMenuChilds(0);
	
?>
	
<a id="megamenu-button-mobile" href="#">&nbsp;</a><!-- <?=_Interface('Menu')?> Menu button responsive-->	
<!-- Begin Mega Menu Container -->
	<ul class="megamenu">
		<?
		foreach($MENU as $mEl1)
			if($mEl1['Active']==1){
			echo '<li><a href="'.(!isset($mEl1['Childs'])?'/'.$Lang.'/'.$mEl1['ShortUrl']:'javascript:void(0)').'" class="'.(isset($mEl1['Childs'])?'drop-down':'nodrop-down').'">'.$mEl1['Title'.$LangChar].'</a>';
			if(isset($mEl1['Childs'])){
				echo '<div class="drop-down-container"><div class="row">';
				foreach($mEl1['Childs'] as $mEl2)
					if($mEl2['Active']==1){
					echo '<div class="col-md-4">';
					if(isset($mEl2['Childs'])){
						echo '<h5>'.$mEl2['Title'.$LangChar].'</h5>';
						echo '<ul class="list-menu">';
						foreach($mEl2['Childs'] as $mEl3)
							if($mEl3['Active']==1)
								echo '<li><a href="/'.$Lang.'/'.$mEl3['ShortUrl'].'"><div>'.$mEl3['Title'.$LangChar].'</div></a></li> ';
						echo '</ul>';
						} else
						echo '<a href="/'.$Lang.'/'.$mEl2['ShortUrl'].'"><h5>'.$mEl2['Title'.$LangChar].'</h5></a>';
					echo '</div>';
					}
				echo '</div></div>';
				}
			echo '</li> ';
			}
	 ?>
	</ul><!-- End Mega Menu -->

	</div><!-- End menu-container -->
</div><!-- End menu-wrapper -->
	
<div class="body_content">
<?
function menuRecord($MENU, $menu){
	foreach($MENU as $Menu){
		if($Menu['ShortUrl']==$menu){
			return $Menu;
			}
			else
		if(isset($Menu['Childs'])){
			$ret=menuRecord($Menu['Childs'], $menu);
			if($ret) {
				$MM=$Menu;
				unset($MM['Childs']);
				$MM['Child']=$ret;
				return $MM;
				}
			} 
		}
	return false;
	}

function menuType($MENU, $menu){
	foreach($MENU as $Menu){
		$MenuName=$Menu['ShortUrl'];
		if($MenuName==$menu){
			if($Menu['Controller']==1)
				return 'Dynamic';
				else 
				return 'Static';
			}
			else
		if(isset($Menu['Childs'])){
			$ret=menuType($Menu['Childs'], $menu);
			if($ret!='Error') return $ret;
			} 
		}
	return 'Error';
	}

function menuChain($menu){
	global $LangChar;
	if(!isset($menu['Title'.$LangChar])) return '';
	$res=$menu['Title'.$LangChar];
	while(isset($menu['Child'])){
		$menu=$menu['Child'];
		$res.=' &rarr; '.$menu['Title'.$LangChar];
		}
	return $res;
	}


$MT=menuType($MENU, $menu);	

$Mr=$Mr0=menuRecord($MENU, $menu);
/*
	?>
	<div class="menuChain"><?=menuChain($Mr)?></div>
	<?
	echo $menu;*/

if($MT=='Dynamic'){
	$page0='pages/'.$menu.'.php';
	if(file_exists($page0)) include $page0;
		else echo '<br><br><br><br><br>'.$page0;
	} 
	else {
	$page0='pages/Page_'.$menu.($Lang=='en'?'_E':'');
	$page_left=$page0.'_left';
	if(file_exists($page0)) {
		$Text = file_get_contents($page0);
		$page0 = embedYoutube($Text);
		

		
		echo '
		<div class="container pageContainer p30_10 p150_30 '.$menu.'">
		  <div class="row">';
		
		
		
		if(file_exists($page_left)) 
			$page_left=file_get_contents($page_left);
			else $page_left='';
		if(trim(strip_tags($page_left,'<img>'))=='') 
        	echo '
			<div class="col-md-12 fixewdPage fixText">
				<!--  Start Fixed Page section  -->
				'.$page0;
			else{
			echo '<aside class="col-md-3 col-sm-4 fixewdPage">
      			<div class="well">'.$page_left.'</div>
      			</aside>
				<div class="col-md-9 col-sm-8 fixewdPage fixText">'.$page0;
			}
		
		if($menu=='contact') include ('pages/contact.php');
		
			echo '</div>';
		echo '</div>';
		echo '</div>';
        ?>
<script>

    $('.fixText img').each(function(i,e){
        var photo=$(e).attr('src');
        var a='<div class="f-carousel__slide" data-thumb-src="'+photo+'" data-thumb-width="108" data-thumb-height="72" data-fancybox="gallery" data-src="'+photo+'"><img src="'+photo+'"></div>';
        // console.log(a);
        $(a).insertAfter(e);
        $(e).remove();
    })
    
    $('.fixewdPage .well a').each(function(i,e){
        // console.log(i,e);
        var url=$(e).attr('href').split('/').pop();
        url=url.split('#')[0];
        // console.log(url,'<?=urldecode($menu)?>');
        if(url=='<?=urldecode($menu)?>')
           $(e).parent().addClass('selected');
        })
    
	$('.fixText a').each(function(i,e){
        var url=$(e).attr('href').split('://');
        if(url[0]=='http' || url[0]=='https')
           $(e).attr('target','_blank');
    })
</script>    
    <?
		if($menu=='donation')
			include ('pages/donate.php');
		if($menu=='become_a_volunteer')
			include ('pages/volunteer_form.php');
		}
		else error(_Interface('404. გვერდი ვერ მოიძებნა'),200,400); // $page0; 
	}
	?>


</div>


<div id="footer-container">

	
	<footer>

		<div class="foot-left">

			<div class="foot-inner-a">
				<a href="/<?=$Lang?>">
					<img src="/img/save-logo-white.png" style="width: 90%; ">
				</a>
			</div>

			<div class="foot-inner-b">
				© 2024 <?=_Interface('საქართველოს ეროვნული ფონდი')?><br />
				<?=_Interface('მეფე სოლომონ ბრძენის ქ.33, 0103 თბილისი, საქართველო')?><br />
				<?=_Interface('ს.კ.:404522389')?><br />
				<?=_Interface('ტელ')?>: (+995) 555 490 917 <br />
				<a href="mailto:office@nationaltrustgeo.org">office@nationaltrustgeo.org</a>
				<br /><br />	


			</div>

		</div>

		<div class="foot-right">

			<div class="foot-inner-a">
				<?=_Interface('გამოგვყევით')?>:<br />
				<a href="https://www.instagram.com/nationaltrustge/" target="_blank">Instagram</a><br />
				<a href="https://www.linkedin.com/company/the-national-trust-of-georgia/?originalSubdomain=ge" target="_blank">Linkedin</a><br />
				<a href="http://www.facebook.com/thenationaltrustofgeorgia" target="_blank">Facebook</a><br />
				<br />
			</div>

			<div class="foot-inner-b">
				<a href="/<?=$Lang?>/donation/" class="donate-button foot_donate"><?=_Interface('შემოწირულობა')?></a>
				<br>
			</div>

		</div>

	</footer>

	<div class="foot-full">
		
	</div>

</div>



	<!-- MEGAMENU --> 
<script src="/js/jquery.easing.js"></script>
<script src="/js/megamenu.js"></script>
	
	<!-- OTHER JS -->    
<script src="/js/bootstrap.js"></script>
<script src="/js/functions.js"></script>
	
<button onclick="topFunction()" id="myBtn" title="Go to top"><?=_Interface('ზევით')?></button>
	
<script>
	$('#search-trigger').on('click',function(e){
		$('#search-form-wrapper').slideToggle();
        $('#search-form-wrapper input').focus();
	})
	
// Get the button:
let mybutton = document.getElementById("myBtn");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if (document.body.scrollTop > 280 || document.documentElement.scrollTop > 280) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
	window.scrollTo({top: 0, behavior: 'smooth'});
/*
var timerHandle = setInterval(function() {
  if (document.body.scrollTop != 0 || document.documentElement.scrollTop != 0)
  window.scrollBy(0,-50); else clearInterval(timerHandle); },10);
*/
	return false;
}


</script>



<script>
  $(window).scroll(function () {
    if ($(window).width() >= 992) {
      var $row = $('.pageContainer > .row');
      var $col = $('.pageContainer > .row > .col-md-3');

      // Make sure both elements exist before continuing
      if ($row.length && $col.length && $row.offset()) {
        var h0 = $col.height();
        var s = $(window).scrollTop();
        var h = $row.height();
        var t = $row.offset().top;

        s = s - t;
        if (s < 0) s = 0;
        var ss = (h0 + s < h) ? s : (h - h0);
        $col.css({ top: ss });
      } else {
        $col.css({ top: 0 });
      }
    } else {
      $('.pageContainer > .row > .col-md-3').css({ top: 0 });
    }
  });
</script>
    
</body>
</html>
