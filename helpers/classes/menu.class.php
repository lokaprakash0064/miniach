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
 * @category   Menu
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
if (!class_exists('Menu')) {

    /**
     * Class file to filter user requests or data submitted
     * 
     * PHP version 8.0
     *
     * @category  Menu
     * @package   TBF
     * @author    Kirti Kumar Nayak <admin@thebestfreelancer.in>
     * @copyright 2015 The Best Freelancer
     * @license   http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ CodeCanyon
     * @version   Release: 4.0
     * @link      http://repo.thebestfreelancer.in
     * @since     Class available since Release 1.0
     */
    class Menu extends Router
    {
        // {{{ properties

        /**
         * Private static variable to hold singleton class object
         * 
         * @access    private
         * @staticvar
         * @var       object  The current class object
         */
        private static $_classObject;
        
        /**
         * Public variable to store page results if any
         * 
         * @access    public
         * @var       string  The resulting page / html data string
         */
        public $pageRes;
        
        /**
         * Public variable to store menu results if any
         * 
         * @access    public
         * @var       string  The resulting menu details string
         */
        public $menuRes;
        
        /**
         * Public variable to store submenu results if any
         * 
         * @access    public
         * @var       string  The resulting submenu details string
         */
        public $subMenuRes;
        
        /**
         * Public variable to store menu results if any
         * 
         * @access    public
         * @var       string  The resulting menu HTML markup string
         */
        public $menuHtmlStr;
        
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
         * Public variable to store breadcrumb element position
         * 
         * @access public
         * @var    int  The breadcrumb array element position
         */
        public $breadCrumbRes;

        /**
         * Public variable to store breadcrumb array
         * 
         * @access    public
         * @var       mixed  The breadcrumb array to generate breadcrumb
         */
        public $bcArr;
        
        // }}}
        // {{{ __construct()

        /**
         * Default constructor class to initialize variables and page data.
         * According to singleton class constructor must be private
         * 
         * @return void
         * @access private
         */
        private function __construct() 
        {
            
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
        // {{{ showDefault()

        /**
         * The following method shows default home page
         * 
         * @return mixed Calls the method via the class object
         * @access public
         */
        public function showDefault() 
        {
            $this->showPage('index' . FILEEXT);
        }

        // }}}
        // {{{ showPage()

        /**
         * The following method shows default home page
         * 
         * @param string $pageUrl The name of the page requested
         * @return mixed Returns / shows the page data
         * @access public
         */
        public function showPage($pageUrl) 
        {
            // act as per request, cases for special jobs, default for static pages
            switch ($pageUrl) {
                case 'captcha.jpg':
                    Captcha::getObject()->createCaptcha();
                    break;
                case 'sitemap.xml':
                    $this->createSitemap();
                    break;
                case 'admin-home' . FILEEXT:
                    if (User::getObject()->isLogged() === false) {
                        $_SESSION['STATUS'] = 'error';
                        $_SESSION['MSG'] = 'It seems your Session has been timed out. Please login again';
                        header('Location:' . ACCESS_URL . 'index' . FILEEXT);
                        exit;
                    }
                    $replaceData = [
                        'PageTitle' => 'Admin Home | Odisha Vacation',
                        'MetaDesc' => '',
                        'MetaKeys' => '',
                        'CSSHelpers' => [],
                        'JSHelpers' => ['custom.js'],
                        'Contents' => file_get_contents(PGS_DIR . DS . 'admin-home.html')
                    ];
                    PageCache::getObject()->createCache(ACCESS_URL . 'admin-home', $replaceData, 'loginTpl.html');
                    break;
                case 'user-logout' . FILEEXT:
                case 'logout' . FILEEXT:
                case 'logout':
                    session_destroy();
                    session_start();
                    $_SESSION['STATUS'] = 'info';
                    $_SESSION['MSG'] = 'Successfully Logged Out';
                    session_write_close();
                    header('Location:' . ACCESS_URL);
                    break;
                case 'about-us':
                    $replaceData = [
                        'PageTitle' => 'About Us | Miniach Solution',
                        'AboutActive' => 'active',
                        'MetaDesc' => '',
                        'MetaKeys' => '',
                        'CSSHelpers' => ['style.css'],
                        'JSHelpers' => ['custom.js'],
                        'Contents' => file_get_contents(PGS_DIR . DS . 'about.html')
                    ];
                    PageCache::getObject()->createCache(ACCESS_URL . 'home', $replaceData, 'innerTpl.html');
                    break;
                default:
                    $replaceData = [
                        'PageTitle' => 'Home | Miniach Solution',
                        'HomeActive' => 'active',
                        'MetaDesc' => '',
                        'MetaKeys' => '',
                        'CSSHelpers' => ['style.css'],
                        'JSHelpers' => ['custom.js'],
                        'Contents' => file_get_contents(PGS_DIR . DS . 'home.html')
                    ];
                    PageCache::getObject()->createCache(ACCESS_URL . 'home', $replaceData, 'template.html');
                    break;
            }
        }

        // }}}
        // {{{ createSitemap()

        /**
         * Create a sitemap for the site as per menu / link created
         *
         * @return string  A message that states, cloning is prohibited
         * @access public
         */
        public function createSitemap() 
        {
            $sql = 'select pgcreated, murl from pages left join menu on mid = mid_fk '
                . 'where mactive = 1 and mtarget <> "external" and pgsitemap = 1'
                . ' order by mposit, mid';
            $data = DbOperations::getObject()->fetchData($sql);
            //var_dump($data);exit;
            $xml = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
            $xml .= '<?xml-stylesheet type="text/xsl" href="sitemap.xsl"?>'.PHP_EOL;
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'.PHP_EOL;
            $xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'.PHP_EOL;
            $xml .= 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9'.PHP_EOL;
            $xml .= 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'.PHP_EOL;
            foreach ($data as $url) {
                $xml .= '<url>'.PHP_EOL;
                $xml .= '<loc>'.ACCESS_URL.$url['murl'] . FILEEXT .'</loc>'.PHP_EOL;
                $xml .= '<lastmod>'.gmdate(DATE_W3C, strtotime($url['pgcreated'])).'</lastmod>'.PHP_EOL;
                $xml .= '<priority>0.8</priority>'.PHP_EOL;
                $xml .= '<changefreq>daily</changefreq>'.PHP_EOL;
                $xml .= '</url>'.PHP_EOL;
            }
            // for pdf brochure
            $xml .= '<url>'.PHP_EOL;
            $xml .= '<loc>'.ACCESS_URL.'download-format.py</loc>'.PHP_EOL;
            $xml .= '<lastmod>'.gmdate(DATE_W3C, filemtime(UPLOAD_DIR . DS . 'Format.docx')).'</lastmod>'.PHP_EOL;
            $xml .= '<priority>0.8</priority>'.PHP_EOL;
            $xml .= '<changefreq>monthly</changefreq>'.PHP_EOL;
            $xml .= '</url>'.PHP_EOL;
            $xml .= '</urlset>';
            header('Content-Type: application/xml; charset=utf-8');
            die($xml);
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