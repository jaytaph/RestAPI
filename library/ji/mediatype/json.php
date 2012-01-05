<?php

class ji_mediatype_json {

    function getViewTemplate($name) {
        return "json/".$name;
    }

    function preOutput(ji_request $request, ji_response $response) {
        $response->addHeader("content-type", "application/json");
    }

}