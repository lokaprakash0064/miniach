<?php
/**
 * Class file to clean user supplied data and
 * encrypt check for password etc
 * 
 * PHP version 8.0
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ CodeCanyon
 *
 * @category   Log
 * @package    TBF
 * @author     Kirti Kumar Nayak <admin@thebestfreelancer.in>
 * @copyright  2015 The Best Freelancer
 * @license    http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ CodeCanyon
 * @version    GIT: $Id$
 * @link       http://repo.thebestfreelancer.in
 * @since      File available since Release 1.0
 * @deprecated File deprecated in Release 2.0
 */

// Check if DataFilter class exists or not and define if not
if (!class_exists('Logger')) {

    /**
     * Class file to filter user requests or data submitted
     * 
     * PHP version 8.0
     *
     * @category  UserRegistration
     * @package   TBF
     * @author    Kirti Kumar Nayak <admin@thebestfreelancer.in>
     * @copyright 2015 The Best Freelancer
     * @license   http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ CodeCanyon
     * @version   Release: 3.0
     * @link      http://repo.thebestfreelancer.in
     * @since     Class available since Release 1.0
     */
    class Logger extends Exception
    {
        // {{{ properties

        /**
         * Private variable for route trace
         * 
         * @access private
         * @var mixed The Route of the error
         */
        private $_route;
        /**
         * Private variable for exception
         * 
         * @access private
         * @var string The exception details
         */
        private $_exception;
        /**
         * Private variable for filename
         * 
         * @access private
         * @var string The file name
         */
        private $_LogFileName;
        /**
         * Private static variable to hold singleton class object
         * 
         * @access    private
         * @staticvar
         * @var       object  The current class object
         */
        private static $_classObject;

        // }}}
        // {{{ __construct()

        /**
         * Default constructor class to initialize variables and page data.
         * According to singleton class costructor must be private
         * 
         * @return void
         * @access private
         */
        private function __construct() 
        {
            $this->_route = null;
            $this->_exception = null;
            $this->_LogFileName = 'errors_' . date('d-M-y') . '.log';
        }

        // }}}
        // {{{ getObject()

        /**
         * Method to return singleton class object.
         * returns current class object if already present
         * else creates one
         * 
         * @return object  The current class object
         * @access public
         * @static
         */
        public static function getObject() 
        {
            // check if class not instantiated
            if (self::$_classObject === null) {
                // then create a new instance
                self::$_classObject = new self();
            }
            // return the class object to be used
            return self::$_classObject;
        }

        // }}}
        // {{{ getLog()
        
        /**
         * Method to get the exception & log
         * 
         * @param string $msg Custom Description message
         * @return string Status of the log
         * @access public
         */
        public function putLog($msg = '') {
            if ($msg === '') {
                $this->_exception = date('Y-m-d H:i:s') . ' --> ' . self::getObject()->getTraceAsString() . PHP_EOL;
            } else {
                $this->_exception = $msg . PHP_EOL;
            }
            $this->writeLog($this->_exception);
        }
        
        // }}}
        // {{{ writeLog()
        
        /**
         * Method to write the exception & log
         * 
         * @param string $msg Custom Description message
         * @return string Status of the log
         * @access public
         */
        public function writeLog($msg) {
            if (!is_dir(LOGDIR)) {
                mkdir(LOGDIR, 0774);
            }
            if (!is_writable(LOGDIR)) {
                chmod(LOGDIR, 0774);
            }
            if (!file_exists(LOGDIR . DS . $this->_LogFileName) or !is_readable(LOGDIR . DS . $this->_LogFileName)) {
                touch(LOGDIR . DS . $this->_LogFileName);
            }
            $fh = fopen(LOGDIR . DS . $this->_LogFileName, 'a');
            fputs($fh, $msg . PHP_EOL);
            fclose($fh);
        }
        
        // }}}
    }
}