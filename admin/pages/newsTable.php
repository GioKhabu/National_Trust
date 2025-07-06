<?
	/*
	CREATE TABLE IF NOT EXISTS `News` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `Pos` int NOT NULL,
  `ThemeID` int DEFAULT NULL,
  `DTime` datetime NOT NULL,
  `Header` text NOT NULL,
  `Text` longtext NOT NULL,
  `Photo` text NOT NULL,
  `Media` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `Pos` (`Pos`),
  KEY `DTime` (`DTime`),
  KEY `ThemeID` (`ThemeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
	*/
if(!isset($Img_width)) die('Error');
if(!isset($showEndDate)) $showEndDate=false;

$Table=$Action;

define('BAZA', $baza);

function getTagID($W,$TableName){
	$sql='select * from Tags where TableName="'.$TableName.'" and Name="'.$W.'"';
	$ptk=mysqli_query(BAZA,$sql);
	if($row=mysqli_fetch_array($ptk)){
		return $row['ID'];
		}else{ 
		mysqli_query(BAZA,'insert into Tags (TableName,Name) values ("'.$TableName.'", "'.$W.'")');
		return mysqli_insert_id(BAZA);
		}
	}  

function updateTagsArticle($ID,$Tags,$TableName){
    mysqli_query(BAZA,'delete from TagsArticle where TableName="'.$TableName.'" and ArticleID='.$ID); 
    foreach($Tags as $Kv){
        mysqli_query(BAZA,'insert into TagsArticle (TableName, ArticleID, TagID) values ("'.$TableName.'", '.$ID.', '.$Kv['ID'].')');
        }
    }

	if(isset($_POST['NID'])){
		$NID=(int)$_POST['NID'];
		$DTime=$_POST['DTime']; 
		$Header=addslashes(json_encode($post['Header'],256));
		$Texts=$post['Text'];
		foreach($Texts as $LChar=>$Text){
			$Text=smartFilter($Text);
			$Texts[$LChar]=$Text;
			}
		$Text=addslashes(json_encode($Texts,256));

		$OldMedia=array();
		if($NID>0){
			$ptk=mysqli_query($baza,'select * from '.$Table.' where ID='.$NID);
			if($row=mysqli_fetch_array($ptk,1))
				$OldMedia=json_decode($row['Media'],true);
			}

        $AllTags=trim($_POST['AllTags']);
        $AllTags=explode(',',$AllTags);
        $Tags=array();
        foreach($AllTags as $Ki=>$Kv)
            if(trim($Kv)!=''){
            $Kv=explode('|',$Kv);
            if($Kv[1]==0)
                $Kv[1]=getTagID($Kv[0],$Table);
            $Tags[]=array('ID'=>$Kv[1],'Name'=>$Kv[0]);
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
					$dst='img/news/'.$Table.'_'.$mc.'_'.$Mk;
					
					img_resize($src, '../'.$dst, $ext, $img_w=$Img_width, $img_h=$Img_height,  $txt='', $resExt='jpg', $AddLogo=false, $AddWatermark=false, $Fit=$Img_fit, $Resize=false, $Filling=$imgFilling);
					img_resize($src, '../'.$dst.'_s', $ext, $img_w=$Img_width, $img_h=$Img_height,  $txt='', $resExt='jpg', $AddLogo=false, $AddWatermark=false, $Fit=$Img_fit, $Resize=false, $Filling=$imgFilling);
					$OldMedia[]=array('Type'=>'Image','Image'=>$dst.'.jpg','Thumb'=>$dst.'_s.jpg'); // ,'Title'=>$MediaTitle,'SubTitle'=>$MediaSubTitle
					}
				}
			}

		$Youtube=trim($_POST['Youtube']);
		$Youtube=getYouTubeCode($Youtube);
		if($Youtube!=''){
			$Frame=getYouTubeFrame($Youtube);
			$LastMediaID++;
			$OldMedia[]=array('Type'=>'Youtube','File'=>$Youtube,'ID'=>$LastMediaID,'Title'=>$MediaTitle,'SubTitle'=>$MediaSubTitle,'Frame'=>$Frame);
			}

		$Media=addslashes(json_encode($OldMedia,256));
		if($NID>0) $sql='update '.$Table.' set DTime="'.$DTime.'",  Header="'.$Header.'", Text="'.$Text.'", Media="'.$Media.'", Tags="'.addslashes(json_encode($Tags,256)).'" where ID='.$NID;
			else $sql='insert into '.$Table.' (DTime,  Header, Text, Media, Tags) values ("'.$DTime.'", "'.$Header.'", "'.$Text.'", "'.$Media.'", "'.addslashes(json_encode($Tags,256)).'")';
		
		//echo $sql.'<br><br><br>';
		
		$res=mysqli_query($baza,$sql); 
		if(!$res) echo mysqli_error($baza);
		if($NID==0) $NID=mysqli_insert_id($baza);
		
		if($showEndDate){
			$EndDate=$_POST['EndDate'];
			if(substr($EndDate,0,4)>'1900'){
				mysqli_query($baza,'update '.$Table.' set EndDate="'.$EndDate.'" where ID='.$NID); 
			}
		}
		
		if(file_exists($_FILES['Photo']['tmp_name'])){
			$src=$_FILES['Photo']['tmp_name'];
			$name=$_FILES['Photo']['name'];
			$ext=getExt($name);
			if(isImage($ext)){
				$dst='img/news/'.$Table.'_'.$NID.'_'.$mc;
				img_resize($src, '../'.$dst, $ext, $img_w=$Img_width, $img_h=$Img_height, $txt='', $resExt='jpg', $AddLogo=false, $AddWatermark=false, $Fit=$Img_fit, $Resize=false, $Filling=$imgFilling);
				$dst.='.jpg';
				mysqli_query($baza,'update '.$Table.' set Photo="'.$dst.'" where ID='.$NID);
				}
			}
        
        updateTagsArticle($NID,$Tags,$Table);
        
		}
	
	$NID=0; $DTime=date('Y-m-d H:i:s'); $EndDate=null;
	$Header=$Text=array();   $Tags=array();
	$Media=array(); $Photo='';
	foreach($Langs as $Lang){
		$Header[$Lang['Char']]=$Text[$Lang['Char']]='';
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
			$Text=json_decode($Text,true);
			$Media=json_decode($Media,true);
            $Tags=$row['Tags'];
			if($Tags!='')
				$Tags=json_decode($Tags,true);
				else $Tags=array();
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
<form id="NewsForm" action="?Action=<?=$Action?>" method="post" enctype="multipart/form-data" onsubmit="if(submitArticle!=1) return false;">
<input name="NID" type="hidden" value="<?=$NID?>">
<table border="1" cellspacing="1" cellpadding="3" align="center" class="normal" style="margin-bottom:30px">
  <tr><td colspan="2" align="center" bgcolor="#CCCCCC"><?=$Table?></td></tr>
    <tr><td>ტეგები</td><td style="white-space:normal; line-height:25px; width: 600px;"><?
		foreach($Tags as $Kv){
			echo '<span class="FixedKey" keyID="'.$Kv['ID'].'" >'.$Kv['Name'].'<img src="/admin/img/b_drop.png" width="16" height="16" /></span> ';
			}
		?> <input type="hidden" name="AllTags" id="AllTags" value="" />
			<input width="153" type="text" name="Tags" id="Tags" keyID="0" value="" autocomplete="off" onkeyup="getSimilarTags(event,this)"></td></tr>
	
  <tr><td>Date-Time</td><td><input class="DTime" type="text" name="DTime" value="<?=substr($DTime,0,16)?>"></td></tr>
    
<?
 if($showEndDate) {?> 
  <tr><td>End Date</td><td><input class="Date" type="text" name="EndDate" value="<?=$EndDate?>"></td></tr>	

	<?} 
	foreach($Langs as $Lang){
	?>
				
  <tr><td>Header <?=$Lang['Char']?></td>
	  <td><input class="w700" type="text" name="Header[<?=$Lang['Char']?>]" 
				 value="<?=isset($Header[$Lang['Char']])?$Header[$Lang['Char']]:$Header?>"></td></tr>
  <tr><td>Text <?=$Lang['Char']?></td><td><textarea class="w700 cledit" type="text" name="Text[<?=$Lang['Char']?>]" ><?=$Text[$Lang['Char']]?></textarea></td></tr>
	<? } ?>
	
  <tr><td>Photo (<?=$Img_width?> x <?=$Img_height?>)</td><td><input type="file" name="Photo"><?
		if(is_file('../'.$Photo))
			echo '<a href="../'.$Photo.'" target="_blank"><img src="../'.$Photo.'" height=50 border=0 ></a> ';
		?></td></tr>
  
  <tr>
    <td>Gallery</td>
    <td style="line-height:25px">
	<input type="file" name="Media[]" multiple>  <br> 
	<div style="display:none">
	<input type="text" class="w300" name="Youtube" > - Youtube Link <br>
	<input type="text" class="w300" name="MediaTitle" > - Item Title  <br>
	<input type="text" class="w300" name="MediaSubTitle" > - Item Subtitle  <br> 
	</div>
	<div class="MediaDiv">
	<ul id="sortable" num="<?=$NID?>">
	<? $i=0;
	foreach($Media as $Mk=>$Ph){
		// $i++; if($i==6) {$i=1; echo '<br />'; }
		if($Ph['Type']=='Youtube') {
			$Icon='img/YouTube-logo.png'; 
			$Link='https://www.youtube.com/watch?v='.$Ph['File'];
			}else {
			$Link='/'.$Ph['Image'];
			$Icon='/'.$Ph['Thumb'];
			}
		echo '<li num="'.$Mk.'"><a href="'.$Link.'" target="Photo"><img src="'.$Icon.'" height=50 border=0 ></a> 
				<a class="DeletePhoto" href="#" NID="'.$NID.'"><img src="/admin/img/b_drop.png" width="16" height="16" brder=0 /></a>
				</li>';  // <div><input type="text" name="Title_'.$Ph['ID'].'" value="'.$Ph['Title'].'" </div> 				<div><input type="text" name="SubTitle_'.$Ph['ID'].'" value="'.$Ph['SubTitle'].'" </div>
		}
	?>
	</ul>
	</div></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Enter" onmouseup="submitArticleForm(this); "></td>
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
var submitArticle=0;
function removeOnOutsideClick(target){
	$('body').click(function() { $(target).remove(); });
	$(target).click(function(event){ event.stopPropagation(); });
	$(target).show();
	}
function popSimilarTags(){
	$('datalist#autoTags').remove();
	if(GSKv!='')
	$.ajax({
		url:"/admin/interactive.php",
		type:'POST',
		data:'f=getSimilarTags&w='+GSKv,
		success: function(data){
			if(data!=''){
				console.log(data);
				$('<div class="SimilarTags"><table>'+data+'</table></div>').insertAfter('#Tags');
				removeOnOutsideClick('.SimilarTags');
				GSKi=0;
				$('.SimilarTags tr').css('cursor','pointer').click(function(e) {
					kid=$(this).attr('kid');
					kv=$(this).text();
					$('#Tags').val(kv+',').attr('keyID',kid);
					getSimilarTags(event,$('#Tags'));
					}); 
				}
			}
		})
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
function getSimilarTags(e,src){
	clearTimeout(GSK);
	if(e.keyCode==38) { // arrow Up
		GSKi--;
		GSKcount=$('.SimilarTags tr').length;
		if(GSKi<1) GSKi=GSKcount;
		selectGSK(GSKi);
		}else
	if(e.keyCode==40) { // arrow Dn
		GSKi++;
		GSKcount=$('.SimilarTags tr').length;
		if(GSKi>GSKcount) GSKi=1;
		selectGSK(GSKi);
		}else{
		$('.SimilarTags').remove();
		GSKv=$(src).val();
		if(e.keyCode==13) GSKv=GSKv+',';
		divider=GSKv.indexOf(',');
		if(divider>=0){
			W1=GSKv.substr(0,divider);
			W1=$.trim(W1);
			if(W1!=''){
				keyID=$('#Tags').attr('keyID');
				$('<span class="FixedKey" keyID="'+keyID+'">'+W1+'<img src="/admin/img/b_drop.png" width="16" height="16" /></span>').insertBefore('#Tags').after(" ");;
				$('.FixedKey img').click(function(e) {
					$(this).parent().remove();
					});
				}
			$('#Tags').val('');
			}else
			GSK=setTimeout('popSimilarTags()',500);
		}
	}
function submitArticleForm(src){
	var tags='';
	$('.Tags').val($('.Tags').val()+',');
	getSimilarTags(event,$('#Tags'))
	$('.FixedKey').each(function(index, element) {
		keyID=$(this).attr('keyID');
		Word=$(this).text();
		tags+=Word+'|'+keyID+',';
		});
	$('#AllTags').val(tags);
	submitArticle=1; 
	$(src).closest('form').submit();
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
			console.log(data);
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
			console.log(data);
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
<?=($showEndDate?'<td>End Date</td>':'')?>
	<td>Photo</td>
	<td>Pos</td><td>Header</td><td>Actions</td></tr>
<?
if(isset($_GET['delNews'])){
	$DD=(int)$_GET['delNews'];
    $ptk=mysqli_query($baza,'select * from '.$Table.' where ID='.$DD);
    if($row=mysqli_fetch_array($ptk,1)){
        $Photo=$row['Photo'];
        if(file_exists('../'.$Photo)) unlink('../'.$Photo);
        $Media = json_decode($row['Media'],true);
        foreach($Media as $Photo)
            if(file_exists('../'.$Photo['Image'])) unlink('../'.$Photo['Image']);
        }
	mysqli_query($baza,'delete from '.$Table.' where ID='.$DD);
	}
$ptk=mysqli_query($baza,'select * from '.$Table.' order by ID desc');
while($row=mysqli_fetch_array($ptk,1)){ 
	?>
	<tr num="<?=$row['ID']?>">
		<?=($showEndDate?'<td>'.substr($row['DTime'],0,10).'</td>':'<td>'.$row['DTime'].'</td>')?>
		<?=($showEndDate?'<td>'.$row['EndDate'].'</td>':'')?>
		<?=($row['Photo']!=''?'<td><img src="/'.$row['Photo'].'" height=50 ></td>':'<td></td>')?>
		<td class="Pos <?=$row['Pos']>0?'Possed':''?>"><?=$row['Pos']?></td>
		<td class="SelectedTD2" num="<?=$row['ID']?>"><?=$row['Header']?></td>
		<td><a class="Confirm" href="?Action=<?=$Action?>&delNews=<?=$row['ID']?>" title="Delete this News"><img src="/admin/img/b_drop.png" width="16" height="16"></a></td>
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
	