<?
if($Action=='Staff'){ // ==================== Staff
	if(isset($_POST['SID']))
		if(trim($_POST['Name']['G'])!='')
		{
		$SID=(int)$_POST['SID'];
		$Name=addslashes(json_encode($post['Name'],256));
		$Title=addslashes(json_encode($post['Title'],256));
		$About=addslashes(json_encode($post['About'],256));
		$Socials=addslashes(json_encode($post['Socials'],256));
		
		$Type=$_POST['Type'];
	/*	$NewType=$_POST['NewType'];
		if($NewType!='') $Type=$NewType;*/
		
		if($SID>0) $sql='update Staff set Type="'.$Type.'", Name="'.$Name.'", Title="'.$Title.'", About="'.$About.'", Socials="'.$Socials.'"  where ID='.$SID;
			else $sql='insert into Staff (Type, Name, Title, About, Socials) values ("'.$Type.'", "'.$Name.'", "'.$Title.'", "'.$About.'", "'.$Socials.'")';
		mysqli_query($baza,$sql);
		if($SID==0) {
			$SID=mysqli_insert_id($baza);
			mysqli_query($baza,'update Staff set Pos=ID where ID='.$SID); 
			}
			
		
		$mc=mctime();
		$Photo=$_FILES['Photo'];
		if(file_exists($Photo['tmp_name'])){
			$src=$Photo['tmp_name'];
			$name=$Photo['name'];
			$ext=getExt($name);
			if(isImage($ext)){
				$dst='img/Staff/S_'.$mc;
				img_resize($src, '../'.$dst, $ext, $img_w=400, $img_h=500,  $txt='', $resExt='jpg', $AddLogo=false, $AddWatermark=false, $Fit=false, $Resize=false, $Filling=false);
				$dst.='.jpg';
				$ptk=mysqli_query($baza,'select * from Staff where ID='.$SID);
				if($row=mysqli_fetch_array($ptk,1)){
					$Photo=$row['Photo'];
					if(is_file('../'.$Photo)) unlink('../'.$Photo);
					}
				mysqli_query($baza,'update Staff set Photo="'.$dst.'" where ID='.$SID);
				}
			}
		}
	$Socialss=array('facebook','twitter','google-plus','pinterest');
	$SID=0;  $Photo=''; $Type='';  $Socials=$Name=$Title=$About=array();
	foreach($Langs as $Lang){
		$Name[$Lang['Char']]=$Title[$Lang['Char']]=$About[$Lang['Char']]='';
		}
	
	if(isset($_GET['ID'])){
		$ID=(int)$_GET['ID'];
		$ptk=mysqli_query($baza,'select * from Staff where ID='.$ID);
		if($row=mysqli_fetch_array($ptk,1)){
			$SID=$ID;
			foreach($row as $Rk=>$Rv)
				$$Rk=$Rv;
			$Name=json_decode($Name,true);
			$Title=json_decode($Title,true);
			$About=json_decode($About,true);
			$Socials=json_decode($Socials,true);
			}
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
  <tr><td colspan="2" align="center" bgcolor="#CCCCCC">Staff</td></tr>
  <tr><td>Type</td><td>
  	<select name="Type">
		<option value=""> - - - </option>
		<option value="თავმჯდომარე" <?=$Type=='თავმჯდომარე'?'selected':''?>> თავმჯდომარე </option>
		<option value="თანათავმჯდომარე" <?=$Type=='თანათავმჯდომარე'?'selected':''?>> თანათავმჯდომარე </option>
        <option value="თანამშრომელი" <?=$Type=='თანამშრომელი'?'selected':''?>> თანამშრომელი </option>
		<option value="გამგეობის წევრი" <?=$Type=='გამგეობის წევრი'?'selected':''?>> გამგეობის წევრი </option>
	</select> 
	</td></tr>
  <!--<tr><td>New Type</td><td><input type="text" name="NewType" value=""></td></tr>-->
	
	<? foreach($Langs as $Lang){ ?>
  <tr><td>Name <?=$Lang['Char']?></td>
	  <td><input class="w700" type="text" name="Name[<?=$Lang['Char']?>]" 
				 value="<?=isset($Name[$Lang['Char']])?$Name[$Lang['Char']]:$Name?>"></td></tr>
  <tr><td>Title <?=$Lang['Char']?></td>
	  <td><input class="w700" type="text" name="Title[<?=$Lang['Char']?>]" 
				 value="<?=isset($Title[$Lang['Char']])?$Title[$Lang['Char']]:$Title?>"></td></tr>
  <tr><td>About <?=$Lang['Char']?></td><td><textarea class="w700 cledit" type="text" name="About[<?=$Lang['Char']?>]" ><?=$About[$Lang['Char']]?></textarea></td></tr>
	<? } 
	
	$Socialss=array('facebook','twitter','google-plus','pinterest','linkedin');
	foreach($Socialss as $Social){ ?>
  <tr><td><?=$Social?></td>
	  <td><input class="w700" type="text" name="Socials[<?=$Social?>]" 
				 value="<?=isset($Socials[$Social])?$Socials[$Social]:''?>"></td></tr>
 	<? } 
	
	?>
	
  <tr><td>Photo (400 x 500)</td><td><input type="file" name="Photo"><?
		if(is_file('../'.$Photo))
			echo '<a href="../'.$Photo.'" target="_blank"><img src="../'.$Photo.'" height=50 border=0 ></a> ';
		?></td></tr>
  

  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Enter"></td>
    </tr>
</table>
</form>
</div>

<table border="1" cellspacing="1" cellpadding="3" align="center" class="normal" style="margin-top:30px">
<tr class="HeaderTR"><td>Photo</td><td>Type</td><td>Name</td><td colspan="10">Actions</td></tr>
    <tbody class="sortable">
<?
if(isset($_GET['delStaff'])){
	$DD=(int)$_GET['delStaff'];
	// delete used Photo
	$ptk=mysqli_query($baza,'select * from Staff where ID='.$DD);
	if($row=mysqli_fetch_array($ptk,1)){
		$Photo=$row['Photo'];
		if(is_file('../'.$Photo)) unlink('../'.$Photo);
		mysqli_query($baza,'delete from Staff where ID='.$DD);
		}
	}
	
$ptk=mysqli_query($baza,'select * from Staff order by Pos');
while($row=mysqli_fetch_array($ptk,1)){
    $Name=json_decode($row['Name'],true);
	?>
	<tr num="<?=$row['ID']?>"><td><?=$row['Photo']!=''?'<img src="/'.$row['Photo'].'" height="50">':''?></td><td><?=$row['Type']?></td>
		<td class="SelectedTD2" num="<?=$row['ID']?>"><?=$Name['G']?></td>
		<td><a class="Confirm" href="?Action=<?=$Action?>&delStaff=<?=$row['ID']?>" title="Delete this News"><img src="img/b_drop.png" width="16" height="16"></a></td>
	</tr>
	<?
	}
?></tbody>
</table>
<script>
    $('.sortable').sortable({
        stop:function(e){
            var pos=[];
            $('.sortable tr').each(function(){
                n=$(this).attr('num');
                pos.push(n);
                })
            $.ajax({
                url:'/admin/interactive.php',
                type:'post',
                data:{f:'setTableOrder',table:'Staff',IDs:pos},
                success:function(data){
                    console.log('success',data)
                    },
                error:function(data){
                    console.log('error',data)
                    }
            })
            }
        });

$('.cledit').cleditor({ width:'99%', height:200, useCSS: false, 
	styles: [["subHeader", "<h2>"], ["Normal", "<p>"]],
	controls:"style bold italic underline | bullets numbering | subscript superscript | alignleft center alignright justify | outdent indent | removeformat | undo redo | link unlink | source",
	bodyStyle:'font-size:13px; line-height:140%; overflow-y:scroll; padding:10px; margin:0; text-align:justify',
	// docCSSFile: '/admin/EventsServices.css',
	});
</script>
	<?
	}
?>