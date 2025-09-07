<?php

/**
 * Class file to handle captcha image requests
 *
 * LICENSE:
 *
 * This source file is subject to the licensing terms that
 * is available through the world-wide-web at the following URI:
 * http://codecanyon.net/wiki/support/legal-terms/licensing-terms/.
 *
 * PHP version >= 7.2
 *
 * @category  Cache
 * @package   TBF
 * @author    Kirti Kumar Nayak, India <thebestfreelancer.in@gmail.com>
 * @copyright 2013 TheBestFreelancer,
 * @license   https://thebestfreelancer.in/ TBF
 * @version   Release 1.0
 * @link      http://demos.thebestfreelancer.in/
 * @tutorial  http://demos.thebestfreelancer.in/
 */
/**
 * Check if Contact class exists or not and define if not
 */
if (!class_exists('PageCache')) {

    /**
     * Class file to handle page output cache
     *
     * This is a singleton pattern class and can be called via static methods
     *
     * @category  Cache
     * @author    Kirti Kumar Nayak, India <admin@thebestfreelancer.in>
     * @copyright 2013 TheBestFreelancer,
     * @license   https://thebestfreelancer.in/ TBF
     * @version   Release 1.0
     * @link      http://demos.thebestfreelancer.in/
     * @tutorial  http://demos.thebestfreelancer.in/
     */
    class PageCache
    {
        // {{{ properties

        /**
         * private variable for template filename
         *
         * @access private
         * @var string  The template filename string
         */
        private $_tplFile;
        
        /**
         * private variable for template contents
         *
         * @access private
         * @var string  The template file contents string
         */
        private $_tplContents;
        
        /**
         * private variable for final page contents
         *
         * @access private
         * @var string  The final page contents string
         */
        private $_finalContents;
        
        /**
         * private variable for formatted alert messages
         *
         * @access private
         * @var string  The formatted alert messages
         */
        private $_formattedMsg;
        
        /**
         * private variable for appending version to new CSS or JS files
         *
         * @access private
         * @var string  The appending version to new CSS or JS files
         */
        private $_version;

        /**
         * Public variable to store cache status whether on or off
         *
         * @access public
         * @var string  The cache status
         */
        public $cacheStatus;

        /**
         * Private variable to store cache path
         *
         * @access private
         * @var string  The cache file path
         */
        private $_cachePath;

        /**
         * Public variable to store the page replaceable data
         *
         * @access public
         * @var string  The replaceable data of page by placeholder
         */
        public $replaceData;
        
        /**
         * Public variable to store navigation active / inactive data
         *
         * @access public
         * @var mixed The active / inactive menu links
         */
        public $navActive;
        
        /**
         * private static variable to hold class object
         *
         * @access private
         * @staticvar
         * @var object  The current class object
         */
        private static $_classObject;

        // }}}
        // {{{ __construct()

        /**
         * Default constructor class to initialize variables and page data.
         * According to singleton class constructor must be private
         *
         * @return void
         * @access  private
         */
        private function __construct()
        {
            // set cache data
            $this->cacheStatus = CACHE_STATUS;
            $this->_cachePath = CACHE_DIR;
            // set replacement data array
            $this->replaceData = [
                'FaviconPath' => ACCESS_URL . 'helpers/images/ico/',
                'ImgPath' => ACCESS_URL . 'helpers/images/',
                'FilePath' => ACCESS_URL . 'helpers/files/',
                'AbsUrl' => ACCESS_URL,
                'UTYPE' => (isset($_SESSION['UTYPE']) ? $_SESSION['UTYPE'] : 0),
                'CurYr' => date('Y'),
                'CSSHelpers' => '',
                'JSHelpers' => '',
                'VisitCount' => HitCount::getObject()->getHits()
            ];
            // set navigation links
            $this->navActive = [
                'HomeActive', 'AboutActive', 'subActive', 'discActive', 'privActive', 'cntActive'
            ];
            // set template file data
            $this->_tplContents = '';
            $this->_finalContents = '';
            $this->_version = '?v=1.23';
            $this->_formattedMsg = '';
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
            //  check if class not instantiated
            if (self::$_classObject === null) {
                //  then create a new instance
                self::$_classObject = new self();
            }
            //  return the class object to be used
            return self::$_classObject;
        }

        // }}}
        // {{{ assignTemplate()

        /**
         * function to check if user is logged in or not
         * and to redirect to the specific page if so
         *
         * @param mixed $replacementArray The associative array to be replaced against defined keys
         *
         * @package EMSI
         * @author Kirti Kumar Nayak <admin@thebestfreelancer.in>
         * @access public
         * @category CommonFunction
         * @return void The modified data assigned to the class variables
         */
        public function assignTemplate($replacementArray, $templateFileName = '')
        {
            // require the template for respective user to show the design
            if ($templateFileName === '') {
                $this->_tplFile = TPL_DIR . DS . 'template.html';
            } else {
                $this->_tplFile = TPL_DIR . DS . $templateFileName;
            }
            // check if the template exists else give out an error
            if (!file_exists($this->_tplFile)) {
                die('The required template : ' . $this->_tplFile . ' could not be found.');
            }
            // assign the replacement data into class variable
            foreach ($replacementArray as $placeholder => $replacement) {
                $this->replaceData[$placeholder] = $replacement;
            }
            $this->replaceData['CSSHelpers'] = '';
            $this->replaceData['JSHelpers'] = '';
            // get the template contents
            $this->_tplContents = file_get_contents($this->_tplFile);
            // convert the css files array into a css link string
            if (array_key_exists('CSSHelpers', $replacementArray)) {
                if (count($replacementArray['CSSHelpers']) > 0) {
                    foreach ($replacementArray['CSSHelpers'] as $key => $value) {
                        // set version info
                        $this->_version = filemtime(DIRPATH . DS . 'helpers' . DS . 'css' . DS . $value);
                        $this->replaceData['CSSHelpers'] .= '<link href="'
                            . ACCESS_URL . 'helpers/css/' . $value . '?v=' . $this->_version
                            . '" rel="stylesheet" type="text/css" media="all">';
                    }
                }
            }
            // convert the js files array into a css link string
            if (array_key_exists('JSHelpers', $replacementArray)) {
                if (count($replacementArray['JSHelpers']) > 0) {
                    foreach ($replacementArray['JSHelpers'] as $key => $value) {
                        // set version info
                        $this->_version = filemtime(DIRPATH . DS . 'helpers' . DS . 'js' . DS . $value);
                        $this->replaceData['JSHelpers'] .= '<script src="'
                            . ACCESS_URL . 'helpers/js/' . $value . '?v='
                            . $this->_version. '"></script>';
                    }
                }
            }
            foreach ($this->navActive as $linkAct) {
                if (array_key_exists($linkAct, $replacementArray)) {
                    $this->replaceData[$linkAct] = 'active';
                } else {
                    $this->replaceData[$linkAct] = '';
                }
            }
        }

        // }}}
        // {{{ clearCache()

        /**
         * function to clean all cached files
         *
         * @package TBF
         * @author Kirti Kumar Nayak <admin@thebestfreelancer.in>
         * @access public
         * @category CommonFunction
         * @return void The modified data assigned to the class variables
         */
        public function clearCache()
        {
            $cacheFiles = scandir(CACHE_DIR);
            foreach ($cacheFiles as $file) {
                if (($file !== '.') or ($file !== '..')) {
                    if (file_exists(CACHE_DIR . DS . $file)) {
                        chmod(CACHE_DIR . DS . $file, 0777);
                        unlink(CACHE_DIR . DS . $file);
                    }
                }
            }
        }

        // }}}
        // {{{ replaceHolder()

        /**
         * function to replace the placeholders as set in template file
         *
         * @package Pause for The Truth
         * @author Kirti Kumar Nayak <admin@thebestfreelancer.in>
         * @access public
         * @category CommonFunction
         * @return void prints the data as per passed
         */
        public function replaceHolder()
        {
            // replace placeholders in template contents
            foreach ($this->replaceData as $key => $value) {
                $this->_tplContents = preg_replace('/{' . $key . '}/', $value, $this->_tplContents);
            }
            foreach ($this->replaceData as $key => $value) {
                $this->_tplContents = preg_replace('/{' . $key . '}/', $value, $this->_tplContents);
            }
            // remove whitespaces to compress the contents
            $this->_finalContents = str_replace(
                ['   ', '    ', "\r", "\n", "\r\n", "\n\r"],
                '',
                $this->_tplContents
            );
            $this->_finalContents = str_replace(
                ['> <', '>  <', '>   <'],
                '><',
                $this->_finalContents
            );
            return $this->_finalContents;
        }

        // }}}
        // {{{ getAlertMsg()

        /**
         * Function to get messages from session and format
         * the message as per Bootstrap design standard.
         * If you want any other type of message you can customize accordingly
         *
         * @package RegistrationSystem
         * @author Kirti Kumar Nayak <admin@thebestfreelancer.in>
         * @access public
         * @category CommonFunction
         * @return string The HTML formatted notification string
         */
        public function getAlertMsg()
        {
            // check if session has been set
            if (isset($_SESSION['STATUS']) and isset($_SESSION['MSG'])) {
                /**
                 * if set initialize a variable to store the html formatted string
                 * @access private
                 * @var string The HTML formatted string
                 */
                switch ($_SESSION['STATUS']) {
                    case 'error':
                        // if status is error then format with related style and so on
                        $this->_formattedMsg = '<div class="alert alert-danger alert-dismissable fade show" role="alert">'
                            . $_SESSION['MSG']
                            . '<button type="button" class="btn-close" data-bs-dismiss="alert" '
                            . 'aria-label="Close"></button></div>';
                        break;
                    case 'warning':
                        $this->_formattedMsg = '<div class="alert alert-warning alert-dismissible fade show" role="alert">'
                            . $_SESSION['MSG']
                            . '<button type="button" class="btn-close" data-bs-dismiss="alert" '
                            . 'aria-label="Close"></button></div>';
                        break;
                    case 'success':
                        $this->_formattedMsg = '<div class="alert alert-success alert-dismissable fade show" role="alert">'
                            . $_SESSION['MSG']
                            . '<button type="button" class="btn-close" data-bs-dismiss="alert" '
                            . 'aria-label="Close"></button></div>';
                        break;
                    case 'primary':
                        $this->_formattedMsg = '<div class="alert alert-primary alert-dismissable fade show" role="alert">'
                            . $_SESSION['MSG']
                            . '<button type="button" class="btn-close" data-bs-dismiss="alert" '
                            . 'aria-label="Close"></button></div>';
                        break;

                    default:
                        $this->_formattedMsg = '<div class="alert alert-info alert-dismissable fade show" role="alert">'
                            . $_SESSION['MSG']
                            . '<button type="button" class="btn-close" data-bs-dismiss="alert" '
                            . 'aria-label="Close"></button></div>';
                        break;
                }
                // at last unset the seeions set
                unset($_SESSION['STATUS']);
                unset($_SESSION['MSG']);
                // return the formatted string
                return $this->_formattedMsg;
            }
            return false;
        }

        // }}}
        // {{{ isCached()

        /**
         * Check if supplied url cache present
         *
         * @param string $url The URL of the requested cache
         * @return boolean  The status of cache file
         * @access public
         */
        public function isCached($url)
        {
            $this->_cachePath = CACHE_DIR . DS . base64_encode($url) . '.html';
            // check if cache status is on, cache file present and modification
            // time is not more than spectified cache lifetime
            if (file_exists($this->_cachePath)
                and (filemtime($this->_cachePath) > (time() - CACHE_LIFETIME))) {
                return true;
            } else {
                return false;
            }
        }

        // }}}
        // {{{ getCache()

        /**
         * Check if supplied url cache present, else create one and return
         *
         * @param string $urlChunk The URL of the requested cache
         * @param string $tplName The name of the template file
         * @return string  The data from cache file
         * @access public
         */
        public function getCache($urlChunk, $tplName = 'template.html')
        {
            if ($this->cacheStatus) {
                if ($this->isCached($urlChunk)) {
                    $this->_tplContents = file_get_contents($this->_cachePath);
                    $this->replaceData['ErrMsgs'] = $this->getAlertMsg();
                    $this->replaceData['VisitCount'] = HitCount::getObject()->getHits();
                    die($this->replaceHolder());
                } else {
                    $this->createCache($urlChunk, $this->getPageData($urlChunk), $tplName);
                }
            } else {
                $this->replaceData = $this->getPageData($urlChunk);
                $this->replaceData['ErrMsgs'] = $this->getAlertMsg();
                $this->replaceData['VisitCount'] = HitCount::getObject()->getHits();
                $this->assignTemplate($this->replaceData, $tplName);
                die($this->replaceHolder());
            }
        }
        
        // }}}
        // {{{ createCache()
        
        /**
         * Creates cache file for faster content delivery
         *
         * @param string $url The URL of the request
         * @param mixed $replacementArray The replacement array for the placeholders
         * @param string $tplName The template file name
         *
         * @return void The content of the file
         */
        public function createCache($url, $replacementArray, $tplName = 'template.html')
        {
            //var_dump($url);exit;
            /*if (count($replacementArray) > 0) {
                $this->assignTemplate($replacementArray, $tplName);
            }*/
            if ($this->isCached($url)) {
                $this->_tplContents = file_get_contents($this->_cachePath);
                $this->replaceData['ErrMsgs'] = $this->getAlertMsg();
                $this->replaceData['VisitCount'] = HitCount::getObject()->getHits();
                die($this->replaceHolder());
            }
            $this->_cachePath = CACHE_DIR . DS . base64_encode($url) . '.html';
            $this->assignTemplate($replacementArray, $tplName);
            if ($this->cacheStatus) {
                file_put_contents($this->_cachePath, $this->replaceHolder());
                touch($this->_cachePath);
            }
            $this->replaceData['ErrMsgs'] = $this->getAlertMsg();
            $this->replaceData['VisitCount'] = HitCount::getObject()->getHits();
            die($this->replaceHolder());
        }
        
        // }}}
        // {{{ __clone()

        /**
         * According to singleton pattern instance, cloning is prohibited
         *
         * @return string  A message that states, cloning is prohibited
         * @access private
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
