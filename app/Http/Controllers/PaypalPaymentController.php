<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaypalPaymentController extends Controller
{
    public function create(Request $request)
    {
 // Check if User has Enrollment
//  $enrollment = Enrollment::where('user_id', auth()->id())->firstOrCreate(['user_id' => auth()->id()]);

 // Init PayPal
 $provider = \PayPal::setProvider();
 $data = json_decode($request->getContent(), true);

 $provider->setApiCredentials(config('paypal')); // Pull values from Config
 $token = $provider->getAccessToken();
 $provider->setAccessToken($token);

 // Prepare Order
 $order = $provider->createOrder([
     'intent'=> 'CAPTURE',
     'purchase_units'=> [[
         'reference_id' => 'transaction_test_number',
         'amount'=> [
           'currency_code'=> 'USD',
           'value'=> '20.00'
         ]
     ]],
     'application_context' => [
          'cancel_url' => 'http://myproject.test/dashboard/payment/cancel',
          'return_url' => 'http://myproject.test/dashboard/payment/success'
     ]
 ]);

//  // Store Token so we can retrieve after PayPal sends them back to us
//  $enrollment->payment_transaction = $order['id'];
//  $enrollment->save();

 // Send user to PayPal to confirm payment//
 return redirect($order['links'][1]['href'])->send();
     echo('Create working');
    }

public function capture(Request $request)
{
    
//     $data = json_decode($request->getContent(), true);
//     $orderId = $data['orderId'];
//     $this->paypalClient->setApiCredentials(config('paypal'));
//     $token = $this->paypalClient->getAccessToken();
//     $this->paypalClient->setAccessToken($token);
//     $result = $this->paypalClient->capturePaymentOrder($orderId);

// //            $result = $result->purchase_units[0]->payments->captures[0];
//     try {
//         DB::beginTransaction();
//         if($result['status'] === "COMPLETED"){
//             $transaction = new Transaction;
//             $transaction->vendor_payment_id = $orderId;
//             $transaction->payment_gateway_id  = $data['payment_gateway_id'];
//             $transaction->user_id = $data['user_id'];
//             $transaction->status   = TransactionStatus::COMPLETED;
//             $transaction->save();
//             $order = Order::where('vendor_order_id', $orderId)->first();
//             $order->transaction_id = $transaction->id;
//             $order->status = TransactionStatus::COMPLETED;
//             $order->save();
//             DB::commit();
//         }
//     } catch (Exception $e) {
//         DB::rollBack();
//         dd($e);
//     }
//     return response()->json($result);
 }
}
