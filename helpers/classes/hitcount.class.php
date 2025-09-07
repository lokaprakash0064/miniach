<?php

/**
 * Class file to handle page hit counts
 *
 * PHP version 8.2
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   HitCount
 * @package    TBF
 * @author     Lokaprakash Behera <admin@thebestfreelancer.in>
 * @copyright  2015 The Best Freelancer
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    GIT: $Id$
 * @link       http://repo.thebestfreelancer.in
 * @since      File available since Release 1.0
 * @deprecated File deprecated in Release 1.0
 */

// Check if HitCount class exists or not and define if not
if (!class_exists('HitCount')) {
    
    /**
     * Class to handle page hit counts
     *
     * PHP version 8.2
     *
     * @category   HitCount
     * @package    TBF
     * @author     Lokaprakash Behera <admin@thebestfreelancer.in>
     * @copyright  2015 The Best Freelancer
     * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
     * @version    Release: @package_version@
     * @link       http://repo.thebestfreelancer.in
     * @since      Class available since Release 1.0
     * @deprecated Class deprecated in Release 1.0
     */

    class HitCount
    {
        // {{{ properties
        
        /**
         * To store URL requested
         *
         * @access public
         * @var    string    The Requested URL via browser
         */
        public $url;
        
        /**
         * To store users IP
         *
         * @access public
         * @var    string    The IP of the user
         */
        public $userIP;
        
        /**
         * To store total hit numbers
         *
         * @access public
         * @var    int    The total number of hits
         */
        public $totalHits;
        
        /**
         * To store page hit numbers
         *
         * @access public
         * @var    int    The page hits count
         */
        public $pageHits;

        /**
         * To store class instance object
         *
         * @access private
         * @var    object  The current class singleton object
         * @static
         */
        private static $_classObject;

        // }}}
        // {{{ __construct()
        
        /**
         * Default constructor class to initialize variables and page data.
         * According to singleton class costructor must be private
         *
         * @category Constructor
         * @return   void
         * @access   private
         */
        private function __construct()
        {
            // initialize all
            $this->url = $_SERVER['REQUEST_URI'];
            $this->totalHits = 0;
            $this->pageHits = 0;
            $this->userIP = null;
        }

        // }}}
        // {{{ getObject()

        /**
         * Here we are trying to get a singleton instance of this class
         * it's very much useful for enhanced speed and low overhead
         * on database connections and variable memory allocations
         * $classObject is a private variable to be used in getObject()
         * to be returned on call as one and only instance of the class
         * getInstance uses this to get a pre initialized instance
         * or new instance if not initializes of the class
         * and through that we can get all other methods called
         * easily and friendly for usage and no need to remember
         *
         * @access   public
         * @static
         * @category instance
         * @return   object   The current class object / instance
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
        // {{{ runQuery()
        
        /**
         * This function returns the number of rows affected by the SQL else false and error
         *
         * @param string $url The URL supplied / requested
         *
         * @access   public
         * @category model
         * @return   int Total pages hit count
         */
        public function getTotalHits()
        {
            $sql = 'select sum(hh_count) as totalCount from hit_history';
            $data = DbOperations::getObject()->fetchData($sql);
            return $this->totalHits + (int) $data[0]['totalCount'];
        }

        // }}}
        // {{{ getHits()
        
        /**
         * Get hit count for a specific page
         *
         * @param string|null $url The URL to check
         *
         * @access   public
         * @category model
         * @return int Page hit count
         */
        public function getHits($url = null)
        {
            if (empty($url)) {
                $url = $_SERVER['REQUEST_URI'];
            }

            $sql = 'select sum(hh_count) as pageCount from hit_history where hh_url = ?';
            $data = DbOperations::getObject()->fetchData($sql, [$url]);

            return (int) ($data[0]['pageCount'] ?? 0);
        }

        // }}}
        // {{{ recordHits()

        /**
         * This method to be used to store / track page hits
         *
         * @param string $url The URL requested
         *
         * @access   public
         * @category model
         */
        public function recordHits($url = null, $userIP = null)
        {
            $this->url = $url;
            if (empty($this->url)) {
                $this->url = $_SERVER['REQUEST_URI'];
            }
            $this->userIP = $userIP;
            if (empty($this->userIP)) {
                $this->userIP = $_SERVER['REMOTE_ADDR'];
            }
            // get details of the IP and date of visit
            $sql = 'select hhid, hh_count'
                . ' from hit_history where hh_ip = ?'
                . ' and hh_url = ? and date(hh_dttm) = ?';
            DbOperations::getObject()->transaction('start');
            $prep = DbOperations::getObject()->prepareQuery($sql);
            $data = DbOperations::getObject()->fetchData($sql, [$this->userIP, $this->url, DBDATE], false, $prep);
            $this->pageHits = (isset($data[0]) ? intval($data[0]['hh_count']) : 0);
            if ($this->pageHits > 0) {
                $prep = DbOperations::getObject()->buildUpdateQuery('hit_history', ['hh_count'], ['hhid']);
                $suc = DbOperations::getObject()->runQuery([($this->pageHits+1), $data[0]['hhid']], $prep);
            } else {
                $prep = DbOperations::getObject()->buildInsertQuery('hit_history');
                $suc = DbOperations::getObject()->runQuery([null, $this->userIP, $this->url, DBTIMESTAMP, 1], $prep);
            }
            if ($suc !== false) {
                DbOperations::getObject()->transaction('on');
                return true;
            } else {
                DbOperations::getObject()->transaction('off');
                return false;
            }
        }

        // }}}
        
        // }}}
        // {{{ __destruct()
        
        /**
         * The default destructor of the class
         *
         * @access public
         * @return void Only destroys variables, nothing returned
         */
        public function __destruct()
        {
            $this->pageHits = 0;
            $this->totalHits = 0;
            $this->url = null;
            $this->userIP = null;
        }
        
        // }}}
        // {{{ __clone()
      
        /**
         * According to singleton instance, cloning is prohibited
         *
         * @access   private
         * @category cloning
         * @return   void  Nothing as it only shows a warning when tried to clone
         */
        private function __clone()
        {
            /*
             * only set an error message
             */
            die('Cloning is prohibited for singleton instance.');
        }
        // }}}
    }
}
