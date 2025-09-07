<?php

/**
 * Class file to clean user supplied data and
 * encrypt check for password etc
 *
 * PHP version 8.1
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ CodeCanyon
 *
 * @category   UserAction
 * @package    OVCRM
 * @author     Lokaprakash Behera <lokaprakash.behera@gmail.com>
 * @copyright  2023 The Best Freelancer
 * @license    http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ CodeCanyon
 * @version    GIT: $Id$
 * @link       http://repo.thebestfreelancer.in
 * @since      File available since Release 1.0
 * @deprecated File deprecated in Release 1.0
 */
// Check if User class exists or not and define if not
if (!class_exists('User')) {

    /**
     * Class file to handle users details
     *
     * PHP version 8.1
     *
     * @category  UserAction
     * @author    Lokaprakash Behera <lokaprakash.behera@gmail.com>
     * @copyright 2015 The Best Freelancer
     * @license   http://codecanyon.net/wiki/support/legal-terms/licensing-terms/ CodeCanyon
     * @version   Release: 1.0
     * @since     Class available since Release 1.0
     */
    class User extends Router 
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
        {}
        
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
        // {{{ index()

        /**
         * Index page for User
         *
         * @access public
         */
        public function index() 
        {
            if ($this->isLogged() === false) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'It seems your Session has been timed out. Please login again';
                header('Location:' . ACCESS_URL . 'index' . FILEEXT);
                exit;
            }
            
            $sql = 'select ver_id, ver_name from tbl_verticals where ver_sts = ? order by ver_name asc';
            $verData = DbOperations::getObject()->fetchData($sql, [1]);
            
            $verticalsOpt = '';
            foreach ($verData as $dat) {
                $verticalsOpt .= '<option value="' . $dat['ver_id'] . '">' . $dat['ver_name'] . '</option>';
            }
            $replaceData = [
                'PageTitle' => 'Add, View, Delete Users | Odisha Vacation',
                'MetaDesc' => '',
                'MetaKeys' => '',
                'CSSHelpers' => [],
                'JSHelpers' => ['custom.js'],
                'Contents' => file_get_contents(PGS_DIR . DS . 'users.html'),
                'Verticals' => $verticalsOpt
            ];
            PageCache::getObject()->createCache(ACCESS_URL . 'users', $replaceData, 'loginTpl.html');
        }
        
        // }}}
        // {{{ userFloatBtn()

        /**
         * Generate floating button based on user role.
         *
         * @return string HTML for the floating button.
         * @access public
         */
        public function userFloatBtn(): string
        {
            if (($this->getLogged() == 4) or ($this->getLogged() == 1)) {
                //return '<button class="btn btn-warning btn-lg" type="button" data-bs-toggle="modal" data-bs-target="#giveMsgModal">Give Message</button>';
                return '<a href="' . ACCESS_URL . 'user' . DIRECTORY_SEPARATOR . 'view-messages' . DIRECTORY_SEPARATOR . '" class="btn btn-warning btn-lg">Give Message</a>';
            } else {
                return '<a href="' . ACCESS_URL .'" class="btn btn-info btn-lg">MD&#39;s Message</a>';
            }
        }
        
        // }}}
        // {{{ viewMessages()

        /**
         * View Messages from MD's login
         *
         * @access public
         */
        public function viewMessages()
        {
            $replaceData = [
                'PageTitle' => 'Send, View Messages | Odisha Vacation',
                'MetaDesc' => '',
                'MetaKeys' => '',
                'CSSHelpers' => [],
                'JSHelpers' => ['custom.js'],
                'Contents' => file_get_contents(PGS_DIR . DS . 'view-messages.html')
            ];
            PageCache::getObject()->createCache(ACCESS_URL . 'view-messages', $replaceData, 'loginTpl.html');
        }
        
        // }}}
        // {{{ getEmployee()

        /**
         * Generate all Employee string list to be used in select element.
         *
         * @return string HTML for the Employees.
         * @access public
         */
        public function getEmployee(): string
        {
            $empStr = '';
            
            $sql = 'select usr_id, usr_fullname from ov_users where usr_id not in (1, 4)';
            $empData = DbOperations::getObject()->fetchData($sql);
            
            foreach ($empData as $dat) {
                $empStr .= '<option value="' . $dat['usr_id'] . '">' . $dat['usr_fullname'] . '</option>';
            }
            return $empStr;
        }
        
        
        // }}}
        // {{{ verDetUpdt()

        /**
         * Method to generate vertical details string
         * like User type after selecting Vertical
         *
         * @param array $verId Vertical ID
         * @return html string
         * @access public
         */
        public function verDetUpdt($verId)
        {
            $html = '';
            $sql = 'select user_id from tbl_user_vertical_relation where vert_id = ? and user_type = ?';
            $data = DbOperations::getObject()->fetchData($sql, [$verId[0], '1']);
            if (count($data) > 0) {
                $sql = 'select usr_id, usr_fullname from tbl_user_vertical_relation left join ov_users '
                        . 'on user_id = usr_id where vert_id = ? and user_type = ? and usr_active = ?';
                $usrData = DbOperations::getObject()->fetchData($sql, [$verId[0], '1', '1']);
                $vhOpt = '';
                if (count($usrData) > 0) {
                    foreach ($usrData as $dat) {
                        $vhOpt .= '<option value="' . $dat['usr_id'] . '">' . $dat['usr_fullname'] . '</option>';
                    }
                } else {
                    $vhOpt .= '<option value="0">No Manager available</option>';
                }
                $html .= '<div class="row"><div class="col-md-4"><label for="uType" class="form-label"><strong>User Type:</strong></label>'
                        . '<select class="form-select" name="uType" id="uType" onchange="vhName()">'
                        . '<option value="1">Manager</option><option value="2">Subordinate</option></select></div>'
                        . '<div id="vhName" class="col-md-4 d-none">'
                        . '<label for="mngr" class="form-label"><strong>Manager:</strong></label>'
                        . '<select class="form-select" name="mngr" id="mngr">'
                        . $vhOpt . '</select></div></div>';
            } else {
                $html .= '<div class="col-md-4"><label for="uType" class="form-label"><strong>User Type:</strong></label>'
                        . '<select class="form-select" name="uType" id="uType">'
                        . '<option value="1">Manager</option></select></div>';
            }
            die($html);
        }
        
        // }}}
        // {{{ verDetUpdtEdit()

        /**
         * Method to generate vertical details string
         * like User type after selecting Vertical in Edit mode
         *
         * @param array $verId Vertical ID
         * @return html string
         * @access public
         */
        public function verDetUpdtEdit($verId)
        {
            $html = '';
            $sql = 'select user_id from tbl_user_vertical_relation where vert_id = ? and user_type = ?';
            $data = DbOperations::getObject()->fetchData($sql, [$verId[0], '1']);
            if (count($data) > 0) {
                $sql = 'select usr_id, usr_fullname from tbl_user_vertical_relation left join ov_users '
                        . 'on user_id = usr_id where vert_id = ? and user_type = ? and usr_active = ?';
                $usrData = DbOperations::getObject()->fetchData($sql, [$verId[0], '1', '1']);
                $vhOpt = '';
                if (count($usrData) > 0) {
                    foreach ($usrData as $dat) {
                        $vhOpt .= '<option value="' . $dat['usr_id'] . '">' . $dat['usr_fullname'] . '</option>';
                    }
                } else {
                    $vhOpt .= '<option value="0">No Manager available</option>';
                }
                $html .= '<div class="row"><div class="col-md-4"><label for="uTypeEdt" class="form-label">'
                        . '<strong>User Type:</strong></label>'
                        . '<select class="form-select" name="uTypeEdt" id="uTypeEdt" onchange="vhNameEdt()">'
                        . '<option value="1">Manager</option><option value="2">Subordinate</option></select></div>'
                        . '<div id="vhNameEdt" class="col-md-4 d-none">'
                        . '<label for="mngrEdt" class="form-label"><strong>Manager:</strong></label>'
                        . '<select class="form-select" name="mngrEdt" id="mngrEdt">'
                        . $vhOpt . '</select></div></div>';
            } else {
                $html .= '<div class="col-md-4"><label for="uTypeEdt" class="form-label"><strong>User Type:</strong></label>'
                        . '<select class="form-select" name="uTypeEdt" id="uTypeEdt">'
                        . '<option value="1">Manager</option></select></div>';
            }
            die($html);
        }
        
        // }}}
        // {{{ loginUser()

        /**
         * Method to login the user using credentials
         *
         * @return mixed Status of the signing in
         * @access public
         */
        public function loginUser()
        {
            if (!isset(parent::getObject()->_post['username']) or empty(parent::getObject()->_post['username'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'Please Enter Username';
                $redirectUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ACCESS_URL;
                header('Location: ' . $redirectUrl);
                exit;
            }
            if (!isset(parent::getObject()->_post['password']) or empty(parent::getObject()->_post['password'])) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'Please Enter Password';
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit;
            }
            $sql = 'select usr_id, usr_fullname, vertical_id, usr_active, user_type, manager_id, usr_lastlogin '
                    . 'from ov_users left join tbl_user_vertical_relation on usr_id = user_id '
                    . 'where (usr_name = ? or usr_email = ?) and usr_password = ?';
            $data = DbOperations::getObject()->fetchData(
                    $sql,
                    [
                        parent::getObject()->_post['username'],
                        parent::getObject()->_post['username'],
                        DataFilter::getObject()->pwdHash(parent::getObject()->_post['password'])
                    ]
            );
            if ((count($data) > 0) and (isset($data[0]))) {
                if (intval($data[0]['usr_active']) !== 1) {
                    $_SESSION['STATUS'] = 'warning';
                    $_SESSION['MSG'] = 'Your account has been blocked or deactivated. Please contact Admin';
                    header('Location:' . $_SERVER['HTTP_REFERER']);
                    exit;
                }
                
                session_regenerate_id();
                $_SESSION['UID'] = $data[0]['usr_id'];
                $_SESSION['VER'] = $data[0]['vertical_id'];
                $_SESSION['UTYPE'] = $data[0]['vertical_id'];
                $_SESSION['USRTYPE'] = $data[0]['user_type'];
                $_SESSION['MID'] = $data[0]['manager_id'];
                $_SESSION['FULLNAME'] = $data[0]['usr_fullname'];
                //session_write_close();
                DbOperations::getObject()->transaction('start');
                DbOperations::getObject()->buildUpdateQuery(
                        'ov_users', ['usr_lastlogin'], ['usr_id']
                );
                $ins = [
                    DBTIMESTAMP,
                    $data[0]['usr_id']
                ];
                $suc = DbOperations::getObject()->runQuery($ins);
                if ($suc !== false) {
                    DbOperations::getObject()->transaction('on');
                } else {
                    DbOperations::getObject()->transaction('off');
                }
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG'] = 'Hello ' . $data[0]['usr_fullname'];
                header('Location:' . ACCESS_URL . 'admin-home/');
                exit;
            } else {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'You are not entering your details correctly, please retry';
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }
        
        // }}}
        // {{{ isLogged()
        
        /**
         * Method to confirm if user is logged in or not. If logged in, returns
         * $_SESSION['UTYPE'] - the type of user
         * 1 - Administrator
         * 2 - Moderator
         * 3 - User
         * 
         * @return mixed Status of the password reset process
         * @access public
         */
        public function isLogged() {
            if (isset($_SESSION['UID']) and !empty($_SESSION['UID']) and isset($_SESSION['UTYPE'])) {
                return $_SESSION['UTYPE'];
            } else {
                return false;
            }
        }
        
        // }}}
        // {{{ getLogged()
        
        /**
         * Method to confirm if user is logged in or not. If logged in, returns
         * $_SESSION['UID'] - the user id
         * 
         * @return mixed Status of the password reset process
         * @access public
         */
        public function getLogged() {
            if (isset($_SESSION['UID']) and !empty($_SESSION['UID']) and isset($_SESSION['UTYPE'])) {
                return $_SESSION['UID'];
            } else {
                return false;
            }
        }
        
        // }}}
        // {{{ saveVertical()

        /**
         * Method to save Vertical Details
         *
         * @return mixed Status from DB
         * @access public
         */
        public function saveVertical() 
        {
            if ($this->isLogged() === false) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'It seems your Session has been timed out. Please login again';
                header('Location:' . ACCESS_URL . 'index' . FILEEXT);
                exit;
            }
            
            if (!isset(parent::getObject()->_post['verName']) or empty(parent::getObject()->_post['verName'])) {
                die('<div class="alert alert-danger alert-dismissable">Please enter Vertical name<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            if ($_SESSION['UTYPE'] !== '1') {
                die('<div class="alert alert-danger alert-dismissable">You are not authorized to add Vertical.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildInsertQuery('tbl_verticals');
            $ins = [
                null,
                parent::getObject()->_post['verName'],
                1,
                $_SESSION['UID'],
                DBTIMESTAMP
            ];
            $suc = DbOperations::getObject()->runQuery($ins);
            if ($suc !== false) {
                DbOperations::getObject()->transaction('on');
                die('<div class="alert alert-success alert-dismissable">Vertical details saved successfully.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            } else {
                DbOperations::getObject()->transaction('off');
                die('<div class="alert alert-danger alert-dismissable">Details could not be saved due to some error. Please retry.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
        }
        
        // }}}
        // {{{ addUser()

        /**
         * Method to login the user using credentials
         *
         * @return mixed Status of the signing in
         * @access public
         */
        public function addUser() 
        {
            if ($this->isLogged() === false) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'It seems your Session has been timed out. Please login again';
                header('Location:' . ACCESS_URL . 'index' . FILEEXT);
                exit;
            }
            $sql = 'select usr_id from ov_users where '
                    . ' usr_email = ? and usr_id <> 1';
            $data = DbOperations::getObject()->fetchData($sql, [parent::getObject()->_post['email']]);
            $uType = '';
            if (!isset(parent::getObject()->_post['name']) or empty(parent::getObject()->_post['name'])) {
                die('<div class="alert alert-danger alert-dismissable">Please enter full name<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            if (!isset(parent::getObject()->_post['email']) or empty(parent::getObject()->_post['email'])) {
                die('<div class="alert alert-danger alert-dismissable">Please enter Email<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            if (!isset(parent::getObject()->_post['phn']) or empty(parent::getObject()->_post['phn'])) {
                die('<div class="alert alert-danger alert-dismissable">Please enter contact number<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            if (count($data) > 0) {
                die('<div class="alert alert-danger alert-dismissable">It seems You have registered before using this Email<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            if (!isset(parent::getObject()->_post['ver']) or empty(parent::getObject()->_post['ver'])) {
                die('<div class="alert alert-danger alert-dismissable">Please select Vertical<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            if (!isset(parent::getObject()->_post['uType']) or empty(parent::getObject()->_post['uType'])) {
                $uType .= 'NR'; // If parent::getObject()->_post['uType'] not set means Vertical is Admin
            }
            if (!isset(parent::getObject()->_post['pass']) or empty(parent::getObject()->_post['pass'])) {
                die('<div class="alert alert-danger alert-dismissable">Please Enter a Password<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            if (parent::getObject()->_post['pass'] !== parent::getObject()->_post['rPass']) {
                die('<div class="alert alert-danger alert-dismissable">Please Re-enter Password Correctly<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildInsertQuery('ov_users');
            $ins = [
                null,
                parent::getObject()->_post['name'],
                parent::getObject()->_post['email'],
                parent::getObject()->_post['email'],
                parent::getObject()->_post['phn'],
                DataFilter::getObject()->pwdHash(parent::getObject()->_post['rPass']),
                parent::getObject()->_post['ver'],
                0,
                DBTIMESTAMP,
                null
            ];
            $suc = DbOperations::getObject()->runQuery($ins);
            if ($uType !== 'NR') {
                $mngrId = '';
                if (parent::getObject()->_post['uType'] == '1') {
                    $mngrId .= $suc;
                } else {
                    $mngrId .= parent::getObject()->_post['mngr'];
                }
                DbOperations::getObject()->buildInsertQuery('tbl_user_vertical_relation');
                $ins1 = [
                    null,
                    $suc,
                    parent::getObject()->_post['ver'],
                    parent::getObject()->_post['uType'],
                    $mngrId
                ];
                $suc1 = DbOperations::getObject()->runQuery($ins1);
            }
            if ($suc !== false) {
                DbOperations::getObject()->transaction('on');
                die('<div class="alert alert-success alert-dismissable">User details saved successfully.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            } else {
                DbOperations::getObject()->transaction('off');
                die('<div class="alert alert-danger alert-dismissable">Details could not be saved due to some error. Please retry.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
        }
        
        // }}}
        // {{{ getVerticalData()

        /**
         * Method to get Verticals Details
         *
         * @return json
         * @access public
         */
        public function getVerticalData() 
        {
            $data = [];
            
            $sql = 'select ver_id, ver_name, ver_sts, ver_dttm, usr_fullname from tbl_verticals '
                    . 'left join ov_users on ver_added_by = usr_id';
            $verData = DbOperations::getObject()->fetchData($sql);
            
            $cnt = 0;
            if (count($verData) > 0) {
                foreach ($verData as $dat) {
                    $markBtnColor = '';
                    $markContent = '';
                    $href = '';
                    if ($dat['ver_sts'] == 1) {
                        $markBtnColor .= 'danger';
                        $markContent .= '<i class="fa-regular fa-circle-xmark"></i> Mark Vertical Inactive';
                        $href .= ACCESS_URL . 'user/mark-vertical/' . $dat['ver_id'] . '/0/';
                    } else {
                        $markBtnColor .= 'success';
                        $markContent .= '<i class="fa-regular fa-circle-check"></i> Mark Vertical Active';
                        $href .= ACCESS_URL . 'user/mark-vertical/' . $dat['ver_id'] . '/1/';
                    }
                    ++$cnt;
                    $data[] = [
                        $cnt,
                        $dat['ver_name'],
                        date('d-m-Y h:i A', strtotime($dat['ver_dttm'])),
                        $dat['usr_fullname'],
                        '<div class="btn-group btn-group-sm">'
                        . '<a href="javascript:void(0);" onclick="showModalVertical(\''
                        . ACCESS_URL . 'user/edit-vertical/' . $dat['ver_id'] . '/\')" class="btn btn-primary">'
                        . '<i class="fa-solid fa-pen-to-square"></i> Edit Vertical</a>'
                        . '<a href="' . $href . '" class="btn btn-' . $markBtnColor . '">' . $markContent . '</a>'
                        . '</div>'
                    ];
                }
            }
            die(json_encode($data));
        }
        
        // }}}
        // {{{ getUserData()

        /**
         * Method to login the user using credentials
         *
         * @return mixed Status of the signing in
         * @access public
         */
        public function getUserData() 
        {
            $data = [];
            $sql = 'select usr_id, usr_fullname, usr_email, usr_phn, vertical_id, ver_name, usr_createddate, usr_active, '
                    . 'usr_lastlogin from ov_users left join tbl_verticals on vertical_id = ver_id '
                    . 'where usr_id <> 1 order by usr_fullname asc';
            $usrData = DbOperations::getObject()->fetchData($sql);
            $sql = 'select usr_fullname, ut_name, vert_id, user_type, manager_id from tbl_user_vertical_relation '
                    . 'left join ov_users on manager_id = usr_id left join tbl_user_types on user_type = ut_id where user_id = ?';
            $mngrRes = DbOperations::getObject()->prepareQuery($sql);
            if (count($usrData) > 0) {
                foreach ($usrData as $dat) {
                    $markBtnColor = '';
                    $markContent = '';
                    $href = '';
                    if ($dat['usr_active'] == 1) {
                        $markBtnColor .= 'danger';
                        $markContent .= '<i class="fa-regular fa-circle-xmark"></i> Mark User Inactive';
                        $href .= ACCESS_URL . 'user/mark-user/' . $dat['usr_id'] . '/0/';
                    } else {
                        $markBtnColor .= 'success';
                        $markContent .= '<i class="fa-regular fa-circle-check"></i> Mark User Active';
                        $href .= ACCESS_URL . 'user/mark-user/' . $dat['usr_id'] . '/1/';
                    }
                    $mngrData = DbOperations::getObject()->fetchData(
                            '',
                            [$dat['usr_id']],
                            false,
                            $mngrRes
                    );
                    $data[] = [
                        $dat['usr_fullname'],
                        $dat['usr_email'],
                        $dat['usr_phn'],
                        $dat['ver_name'],
                        (!empty($mngrData[0]['ut_name']) ? $mngrData[0]['ut_name'] : ''),
                        (!empty($mngrData[0]['usr_fullname']) ? $mngrData[0]['usr_fullname'] : ''),
                        '<div class="btn-group btn-group-sm">'
                        . '<a href="javascript:void(0);" onclick="showModalUser(\''
                        . ACCESS_URL . 'user/edit-user/' . $dat['usr_id'] . '/\')" class="btn btn-primary">'
                        . '<i class="fa-solid fa-pen-to-square"></i> Edit User</a>'
                        . '<a href="' . $href . '" class="btn btn-' . $markBtnColor . '">' . $markContent . '</a>'
                        . '</div>'
                    ];
                }
            }
            die(json_encode($data));
        }
        
        // }}}
        // {{{ editVertical()

        /**
         * Method to generate form to edit Vertical details
         *
         * @param array $verId Vertical ID
         * @return html string
         * @access public
         */
        public function editVertical($verId) 
        {
            if ($this->isLogged() === false) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'It seems your Session has been timed out. Please login again';
                header('Location:' . ACCESS_URL . 'index' . FILEEXT);
                exit;
            }
            $sql = 'select ver_name from tbl_verticals where ver_id = ' . $verId[0];
            $verData = DbOperations::getObject()->fetchData($sql);
            
            $html = '<form name="verUpdtForm" id="verUpdtForm" class="needs-validation my-3" novalidate="novalidate"'
                    . ' method="post" action="' . ACCESS_URL . 'user/update-vertical/" onsubmit="return updateVertical(event);">'
                    . '<input type="hidden" name="usrId" id="usrId" value="' . $verId[0] . '">'
                    . '<div class="form-group mb-3 row"><div class="col-12">'
                    . '<label for="name" class="form-label"><strong>Vertical Name:</strong></label>'
                    . '<input type="text" class="form-control" name="verName" id="verName" required="required" '
                    . 'placeholder="Vertical Name" value="' . $verData[0]['ver_name'] . '">'
                    . '<div class="invalid-feedback">Please Enter Vertical Name</div>'
                    . '<div class="valid-feedback">Looks Good!</div></div></div><div class="text-center my-3">'
                    . '<button type="submit" class="btn btn-danger">Update</button></div></form>';
            die($html);
        }
        
        // }}}
        // {{{ editUser()

        /**
         * Method to generate form to edit user details
         *
         * @param array $usrId User ID
         * @return html string
         * @access public
         */
        public function editUser($usrId) 
        {
            if ($this->isLogged() === false) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'It seems your Session has been timed out. Please login again';
                header('Location:' . ACCESS_URL . 'index' . FILEEXT);
                exit;
            }
            $sql = 'select usr_fullname, usr_name, usr_phn, vertical_id from ov_users where usr_id = ' . $usrId[0];
            $usrData = DbOperations::getObject()->fetchData($sql);
            $sql = 'select ver_id, ver_name from tbl_verticals';
            $verData = DbOperations::getObject()->fetchData($sql);
            $verOpt = '';
            foreach ($verData as $dat) {
                $verOpt .= '<option value="' . $dat['ver_id'] . '"' . (($usrData[0]['vertical_id'] == $dat['ver_id']) ? ' selected="selected" ' : '') . '>' . $dat['ver_name'] . '</option>';
            }
            $usrDetData = '';
            $sql = 'select vert_id, user_type, manager_id from tbl_user_vertical_relation where user_id = ?';
            $uvrData = DbOperations::getObject()->fetchData($sql, [$usrId[0]]);
            if (count($uvrData) > 0) {
                $sql = 'select usr_id, usr_fullname, manager_id from tbl_user_vertical_relation left join ov_users '
                        . 'on user_id = usr_id where vert_id = ? and user_type = ? and usr_active = ?';
                $userData = DbOperations::getObject()->fetchData($sql, [$uvrData[0]['vert_id'], '1', '1']);
                $vhOpt = '';
                if (count($userData) > 0) {
                    foreach ($userData as $dat) {
                        $vhOpt .= '<option value="' . $dat['usr_id'] . '"' . (($uvrData[0]['manager_id'] == $dat['manager_id']) ? ' selected="selected" ' : '') . '>' . $dat['usr_fullname'] . '</option>';
                    }
                } else {
                    $vhOpt .= '<option value="0">No Manager available</option>';
                }
                $usrDetData .= '<div class="row"><div class="col-md-4"><label for="uTypeEdt" class="form-label">'
                        . '<strong>User Type:</strong></label><select class="form-select" name="uTypeEdt" id="uTypeEdt" '
                        . 'onchange="vhNameEdt()">'
                        . '<option value="1"' . ($uvrData[0]['user_type'] == 1 ? ' selected="selected" ' : '') 
                        . '>Manager</option>'
                        . '<option value="2"' . ($uvrData[0]['user_type'] == 2 ? ' selected="selected" ' : '') 
                        . '>Subordinate</option></select>'
                        . '</div><div id="vhNameEdt" class="col-md-4 '
                        . (($uvrData[0]['manager_id'] == $usrId[0]) ? 'd-none' : '') . '">'
                        . '<label for="mngrEdt" class="form-label"><strong>Manager:</strong></label>'
                        . '<select class="form-select" name="mngrEdt" id="mngrEdt">'
                        . $vhOpt. '</select></div></div>';
            }
            $html = '<form name="updateUser" id="updateUser" class="my-3" method="post" novalidate="novalidate" '
                    . 'onsubmit="updateUsr(event);" action="' . ACCESS_URL . 'user/update-user/">'
                    . '<input type="hidden" name="usrId" id="usrId" value="' . $usrId[0] . '">'
                    . '<div class="form-group mb-3 row"><div class="col-md-4"><label for="name" class="form-label">'
                    . '<strong>Full Name:</strong></label><input type="text" class="form-control" name="name" id="name"'
                    . ' required="required" value="' . $usrData[0]['usr_fullname'] . '">'
                    . '<div class="invalid-feedback">Please Enter Full Name</div>'
                    . '<div class="valid-feedback">Looks Good!</div></div><div class="col-md-4"><label for="email" '
                    . 'class="form-label"><strong>E-mail:</strong></label><input type="text" class="form-control" '
                    . 'name="email" id="email" required="required" value="' . $usrData[0]['usr_name'] . '" readonly="readonly">'
                    . '<div class="invalid-feedback">'
                    . 'Please Enter Email Id</div><div class="valid-feedback">Looks Good!</div></div>'
                    . '<div class="col-md-4"><label for="phn" class="form-label"><strong>Contact Number:</strong></label>'
                    . '<input type="text" class="form-control" name="phn" id="phn" required="required" value="'
                    . $usrData[0]['usr_phn'] . '"><div class="invalid-feedback">Please Enter Contact Number'
                    . '</div><div class="valid-feedback">Looks Good!</div>'
                    . '</div></div><div class="form-group mb-3 row"><div class="col-md-4"><label for="verEdit" class="form-label">'
                    . '<strong>Vertical:</strong></label><select class="form-select" name="verEdit" id="verEdit" onchange="verDetEdt()">'
                    . $verOpt . '</select><div class="invalid-feedback">Please Select Vertical</div>'
                    . '<div class="valid-feedback">Looks Good!</div></div><div id="verDetEdt" class="col-md-8">'
                    . $usrDetData . '</div>'
                    . '<div class="col-md-4"><label for="pass" class="form-label"><strong>Password:</strong></label>'
                    . '<input type="password" class="form-control" name="pass" id="pass" required="required" placeholder="Password">'
                    . '<div class="invalid-feedback">Please Enter Password</div><div class="valid-feedback">Looks Good!</div>'
                    . '</div><div class="col-md-4"><label for="rPass" class="form-label"><strong>Re-Type Password:</strong></label>'
                    . '<input type="password" class="form-control" name="rPass" id="rPass" required="required" '
                    . 'placeholder="Re-Type Password"><div class="invalid-feedback">Please Re-Type Password</div>'
                    . '<div class="valid-feedback">Looks Good!</div></div></div><div class="text-center my-3">'
                    . '<button type="submit" name="saveBook3" id="saveBook3" '
                    . 'class="btn btn-primary btn3">Save</button></div></form>';
            die($html);
        }
        
        // }}}
        // {{{ updateVertical()

        /**
         * Method to update Vertical data
         *
         * @access public
         */
        public function updateVertical() 
        {
            if ($this->isLogged() === false) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'It seems your Session has been timed out. Please login again';
                header('Location:' . ACCESS_URL . 'index' . FILEEXT);
                exit;
            }
            if (!isset(parent::getObject()->_post['verName']) or empty(parent::getObject()->_post['verName'])) {
                die('<div class="alert alert-danger alert-dismissable">Please enter Vertical name<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            if ($_SESSION['UTYPE'] != '1') {
                die('<div class="alert alert-danger alert-dismissable">You are not authorized to update Vertical.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildUpdateQuery('tbl_verticals', ['ver_name', 'ver_added_by', 'ver_dttm'], ['ver_id']);
            
            $updtArr = [
                parent::getObject()->_post['verName'],
                $_SESSION['UID'],
                DBTIMESTAMP,
                parent::getObject()->_post['usrId']
            ];
            
            $suc = DbOperations::getObject()->runQuery($updtArr);
            
            if ($suc !== false) {
                DbOperations::getObject()->transaction('on');
                die('<div class="alert alert-success alert-dismissable">Vertical details updated successfully.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            } else {
                DbOperations::getObject()->transaction('off');
                die('<div class="alert alert-danger alert-dismissable">Details could not be updated due to some error. Please retry.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
        }
        
        // }}}
        // {{{ updateUser()

        /**
         * Method to update user data
         *
         * @access public
         */
        public function updateUser() 
        {
            if ($this->isLogged() === false) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'It seems your Session has been timed out. Please login again';
                header('Location:' . ACCESS_URL . 'index' . FILEEXT);
                exit;
            }
            $sql = 'select vertical_id from ov_users where usr_id = ?';
            $usrData = DbOperations::getObject()->fetchData($sql, [parent::getObject()->_post['usrId']]);
            if (!isset(parent::getObject()->_post['name']) or empty(parent::getObject()->_post['name'])) {
                die('<div class="alert alert-danger alert-dismissable">Please enter full name<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            if (!isset(parent::getObject()->_post['email']) or empty(parent::getObject()->_post['email'])) {
                die('<div class="alert alert-danger alert-dismissable">Please enter Email<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            if (!isset(parent::getObject()->_post['pass']) or empty(parent::getObject()->_post['pass'])) {
                die('<div class="alert alert-danger alert-dismissable">Please Enter a Password<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            if (parent::getObject()->_post['pass'] !== parent::getObject()->_post['rPass']) {
                die('<div class="alert alert-danger alert-dismissable">Please Re-enter Password Correctly<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
            $sql = 'select user_id, vert_id from tbl_user_vertical_relation where user_id = ?';
            $uvrData = DbOperations::getObject()->fetchData($sql, [parent::getObject()->_post['usrId']]);
            if (count($uvrData) > 0) {
                
            }
            DbOperations::getObject()->transaction('start');
            if ($usrData[0]['vertical_id'] != parent::getObject()->_post['verEdit']) {
                if (parent::getObject()->_post['verEdit'] == 1) {
                    DbOperations::getObject()->buildDeleteQuery(
                            'tbl_user_vertical_relation',
                            ['user_id']
                    );
                    DbOperations::getObject()->runQuery([parent::getObject()->_post['usrId']]);
                } else if (($usrData[0]['vertical_id'] == 1) and (!isset($uvrData) or empty($uvrData))) {
                    $mngrId = '';
                    if (parent::getObject()->_post['uTypeEdt'] == '1') {
                        $mngrId .= parent::getObject()->_post['usrId'];
                    } else {
                        $mngrId .= parent::getObject()->_post['mngrEdt'];
                    }
                    DbOperations::getObject()->buildInsertQuery('tbl_user_vertical_relation');
                    $ins1 = [
                        null,
                        parent::getObject()->_post['usrId'],
                        parent::getObject()->_post['verEdit'],
                        parent::getObject()->_post['uTypeEdt'],
                        $mngrId
                    ];
                    $suc1 = DbOperations::getObject()->runQuery($ins1);
                } else {
                    $mngrId = '';
                    if (parent::getObject()->_post['uTypeEdt'] == '1') {
                        $mngrId .= parent::getObject()->_post['usrId'];
                    } else {
                        $mngrId .= parent::getObject()->_post['mngrEdt'];
                    }
                    DbOperations::getObject()->buildUpdateQuery('tbl_user_vertical_relation', ['vert_id', 'user_type', 'manager_id'], ['user_id']);
                    DbOperations::getObject()->runQuery([parent::getObject()->_post['verEdit'], parent::getObject()->_post['uTypeEdt'], $mngrId, parent::getObject()->_post['usrId']]);
                }
            }
            DbOperations::getObject()->buildUpdateQuery('ov_users', ['usr_fullname', 'usr_phn', 'usr_password', 'vertical_id'], ['usr_id']);
            $ins = [
                parent::getObject()->_post['name'],
                parent::getObject()->_post['phn'],
                DataFilter::getObject()->pwdHash(parent::getObject()->_post['rPass']),
                parent::getObject()->_post['verEdit'],
                parent::getObject()->_post['usrId']
            ];
            $suc = DbOperations::getObject()->runQuery($ins);
            if ($suc !== false) {
                DbOperations::getObject()->transaction('on');
                die('<div class="alert alert-success alert-dismissable">User details updated successfully.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            } else {
                DbOperations::getObject()->transaction('off');
                die('<div class="alert alert-danger alert-dismissable">Details could not be updated due to some error. Please retry.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button></div>');
            }
        }
        
        // }}}
        // {{{ markVertical()

        /**
         * Method to mark Vertical Active/Inactive
         *
         * @param array $param Vertical ID and Status to be change
         * @access public
         */
        public function markVertical($param) 
        {
            if ($this->isLogged() === false) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'It seems your Session has been timed out. Please login again';
                header('Location:' . ACCESS_URL . 'index' . FILEEXT);
                exit;
            }
            $msg = '';
            switch (intval($param[1])) {
                case 1:
                    $msg = 'Vertical activated successfully';
                    break;
                default:
                    $msg = 'Vertical deactivated successfully';
                    break;
            }
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildUpdateQuery(
                    'tbl_verticals', ['ver_sts'], ['ver_id']
            );
            $ins = [
                $param[1],
                $param[0]
            ];
            $suc = DbOperations::getObject()->runQuery($ins);
            if ($suc !== false) {
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG'] = $msg;
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                DbOperations::getObject()->transaction('off');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'Error changing User status';
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit;
            }
        }
        
        // }}}
        // {{{ markUser()

        /**
         * Method to mark user Active/Inactive
         *
         * @param array $param User ID and Status to be change
         * @access public
         */
        public function markUser($param) 
        {
            if ($this->isLogged() === false) {
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'It seems your Session has been timed out. Please login again';
                header('Location:' . ACCESS_URL . 'index' . FILEEXT);
                exit;
            }
            $msg = '';
            switch (intval($param[1])) {
                case 1:
                    $msg = 'User activated successfully';
                    break;
                default:
                    $msg = 'User deactivated successfully';
                    break;
            }
            DbOperations::getObject()->transaction('start');
            DbOperations::getObject()->buildUpdateQuery(
                    'ov_users', ['usr_active'], ['usr_id']
            );
            $ins = [
                $param[1],
                $param[0]
            ];
            $suc = DbOperations::getObject()->runQuery($ins);
            if ($suc !== false) {
                DbOperations::getObject()->transaction('on');
                $_SESSION['STATUS'] = 'success';
                $_SESSION['MSG'] = $msg;
                header('Location:' . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                DbOperations::getObject()->transaction('off');
                $_SESSION['STATUS'] = 'error';
                $_SESSION['MSG'] = 'Error changing User status';
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
    