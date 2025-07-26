<?
if($Action=='CraftGallery'){ // ==================== CraftGallery
	
	
	//print_r($_POST);
	
	$Table='CraftGallery';
	
	if(isset($_POST['NID'])){
		$NID=(int)$_POST['NID'];
		$Header=addslashes(json_encode($post['Header'],256));
		$Descriptions=$post['Description'];
		foreach($Descriptions as $LChar=>$Description){
			$Description=smartFilter($Description);
			$Descriptions[$LChar]=$Description;
			}
		$Description=addslashes(json_encode($Descriptions,256));

		$OldMedia=array();
		if($NID>0){
			$ptk=mysqli_query($baza,'select * from '.$Table.' where ID='.$NID);
			if($row=mysqli_fetch_array($ptk,1))
				$OldMedia=json_decode($row['Media'],true);
			}

		$mc=mctime();
		$Media=$_FILES['Media'];
		//print_r($Media); echo '<br>';
		$Media=reArrayFiles($Media);
		//print_r($Media); echo '<br>';
		foreach($Media as $Mk=>$MM){
			if(file_exists($MM['tmp_name'])){
				$src=$MM['tmp_name'];
				$name=$MM['name'];
				$ext=getExt($name);
				if(isImage($ext)){
					$dst='img/crafts/'.$Table.'_'.$mc.'_'.$Mk;
					img_resize($src, '../'.$dst.'_s', $ext, $img_w=600, $img_h=600,  $txt='', $resExt=$ext);
					list($width, $height, $type, $attr) =getimagesize($src);
					img_resize($src, '../'.$dst, $ext, $img_w=1200, $img_h=1200,  $txt='', $resExt=$ext);
					$OldMedia[]=array('Type'=>'Image','Image'=>$dst.'.'.$ext,'Thumb'=>$dst.'_s.'.$ext); // ,'Title'=>$MediaTitle,'SubTitle'=>$MediaSubTitle
					}
				}
			}



		$Media=addslashes(json_encode($OldMedia,256));
		if($NID>0) $sql='update '.$Table.' set  Header="'.$Header.'", Description="'.$Description.'", Media="'.$Media.'" where ID='.$NID;
			else $sql='insert into '.$Table.' ( Header, Description, Media) values ( "'.$Header.'", "'.$Description.'", "'.$Media.'")';
		
		//echo $sql.'<br><br><br>';
		
		$res=mysqli_query($baza,$sql); 
		if(!$res) echo mysqli_error($baza);
		if($NID==0) $NID=mysqli_insert_id($baza);
		


		}
	
	$NID=0; 
	$Header=$Description=array();   
	$Media=array(); $Photo='';
	foreach($Langs as $Lang){
		$Header[$Lang['Char']]=$Description[$Lang['Char']]='';
		}
	if(isset($_GET['ID'])){
		$ID=(int)$_GET['ID'];
		$ptk=mysqli_query($baza,'select * from '.$Table.' where ID='.$ID);
		if($row=mysqli_fetch_array($ptk,1)){
			$NID=$ID;
			foreach($row as $Rk=>$Rv){
				$$Rk=$Rv;
				}
			$Header=json_decode($Header,true);
			$Description=json_decode($Description,true);
			$Media=json_decode($Media,true);
			}
		}
	?>
<style>
.w700{ width:700px}
.w300{ width:300px}
textarea.w700{ height:80px}
</style>
<div style="margin-top:30px">
<form  method="post" enctype="multipart/form-data">
<input name="NID" type="hidden" value="<?=$NID?>">
<table border="1" cellspacing="1" cellpadding="3" align="center" class="normal" style="margin-bottom:30px">
  <tr><td colspan="2" align="center" bgcolor="#CCCCCC"><?=$Table?></td></tr>
<?
	foreach($Langs as $Lang){
	?>
  <tr><td>Header <?=$Lang['Char']?></td>
	  <td><input class="w300" type="text" name="Header[<?=$Lang['Char']?>]" 
				 value="<?=isset($Header[$Lang['Char']])?$Header[$Lang['Char']]:$Header?>"></td></tr>
  <tr><td>Description <?=$Lang['Char']?></td><td><textarea class="w300" type="text" name="Description[<?=$Lang['Char']?>]" ><?=$Description[$Lang['Char']]?></textarea></td></tr>
	<? } ?>
	
  <tr>
    <td>Gallery</td>
    <td style="line-height:25px">
	<input type="file" name="Media[]" multiple>  <br> 

	<div class="MediaDiv">
	<ul id="sortable" num="<?=$NID?>">
	<? $i=0;
	foreach($Media as $Mk=>$Ph){
		$i++; if($i==6) {$i=1; echo '<br />'; }
		$Link='/'.$Ph['Image'];
		$Icon='/'.$Ph['Thumb'];
		echo '<li num="'.$Mk.'"><a href="'.$Link.'" target="Photo"><img src="'.$Icon.'" height=50 border=0 ></a> 
				<a class="DeletePhoto" href="#" NID="'.$NID.'"><img src="img/b_drop.png" width="16" height="16" brder=0 /></a>
				</li>';  
		}
	?>
	</ul>
	</div></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Enter"></td>
    </tr>
</table>
</form>
</div>
<style>
#sortable { list-style-type: none; margin: 0; padding: 0; }
#sortable li {margin: 3px; height: 55px; float: left; background-color:rgba(0,0,0,0.2); padding: 3px; } 
#NewsList tr td:nth-child(1){white-space:nowrap;}
</style>
<script>
function enumPhotos(){
	$('.MediaDiv li').each(function(index, element) {
		$(this).attr('num',index);
		});
	}
$('.DeletePhoto').click(function(e) {
	nid=$(this).attr('NID');
	pid=$(this).closest('li').attr('num');
	var delPhoto=this;
	if(confirmMessage('Delete Photo',''));
	$.ajax({
		url:"interactive.php",
		type:'POST',
		data:'f=deleteTableImage&Table=<?=$Table?>&nid='+nid+'&pid='+pid,
		success: function(data){
			// console.log(data);
			if(data==2){
				$(delPhoto).parent().remove();
				enumPhotos();
				}
			},
		error: function(data){
			console.log(data);
			},
		})
	return false;
	});
function updatePhotosPos(){
	list='';
	$('#sortable li').each(function(index, element) {
		list=list+' '+$(this).attr('num');
		});
	nid=$('#sortable').attr('num');
	$.ajax({
		url:"interactive.php",
		type:'POST',
		data:'f=sortTableMedia&Table=<?=$Table?>&nid='+nid+'&list='+list.substr(1),
		success: function(data){
			// console.log(data);
			enumPhotos();
			},
		error: function(data){
			console.log(data);
			},
		})
	}
$(function() {
	$( "#sortable").sortable({
		stop: function( event, ui ) { updatePhotosPos(); }
		});
	$( "#sortable").disableSelection();
	});
</script>

<table border="1" cellspacing="1" cellpadding="3" align="center" class="normal" id="NewsList" style="margin-top:30px; min-width:600px">
<tr class="HeaderTR">	<td>Date</td>

	<td>Photo</td>
	<td>Pos</td><td>Header</td><td>Actions</td></tr>
<?
if(isset($_GET['delNews'])){
	$DD=(int)$_GET['delNews'];
	// delete used images
	mysqli_query($baza,'delete from '.$Table.' where ID='.$DD);
	}
$ptk=mysqli_query($baza,'select * from '.$Table.' order by ID desc');
while($row=mysqli_fetch_array($ptk,1)){ 
	$Media=json_decode($row['Media'],true);
	$Photo=$Media[0]['Thumb'];
	?>
	<tr num="<?=$row['ID']?>">
		<td><?=$row['DTime']?></td>
		<td><img src="/<?=$Photo?>" height=50 ></td>
		<td class="Pos <?=$row['Pos']>0?'Possed':''?>"><?=$row['Pos']?></td>
		<td class="SelectedTD2" num="<?=$row['ID']?>"><?=$row['Header']?></td>
		<td><a class="Confirm" href="?Action=<?=$Action?>&delNews=<?=$row['ID']?>" title="Delete this News"><img src="img/b_drop.png" width="16" height="16"></a></td>
	</tr>
	<?
	}
?>
</table>
<style>
	.Possed {
		background-color: #8f8;
	}
	.Pos{
		text-align: center;
		cursor: pointer;
		}
	.Pos:hover{
		background-color: #080;
		color: #fff;
	}
</style>
<script>
$('.Pos').on('click',function(){
	var pos=$(this).text();
	var num=$(this).closest('tr').attr('num');
	let pos2 = prompt("შეცვალეთ რჩეულის ნომერი", pos);
	if(pos!=pos2){
		$.ajax({
			url:'interactive.php',
			type:'post',
			dataType:'json',
			data:{f:'setTablePos',Table:"<?=$Table?>",ID:num,Pos:pos2}, 
			})
		.done(function(data){
			//console.log('done',data);
			$.each(data,function(i,e){
				//console.log(i,e);
				$('#NewsList tr[num='+i+'] .Pos').text(e);
				if(e>0) $('#NewsList tr[num='+i+'] .Pos').addClass('Possed');
					else $('#NewsList tr[num='+i+'] .Pos').removeClass('Possed');
				})
			})
		.fail(function(data){
			console.log('fail',data);
			})
		}
})	
</script>

<script>
$(document).ready(function() {
	$(".cledit").cleditor({ width:'100%', height:400, useCSS: false,  
		styles: [["subHeader", "<h2>"], ["Normal", "<p>"]],
		controls:"style bold italic underline | bullets numbering | subscript superscript | alignleft center alignright justify | outdent indent | removeformat | undo redo | link unlink | source",
		bodyStyle:'font-size:13px; line-height:140%; overflow-y:scroll; padding:10px; margin:0; text-align:justify',
		// docCSSFile: '/admin/EventsServices.css',
		});
	});
</script>
<? } ?>