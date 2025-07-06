<?
if($Action=='Partners'){ // ==================== Partners
	
	/*
	CREATE TABLE IF NOT EXISTS `Partners` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Name` text COLLATE utf8mb3_unicode_ci,
  `Logo` text COLLATE utf8mb3_unicode_ci,
  `Url` text COLLATE utf8mb3_unicode_ci,
  `Pos` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
	*/
	if(isset($_POST['SID'])){
		$SID=(int)$_POST['SID'];
		$Name=addslashes(json_encode($post['Name'],256));
		$Text=addslashes(json_encode($post['Text'],256));
		$Url=$_POST['Url'];
		
		if($SID>0) $sql='update Partners set Name="'.$Name.'", Text="'.$Text.'", Url="'.$Url.'"  where ID='.$SID;
			else $sql='insert into Partners (Name, Text, Url) values ("'.$Name.'", "'.$Text.'", "'.$Url.'")';
		mysqli_query($baza,$sql);
		if($SID==0) {
			$SID=mysqli_insert_id($baza);
			mysqli_query($baza,'update Partners set Pos=ID where ID='.$SID);
			}
		
		$mc=mctime();
		$Logo=$_FILES['Logo'];
		if(file_exists($Logo['tmp_name'])){
			$src=$Logo['tmp_name'];
			$name=$Logo['name'];
			$ext=getExt($name);
			if(isImage($ext)){
				$dst='img/partners/P_'.$SID.'_'.$mc;
				img_resize($src, '../'.$dst, $ext, $img_w=450, $img_h=150,  $txt='', $resExt=$ext, $AddLogo=false, $AddWatermark=false, $Fit=true, $Resize=false, $Filling=false);
				$dst.='.'.$ext;
				mysqli_query($baza,'update Partners set Logo="'.$dst.'" where ID='.$SID);
				}
			}
		}
	
	$SID=0; $Logo='';  $Url='';  $Name=$Text=array();
	foreach($Langs as $Lang){
		$Name[$Lang['Char']]='';
		$Text[$Lang['Char']]='';
		}
	
	if(isset($_GET['ID'])){
		$ID=(int)$_GET['ID'];
		$ptk=mysqli_query($baza,'select * from Partners where ID='.$ID);
		if($row=mysqli_fetch_array($ptk,1)){
			$SID=$ID;
			foreach($row as $Rk=>$Rv)
				$$Rk=$Rv;
			}
		$Name=json_decode($Name,true);
		$Text=json_decode($Text,true);
		}
	?>
<style>
.w700{ width:700px}
.w300{ width:300px} 
textarea.w700{ height:80px}
</style>
<div style="margin-top:30px">
<form id="NewsForm" action="?Action=<?=$Action?>" method="post" enctype="multipart/form-data">
<input name="SID" type="hidden" value="<?=$SID?>">
<table border="1" cellspacing="1" cellpadding="3" align="center" class="normal" style="margin-bottom:30px">
  <tr><td colspan="2" align="center" bgcolor="#CCCCCC">Partners</td></tr>
  <?
	foreach($Langs as $Lang){
	?>
  <tr><td>Name <?=$Lang['Char']?></td>
	  <td><input type="text" name="Name[<?=$Lang['Char']?>]" value="<?=$Name[$Lang['Char']]?>"></td></tr>
  <tr><td>Text <?=$Lang['Char']?></td>
      <td><textarea cols="60" rows="5" name="Text[<?=$Lang['Char']?>]" ><?=$Text[$Lang['Char']]?></textarea></td></tr>
  
	<? } ?>
	
  <tr><td>Url</td><td><input type="text" name="Url" value="<?=$Url?>"></td></tr>
  <tr><td>Logo (450 x 150)</td><td><input type="file" name="Logo"><?
		if(is_file('../'.$Logo))
			echo '<a href="../'.$Logo.'" target="_blank"><img src="../'.$Logo.'" height=50 border=0 ></a> ';
		?></td></tr>
  

  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Enter"></td>
    </tr>
</table>
</form>
</div>

<table border="1" cellspacing="1" cellpadding="3" align="center" class="normal" style="margin-top:30px">
<tr class="HeaderTR"><td>Logo</td><td>Name</td><td colspan="10">Actions</td></tr>
<?
if(isset($_GET['delPartners'])){
	$DD=(int)$_GET['delPartners'];
	// delete used Logo
	mysqli_query($baza,'delete from Partners where ID='.$DD);
	}
$ptk=mysqli_query($baza,'select * from Partners order by Pos');
while($row=mysqli_fetch_array($ptk,1)){
	$Name=json_decode($row['Name'],true);
	?>
	<tr><td><?=$row['Logo']!=''?'<img src="/'.$row['Logo'].'" height="50">':''?></td>
		<td class="SelectedTD2" num="<?=$row['ID']?>"><?=$Name['G']?></td>
		<td><a class="Confirm" href="?Action=<?=$Action?>&delPartners=<?=$row['ID']?>" title="Delete this News"><img src="img/b_drop.png" width="16" height="16"></a></td>
	</tr>
	<?
	}
?>
</table>

	<?
	}
?>