<?
/* */	
$sql='CREATE TABLE IF NOT EXISTS `Options` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Value` text NOT NULL,
  `Unit` text NOT NULL,
  `Comment` text,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
mysqli_query($baza,$sql); /**/

if($Action=='Options'){ // ==================== Options RequestTimeInterval
	loadOptions();
	$Options=$_SESSION['Options'];
	
	if(isset($_POST['OptName'])){
		$OptNames=$_POST['OptName'];
		$OptValues=$_POST['OptValue'];
		$OptUnits=$_POST['OptUnit'];
		$OptComments=normalQuotes($post['OptComment']);
		foreach($OptNames as $Ok=>$On)
			if($On!=''){
				$Ov=$OptValues[$Ok];
				$Ou=$OptUnits[$Ok];
				$Oc=$OptComments[$Ok];
				$Options[$On]=array('Value'=>$Ov,'Unit'=>$Ou,'Comment'=>$Oc);
				}
		
		foreach($Options as $On=>$Ov)
			if(!in_array($On,$OptNames))
				unset($Options[$On]);
		
		$_SESSION['Options']=$Options;
		saveOptions();
		}
	loadOptions();
	?>
<style>
input.normal{width:200px}
input[readonly]{border:none; outline:none; background-color: #ddd;    color: #999; cursor:default; }
</style>
	<form method="post">
	<table border="1" cellspacing="0" cellpadding="5" class="normal" align="center" style="border-collapse:collapse; margin-top:20px">
		<thead><tr><td>Name</td><td>Value</td><td>Unit</td><td>Comment</td></tr></thead>
	<?
	foreach($Options as $Ok=>$Ov){
		echo '<tr><td><input type="text" name="OptName[]" value="'.$Ok.'" class="normal" '.($isMe?'':'readonly').'></td>
		<td><input type="text" name="OptValue[]" value="'.$Ov['Value'].'" class="normal"></td>
		<td><input type="text" name="OptUnit[]" value="'.$Ov['Unit'].'" class="normal" '.($isMe?'':'readonly').'></td>
		<td><input type="text" name="OptComment[]" value="'.$Ov['Comment'].'" class="normal" '.($isMe?'':'readonly').'></td>
		</tr>';
	}
	if($isMe) { ?>
		<tr><td><input type="text" name="OptName[]" value="" class="normal"></td>
			<td><input type="text" name="OptValue[]" value="" class="normal"></td>
			<td><input type="text" name="OptUnit[]" value="" class="normal"></td>
			<td><input type="text" name="OptComment[]" value="" class="normal"></td>
		</tr>
		<? } ?>
	<tr><td align="center" colspan="10"><input type="submit"></td>
	</table>
	</form> 
	<?
	}

	?>
<style>
input.normal{width:200px}
input[readonly]{border:none; outline:none}
</style>