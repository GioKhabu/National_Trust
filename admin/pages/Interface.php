<?
if($Action=='Interface'){ // ==================== Interface
	

	
	?>
	<table border="1" cellpadding="3" align="center" class="normal">
		<?
		if(isset($_GET['did'])){ 
			$DID=$_GET['did'];
			if(isset($Interface[$DID])){
				unset($Interface[$DID]);
				$_SESSION['Interface']=$Interface;
				mysqli_query($baza,'delete from Interface where Name="'.$DID.'"');				
				}
			}
		$i=0;
		foreach($Interface as $Iname=>$Ivalues){ $i++;
			?>
            <tr ><?
				foreach($Langs as $Li=>$Lv){
					$ID=$Ivalues[$Li]['ID'];
					if(isset($Ivalues[$Li])) 
						$Ivalue=$Ivalues[$Li]['Value']; 
						else  $Ivalue='???'; 
					if($Ivalue=='???') $cl='0';  else $cl=''; 
					?>
                    <td class="free<?=$cl?>" lng="<?=$Lv['Name2']?>"><a name="I<?=$i?>"></a><?=$Lv['Name2'].($Lv['Name2']!='ge'?'<img class="translate">':'')?>
					<input type="text" id="Int_<?=$ID?>_<?=$Li?>" name="<?=$Iname?>" value="<?=normalQuotes($Ivalue)?>" class="IntInp"></td>
                    <? }
            ?><td><img src="/admin/img/b_drop.png" width="16" height="16" title="Delete Record" num="<?=$Iname?>" ii="<?=$i?>" class="Delete"/></td></tr>
            <?
			}
        ?>
	</table>
<style>
.free0{background-color:#FD9}
.Delete{cursor:pointer;}
.translate{cursor:pointer; vertical-align:middle; margin:0 5px;}
</style>
<script>
String.prototype.capitalize = function(ARGUMENT){ return this.substring(0, 1).toUpperCase() + this.substring(1); };
	
$('.translate').attr('title','Google Translate').attr('src','/admin/img/b_translate.png');
$('.translate').click(function(e) { 
	id=$(this).closest('td').find('input').attr('id');
	ld=$(this).closest('td').attr('lng');
	t=$(this).closest('tr').find('td[lng=ge]').find('input').val();
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
});
$(document).ready(function(e) {
	$('.Delete').click(function(e) {
		if(confirmMessage($(this).attr('title'),'')){
			location='?Action=Interface&did='+$(this).attr('num')+'#I'+($(this).attr('ii')-1);
			}
		});
	$('.IntInp').change(function(e) {
		id=$(this).attr('id');
		name=$(this).attr('name');
		id=id.split('_');
		v=$(this).val();
		$(this).css('background-color','#F00');
		src=this;
		$.ajax({
			url:"/admin/interactive.php",
			type:'POST',
			data:{f:'setIntValue',id:id[1],li:id[2],n:name, v:v},
			success: function(data){
				console.log(data);
				$(src).css('background-color','#FFF');
				},
			error: function(data){
				console.log('Error');
				console.log(data);
				}
			});
		});
	});
</script>
	<?
	}
?>