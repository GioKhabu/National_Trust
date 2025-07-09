<?php
$merchant_id = MERCHANT_ID;
?>
<div class="container Donation">
    <div class="row">
        <?php if ($page == 'result') {
            if (isset($_POST['merchant_id']) && $_POST['merchant_id'] == $merchant_id) {
                $amount = $_POST['amount'] / 100;
                $currency = $_POST['currency'];
                $order_status = $_POST['order_status'];
                $response_status = $_POST['response_status'];
                $order_id_parts = explode('_', $_POST['order_id']);
                $order_id = (int)$order_id_parts[1];

                if ($order_id > 0) {
                    mysqli_query($baza, 'UPDATE Donation SET Status="' . $response_status . '", Response="' . addslashes(json_encode($_POST, 256)) . '" WHERE ID=' . $order_id);
                }

                if ($order_status == 'approved' && $response_status == 'success') {
                    info(_Interface('თქვენს მიერ გაცემული თანხა:') . ' ' . $amount . ' ' . $currency . ' ' . _Interface('წარმატებით ჩაირიცხა ფონდის ანგარიშზე') . '<br><br>' . _Interface('მადლობა შემოწირულობისთვის'), 0, 300);
                } else {
                    error(_Interface('შეცდომა თანხის ჩარიხვისას'), 0, 300);
                }
            } else {
                error(_Interface('404. არასწორი მომართვა გვერდზე'), 0, 300);
            }
        } else { ?>
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
                            <label><input type="radio" name="currency" value="USD" checked> USD </label>
                            <label><input type="radio" name="currency" value="EUR"> EUR </label>
                            <label><input type="radio" name="currency" value="GEL"> GEL </label>
                            <br><br>
                        </div>
                        <div class="col-md-6">
                            <div><?=_Interface('Donation Amount')?></div>
                            <label><input type="radio" name="amount" value="20" checked> 20 </label>
                            <label><input type="radio" name="amount" value="50"> 50 </label>
                            <label><input type="radio" name="amount" value="100"> 100 </label>
                            <label><input type="radio" name="amount" value="250"> 250 </label>
                            <label><input type="radio" name="amount" value="500"> 500 </label>
                            <label ><input type="radio" name="amount" value="0" > <?=_Interface('Other Amount')?> </label>
                            <input type="number" name="amount" id="amount" min="1" value="1000" step="1" style="display: none;">
                        </div>
                    </div>
                    <hr>
                    <a class="btn btn-primary Details"><?=_Interface('Next')?></a>
                </div>

                <div id="Details" class="tab-pane fade">
                    <h3><?=_Interface('Your Details')?></h3>
                    <p><?=_Interface('სახელი')?> <input type="text" name="Name" id="Name"></p>
                    <p><?=_Interface('ელ-ფოსტა')?> <input type="email" name="Email" id="Email"></p>
                    <p><?=_Interface('მისამართი')?> <input type="text" name="Address" id="Address"></p>
                    <p><?=_Interface('ტელეფონი')?> <input type="text" name="Phone" id="Phone"></p>
                    <hr>
                    <a class="btn btn-primary Payment"><?=_Interface('Next')?></a>
                </div>

                <div id="Payment" class="tab-pane fade">
                    <div id="checkout-container"></div>
                    <script src="https://pay.flitt.com/latest/checkout-vue/checkout.js"></script>
                </div>
            </div>

            <script>
                $('input[type="radio"][name="amount"]').on('change', function () {
                    if ($(this).val() == 0) {
                        $('#amount').show().focus();
                    } else {
                        $('#amount').hide();
                    }
                });

                $(".tab-content a.btn.Details").click(function () {
                    $('.nav-tabs a[href="#Details"]').tab('show');
                });

                $(".tab-content a.btn.Payment").click(function () {
                    var cur = $('input[type="radio"][name="currency"]:checked').val();
                    var amount = $('input[type="radio"][name="amount"]:checked').val();
                    if (amount == 0) {
                        amount = $('input#amount').val();
                    }
                    amount = parseInt(amount * 100);

                    var Name = $('#Name').val();
                    var Email = $('#Email').val();
                    var Address = $('#Address').val();
                    var Phone = $('#Phone').val();
                    var UserData = { Name: Name, Email: Email, Address: Address, Phone: Phone };

                    $('.nav-tabs a[href="#Payment"]').tab('show');
                    $('#checkout-container').html("Loading...");

                    $.ajax({
                        url: '/interactive.php',
                        type: 'post',
                        dataType: 'json',
                        data: { f: 'genDonateOrder', Currency: cur, Amount: amount, UserData: UserData },
                        success: function (data) {
                            if (data.ID) {
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
                                        theme: { type: "light", preset: "reset" }
                                    },
                                    params: {
                                        merchant_id: <?=$merchant_id?>,
                                        required_rectoken: "y",
                                        currency: cur,
                                        amount: amount,
                                        order_desc: "Donation Order #" + data.ID,
                                        response_url: 'https://nationaltrustofgeorgia.org.ge/<?=$Lang?>/donation/result',
                                        order_id: "O_" + data.ID,
                                        lang: '<?=$Lang == 'ge' ? 'ka' : 'en'?>'
                                    },
                                    css_variable: {
                                        main: '#2D5454',
                                        card_bg: '#489391',
                                        card_shadow: '#346A68'
                                    }
                                };
                                checkout("#checkout-container", Options);
                            } else {
                                $('#checkout-container').html('ERROR: ' + JSON.stringify(data));
                            }
                        },
                        error: function (data) {
                            console.log('error', data);
                            $('#checkout-container').html('ERROR: ' + JSON.stringify(data));
                        }
                    });
                });
            </script>

            <style>
                .Donation img {
                    width: auto;
                }
                .Donation label:hover {
                    background: #0001;
                }
                .Donation input {
                    margin-bottom: 0;
                }
            </style>
        <?php } ?>
    </div>
</div>
