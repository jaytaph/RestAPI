<?php

class event_talk_commentController extends ji_restController {

    function handleRetrieveResource() {
        $response = $this->getResponse();
        $response->setBody("EVENT_TALK_COMMENT_CONTROLLER IS CALLED!!! WHOOHOO");
    }

    function handleRetrieveCollection() {
        $response = $this->getResponse();

        $a = array(1,2,3,4,5);
        $response->setBody($a);
    }

}