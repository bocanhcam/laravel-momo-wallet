<?php

namespace Hora\LaravelMomoWallet\Models;

use GuzzleHttp\Client;

class AIORequest{

    public $partnerCode;
    public $accessKey;
    public $requestId;
    public $amount;
    public $orderId;
    public $orderInfo;
    public $returnUrl;
    public $notifyUrl;
    public $requestType;
    public $signature;
    public $extraData;

    public function __construct(
        $requestId,
        $amount,
        $orderId,
        $orderInfo,
        $returnUrl,
        $notifyUrl,
        $requestType,
        $extraData
    )
    {
        $this->partnerCode = config('laravel-momo.momo_partner_code');
        $this->accessKey = config('laravel-momo.momo_access_key');
        $this->requestId = (string) $requestId;
        $this->amount = (string) $amount;
        $this->orderId = (string) $orderId;
        $this->orderInfo = $orderInfo;
        $this->returnUrl = $returnUrl;
        $this->notifyUrl = $notifyUrl;
        $this->requestType = $requestType;
        $this->extraData = $extraData;
        $this->setSignature();
    }

    public function setSignature()
    {
        $string = $this->makeQuery();
        $this->signature = hash_hmac('sha256', $string, 'TOlFoncBLYdGjeUYkH5DxI0UQxlXnYcn');
    }

    protected function makeQuery()
    {
        $query = 'partnerCode='.$this->partnerCode
            .'&accessKey='.$this->accessKey
            .'&requestId='.$this->requestId
            .'&amount='.$this->amount
            .'&orderId='.$this->orderId
            .'&orderInfo='.$this->orderInfo.
            '&returnUrl='.$this->returnUrl.
            '&notifyUrl='.$this->notifyUrl
            .'&extraData='.$this->extraData;

        return $query;
    }

    public function makeRequest()
    {
        $client = new Client();

        $response = $client->request('POST',config('laravel-momo.momo_payment_request'),[
            'json' => [
                'partnerCode' => $this->partnerCode,
                'accessKey' => $this->accessKey,
                'requestId' => $this->requestId,
                'amount' => $this->amount,
                'orderId' => $this->orderId,
                'orderInfo' => $this->orderInfo,
                'returnUrl' => $this->returnUrl,
                'notifyUrl' => $this->notifyUrl,
                'requestType' => $this->requestType,
                'signature' => $this->signature,
                'extraData' => $this->extraData
            ],
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8'
            ]
        ]);

        return $response;
    }
}