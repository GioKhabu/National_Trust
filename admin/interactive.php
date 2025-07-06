<? session_start();
$res=0;

function getParcelType($ParcelTypeId){
	global $baza;
	$ptk=mysqli_query($baza,'select * from Services where ID='.$ParcelTypeId);
	if($row=mysqli_fetch_array($ptk,1))
		return $row['Type'];
	}

if(isset($_SESSION['admin']))
if(isset($_POST['f'])){
	include '../conf.php';
	$Refresh=''; 
	$NoInterface=true;
	include '../functions.php';
	$f=$_POST['f'];
    if($f=='getSimilarTags'){
        $Word=$_POST['w'];
        $sql='select *  from Tags where Name like "%'.$Word.'%" order by  Name limit 10';
        $ptk=mysqli_query($baza,$sql);
        $res=''; $i=0;
        while($row=mysqli_fetch_array($ptk)){
            $i++;
            $res.='<tr class="KeyItem KI'.$i.'" kid="'.$row['ID'].'"><td>'.$row['Name'].'</td></tr>';
            }
		} else
    if($f=='setTableOrder'){
        $table=$_POST['table'];
        $IDs=$_POST['IDs'];
        $Pos=0;
        $ptk=mysqli_query($baza,'select * from '.$table.' order by FIELD(id,'.implode(',',$IDs).')');
        while($row=mysqli_fetch_array($ptk,1))
            mysqli_query($baza,'update '.$table.' set Pos='.($Pos++).' where ID='.$row['ID']);
        $res=1;
        }else
	if($f=='deleteTableImage'){
		$ID=(int)$_POST['nid'];
		$Pk=(int)$_POST['pid'];
		$Table=$_POST['Table'];
		$ptk=mysqli_query($baza,'select * from '.$Table.' where ID='.$ID);
		if($row=mysqli_fetch_array($ptk,1)){
			$Media=json_decode($row['Media'],true);
			$MM=array();
			foreach($Media as $Mk=>$Mv)
				if($Mk!=$Pk) $MM[]=$Mv;
					else{
					$Ph=$Mv['Image']; if(file_exists('../'.$Ph)) unlink('../'.$Ph);
					$Ph=$Mv['Thumb']; if(file_exists('../'.$Ph)) unlink('../'.$Ph);
					}
			mysqli_query($baza,'update '.$Table.' set Media="'.addslashes(json_encode($MM)).'" where ID='.$ID);
			$res='2';
			}
		}else
	if($f=='setTablePos'){ 
		$ID=$_POST['ID'];
		$Pos=$_POST['Pos'];
		$Table=$_POST['Table'];
		$ptk=mysqli_query($baza,'select * from '.$Table.' where Pos>0 and ID!='.$ID.' order by Pos');
		$P=1;
		while($row=mysqli_fetch_array($ptk,1)){
			if($P==$Pos) $P++;
			mysqli_query($baza,'update '.$Table.' set Pos='.$P.' where ID='.$row['ID']);
			$P++;
			}
		mysqli_query($baza,'update '.$Table.' set Pos='.$Pos.' where ID='.$ID);
		
		$ptk=mysqli_query($baza,'select * from '.$Table.' where Pos>0 order by Pos');
		$P=1;
		while($row=mysqli_fetch_array($ptk,1)){
			mysqli_query($baza,'update '.$Table.' set Pos='.$P.' where ID='.$row['ID']);
			$P++;
			}
		
		$ptk=mysqli_query($baza,'select * from '.$Table.' where Pos>0 or ID='.$ID.' order by Pos');
		$res=array();
		while($row=mysqli_fetch_array($ptk,1)){
			$res[$row['ID']]=$row['Pos'];
			}
		}else
	if($f=='sortTableMedia'){
		$ID=(int)$_POST['nid'];
		$Table=$_POST['Table'];
		$list=explode(' ',$_POST['list']);
		$k=count($list);
		$ptk=mysqli_query($baza,'select * from '.$Table.' where ID='.$ID);
		if($row=mysqli_fetch_array($ptk,1)){
			$Media=json_decode($row['Media'],true);
			$MM=array();
			for($i=0; $i<$k; $i++)
				$MM[]=$Media[$list[$i]];
			$sql='update '.$Table.' set Media="'.addslashes(json_encode($MM)).'" where ID='.$ID;
			mysqli_query($baza,$sql);
			}
		$res='';
		}else
	if($f=='translate'){
		$ls=$_POST['ls'];
		$ld=$_POST['ld'];
		$t=$_POST['t'];
		$id=$_POST['id'];
		if($ls=='ka')
			$res=array('id'=>$id,'txt'=>translate_ka($t,$ls,$ld));
			else
			$res=array('id'=>$id,'txt'=>translate($t,$ls,$ld));
		}else
	if($f=='setIntValue'){
		if(isset($_POST['id'])){
			$ID=(int)$_POST['id'];
			$LID=(int)$_POST['li'];
			$v=$_POST['v'];
			$name=$_POST['n'];
			if($ID==0) $sql='insert into Interface (Name,LangID,Value)values("'.$name.'","'.$LID.'","'.$v.'")';
				else $sql='Update Interface set Value="'.$v.'" where ID='.$ID;
			$ptk=mysqli_query($baza,$sql);
			if(!$ptk) echo mysqli_error($baza).' '.$sql;
			$res=1; 
			$_SESSION['Interface']=getInterface();
			} else $res=0;	
		} else   $res=$f;
	} else print_r($_POST);
if(is_array($res)) echo json_encode($res);
	else echo $res;
?>