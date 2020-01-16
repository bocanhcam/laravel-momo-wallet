<?php

namespace Hora\LaravelMomoWallet\Models;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Refund{

    public $partnerCode;
    public $accessKey;
    public $requestId;
    public $amount;
    public $orderId;
    public $transId;
    public $requestType;
    public $signature;

    const REQUEST_TYPE = 'refundMoMoWallet';

    public function __construct(
        $orderId,
        $amount,
        $transId
    )
    {
        $this->partnerCode = config('laravel-momo.momo_partner_code');
        $this->accessKey = config('laravel-momo.momo_access_key');
        $this->requestId = config('laravel-momo.merchant_refund_prefix').$orderId;
        $this->amount = (string) $amount;
        $this->orderId = config('laravel-momo.merchant_refund_prefix').$orderId;
        $this->transId = $transId;
        $this->requestType = self::REQUEST_TYPE;
    }

    public function setSignature()
    {
        $string = $this->makeQuery();
        $this->signature = hash_hmac('sha256', $string, config('laravel-momo.momo_secret_key'));
    }

    protected function makeQuery()
    {
        $query = 'partnerCode='.$this->partnerCode
            .'&accessKey='.$this->accessKey
            .'&requestId='.$this->requestId
            .'&amount='.$this->amount
            .'&orderId='.$this->orderId
            .'&transId='.$this->transId
            .'&requestType='.$this->requestType;

        return $query;
    }

    public function makeRequest()
    {
        $client = new Client();

        try {
            $response = $client->request('POST',config('laravel-momo.momo_payment_request'),[
                'json' => [
                    'partnerCode' => $this->partnerCode,
                    'accessKey' => $this->accessKey,
                    'requestId' => $this->requestId,
                    'amount' => $this->amount,
                    'orderId' => $this->orderId,
                    'transId' => $this->transId,
                    'requestType' => $this->requestType,
                    'signature' => $this->signature
                ],
                'headers' => [
                    'Content-Type' => 'application/json; charset=UTF-8'
                ]
            ]);
            return $response;
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }

}