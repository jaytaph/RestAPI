<?php

class ji_mediatype_html {

    function getViewTemplate($name) {
        return "html/".$name;
    }

    function preOutput(ji_request $request, ji_response $response) {
        $response->addHeader("content-type", "text/html");
    }

}