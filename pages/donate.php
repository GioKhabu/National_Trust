<?
$merchant_id='1549901';
?>
<div class="container Donation">
    <div class="row">
       <!-- <div class="fixHeader">
            <h3 align="center">
                <?=_Interface('შემოწირულობა')?>
            </h3>
        </div>-->
        
        
<? if($page=='result') {
    if(isset($_POST['merchant_id']) && $_POST['merchant_id']==$merchant_id){
        $amount=$_POST['amount']/100;
        $currency=$_POST['currency'];
        $order_status=$_POST['order_status'];
        $response_status=$_POST['response_status'];
        $order_id=explode('_',$_POST['order_id']);
        $order_id=(int)$order_id[1];
        if($order_id>0)
            mysqli_query($baza,'update Donation set Status="'.$response_status.'", Response="'.addslashes(json_encode($_POST,256)).'" where ID='.$order_id);
        if($order_status=='approved' && $response_status=='success')
            info(_Interface('თქვენს მიერ გაცემული თანხა:').' '.$amount.' '.$currency.' '._Interface('წარმატებით ჩაირიცხა ფონდის ანგარიშზე').'<br><br>'.
                _Interface('მადლობა შემოწირულობისთვის'),0,300);
            else
            error(_Interface('შეცდომა თანხის ჩარიხვისას'),0,300);
        } else error(_Interface('404. არასწორი მომართვა გვერდზე'),0,300);
    }
        else { ?>
        
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#Donation"><?=_Interface('Choose Your Donation')?></a></li>
            <li><a data-toggle="tab" href="#Details"><?=_Interface('Your Details')?></a></li>
            <li><a href="#Payment"><?=_Interface('Payment')?></a></li>
        </ul>
        
        <div class="tab-content">
            
            <div id="Donation" class="tab-pane fade in active">
                <h3><?=_Interface('Choose Your Donation')?></h3>
                <div class="row">
                    <div class="col-md-6">              
              <div><?=_Interface('Select your currency')?></div>
                <label ><input type="radio" name="currency" value="USD" checked> USD </label>
                <label ><input type="radio" name="currency" value="EUR"> EUR </label>
                <label ><input type="radio" name="currency" value="GEL"> GEL </label>
                
                <br><br>
                        </div>
                    <div class="col-md-6">
            <div><?=_Interface('Donation Amount')?></div>
                <label ><input type="radio" name="amount" value="20" checked> 20 </label>
                <label ><input type="radio" name="amount" value="50"> 50 </label>
                <label ><input type="radio" name="amount" value="100"> 100 </label>
                <label ><input type="radio" name="amount" value="250"> 250 </label>
                <label ><input type="radio" name="amount" value="500"> 500 </label>
                <label ><input type="radio" name="amount" value="0" > <?=_Interface('Other Amount')?> </label>
                <input type="number" name="amount" id="amount" min=100 value="1000" step="100" style="display: none">
                        </div>
                </div>
                <hr>
                <a class="btn btn-primary Details"><?=_Interface('Next')?></a>
            </div>
            
            <div id="Details" class="tab-pane fade">
              <h3><?=_Interface('Your Details')?></h3>
                  <p><?=_Interface('სახელი')?> <input type="text" name="Name" id="Name"> </p>
                  <p><?=_Interface('ელ-ფოსტა')?> <input type="email" name="Email" id="Email"> </p>
                  <p><?=_Interface('მისამართი')?> <input type="text" name="Address" id="Address"> </p>
                  <p><?=_Interface('ტელეფონი')?> <input type="text" name="Phone" id="Phone"> </p>
                <hr>
                <a class="btn btn-primary Payment"><?=_Interface('Next')?></a>
            </div>
            
            <div id="Payment" class="tab-pane fade">
               <div id="checkout-container"></div>
        <script src="https://pay.flitt.com/latest/checkout-vue/checkout.js"></script>
        <script id="rendered-js">
        var Options = {
          options: {
            methods: ["card"],
            methods_disabled: [],
            card_icons: ["mastercard", "visa", "maestro"],
            fields: false,
            full_screen: false,
            button: true,
            hide_title: true,
            hide_link: true,
            email: false,
            theme: {
              type: "light",
              preset: "reset" },

            loading: 
             "data:image/svg+xml;base64,PHN2ZyBpZD0iX9Ch0LvQvtC5XzIiIGRhdGEtbmFtZT0i0KHQu9C+0LkgMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgNDk1LjUxIDE5Ny40MSI+CiAgPGcgaWQ9IkxheWVyXzEiIGRhdGEtbmFtZT0iTGF5ZXIgMSI+CiAgICA8cGF0aCBmaWxsPSJjdXJyZW50Q29sb3IiIGQ9Im00ODMuNSwxMjEuMzljMS43NC0yLjU2LDIuOTQtNS41NCwzLjQtOC43OGw0Ljc5LTMzLjI1Yy4xOS0xLjEzLjMtMi4yOS4zLTMuNDgsMC0xMS41Ni05LjM2LTIwLjkyLTIwLjkyLTIwLjkyaC0xOC4zNmw0LjI0LTI5LjQ2Yy44Ny02LjAxLS45Mi0xMi4wOS00Ljg4LTE2LjY3LTMuOTgtNC41OS05Ljc1LTcuMjMtMTUuODEtNy4yM2gtMzYuNTNjLTEwLjQsMC0xOS4yMyw3LjY0LTIwLjcxLDE3Ljk0bC01LjI1LDM2LjQ5Yy0yLjA4LS42OS00LjMtMS4wNi02LjYyLTEuMDZoLTE4LjM2bDQuMjUtMjkuNDZjLjg2LTYuMDEtLjkyLTEyLjA5LTQuOS0xNi42Ny0zLjk4LTQuNTktOS43NS03LjIzLTE1LjgxLTcuMjNoLTM2LjUzYy03LjU0LDAtMTQuMjUsNC4wMS0xNy45MywxMC4xNC03LjMxLTcuMjUtMTcuMzUtMTEuNzQtMjguNDQtMTEuNzQtOS42LDAtMTguNDIsMy4zNi0yNS4zNiw4Ljk3LTMuODMtNC41MS05LjU1LTcuMzctMTUuOTMtNy4zN2gtMTE2LjA4Yy0yMy40NCwwLTM5LjMxLDQuNi01My4wMiwxNS4zOC0xNi40NSwxMi45Mi0yMC45NSwzMi40Ny0yMi45MSw0Ni4wNEwuMjEsMTczLjUxYy0uODYsNi4wMS45MiwxMi4wOSw0LjksMTYuNjcsMy45OCw0LjYsOS43NSw3LjIzLDE1LjgxLDcuMjNoMzYuNTNjMTAuNCwwLDE5LjIzLTcuNjQsMjAuNzEtMTcuOTRsNS4zLTM2LjgxaDQ5LjY3bC00LjQ0LDMwLjg1Yy0uODYsNi4wMS45MiwxMi4wOSw0LjksMTYuNjcsMy45OCw0LjYsOS43NSw3LjIzLDE1LjgxLDcuMjNoOTguNjVjMTAuNCwwLDE5LjIzLTcuNjQsMjAuNzEtMTcuOTRsMS42Mi0xMS4yNWM2LjMxLDkuNzUsMTUuMiwxNy42NSwyNS45NCwyMi41NywxMi4wOSw1LjUzLDI0LjYzLDYuNjIsNDIuMjEsNi42MmgyNi45MWM5LjcyLDAsMTguMDctNi42OCwyMC4zMy0xNS45Nyw0LjMxLDMuNzUsOS4xNyw2LjkyLDE0LjQ5LDkuMzUsMTIuMDksNS41MywyNC42Myw2LjYyLDQyLjIxLDYuNjJoMjYuOWMxMC40LDAsMTkuMjMtNy42NCwyMC43MS0xNy45NGw1LjE0LTM1LjY2Yy4xOS0xLjEzLjMtMi4yOS4zLTMuNDksMC04LjM2LTQuOTEtMTUuNTktMTIuMDItMTguOTNaTTE0Mi4wMiw1OC42OGgtNTIuMTFjLTUuMDUsMC05LjE3LjM3LTEyLjI3LDIuNzItMi41NywxLjk2LTQuMzUsNS4xNC01LjEyLDEwLjU5bC0yLjMyLDE2aDY3LjZsLTQuODcsMzMuNzRoLTY3LjU5bC03Ljg4LDU0Ljc1SDIwLjkybDE1LjkyLTExMC40OGMyLjEtMTQuNTYsNi4zNy0yNS42OSwxNS4xMi0zMi41OCw5LjIxLTcuMjMsMjAuMDMtMTAuOSw0MC4wOS0xMC45aDU1LjE3bC01LjIxLDM2LjE1Wm00My45MywxMTcuODFoLTM2LjUzbDIyLjE5LTE1My45NmgzNi41MmwtMjIuMTgsMTUzLjk2Wm02Mi4xMiwwaC0zNi41MmwxNC40OS0xMDAuNmgzNi41M2wtMTQuNSwxMDAuNlptMS4zNi0xMTYuNjVjLTEwLjc2LDAtMTkuNDYtOC43MS0xOS40Ni0xOS40NnM4LjY5LTE5LjQ3LDE5LjQ2LTE5LjQ3LDE5LjQ0LDguNzIsMTkuNDQsMTkuNDctOC43MSwxOS40Ni0xOS40NCwxOS40NlptMTE2LjAzLDExNi42NWgtMjYuOTFjLTE3LjQsMC0yNS44NS0xLjIyLTMzLjQ5LTQuNzItMTQuMi02LjUxLTIyLjkxLTIwLjY1LTIzLjk4LTM1Ljc4LS4zMS00LjM2LjE0LTEyLjM0LDEuNTUtMjIuMDdsMTMuMTYtOTEuMzloMzYuNTNsLTcuNjksNTMuMzZoNDIuNTFsLTQuODYsMzMuNzRoLTQyLjUxbC0yLjA4LDE0LjQ2Yy0uNjQsNC40OC0uMjUsOC4zNiwxLjgsMTEuMjcsMi41OCwzLjYzLDYuODYsNC45NiwxNS4xNiw0Ljk2aDM2LjAxbC01LjIxLDM2LjE2Wm0xMDMuOTIsMGgtMjYuOWMtMTcuNDEsMC0yNS44NS0xLjIyLTMzLjQ5LTQuNzItMTQuMi02LjUxLTIyLjkxLTIwLjY1LTIzLjk4LTM1Ljc4LS4zMS00LjM2LjE0LTEyLjM0LDEuNTQtMjIuMDdsMTMuMTctOTEuMzloMzYuNTNsLTcuNjksNTMuMzZoNDIuNTFsLTQuODYsMzMuNzRoLTQyLjUxbC0yLjA5LDE0LjQ2Yy0uNjQsNC40OC0uMjUsOC4zNiwxLjgyLDExLjI3LDIuNTgsMy42Myw2Ljg2LDQuOTYsMTUuMTQsNC45NmgzNi4wMmwtNS4yMiwzNi4xNloiLz4KICA8L2c+Cjwvc3ZnPg==",
            logo_url:
            "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyBpZD0iX9Ch0LvQvtC5XzIiIGRhdGEtbmFtZT0i0KHQu9C+0LkgMiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2aWV3Qm94PSIwIDAgNDE0Ljg4IDE2NS4zIj4KICA8ZGVmcz4KICAgIDxzdHlsZT4KICAgICAgLmNscy0xIHsKICAgICAgICBmaWxsOiAjZmZmOwogICAgICB9CgogICAgICAuY2xzLTIgewogICAgICAgIGZpbGw6ICM3MDg3ZmM7CiAgICAgIH0KICAgIDwvc3R5bGU+CiAgPC9kZWZzPgogIDxnIGlkPSJMYXllcl8xIiBkYXRhLW5hbWU9IkxheWVyIDEiPgogICAgPGc+CiAgICAgIDxwYXRoIGNsYXNzPSJjbHMtMiIgZD0ibTQwNC44MywxMDEuNjRjMS40Ni0yLjE0LDIuNDYtNC42NCwyLjg1LTcuMzVsNC4wMS0yNy44M2MuMTYtLjk1LjI0LTEuOTIuMjQtMi45MiwwLTkuNjctNy44NC0xNy41Mi0xNy41Mi0xNy41MmgtMTUuMzdsMy41NS0yNC42NmMuNzItNS4wMy0uNzctMTAuMTMtNC4xLTEzLjk3LTMuMzMtMy44NC04LjE2LTYuMDUtMTMuMjQtNi4wNWgtMzAuNTljLTguNzEsMC0xNi4xLDYuNC0xNy4zNCwxNS4wMmwtNC40LDMwLjU1Yy0xLjc0LS41OC0zLjYtLjg5LTUuNTMtLjg5aC0xNS4zN2wzLjU1LTI0LjY2Yy43Mi01LjAzLS43Ny0xMC4xMy00LjEtMTMuOTctMy4zMy0zLjg0LTguMTYtNi4wNS0xMy4yNC02LjA1aC0zMC41OWMtNi4zMSwwLTExLjkzLDMuMzYtMTUuMDIsOC40OS02LjExLTYuMDgtMTQuNTMtOS44My0yMy44MS05LjgzLTguMDMsMC0xNS40MiwyLjgyLTIxLjIzLDcuNTEtMy4yMS0zLjc4LTgtNi4xNy0xMy4zNC02LjE3aC05Ny4xOWMtMTkuNjMsMC0zMi45MSwzLjg1LTQ0LjQsMTIuODgtMTMuNzcsMTAuODItMTcuNTQsMjcuMTktMTkuMTcsMzguNTVMLjE4LDE0NS4yOGMtLjcyLDUuMDMuNzcsMTAuMTMsNC4xLDEzLjk3LDMuMzMsMy44NCw4LjE2LDYuMDUsMTMuMjQsNi4wNWgzMC41OWM4LjcxLDAsMTYuMS02LjQsMTcuMzQtMTUuMDJsNC40NC0zMC44Mmg0MS40MmMuMDYsMCwuMTIsMCwuMTgsMGwtMy43MiwyNS44M2MtLjcyLDUuMDMuNzcsMTAuMTMsNC4xLDEzLjk3LDMuMzMsMy44NCw4LjE2LDYuMDUsMTMuMjQsNi4wNWg4Mi42YzguNzEsMCwxNi4xLTYuNCwxNy4zNC0xNS4wMmwxLjM2LTkuNDNjNS4yOCw4LjE2LDEyLjczLDE0Ljc4LDIxLjcyLDE4LjksMTAuMTIsNC42NCwyMC42Miw1LjU1LDM1LjM0LDUuNTVoMjIuNTNjOC4xNSwwLDE1LjEzLTUuNiwxNy4wMi0xMy4zNywzLjYyLDMuMTQsNy42OSw1Ljc5LDEyLjEzLDcuODMsMTAuMTIsNC42NCwyMC42Miw1LjU1LDM1LjM0LDUuNTVoMjIuNTNjOC43MSwwLDE2LjEtNi40LDE3LjM0LTE1LjAybDQuMy0yOS44NmMuMTYtLjk1LjI0LTEuOTIuMjQtMi45MiwwLTcuMDEtNC4xMS0xMy4wNS0xMC4wNS0xNS44NVoiLz4KICAgICAgPHBhdGggY2xhc3M9ImNscy0xIiBkPSJtNTguNzgsNzMuNjhoNTYuNTlsLTQuMDcsMjguMjVoLTU2LjU5bC02LjYxLDQ1Ljg0aC0zMC41OWwxMy4zMy05Mi41YzEuNzYtMTIuMTksNS4zMi0yMS41MSwxMi42Ni0yNy4yOCw3LjctNi4wNiwxNi43Ny05LjE0LDMzLjU3LTkuMTRoNDYuMTlsLTQuMzYsMzAuMjhoLTQzLjY0Yy00LjIzLDAtNy42OC4zMS0xMC4yNiwyLjI4LTIuMTYsMS42NC0zLjY0LDQuMy00LjMsOC44N2wtMS45MywxMy40Wm05Ni45LDc0LjFsMTguNTctMTI4LjkyaC0zMC41OWwtMTguNTcsMTI4LjkyaDMwLjU5Wm01Mi4wMiwwbDEyLjE0LTg0LjI0aC0zMC41OWwtMTIuMTQsODQuMjRoMzAuNTlabTE3LjQxLTExMy45N2MwLDktNy4yOSwxNi4yOS0xNi4yOCwxNi4yOXMtMTYuMjktNy4yOS0xNi4yOS0xNi4yOSw3LjI5LTE2LjI5LDE2LjI5LTE2LjI5LDE2LjI4LDcuMywxNi4yOCwxNi4yOVptMjIuNTQtMTQuOTVoMzAuNTlsLTYuNDQsNDQuNjhoMzUuNTlsLTQuMDcsMjguMjVoLTM1LjU5bC0xLjc0LDEyLjExYy0uNTQsMy43NS0uMjEsNy4wMSwxLjUyLDkuNDQsMi4xNiwzLjA0LDUuNzQsNC4xNSwxMi42OCw0LjE1aDMwLjE1bC00LjM2LDMwLjI4aC0yMi41M2MtMTQuNTcsMC0yMS42NC0xLjAyLTI4LjA0LTMuOTUtMTEuODktNS40NS0xOS4xOC0xNy4yOS0yMC4wOC0yOS45Ni0uMjYtMy42NS4xMi0xMC4zMywxLjMtMTguNDhsMTEuMDMtNzYuNTJabTg3LjAyLDBoMzAuNTlsLTYuNDQsNDQuNjhoMzUuNTlsLTQuMDcsMjguMjVoLTM1LjU5bC0xLjc0LDEyLjExYy0uNTQsMy43NS0uMjEsNy4wMSwxLjUyLDkuNDQsMi4xNiwzLjA0LDUuNzQsNC4xNSwxMi42OCw0LjE1aDMwLjE1bC00LjM2LDMwLjI4aC0yMi41M2MtMTQuNTcsMC0yMS42NC0xLjAyLTI4LjA0LTMuOTUtMTEuODktNS40NS0xOS4xOC0xNy4yOS0yMC4wOC0yOS45Ni0uMjYtMy42NS4xMi0xMC4zMywxLjMtMTguNDhsMTEuMDMtNzYuNTJaIi8+CiAgICA8L2c+CiAgPC9nPgo8L3N2Zz4=" 
          },
          params: {
            merchant_id: <?=$merchant_id?>,
            required_rectoken: "y",
            currency: "EUR",
            amount: 5000,
            response_url: 'https://nationaltrustofgeorgia.org.ge/<?=$Lang?>/donation/result',
            order_id: 1234560,
            lang: '<?=$Lang=='ge'?'ka':'en'?>',
            },

          css_variable: {
            main: '#2D5454',
            card_bg: '#489391',
            card_shadow: '#346A68' } };
    </script>
                
                
            </div>
            
        </div>
        
<script>
    
    
    $('input[type="radio"][name="amount"]').on('change',function(e){
        if($(this).val()==0)
            $('#amount').show().focus();
        else
            $('#amount').hide();
        })
    
  $(".tab-content a.btn.Details").click(function(){
    $('.nav-tabs a[href="#Details"]').tab('show')    
  });
    
  $(".tab-content a.btn.Payment").click(function(){
      var cur=$('input[type="radio"][name="currency"]:checked').val();
      var amount=$('input[type="radio"][name="amount"]:checked').val();
      if(amount==0) amount=$('input#amount').val();
      amount=parseInt(amount*100);
      Options.params.amount=amount;
      Options.params.currency=cur;
      
      var Name=$('input#Name').val();
      var Email=$('input#Email').val();
      var Address=$('input#Address').val();
      var Phone=$('input#Phone').val();
      var UserData={Name:Name, Email:Email, Address:Address, Phone:Phone};
      $('.nav-tabs a[href="#Payment"]').tab('show');
      $.ajax({
          url:'/interactive.php',
          type:'post',
          dataType:'json',
          data:{f:'genDonateOrder', Currency:cur, Amount:amount, UserData:UserData},
          success:function(data){
                if(data.ID){
                    Options.params.order_id='O_'+data.ID;
                    checkout("#checkout-container", Options);
                    }
              else
                  $('#checkout-container').html('ERROR: '+data);
            },
          error:function(data){
              console.log('error', data);
              $('#checkout-container').html('ERROR: '+data);
            },
          })
      });

</script>        
        
        
        
        
        
         
       

        
        <? } ?>
        
    </div>
</div>

<style>
    .donation img {
        width: auto;
    }
    .Donation label:hover{
        background: #0001;
    }
    .Donation input{
        margin-bottom: 0;
    }
</style>
