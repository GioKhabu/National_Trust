<?
if($Action=='Events'){ // ==================== Events
	
    $Img_width=1200;
	$Img_height=900;
	$Img_fit=false;
	$imgFilling=false;
    $Table=$Action;

define('BAZA', $baza);


	if(isset($_POST['NID'])){
		$NID=(int)$_POST['NID'];
		$Date=$_POST['Date']; 
		$Theme=$_POST['Theme'];
		$NewTheme=addslashes(json_encode($post['NewTheme'],256));
        if($Theme=='') $Theme=$NewTheme; else{
            $Theme =explode('|',$Theme.'|');
            $Theme =array('G'=>$Theme[0], 'E'=>$Theme[1]);
            $Theme = addslashes(json_encode($Theme,256));
            }
        
		$Header=addslashes(json_encode($post['Header'],256));
		$Description=addslashes(json_encode($post['Description'],256));

		$OldMedia=array();
		if($NID>0){
			$ptk=mysqli_query($baza,'select * from '.$Table.' where ID='.$NID);
			if($row=mysqli_fetch_array($ptk,1))
				$OldMedia=json_decode($row['Media'],true);
			}
        
		$mc=mctime();
		$Media=$_FILES['Media'];
		$Media=reArrayFiles($Media);

		foreach($Media as $Mk=>$MM){
			if(file_exists($MM['tmp_name'])){
				$src=$MM['tmp_name'];
				$name=$MM['name'];
				$ext=getExt($name);
				if(isImage($ext)){
					$dst='img/events/'.$Table.'_'.$mc.'_'.$Mk;
					
					img_resize($src, '../'.$dst, $ext, $img_w=$Img_width, $img_h=$Img_height,  $txt='', $resExt='jpg', $AddLogo=false, $AddWatermark=false, $Fit=$Img_fit, $Resize=false, $Filling=$imgFilling);
					img_resize($src, '../'.$dst.'_s', $ext, $img_w=$Img_width, $img_h=$Img_height,  $txt='', $resExt='jpg', $AddLogo=false, $AddWatermark=false, $Fit=$Img_fit, $Resize=false, $Filling=$imgFilling);
					$OldMedia[]=array('Type'=>'Image','Image'=>$dst.'.jpg','Thumb'=>$dst.'_s.jpg'); // ,'Title'=>$MediaTitle,'SubTitle'=>$MediaSubTitle
					}
				}
			}

		$Media=addslashes(json_encode($OldMedia,256));
		if($NID>0) $sql='update '.$Table.' set Theme="'.$Theme.'", Date="'.$Date.'",  Header="'.$Header.'", Description="'.$Description.'", Media="'.$Media.'" where ID='.$NID;
			else $sql='insert into '.$Table.' (Theme, Date,  Header, Description, Media) values ("'.$Theme.'", "'.$Date.'", "'.$Header.'", "'.$Description.'", "'.$Media.'")';
		
		//echo $sql.'<br><br><br>';
		
		$res=mysqli_query($baza,$sql); 
		if(!$res) echo mysqli_error($baza);
		if($NID==0) $NID=mysqli_insert_id($baza);
		}
	
	$NID=0; $Date=date('Y-m-d'); 
	$Header=$Description=$Theme=array();   $Tags=array();	$Media=array(); 
	foreach($Langs as $Lang){
		$Header[$Lang['Char']]=$Description[$Lang['Char']]=$Theme[$Lang['Char']]='';
		}
	if(isset($_GET['ID'])){
		$ID=(int)$_GET['ID'];
		$ptk=mysqli_query($baza,'select * from '.$Table.' where ID='.$ID);
		if($row=mysqli_fetch_array($ptk,1)){
			$NID=$ID;
			foreach($row as $Rk=>$Rv){
				$$Rk=$Rv;
				}
			$Theme=json_decode($Theme,true);
			$Header=json_decode($Header,true);
			$Description=json_decode($Description,true);
			$Media=json_decode($Media,true);
			}
		}
	?>
<style>
.SimilarTags{height:0; width:0; position: relative; display: inline-block; top: 8px; left: -155px;}
.SimilarTags table{position:relative; z-index:999; padding:5px; border:1px solid #CCC; box-shadow:4px 4px 20px rgba(0,0,0,0.5); background-color:#FFF; width:auto}
.FixedKey{margin: 1px 2px; padding: 0px 2px; border:1px solid #CCC; white-space: nowrap; float: left; line-height: 21px;}
.FixedKey img{ margin-left:5px; vertical-align: middle; cursor:pointer; }
.KeyItemSel{background-color:#CCC}
.KeyItem:hover{background-color:#EEE}
.TrDiv{margin:2px 0 10px; padding:2px; background-color:#CFC; border:1px solid #4A4; white-space:normal; width:800px}

.w700{ width:700px}
.w300{ width:300px}
textarea.w700{ height:80px}
</style>

<div style="margin-top:30px">
<form id="NewsForm" action="?Action=<?=$Action?>" method="post" enctype="multipart/form-data" >
<input name="NID" type="hidden" value="<?=$NID?>">
<table border="1" cellspacing="1" cellpadding="3" align="center" class="normal" style="margin-bottom:30px">
  <tr><td colspan="2" align="center" bgcolor="#CCCCCC"><?=$Table?></td></tr>
    <tr><td> Theme </td><td style="white-space:normal; line-height:25px; width: 600px;">
        choose
        <select name="Theme" id="Theme" > <option value=""> - - - </option>
        <?
        $ptk=mysqli_query($baza,'select Theme from '.$Table.' group by Theme order by Theme');
        while($row=mysqli_fetch_array($ptk,1)){
            $Th=json_decode($row['Theme'],true);
            echo '<option value="'.implode('|',$Th).'" '.($Th==$Theme?'selected':'').'>'.$Th['G'].'</option>';
            }
		?>
        </select><br>
        or enter new <br>
        <?	foreach($Langs as $Lang){	?>
            <?=$Lang['Char']?><input type="text" name="NewTheme[<?=$Lang['Char']?>]" id="NewTheme<?=$Lang['Char']?>" value="" /><br>
            <? } ?>
	    </td></tr>
	
  <tr><td> Date </td><td><input class="Date" type="text" name="Date" value="<?=$Date?>"></td></tr>
    
    <?	foreach($Langs as $Lang){	?>
				
  <tr><td>Header <?=$Lang['Char']?></td>
	  <td><input class="w700" type="text" name="Header[<?=$Lang['Char']?>]" 
				 value="<?=isset($Header[$Lang['Char']])?$Header[$Lang['Char']]:$Header?>"></td></tr>
  <tr><td>Description <?=$Lang['Char']?></td><td><textarea class="w700 " type="text" name="Description[<?=$Lang['Char']?>]" ><?=$Description[$Lang['Char']]?></textarea></td></tr>
	<? } ?>
	
  
  
  <tr>
    <td>Gallery</td>
    <td style="line-height:25px">
	<input type="file" name="Media[]" multiple>  <br> 
	
	<div class="MediaDiv">
	<ul id="sortable" num="<?=$NID?>">
	<? 
	foreach($Media as $Mk=>$Ph){
		if($Ph['Type']=='Youtube') {
			$Icon='img/YouTube-logo.png'; 
			$Link='https://www.youtube.com/watch?v='.$Ph['File'];
			}else {
			$Link='/'.$Ph['Image'];
			$Icon='/'.$Ph['Thumb'];
			}
		echo '<li num="'.$Mk.'"><a href="'.$Link.'" target="Photo"><img src="'.$Icon.'" height=50 border=0 ></a> 
				<a class="DeletePhoto" href="#" NID="'.$NID.'"><img src="/admin/img/b_drop.png" width="16" height="16" brder=0 /></a>
				</li>'; 
		}
	?>
	</ul>
	</div></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Enter" ></td>
    </tr>
</table>
</form>
</div>

<script>
    $('#NewThemeG, #NewThemeE').on('change',function(e){
        if($(this).val()!='') $('#Theme').val('');
    })
</script>

<style>
#sortable { list-style-type: none; margin: 0; padding: 0; }
#sortable li {margin: 3px; height: 55px; float: left; background-color:rgba(0,0,0,0.2); padding: 3px; } 
#NewsList tr td:nth-child(1){white-space:nowrap;}
</style>
<script>
var submitArticle=0;
function removeOnOutsideClick(target){
	$('body').click(function() { $(target).remove(); });
	$(target).click(function(event){ event.stopPropagation(); });
	$(target).show();
	}

var GSK=0;
var GSKi=0;
var GSKv='';
function selectGSK(GSKi){
	$('.KeyItem').removeClass('KeyItemSel');
	$('.KI'+GSKi).addClass('KeyItemSel');
	kid=$('.KI'+GSKi).attr('kid');
	kv=$('.KI'+GSKi).text();
	$('#Tags').val(kv).attr('keyID',kid);
	}


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
<tr class="HeaderTR">	
    <td>Date</td>
	<td>Theme</td>
	<td>Photo</td>
	<td>Header</td>
    <td>Actions</td></tr>
<?
if(isset($_GET['delNews'])){
	$DD=(int)$_GET['delNews'];
	// delete used images
	mysqli_query($baza,'delete from '.$Table.' where ID='.$DD);
	}
$ptk=mysqli_query($baza,'select * from '.$Table.' order by ID desc');
while($row=mysqli_fetch_array($ptk,1)){ 
    $Media=json_decode($row['Media'],true);
    $Header=json_decode($row['Header'],true);
    $Theme=json_decode($row['Theme'],true);
    $Photo=$Media[0];
	?>
	<tr num="<?=$row['ID']?>">
		<td><?=$row['Date']?></td>
		<td><?=$Theme['G']?></td>
		<?=($Photo['Image']!=''?'<td><img src="/'.$Photo['Image'].'" height=50 ></td>':'<td></td>')?>
		<td class="SelectedTD2" num="<?=$row['ID']?>"><?=$Header['G']?></td>
		<td><a class="Confirm" href="?Action=<?=$Action?>&delNews=<?=$row['ID']?>" title="Delete this News"><img src="/admin/img/b_drop.png" width="16" height="16"></a></td>
	</tr>
	<?
	}
?>
</table>


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