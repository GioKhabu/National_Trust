<style>
	.Staff img {
		width: 200px;
	}
	.member {
		margin-bottom: 50px;
	}
	.homeTitle {
		font-family: 'BPGCaps';
		font-weight: bold;
		margin: 10px;
	}
	.SName { 
		font-weight: bold;
	}
    .STytle {
        margin: 10px 0;
    }
	.SAbout0 {
		margin-top: 10px;
		padding-left: 15px;
		border-left: 15px solid #4868;
		padding-bottom: 10px;
		border-bottom: 2px solid #4868;
	}
	.SSocials span {
		font-weight: bold;
	}
</style>
<div class="container Staff">
    <div class="row">
        <div class="fixHeader">
            <h3 align="center">
<!--                <?=_Interface('საქართველოს ეროვნული ფონდის საბჭო')?>-->
            </h3>
        </div>
        
		
		<div class="homeTitle"><?=_Interface('გუნდი')?>:</div>
        <?
	$gamg=false;
	$ptk=mysqli_query($baza,'select * from Staff order by Pos');
	while($row=mysqli_fetch_array($ptk,1)){
		$Title=json_decode($row['Title'],true);
		$Name=json_decode($row['Name'],true);
		$About=json_decode($row['About'],true);
		$Socials=json_decode($row['Socials'],true);
		if(!$gamg && $row['Type']=='გამგეობის წევრი'){
			$gamg=true;
			?> <div class="homeTitle"><?=_Interface('გამგეობა')?>:</div>
			<?}?>
		<div class="member">
			<img src="/<?=$row['Photo']?>" alt="">
			<div class="SName"><?=$Name[$LangChar]?></div> 
			<div class="STytle"><?=$Title[$LangChar]?></div>
			
			<div class="SSocials"><?
				foreach($Socials as $Soc=>$Social)
					if(trim($Social)!='')
						echo '<div><span>'.$Soc.': </span>'.$Social.'</div>';
				?></div>
			<div class="SAbout"><?=embedYoutube($About[$LangChar])?></div> 
		</div>
		<?	}	?>

        
    </div>
</div>
