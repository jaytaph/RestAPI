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

        // collect URL info and sets the url elements.
        if(isset($server_vars['PATH_INFO'])) {
            $items = explode('/',$server_vars['PATH_INFO']);
            array_shift($items);

            // Default default to default..
            if (count($items) == 0) $items[] = "default";

            /* Resources have an even number of items on the path. Collections don't:
             * Resource: /event/3/talk/5
             * Collection: /event/4/talks
             */
            $this->setResource((count($items) & 1) != 1);

            // Map serialized url data into key value pairs
            $path = "";
            while (count($items)) {
                $k = array_shift($items);
                if (strlen($k) == 0) continue;
                $this->addUrlElement($k, array_shift($items), $path);

                // Holds the "hiearchy" for finding deeper controllers
                $path .= $k."_";
            }
        }

        // Parse query string and set (default) values
        parse_str($server_vars['QUERY_STRING'], $parameters);
        $parameters['resultsperpage'] = isset($parameters['resultsperpage']) ? $parameters['resultsperpage'] : 20;
        $parameters['page'] = isset($parameters['page']) ? $parameters['page'] : 1;
        $this->setParameters($parameters);
    }


    public function addUrlElement($element, $resource, $path) {
        $this->_url_elements[] = array(
            'element' => $element,
            'resource' => $resource,
            'path' => $path
        );
    }

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
        return $this->_resource;
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