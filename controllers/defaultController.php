<?php

class defaultController extends ji_restController {
    
    function handleRetrieveCollection() {
        $response = $this->getResponse();
        $response->setBody("COLLECTION STATUS IS UP AND RUNNING");
    }

    function handleRetrieveResource() {
        $request = $this->getRequest();
        $resource = $request->resource;

        $response = $this->getResponse();
        $response->setBody("RESOURCE STATUS IS UP AND RUNNING");
    }
}