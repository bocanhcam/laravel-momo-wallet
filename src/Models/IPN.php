<?php

namespace Hora\LaravelMomoWallet\Models;

class IPN{

    //public $partnerCode;
    //public $accessKey;
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

    const SUCCESS = 0;
    const ERROR_ORDER_ID_WRONG_FORMART = 2;
    const ERROR_SIGNATURE_WRONG = 5;
    const ERROR_ORDER_ID_ALREADY_HAS = 6;
    const ERROR_ORDER_ID_NOT_FOUND = 58	;
    const ERROR_UNKNOWN = 99;

    public $responseMessage = [
        self::SUCCESS => 'Giao dịch thành công',
        self::ERROR_ORDER_ID_WRONG_FORMART => 'OrderId Sai định dạng',
        self::ERROR_SIGNATURE_WRONG => 'Sai chữ ký',
        self::ERROR_ORDER_ID_ALREADY_HAS => 'Đơn hàng đã thanh toán',
        self::ERROR_ORDER_ID_NOT_FOUND => 'Đơn hàng không tồn tại',
        self::ERROR_UNKNOWN => 'Lỗi không xác định',
    ];

    public function __construct(array $params = array())
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (array_key_exists($key, $params)) {
                $this->{$key} = $params[$key];
            }
        }
    }

    protected function makeQueryRequest()
    {
        $query = 'partnerCode='.config('laravel-momo.momo_partner_code')
            .'&accessKey='.config('laravel-momo.momo_access_key')
            .'&requestId='.$this->requestId
            .'&amount='.$this->amount
            .'&orderId='.$this->orderId
            .'&orderInfo='.$this->orderInfo
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

    public function checkSignatureRequest()
    {
        $string = $this->makeQueryRequest();
        $signature = hash_hmac('sha256', $string, config('laravel-momo.momo_secret_key'));

        if (!$this->signature == $signature){
            return false;
        }

        return true;
    }

    public function getResponse($errorCode)
    {
        date_default_timezone_set('asia/ho_chi_minh');

        return [
            'partnerCode' => config('laravel-momo.momo_partner_code'),
            'accessKey' => config('laravel-momo.momo_access_key'),
            'requestId' => $this->requestId,
            'orderId' => $this->orderId,
            'errorCode' => $errorCode,
            'message' => !empty($this->responseMessage[$errorCode]) ? $this->responseMessage[$errorCode] : "",
            'responseTime' => date("YYYY-MM-DD HH:mm:ss"),
            'extraData' => $this->extraData,
            'signature' => $this->getSignatureResponse($errorCode)
        ];
    }

    protected function getSignatureResponse($errorCode)
    {
        $string = $this->makeQueryResponse($errorCode);
        return hash_hmac('sha256', $string, config('laravel-momo.momo_secret_key'));
    }

    protected function makeQueryResponse($errorCode)
    {
        $query = 'partnerCode='.config('laravel-momo.momo_partner_code')
            .'&accessKey='.config('laravel-momo.momo_access_key')
            .'&requestId='.$this->requestId
            .'&orderId='.$this->orderId
            .'&errorCode='.$errorCode
            .'&message='.$this->message
            .'&responseTime='.$this->responseTime
            .'&extraData='.$this->extraData;

        return $query;
    }
}
