<?php

class ji_request {
    protected $_resource;
    protected $_accept;
    protected $_host;
    protected $_parameters;
    protected $_verb;
    protected $_url_elements;

    /**
     * Constructs a request. Can be populate form server variables if needed
     *
     * @param array|null $server_vars
     */
    function __construct(array $server_vars = null) {
        if (is_array($server_vars)) {
            $this->_populate($server_vars);
        }
    }

    /**
     * Populates the request. Accepts apache server variables
     *
     * @return ji_request
     */
    protected function _populate($server_vars) {
        // Collect headers
        $this->setVerb($server_vars['REQUEST_METHOD']);
        $this->setAccept($server_vars['HTTP_ACCEPT']);
        $this->setHost($server_vars['HTTP_HOST']);

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

    public function setVerb($verb)
    {
        $this->_verb = $verb;
    }

    public function getVerb()
    {
        return $this->_verb;
    }

} 