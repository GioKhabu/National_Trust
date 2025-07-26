<?
$crlf=chr(13).chr(10); 
if(!isset($Refresh)) $Refresh='1';

$KB=1024;
$MB=$KB*$KB;
$GB=$MB*$KB; 
$TB=$GB*$KB;

function embedYoutube($Text){
    $YText=explode('[Y=',$Text);
    if(count($YText)>1){ 
        for($i=1; $i<count($YText); $i++){
            $Yp=strpos($YText[$i],']');
            $Ylink=substr($YText[$i],0,$Yp);
            $Ylink=getYouTubeCode($Ylink);
            $YText[$i]='<iframe width="100%" height="410" class="youtubeFrame" src="//www.youtube.com/embed/'.$Ylink.'?rel=0" frameborder="0" allowfullscreen></iframe>'.substr($YText[$i],$Yp+1);
            }
        $Text=implode($YText);
        }
    return $Text;
    }
function LangPart($h){
	global $LangChar;
	$h=json_decode($h,true);
	return $h[$LangChar];
	}

function in_multi_array($needle,$array,$column){
	foreach($array as $i=>$ar)
		if($needle==$ar[$column])
			return $i;
	return false;
	}

function getMenuChilds($ParentID=0){
	global $baza;
	$ptk=mysqli_query($baza,'select * from Menus where ParentID='.$ParentID.'  order by Pos'); 
	$Menus=array();
	while($row=mysqli_fetch_array($ptk,1)){
		$Childs=getMenuChilds($row['ID']);
		if(count($Childs)>0) $row['Childs']=$Childs;
		$Menus[$row['ID']]=$row;
		}
	return $Menus;
	}

if(!isset($NoInterface)){
	if(!isset($_SESSION['Langs'.$Refresh]) || !isset($_SESSION['Langs']) || count($_SESSION['Langs'])==0 ){
	/*	mysqli_query($baza,"CREATE TABLE IF NOT EXISTS `Langs` (
	  `ID` int(11) NOT NULL AUTO_INCREMENT,
	  `Name` varchar(3) NOT NULL,
	  `Name2` varchar(2) NOT NULL,
	  `Char` varchar(1) NOT NULL,
	  `FullName` tinytext NOT NULL,
	  `OrigName` tinytext NOT NULL,
	  `Visible` tinyint(1) DEFAULT NULL,
	  PRIMARY KEY (ID),
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1; ");*/

	/*
	INSERT INTO `Langs` (`ID`, `Name`, `Name2`, `Char`, `FullName`, `OrigName`, `Visible`) VALUES
	(1, 'ქარ', 'ge', 'G', 'Georgian', 'ქართული', 1),
	(2, 'Eng', 'en', 'E', 'English', 'English', 1);
	*/

		$_SESSION['Langs']=array();
		$ptk=mysqli_query($baza,'select * from Langs order by ID');
		while($row=mysqli_fetch_array($ptk,1))
			$_SESSION['Langs'][$row['ID']]=$row; 
		}
	$Langs=$_SESSION['Langs'];
	}

$months=array(
	'E'=>array('january','february','march','april','may','june','july','august','september','october','november','december'),
	'G'=>array('იანვარი','თებერვალი','მარტი','აპრილი','მაისი','ივნისი','ივლისი','აგვისტო','სექტემბერი','ოქტომბერი','ნოემბერი','დეკემბერი'),
	);
function newsDTime($DTime,$LangChar){
	global $months;
	$dd=explode(' ',$DTime);
	$d0=explode('-',$dd[0]);
	$d1=explode(':',$dd[1]);
	return ucfirst($months[$LangChar][$d0[1]-1]).' '.$d0[2].', '.$d0[0].' — '.substr($dd[1],0,5);
	}
function newsDate($DTime,$LangChar){
	global $months;
	$dd=explode(' ',$DTime);
	$d0=explode('-',$dd[0]);
	$d1=explode(':',$dd[1]);
	if($dd[0]==date('Y-m-d'))
		return substr($d1,0,5);
		else
		return $d0[2].' '.$months[$LangChar][$d0[1]-1].', '.$d0[0];
	}
function BlogDTime($DT){ // 16th July, 2020
	global $months,$LangChar;
	$month=$months[$LangChar];
	$d=explode(' ',$DT);
	$d=explode('-',$d[0]);
	return $d[2].($LangChar=='E'?'th':'').' '.$month[$d[1]-1].', '.$d[0];
	}
function GeoDate($Date,$LangChar='G'){
	global $months;
	$dd=explode(' ',$Date);
	$d0=explode('-',$dd[0]);
	return $d0[2].' '.$months[$LangChar][$d0[1]-1].', '.$d0[0];
	}

function getInterface(){
	global $Langs;
	global $baza;
	$Interface=array();
	$ptk=mysqli_query($baza,'select * from Interface');
	while($row=mysqli_fetch_array($ptk,1)){
		$Interface[$row['Name']][$row['LangID']]=$row;
		}
	foreach($Interface as $Name=>$Int)
		if(!is_null($Langs))
		foreach($Langs as $Li=>$Lv)
			if(!isset($Interface[$Name][$Li])){
				mysqli_query($baza,'insert into Interface (Name, Value, LangID) values ("'.$Name.'", "???", "'.$Li.'")');
				$id=mysqli_insert_id($baza);
				$Interface[$Name][$Li]=array('Value'=>'???','ID'=>$id);
				}
	return $Interface;
	}

function _Interface($Name0,$LL=0){
	global $LangID,$Interface,$Langs;
	if($LL>0) $LangID=$LL;
	global $baza; 
	$Name=md5($Name0);
	if(!isset($Interface[$Name])){
		foreach($Langs as $LangID0=>$Lang0){
			mysqli_query($baza,'insert into Interface (Name,LangID,Value) values ("'.$Name.'","'.$LangID0.'","'.addslashes($Name0).'")');
			$Interface[$Name][$LangID0]=array('Value'=>$Name0,'ID'=>mysqli_insert_id($baza));
			}
		$_SESSION['Interface']=$Interface;
		// $Interface=$_SESSION['Interface']=getInterface();
		}else
	if(!isset($Interface[$Name][$LangID])){
		mysqli_query($baza,'insert into Interface (Name,LangID,Value) values ("'.$Name.'","'.$LangID.'","'.addslashes($Name0).'")');
		$Interface[$Name][$LangID]=array('Value'=>$Name0,'ID'=>mysqli_insert_id($baza));
		$_SESSION['Interface']=$Interface;
		$Interface=$_SESSION['Interface']=getInterface();
		}
	return $Interface[$Name][$LangID]['Value'];
	}

if(!isset($NoInterface)){
	if(!isset($_SESSION['Interface'.$Refresh])){ 
		$_SESSION['Interface']=getInterface();
		}
	$Interface=$_SESSION['Interface']; 
	}

function getCurSchoolYear(){
	$YY=date('Y');
	$MM=date('m');
	if ($MM<8) $YY--;
	return $YY;
	}

function updateClassStudentsCount(){
	global $baza;
	$ptk=mysqli_query($baza,'SELECT ClassID, count(ClassID) as cc FROM `Students` group by ClassID;');
	while($row=mysqli_fetch_array($ptk,1))
		mysqli_query($baza,'update Classes set StudentsCount='.$row['cc'].' where ID='.$row['ClassID']);
	}

function updateClassThemes($clid,$tid,$v){
	global $baza;
	
	$sql='select Classes.*, TT.ThemeID, TT.Count, CT.ID as CTID from Classes 
			left outer join ThemesTimeTable as TT on TT.ClassLevel=Classes.Level and TT.Semester=1
			left outer join ClassThemes as CT on CT.ClassID=Classes.ID and CT.ThemeID=TT.ThemeID  
			where Classes.ID='.$clid; 
	$ptk=mysqli_query($baza,$sql);
	if(!$ptk) die(mysqli_error($baza).PHP_EOL.$sql);
	while($row=mysqli_fetch_array($ptk,1)){
		if($row['CTID']>0)
			mysqli_query($baza,$sql='update ClassThemes set Count="'.$row['Count'].'" where ID='.$row['CTID']);
		else
			mysqli_query($baza,$sql='insert into ClassThemes (ClassID, ThemeID, Count) values ("'.$row['ID'].'", "'.$row['ThemeID'].'", "'.$row['Count'].'")');
		}
	
	$sql='SELECT ClassThemes.*, ThemesTimeTable.ID as TTTID FROM ClassThemes
			left outer join Classes on Classes.ID = ClassThemes.ClassID 
			left outer join ThemesTimeTable on ThemesTimeTable.ThemeID = ClassThemes.ThemeID and ThemesTimeTable.ClassLevel = Classes.Level  
			where Classes.ID='.$clid.' and ThemesTimeTable.ID is null';
	$ptk=mysqli_query($baza,$sql);
	while($row=mysqli_fetch_array($ptk,1)){
		mysqli_query($baza,$sql='delete from ClassThemes  where ID='.$row['ID']);
		}
	}

function my_simple_crypt( $string, $action = 'e' ) {
	$secret_key = 'my_simple_secret_key';
	$secret_iv = 'my_simple_secret_iv';

	$output = false;
	$encrypt_method = "AES-256-CBC";
	$key = hash( 'sha256', $secret_key );
	$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

	if( $action == 'e' ) $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	else if( $action == 'd' ) $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	return $output;
    }

function number2roman($num,$isUpper=true) {
    $n = intval($num);
	if($n==0) return $num;
    $res = '';
    $roman_numerals = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1 );
    foreach ($roman_numerals as $roman => $number) {
        $matches = intval($n / $number);
        $res .= str_repeat($roman, $matches);
        $n = $n % $number;
    	}
    if($isUpper) return $res;
    	else return strtolower($res);
	}

function array_stripslashes($G){
	if(is_array($G)){
		foreach($G as $Gk=>$Gv)
			$G[$Gk]=array_stripslashes($Gv);
		return $G;
		}
		else return stripslashes($G);
	} 

function translate_ka($Text, $sl, $tl ){
	$url='https://translate.googleapis.com/translate_a/single?client=gtx&dt=t&sl='.$sl.'&tl='.$tl.'&text='.urlencode($Text);
	$result=file_get_contents($url); 
	$res=json_decode($result,true);
	$t=$res[0];
	$rr='';
	if(is_array($t))
		foreach($t as $tt) 
			$rr.=$tt[0];
	return $rr;
	}

function translate($sourceText, $sourceLang, $targetLang ) {
	$url = "https://translate.googleapis.com/translate_a/single";
	$data = array(
		'client' => 'gtx',
		'dt' => 't',
		'sl' => $sourceLang,
		'tl' => $targetLang,
		'q' => $sourceText,
		'text' => $sourceText,
		);

	$postvars = '';
	foreach($data as $key=>$value) {
		$postvars .= $key . "=" . $value . "&";
		}

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch,CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
	curl_setopt($ch,CURLOPT_TIMEOUT, 20);
	$result = curl_exec($ch);

	curl_close($ch);
	
	$res=json_decode($result,true);
	$t=$res[0];
	$rr='';
	if(is_array($t))
		foreach($t as $tt)
			$rr.=$tt[0];
	return $rr;
	}	

function recheckBlockStatus(){
	global $WebUser, $baza;
	$UID=$WebUser['ID'];
	$ptk=mysqli_query($baza,'select * from Users where ID='.$UID);
	if($row=mysqli_fetch_array($ptk,1)){
		$Ban=$row['Ban'];
		if($Ban){
			session_destroy();
			error('თქვენ დაბლოკილი ხართ ადმინისტრატორის მიერ',50);
			die();
			}
		}
	} 

function getCategories(){
	global $baza,$CatList;
	$Categories=$CatList=array();
	$sql='select * from Categories order by ParentID, Pos';
	$ptk=mysqli_query($baza,$sql);
	while($row=mysqli_fetch_array($ptk,1)){
		$row['Name']=json_decode($row['Name'],true);
		$CatList[$row['ID']]=$row;
		if($row['ParentID']==0)
			$Categories[$row['ID']]=$row;
			else
			$Categories[$row['ParentID']]['Sub'][$row['ID']]=$row;
		}
	return $Categories;
	}
		
function withoutQuotes($str){
	return mb_substr($str,1,mb_strlen($str)-2);
	}
function random($min=0,$max=1){
	$r=rand($min,$max);
	return (int)$r;
	}

function setLastActiveTime(){
	global $baza;
	if(isset($_SESSION['WebUser'])){
		$row=$_SESSION['WebUser'];
		mysqli_query($baza,'update Users set LastAccess="'.time().'" where ID='.$row['ID']);
		}
	}

function saveOptions(){
	global $baza; 
	$Options=$_SESSION['Options'];
	foreach($Options as $On=>$Ov){		
		$ptk=mysqli_query($baza,'select * from Options where Name="'.$On.'"');
		if($row=mysqli_fetch_array($ptk,1)){
			$sql='update Options set `Value`="'.normalQuotes($Ov['Value']).'", `Unit`="'.$Ov['Unit'].'", `Comment`="'.normalQuotes($Ov['Comment']).'"  where ID='.$row['ID'];
			mysqli_query($baza,$sql);
			}
			else{
			$sql='insert into Options (Name,`Value`,`Unit`,`Comment`) values ("'.$On.'","'.normalQuotes($Ov['Value']).'","'.$Ov['Unit'].'","'.normalQuotes($Ov['Comment']).'")';
			mysqli_query($baza,$sql);
			}
		}
	$ptk=mysqli_query($baza,'select * from Options ');
	while($row=mysqli_fetch_array($ptk))
		if(!isset($Options[$row['Name']]))
			mysqli_query($baza,'delete from  Options where ID='.$row['ID']);
	}
function loadOptions(){
	global $baza; 
	$ptk=mysqli_query($baza,'select * from Options');
	$Options=array();
	if($ptk)
	while($row=mysqli_fetch_array($ptk,1))
		$Options[$row['Name']]=$row;
	$_SESSION['Options']=$Options;
	}

if(false)
if(!isset($NoInterface)){
	if(!isset($_SESSION['Options-'])) loadOptions();
	$Options=$_SESSION['Options'];
	foreach($Options as $Option){
		$Name=$Option['Name'];
		$$Name=$Option['Value'];
		}
	}
	
function spaces($cnt,$space=' '){
	$res='';
	for($i=0; $i<$cnt; $i++)
		$res.=$space;
	return $res;
	}

function domain_exists($email, $record = 'MX'){
	list($user, $domain,$rec) = explode('@', trim($email).'@@');
	if($user=='') return false;
		else
	if($domain=='') return false;
		else
	if($rec!='') return false;
		else
	return checkdnsrr($domain, $record);
	}


function file_extension($filename){
    $path_info = pathinfo($filename);
    return $path_info['extension'];
	}

function normalQuotes($t){
	return str_replace(array('"',',,',"''","´´","``","''"),array('&quot;','&quot;','&quot;','&quot;','&quot;','&quot;'),$t);
	return ($t);
	}

function filterHex($t){ return strtoupper(dechex(hexdec($t))); }

 
function getYouTubeCode($Youtube){
	if(substr($Youtube,0,7)=='<iframe'){
		$Youtube=stripslashes(str_replace('<','',$Youtube));
		$H=explode(' ',$Youtube);
		$lnk='';
		
		foreach($H as $hv){
			$hh=explode('=',$hv);
			if($hh[0]=='src') $lnk=$hh[1];
			}
		$l='';
		if($lnk!=''){
			$l=str_replace('"','',$lnk);
			$l=explode('/',$l);
			$l=array_pop($l);
			}
		}
	elseif(substr($Youtube,0,4)=='http'){
		if((strpos($Youtube,'&v=')>0)||(strpos($Youtube,'?v=')>0)){
			$y=explode('?',$Youtube);
			$y=$y[1];
			$y=explode('&',$y);
			foreach($y as $y0){
				$y0=explode('=',$y0);
				if($y0[0]=='v'){
					$l=$y0[1];
					$l=explode('&',$l);
					$l=$l[0];
					}
				}
			}else{
			$l=str_replace('"','',$Youtube);
			$l=explode('/',$l);
			$l=array_pop($l);
			}
		}else $l=$Youtube;
	return $l;
	}
function getYouTubeData($YoutubeCode){
    $url = 'https://www.googleapis.com/youtube/v3/videos?id=' . urlencode($YoutubeCode)
         . '&key=' . YOUTUBE_API_KEY
         . '&part=snippet,contentDetails,statistics,status';

    $json = file_get_contents($url);
    return json_decode($json, true);
}
function getYouTubeDuration($YoutubeCode){
	$YouTubeData=getYouTubeData($YoutubeCode);
	if(count($YouTubeData['items'])>0){
		$I0=$YouTubeData['items'][0];
		$Duration=$I0['contentDetails']['duration'];
		$date = new DateTime('1970-01-01');
		$date->add(new DateInterval($Duration));
		return $date->format('U')+date('Z');
		} 
	return 0;
	}
function getYouTubeFrame($YoutubeCode,$Quality=0){
	$YouTubeData=getYouTubeData($YoutubeCode);
	if(count($YouTubeData['items'])>0){
		$I0=$YouTubeData['items'][0];
		if((isset($I0['snippet']['thumbnails']['maxres']))&&($Quality>=0))
			return $I0['snippet']['thumbnails']['maxres']['url'];
			else
		if((isset($I0['snippet']['thumbnails']['high']))&&($Quality>=1))
			return $I0['snippet']['thumbnails']['high']['url'];
			else
		if((isset($I0['snippet']['thumbnails']['medium']))&&($Quality>=2))
			return $I0['snippet']['thumbnails']['medium']['url'];
			else
		if(isset($I0['snippet']['thumbnails']['default']))
			return $I0['snippet']['thumbnails']['default']['url'];
			else return '';
		}
		else return '';
	}

function floatMctime(){
	$t=explode(' ',microtime());
	return (float)$t[0]+(float)$t[1];
	}
function mctime(){
	$t=explode(' ',microtime());
	$t0=explode('.',$t[0]);
	return $t[1].substr($t0[1],0,6);
	}
function getIPVal($IP) {
	// IPv4
	if (filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
		$parts = explode('.', $IP);
		return (($parts[0] * 256 + $parts[1]) * 256 + $parts[2]) * 256 + $parts[3];
	}

	// IPv6 — fallback to hash-based approach
	if (filter_var($IP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
		return hexdec(substr(md5($IP), 0, 8)); // Turn IPv6 into a stable 32-bit integer
	}

	return 0; // Unknown/invalid IP
}
function reArray2(&$arrs) {
	$file_ary = array();
	if($arrs)
	if(is_array($arrs))
	foreach ($arrs as $key1=>$arr) 
		foreach ($arr as $key2=>$elem) 
			$file_ary[$key2][$key1] = $elem;
	return $arrs=$file_ary;
	}
function reArrayFiles(&$file_post) {
	$file_ary = array();
	$file_count = count($file_post['name']);
	$file_keys = array_keys($file_post);
	// if($file_count==1) $file_ary=array($file_post); else
	for ($i=0; $i<$file_count; $i++) 
		foreach ($file_keys as $key) 
			$file_ary[$i][$key] = $file_post[$key][$i];
	return $file_ary;
	}

function ImageTrueColorToPalette2($image, $dither, $ncolors) {
    $width = imagesx( $image );
    $height = imagesy( $image );
    $colors_handle = ImageCreateTrueColor( $width, $height );
    ImageCopyMerge( $colors_handle, $image, 0, 0, 0, 0, $width, $height, 100 );
    ImageTrueColorToPalette( $image, $dither, $ncolors );
    ImageColorMatch( $colors_handle, $image );
    ImageDestroy($colors_handle);
    return $image;
}

function img_split($ID, $src, $dstPrefix, $partWidth=200, $partHeight=200){
	$ext=getExt($src);
	$NoImage=false;
	if(($ext=='jpg')||($ext=='jpeg')) $src_img=imagecreatefromjpeg($src); 
		else
	if($ext=='gif') $src_img=imagecreatefromgif($src); 
		else
	if($ext=='png') { 
		$src_img=@imagecreatefrompng($src); 
		imagesavealpha($src_img, true);  
		$src_w = imagesx($src_img);
		$src_h = imagesy($src_img);
		$final_img = imagecreatetruecolor($src_w, $src_h);
		imagesavealpha($final_img, true);
		$trans_colour = imagecolorallocate($final_img, 255, 255, 255); 
		imagefill($final_img, 0, 0, $trans_colour);
		imagecopy($final_img, $src_img, 0, 0, 0, 0, $src_w, $src_h);
		$src_img=$final_img;
		}
		else
	if($ext=='bmp') $src_img=imagecreatefromwbmp($src); 
		else $NoImage=true;
	if(!$NoImage){
		$src_w = imagesx($src_img);
		$src_h = imagesy($src_img);
		$stx=$sty=0;
		$ky=0;
		while($sty<$src_h){
			$kx=$stx=0;
			while($stx<$src_w){
				$fn=md5($ID.'_'.$kx.'_'.$ky);
				if($stx+$partWidth<=$src_w) $resW=$partWidth;
					else $resW=$src_w-$stx;
				if($sty+$partHeight<=$src_h) $resH=$partHeight;
					else $resH=$src_h-$sty;
				$dst_img=imagecreatetruecolor($resW, $resH); 
				
				imagecopyresampled($dst_img,$src_img,0,0,$stx,$sty,$resW,$resH,$resW,$resH);
				$dst_img=ImageTrueColorToPalette2($dst_img, false, 128);
/*
				$background = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
				imagecolortransparent($dst_img, $background);
				imagealphablending($dst_img, false);
				imagesavealpha($dst_img, true);
*/
				if($ext=='jpg') imagejpeg($dst_img, $dstPrefix.'_'.$fn.'.jpg',90);
					elseif($ext=='png') imagepng($dst_img, $dstPrefix.'_'.$fn.'.png',9);
					elseif($ext=='gif') imagegif($dst_img, $dstPrefix.'_'.$fn.'.gif');
				imagedestroy($dst_img);
				$kx++;
				$stx=$kx*$partWidth;
				}
			$ky++;
			$sty=$ky*$partHeight;
			}
		}
	}

function img_resize($src, $dst, &$ext, $img_w=788, $img_h=576,  $txt='', $resExt='jpg', $AddLogo=false, $AddWatermark=false, $Fit=false, $Resize=false, $Filling=false){ // fill color white 
	list($width, $height, $imagetype) = getimagesize($src);
	if($img_w==0) $img_w=$width;
	if($img_h==0) $img_h=$height;
	/*
	$imagetype=exif_imagetype($src);
	*/
	if($imagetype==1) $ext='gif';
	if($imagetype==2) $ext='jpg';
	if($imagetype==3) $ext='png';
	if($imagetype==6) $ext='bmp';
	/* */
	$ext=strtolower($ext);
	$NoImage=false;
	if(($ext=='jpg')||($ext=='jpeg')) $src_img=imagecreatefromjpeg($src); 
		else
	if($ext=='gif') $src_img=imagecreatefromgif($src); 
		else
	if($ext=='png') { 
		$src_img=@imagecreatefrompng($src); 
		imagesavealpha($src_img, true);  
		}
		else
	if($ext=='bmp') $src_img=imagecreatefromwbmp($src); 
		else $NoImage=true;
	if(!$NoImage){
		$dx=0; $dy=0;
		$src_w = imagesx($src_img);
		$src_h = imagesy($src_img);
		$x=0; $y=0; $h=$src_h; $w=$src_w; 
		if($Filling){
			$img_w0=$img_w; $img_h0=$img_h;
			if ($src_w/$src_h<$img_w/$img_h)  $img_w0=round($img_h*($src_w/$src_h)); else $img_h0=round($img_w*($src_h/$src_w));  // Fit dimentions
			$dx=round(($img_w-$img_w0)/2);
			$dy=round(($img_h-$img_h0)/2);
			}else
		if(!$Resize)
		if($Fit){
			if(($src_w>$img_w)||($src_h>$img_h)) // if Big then fit
				if ($src_w/$src_h<$img_w/$img_h)  $img_w=round($img_h*($src_w/$src_h)); else $img_h=round($img_w*($src_h/$src_w));  // Fit dimentions
				else {
					$img_w=$src_w;
					$img_h=$src_h;
					}
			}
			else
			if ($src_w/$src_h<$img_w/$img_h)  { $h=round($src_w/($img_w/$img_h)); $y=round(($src_h-$h)/2); }	  // Crop dimentions
				else { $w=round($src_h/($img_h/$img_w)); $x=round(($src_w-$w)/2); } 
				
		$dst_img=imagecreatetruecolor($img_w, $img_h); 
		// Make a new transparent image and turn off alpha blending to keep the alpha channel
		$background = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
		imagecolortransparent($dst_img, $background);
		imagealphablending($dst_img, false);
		imagesavealpha($dst_img, true);

		if($Filling){$cl = imagecolorallocate($dst_img, 255, 255, 255); imagefilledrectangle($dst_img,0,0,$img_w,$img_h,$cl); $img_w=$img_w0; $img_h=$img_h0; }
		imagecopyresampled($dst_img,$src_img,(int)$dx,(int)$dy,(int)$x,(int)$y,(int)$img_w,(int)$img_h,(int)$w,(int)$h); 
		if($txt!=''){	 
			$cl = imagecolorallocate($dst_img, 255, 255, 255);
			imagestring($dst_img, 3, 5, $img_h-15, $txt, $cl); 
			}
		if($AddWatermark){
			$wmSrc_img=@imagecreatefrompng($AddWatermark); 
			$wmsrc_w = imagesx($wmSrc_img);
			$wmsrc_h = imagesy($wmSrc_img);

			$w = imagesx($dst_img);
			$h = imagesy($dst_img);

			$wmdst_h=$w*$wmsrc_h/$wmsrc_w;
			$wmdst_w=$w;
			
			$dest_x = ($w-$wmdst_w)/2;  
			$dest_y = ($h-$wmdst_h)/2; 
			
			imagealphablending($dst_img, true);
			imagesavealpha($dst_img, true);
			imagealphablending($wmSrc_img, true);  
			imagecopyresampled($dst_img,$wmSrc_img,$dest_x,$dest_y,0,0,$wmdst_w,$wmdst_h,$wmsrc_w,$wmsrc_h); 
			}
			
		if($AddLogo){
			$wmSrc_img=@imagecreatefrompng('img/multimedia/wmLogo.png'); 
			$wmsrc_w = imagesx($wmSrc_img);
			$wmsrc_h = imagesy($wmSrc_img);
			$dest_x = $img_w - $wmsrc_w-10 ;  
			$dest_y = 10; // $img_h - $wmsrc_h - 1; 
			imagealphablending($dst_img, true);
			imagealphablending($wmSrc_img, true); 
			imagecopy($dst_img, $wmSrc_img, $dest_x, $dest_y, 0, 0, $wmsrc_w, $wmsrc_h);
			}
			
		if($resExt==''){
			if(($ext=='jpg')||($ext=='jpeg')||($ext=='bmp')) { $ext='jpg'; imagejpeg($dst_img, $dst.'.'.$ext,90); return 'jpg'; }
				else
			if($ext=='gif') { imagegif($dst_img, $dst.'.'.$ext); return 'gif'; }
				else
			if($ext=='png') { imagepng($dst_img, $dst.'.'.$ext); return 'png'; }
			}else{
			$ext=$resExt;
			if(($ext=='jpg')||($ext=='jpeg')||($ext=='bmp')) { $ext='jpg'; imagejpeg($dst_img, $dst.'.'.$ext,90); return 'jpg'; }
				else
			if($ext=='gif') { imagegif($dst_img, $dst.'.'.$ext); }
				else
			if($ext=='png') { imagepng($dst_img, $dst.'.'.$ext); }
			}
		}
	};

function isImage($Ext){
	$Images=array('jpg','gif','png','jpeg','bmp');
	return (in_array($Ext,$Images));
	}

function info($s,$Margin_top=50,$Margin_bottom=0){ 
	echo '<div align="center" style="margin:'.$Margin_top.'px 0 '.$Margin_bottom.'px">
	<table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #aabbff; height:60px; margin:10px;" align="center" class="normal"><tr>
	<td align="center" valign="middle"><div style="width:70px; height:70px; background:url(/img/Information.png) center right no-repeat;"></div></td>
	<td valign="middle" align="center" style="padding:20px;     vertical-align: middle;">'.$s.'</td></tr></table></div>';
	}
function error($t,$Margin_top=50,$Margin_bottom=0){ 
	global $ErrorCode;
	$ErrorCode=1;
	echo '<div align="center" style="margin:'.$Margin_top.'px 0 '.$Margin_bottom.'px ">
	<table border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" style="border:1px solid #aabbff; height:60px; margin:10px;" align="center" class="normal"><tr>
	<td align="center" valign="middle"><div style="width:70px; height:70px; background:url(/img/error.png) center right no-repeat;"></div></td>
	<td valign="middle" align="center" style="padding:20px;">'.$t.'</td></tr></table></div>';
	}

function getFindedSub($src,$text,$isHeader=false){
	$text=strip_tags($text);
	$text=str_replace('<',' <',$text);
	$text=strip_tags($text);
	$text=str_replace('&nbsp;',' ',$text);
	$wordsBefore=5;
	$wordsAfter=10;
	if (strlen($src)>0){
	//	$sp=strpos(mb_strtoupper ($text, "utf-8"),mb_strtoupper ($src, "utf-8"));
		$sp=stripos(chr(5).$text,$src);
		if ($sp>0)
			{ $sp--;
			if ($isHeader) {
				$sp0=0; $sp1=strlen($text);
				} else {
				$sp0=$sp; $spi=0;
				while ($spi<$wordsBefore)
					{
					$sp0--;
					if (substr($text,$sp0,1)==' ') $spi++;
					if ($sp0<0) {$sp0=0; $spi=$wordsBefore;}
					}
				$sp1=$sp; $spi=0;
				while ($spi<$wordsAfter)
					{
					$sp1++;
					if (substr($text,$sp1,1)==' ') {$spi++;}
					if ($sp1>strlen($text)) {$sp1=strlen($text); $spi=$wordsAfter;} 
					}
				}
			$result=substr($text,$sp0,$sp-$sp0).'<span class="srcText2">'.substr($text,$sp,strlen($src)).'</span>'.substr($text,$sp+strlen($src),$sp1-$sp-strlen($src));
			if (!$isHeader)$result.=' ...';
			if ($sp0>0) $result='... '.$result;
			return $result;
			} else 
			if ($isHeader) return $text; else return '';
		} 
		else if ($isHeader) return $text; else return smallText($text, 300);
	}
function smallText ($txt, $count=300){ 
	$txt=strip_tags($txt); 
	$txt.=' ';
	$cc=mb_strlen($txt,'UTF-8');
	if ($cc>$count) 
		$txt=mb_substr($txt,0,mb_strpos($txt,' ',$count,'UTF-8'),'UTF-8').'  ...'; 
	return $txt;
	}
function changeUrl($var,$val,$varDel=array(),$url=''){ 
	if(!is_array($var)) $var=explode(',',$var);
	if(!is_array($val)) $val=explode(',',$val);
	if(!is_array($varDel)) $varDel=explode(',',$varDel);
	if($url=='') $url=$_SERVER['REQUEST_URI'];
	$u=parse_url ($url);
	if(isset($u['query']))
		parse_str($u['query'],$uArray);
		else $uArray=array();
	foreach($var as $vn =>$vv)
		if ($vv!='')
			$uArray[$vv]=$val[$vn];
	foreach($varDel as $vn)
		unset($uArray[$vn]);
	$url='?'.http_build_query($uArray);
	/*
	foreach($uArray as $un => $ul)
		if($url!='?') 
			if(is_array($ul))
				$url.='&'.$un.'='.serialize($ul);
				else
				$url.='&'.$un.'='.$ul;
			else $url.=$un.'='.$ul;
	*/
	return $url;
	}
function Paging2($Count, $st, $RowsPerPage, $Url) { 
	// echo $Count.'='. $st.'='. $RowsPerPage.'='. $Url;
	$PagesCount=floor($Count/$RowsPerPage);
	if($PagesCount*$RowsPerPage<$Count) $PagesCount++;

	$CurPage=ceil($st/$RowsPerPage);
//	if($CurPage*$RowsPerPage<$st) $CurPage++;

	$p1=max(1,$CurPage-4);
	$p2=min($PagesCount,$CurPage+6);
	$res='<div class="pager">';
	if($p1>1) $res.= '<a href="'.$Url.'/0">&lt;&lt; პირველი</a>';
	if($CurPage>1) $res.= '<a href="'.$Url.'/'.(($CurPage-1)*$RowsPerPage).'">&lt;&lt; წინა</a>';
	for($i=$p1; $i<=$p2; $i++)
		if(($CurPage+1)==$i)  $res.= '<strong>'.$i.'</strong>';
			else $res.= '<a href="'.$Url.'/'.(($i-1)*$RowsPerPage).'">'.$i.'</a>';
	if($CurPage<$PagesCount-1) $res.= '<a href="'.$Url.'/'.(($CurPage+1)*$RowsPerPage).'">შემდეგი &gt;&gt;</a>';
	$res.= '</div>';		
	return $res;
	} 
	
function Paging($Count, $st, $RowsPerPage, $url='') { 
	if($url=='')  $url=$_SERVER['REQUEST_URI'];
	$PagesCount=floor($Count/$RowsPerPage);
	if($PagesCount*$RowsPerPage<$Count) $PagesCount++;
	
	$CurPage=floor($st/$RowsPerPage);
	if($CurPage*$RowsPerPage<$st) $CurPage++;
	
	$ppCount=10;
	$p1=floor($CurPage/$ppCount)*$ppCount+1;
	$p2=$p1+$ppCount-1;
	if($p2>$PagesCount) $p2=$PagesCount;
	echo '<div ><ul class="pagination">';
			
	if($p1>1) echo '<li><a class="prev-next prev" href="'.changeUrl('st',($p1-2)*$RowsPerPage, '',$url).'"><i class="ion-ios-arrow-left"></i> '.('Prev').'</a>';
	for($i=$p1; $i<=$p2; $i++)
		echo '<li ><a class="'.(($CurPage+1)==$i?'active':'').'" href="'.changeUrl('st',($i-1)*$RowsPerPage, '',$url).'">'.$i.'</a></li>';
			
	if($p2<$PagesCount) echo '
		<li><a class="prev-next next" href="'.changeUrl('st',($p2)*$RowsPerPage, '', $url).'">'.('Next').'<i class="ion-ios-arrow-right"></i> </a></li>';
	echo '</ul></div>';	
	}

function twoDigit($i){
	$i=(int)$i;
	if($i<10) return '0'.$i; else return $i;
	}
function twoDecimals($i){
	$i=(float)$i;
	return number_format($i, 2);
	}
	
function getExt($s){
	while (strpos('~'.$s,'.')) $s=substr($s,strpos($s,'.')+1);
	return strtolower($s);
	}

function filterHTML($Text,$tags,$attrib){
	global $baza;
	if(!is_array($tags)) $tags=explode(' ',$tags);
	if(!is_array($attrib)) $attrib=explode(' ',$attrib);
	$Text=str_replace('&lt;','<',$Text);
	$Text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $Text); 
	$tag='';
	foreach($tags as $t)
		$tag.='<'.$t.'>';
	$Text = strip_tags($Text,$tag); // '<b><strong><br><em><i><p><a>'
	$sa = new StripAttributes();
	$sa->allow = $attrib; // array( 'align','href','target','src');
	// $Text = mysqli_real_escape_string($baza, $sa->strip( stripslashes($Text)));
	return ($Text);
	}

function smartFilter($Text,$Escape=true){
	$Text=str_replace('\\r\\n',' ',$Text);
	$Text=str_replace('style="text-align: left;"','align="left"',stripslashes($Text));
	$Text=str_replace('style="text-align: center;"','align="center"',stripslashes($Text));
	$Text=str_replace('style="text-align: right;"','align="right"',stripslashes($Text));
	$Text=str_replace('style="text-align: justify;"','align="justify"',stripslashes($Text));
	$Text=str_replace('align="left" align=','align=',stripslashes($Text));
	$Text=str_replace('align="center" align=','align=',stripslashes($Text));
	$Text=str_replace('align="right" align=','align=',stripslashes($Text));
	$Text=str_replace('align="justify" align=','align=',stripslashes($Text));

	// $Text=str_replace('<div','<p',$Text);	$Text=str_replace('</div','</p',$Text);
	return (filterHTML($Text,'b strong br em i div p a ul li ol u blockquote sub sup h2 h3 h4 h5 section img','href target title align src tom',$Escape));
	}
	
function onlyDigits($p){
	return preg_replace("/[^0-9]/", "",$p);
	}
 
/**
* Strip attribute Class
* Remove attributes from XML elements
* @author David (semlabs.co.uk)
* @version 0.2.1
*/
function reg_escape( $str ){
	$conversions = array( "^" => "\^", "[" => "\[", "." => "\.", "$" => "\$", "{" => "\{", "*" => "\*", "(" => "\(", "\\" => "\\\\", "/" => "\/", "+" => "\+", ")" => "\)", "|" => "\|", "?" => "\?", "<" => "\<", ">" => "\>" );
	return strtr( $str, $conversions );
	}
class StripAttributes { 
	public $str			= '';
	public $allow		= array();
	public $exceptions	= array();
	public $ignore		= array();
	
	public function strip( $str )	{
		$this->str = $str;		
		if( is_string( $str ) && strlen( $str ) > 0 )		{
			$res = $this->findElements();
			if( is_string( $res ) )
				return $res;
			$nodes = $this->findAttributes( $res );
			$this->removeAttributes( $nodes );
			}
		return $this->str;
		}
	
	private function findElements()	{
		# Create an array of elements with attributes
		$nodes = array();
		preg_match_all( "/<([^ !\/\>\n]+)([^>]*)>/i", $this->str, $elements );
		foreach( $elements[1] as $el_key => $element )		{
			if( $elements[2][$el_key] )			{
				$literal = $elements[0][$el_key];
				$element_name = $elements[1][$el_key];
				$attributes = $elements[2][$el_key];
				if( is_array( $this->ignore ) && !in_array( $element_name, $this->ignore ) )
					$nodes[] = array( 'literal' => $literal, 'name' => $element_name, 'attributes' => $attributes );
				}
			}
		
		# Return the XML if there were no attributes to remove
		if(!isset($nodes[0])) return $this->str;
			if( !$nodes[0]) return $this->str;
				else return $nodes;
		}
	
	private function findAttributes( $nodes ) {
		# Extract attributes
		foreach( $nodes as &$node ){
			preg_match_all( "/([^ =]+)\s*=\s*[\"|']{0,1}([^\"']*)[\"|']{0,1}/i", $node['attributes'], $attributes );
			if( $attributes[1] ){
				$atts=array();
				foreach( $attributes[1] as $att_key => $att ) {
					$literal = $attributes[0][$att_key];
					$attribute_name = $attributes[1][$att_key];
					$value = $attributes[2][$att_key];
					$atts[] = array( 'literal' => $literal, 'name' => $attribute_name, 'value' => $value );
					}
				$node['attributes'] = $atts;
				unset( $atts );
				}
			else
				$node['attributes'] = null;
			}
		return $nodes;
		}
	
	private function removeAttributes( $nodes ){
		# AllCaps Attributes
		foreach( $this->allow as $k => $s )
			$this->allow[$k]=strtoupper($s);
		# Remove unwanted attributes
		foreach( $nodes as $node )		{
			# Check if node has any attributes to be kept
			$node_name = $node['name'];
			$new_attributes = '';
			if( is_array( $node['attributes'] ) )			{
				foreach( $node['attributes'] as $attribute )				{
					if( ( is_array( $this->allow ) && in_array( strtoupper($attribute['name']), $this->allow ) ) || $this->isException( $node_name, $attribute['name'], $this->exceptions ) )
						$new_attributes = $this->createAttributes( $new_attributes, $attribute['name'], $attribute['value'] );
					}
				}
			$replacement = ( $new_attributes ) ? "<$node_name $new_attributes>" : "<$node_name>";
			$this->str = preg_replace( '/'. reg_escape( $node['literal'] ) .'/', $replacement, $this->str );
			}
		}
	
	private function isException( $element_name, $attribute_name, $exceptions )	{
		if( array_key_exists($element_name, $this->exceptions) )		{
			if( in_array( $attribute_name, $this->exceptions[$element_name] ) )
				return true;
			}		 
		return false;
		}
	
	private function createAttributes( $new_attributes, $name, $value )	{
		if( $new_attributes )
			$new_attributes .= " ";
		$new_attributes .= "$name=\"$value\"";
		
		return $new_attributes;
		}
	}

/*
$str=implode(file('test.xml'));
$sa = new StripAttributes();
$sa->allow = array( 'align','href','target','src');
$str = $sa->strip( $str );
*/ 

function xml2array($data) {
	return json_decode(json_encode($data),true);
	}

	
?>