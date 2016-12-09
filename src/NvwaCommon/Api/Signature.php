<?php
/**
 * Created by IntelliJ IDEA.
 * User: r
 * Date: 16/7/13
 * Time: 下午1:25
 */

namespace NvwaCommon\Api;


class Signature
{

    private $signItem = "sign";
    private $secretKey = ".....secret key,change it please....";

    /**
     * Signature constructor.
     * @param string $secretKey
     */
    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }


    /**
     * @return string
     */
    public function getSignItem()
    {
        return $this->signItem;
    }

    /**
     * @param string $signItem
     */
    public function setSignItem($signItem)
    {
        $this->signItem = $signItem;
    }



    /**
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * @param string $secretKey
     */
    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }


    /**
     * @param array $paramArray
     * @return array
     * @throws ParamException
     */
    public  function buildParamForUrl(array $paramArray){
        if(isset($paramArray[$this->signItem])){
            throw new ParamException("paramArray can't contains an item with '".$this->signItem."'");
        }
        $sign = $this->createSign($paramArray);
        $newArray = $paramArray;
        $newArray[$this->signItem] = $sign;
        return $newArray;
    }

    /**
     * @param  array $paramArray  已有的API参数数组;
     * @return mixed 返回验证字串
     */
    public  function createSign(array $paramArray)
    {
        $sign = "";
        ksort($paramArray);
        foreach ($paramArray as $key => $val) {
            if ($key != '' && $val != '') {
                $sign .= $key . $val;
            }
        }
        return strtoupper(hash_hmac("md5", $sign, $this->getSecretKey()));
    }

    /**
     * @param array $paramArray
     * @return bool 返回是否校验正确.
     * @throws ParamException
     */
    public  function checkSign(array $paramArray) {
        if(!isset($paramArray[$this->signItem])){
            throw  new ParamException("paramArray must have an item named ".$this->signItem);
        }
        $signProvide = $paramArray[$this->signItem];
        unset($paramArray[$this->signItem]);
        $calculatedSign = $this->createSign($paramArray);
        return $calculatedSign==$signProvide;
    }
}