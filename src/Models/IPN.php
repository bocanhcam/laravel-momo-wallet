<?php
namespace Hora\LaravelMomoWallet\Models;

class IPN{

    public $partnerCode;
    public $accessKey;
    public $requestId;
    public $amount;
    public $orderId;
    public $orderInfo;
    public $orderType;
    public $transId;
    public $errorCode;
    public $message;
    public $localMessage;
    public $payType;
    public $responseTime;
    public $extraData;
    public $signature;

    const ORDER_TYPE = 'momo_wallet';

    public function __construct(
        $partnerCode,
        $accessKey,
        $requestId,
        $amount,
        $orderId,
        $orderInfo,
        $orderType,
        $transId,
        $errorCode,
        $message,
        $localMessage,
        $payType,
        $responseTime,
        $extraData,
        $signature
    )
    {
        $this->partnerCode = $partnerCode;
        $this->accessKey = $accessKey;
        $this->requestId = $requestId;
        $this->amount = $amount;
        $this->orderId = $orderId;
        $this->orderInfo = $orderInfo;
        $this->orderType = $orderType;
        $this->transId = $transId;
        $this->errorCode = $errorCode;
        $this->message = $message;
        $this->localMessage = $localMessage;
        $this->payType = $payType;
        $this->responseTime = $responseTime;
        $this->extraData = $extraData;
        $this->signature = $signature;
    }

    protected function makeQueryRequest(
        $requestId,
        $amount,
        $orderId,
        $orderInfo
    )
    {
        $query = 'partnerCode='.config('laravel-momo.momo_partner_code')
            .'&accessKey='.config('laravel-momo.momo_access_key')
            .'&requestId='.$requestId
            .'&amount='.$amount
            .'&orderId='.$orderId
            .'&orderInfo='.$orderInfo
            .'&orderType='.self::ORDER_TYPE
            .'&transId='.$this->transId
            .'&message='.$this->message
            .'&localMessage='.$this->localMessage
            .'&responseTime='.$this->responseTime
            .'&errorCode='.$this->errorCode
            .'&payType='.$this->payType
            .'&extraData='.$this->extraData;

        return $query;
    }

    public function checkSignatureRequest(
        $requestId,
        $amount,
        $orderId,
        $orderInfo
    )
    {
        $string = $this->makeQueryRequest($requestId,$amount,$orderId,$orderInfo);
        $signature = hash_hmac('sha256', $string, config('laravel-momo.momo_secret_key'));

        if (!$this->signature == $signature){
            return false;
        }

        return true;
    }

    public function getResponse(
        $requestId,
        $orderId,
        $errorCode,
        $message,
        $responseTime,
        $extraData
    )
    {
        return [
            'partnerCode' => config('laravel-momo.momo_partner_code'),
            'accessKey' => config('laravel-momo.momo_access_key'),
            'requestId' => $requestId,
            'orderId' => $orderId,
            'errorCode' => $errorCode,
            'message' => $message,
            'responseTime' => $responseTime,
            'extraData' => $extraData,
            'signature' => $this->getSignatureResponse(
                $requestId,
                $orderId,
                $errorCode,
                $message,
                $responseTime,
                $extraData
            )
        ];
    }

    protected function getSignatureResponse(
        $requestId,
        $orderId,
        $errorCode,
        $message,
        $responseTime,
        $extraData
    )
    {
        $string = $this->makeQueryResponse(
            $requestId,
            $orderId,
            $errorCode,
            $message,
            $responseTime,
            $extraData
        );
        return hash_hmac('sha256', $string, config('laravel-momo.momo_secret_key'));
    }

    protected function makeQueryResponse(
        $requestId,
        $orderId,
        $errorCode,
        $message,
        $responseTime,
        $extraData
    )
    {
        $query = 'partnerCode='.config('laravel-momo.momo_partner_code')
            .'&accessKey='.config('laravel-momo.momo_access_key')
            .'&requestId='.$requestId
            .'&orderId='.$orderId
            .'&errorCode='.$errorCode
            .'&message='.$message
            .'&responseTime='.$responseTime
            .'&extraData='.$extraData;

        return $query;
    }
}