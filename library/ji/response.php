<?php

class ji_response {
    protected $_httpCode = 200;
    protected $_httpStatus = "Ok";
    protected $_body = "";

    function __construct() {
    }

    function setHttpCode($httpCode) {
        $this->_httpCode = $httpCode;
    }

    function getHttpCode() {
        return $this->_httpCode = $httpCode;
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

} 