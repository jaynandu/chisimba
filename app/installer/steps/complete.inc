<?php
require_once INSTALL_DIR.'/step.inc';
require_once INSTALL_DIR.'/stepaction.inc';

/**
* CompleteStep
*
*
* The final step in the wizard that notifies the user that things are complete.
*
* @author prince mbekwa
* @version $Id
* @package Installer
*/

class Complete extends Step
{

    /**
    * The step shown when install is completed.
    *
    * @param
    * @return void
    * @access public
    */
    function Complete()
    {
        $this->actions[] = new CompleteAction(get_class($this));
        $this->step_title = 'Complete';
        $this->title_logo = 'complete.gif';
        $this->can_cancel = false;
        $this->step_help = "Congratulations! Your Chisimba installation has been a success. Please follow the link below to view your site";

    }


}


/**
* CompleteAction
*
* This action is fired when the user has completed the installation process.
*
* @author prince mbekwa, James Scoble
* @version $Id
* @package Installer
*/

class CompleteAction extends StepAction
{


    /**
    * Show the complete text
    *
    * @return void
    * @access public
    */
    function paintAction()
    {
        $tpl = new Template(INSTALL_DIR.'/templates/complete.tpl');
        $registerLink = 'http://'.str_replace('//','/',$_SESSION['site_server_url'].'/'.$_SESSION['site_url'].'/index.php?module=modulecatalogue&action=firsttimeregistration&sysType=').str_replace(' ','%20',$_SESSION['SysType']);
        // Define variables for AJAX installer
        $ajaxRegisterLink = 'http://'.str_replace('//','/',$_SESSION['site_server_url'].'/'.$_SESSION['site_url'].'/').'index.php';
        //$ajaxRegisterLinkParams = 'module=modulecatalogue&action=firsttimeregistration&sysType='.$_SESSION['SysType'].'&ajax=true'; //str_replace(' ','%20',);
        $ajaxRegisterLinkParams = "{ module: \'modulecatalogue\', action: \'firsttimeregistration\', sysType: \'".$_SESSION['SysType']."\', ajax: \'true\' }"; //urlencode()
        $ajaxLoginLink = 'http://'.str_replace('//','/',$_SESSION['site_server_url'].'/'.$_SESSION['site_url'].'/').'index.php?module=security&action=login&username=admin&password=a&mod=modulecatalogue';

        $tpl->set('login_url', $registerLink);
        // Pass variables for AJAX installer to template
        $tpl->set('extra', './extra');
        $tpl->set('ajaxregister_url', $ajaxRegisterLink);
        $tpl->set('ajaxregister_url_params', $ajaxRegisterLinkParams);
        $tpl->set('ajaxlogin_url', $ajaxLoginLink);

        echo $tpl->fetch();

    }


}
?>
