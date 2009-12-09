<?php
/* -------------------- security class extends module ----------------*/

// security check - must be included in all scripts
if (! $GLOBALS ['kewl_entry_point_run']) {
    die ( "You cannot view this page directly" );
}
// end security check


/**
 * Module class to handle displaying the module list
 *
 * @author Sean Legassick
 *
 * $Id$
 */
class security extends controller {
    public $objUser;
    public $objLanguage;
    public $objUserModel;
    public $objSkin;
    public $objConfig;
    public $objEpiCurl;
    public $objEpiOAuth;
    public $objEpiTwitter;
    public $objEpiWrapper;
    public $objDbSysconfig;
    public $consumer_key;
    public $consumer_secret;
    
    function init() {
        $this->objUser = $this->getObject ( 'user' );
        $this->objUserModel = $this->getObject ( 'useradmin_model2' );
        $this->objLanguage = $this->getObject ( 'language', 'language' );
        $this->objDbSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
        
        //Get an instance of the skin
        $this->objSkin = $this->getObject ( 'skin', 'skin' );
        $this->objConfig = $this->getObject ( 'altconfig', 'config' );
        $this->objEpiWrapper = $this->getObject ( 'epiwrapper' );
    }

    function requiresLogin($action) {
        $actions = array ('showlogin', 'login', 'logintwitter', 'error', 'needpassword', 'needpasswordconfirm', 'emailsent', 'generatenewcaptcha', 'oauthdisp', 'fbconnect' );

        if (in_array ( $action, $actions )) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function dispatch($action) {
        $this->setLayoutTemplate ( NULL );
        switch ($action) {
            case 'login' :
                $module = $this->getParam ( 'mod' );
                return $this->doLogin ( $module );
            case 'logintwitter' :
                $module = $this->getParam ( 'mod' );
                return $this->doTwitterLogin ( $module );
            case 'bbauthlogin' :
                // log in via Yahoo! BBAuth

                break;
            case 'logoff' :
                return $this->doLogoff ();
            case 'error' :
                return $this->errorMessages ();
            case 'needpassword' :
                return $this->needPassword ();
            case 'generatenewcaptcha' :
                return $this->generateNewCaptcha ();
            case 'needpasswordconfirm' :
                return $this->needPasswordConfirm ();
            case 'emailsent' :
                return $this->emailSent ();
            case 'oauthdisp' :
                echo $this->oauthDisp(); break;
            case 'fbconnect' :
                $this->objMods = $this->getObject('modules', 'modulecatalogue');
                $this->objDbSysconfig = $this->getObject('dbsysconfig', 'sysconfig');
                if($this->objMods->checkIfRegistered('facebookapps')) {
                    $apikey = $this->objDbSysconfig->getValue('apikey', 'facebookapps');
                    $secret = $this->objDbSysconfig->getValue('apisecret', 'facebookapps');
                    include($this->getResourcePath('facebook.php','facebookapps'));
                    
                    $this->facebook = new Facebook($apikey, $secret);
                    $uid = $this->facebook->get_loggedin_user(); 
                    $user_details = $this->facebook->api_client->users_getInfo($uid, 'first_name, last_name, proxied_email, username, sex'); 
                    // var_dump($user_details); die();
                    $details = $user_details[0];
                    $username = $details['username'];
                    $p = explode("@", $details['proxied_email']);
                    $password = $p[0];
                    // try the login
                    $objUModel = $this->getObject('useradmin_model2', 'security');
                    $objUser = $this->getObject('user', 'security');
                    $login = $this->objUser->authenticateUser($username, $password, FALSE);
                    if(!$login) {
                        // login failure, so new user. Lets create him in the system now and then log him in.
                        $userid = $details['uid'];
                        $title = '';
                        $firstname = $details['first_name'];
                        $surname = $details['last_name'];
                        $email = $details['proxied_email'];
                        $sex = $details['sex'];
                        if($sex == 'male') {
                            $sex = 'M';
                        }
                        else {
                            $sex = 'F';
                        }
                        $country = '';
                        $accountType = 'Facebook'; 
                        $objUModel->addUser($userid, $username, $password, $title, $firstname, $surname, $email, $sex, $country, $cellnumber='', $staffnumber='', $accountType, '1');
                        $this->objUser->authenticateUser($username, $password, FALSE);
                        $this->nextAction('');
                    }
                    else {
                        $this->nextAction('');
                    }
                   
                }
                break;
            case 'showlogin' :
            default :
                return $this->showPreLoginModule ();
        }
    }

    /**
     * Login method, handles login logic.
     * @return string Name of template to display
     */
    function doLogin($module = NULL) {
        $username = $this->getParam ( 'username', '' );
        $password = $this->getParam ( 'password', '' );
        $remember = $this->getParam( 'remember', 'off');
        if(strlen($username) > 255 || strlen($password) > 255) {
            $message = 'wrongpassword';
            return $this->nextAction ( 'error', array ('message' => $message ) );
        }
        if($password == '--twitter--') {
            $message = 'dooauth';
            return $this->nextAction ( 'error', array ('message' => $message ) );
        }
        if($remember == 'on') {
            $remember = true;
        }
        else {
            $remember = false;
        }
        if ($this->objUser->authenticateUser ( $username, $password, $remember )) {
            // we hold off creating a new session until successful
            // (only is we didn't already have a session on the go,
            //  as if so it will already have been started in index.php)
            if (! isset ( $_REQUEST [session_name ()] )) {
                $this->objEngine->sessionStart ();
            } else {
                session_regenerate_id ();
            }
            //Validate the current skin Session or set it if not present
            //Skin is also passed as a hidden input
            $this->objSkin->validateSkinSession ();
            // Redirect to logged in page so that user can refresh it
            // without being hassled by browser about resubmitting
            // form details
            // Redirect to logged in page so that user can refresh it
            // without being hassled by browser about resubmitting
            // form details
            $url = $this->getSession ( 'oldurl' );
            $url ['passthroughlogin'] = 'true';
            if ($module != NULL) {
                $url ['module'] = $module;
            }
            if (is_array ( $url ) && (isset ( $url ['module'] )) && ($url ['module'] != 'splashscreen')) {
                if (isset ( $url ['action'] ) && ($url ['action'] != 'logoff')) {
                    $act = $url ['action'];
                } else {
                    $act = NULL;
                }
                return $this->nextAction ( $act, $url, $url ['module'] );
            }
            $postlogin = $this->objConfig->getdefaultModuleName ();
            return $this->nextAction ( NULL, NULL, $postlogin );
        }
        if (defined ( 'STATUS' ) && STATUS == 'inactive') {
            //user account is inactive. Contact the SysAdmin if you need it re-enabled.
            // still to be developed
            return $this->nextAction ( 'error', array ('message' => 'inactive' ) );
        } else {
            // unsuccessful authentication of user
            // Further checks to support the user
            // Check if the username exists
            if ($this->objUser->valueExists ( 'username', $username )) {
                $message = 'wrongpassword'; // send a message that the password was wrong
            } else {
                $message = 'noaccount'; // send a message that the username doesn't exist
            }
            // Check for LDAP error
            if ($this->getSession ( 'ldaperror' ) == 'FAIL') {
                $this->setSession ( 'ldaperror', '' );
                $message = 'no_ldap'; // send a message that the LDAP server cannot be contacted.
            }
            return $this->nextAction ( 'error', array ('message' => $message ) );
        }
    }

    public function doTwitterLogin($module = NULL) {
        // grab the consumer secret and key from sysconfig quickly
        try {
            $this->consumer_key = $this->objDbSysconfig->getValue('twitter_consumer_key', 'security');
            $this->consumer_secret = $this->objDbSysconfig->getValue('twitter_consumer_secret', 'security');
        
            $this->objEpiTwitter = new EpiTwitter($this->consumer_key, $this->consumer_secret);

            $this->objEpiTwitter->setToken($this->getParam('oauth_token'));
            $token = $this->objEpiTwitter->getAccessToken();
            $this->objEpiTwitter->setToken($token->oauth_token, $token->oauth_token_secret);

            // save to cookies
            setcookie('oauth_token', $token->oauth_token);
            setcookie('oauth_token_secret', $token->oauth_token_secret);

            $twitterInfo = $this->objEpiTwitter->get_accountVerify_credentials();

            $password = "--twitter--";
            $userid = $twitterInfo->id;
            $username = $twitterInfo->screen_name;
            $fullname = $twitterInfo->name;
            $howcreated = 'twitter oauth';
            $name = explode(" ", $fullname);
            $firstname = $name[0];
            $surname = $name[1];

            if ($this->objUser->authenticateUser ( $username, $password, $remember )) {
                if (! isset ( $_REQUEST [session_name ()] )) {
                    $this->objEngine->sessionStart ();
                } else {
                    session_regenerate_id ();
                }
                $this->objSkin->validateSkinSession ();
                $url = $this->getSession ( 'oldurl' );
                $url ['passthroughlogin'] = 'true';
                if ($module != NULL) {
                    $url ['module'] = $module;
                }
                if (is_array ( $url ) && (isset ( $url ['module'] )) && ($url ['module'] != 'splashscreen')) {
                    if (isset ( $url ['action'] ) && ($url ['action'] != 'logoff')) {
                        $act = $url ['action'];
                    } else {
                        $act = NULL;
                    }
                    return $this->nextAction ( $act, $url, $url ['module'] );
                }
                $postlogin = $this->objConfig->getdefaultModuleName ();
                return $this->nextAction ( NULL, NULL, $postlogin );
            }
            else {
                // The user has never signed in before so needs to be created before he does
                $objUAModel = $this->getObject('useradmin_model2', 'security');
                $pk = $objUAModel->addUser($userid, $username, $password, $title, $firstname, $surname, $email, $sex, $country, '', '', 'twitter oauth', '1');
                $this->objUser->authenticateUser ( $username, $password, $remember );
                if (! isset ( $_REQUEST [session_name ()] )) {
                    $this->objEngine->sessionStart ();
                } else {
                    session_regenerate_id ();
                }
                $this->objSkin->validateSkinSession ();
                $url = $this->getSession ( 'oldurl' );
                $url ['passthroughlogin'] = 'true';
                if ($module != NULL) {
                    $url ['module'] = $module;
                }
                if (is_array ( $url ) && (isset ( $url ['module'] )) && ($url ['module'] != 'splashscreen')) {
                    if (isset ( $url ['action'] ) && ($url ['action'] != 'logoff')) {
                        $act = $url ['action'];
                    } else {
                        $act = NULL;
                    }
                    return $this->nextAction ( $act, $url, $url ['module'] );
                }
                $postlogin = $this->objConfig->getdefaultModuleName ();
                return $this->nextAction ( NULL, NULL, $postlogin );
             }
        }    
        catch (customException $e) {
            customException::cleanUp();
            exit;
        }
    }

    

    /**
     * Logoff method, handle logoff logic.
     * @return string Name of template to display
     */
    function doLogoff() {
        $show = $this->objDbSysconfig->getValue('show_twitter_auth', 'security');
        $fbshow = $this->objDbSysconfig->getValue('show_fbconnect_auth', 'security');
        if(strtolower($show) == 'true') {
            $this->consumer_key = $this->objDbSysconfig->getValue('twitter_consumer_key', 'security');
            $this->consumer_secret = $this->objDbSysconfig->getValue('twitter_consumer_secret', 'security');
            $this->objEpiTwitter = new EpiTwitter($this->consumer_key, $this->consumer_secret, $_COOKIE['oauth_token'], $_COOKIE['oauth_token_secret']);

            $this->objEpiTwitter->get_accountEnd_session();
            $lo = $this->objLu->logout ();
        }
        if($fbshow == 'true') {
            $apikey = $this->objDbSysconfig->getValue('apikey', 'facebookapps');
            $secret = $this->objDbSysconfig->getValue('apisecret', 'facebookapps');
            include($this->getResourcePath('facebook.php','facebookapps'));
            $this->facebook = new Facebook($apikey, $secret);
            $this->facebook->clear_cookie_state();
            sleep(300);
            $lo = $this->objLu->logout ();
        }
        else {
            $lo = $this->objLu->logout ();
        }

        return $this->showPreLoginModule ();
    }

    /**
     * Method to show the Pre Login Module
     */
    function showPreLoginModule() {
        // Validate the skin, checks if it exists or changed
        $this->objSkin->validateSkinSession ();
        $url = $_GET;
        if (is_array ( $url ) && isset ( $url ['module'] ) && ! in_array ( $url ['module'], array ('security', '_default' ) )) {
            $this->setSession ( 'oldurl', $url );
            return $this->nextAction ( 'error', array ('message' => 'needlogin' ) );
        }
        return $this->nextAction ( NULL, NULL, $this->objConfig->getPrelogin ( 'KEWL_PRELOGIN_MODULE' ) );
    }

    function needPassword() {
        if ($this->objUser->isLoggedIn ()) {
            $postlogin = $this->objConfig->getdefaultModuleName ();
            return $this->nextAction ( NULL, NULL, $postlogin );
        } else {
            $this->setLayoutTemplate ( 'login_layout_tpl.php' );
            $this->setVar('_actionneedpassword', 1);
            return 'forgotyourpassword_tpl.php';
        }
    }

    function generateNewCaptcha() {
        $objCaptcha = $this->getObject ( 'captcha', 'utilities' );
        echo $objCaptcha->show ();
        //echo 'asffas';
    }

    function needPasswordConfirm() {
        if ($this->objUser->isLoggedIn ()) {
            $postlogin = $this->objConfig->getdefaultModuleName ();
            return $this->nextAction ( NULL, NULL, $postlogin );
        }

        if (md5 ( strtoupper ( $this->getParam ( 'request_captcha' ) ) ) == $this->getParam ( 'captcha' )) {
            $username = $this->getParam ( 'request_username' );
            $email = $this->getParam ( 'request_email' );

            $userDetails = $this->objUserModel->getUserNeedPassword ( $username, $email );
            $usernameAvailable = $this->objUserModel->usernameAvailable ( $username );

            if ($userDetails == FALSE) {
                return $this->nextAction ( 'needpassword', array ('error' => 'details' ) );
            }

            if ($userDetails ['howcreated'] == 'LDAP') {
                return $this->nextAction ( 'needpassword', array ('error' => 'ldap' ) );
            } else {
                $this->objUserModel->newPasswordRequest ( $userDetails ['id'] );
                $this->setSession ( 'passwordrequest', $userDetails ['id'] );
                return $this->nextAction ( 'emailsent' );
            }

        } else {
            return $this->nextAction ( 'needpassword', array ('error' => 'captcha' ) );
        }
    }

    function emailSent() {
        if ($this->getSession ( 'passwordrequest' ) == '') {
            return $this->nextAction ( NULL, NULL, '_default' );
        }

        $userDetails = $this->objUserModel->getUserDetails ( $this->getSession ( 'passwordrequest' ) );

        if ($userDetails == FALSE) {
            return $this->nextAction ( NULL, NULL, '_default' );
        } else {
            $this->setVarByRef ( 'user', $userDetails );
            $this->setLayoutTemplate ( 'login_layout_tpl.php' );
            return 'emailsent.php';
        }
    }

    function errorMessages() {
        if ($this->objUser->isLoggedIn ()) {
            $postlogin = $this->objConfig->getdefaultModuleName ();
            return $this->nextAction ( NULL, NULL, $postlogin );
        }

        $this->setLayoutTemplate ( 'login_layout_tpl.php' );
        $this->setVar ( 'pageSuppressToolbar', TRUE );
        return 'error_message.php';
    }
}

?>