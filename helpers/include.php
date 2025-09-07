<?php

/**
 * This is the common configuration file to be included all over the project
 * and it contains the required constants and variables that will be commonly used
 * New functionality and classes can be added via class files but this file should
 * remain intact if possible.
 *
 * @version Build 1.0
 * @outputBuffering disabled
 */
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_start();
/*
 * set the error handler in ALL, DEPRICATED & STRICT mode
 * so that no errors (not even syntactical) can be tolerated
 */
error_reporting(E_ALL | E_STRICT);

/*
 * set default time-zone to confirm the timezone
 * else it will show an error that system time is not reliable
 * Change it as per your timezone
 */
date_default_timezone_set('Asia/Calcutta');

/*
 * set maximum script execution time to overcome
 * timeout situations
 * I have set it for 5 minutes, i.e. 5 mins * 60 seconds,
 * But dont use unlimited or too much time as it may cause
 * too much server load and even breakdown
 */
set_time_limit(0);


/**
 * Define commonly used Constants so that using them will be easier
 * Here I've tried to name the constants in capitals so that they can be
 * distinguished clearly without any confusion, which is a standard also
 * Hence I'm going to name all GLOBALS & CONSTANTS in CAPITALS
 * Variables in camelCase,
 * private, protected properties and methods with _underscrore
 * and all pear2 standards of coding PHP
 * @link http://pear.php.net/manual/en/coding-standards.php for more info
 */
// define the server or host
define('HOST', $_SERVER['HTTP_HOST']);
/*
 * define the installed directory name
 * as this file is inside 'classes' directory, we must go one level
 * up to get the base directory name
 */
define('INSTALL_DIR', 'miniach');

// get http protocol
define(
    'PROTOCOL',
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://"
);
//define('PROTOCOL', 'https://');
// define complete host url by adding two slashes at start and end
define('ACCESS_URL', PROTOCOL . HOST . (INSTALL_DIR === '' ? '/' : '/' . INSTALL_DIR . '/'));


// define directory separator
define('DS', DIRECTORY_SEPARATOR);

// define complete directory path in order to get the actual directory path relative to the system
define('DIRPATH', dirname(dirname(__FILE__)));

// define log directory path
define('LOGDIR', DIRPATH . DS . 'logs');

// define page extension / file extension
define('FILEEXT', '');

// define current date
define('CURDATE', date('d-m-Y'));

// define current time
define('CURTIME', date('h:i:s A'));

// define database format date
define('DBDATE', date('Y-m-d'));

// define database format time
define('DBTIME', date('H:i:s'));

// define database timestamp
define('DBTIMESTAMP', date('Y-m-d H:i:s'));


// define a temporary directory to store attachments till message being sent
define('UPLOAD_DIR', DIRPATH . DS . 'helpers' . DS . 'images' . DS . 'uploads');
define('UPLOAD_DIR_FILE', DIRPATH . DS . 'helpers' . DS . 'files');

// define max attachment files size could be sent
define('MAX_ATTACHMENT_SIZE', 100 * 1024 * 1024);

// define mail template location
define('TPL_DIR', DIRPATH . DS . 'helpers' . DS . 'tpls');

// define cached pages option on or off
define('CACHE_STATUS', false);

// define cached pages location
define('CACHE_DIR', DIRPATH . DS . 'helpers' . DS . 'cache');

define('PGS_DIR', DIRPATH . DS . 'helpers' . DS . 'pages');

// define cache lifetime in seconds => 1 min = 60 seconds
define('CACHE_LIFETIME', 2 * 3600);

/* * **********************************************************************************************************
 * Database details start here, we have to define all the database details so that
 * those can be connected via those credentials.
 */

// define database host
define('DBHOST', 'localhost');

// define database driver
define('DRIVER', 'mysql');

// define database name
//define('DBNAME', 'miniach_data');
define('DBNAME', 'miniach');

// define database username
define('DBUSER', 'pma');
//define('DBUSER', 'miniach_user');

// define database password
define('DBPASS', 'password');
//define('DBPASS', ']b.b~n;}AJ*fE[xI');
//define('DBPASS', 'S~XNii}O]?=ZHL8P');

/**
 * define a security salt to encrypt password
 * This is used for password encryption which is
 * irreversible and don't ever change after you
 * have logged out of admin panel, else you may
 * never reset your password.
 * CAUTION
 * If you want to change the salt, contact the author
 */
define('SECURITY_SALT', 'lk,;h``h_+/-lkL"\'Llk*&%67445_7!~lLKJHkjhkut');

/*
 * Mail configuration, Change as per need
 */
// define email sending host
define('MAILERHOST', 'mail.visitpanchalingeswar.com');

// define user for authentication
define('MAILERUSER', 'support@visitpanchalingeswar.com');

// define password for authentication
define('MAILERPASS', 'Gi1irmYvvW6v');

// define port to send e-mail, normally the post may be 465 or 587 etc. for TLS
define('MAILERPORT', '465');

//define no-reply name
define('NOREPLYNAME', 'Odisha Vacation');

//define no-reply e-mail
define('NOREPLYMAIL', 'support@visitpanchalingeswar.com');

// define support e-mail for unsub
define('SUPPORTMAIL', 'support@visitpanchalingeswar.com');

// define support Name for unsub
define('SUPPORTNAME', 'Odisha Vacation Support');

// define reply to e-mail for any conversation
define('REPLYTOMAIL', 'shakti.das@odishavacations.com');

// define support Name for unsub
define('REPLYTONAME', 'Odisha Vacation Chapter');

/**
 * Function to autoload class files needed dynamically when new instance of the class is created
 * This autoload function need not be called but automatically fired
 *
 * @package RegistrationSystem
 * @param string $className The name of the class which is called
 * @access public
 * @category CommonFunction
 * @link http://www.php.net/manual/en/function.spl-autoload.php The autoload function documentation
 */
spl_autoload_register(function ($className) {
    /**
     * variable to store the filename of the class
     * The common style is to make the string into lowercase
     * and append .class.php in order to make the class file name
     * Same is also followed to name a class file
     *
     * @var string The class file name
     * @access private
     */
    $fileName = strtolower($className) . '.class.php';
    // check if the file exists
    if (file_exists(DIRPATH . DS . 'helpers' . DS . 'classes' . DS . $fileName)) {
        // if exists, require it
        require_once DIRPATH . DS . 'helpers' . DS . 'classes' . DS . $fileName;
    } else {
        die('The required class file not found. Path:' . DIRPATH . DS . 'helpers' . DS . 'classes' . DS . $fileName);
    }
});

function formatNumber($num)
{
    if ($num < 10) {
        return "00" . $num;
    } elseif ($num >= 10 and $num < 100) {
        return "0" . $num;
    } else {
        return strval($num);
    }
}

if (!function_exists('voucherNumber')) {
    function voucherNumber($bkId) {
        $sql = 'select bk1_id, bk1_resort, bk1_dttm from tbl_book1 where bk1_id = ' . $bkId;
        $bkData = DbOperations::getObject()->fetchData($sql);
        
        $vchrNum = '';
        switch ($bkData[0]['bk1_resort']) {
            case '1':
                $vchrNum .= 'HSR';
                break;
            case '3':
                $vchrNum .= 'HKRR';
                break;
            default :
                $vchrNum .= 'UNKNWN';
                break;
        }
        $vchrNum .= date('dmy', strtotime($bkData[0]['bk1_dttm'])) . formatNumber($bkData[0]['bk1_id']);
        
        return $vchrNum;
    }
}

function getHotelName($bkId) {
    $sql = 'select htl_name from tbl_book1 left join tbl_hotels on bk1_resort = htl_id where bk1_id = ' . $bkId;
    $bkData = DbOperations::getObject()->fetchData($sql);
    
    return $bkData[0]['htl_name'];
}

function getMrBkId($bkId) {
    $sql = 'select bk_id, res_id, mr_res_id, mr_advnc, mr_dttm from tbl_money_receipt where bk_id = ' . $bkId;
    $mrdata = DbOperations::getObject()->fetchData($sql);
    $mrDat = [];
    foreach ($mrdata as $dat) {
        $mrNum = 'MR-';
        switch ($dat['res_id']) {
            case '1':
                $mrNum .= 'HSR';
                break;
            case '3':
                $mrNum .= 'HKRR';
                break;
            default :
                $mrNum .= 'UNKNWN';
                break;
        }
        $mrNum .= date('dmy', strtotime($dat['mr_dttm'])) . formatNumber($dat['mr_res_id']);
        $mrDat[] = $mrNum . ' (' . $dat['mr_advnc'] . ')';
    }
    return $mrDat;
}

function getMrResort($bkId, $mrResId) {
    $sql = 'select res_id, mr_res_id, mr_dttm from tbl_money_receipt where bk_id = ? and mr_res_id = ?';
    $mrdata = DbOperations::getObject()->fetchData($sql, [$bkId, $mrResId]);
    $mrNum = 'MR-';
    switch ($mrdata[0]['res_id']) {
        case '1':
            $mrNum .= 'HSR';
            break;
        case '3':
            $mrNum .= 'HKRR';
            break;
        default :
            $mrNum .= 'UNKNWN';
            break;
    }
    $mrNum .= date('dmy', strtotime($mrdata[0]['mr_dttm'])) . formatNumber($mrdata[0]['mr_res_id']);
    return $mrNum;
}
