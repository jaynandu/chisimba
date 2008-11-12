<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* textare class to use to make textarea inputs.
* 
* @package   htmlelements
* @category  HTML Controls
* @author    Wesley Nitsckie
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
* @todo      -c HTML Editor that will extend this object
*/

//require_once("htmlbase_class_inc.php");
class htmlarea extends object
 {
     /**
    * 
    * @var string $siteRootPath: The path to the site
    */
    var $siteRootPath;
     /**
    * 
    * @var string $cols: The number of columns the textare will have
    */
    var $cols;
    /**
    * 
    * @var string $rows: The number of rows the textare will have
    */
    var $rows;
    
    /**
    * 
    * @var string $label: The label of the editor
    */
    var $label;
    
    /**
    * 
    * @var string $cssClass: The style sheet class
    */
    var $cssClass;
    
    /**
    * 
    * @var string $height: The height of the editor
    */
    var $height;
    
    /**
    * 
    * @var string $width: The width of the editor 
    */
    var $width;
    /**
    * 
    * @var string $toolbarSet: The toolbarSet of the editor : either Default or Basic
    */
    var $toolbarSet;

    /**
    * @var boolean $context Are we in a context aware mode.
    */
    var $context;

   /**
    * @var string $fck_version Which version of FCKEditor to load (2.5.1 vs 2.6.3)
    */
    public $fckVersion;

    
    /**
    * Method to establish the default values
    */
    function init($name=null,$value=null,$rows=4,$cols=50,$context=false)
     {
        $this->sysConf = $this->getObject('dbsysconfig', 'sysconfig');
        //Loading the default FCK version from config
        $this->fckVersion = $this->sysConf->getValue('FCKEDITOR_VERSION', 'htmlelements');
        $this->height = '400px';
        $this->width = '100%';
        $this->toolbarSet='Default';
        $this->name=$name;
        $this->value=$value;
        $this->rows=$rows;
        $this->cols=$cols;
        $this->css='textarea';
        //$this->_objConfig =& $this->getObject('config', 'config');
        //$siteRootPath = $this->_objConfig->siteRootPath();
        $objConfig=$this->getObject('altconfig','config');
        $siteRoot=$objConfig->getsiteRoot();
        //$siteRootPath = "http://".$_SERVER['HTTP_HOST']."/nextgen/";
        //$this->setSiteRootPath($siteRoot);
        $this->context = $context;
        $this->toolbarSet = 'advanced';
        
    }

   /**
    * Method to set the version of FCKEditor to load (2.5.1 vs 2.6.3)
    *
    */
    public function setVersion($fckVersion)
    {
        $this->fckVersion = $fckVersion;
    }

    /**
    * function to set the root path
    * 
    * @var string $siteRootPath: The site path
    */
    function setSiteRootPath($siteRootPath)
    {
        $this->siteRootPath = $siteRootPath;
    }
    
    /**
    * function to set the value of one of the properties of this class
    * 
    * @var string $name: The name of the textare
    */
    function setName($name)
    {
        $this->name=$name;
    }
    /**
    * function to set the amount of rows 
    * @var string $Rows: The number of rows of the textare
    *             
    */
    function setRows($rows)
    {
        $this->rows=$rows;
    }
    /**
    * function to set the amount of cols 
    * @var string $cols: The number of cols of the textare
    *             
    */
    function setColumns($cols)
    {
        $this->cols=$cols;
    }
    
    /**
    * function to set the content
    * @var string $content: The content of the textare
    */
    function setContent($value=null)
    {
        $this->value=$value;
    }
   
    /**
    * Method to display the WYSIWYG Editor
    */
    function show()
    {
        return $this->showFCKEditor();
    }
    
    /**
    * Method to show the FCKEditor
    * @return string
    */
    function showFCKEditor($version = '2.6.3')
    {
        if ($this->fckVersion == '2.5.1') {
            require_once($this->getResourcePath('fckeditor_2.5.1/fckeditor.php', 'htmlelements'));
        } else {
            require_once($this->getResourcePath('fckeditor/fckeditor_2.6.3/fckeditor_php5.php', 'htmlelements'));
        }
        $objConfig =  $this->newObject('altconfig', 'config');

        $sitePath = pathinfo($_SERVER['PHP_SELF']);
        $sBasePath = $sitePath['dirname'];
		
        $sBasePath = str_replace('\\', '/', $sBasePath);
        $sBasePath = preg_replace('/\/+/', '/', $sBasePath);
      
		if (substr($sBasePath, -1, 1) != '/') {
			$sBasePath .= '/';
		}
		
        if ($this->fckVersion == '2.5.1') {
            $sBasePath .= 'core_modules/htmlelements/resources/fckeditor_2.5.1/';
        } else {
            $sBasePath .= 'core_modules/htmlelements/resources/fckeditor/fckeditor_2.6.3/';
        }
        
        $oFCKeditor = new FCKeditor($this->name) ;
        $oFCKeditor->BasePath = $sBasePath ;
        $oFCKeditor->Width= $this->width ;
		$oFCKeditor->Height=$this->height;
        $oFCKeditor->ToolbarSet=$this->toolbarSet;
        //$oFCKeditor->SiteRoot=$objConfig->getsiteRoot();
        
        $siteRootPath= str_replace('\\', '/', $sitePath['dirname']);
        $siteRootPath = preg_replace('/\/+/', '/', $siteRootPath);
        
        
        if (substr($siteRootPath, -1, 1) != '/') {
			$siteRootPath .= '/';
		}
        
        $oFCKeditor->SiteRoot = $siteRootPath;
        
        $oFCKeditor->Config['SkinPath'] = $sBasePath . 'editor/skins/default/' ;     
        $oFCKeditor->Config['CustomConfigurationsPath'] = $sBasePath . 'chisimba_config.js'  ;
        
        if ($this->context) {
            $oFCKeditor->Context = 'Yes';
        } else {
            $oFCKeditor->Context = 'No';
        }
       
        $oFCKeditor->Value = $this->value;
        
        //$this->setVar('pageSuppressXML', TRUE);
        
        $this->showFCKEditorWakeupJS();
        
        //return $oFCKeditor->CreateHtml();
        
        
        return '<span onmouseover="wakeUpFireFoxFckeditor(\''.$this->name.'\');">'.$oFCKeditor->CreateHtml().'</span>';
        // Addition for Testing Purposes
        // <div id="content_'.$this->name.'"></div>
    }
    
    /**
     * Method to load JS to fix FCKEditor refusing to focus
     * @author Tohir Solomons
     *         
     *         Taken from: http://www.tohir.co.za/2006/06/fckeditor-doesnt-want-to-focus-in.html
     */
    function showFCKEditorWakeupJS()
    {
        $this->appendArrayVar('headerParams', '
<script type="text/javascript">
    function wakeUpFireFoxFckeditor(fckEditorInstance)
    {
        try
        {
            var oEditor = FCKeditorAPI.GetInstance(fckEditorInstance);
            try
            {
                oEditor.MakeEditable();
            }
                catch (e) {}
            //oEditor.Focus();
        }
            catch (e) {}
    }
    
    function copyFCKData(fckEditorInstance)
    {
        try
        {
            var oEditor = FCKeditorAPI.GetInstance(fckEditorInstance);
            try
            {
                oEditor.UpdateLinkedField();
                // For Testing Purposes
                //document.getElementById(\'content_\'+fckEditorInstance).innerHTML = document.getElementById(fckEditorInstance).value;
            }
                catch (e) {}
            //oEditor.Focus();
        }
            catch (e) {}
            
    }
    
</script>');
    }
    
    /**
    * Method to show the tinyMCE Editor
    * @return string
    */
    function showTinyMCE()
    {      
    	$str = '';
    	$str =$this->getJavaScripts();
    	$str .='<form name="imgform"><input type="hidden" name="hiddentimg"/></form>';
    	$str .='<textarea id="'.$this->name.'" name="'.$this->name.'" rows="'.$this->rows.'" cols="'.$this->cols.'" style="width: 100%">'.$this->value.'</textarea>';
    	return   $str;
    }
    
    /**
    * Method to set the toolbar set to basic 
    * meaning that only the basic commands are available of the editor
    */
    function setBasicToolBar(){
        $this->toolbarSet = 'simple';
    }
    
    /**
    * Method to toolbar set to default 
    */
    function setDefaultToolBarSet(){
         $this->toolbarSet = 'advanced';
    }
    
    /**
    * Method to toolbar set to default without the save button
    */
    function setDefaultToolBarSetWithoutSave(){
         $this->toolbarSet = 'DefaultWithoutSave';
    }


    /**
    * Method to toolbar set to CMS Specific specific
    */
    function setCMSToolBar(){
         $this->toolbarSet = 'cms';
    }

    /**
    * Method to load the Content Templates
    */
    function loadContentTemplatesFromDB(){
         $this->toolbarSet = 'cms';
    }

    /**
    * Method to get the javascript files
    * @return string
    */
    public function getJavaScripts()
    {
    	$str = '
    			<script language="javascript" type="text/javascript" src="core_modules/htmlelements/resources/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
    			
    			
				<script language="javascript" type="text/javascript">
				
					tinyMCE.init({
						mode : "textareas",
						theme : "'.$this->toolbarSet.'",
						plugins : "style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,flash,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable",
						theme_advanced_buttons1_add_before : "save,newdocument,separator",
						theme_advanced_buttons1_add : "fontselect,fontsizeselect",
						theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,separator,forecolor,backcolor",
						theme_advanced_buttons2_add_before: "cut,copy,paste,pastetext,pasteword,separator,search,replace,separator",
						theme_advanced_buttons3_add_before : "tablecontrols,separator",
						theme_advanced_buttons3_add : "emotions,iespell,flash,advhr,separator,print,separator,ltr,rtl,separator,fullscreen",
						theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "left",
						theme_advanced_path_location : "bottom",
						content_css : "example_full.css",
					    plugin_insertdate_dateFormat : "%Y-%m-%d",
					    plugin_insertdate_timeFormat : "%H:%M:%S",
						extended_valid_elements : "hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
						external_link_list_url : "example_link_list.js",
						external_image_list_url : "example_image_list.js",
						flash_external_list_url : "example_flash_list.js",
						file_browser_callback : "fileBrowserCallBack",
						theme_advanced_resize_horizontal : false,
						theme_advanced_resizing : true
					});
				
					function fileBrowserCallBack(field_name, url, type, win) {
						// This is where you insert your custom filebrowser logic
						//alert("Example of filebrowser callback: field_name: " + field_name + ", url: " + url + ", type: " + type);
						mywindow = window.open ("'.$this->uri(array('action' => 'showmedia'), 'mediamanager').'",  "imagewindow","location=1,status=1,scrollbars=0,  width=200,height=200");  mywindow.moveTo(0,0);
						
						//alert(mywindow.document.forms[0].hideme.value);
						// Insert new URL, this would normaly be done in a popup
						win.document.forms[0].elements[hide'.$this->name.'].value = "'.$this->uri(array('action' => 'list'), 'mediamanager').'";
					}
				</script>
					';
    	$this->appendArrayVar('headerParams', $str);
    	//return $str;
    }
 }

?>
