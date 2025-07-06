<style>

	hr{ 
		clear: both;
	}
	.sidebar {
		background-color: #0301;
		padding: 10px;
	}
    
    .residents {
        border: 1px solid #888;
        border-radius: 10px;
        overflow: hidden;
        padding: 0;
        text-align: center;
        transition: all .5s linear;
        }
    .residents:hover{
        box-shadow: 5px 5px 10px #0008;
    }
    .residents img{
        border: none;
        }
	
    .Photo img {
        width: 200px;
        margin-bottom: 0;
    }

    @media (min-width:768px){
        .col-sm-6.residents p{
            display: none;
        }
        .col-sm-6.residents h2{
            font-size: 20px;
        }
        .col-sm-6.residents h4{
            font-size: 14px;
        }
        .col-sm-6.residents {
            height: 262px;
            overflow: hidden;
        }
    }
    @media (min-width:992px){
        .col-sm-6.residents p{
            display: block;
        }
        .col-sm-6.residents h2{
            font-size: 1.75em;
        }
        .col-sm-6.residents h4{
            font-size: 1.25em;
        }
        .col-sm-6.residents {
            height: 355px;
            overflow: hidden;
        }
    }
</style>



<div class="container">

    <div class="row">

		  
<div class="col-md-3 col-sm-4 fixewdPage">
    <div class="well"><a href="/<?=$Lang?>/residents/"><h3 align="center"><?=_Interface('რეზიდენტები')?></h3></a>
    <?
    $page=(int)$page;
    $ptk=mysqli_query($baza,'select * from Residents order by Pos');
	while($row=mysqli_fetch_array($ptk,1)){
        $Name=json_decode($row['Name'],true);
        ?> 
        <h5 style="" <?=$row['ID']==urldecode($page)?'class="selected"':''?>><a href="/<?=$Lang?>/residents/<?=$row['ID']?>" style="font-size: 13px;"> <?=$Name[$LangChar]?> </a></h5> 
        <? }?>
    </div>
</div>
        
<div class="col-md-9 col-sm-8 fixewdPage ">
        <?
    
    $ID=(int)$page;
    if($ID>0){
        $ptk=mysqli_query($baza,$sql='select * from Residents where ID='.$ID);
        if($row=mysqli_fetch_array($ptk,1)){
            ?> 
            <div class="post" class="col-md-6 col-sm-4 resident"> 
                <div class="Photo"><img src="/<?=$row['Photo']?>" alt=""></div>
                <h4><?=LangPart($row['Name'])?></h4> 
                <div class="title"><?=LangPart($row['Title'])?></div>
                <div class="About"><?=embedYoutube(LangPart($row['About']))?></div>
            </div> <!-- end post -->
       <? } 
    }else {

    $ptk=mysqli_query($baza,$sql='select * from Residents order by Pos');
    while($row=mysqli_fetch_array($ptk,1)){
        ?> 
        <div class=" col-sm-4">  
            <div class="residents">
                <a href="/<?=$Lang?>/<?=$menu?>/<?=$row['ID']?>"><img src="/<?=$row['Photo']?>" alt="" class="picture img-responsive"></a>
                <h4><a href="/<?=$Lang?>/<?=$menu?>/<?=$row['ID']?>"><?=LangPart($row['Name'])?></a></h4> 
                <p><?=LangPart($row['Title'])?></p>
            </div>
        </div> <!-- end post -->
       <? } 
    }
    ?> 
</div> 


  </div><!-- end row-->
</div> <!-- end container-->
  



