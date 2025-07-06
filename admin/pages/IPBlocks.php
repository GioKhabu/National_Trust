<?
if($Action=='IPBlocks'){ // ==================== IPBlocks
	if(isset($_GET['DWID'])){
		$ID=(int)$_GET['DWID'];
		mysqli_query($baza,'delete from AdminIPWhiteList where ID='.$ID); 
		}
	if(isset($_POST['WIP'])){
		$IP=str_replace(' ','',$_POST['WIP']);
		$IPVal=getIPVal($IP);
		$Name=$_POST['Name'];
		$sql='insert into AdminIPWhiteList (IP,IPVal,Name) values ("'.$IP.'","'.$IPVal.'","'.$Name.'")';
		mysqli_query($baza,$sql);
		}
	if(isset($_GET['DBID'])){
		$ID=(int)$_GET['DBID'];
		mysqli_query($baza,'delete from AdminIPBlocker where ID='.$ID);
		}
	if(isset($_POST['BIP'])){
		$IP=str_replace(' ','',$_POST['BIP']);
		$IPVal=getIPVal($IP);
		$sql='insert into AdminIPBlocker (IP,IPVal,LastAccess,Ban) values ("'.$IP.'","'.$IPVal.'","'.time().'",1)';
		mysqli_query($baza,$sql);
		}
	?>
	<div align="center" style="padding:50px">
	<table border="1" bordercolor="#BBB" cellspacing="0" cellpadding="5" style="border-collapse:collapse; border: none;" align="center">
		<tr>
			<td class="HeaderTR">White List</td>
			<td width="10" style="border: none;"></td>
			<td class="HeaderTR">Black List</td>
		</tr>
		<tr valign="top"><td>Add <span style="float:right; cursor:pointer" onclick="$('#WIP').val('<?=$_SERVER['REMOTE_ADDR']?>')">current</span><br /><form method="post" enctype="multipart/form-data">
			<div style="margin:10px 0">IP&nbsp;<input type="text" name="WIP" id="WIP" title="IP"  style="width:150px"  />
			<input type="submit" value="" style="background:url(img/b32_enter.png) center; background-size: 100%; border:0px ; vertical-align: text-bottom; width:17px; height:17px; margin-left:5px; cursor:pointer" /> </div>
			<div style="margin:10px 0"><input type="text" Name="Name" placeholder="Name"     style="width:180px" /></div></form>
		<table width="180">
		<?
		$ptk=mysqli_query($baza,'select * from AdminIPWhiteList order by IPVal asc');
		while($row=mysqli_fetch_array($ptk))
			echo '<tr><td>'.$row['IP'].' - '.$row['Name'].'</td><td align="right"><a href="?Action='.$Action.'&DWID='.$row['ID'].'"><img src="/img/b_drop.png" width="10" height="10" style="padding:3px" /></a></td></tr>';
		?>
		</table>
		</td>
		<td style="border: none;"></td>
		<td>Add<br /><form method="post" enctype="multipart/form-data">
			<div style="margin:10px 0">IP&nbsp;<input type="text" name="BIP" title="IP"  style="width:150px"  />
			<input type="submit" value="" style="background:url(img/b32_enter.png) center; background-size: 100%; border:0px ; vertical-align: text-bottom; width:17px; height:17px; margin-left:5px; cursor:pointer" /> </div>
			 </form>
		<table width="180">
		<?
		$ptk=mysqli_query($baza,'select * from AdminIPBlocker where Ban=1 order by IPVal asc');
		while($row=mysqli_fetch_array($ptk))
			echo '<tr><td>'.$row['IP'].'</td><td align="right"><a href="?Action='.$Action.'&DBID='.$row['ID'].'"><img src="/img/b_drop.png" width="16" height="16" /></a></td></tr>';
		?>
		</table>
		
		</td>
		</tr>
	</table>
	</div>
	<? } ?>