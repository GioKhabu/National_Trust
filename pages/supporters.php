
<style>
.regular.Partners{
	/*text-align: center;*/
	}
.regular.Partners .h200 {
    display: block;
    margin: 10px 10px 20px 10px;
    background: linear-gradient(90deg, #0002, transparent);
    padding: 10px;
    min-height: 90px;
}
.regular.Partners .h200:hover {
    background: linear-gradient(90deg, #0424, transparent);
}
    .regular.Partners .h200 div{
        margin-bottom: 10px;
    }
.regular.Partners .h200 img {
    max-width: 190px;
    max-height: 70px;
    filter: grayscale(1) opacity(.5);
    transition: all .5s;
    vertical-align: middle;
    float: left;
    margin-right: 30px;
}
.regular.Partners .h200:hover img {
    filter: none;
    transform: scale(1.2);
	}
.SuppText {
    background: #0001;
    min-height: 20px;
}
</style>
<div class="container">
  <div class="row">
	<div class="fixHeader">
		<h3 align="center"><?=_Interface('პარტნიორები/დონორები')?></h3>
	</div>
	  


		<div class="row news">
			  <section class="regular Partners">
				 <?
				$i=1;
				$ptk=mysqli_query($baza,'select * from Partners order by Pos');
				while($row=mysqli_fetch_array($ptk,1)){
					$Name=json_decode($row['Name'],true);
					$Text=json_decode($row['Text'],true);
					$Name=$Name[$LangChar];
					$Text=$Text[$LangChar];
					$Url=$row['Url'];
				  ?> 
				<div class="h200"><a href="<?=$Url?>" target="_blank"><img src="/<?=$row['Logo']?>" alt="<?=addslashes($Name)?>"><div><strong><?=$Name?></strong></div></a>
                    <?=str_replace(chr(13),'<br>',$Text)?>
                </div>
				  <?
				$i++;
				}?>
			  </section>
		</div>
	

	</div>
</div>