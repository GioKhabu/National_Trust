<?
error_reporting(E_ALL ); ini_set('display_errors', '1');  
$NoInterface=1;
include '../conf.php';
include_once '../functions.php';
if(isset($_FILES['imageName']))
	if(file_exists($_FILES['imageName']['tmp_name'])){
		$src=$_FILES['imageName']['tmp_name'];
		$name=$_FILES['imageName']['name'];
		$ext=strtolower(end(explode('.',$name)));
		if(in_array($ext,array('jpg','png','gif'))){
			$mc=mctime();
			$dst='/img/pages/im_'.$mc;
			$image_info = getimagesize($src);
			$image_width = $image_info[0];
			if($image_width>1140)
				img_resize($src, '..'.$dst, $ext, $img_w=1140, $img_h=99999,  $txt='', $ext,
					$AddLogo=false, $AddWatermark=false, $Fit=true, $Resize=false, $Filling=false);
			 	
				else
				move_uploaded_file($src,'..'.$dst.'.'.$ext);
			$dst.='.'.$ext;
			echo '<div id="image">'.$dst.'</div>';
			} else echo  'No Image Extension';
		} else echo  'No Tmp File';
	else echo  'No $_FILES["imageName"]';
?>