

<div class="container Contacts">
    <div class="row">
		
<?
$res=-1;

if(isset($_POST['name'])){
	$name=$_POST['name'];
	$email=$_POST['email'];
	$mobile=$_POST['mobile'];
	$message=$_POST['message'];
	$html="Name: $name <br>
		mobile: $mobile <br>
		Email: $email <br><br>
		".$message;
	
	include 'sendmail.php';
	
	$res=sendMail('info@nationaltrustofgeorgia.org.ge','National Trust of Georgia', 'info@nationaltrustofgeorgia.org.ge', 'Web Contact', $email, $name, '', 'Web Contact', $html, strip_tags($html));
	
	}


?>



<!--<div class="container">
  <div class="row">
	<div class="fixHeader">
		<h3 align="center"><?=_Interface('კონტაქტი')?></h3>
	</div>-->
	  
	<?
	if($res==1) info(_Interface('შეტყობინება წარმატებით გაიგზავნა'),100,100);
		else
	if($res==0) error(_Interface('შეტყობინება არ გაიგზავნა'),100,100);		
		else{
			
if(false)
if($Lang=='ge')		{?> გთხოვთ შეგვატყობინოთ ელექტრონული ფოსტით, თუ გსურთ, გახდეთ საქართველოს ეროვნული ფონდის ანგელოზი წევრი. <?} 
		else  {?> Please let us know by email if you would like to join the Angel Membership scheme. Depending on your location, we will indicate where donations should be sent. <?} 		
	?>  
	  
    <div class="col-md-6 mt30 mb30">
      <form method="post" enctype="multipart/form-data">
        <div class="form-group">
          <label><?=_Interface('სრული სახელი')?></label>
          <input type="text" class="form-control" name="name" id="name" required autofocus>
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
          <label><?=_Interface('შეტყობინება')?></label>
          <textarea class="form-control" rows="5" name="message" id="message" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><?=_Interface('შეტყობინების გაგზავნა')?></button>
        <span class="help-block loading"></span>
      </form>
    </div>
    <!--// col md 9-->
    
    <div class="col-md-6 mb30">
      
      <div class="row">
        <div class="col-lg-2"> </div>
        <div class="col-lg-10"><br><br><br>

			<h3><?=_Interface('ჩვენი მისამართი')?></h3>
			<?=_Interface('საქართველოს ეროვნული ფონდი')?><br> 
      <?=_Interface('ს.კ.:404522389')?><br>
          <?=_Interface('მეფე სოლომონ ბრძენის ქ.33, 0103 თბილისი, საქართველო')?><br>
        	<?=_Interface('ტელ')?>: (+995) 555 490 917 <br>
          <?=_Interface('ელ-ფოსტა')?>: <a href="mailto:office@nationaltrustgeo.org">office@nationaltrustgeo.org</a></div>
      </div>
      
		
    </div>
	  <? }
			?>
  </div>
</div> 


<!-- LOCATION MAP -->
<div class="location-map">
    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d225.24957356976873!2d44.81528595134868!3d41.690203202994695!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40440d0b1f60b44b%3A0x39e4686bbe9fd156!2sThe%20National%20Trust%20of%20Georgia!5e1!3m2!1sen!2sge!4v1740120800308!5m2!1sen!2sge" height="260" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>
<!-- // END LOCATION MAP  --> 

		
		<!--</div>
	</div>-->