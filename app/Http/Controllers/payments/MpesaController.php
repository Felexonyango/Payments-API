<?php

namespace App\Http\Controllers\payments;
use Carbon\Carbon;
use App\Models\MpesaTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;


class MpesaController extends Controller
{
    public function getAccessToken(){
        //we are making test local with 0 but if its life we change to 1
        $url = env('MPESA_ENV') == 0
        ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
        : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic '.
            base64_encode(
                env('MPESA_CONSUMER_KEY'). ':' . env('MPESA_CONSUMER_SECRET')
            )
        ));
        curl_setopt($curl, CURLOPT_HEADER,false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);
        $access_token = json_decode($curl_response);

        curl_close($curl);
        return $access_token->access_token;

    }

    public function mpesaPassword(){
        $lipa_time = Carbon::rawParse('now')->format('YmdHms');
        $passkey = env('MPESA_PASSKEY');
        $BSC = env('MPESA_STK_SHORTCODE');
        $timestamp = $lipa_time;

        $lipa_na_mpesa_password = base64_encode($BSC.$passkey.$timestamp);
        return $lipa_na_mpesa_password;
    }


    public function stkPush(Request $request){
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer'.' '.
            $this->getAccessToken(),
            'Content-Type:application/json'
            )
        );

        $curl_post_data = [
            'BusinessShortCode' => env('MPESA_STK_SHORTCODE'), 
            'Password' => $this->mpesaPassword(),
            'Timestamp' => Carbon::rawParse('now')->format('YmdHms'),
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $request->amount,
            'PartyA' => $request->phone,
            'PartyB' => env('MPESA_STK_SHORTCODE'),
            'PhoneNumber' => $request->phone,
            'CallBackURL' => env('MPESA_TEST_URL'). '/stkpush',
            'AccountReference' => 'Felex web',
            'TransactionDesc' => 'Testing stk push',
        ];

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
//confirmation from mpesa 
    public function mpesaConfirmation(Request $request){
        $content = json_decode($request->getContent());

        $mpesa = new MpesaTransaction();
        $mpesa->TransactionType = $content->TransactionType;
        $mpesa->TransactionID = $content->TransID;
        $mpesa->TransTime = $content->TransTime;
        $mpesa->BusinessShortCode = $content->BusinessShortCode;
        $mpesa->BillRefNumber = $content->BillRefNumber;
        $mpesa->InvoiceNumber = $content->InvoiceNumber;
        $mpesa->OrgAccountBalance = $content->OrgAccountBalance;
        $mpesa->ThirdPartyTransID = $content->ThirdPartyTransID;
        $mpesa->MSISDN = $content->MSISDN;
        $mpesa->FirstName = $content->FirstName;
        $mpesa->MiddleName = $content->MiddleName;
        $mpesa->LastName = $content->LastName;
        $mpesa->save();
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml; charset=utf-8');
        $response->setContent(json_encode([
            'C2BPaymentConfirmationResult' => 'Success'
        ]));

        return $response;
    }


//register url
    public function MpesaRegisterurl()
    {
        $body = array(
            'ShortCode' => env('MPESA_SHORTCODE'),
            'ResponseType' => 'Completed',
            'ConfirmationURL' => env('MPESA_TEST_URL') . '/confirmation',
            'ValidationURL' => env('MPESA_TEST_URL') . '/validation'
        );

        $url = '/stkpush/v1/processrequest';
        $response = $this->makeHttp($url, $body);

        return $response;
    }


    public function makeHttp($url,$body){
     $curl = curl_init();
     curl_setopt_array(
         $curl,
         array(
                 CURLOPT_URL => $url,
                 CURLOPT_HTTPHEADER => array('Content-Type:application/json','Authorization:Bearer '.$this->getAccessToken()),
                 CURLOPT_RETURNTRANSFER => true,
                 CURLOPT_POST => true,
                 CURLOPT_POSTFIELDS => json_encode($body)
             )
     );
     $curl_response = curl_exec($curl);
     curl_close($curl);
     return $curl_response;
    }
}
