<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class EsewaController extends Controller
{
    public function initiatePayment(Request $request)
    {
        try {
            
        $request->validate([
            'amount' => 'required|numeric',
            // 'referenceId' => 'required|string',
        ]);

        $amount = $request->input('amount');
        $referenceId = $request->input('referenceId')?? uniqid();

        $url = "https://uat.esewa.com.np/epay/main";
        $params = [
            'amt' => $amount,
            'pdc' => 0,
            'psc' => 0,
            'txAmt' => 0,
            'tAmt' => $amount,
            'pid' => $referenceId,
            'scd' => 'EPAYTEST',
            'su' => route('esewa.success'), // success route
            'fu' => route('esewa.failure'), // failure route
        ];

        $data=[
            'url' => $url,
            'params' => $params,
        ];
        return $this->jsonResponse($data,null,true,200);


    } catch (\Throwable $th) {
        return $this->jsonResponse(null,$th->getMessage(),false,500);

    }
    }

    public function verifyPayment(Request $request)
    {
        $request->validate([
            'referenceId' => 'required|string',
            'amount' => 'required|numeric',
            'transactionId' => 'required|string',
        ]);

        $referenceId = $request->input('referenceId');
        $amount = $request->input('amount');
        $transactionId = $request->input('transactionId');

        $client = new Client();
        $res = $client->request('POST', 'https://uat.esewa.com.np/epay/transrec', [
            'form_params' => [
                'amt' => $amount,
                'rid' => $transactionId,
                'pid' => $referenceId,
                'scd' => 'EPAYTEST',
            ],
        ]);

        $response = simplexml_load_string($res->getBody());

        if (isset($response->response_code) && $response->response_code == 'Success') {
            // Handle successful payment
            return response()->json(['status' => 'success']);
        } else {
            // Handle failed payment
            return response()->json(['status' => 'failed']);
        }
    }
    public function success(Request $request)
    {
        // Handle successful payment logic here
        // For example, verify the payment, update order status, etc.
        // return $this->jsonResponse($request->all(),'Payment was successful',true,200);
       
        // return redirect()->away(" http://localhost:5173" . '/success?status=success&reference=' . $request->input('oid'));
        return redirect()->away("http://localhost:5173" . '/success?status=success&reference=' . $request->input('oid'));






    }

    public function failure(Request $request)
    {
        return redirect()->away("http://localhost:5173" . '/success?status=failure');

    }
}
