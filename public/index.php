<?php

// Full error reporting
error_reporting(-1);
ini_set('display_errors', true);

// Include rest mapping
include_once("../config/restmap.php");

// Setup
set_exception_handler('handle_exception');
$request = new ji_request($_SERVER);

// Pass through parent controllers if needed (in case of /event/%d/talk/%d/comment/%d)
foreach ($request->getParentElements() as $element) {
    $controllerClass = $element['path'].$tmp[0]."Controller";
    print "Controller: ".$controllerClass." : ";

    // Check if we are allowed to continue
    $controller = new $controllerClass();
    if ($controller->passThrough($element['resource'])) {
        print "Continue; <br>\n";
        // This is ok, continue with the next element form the URL
    } else {
        print "No continue; <br>\n";
        // Do something, like returning a 404
    }
}

// Call the actual endpoint controller (we call handle(), not passthrough() and we call without resource) 
$element = $request->getMainElement();
$controllerClass = $element['path'].$element['element']."Controller";
$controller = new $controllerClass();
$response = $controller->handle($request);

// Response is populated. Do something with it, like output depending on the response values (which media format etc)
var_dump ($response);
exit;


/**
 * Not used. Output thingie.
 */
function output(ji_response $response) {
    // Output
    header("Status ".$response->getHttpCode()." ".$response->getHttpStatus());

    // Output additional headers
    foreach ($response->getHeaders() as $header) {
        header($header);
    }

    return $response->body;
}





/**
 *  Add exception handler
 */
function handle_exception($e) {
    // TODO pass this through the output handlers
	echo "BADNESS<pre>";
	var_dump($e);
	error_log('Exception Handled: ' . $e->getMessage());
}

/**
 * autoloader
 */
function __autoload($classname) {
    // We use directory separators in our classes.
    $classname = str_replace('_', '/', $classname);

	if(false !== strpos($classname, '.')) {
		// this was a filename, don't bother
		return;
	}

    if(preg_match('/^ji\//',$classname)) {
        // Check ji_* classes inside the library
        include('../library/' . $classname . '.php');
        return true;
    } elseif(preg_match('/[a-zA-Z]+Controller$/',$classname)) {
		include('../controllers/' . $classname . '.php');
		return true;
	} elseif(preg_match('/[a-zA-Z]+Model$/',$classname)) {
		include('../models/' . $classname . '.php');
		return true;
	} elseif(preg_match('/[a-zA-Z]+View$/',$classname)) {
		include('../views/' . $classname . '.php');
		return true;
	}
}
