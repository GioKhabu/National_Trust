<?php

function saveToLog($Log,$Pref='IP'){
	$today=date('Y.m.d.H',time());
	$cd=dirname(__FILE__);
	$dir=$cd."/logs";
	$subdir1='/'.date('Y'); 			
	$subdir2='/'.date('m');
	$subdir3='/'.date('d');
	if(!is_dir($dir.$subdir1)) mkdir($dir.$subdir1,0777);
		$dir.=$subdir1;
	if(!is_dir($dir.$subdir2)) mkdir($dir.$subdir2,0777);
		$dir.=$subdir2;
	if(!is_dir($dir.$subdir3)) mkdir($dir.$subdir3,0777);
		$dir.=$subdir3; 
		 
	// $OpenToday=fopen($dir."/ACC_".$today.".txt","a");
	if(isset($_SERVER['REMOTE_ADDR'])) $remoteAddress=$_SERVER['REMOTE_ADDR']; else $remoteAddress='0.0.0.0';
	$OpenToday=fopen($dir."/".$Pref."_".$today.".txt","a");
	flock($OpenToday,1);
	flock($OpenToday,2);
	fwrite($OpenToday, date('Y-m-d H:i:s').' - '.$remoteAddress.' - '.$_SERVER['SCRIPT_FILENAME'].' == '.$Log.chr(13).chr(10).chr(13).chr(10));	
	fclose($OpenToday); 	
	}

if(!isset($nolog)){
	$phpInput = file_get_contents('php://input');
	saveToLog('GET='.json_encode($_GET,JSON_UNESCAPED_UNICODE).' POST='.json_encode($_POST,JSON_UNESCAPED_UNICODE).' FILES='.json_encode($_FILES,JSON_UNESCAPED_UNICODE).' PHPInput='.$phpInput,isset($LogPrefix)?$LogPrefix:'IP');
	}
