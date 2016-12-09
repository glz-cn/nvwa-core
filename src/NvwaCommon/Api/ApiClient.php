<?php
/**
 * Created by IntelliJ IDEA.
 * User: renlu
 * Date: 16/7/13
 * Time: 下午1:47
 */

namespace NvwaCommon\Api;
use Snoopy\Snoopy;

class ApiClient
{
    private $paramArray =array();
    private $apiUrlPrefix = "";
    private $secretKey = "";

    private $signature ;

    /**
     * ApiClient constructor.
     * @param string $apiUrlPrefix
     * @param array $paramArray
     * @param string $secretKey
     */
    public function __construct($apiUrlPrefix,array $paramArray,  $secretKey)
    {
        $this->paramArray = $paramArray;
        $this->apiUrlPrefix = $apiUrlPrefix;
        $this->secretKey = $secretKey;
        $this->signature = new Signature($secretKey);

    }

    /**
     * 修改某一个参数的值;
     * @param $key
     * @param $value
     */
    public function putParam($key,$value){
        $this->paramArray[$key] = $value;
    }


    /**
     *  用GET的方式提交到某一个URL地址;
     *
     * @param null $snoopyObject
     * @return mixed
     * @throws ParamException
     */
    public function fetch($snoopyObject=null){
        if(is_null($snoopyObject)){
            $snoopyObject = new Snoopy();
        }
        $paramForUrl = $this->signature->buildParamForUrl($this->getParamArray());
        $snoopyObject->fetch($this->buildUrl($paramForUrl));
        return $snoopyObject->results;
    }

    /**
     * @return array
     */
    public function getParamArray()
    {
        return $this->paramArray;
    }

    /**
     * @param array $paramArray
     */
    public function setParamArray($paramArray)
    {
        $this->paramArray = $paramArray;
    }

    public function buildUrl(array $params)
    {
        $urlArgs = [];
        foreach ($params as $key => $val) {
            $urlArgs[] = urlencode($key) . "=" . urlencode($val);
        }
        return $this->getApiUrlPrefix() . "?" . join("&", $urlArgs);
    }

    /**
     * @return string
     */
    public function getApiUrlPrefix()
    {
        return $this->apiUrlPrefix;
    }

    /**
     * @param string $apiUrlPrefix
     */
    public function setApiUrlPrefix($apiUrlPrefix)
    {
        $this->apiUrlPrefix = $apiUrlPrefix;
    }

    /**
     * 用POST的方式提交数据;
     * @param null $snoopyObject
     * @return mixed
     * @throws ParamException
     */
    public function post($snoopyObject = null)
    {
        if (is_null($snoopyObject)) {
            $snoopyObject = new Snoopy();
        }
        $paramForUrl = $this->signature->buildParamForUrl($this->getParamArray());
        $snoopyObject->submit($this->getApiUrlPrefix(), $paramForUrl);
        return $snoopyObject->results;
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





}