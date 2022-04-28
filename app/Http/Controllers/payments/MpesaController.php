<?php

namespace App\Http\Controllers\payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
//register url

    public function MpesaRegisterurl()
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json', 
            'Authorization: Bearer '.
            $this->getAccessToken()
        ));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            'ShortCode' => '600141',
            'ResponseType' => 'Completed',
            'ConfirmationURL' => env('MPESA_TEST_URL'). '/confirmation',
            'ValidationURL' => env('MPESA_TEST_URL'). '/validation'
        )));

        $curl_response = curl_exec($curl);
        curl_close($curl);
        return $curl_response;
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
