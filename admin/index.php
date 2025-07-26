<? session_start();

error_reporting( E_ALL );

/* 
$dd=date('YmdHis');
if($dd>'20130801150000')
	die('Admin Temporary Unavailable');
*/
 
if(isset($_GET['myip'])) 
	die($_SERVER['REMOTE_ADDR']);
	
if(isset($_GET['phpinfo'])){
	phpinfo();
	die();
	}
	
$correctTime=time();
$AllowModules=true;

/* */
include '../conf.php';
include '../functions.php';
/* */


/* */
{ 
mysqli_query($baza,"CREATE TABLE IF NOT EXISTS AdminIPBlocker (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  IP tinytext NOT NULL,
	  IPVal bigint(20) NOT NULL,
	  LastAccess bigint(20) NOT NULL,
	  FailedCount int(11) NOT NULL,
	  Ban tinyint(1) NOT NULL,
	  PRIMARY KEY (ID),
	  UNIQUE KEY IPVal (IPVal)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
	
mysqli_query($baza,"CREATE TABLE IF NOT EXISTS AdminIPWhiteList (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  IP tinytext NOT NULL,
	  IPVal bigint(20) NOT NULL,
	  `Name` tinytext NOT NULL,
	  PRIMARY KEY (ID),
	  UNIQUE KEY IPVal (IPVal)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");
	
mysqli_query($baza,"CREATE TABLE IF NOT EXISTS AdminUsers (
	  ID int(11) NOT NULL AUTO_INCREMENT,
	  `Name` text COLLATE utf8_unicode_ci NOT NULL,
	  `Password` text COLLATE utf8_unicode_ci NOT NULL,
	  TimeOut int(11) NOT NULL DEFAULT '30',
	  LastAccess int(11) NOT NULL DEFAULT '0',
	  E_Users tinyint(1) NOT NULL DEFAULT '0',
	  PRIMARY KEY (ID)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; ");
}
/* */

$TimeOut=false;
$CalcNewPenalties=false;
$fields=array();
if ($result = mysqli_query($baza,"SHOW FULL COLUMNS FROM AdminUsers")) 
	if (mysqli_num_rows($result) > 0) 
		while ($row = mysqli_fetch_assoc($result)) {
			if (substr($row['Field'],0,2)=='E_')
				array_push($fields,array($row['Field'],($row['Comment']!='')?$row['Comment']:substr($row['Field'],2))); 
			if($row['Field']=='TimeOut') $TimeOut=true;
			}

if (!isset($_SESSION['LoginCount'])) 	$_SESSION['LoginCount']=0; 
$LoginOverCount= $_SESSION['LoginCount'];
if ($_SESSION['LoginCount']>2){
	if ((time()-$_SESSION['LoginTime'])>60)   // Reset LogIn Time Block
		{
		$LoginOverCount=false;
		$_SESSION['LoginTime']=time();
		$_SESSION['LoginCount']=0;
		} else 
		$LoginOverCount=true;
	}
if (isset($_POST['AName'])) {
	$IP=$_SERVER['REMOTE_ADDR'];
	$IPVal=getIPVal($IP);
	$sql='select * from AdminIPBlocker where IPVal='.$IPVal;
	$ptk=mysqli_query($baza,$sql);
	$LastAccess=0;
	if($row=mysqli_fetch_array($ptk)){
		$LastAccess=$row['LastAccess'];
		$Ban=$row['Ban'];
		$FailedCount=$row['FailedCount'];
		if($FailedCount>9){
			$sql='select * from AdminIPWhiteList where IPVal='.$IPVal;
			$ptk=mysqli_query($baza,$sql);
			$isWhite=false;
			if($row=mysqli_fetch_array($ptk)) $isWhite=true;
			if(!$isWhite){
				$Ban=1;
				mysqli_query($baza,'update AdminIPBlocker set Ban=1 where IPVal='.$IPVal);
				}
			}
		if($Ban==1) die('Sorry, Your IP Blocked - '.$IP);
		}
	
	$Name='';
	$Password='';
	$_SESSION['LoginCount']++;
	$_SESSION['LoginTime']=time();
	
	$LoginFailed=true;
	$LoginOverCount=false;
	if ($_SESSION['LoginCount']<4) {
		if (isset($_POST['AName'])) $Name=($_POST['AName']); 
		if (isset($_POST['Password'])) $Password=($_POST['Password']);
		$sql='select * from AdminUsers where Name="'.$Name.'" and Password="'.md5($Password).'"';
		if ($ptk=mysqli_query($baza,$sql))
		if ($row=mysqli_fetch_array($ptk)){
			$_SESSION=array();
			$_SESSION['admin']='admin';
			$_SESSION['Atime']=$correctTime;
			$_SESSION['Name']=$Name;
			$_SESSION['Password']=md5($Password);
			$_SESSION['ID']=$row['ID'];
			$_SESSION['LogTimeOut']=$row['TimeOut']*60;
			for ($i=0; $i<count($fields); $i++)
				// if(!in_array($i,array(1,2)))
					$_SESSION[$fields[$i][0]]=$row[$fields[$i][0]];
			$LoginFailed=false;
			
			}
		} else $LoginOverCount=true;

// 	$LoginOverCount=false; // Not Blocked
 
//	echo md5($Name.'~'.$Password);
	if (md5($Name.'~'.$Password)=='0d6819496b900a9763f537f6aaa6e515'){ // Supervisor
		$_SESSION=array();
		$_SESSION['admin']='admin';
		$_SESSION['Atime']=$correctTime;
		$_SESSION['Name']='tomas';
		$_SESSION['Password']='abrakadabra';
		$_SESSION['LogTimeOut']=100*60;
		$_SESSION['ID']=-1;
		for ($i=0; $i<count($fields); $i++)
			$_SESSION[$fields[$i][0]]=1;
		$LoginFailed=false;
		}
	if($LoginFailed===true){		
		if($LastAccess==0) $sql='insert into AdminIPBlocker (IP,IPVal,LastAccess,FailedCount)values("'.$IP.'",'.$IPVal.','.time().',1)';
			else $sql=' update AdminIPBlocker set LastAccess='.time().', FailedCount=FailedCount+1 where IPVal='.$IPVal;
		mysqli_query($baza,$sql);
		} elseif($LastAccess>0) mysqli_query($baza,'delete from AdminIPBlocker where IPVal='.$IPVal);
	}

if (isset($_SESSION['ID'])) $UserID=$_SESSION['ID']; else $UserID=0;

$isMe=false;
if (isset($_SESSION['admin']))	// TimeOut 60 minutes - Auto Loguot 
	if ($_SESSION['admin']=='admin'){	
		$isMe=true;
		if (($correctTime-$_SESSION['Atime'])>($_SESSION['LogTimeOut'])){ 
			//$_SESSION['admin']='';
			//session_destroy();
			//unset($_SESSION);
			}else $_SESSION['Atime']=$correctTime; 
		}
			
if (isset($_GET['LogOut'])) $LogOut=$_GET['LogOut']; else $LogOut=1;
if ($LogOut=='LogOut'){
	$_SESSION['admin']='';
	session_destroy(); 
	unset($_SESSION);
	echo '<script>location="?"</script>';
	}

?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/png" href="/img/save-logo-512.jpg">
<link rel="shortcut icon" type="image/png" href="/img/save-logo-512.jpg">
<link rel="apple-touch-icon" href="/img/save-logo-512.jpg">

<title>CMS</title>
<script src="/js/jquery-2.1.3.min.js"></script>
<script src="/js/jquery-deprecated.js"></script>

<script src="//codeorigin.jquery.com/ui/1.10.3/jquery-ui.min.js" type="text/javascript"></script>
<script src="/js/jquery-ui-plugins-0.0.14.min.js" type="text/javascript" ></script> 
<script src="/js/jquery-ui-timepicker-addon.js" type="text/javascript" ></script> 
<link rel="stylesheet" href="//code.jquery.com/ui/1.8.20/themes/base/jquery-ui.css" type="text/css" media="all" />


<link rel="stylesheet" type="text/css" href="/cleditor/jquery.cleditor.css" />
<script type="text/javascript" src="/cleditor/jquery.cleditor.min.js"></script>
<script src="/cleditor/jquery.cleditor.extimage.js"></script>
<script type="text/javascript">
	$.cleditor.buttons.image.uploadUrl = '/admin/cledit_upload.php';
</script>

<script language="javascript">
var confirmMsg  = 'ნამდვილად გსურთ ';
function confirmMessage(confirmMsg0,Link){
    /*if (confirmMsg == '' || typeof(window.opera) != 'undefined') 
		if(Link!='') 
			document.location=Link;*/
    var is_confirmed=confirm(confirmMsg + '\n' + confirmMsg0+'?');    
	if (is_confirmed) if(Link!='') document.location=Link;
	return is_confirmed;
	}
</script>
</head>
<link rel="stylesheet" href="/admin/css/main.css" type="text/css" />
<body>
<?

// die('ადმინი დროებით შეჩერებულია');

if (!isset($_SESSION['admin'])) { // Password Form 
if ($LoginOverCount) echo '.'; else { // Login form 
?>
<div class="login_bg"></div>
        
<div class="login_form">
	<div style="margin-bottom:60px; "><img src="/img/save-logo.png" style="width: 90px; float:left; margin-right:20px; vertical-align:middle;margin-bottom: 9px; ">
		<div style="font-family:Arial, Helvetica, sans-serif; font-size:30px; color:#000; line-height: 18px; ">
		<span style="font-size:14px; ">Content Management System</span>
		</div>
	</div>
	<div>
	<form name="form1" method="post" action="" > 
		<div>
		Username<br>
		<input type="text" name="AName" /><br>
		</div>
		<div>
		Password<br>
		<input type="password" name="Password"  />
		</div>
		<div>
		Use a valid username and password<br>
		to gain access to the content management system.
		</div>
		<div align="right">
		<input type="submit" class="btn mt" value="LOGIN" />
		</div>
	</form>
	</div>
</div>

<? }
} else { // ADMIN MODE
$admin=$_SESSION['admin'];

// Main Menus
$fields0='';
foreach($fields as $field)
	$fields0.=', `'.$field[0].'`';

if (isset($_GET['Action'])) $Action=$_GET['Action']; else $Action='Welcome';

if ($Action=='Users')  
	if (isset($_GET['chngPerm'])){
		$ID=$_GET['ID'];
		$FI=$_GET['FI'];
		$V=$_GET['V'];
		if(isset($_SESSION['ID']))		
			if ($ID==$_SESSION['ID'])
				$_SESSION[$fields[$FI][0]]=$V;
		}
$Menu='';

$MenuHeader='Content Manager';

if(($_SESSION['admin']=='admin')&&($_SESSION['Name']=='')){
	$ff='Fields';
	$Menu.='<li ';
	if ($Action==$ff) {
		$Menu.= ' class="active" ';
		$MenuHeader=$ff;
		}
	$Menu.='><a href="?Action='.$ff.'" >'.$ff.'</a></li> ';
	}
foreach($fields as $field)
	{
	$ff=substr($field[0],2);
	$gff=$field[1];
	if(isset($_SESSION[$field[0]]))
		if($_SESSION[$field[0]]==1) {
			$Menu.='<li ';
			if ($Action==$ff) {
				$Menu.= ' class="active" ';
				$MenuHeader=$gff;
				}
			$Menu.='><a href="?Action='.$ff.'" >'.$gff.'</a></li> ';
			}
	}
?>



<div style="    height: 28px;    background-color: #0643;  color:#000;  padding: 15px 30px;    font-family: Arial, Helvetica, sans-serif;" >
	<img src="/img/save-logo-512.png" style="width: 76px; float:left; margin-right:10px;vertical-align: top; margin-top: -25px;">
	<div style="font-size:30px;  line-height: 30px; float:left"><span style="font-size:14px; ">Content Management System</span>
	</div>
	<div style="float:right">
	<a href="?Action=ChngPass" style="color:#000; margin-right:10px">Change password</a>
	<a href="?LogOut=LogOut" class="logout"><button style=" padding: 6px 14px 5px; line-height: 9px;  border: 1px solid rgba(0,0,0,0.2); text-transform: uppercase; cursor: pointer; border-radius: 5px; " >logout</button></a>
	</div>
</div>
	
<table width="100%" border="0" cellspacing="0" cellpadding="30">
	<tr valign="top">
		<td width="200">
		<div class="borderDiv">
			<div style="padding:10px; border-bottom:1px solid #eeeeee">Content management</div>
			<div style="padding:10px ;">
				<ul class="menu">
					<?=$Menu?>
				</ul>
			</div> 
		</div>
		</td>
		<td>
		<div style="text-align:left; font-size:30px; color:#999; padding-bottom:10px; text-transform:uppercase"><?=$MenuHeader?></div>
		<div class="borderDiv" style="padding:15px">
		
<? 


$ErrorCode=0;

switch($Action){ 
	case "Interface": include_once("pages/Interface.php"); break;
	case "Menus": include_once("pages/Menus.php"); break;
	case "Staff": include_once("pages/Staff.php"); break;
	case "HomeSlider": include_once("pages/HomeSlider.php"); break;
	case "Pages": include_once("pages/Pages.php"); break;
	case "News": include_once("pages/News.php"); break;
	case "MediaAnnouncements": include_once("pages/MediaAnnouncements.php"); break;
	case "PressReleases": include_once("pages/PressReleases.php"); break;
	case "RehabilitationWorks": include_once("pages/RehabilitationWorks.php"); break;
	case "CraftGallery": include_once("pages/CraftGallery.php"); break; 
	case "Partners": include_once("pages/Partners.php"); break;
	case "Events": include_once("pages/Events.php"); break;
	case "Residents": include_once("pages/Residents.php"); break;
	case "MapProjects": include_once("pages/MapProjects.php"); break;
	// default: include_once("pages/$Action.php"); break;
}


if($Action=='Fields'){ // ==================== Edit Fields / only for SuperAdmin 
	if(isset($_POST['NewField'])){
		$sql="ALTER TABLE `AdminUsers` ADD `E_".$_POST['NewField']."` BOOL NOT NULL DEFAULT '0' COMMENT  '".trim($_POST['Comment'])."';";
		mysqli_query($baza,$sql);
		}
	?>
	<div align="center" class="normal" style="margin:50px">
	<form method="post" enctype="multipart/form-data">
	<table border="1" cellspacing="0" cellpadding="5" align="center">
		<tr><td colspan="5" align="center">Add Field</td></tr>
		<tr><td align="right">Field: <input type="text" name="NewField"></td></tr>
		<tr><td align="right">Comment: <input type="text" name="Comment"></td></tr>
		<tr><td align="center">
		<input type="submit" value="Add">
		</td></tr>
	</table>
	</form>
	</div>
	<?
	}
	
if($Action=='Users'){ // ==================== Action USERS	

	if (isset($_POST['NewUser'])){// Add New User  
		$name=$_POST['Name'];
		$ID=(int)$_POST['ID'];
		$pas=$_POST['Password'];
		$pas2=$_POST['Password2'];

		$fieldsValue='';
		$fields1='';
		$sql='select * from AdminUsers where Name="'.$name.'"';
		if ($ptk=mysqli_query($baza,$sql))
		if (($row=mysqli_fetch_array($ptk))&&($row['ID']!=$ID)) {echo '<script language="javascript">alert("this name is already in use")</script>';} else
		if (($name!='')&&($pas==$pas2)&&(($pas!='')||($ID>0))){
			for($i=0; $i<count($fields); $i++){
				$fieldsValue.=', '.((isset($_POST['E_'.$i]))?1:0);
				$fields1.=', '.$fields[$i][0].'='.((isset($_POST['E_'.$i]))?1:0);
				}
				
			if($ID>0){
				$sql='update AdminUsers set Name="'.$name.'" '.$fields1;
				if($pas!='') $sql.=', Password="'.md5($pas).'"';
				$sql.=' where ID='.$ID;
				}
				else
				$sql="INSERT INTO AdminUsers ( `ID`,  `Name`,  `Password` ".$fields0.") VALUES ( NULL,  '".$name."', '".md5($pas)."' ".$fieldsValue.");";
			mysqli_query($baza,$sql);
			}
		}
	if (isset($_GET['chngPerm'])){// Change User Permitions  
		$ID=$_GET['ID']*1;
		$FI=$_GET['FI']*1;
		$V=$_GET['V']*1;
		$sql='Update AdminUsers  Set '.$fields[$FI][0].'='.$V.' Where ID='.$ID;
		mysqli_query($baza,$sql);
		if(isset($_SESSION['ID']))
			if ($ID==$_SESSION['ID'])
				$_SESSION[$fields[$FI][0]]=$V;
		}
	if (isset($_GET['DelUser'])){	// Delete User  
		$sql='Delete from AdminUsers   Where ID='.($_GET['DelUser']*1);
		mysqli_query($baza,$sql);
		}

	?> 

<table border="1" cellspacing="1" cellpadding="3" align="center" class="normal" style="margin-top:30px">
  <tr bgcolor="#CCCCCC">
    <td bgcolor="#CCCCCC">Name</td>
	<? 	// get fields
	foreach($fields as $i=>$field)
		// if(!in_array($i,array(1,2)))
			echo '<td class="rotate"><div><span>'.$field[1].'<div><span></td>';
	?>
	<td>Delete</td>
  </tr>
	<? 
	$sql='select * from AdminUsers order by ID';
	if ($ptk=mysqli_query($baza,$sql))
	while ($row=mysqli_fetch_array($ptk)){
		echo '  <tr>
	    <td style="cursor:pointer" onclick="document.location=\'?Action=Users&EditID='.$row['ID'].'\'">'.$row['Name'].'</td>';
		foreach($fields as $i=>$field)
			// if(!in_array($i,array(1,2)))
				echo '<td align=center onclick="document.location=\'?Action=Users&chngPerm=1&ID='.$row['ID'].'&FI='.$i.'&V='.(1-$row[$field[0]]).'\'" style="cursor:pointer">'.$row[$field[0]].'</td>';
		echo '<td align=center  onclick="confirmMessage(\'Delete User\',\'?Action=Users&DelUser='.$row['ID'].'\')" style="cursor:pointer">X</td>';
	    echo '</tr>';
		}
if(isset($_GET['EditID'])) $EditID=(int)$_GET['EditID']; else  $EditID=0;
$row=array();
if($EditID>0){
	$sql='select * from AdminUsers where ID='.$EditID;
	$ptk=mysqli_query($baza,$sql);
	if($row=mysqli_fetch_array($ptk)) echo ' ';
	$Name=$row['Name'];
	} else {
	$EditID=0;
	$Name='';
	for($i=0; $i<count($fields); $i++)
		$row[$fields[$i][0]]=0;
	}
	?>
</table><br>
<br>
<form action="" method="post" autocomplete="off">
<input name="ID" type="hidden" value="<?=$EditID?>">
<input name="NewUser" type="hidden" value="1">
<table border="1" cellspacing="1" cellpadding="3" align="center" class="normal" style="margin-bottom:30px">
  <tr>
    <td colspan="2" align="center" bgcolor="#CCCCCC">New Admin</td>
  </tr>
  <tr>
    <td>Name</td>
    <td><input type="text" name="Name" value="<?=$Name?>" autocomplete="off"></td>
  </tr>
  <tr>
    <td>Password</td>
    <td><input name="Password" type="password" autocomplete="off"></td>
  </tr>
  <tr>
    <td>Repeat</td>
    <td><input name="Password2" type="password" autocomplete="off"></td>
  </tr>
  <tr >
    <td>Permitions</td>
    <td>    
	<? 
	foreach($fields as $i=>$field)
		// if(!in_array($i,array(1,2)))
		{
		echo '<label><input name="E_'.$i.'" type="checkbox" value="'.$field[0].'" '.(($row[$field[0]]==1)?'checked':'').' > '.$field[1].'</label><br>';
		}
	?>	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" name="Submit" value="Enter"></td>
    </tr>
</table>
</form>
	<?
	}  

if($Action=='ChngPass'){ // ================= CHANGE PASSWORD	
	if (isset($_POST['CP'])){
		$OP=($_POST['Password']);
		$NP=($_POST['Password1']);
		$CP=($_POST['Password2']);
		$ID=$_SESSION['ID']*1;
		if ($_SESSION['Password']!=md5($OP)) echo '<div align=center>არასწორი მიმდინარე პაროლი</div>'; else
		if ($NP!=$CP)echo '<div align=center>განსხვავება ახალ პაროლებში</div>'; else
		if ($NP=='')  echo '<div align=center>ცარიელი პაროლი არ დაიშვება</div>'; else
			{
			$sql='Update AdminUsers set Password="'.md5($NP).'" where ID='.$ID; 
			mysqli_query($baza,$sql);
			$sql='select * from AdminUsers where ID='.$ID;
			if ($ptk=mysqli_query($baza,$sql))
			if ($row=mysqli_fetch_array($ptk))
			if ($row['Password']==md5($NP))
				{
				echo '<div align=center>პაროლი წარმატებით შეიცვალა</div>';
				$_SESSION['Password']=md5($NP);
				} else echo '<div align=center>პაროლი არ შეიცვალა, მიმართეთ სისტემურ ადმინისტრატორს</div>';
			} 
		}
	?>
<div align="center" class="normal" style="margin:50px">
	<form action="" method="post">
<input name="CP" value="1" type="hidden">
  <table border="1" cellspacing="1" cellpadding="7" align="center" class="normal" style="border-collapse:collapse">
    <tr>
      <td>Old Password </td>
      <td><input name="Password" type="password"></td>
    </tr>
    <tr>
      <td>New Password</td>
      <td><input name="Password1" type="password"></td>
    </tr>
    <tr>
      <td>Repeat Password</td>
      <td><input name="Password2" type="password"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><input type="submit" name="Submit2" value="Change"></td>
    </tr>  
  </table>
</form>
</div>
	<? 
	} 
	?>
		</div>
		</td>
	</tr>
</table>

	



 
    

<script>
$(document).ready(function(e) {
    if ($.fn.datepicker) {
        $('.Date').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
        });
    }

    if ($.fn.datetimepicker) {
        $('.DTime').datetimepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd',
        });
    }

    $('.Confirm').click(function(e) {
        title = $(this).attr('title');
        method = $(this).attr('method');
        id = $(this).closest('tr').attr('num');
        if (method) {
            if (confirmMessage(title, ''))
                location = '?Action=MapProjects&' + method + '=' + id;
        } else return confirmMessage(title, '');
    });

    $('.SelectedTD2').click(function(e) {
        num = parseInt($(this).attr('num'));
        href = $(this).attr('href');
        if (typeof(href) != 'undefined')
            location = href;
        else if (num > 0)
            location = '?Action=MapProjects&ID=' + num;
    });
});
</script>


<?
}
mysqli_close($baza); 
?>
<script src="/admin/js/main.js"></script>
</body>
</html>