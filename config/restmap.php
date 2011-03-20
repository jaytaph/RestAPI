<?php

define ("MAP_COLLECTION", true);
define ("MAP_RESOURCE", false);

$config['mapping'] = array(
        'event' => array('event', MAP_RESOURCE),
        'events' => array('event', MAP_COLLECTION),
        'talk' => array('talk', MAP_RESOURCE),
        'talks' => array('talk', MAP_COLLECTION),
        'user' => array('user', MAP_RESOURCE),
        'users' => array('user', MAP_COLLECTION),
        'comment' => array('comment', MAP_RESOURCE),
        'comments' => array('comment', MAP_COLLECTION),
        'default' => array('default', MAP_RESOURCE),
    );