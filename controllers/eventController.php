<?php

class eventController extends ji_restController {
    function passThrough($resource) {
        return ($resource == 2);
    }
}