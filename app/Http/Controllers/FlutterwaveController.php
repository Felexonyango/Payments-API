<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Facade\FlareClient\View;

class FlutterwaveController extends Controller
{

    public function index()
    {
        return view('pages.index');
    }



    public function verify(Request $request){

      $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$request->transaction_id}/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . env('FLUTTER_S_KEY')
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $res = json_decode($response);

        // return response [$res];
        return response()->json(['success' =>true, 'data'=>[$res]]);
       
    }


    
}
