<?php

class ji_request {
    protected $_resource;
    protected $_accept;
    protected $_host;
    protected $_parameters;
    protected $_method;
    protected $_url_elements;
    protected $_url;
    protected $_version;
    protected $_format;
    protected $_header;

    /**
     * Constructs a request. Can be populate form server variables if needed
     *
     * @param array|null $server_vars
     */
    function __construct($db, array $server_vars = null) {
        $this->db = $db;
        if (is_array($server_vars)) {
            $this->_populate($server_vars);
        }
    }

    function addHeader($k, $v) {
        $this->_headers[$k] = $v;
    }

    function getHeader($k, $default = null) {
        return isset($this->_headers[$k]) ? $this->_headers[$k] : $default;
    }

    /**
     * Populates the request. Accepts apache server variables
     *
     * @return ji_request
     */
    protected function _populate($server_vars) {
        // Collect headers
        $this->setMethod($server_vars['REQUEST_METHOD']);
        $this->setAccept($server_vars['HTTP_ACCEPT']);
        $this->setHost($server_vars['HTTP_HOST']);
        foreach ($server_vars as $k => $v) {
            if (preg_match("|^HTTP_|", $k)) $this->addHeader($k, $v);
        }

        $items = array();

        // collect URL info and sets the url elements.
        if(isset($server_vars['PATH_INFO'])) {
            $items = explode('/',$server_vars['PATH_INFO']);

            // Remove first (empty) item
            array_shift($items);
        }

            // Default defaults to default..
            if (count($items) == 0) $items[] = "default";

            // Map serialized url data into key value pairs
            $path = "";
            while (count($items)) {
                $k = array_shift($items);
                if (strlen($k) == 0) continue;

                $tmp = $this->_findMapping($k);
                if ($tmp === false) {
                    throw new Exception("Mapping not found");
                }

                if ($tmp[1] == MAP_RESOURCE) {
                    $this->addUrlElement($k, array_shift($items), $path);
                } else {
                    $this->addUrlElement($k, null, $path);
                }

                // Holds the "hierarchy" for finding deeper controllers
                $path .= $k."_";
            }

        // Parse query string and set (default) values
        parse_str($server_vars['QUERY_STRING'], $parameters);
        $parameters['resultsperpage'] = isset($parameters['resultsperpage']) ? $parameters['resultsperpage'] : 20;
        $parameters['page'] = isset($parameters['page']) ? $parameters['page'] : 1;
        $this->setParameters($parameters);
    }


    /**
     * @param  $name
     * @return bool
     */
    protected function _findMapping($name) {
        global $config;
        return isset ($config['mapping'][$name]) ? $config['mapping'][$name] : false;
    }


    /**
     * @param  $name
     * @return bool
     */
    public function addUrlElement($element, $resource, $path) {
        $this->_url_elements[] = array(
            'element' => $element,
            'resource' => $resource,
            'path' => $path
        );
    }


    /**
     * @param  $name
     * @return bool
     */
    public function getUrlElements() {
        return $this->_url_elements;
    }

    /**
     * Returns parent elements.
     *
     * Returns event/5 and talk/3 url element pairs from the url:
     *   /event/5/talk/3/comment/5
     *
     * @return array
     */
    public function getParentElements() {
        return array_splice($this->_url_elements, 0, count($this->_url_elements)-1);
    }

    /**
     * Returns main element or rest end-point if you will.
     *
     * Returns comment/5 url element pairs from the url:
     *   /event/5/talk/3/comment/5
     *
     * @return array
     */
    public function getMainElement() {
        return $this->_url_elements[count($this->_url_elements)-1];
    }

    public function setAccept($accept)
    {
        $this->_accept = $accept;
    }

    public function getAccept()
    {
        return $this->_accept;
    }

    public function setHost($host)
    {
        $this->_host = $host;
    }

    public function getHost()
    {
        return $this->_host;
    }

    public function setParameters($parameters)
    {
        $this->_parameters = $parameters;
    }

    public function getParameters()
    {
        return $this->_parameters;
    }

    public function setResource($resource)
    {
        $this->_resource = $resource;
    }

    public function isResource()
    {
        return ($this->getResource() !== null);
    }

    public function getResource() {
        $element = $this->getMainElement();
        return $element['resource'];
    }

    public function setMethod($method)
    {
        $this->_method = $method;
    }

    public function getMethod()
    {
        return $this->_method;
    }

    public function setUrl($url)
    {
        $this->_url = $url;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function setFormat($format)
    {
        $this->_format = $format;
    }

    public function getFormat()
    {
        return $this->_format;
    }

    public function setVersion($version)
    {
        $this->_version = $version;
    }

    public function getVersion()
    {
        return $this->_version;
    }

    public function handleMediaType() {
        $this->setVersion("1.0");
        $this->setFormat("json");
    }

    public function handleOauth() {
        // Default we aren't authenticated
        $this->authenticated = false;

/**
        // Find all authorization headers (we assume only 1 header exists)
        $authHeader = $this->getHeader('HTTP_AUTHORIZATION');
        if (! $authHeader) {
            print "Header not found";
            return;
        }

        // Parse headers into assoc array
        $oauthHeaders = array();
        foreach (explode(",", $authHeader) as $item) {
            if (! preg_match("|^(?:OAuth )?(.+)=([\"']?)([^\"]+)\\2$|i", $item, $matches)) {
                continue;
            }

            // Use trim on the key - some OAuth libraries are generous with spaces...
            $oauthHeaders[trim($matches[1])] = urldecode($matches[3]);
        }
 */

        // WE CHECK THE GET REQUEST, SHOULD BE HTTP_AUTHORIZATION_HEADERS
        $oauthHeaders = $_GET;


        // Check if all mandatory items exists
        $mandatoryItems = array (
            'oauth_version' => 1,
            'oauth_nonce' => 1,
            'oauth_timestamp' => 1,
            'oauth_consumer_key' => 1,
            'oauth_token' => 1,
            'oauth_signature_method' => 1,
            'oauth_signature' => 1);
        $tmp = array_diff_key($mandatoryItems, $oauthHeaders);
        if (count($tmp) > 0) {
            // Not all mandatory items are found. Exit (BAD REQUEST)
            print "Mandatory not found\n";
            return;
        }

        // If set, check for correct realm. Realm is not required by the OAuth spec
        if (isset($oauthHeaders['realm']) && $oauthHeaders['realm'] != "mainplus") {
            // Incorrect realm. Exit (BAD REQUEST);
            print "Incorrect realm\n";
            return;
        }

//        // Check for timestamp bandwidth
//        $delta = time() - $oauthHeaders['oauth_timestamp'];
//        if ($delta < (0 - $config->settings->oauth->timestamp->before) ||
//            $delta > ($config->settings->oauth->timestamp->after - 0)) {
//            // Not inside timestamp bandwith. Exit (UNAUTHORIZED)
//            return;
//        }


//        // Check if nonce for timestamp is already used, and save the nonce
//        $nonce = new General_Model_Oauthnonce_Entity();
//        $nonce = $nonce->findByNonce(
//            $oauthHeaders['oauth_consumer_key'],
//            $oauthHeaders['oauth_timestamp'],
//            $oauthHeaders['oauth_nonce']
//            );
//        if ($nonce instanceof General_Model_Oauthnonce_Entity) {
//            // Replay (attack). Nonce and timestamp already used
//            return $this->createError(
//                Idm_Constants::MSG_OAUTH_FAILURE_1,
//                Idm_Constants::HTTP_STATUS_UNAUTHORIZED);
//        }
//        $nonce = new General_Model_Oauthnonce_Entity();
//        $nonce->setNonce($oauthHeaders['oauth_nonce']);
//        $nonce->setTimestamp($oauthHeaders['oauth_timestamp']);
//        $nonce->setConsumerKey($oauthHeaders['oauth_consumer_key']);
//        $nonce->save($nonce);


//        // Fetch user-id from consumer_key/access_key
//        $oauthHeaders['oauth_consumer_key']);
//        if (! $website instanceof General_Model_Website_Entity) {
//            return $this->createError(
//                Idm_Constants::MSG_OAUTH_FAILURE_2,
//                Idm_Constants::HTTP_STATUS_UNAUTHORIZED);
//        }

        // Check if encryption method can be used
        $methods = explode(",", "PLAINTEXT,HMAC-SHA1");
        if (! in_array($oauthHeaders['oauth_signature_method'], $methods)) {
            // Exit, BAD REQUEST
            print "Incorrect signmethod\n";
            return;
        }


        // Find consumer-key access-token pair
        $result = $this->db->query("SELECT c.consumer_key, c.consumer_secret,
                                           a.access_token, a.access_secret,
                                           a.user_id
                                    FROM oauth_access_tokens AS a
                                    LEFT JOIN oauth_consumers AS c ON a.oauth_id = c.id
                                    WHERE a.access_token = '".$oauthHeaders['oauth_token']."' AND
                                          c.consumer_key = '".$oauthHeaders['oauth_consumer_key']."'");
        $row = $result ? $result->fetch_assoc() : null;
        if ($row == null) {
            // CONSUMER + ACCESS TOKEN NOT FOUND. Exit BAD REQUEST
            print "Customer + access not found \n";
            return;
        }

        // Validate signature
        $consumer = new OAuthConsumer($row['consumer_key'], $row['consumer_secret']);
        $access = new OAuthToken($row['access_token'], $row['access_secret']);
        switch ($oauthHeaders['oauth_signature_method'])
        {
            case 'HMAC-SHA1' :
                $signMethod = new OAuthSignatureMethod_HMAC_SHA1();
                break;
            case 'RSA-SHA1' :
                $signMethod = new OAuthSignatureMethod_RSA_SHA1();
                break;
            case 'PLAINTEXT' :
            default :
                $signMethod = new OAuthSignatureMethod_PLAINTEXT();
                break;
        }

        $uri = 'http://' . $this->getHost() . $this->getUrl();
        $requestMethod = OauthRequest::from_request($this->getMethod(), $uri);
        $signature = $oauthHeaders['oauth_signature'];
        $valid = $signMethod->check_signature($requestMethod, $consumer, $access, $signature);
        if (! $valid) {
            print "Signature invalid found \n";
            return;
        }


        // All is ok, set authentication information
        $this->authenticated = true;
        $this->authenticationinfo['user_id'] = $row['user_id'];
        $this->authenticationinfo['consumer_key'] = $row['consumer_key'];
        $this->authenticationinfo['access_token'] = $row['access_token'];
    }

} 