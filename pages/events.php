<style>

	hr{ 
		clear: both;
	}
	.sidebar {
		background-color: #0301;
		padding: 10px;
	}
    
	
    
    @media (min-width:768px){
        .col-sm-6.events p{
            display: none;
        }
        .col-sm-6.events h2{
            font-size: 20px;
        }
        .col-sm-6.events h4{
            font-size: 14px;
        }
        .col-sm-6.events {
            height: 262px;
            overflow: hidden;
        }
    }
    @media (min-width:992px){
        .col-sm-6.events p{
            display: block;
        }
        .col-sm-6.events h2{
            font-size: 1.75em;
        }
        .col-sm-6.events h4{
            font-size: 1.25em;
        }
        .col-sm-6.events {
            height: 355px;
            overflow: hidden;
        }
    }
</style>



<div class="container">

    <div class="row">

		  
<div class="col-md-3 col-sm-4 fixewdPage">
    <div class="well"><a href="/<?=$Lang?>/events/"><h3 align="center"><?=_Interface('ღონისძიებები')?></h3></a>
    <?
    $ptk=mysqli_query($baza,'select Theme from Events group by Theme  order by Theme');
	while($row=mysqli_fetch_array($ptk,1)){
        $Theme=json_decode($row['Theme'],true);
        ?> 
        <h5 style="" <?=$Theme['E']==urldecode($page)?'class="selected"':''?>><a href="/<?=$Lang?>/events/<?=$Theme['E']?>" style="font-size: 13px;"> <?=$Theme[$LangChar]?> </a></h5> 
        <? }?>
    </div>
</div>
        
<div class="col-md-9 col-sm-8 fixewdPage ">
        <?
    
    $ID=(int)$page;
    if($ID==$page){
        $ptk=mysqli_query($baza,$sql='select * from Events where ID='.$ID);
        if($row=mysqli_fetch_array($ptk,1)){
            $Theme=($row['Theme']);
            $Media=json_decode($row['Media'],true);
            ?> 
            <div class="post" class="col-md-6 col-sm-4 event"> 
                <h2><?=LangPart($Theme)?></h2>
                <h4><?=LangPart($row['Header'])?></h4> 
                
                

					
<script src="/js/jssor_slider.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jssor_class.css">

<div id="jssor_1" style="position: relative; margin: 16px auto; top: 0px; left: 0px; width: 800px; height: 616px; overflow: hidden; visibility: hidden;"><!-- Loading Screen -->
	<div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
		<div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
		<div style="position:absolute;display:block;background:url('/img/loading.gif') no-repeat center center; background-size:7%; top:0px;left:0px;width:100%;height:100%;"></div>
	</div>
	<div id="jssor_slides" data-u="slides" style="cursor: default; position: relative; top: 0px; left: 0px; width: 800px; height: 600px; overflow: hidden;">
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
                
                
                <p><?=LangPart($row['Description'])?></p>
            </div> <!-- end post -->
       <? } 
    }else {
    $Theme=mysqli_real_escape_string($baza,urldecode($page)); 
    if($Theme!='') $Theme=' and Theme like "%\"E\":\"'.$Theme.'\"%"'; else $Theme='';

    $ptk=mysqli_query($baza,$sql='select * from Events where 1=1  '.$Theme.'  order by Date desc');
    while($row=mysqli_fetch_array($ptk,1)){
        $Theme0=($row['Theme']);
        $Media=json_decode($row['Media'],true);
        $Photo=$Media[0];
        ?> 
        <div class=" col-sm-6 events">  
            <a href="/<?=$Lang?>/<?=$menu?>/<?=$row['ID']?>"><img src="/<?=$Photo['Image']?>" alt="" class="picture img-responsive"></a>
            <? if($Theme==''){?> <h2><?=LangPart($Theme0)?></h2><? } ?>
            <h4><a href="/<?=$Lang?>/<?=$menu?>/<?=$row['ID']?>"><?=LangPart($row['Header'])?></a></h4> 
            <!--<p><?=smallText(LangPart($row['Description']),100)?></p>-->

        </div> <!-- end post -->



       <? } 
    }
    ?> 
</div> 
      
       
       


  </div><!-- end row-->
</div> <!-- end container-->
  
