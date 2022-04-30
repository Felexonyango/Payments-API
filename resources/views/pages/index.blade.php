<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <title>Document</title>
</head>
<body>
    <div class="container">
        <div class="card text-center bg-danger mb-5">Pay Now</div>
        <div class="row">
            <div class="col-md-4">
                <form  id="paymentForm" method="post">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name">
                    </div>
                    <div class="form-group">
                        <input type="text" name="email" id="email" class="form-control" placeholder="Enter Email">
                    </div>
                    <div class="form-group">
                        <input type="number" name="amount" id="amount" class="form-control" placeholder="Enter Amount">
                    </div>
                    <div class="form-group">
                        <input type="number" name="phone_number" id="phone_number" class="form-control" placeholder="Enter Phone Number">
                    </div>
                    <input type="submit" value="Pay" class="btn btn-primary">
                </form>
                
            </div>
        </div>
        
    </div>
    
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
    <script>
        const paymentForm = document.getElementById('paymentForm');
        paymentForm.addEventListener("submit", function(e){
            e.preventDefault();

            var name = document.getElementById('name').value;
            var email = document.getElementById('email').value;
            var amount = document.getElementById('amount').value;
            var phone_number = document.getElementById('phone_number').value;

            makePayment(name, email, amount, phone_number);
        });

        function makePayment(name, email, amount, phone_number) {
          FlutterwaveCheckout({
            public_key: "FLWPUBK_TEST-4df11aef1d3e74c2bfbaec48a1c462de-X",
            tx_ref: "RX1_{{rand(0,time())}}",
            amount,
            currency: "NGN",
            country: "NG",
            payment_options: " ",
            customer: {
              email,
              phone_number,
              name,
            },
            callback: function (data) {
              var trans_id = data.transaction_id;
              var _token = $("input[name='_token']").val();
              console.log
              //making an AJAX request

              $.ajax({
                type: "POST",
                url: "{{URL::to('verify-payment')}}",
                data: {
                    trans_id,
                    _token
                },
                success: function (response) {
                    console.log(response);
                }
            }); 

            },
            onclose: function() {
              // close modal
            },
            customizations: {
              title: "My store",
              description: "Payment for items in cart",
              logo: "https://images.fastcompany.net/image/upload/w_1280,f_auto,q_auto,fl_lossy/w_596,c_limit,q_auto:best,f_auto/fc/3034007-inline-i-applelogo.jpg",
            },
          });
        }
        
      </script>
</body>
</html>