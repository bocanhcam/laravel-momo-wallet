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

    const REQUEST_TYPE = 'captureMoMoWallet';

    public function __construct(
        //$requestId,
        $orderId,
        $amount
        //$orderInfo,
        //$returnUrl
        //$notifyUrl
        //$requestType,
        //$extraData
    )
    {
        $this->partnerCode = config('laravel-momo.momo_partner_code');
        $this->accessKey = config('laravel-momo.momo_access_key');
        $this->requestId = config('laravel-momo.merchant_name').$orderId;
        $this->amount = (string) $amount;
        $this->orderId = config('laravel-momo.merchant_name').$orderId;
        $this->orderInfo = '';
        $this->returnUrl = config('laravel-momo.return_url');
        $this->notifyUrl = config('laravel-momo.notify_url');
        $this->requestType = self::REQUEST_TYPE;
        $this->extraData = '';
        $this->setSignature();
    }

    public function setOrderInfo($orderInfo)
    {
        $this->orderInfo = $orderInfo;
    }

    public function setExtraData($extraData)
    {
        $this->extraData = $extraData;
    }

    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }

    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    protected function setSignature()
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