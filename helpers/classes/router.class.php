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
 * @category   Router
 * @package    TBF
 * @author     Kirti Kumar Nayak <admin@thebestfreelancer.in>
 * @copyright  2015 The Best Freelancer
 * @license    http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ CodeCanyon
 * @version    GIT: $Id$
 * @link       http://repo.thebestfreelancer.in
 * @since      File available since Release 4.0
 * @deprecated File deprecated in Release 2.0
 */

// Check if DataFilter class exists or not and define if not
if (!class_exists('Router')) {

    /**
     * Class file to filter user requests or data submitted
     * 
     * PHP version 8.0
     *
     * @category  Router
     * @package   TBF
     * @author    Kirti Kumar Nayak <admin@thebestfreelancer.in>
     * @copyright 2015 The Best Freelancer
     * @license   http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ CodeCanyon
     * @version   Release: 4.0
     * @link      http://repo.thebestfreelancer.in
     * @since     Class available since Release 1.0
     */
    class Router
    {
        // {{{ properties

        /**
         * Protected variable to store global $_GET data
         * 
         * @access protected
         * @var mixed The $_GET Global array
         */
        protected $_get;

        /**
         * Protected variable to store global $_POST data
         * 
         * @access protected
         * @var mixed The $_POST Global array
         */
        protected $_post;
        
        /**
         * Private variable to store cleaned request array exploded from $_GET data
         * 
         * @access private
         * @var mixed The analyzed $_GET array
         */
        private $_reqArray;

        /**
         * Public variable to store class name called
         * 
         * @access public
         * @var    string  The called class name
         */
        public $className;
        
        /**
         * Public variable to store method name called
         * 
         * @access public
         * @var    string  The called method name
         */
        public $methodName;

        /**
         * Protected variable to store class file name
         * 
         * @access protected
         * @var    string  The name of the class file
         */
        protected $_classFileName;
        
        /**
         * Protected variable to store class file directory path
         * 
         * @access protected
         * @var    string  The path of the class file
         */
        protected $_classDir;
        
        /**
         * Public variable to store breadcrumb string
         * 
         * @access public
         * @var    string  The HTML formatted breadcrumb string
         */
        public $breadCrumbStr;
        
        /**
         * Public variable to store breadcrumb array
         * 
         * @access public
         * @var    mixed  The breadcrumb array
         */
        public $breadCrumbArray;
        
        /**
         * Public variable to store breadcrumb array
         * 
         * @access    public
         * @var       mixed  The breadcrumb array to generate breadcrumb
         */
        public $bcArr;
        
        /**
         * Public variable to store page template as per language preferred
         * 
         * @access    public
         * @var       mixed  The array of language
         */
        public $pageLangTpl;
        
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
         * According to singleton class constructor must be private
         * 
         * @return void
         * @access private
         */
        private $_className = '';
        
        private function __construct() 
        {
            self::defaultAutoLoad();
            $this->_get = DataFilter::getObject()->cleanData($_GET);
            $this->_reqArray = [];
            $this->_post = DataFilter::getObject()->cleanData($_POST);
            $this->_classDir = __DIR__;
            $this->_classFileName = '';
            $this->bcArr = [];
            $this->breadCrumbArray = [];
            $this->breadCrumbStr = '';
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
        // {{{ defaultAutoLoad()

        /**
         * The following method automatically includes / requires the class file
         * as per the called class name. Please note that, the class file should
         * be named as classname.class.php and the class should be ClassName
         * 
         * @param string $className  The called class name
         * 
         * @return mixed Only generates the require_once line as per the class called
         * @access public
         */
        public static function defaultAutoLoad() 
        {
            spl_autoload_register(function ($className) {
                if (file_exists(__DIR__ . DS . strtolower($className) . '.class.php')) {
                    require_once __DIR__ . DS . strtolower($className) . '.class.php';
                } else {
                    PageCache::getObject()->cacheStatus = false;
                    PageCache::getObject()->createCache('404.html', ['ErrorCode' => '404 - Class File Not Found Error'], '404.html');
                    die('The required class file not found. Path:'
                        . __DIR__ . DS . strtolower($className) . '.class.php');
                }
            });
        }

        // }}}
        // {{{ disableDefaultAutoLoad()

        /**
         * The following method disables the registered default autoload
         * 
         * @return mixed Only disables the default autoload
         * @access public
         */
        public static function disableDefaultAutoLoad() 
        {
            // get all autoload functions as an array
            $functions = spl_autoload_functions();
            // unregister them in a loop
            foreach($functions as $function) {
                spl_autoload_unregister($function);
            }
        }

        // }}}
        // {{{ serveRequests()

        /**
         * The following method analyses and breaks $_GET via .htaccess file and
         * includes / requires the class file. Then calls the default / required
         * method. The first element of the array becomes class and next is method.
         * rest of the array used as the arguments of the method. The .htaccess
         * file should have the following lines:
         * #################################################################
         * <IfModule mod_rewrite.c>
         * Options +FollowSymLinks
         * RewriteEngine On
         * RewriteRule ^helpers - [L]
         * RewriteCond %{SCRIPT_FILENAME} !-f
         * RewriteCond %{SCRIPT_FILENAME} !-d
         * RewriteRule ^(.*)$ index.php?action=$1 [PT,L]
         * </IfModule>
         * #################################################################
         * 
         * @return mixed Calls the method via the class object
         * @access public
         */
        public function serveRequests() 
        {
            HitCount::getObject()->recordHits();
            if (isset($this->_get['action']) and !empty($this->_get['action'])) {
                // clean extra slashes
                $this->_get['action'] = trim($this->_get['action'], '/');
                // get parts by exploding
                $this->_reqArray = explode('/', $this->_get['action']);
                // check if array empty
                try {
                    switch (count($this->_reqArray)) {
                        case 0:
                            Menu::getObject()->showDefault();
                            break;
                        case 1:
                            Menu::getObject()->showPage($this->_reqArray[0]);
                            break;
                        case 2:
                            $this->className = DataFilter::getObject()->convertStrCap($this->_reqArray[0]);
                            $this->methodName = DataFilter::getObject()->convertStrCamel($this->_reqArray[1]);
                            $this->className::getObject()->{$this->methodName}();
                            break;
                        default :
                            $this->className = DataFilter::getObject()->convertStrCap($this->_reqArray[0]);
                            $this->methodName = DataFilter::getObject()->convertStrCamel($this->_reqArray[1]);
                            $args = array_slice($this->_reqArray, 2);
                            $this->className::getObject()->{$this->methodName}($args);
                            break;
                    }
                } catch (Exception $ex) {
                    Logger::getObject()->putLog($ex->getTraceAsString());
                    PageCache::getObject()->createCache('404.html', ['ErrorCode' => '4041 - Method in Class Not Found Error'], '404.html');
                }
            } else {
                // else call the default function
                Menu::getObject()->showDefault();
            }
        }

        // }}}
        // {{{ downloadFile()

        /**
         * Downloads the specified file by its name and path details
         *
         * @param string $fileName The name of the file, required
         * @param string $filePath The fully qualified path of the file, default UPLOAD_DIR
         * @param string $displayFileName The name of the file to be renamed if required
         * @return mixed  The file headers to download
         * @access public
         */
        public function downloadFile($fileName, $filePath = UPLOAD_DIR, $displayFileName  = '') 
        {
            if (empty($fileName)) {
                $_SESSION['MSG'] = 'File name not supplied!';
                $_SESSION['STATUS'] = 'error';
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit;
            }
            if (empty($displayFileName)) {
                $displayFileName = $fileName;
            }
            if (file_exists($filePath . DS . $fileName) and is_file($filePath . DS . $fileName)) {
                header('Content-Disposition: attachment; filename="' . $displayFileName . '"');   
                header('Content-Type: application/octet-stream');
                header('Content-Type: application/download');
                header('Content-Description: File Transfer');            
                header('Content-Length: ' . filesize($filePath . DS . $fileName));
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public'); 
                header('Accept-Ranges: bytes');
                header('Content-Transfer-Encoding: binary' . "\n");
                flush(); // this doesn't really matter.
                $fp = fopen($filePath . DS . $fileName, "rb");
                while (!feof($fp)) {
                    echo fread($fp, 65536);
                    flush(); // this is essential for large downloads
                } 
                fclose($fp); 
                readfile($filePath . DS . $fileName);
                //unlink($file);
            } else {
                $_SESSION['MSG'] = 'Requested File not available!';
                $_SESSION['STATUS'] = 'error';
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }

        // }}}
        // {{{ __clone()

        /**
         * According to singleton pattern instance, cloning is prohibited
         *
         * @return string  A message that states, cloning is prohibited
         * @access public
         */
        private function __clone() 
        {
            // only set an error message
            die('Cloning is prohibited for singleton instance.');
        }

        // }}}
    }
}