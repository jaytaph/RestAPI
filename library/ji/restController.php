<?php

abstract class ji_restController {
    /**
     * @var ji_request
     */
    protected $_request = null;

    /**
     * @var ji_response
     */
    protected $_response = null;

    /**
     * 
     */
    function __construct() {
    }


    /**
     * Global REST handler
     *
     * @param stdClass $request
     * @return null
     */
    function handle(ji_request $request) {
        $this->setRequest($request);
        if ($this->getResponse() == null) {
            $this->setResponse(new ji_response());
        }

        // Check if we need to call resource or collection
        $entity = $request->isResource() ? "Resource" : "Collection";

        /**
         * HTTP PUT and POST are pretty much interchangeable. Can be configured here 
         */
        switch (strtolower($this->getRequest()->getMethod())) {
            case "delete" :
                // Delete is always delete
                $crudMethod = "Delete";
                break;
            case "put" :
                // PUT is create or update, depending on resource or collection
                $crudMethod = ($request->isResource()) ? "Update" : "Create";
                break;
            case "post" :
                // POST is create or update, depending on resource or collection
                $crudMethod = ($request->isResource()) ? "Update" : "Create";
                break;
            case "get" :
            default :
                // Default and GET are retrievals
                $crudMethod = "Retrieve";
                break;
        }

        // Call function
        $handleFunc = "handle".$crudMethod.$entity;
        $this->$handleFunc();

        // Return response
        return $this->getResponse();
    }

    /**
     * @param ji_request $request
     * @return void
     */
    function setRequest(ji_request $request) {
        $this->_request = $request;
    }

    /**
     * @return ji_request|null
     */
    function getRequest() {
        return $this->_request;
    }

    /**
     * @param ji_response $response
     * @return void
     */
    function setResponse(ji_response $response) {
        $this->_response = $response;
    }

    /**
     * @return ji_response|null
     */
    function getResponse() {
        return $this->_response;
    }

    /**
     * Error creator. Just an easy way to fill the response for error outputting
     */
    function createError($http_code, $http_status, $body = "") {
        $response = $this->getResponse();
        $response->setHttpCode($http_code);
        $response->setHttpStatus($http_status);
        $response->setBody($body);
    }


    /**
     * 8 default handle functions. Will handle CRUD operations for both resources and collections.
     * By default they will return a 501 status since they are not defined.
     */
    function handleCreateCollection() {
        $this->createError(501, "Not implemented");
    }

    function handleUpdateCollection() {
        $this->createError(501, "Not implemented");
    }

    function handleRetrieveCollection() {
        $this->createError(501, "Not implemented");
    }

    function handleDeleteCollection() {
        $this->createError(501, "Not implemented");
    }

    function handleCreateResource() {
        $this->createError(501, "Not implemented");
    }

    function handleCUpdateResource() {
        $this->createError(501, "Not implemented");
    }

    function handleRetrieveResource() {
        return $this->createError(501, "Not implemented");
    }

    function handleDeleteResource() {
        return $this->createError(501, "Not implemented");
    }

    /**
     * Called when this controller is a parent of another controller. Checks if the item exists or not.
     * For example:
     *
     * GET event/5/talk/3/comment/1   (get first comment from 3rd talk from event 5).
     *
     * will call
     *   + eventController::passthrough(5),
     *   + event_talkController::passthrough(3)
     *   + event_talk_commentController::retrieveResource(1)
     *
     * @param  $resource
     * @return boolean true|false
     */
    function passThrough($resource) {
        return false;
    }

}
