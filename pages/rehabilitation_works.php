<style>
	.NewsHeader {
		font-size: 24px;
		text-align: center;
		margin-bottom: 35px;
		padding-bottom: 20px;
		border-bottom: 3px dotted #0002;
	}
	.container.news {
		margin-top: 40px;
	}
	hr{
		clear: both;
	}
	.recent_post li {
		margin-bottom: 10px;
		padding-bottom: 10px;
		border-bottom: 1px dotted #0002;
	}
	.sidebar {
		background-color: #0301;
		padding: 10px;
	}
	.recent_post li {
		margin-left: -20px;
	}
	
	

.pagination {
    display: inline-block;
    padding-left: 0;
    margin: 20px 0;
    border-radius: 4px
}

.pagination>li {
    display: inline
}

.pagination>li>a,.pagination>li>span {
    position: relative;
    float: left;
    padding: 6px 12px;
    margin-left: -1px;
    line-height: 1.42857143;
    color: #00B748;
    text-decoration: none;
    background-color: #fff;
    border: 1px solid #ddd
}

.pagination>li:first-child>a,.pagination>li:first-child>span {
    margin-left: 0;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px
}

.pagination>li:last-child>a,.pagination>li:last-child>span {
    border-top-right-radius: 4px;
    border-bottom-right-radius: 4px
}

.pagination>li>a:focus,.pagination>li>a:hover,.pagination>li>span:focus,.pagination>li>span:hover {
    color: #227C32;
    background-color: #eee;
    border-color: #ddd
}

.pagination a.active {
    z-index: 2;
    color: #fff;
    cursor: default;
    background-color: #489391;
    border-color: #489391
}

.pagination>.disabled>a,.pagination>.disabled>a:focus,.pagination>.disabled>a:hover,.pagination>.disabled>span,.pagination>.disabled>span:focus,.pagination>.disabled>span:hover {
    color: #777;
    cursor: not-allowed;
    background-color: #fff;
    border-color: #ddd
}

.news .post {
    font-size: 14px;
}
.news .dates {
    color: #0008;
    font-style: italic;
}
.news h4 a {
    color: #284949;
}
</style>



<div class="container news">

    <div class="row">

		

    	<?
		if($page>0){
			$page=(int)$page;
			$ptk=mysqli_query($baza,'select * from RehabilitationWorks where  ID='.$page);
			if($row=mysqli_fetch_array($ptk,1)){?> 
				<div class="post">
					<h2><?=LangPart($row['Header'])?></h2>
					
					
					
					
<script src="/js/jssor_slider.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jssor_class.css">

<div id="jssor_1" style="position: relative; margin: 16px auto; top: 0px; left: 0px; width: 800px; height: 616px; overflow: hidden; visibility: hidden;"><!-- Loading Screen -->
	<div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
		<div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
		<div style="position:absolute;display:block;background:url('/img/loading.gif') no-repeat center center; background-size:7%; top:0px;left:0px;width:100%;height:100%;"></div>
	</div>
	<div id="jssor_slides" data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width: 800px; height: 600px; overflow: hidden;">
			<div data-p="112.50" style="display: none;"><img data-u="image" src="/<?=$row['Photo']?>" />
			</div>
		<?
		$Media=json_decode($row['Media'],true);
		foreach($Media as $ph){?> 
			<div data-p="112.50" style="display: none;"><img data-u="image" src="/<?=$ph['Image']?>" />
			</div>
			<? }
		?>
	</div>
	<!-- Bullet Navigator -->
	<div data-u="navigator" class="jssorb01" style="bottom:24px;right:16px;">
		<div data-u="prototype" style="width:12px;height:12px;"></div>
	</div>
	<!-- Arrow Navigator -->
	<span data-u="arrowleft" class="jssora02l" style="top:0px;left:8px;width:55px;height:55px;" data-autocenter="2"></span>
	<span data-u="arrowright" class="jssora02r" style="top:0px;right:8px;width:55px;height:55px;" data-autocenter="2"></span>
</div>
<script>
w=$('.post').width()+20;
console.log(w);
//$('#jssor_1, #jssor_slides').width(w).height(w/2+10);
//$('#jssor_slides').width(w).height(w/2);
jssor_1_slider_init();
</script>
<!-- #endregion Jssor Slider End -->
					
					
					
						<div class="post_info clearfix">
							<div class="post-left">
								<ul>
									<li><i class="icon-calendar-empty"></i><span><?=BlogDTime($row['DTime'])?></span></li>
									<? /*
									<li><i class="icon-user"></i>By <a href="#">John Smith</a></li>
									<li><i class="icon-tags"></i>Tags <a href="#">Works</a> <a href="#">Personal</a></li>
									*/ ?>
								</ul>
						   </div>
						   <? // <div class="post-right"><i class="icon-comments"></i><a href="#">25 </a>Comments</div>   ?>
						   <div align="right">
								<div class="fb-like"  data-href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>" 
									data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>
							</div>
					   </div>                                
						<div class="blogText"><?
						
						$Text=LangPart($row['Text']);
						echo embedYoutube($Text);
						?></div>
				   </div> <!-- end post -->
				   <hr>
				   
				   <!--  FB Comments -->
				   <div class="fb-comments" data-href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>" data-width="100%" data-numposts="10"></div>
				   
				<? } else info(_Interface('ასეთი გვერდი არ მოიძებნა'));
			}
		else{
			
			$ptkc=mysqli_query($baza,'select COUNT(*) as cc from RehabilitationWorks ');
			$row=mysqli_fetch_array($ptkc,1);
			$Count=$row['cc'];
			// Paging($Count, $st, $RowsPerPage, $url='')
			$RowsPerPage=12;
			if(isset($_GET['st'])) $st=(int)$_GET['st']; else $st=0;
			$ptk=mysqli_query($baza,'select * from RehabilitationWorks  order by DTime desc limit '.$st.','.$RowsPerPage);
			while($row=mysqli_fetch_array($ptk,1)){?> 
				<div class="col-md-3 col-sm-4 post">
					<div class="news_img" >
                        <a href="/<?=$Lang?>/<?=$menu?>/<?=$row['ID']?>">
                    		<img src="/<?=$row['Photo']?>" alt="" class="picture img-responsive">
						</a>
                    </div>
					<div class="dates">
						<span><?=BlogDTime($row['DTime'])?></span>
					</div>
					<h4><a href="/<?=$Lang?>/<?=$menu?>/<?=$row['ID']?>"><?=LangPart($row['Header'])?></a></h4>
					<p><?=smallText(LangPart($row['Text']),100)?></p>
					
				</div> <!-- end post -->

			   
               
			   <? } ?> 
		<div style="clear:both"></div> 
		<hr>
		<div align="center">		
		<? Paging($Count, $st, $RowsPerPage); ?>
			</div>
		<?	} ?>

       
       


  </div><!-- end row-->
  </div> <!-- end container-->
  
