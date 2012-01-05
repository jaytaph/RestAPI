<?php

class ji_mediatype_xml {

    function getViewTemplate($name) {
        return "xml/".$name;
    }

    function preOutput(ji_request $request, ji_response $response) {
        $response->addHeader("content-type", "text/xml");
    }

}