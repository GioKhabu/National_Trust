<?
if($Action=='HomeSlider'){ // ==================== HomeSlider 
	
		
	if(isset($_POST['HeaderE'])){
		$NPID=(int)$_POST['PID'];
		$HeaderE=($post['HeaderE']);
		$HeaderG=($post['HeaderG']);
		
		$SubHeaderE=($post['SubHeaderE']);
		$SubHeaderG=($post['SubHeaderG']);

		
		$UrlE=($post['UrlE']);
		$UrlG=($post['UrlG']);
		
		$Photo='';
		if(file_exists($_FILES['Photo']['tmp_name'])){
			$src=$_FILES['Photo']['tmp_name'];
			$name=$_FILES['Photo']['name'];
			$ext=getExt($name);
			if(isImage($ext)){
				$mc=mctime();
				$Photo=$dst='img/poster/Poster_'.$mc.'_';
				img_resize($src, '../'.$dst, $ext, $img_w=980, $img_h=520,  $txt='', $resExt='jpg', $AddLogo=false, $AddWatermark=false, $Fit=false, $Resize=false, $Filling=false);
				$Photo.='.jpg';
				}
			}elseif($NPID>0){
			$ptk=mysqli_query($baza,'select * from Slider where ID='.$NPID);
			if($row=mysqli_fetch_array($ptk,1)){
				$Data=json_decode($row['Data'],true);
				$Photo=$Data['Photo'];
				}
			}

		$data=array(
			'HeaderE'=>$HeaderE, 'SubHeaderE'=>$SubHeaderE, 'UrlE'=>$UrlE, 
			'HeaderG'=>$HeaderG, 'SubHeaderG'=>$SubHeaderG, 'UrlG'=>$UrlG, 
			'Photo'=>$Photo, 
			);
		if($NPID>0) 
			mysqli_query($baza,'update Slider set Data="'.addslashes(json_encode($data,256)).'" where ID='.$NPID);
		else
			mysqli_query($baza,'insert into Slider (Data) values ("'.addslashes(json_encode($data,256)).'") ');
		if($NPID==0){ 
			$NPID=mysqli_insert_id($baza);
			mysqli_query($baza,'update Slider set Pos=ID where ID='.$NPID);
			}
		}
	
	if(isset($_GET['DID'])){
		$DID=(int)$_GET['DID'];
		$ptk=mysqli_query($baza,'select * from Slider where ID='.$DID);
		if($row=mysqli_fetch_array($ptk,1)){
			$Data=json_decode($row['Data'],true);
			$Photo=$Data['Photo'];
			$Ph='../'.$Photo;
			if(file_exists($Ph)) unlink($Ph);
			mysqli_query($baza,'delete from Slider where ID='.$DID);
			}
		}
	
	
	$HeaderE=''; $SubHeaderE=''; $UrlE=''; 
	$HeaderG=''; $SubHeaderG=''; $UrlG=''; 
	$Photo='';  $PID=0;
	
	if(isset($_GET['ID'])) $PID=(int)$_GET['ID']; else $PID=0;
	
?>
<table width="200" border="1" cellpadding="2" cellspacing="2" align="center" style="margin-bottom: 20px">
  <thead>
    <tr class="HeaderTR">
      <th scope="col">დასახელება</th>
      <th scope="col">ფოტო</th>
      <th scope="col" colspan=10></th>
    </tr>
</thead>
<tbody>
	<?
	$ptk=mysqli_query($baza,'select * from Slider order by Pos');
	while($row=mysqli_fetch_array($ptk,1)){
		$data=json_decode($row['Data'],true);
		if($row['ID']==$PID) {
			foreach($data as $di=>$dv)
				$$di=$dv;
			}
		?>
	<tr<?=$PID==$row['ID']?' class="SelectedTR"':''?>>
      <td num="<?=$row['ID']?>" class="SelectedTD2"><?=isset($data['HeaderG'])?$data['HeaderG']:$data['Header']?></td>
      <td><img src="/<?=$data['Photo']?>" alt="" height="50"></td>
       <td align=center><img src="img/b_drop.png" class="hand" onClick="confirmMessage('ბანერის წაშლა','?Action=HomeSlider&DID=<?=$row['ID']?>')" title="წაშლა"></td>
    </tr>
	<?} ?>
  </tbody>
</table>

<?
	


	
	?>
	<form id="HomeSliderForm" method="post" enctype="multipart/form-data" action="?Action=<?=$Action?>">
		<input type="hidden" name="PID" value="<?=$PID?>">
		<table align="center" border="1" cellspacing="0" cellpadding="5" style="line-height:18px">

			<tr><td>HeaderG<br><input type="text" name="HeaderG" id="HeaderG" value="<?=isset($Header)?$Header:$HeaderG?>" ></td></tr>
			<tr><td>SubHeaderG<br><input type="text" name="SubHeaderG" id="SubHeaderG" value="<?=isset($SubHeader)?$SubHeader:$SubHeaderG?>" ></td></tr>
			<tr><td>UrlG<br><input type="text" name="UrlG" id="UrlG" value="<?=isset($Url)?$Url:$UrlG?>" ></td></tr>

			<tr><td>HeaderE<br><input type="text" name="HeaderE" id="HeaderE" value="<?=$HeaderE?>" ></td></tr>
			<tr><td>SubHeaderE<br><input type="text" name="SubHeaderE" id="SubHeaderE" value="<?=$SubHeaderE?>" ></td></tr>
			<tr><td>UrlE<br><input type="text" name="UrlE" id="UrlE" value="<?=$UrlE?>" ></td></tr>

			<tr><td>Photo (1600 x 442)<br><input type="file" name="Photo"  ><?
			$Ph='../'.$Photo;
			if(is_file($Ph)) echo '<div style="padding-top:10px"><a href="/'.$Photo.'" target="_blank"><img src="/'.$Photo.'" height="50"></a></div>';
			?></td></tr>
			
			<tr ><td align="center"><input type="submit"></td></tr>
		</table>
	</form>
<style>
#HomeSliderForm input[type="text"]{ width:500px}
</style>

<script>
	String.prototype.capitalize = function(ARGUMENT){ return this.substring(0, 1).toUpperCase() + this.substring(1); };

	function translate(id,ld,t){
		console.log(id,ld,t);
		$.ajax({
			url:"/admin/interactive.php",
			type:'POST',
			dataType:"json",
			data:{f:'translate',ls:'ka',ld:ld,t:t,id:id}
		}).done(function(data){
			console.log('done=',data);
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
	$('#HeaderE').on('click',function(e){
		if($(this).val()==''){
			var id='HeaderE';
			var t=$('#HeaderG').val();
			var ld='en';
			translate(id,ld,t);
			}
		})
	$('#SubHeaderE').on('click',function(e){
		if($(this).val()==''){
			var id='SubHeaderE';
			var t=$('#SubHeaderG').val();
			var ld='en';
			translate(id,ld,t);
			}
		})
	$('#UrlE').on('click',function(e){
		if($(this).val()==''){
			var t=$('#UrlG').val();
			t=t.replaceAll('/ge/','/en/');
			$('#UrlE').val(t);
			}
		})
</script>
	<?
	}
?>