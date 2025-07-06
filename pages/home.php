

<!-- Large screen slider -->
<div class="hero-content">
  <ul class="bxslider">
	  <?
	  $ptk=mysqli_query($baza,'select * from Slider order by Pos');
		while($row=mysqli_fetch_array($ptk,1)){
			$data=json_decode($row['Data'],true);
	  ?>
    <li style="background:url(/<?=$data['Photo']?>) center; background-size: cover; "><img src="/img/Poster_transparent.png" />
		<?=$data['Url'.$LangChar]!=''?'<a href="'.$data['Url'.$LangChar].'" '.(substr($data['Url'.$LangChar],0,4)=='http'?'target="_blank"':'').'>':''?>
      <div class="hero-caption"><?=$data['Header'.$LangChar]?>
		  <div class="hc2"><?=$data['SubHeader'.$LangChar]?></div>
		</div>
      <?=$data['Url'.$LangChar]!=''?'</a>':''?>
	</li>
	  <? } ?>
  </ul>
</div>
<link rel="stylesheet" type="text/css" href="/bxslider4/dist/jquery.bxslider.min.css?v<?=$v?>" />
<script src="/bxslider4/dist/jquery.bxslider.min.js?v<?=$v?>"></script>
<script type="text/javascript">
		$(document).ready(function(){
			$('.bxslider').bxSlider({
				auto: true,
				pager: false,
				autoHover: true,
				mode:'fade',
				// captions: true,
				// hyperlinks: true,
				// autoControls: true,
				// buildPager: function(slideIndex){
				// 	switch(slideIndex){
				// 	}
				// }
			});
		});
	</script>



<div class="home-heading-container reconstruction">
    <h2><?=($Lang=='ge')?'საიტზე მიმდინარეობს რეორგანიზაცია':'Site Under Reconstruction'?></h2>
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
			<?=_Interface('ნამუშევრების გალერეა')?>
		</h2>
		<div class="row news"><?
		if(isset($_GET['st'])) $st=(int)$_GET['st']; else $st=0;
		$ptk=mysqli_query($baza,'select * from CraftGallery  order by Pos desc limit 4');
		while($row=mysqli_fetch_array($ptk,1)){
			$Media=json_decode($row['Media'],true);
			$Photo=$Media[0]['Thumb'];
			?> 
			<div class="col-sm-6 col-md-3 post">
				<div class="news_img" >
					<a href="/<?=$Lang?>/gallery_of_works/<?=$row['ID']?>">
						<img src="/<?=$Photo?>" alt="" class="picture img-responsive">
					</a>
				</div>
				<h4><a href="/<?=$Lang?>/gallery_of_works/<?=$row['ID']?>"><?=LangPart($row['Header'])?></a></h4>
				<p><?=smallText(LangPart($row['Description']),100)?></p>
			</div> <!-- end post -->
		   <? }	?>
		</div>
		<div align="right">
			<a href="/<?=$Lang?>/gallery_of_works" class="btn btn-primary"><?=_Interface('ყველა ნიმუში')?></a>
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
				$ptk=mysqli_query($baza,'select * from Partners order by Pos');
				while($row=mysqli_fetch_array($ptk,1)){
					$Name=json_decode($row['Name'],true);
					$Name=$Name[$LangChar];
					$Url=$row['Url'];
				  ?> 
				 <div class="h200" title="<?=$Name?>"><a href="<?=$Url?>" target="_blank"><img src="/<?=$row['Logo']?>" alt="<?=addslashes($Name)?>"></a></div>
				  <?
				$i++;
				}?>
			  </section>
		</div>
	
	</div>
</div>









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
</style>