

<div class="container Contacts">
    <div class="row">
		
<?
$res=-1;

if(isset($_POST['name'])){
	$name=$_POST['name'];
	$email=$_POST['email'];
	$mobile=$_POST['mobile'];
	$Profession=$_POST['Profession'];
	$message=$post['message'];
    $message=str_replace(chr(13),chr(13).'<br>',$message);
	$html="Name: $name <br>
		Profession: $Profession <br>
		Mobile: $mobile <br>
		Email: $email <br><br>
		".$message;
	
	include 'sendmail.php';
	
	$res=sendMail('contact@nationaltrustofgeorgia.org.ge','National Trust of Georgia', 'contact@nationaltrustofgeorgia.org.ge', 'New Volunteer', $email, $name, '', 'New Volunteer', $html, strip_tags($html));
	
	}


?>



<div class="container">
  <div class="row">
	<div class="fixHeader">
		<h3 align="center"><?=_Interface('თუ გსერთ გახდეთ მოხალისე, შეავსეთ და გამოგვიგზავნეთ ფორმა')?></h3>
	</div><a name="res"></a>
	  
	<?
	if($res==1) info(_Interface('შეტყობინება წარმატებით გაიგზავნა'),100,100);
		else
	if($res==0) error(_Interface('შეტყობინება არ გაიგზავნა'),100,100);		
		else{
 		
	?>  
	  
    <div class="col-md-6 mt30 mb30">
      <form method="post" enctype="multipart/form-data" action="#res">
        <div class="form-group">
          <label><?=_Interface('სრული სახელი')?></label>
          <input type="text" class="form-control" name="name" id="name" required >
        </div>
        <div class="form-group">
          <label><?=_Interface('ელ-ფოსტა')?></label>
          <input type="email" class="form-control" name="email" id="email" required>
        </div>
        <div class="form-group">
          <label><?=_Interface('ტელეფონი')?></label>
          <input type="text" class="form-control" name="mobile" id="mobile" required>
        </div>
          
         <div class="form-group">
          <label><?=_Interface('პროფესია')?></label>
          <input type="text" class="form-control" name="Profession" id="Profession" required>
        </div>
          
        <div class="form-group">
          <label><?=_Interface('მოხალისეობის მიზნის მოკლე აღწერა')?></label>
          <textarea class="form-control" rows="5" name="message" id="message" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><?=_Interface('შეტყობინების გაგზავნა')?></button>
        <span class="help-block loading"></span>
      </form>
    </div>
    <!--// col md 9-->
    
   
	  <? }
			?>
  </div>
</div> 


		
		</div>
	</div>