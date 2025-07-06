<? session_start();
$res=0;




if(isset($_POST['f'])){
	include 'conf.php';
	$Refresh=''; 
	$NoInterface=true;
	include 'functions.php';
	$f=$_POST['f'];
    
    if($f=='genDonateOrder'){
        $Currency=$_POST['Currency'];
        $Amount=$_POST['Amount'];
        $UserData=addslashes(json_encode($post['UserData'],256));
        $ptk=mysqli_query($baza,'insert into Donation (UserData, Currency, Amount) values ("'.$UserData.'", "'.$Currency.'", "'.$Amount.'")');
        if($ptk){
            $ID=mysqli_insert_id($baza);
            $res=array('ID'=>$ID);
            }
        }else $res=$f;
	} else print_r($_POST);
if(is_array($res)) echo json_encode($res);
	else echo $res;
?>