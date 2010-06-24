<?php
/**
 *
 * Skin elements render skin chooser
 *
 * Skin elements render skin chooser as a dropdown that can be used
 * in a block.
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   skin
 * @author    Derek Keats <derek.keats@wits.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
*
* Skin elements render skin chooser
*
* Skin elements render skin chooser as a dropdown that can be used
* in a block.
*
* @package   skin
* @author    Derek Keats <derek.keats@wits.ac.za>
*
*/
class skinchooser extends object
{
    /**
    *
    * @var string object Hold configuration reading object
    * @access public
    *
    */
    public $objConfig;

    /*
    *
    * @var string Property to hold the skin root
    * @access public
    *
    */
    public $skinRoot;

    /*
    *
    * @var string object Hold language object
    * @access public
    *
    */
    public $objLanguage;

    /**
    *
    * Intialiser for the skin chooser
    * @access public
    *
    */
    public function init()
    {
        // Load the form building classes used by the dropdown.
        $this->loadClass('form','htmlelements');
        $this->loadClass('dropdown','htmlelements');
        $this->loadClass('button','htmlelements');
        // Load the config object to get the directory locations.
        $this->objConfig = $this->getObject('altconfig','config');
        // Get the location of the skin root directory.
        $this->skinRoot = $this->objConfig->getskinRoot();
        // Load an instance of the language object for text rendering.
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
     *
     * Render the dropdown skin chooser
     *
     * @return string Form with dropdown
     * @access public
     *
     */
    public function show()
    {
        return $this->showAjax();/*
        //replace withthe name of the current script
        $script=$_SERVER['PHP_SELF'];
        $objNewForm = new form('ignorecheck',$script);
        $objDropdown = new dropdown('skinlocation');
        $objDropdown->extra = "onchange =\"document.forms['ignorecheck'].submit();\"";
        $skins = array();
        // Get all the skins as an array
        $dirList = $this->getAllSkins();
        // Sort the array of skins alphabetically
        asort($dirList);
        // Loop and add them to the dropdown
        foreach ($dirList as $element=> $value) {
           $objDropdown->addOption($element,$value);
        }
        $objNewForm->addToForm($this->objLanguage->languageText('phrase_selectskin').":<br />\n");
        // Set the current skin as the default selected skin
        $objDropdown->setSelected($this->getSession('skin'));
        $objDropdown->cssClass = 'coursechooser';
        $objNewForm->addToForm($objDropdown->show());
        return $objNewForm->show();*/
    }

     /**
     *
     * Render the dropdown skin chooser so that it is processed
     * by the skinselect module, for use with Ajax
     *
     * @return string Form with dropdown
     * @access public
     *
     */
    public function showAjax()
    {
        $script = $this->uri(array('action' => 'ajaxsave'), 'skinselect');
        $objNewForm = new form('selectskin',$script);
        $objDropdown = new dropdown('skinlocation');
        $objDropdown->extra = "onchange =\"document.forms['selectskin'].submit();\"";
        $skins = array();
        // Get all the skins as an array
        $dirList = $this->getAllSkins();
        // Sort the array of skins alphabetically
        asort($dirList);
        // Loop and add them to the dropdown
        foreach ($dirList as $element=> $value) {
           $objDropdown->addOption($element,$value);
        }
        $objNewForm->addToForm($this->objLanguage->languageText('phrase_selectskin').":<br />\n");
        // Set the current skin as the default selected skin
        $objDropdown->setSelected($this->getSession('skin'));
        $objDropdown->cssClass = 'coursechooser';
        $objNewForm->addToForm($objDropdown->show());
        return $objNewForm->show();
    }

    /**
    *
    * Get all the skins as an array by using glob with only directories
    * (GLOB_ONLYDIR) and build an array of skins and their names.
    *
    * @return string array An array of skins
    * @access public
    *
    */
    public function getAllSkins()
    {
        //loop through the folders and build an array of available skins
        $basedir=$this->objConfig->getsiteRootPath().$this->skinRoot;
        // Compile an array of the skin names.
        $dirList = array();
        $directories = glob($basedir.'*', GLOB_ONLYDIR);
        foreach ($directories as $directory) {
            $key = basename($directory);
            if (file_exists($directory.'/skin.conf')) {
                $conf = $this->readConf($directory.'/skin.conf');
                $dirList[$key] = $conf['SKIN_NAME'];
            } elseif (file_exists($directory.'/skinname.txt')) {
                $dirList[$key] = trim(file_get_contents($directory.'/skinname.txt'));
            } else {
                $dirList[$key] = $key;
            }
        }
        return $dirList;
    }

    /**
    *
    * Reads the 'skin.conf' file provided by the skin
    * These are then returned as an associative array.
    *
    * @param  string  $filepath  path and filename of file.
    * @param  boolean $useDefine determine use of defined constants
    * @return array   $registerdata all the info from the register.conf file
    *
    */
    public function readConf($filepath,$useDefine=FALSE) {
        try {
            if (file_exists($filepath)) {
                $registerdata=array();
                $lines=file($filepath);
                $cats = array();
                foreach ($lines as $line) {
                    preg_match('/([^:]+):(.*)/',$line,$params);
                    $params[0] =isset($params[1])? trim($params[1]) : '';
                    $params[1] =isset($params[2])? trim($params[2]) : '';
                    $registerdata[$params[0]]=rtrim($params[1]);
                } //    end of foreach
                return ($registerdata);
            } else {
                return FALSE;
            } // end of if
        } catch (Exception $e) {
            throw new customException($e->getMessage());
            exit(0);
        }
    }
}
?>