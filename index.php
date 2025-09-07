<?php

/**
 * This is the common responder file to respond public requests all over the project
 * and it contains the required actions with request data that will be commonly used
 * New functionality and actions can be added using class files
 *
 * @author Lokaprakash Behera <lokaprakash.behera@gmail.com>
 * @version Build 1.0
 * @package Odisha Vacation CRM
 * @copyright (c) 2022, Odisha Vacation
 * @outputBuffering enabled
 */
// common include file required
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'include.php';
// require request resolver router class
require_once DIRPATH . DS . 'helpers' . DS . 'classes' . DS . 'router.class.php';
try {
    // call the request serving methods
    Router::getObject()->serveRequests();
} catch (Exception $ex) {
    die('Error: ' . $ex->getTraceAsString());
}
