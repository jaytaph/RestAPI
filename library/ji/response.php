<?php

class ji_response {
    protected $_httpCode = 200;
    protected $_httpStatus = "Ok";
    protected $_body = "";
    protected $_headers = array();

    function __construct() {
    }

    function setHttpCode($httpCode) {
        $this->_httpCode = $httpCode;
    }

    function getHttpCode() {
        return $this->_httpCode;
    }

    function getHeaders() {
        return $this->_headers;
    }

    public function setBody($body) {
        $this->_body = $body;
    }

    public function getBody() {
        return $this->_body;
    }

    public function setHttpStatus($httpStatus)
    {
        $this->_httpStatus = $httpStatus;
    }

    public function getHttpStatus()
    {
        return $this->_httpStatus;
    }

    public function addHeader($key, $value) {
       $this->_headers[$key] = $value;
    }

} 
