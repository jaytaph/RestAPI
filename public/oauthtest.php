<?php

// Full error reporting
error_reporting(-1);
ini_set('display_errors', true);

define("OAUTH_CONSUMER_KEY", "customer001");
define("OAUTH_CONSUMER_SECRET", "be2283753051536681b432186aa546d5");
define("OAUTH_ACCESS_KEY", "81fda5b03a9588529cc0950ca8952b50");
define("OAUTH_ACCESS_SECRET", "a77a4d0468aa215574d92a9af691163d");

include_once "../library/oauth/OAuth.php";

$sig_method = new OAuthSignatureMethod_PLAINTEXT();

$url = "http://restapi.debian.virtualbox.local/decision";
$consumer = new OAuthConsumer(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET);
$access = new OAuthToken(OAUTH_ACCESS_KEY, OAUTH_ACCESS_SECRET);
$access_req = OauthRequest::from_consumer_and_token($consumer, $access, "GET", $url);
$access_req->sign_request($sig_method, $consumer, $access);

$header = $access_req->to_header("mainplus");
print $header;
print "<br>";

print $access_req->to_url();