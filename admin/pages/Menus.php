<?
if($Action=='Menus'){ // ==================== Menus
/*	$sql='CREATE TABLE if not exists `Menus` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ParentID` int(11) NOT NULL,
  `Pos` int(11) NOT NULL DEFAULT 0,
  `ShortUrl` text NOT NULL,
  `TitleG` text NOT NULL,
  `TitleE` text NOT NULL,
  `Controller` tinyint(1) NOT NULL,
  `Active` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `ParentID` (`ParentID`),
  KEY `Controller` (`Controller`),
  KEY `Pos` (`Pos`),
  KEY `Active` (`Active`)
) ENGINE=MyISAM AUTO_INCREMENT=319 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;';
	mysqli_query($baza,$sql);*/
	
	if(isset($_GET['DeleteMID'])){
		$DID=(int)$_GET['DeleteMID'];
		mysqli_query($baza,'delete from Menus where ID='.$DID);
		$Menus=$_SESSION['Menus']=getMenuChilds();
		}
	if(isset($_POST['MenuID'])){
		$MenuID=(int)$_POST['MenuID'];
		$ParentID=(int)$_POST['ParentID'];
		$ShortUrl=$_POST['ShortUrl'];
		$Controller=$_POST['Controller'];
		$Active=$_POST['Active'];
		$TitleG=$_POST['TitleG'];
		$TitleE=$_POST['TitleE'];

		if($MenuID>0)
			$sql='update Menus set ParentID='.$ParentID.', ShortUrl="'.$ShortUrl.'", Controller="'.$Controller.'", Active="'.$Active.'", 
				TitleG="'.$TitleG.'", TitleE="'.$TitleE.'" where  ID='.$MenuID;
			else 
			$sql='insert into Menus (ParentID, ShortUrl, Controller, Active, TitleG, TitleE) values 
				('.$ParentID.', "'.$ShortUrl.'", "'.$Controller.'", "'.$Active.'", "'.$TitleG.'", "'.$TitleE.'")';
		// echo $sql;
		mysqli_query($baza,$sql);
		if($MenuID==0) mysqli_query($baza,'update Menus set Pos=ID where Pos=0');
		$Menus=$_SESSION['Menus']=getMenuChilds();
		}

if(isset($_GET['changeStatus'])){
	$MID=(int)$_GET['changeStatus'];
	mysqli_query($baza,'update Menus set Active=1-Active where  ID='.$MID);
	$Menus=$_SESSION['Menus']=getMenuChilds();
	}

function listMenu($Menus,$Level=0,$Deleted=0){
	global $Action, $MenuCategories, $MenuTypes;
	foreach($Menus as $Mm){
		echo '<li class="ui-state-default" id="list_'.$Mm['ID'].'">
				<div>
					<span class="hendle"></span>
					<a class="mtitle" href="?Action='.$Action.'&MID='.$Mm['ID'].'" style="margin-right:-'.($Level*15).'px">'.$Mm['TitleG'].'</a>
				<dl class="label">
					<dt class="short_url">'.$Mm['ShortUrl'].'</dt>
				</dl>
				
				<span class="action">
					'.(isset($Mm['Childs'])?
						'<img src="img/b_delete_restricted.png" title="წაშლა შეუძლებელია">':
						'<img src="img/b_delete.png" class="delete_icon" title="წაშლა" mid="'.$Mm['ID'].'">').'
					<img src="img/b_showPage.png" class="showPage_icon"  title="საიტზე ნახვა">
					<img src="img/b_active'.$Mm['Active'].'.png" class="changeStatus_icon"  title="სტატუსის შეცვლა">
				</span>					

			</div>';
		if(isset($Mm['Childs'])){
			echo '<ol>';
			listMenu($Mm['Childs'],$Level+1,$Deleted);
			echo '</ol>';
			}
		echo '</li>';
		}
	}
	
	if(isset($_POST['List'])){
		$List=json_decode(stripslashes($_POST['List']),true);
		$Pos=1;
		foreach($List as $line)
			if($line['item_id']!=''){
				mysqli_query($baza,'update Menus set ParentID='.((int)$line['parent_id']).', Pos='.$Pos.' where ID='.(int)$line['item_id']); 
				$Pos++;
				}
		$Menus=$_SESSION['Menus']=getMenuChilds();
		}

$Menus=$_SESSION['Menus']=getMenuChilds();
	?>
<link rel="stylesheet" type="text/css" href="css/menuSortable.css"/>
<script src="/js/jquery.ui.nestedSortable.js"></script>
<table border="0" cellspacing="0" cellpadding="0" align="center" width="100%">
	<tr valign="top">
		<td>
		<div id="loader">
		<ol class="sortable">
		<? listMenu($Menus,0,0); ?> 
		</ol></div>
		<div align="center"  style="margin-top:20px"><button onClick="saveMenu()">გადაადგილების ცვლილებების შეტანა</button></div>
		<form id="menuList" method="post" action="?Action=<?=$Action?>">
			<input type="hidden" name="List" id="mList">
		</form>

<script>
function saveMenu(){
	list = $('ol.sortable').nestedSortable('toArray');
	list=JSON.stringify(list);
	$('#mList').val(list);
	$('#menuList').submit();
	}
 $(document).ready(function(){
	 $('.delete_icon').click(function(e) {
		mid=$(this).attr('mid');
		if(confirmMessage('Delete this menu item',''))
			location='?Action=Menus&DeleteMID='+mid;
		});
	 $('.changeStatus_icon').css({'width':'16px','height':'16px','cursor':'pointer'}).click(function(e) {
		id=$(this).closest('li').attr('id');
		id=id.split('_');
		id=parseInt(id[1]);
		location='?Action=<?=$Action?>&changeStatus='+id;
		});
	 
	$('.sortable li div').css('box-shadow','1px 2px 3px rgba(145, 145, 145, 0.3)');
	$('ol.sortable').nestedSortable({
		disableNesting: 'no-nest',
		forcePlaceholderSize: true,
		handle: 'span',
		helper:	'clone',
		items: 'li',
		maxLevels: 10,
		opacity: .8,
		placeholder: 'placeholder',
		revert: 100,
		tabSize: 25,
		tolerance: 'pointer',
		toleranceElement: '> div'
		});
	});
</script>
		</td>
		<td width="10">		
		</td>
		<td>

<?
$MenuID=0; $TitleG=''; $TitleE=''; $Active=1; $ShortUrl=''; $Controller=0;
if(isset($_GET['MID'])){
	$MID=(int)$_GET['MID'];
	$ptk=mysqli_query($baza,'select * from Menus where  ID='.$MID);
	if($row=mysqli_fetch_array($ptk)){ 
		$MenuID=$MID;
		$TitleG=$row['TitleG'];
		$TitleE=$row['TitleE'];
		$ParentID=$row['ParentID'];
		$Active=$row['Active'];
		$ShortUrl=$row['ShortUrl'];
		$Controller=$row['Controller'];
		}
	}
?>

	<form method="post" action="?Action=<?=$Action?>">
	<input type="hidden" name="MenuID" value="<?=$MenuID?>">
	<table border="1" cellspacing="0" cellpadding="5" align="center" class="normal">
		<tr class="HeaderTR"><td colspan="10">მენიუს <?=$MenuID>0?'რედაქტირება':'დამატება'?></td></tr>
		<tr><td>დასახელება</td><td><input type="text" name="TitleG" id="TitleG" class="formss" value="<?=normalQuotes($TitleG)?>"></td></tr>
		<tr><td>Title</td><td><input type="text" name="TitleE" id="TitleE" class="formss" value="<?=normalQuotes($TitleE)?>"></td></tr>
		<tr><td>მოკლე<br />მისამართი</td><td><input type="text" name="ShortUrl" id="ShortUrl" class="formss" value="<?=$ShortUrl?>"></td></tr>
		<tr><td>მშობელი</td><td><select name="ParentID" class="formss" style="width: 200px;" ><option value="0"> - - - </option>
			<?
			function optionsMenu($Menus,$Level=0,$ParentID=0){
				global $MenuID;
					foreach($Menus as $Mm)
					if($MenuID!=$Mm['ID']){
						echo '<option value="'.$Mm['ID'].'" '.($ParentID==$Mm['ID']?'selected':'').'>'.spaces($Level*2,'&nbsp;').$Mm['TitleG'].'</option>';
						if(isset($Mm['Childs']))
							optionsMenu($Mm['Childs'],$Level+1,$ParentID);
						}
					}
			optionsMenu($Menus,0,$ParentID?$ParentID:0);
			?></select></td></tr>
		<tr><td>ტიპი</td><td><label><input type="radio" value="1" name="Controller" <?=$Controller==1?'checked':''?>> დინამიური</label> 
			<label><input type="radio" value="0" name="Controller" <?=$Controller==0?'checked':''?>> სტატიკური</label></td></tr>
		<tr><td>სტატუსი</td><td><label><input type="radio" value="1" name="Active" <?=$Active==1?'checked':''?>> აქტიური</label> 
			<label><input type="radio" value="0" name="Active" <?=$Active==0?'checked':''?>> პასიური</label></td></tr>
		<tr><td colspan="10" align="center"><input type="submit"></td></tr>
	</table>
	</form>
	
	</td></tr></table>
<script>
	
String.prototype.capitalize = function(ARGUMENT){ return this.substring(0, 1).toUpperCase() + this.substring(1); };


$('#ShortUrl').on('click',function(e){
	var t=$(this).val();
	if(t==''){
		var t=$('#TitleE').val();
		t=t.toLowerCase().replaceAll(' ','_');
		$(this).val(t);
		}
	})
	
$('#TitleE').on('click',function(e){
	var t=$(this).val();
	if(t==''){
		var id='TitleE';
		var t=$('#TitleG').val();
		var ld='en';
		// console.log(id,ld,t);
		$.ajax({
			url:"/admin/interactive.php",
			type:'POST',
			dataType:"json",
			data:{f:'translate',ls:'ka',ld:ld,t:t,id:id}
		}).done(function(data){
			// console.log('done=',data);
			id=data['id'];
			txt=data['txt'];
			$('input[id='+id+']').val(txt.capitalize()).change();
			})
		.fail(function(data){
			console.log('fail=',data);
			td=$('input[id='+id+']').closest('td');
			$(td).css({'background-color':'red'}).animate({'background-color':'white'},2000);

			})
	}
})
	
</script>
	<?
	}
?>