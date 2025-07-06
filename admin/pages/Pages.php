<?
if($Action=='Pages'){ // ==================== Pages 
	
	
	if(isset($_SESSION['Menus']))
		$MENU=$_SESSION['Menus'];
		else
		$MENU=$_SESSION['Menus']=getMenuChilds(0);
	
	if(isset($_GET['FixedPage']))
		$_SESSION['FixedPage']=$_GET['FixedPage']; 
	
	if(isset($_SESSION['FixedPage']))
		$FixedPage=$_SESSION['FixedPage'];
		else $FixedPage='';
		
	
	
	?>
	<div align="center" style="padding:10px 0">
		Select Page 
		<?
		function getMenuSubs($MENU,$mmPref=''){
			global $FixedPage;
			if($mmPref!='') $mmPref.=' -> ';
			$mm='';
			foreach($MENU as $Menu){
				if(($Menu['Controller']==0)){ // &&!isset($Menu['Childs'])
					$MenuName=$Menu['ShortUrl'];
					$mm.='<option value="'.$MenuName.'">'.$mmPref.$Menu['TitleG'].'</option>';
					if($FixedPage=='') $FixedPage=$MenuName;
					}
				if(isset($Menu['Childs']))
					$mm.=getMenuSubs($Menu['Childs'],$mmPref.$Menu['TitleG']);
				}
			return $mm;
			}
		$m=getMenuSubs($MENU);


		$fn='../pages/Page_'.$FixedPage;

	$dateLeft=array();
	if(isset($_POST['LeftTextG'])){
		$LeftTextG=smartFilter($post['LeftTextG'],false); 
		$LeftTextE=smartFilter($post['LeftTextE'],false);
		$f=fopen($fn.'_left','w');
		fwrite($f,$LeftTextG);
		fclose($f);
		
		$f=fopen($fn.'_E_left','w');
		fwrite($f,$LeftTextE);
		fclose($f);
		}
	if(file_exists($fn.'_left'))
		$LeftTextG=file_get_contents($fn.'_left');
	if(file_exists($fn.'_E_left'))
		$LeftTextE=file_get_contents($fn.'_E_left');
	if(!isset($LeftTextG))$LeftTextG='';
	if(!isset($LeftTextE))$LeftTextE='';
	

		?>
		<select class="FixedPageSelect" onChange="location='?Action=<?=$Action?>&FixedPage='+this.value" value="<?=$FixedPage?>">
		<?=$m?>
		</select>
	</div>	
	<?
	$content='';
	$contentE='';
	if($FixedPage!='')
	if(file_exists($fn))
		$content=file_get_contents($fn);
	if(file_exists($fn.'_E'))
		$contentE=file_get_contents($fn.'_E');
		 
	if(isset($_POST['Page'])){
		$content=smartFilter($post['Page'],false);
		$contentE=smartFilter($post['PageE'],false);

		$f=fopen($fn,'w');
		fwrite($f,$content);
		fclose($f);

		$f=fopen($fn.'_E','w');
		fwrite($f,$contentE);
		fclose($f);
		}
	
	?>
 
<link rel="stylesheet" type="text/css" href="../../font-awesome/css/font-awesome.css"> 

<table width="100%" border="0" cellpadding="10">
  <tbody>
    <tr valign="top">
    	<td>
    	<form id="LeftPagesForm" method="post" enctype="multipart/form-data" action="">
		<table align="center" border="1" cellspacing="0" cellpadding="5" style="line-height:18px; margin-bottom:20px; " >
			<tr class="HeaderTR"><td colspan="2">Left side Text</td></tr>
			<tr><td>TextG<br><textarea class="cledit2" name="LeftTextG" ><?=$LeftTextG?></textarea></td></tr>
			<tr><td>TextE<br><textarea class="cledit2" name="LeftTextE" ><?=$LeftTextE?></textarea></td></tr>
			<tr ><td align="center" colspan="2"><input type="submit"></td></tr>
		</table>
</form>
	

</td>
		<td>  

	<form id="PagesForm" method="post" enctype="multipart/form-data" action="?Action=<?=$Action?>">
		<table align="center" border="1" cellspacing="0" cellpadding="5" style="line-height:18px; margin-bottom:20px;  ">
        <tr class="HeaderTR"><td colspan="2">Main Text</td></tr>
			<tr><td width="700">Ge<br>
				<textarea class="cledit" name="Page" style="width:700px" rows="10"><?=$content?></textarea></td></tr>
			<tr><td width="700">En<br>
				<textarea class="cledit" name="PageE" style="width:700px" rows="10"><?=$contentE?></textarea></td></tr>
			<tr ><td align="center" colspan="2"><input type="submit"></td></tr>
		</table>
	</form>
</td>
    </tr>
  </tbody>
</table>
<script>
$('select.FixedPageSelect').each(function(index, element) {
	var v=$(this).attr('value');
	$(this).find('option').each(function(index, element) {
		vv=$(this).attr('value');
		if(v==vv) $(this).attr('selected','selected')
		});
	});
</script>
<script>

$(document).ready(function() {

	$(".cledit2").cleditor({ width:300, height:400, useCSS: false, 
		styles: [["Normal", "<p>"], ["subHeader 3", "<h3>"], ["subHeader 2", "<h2>"], ["Button", "<h5>"], ["Blocks", "<section>"]],
		controls:"style bold italic underline | bullets numbering | subscript superscript | alignleft center alignright justify | outdent indent | removeformat | undo redo | image | link unlink | source",
		bodyStyle:'font-size:13px; line-height:140%; overflow-y:scroll; padding:10px; margin:0; text-align:justify',
		docCSSFile: '/admin/css/cleditor_doc.css?v2',
		});
	
	$(".cledit").cleditor({ width:'100%', height:400, useCSS: false,  
		styles: [["Normal", "<p>"], ["subHeader 3", "<h3>"] , ["subHeader 2", "<h2>"] , ["subHeader 1", "<h1>"] ], 
		controls:"style bold italic underline | bullets numbering | subscript superscript | alignleft center alignright justify | outdent indent | removeformat | undo redo | image | link unlink | source",
		bodyStyle:'overflow-y:scroll; padding:10px; margin:0; text-align:justify',
		docCSSFile: '/admin/css/cleditor_doc.css?v2',
		});
    
    var iframes=$(".cledit").siblings('iframe');
    $(iframes).each(function(i,e){
        var body=this.contentDocument.body;
        $(body).find('img').removeAttr('style');
        $(body).find('img').on('click',function(e){ $(this).toggleClass('float');})
        });
    
	});
    
    
</script>
	<?
	}
?>