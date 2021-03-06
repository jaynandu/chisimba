<?php

/**
 * Context Object
 *
 * This class contains required functionality for creating contexts
 * and retrieve context details
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
 * @package   context
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* -------------------- dbTable class ---------------- */
// security check - must be included in all scripts
if (!/**
         * Description for $GLOBALS
         * @global entry point $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS ['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Context Object
 *
 * This class contains required functionality for creating contexts
 * and retrieve context details
 *
 * @category  Chisimba
 * @package   context
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class dbcontext extends dbTable {

    /**
     * The user Object
     *
     * @var object $objUser
     */
    public $objUser;

    /**
     * Initialize by send the table name to be accessed
     */
    public function init() {
        parent::init('tbl_context');
        $this->table = 'tbl_context';
        $this->objUser = $this->getObject('user', 'security');
    }

    /**
     * Method to get the details for a given
     * context
     *
     * @param  string    $contextCode
     * @return array
     * @access public
     */
    public function getContextDetails($contextCode) {
        return $this->getContext($contextCode);
    }

    /**
     * Method to get the details for a given
     * context
     *
     * @param  string    $contextCode
     * @return array
     * @access public
     */
    public function getContext($contextCode) {
        return $this->getRow('contextcode', $contextCode);
    }

    /**
     * Method to get a field from the
     * current table
     *
     * @param  $fiedname    string : the name of the field
     * @param  $contextCode int    : the context Code
     * @return string       | bool : The field value or FALSE when not found
     * @access public
     */
    public function getField($fieldname, $contextCode = NULL) {
        if (!isset($contextCode)) {
            $contextCode = $this->getContextCode();
        }

        $line = $this->getRow('contextcode', $contextCode);

        $fieldname = strtolower($fieldname);

        if ($line [$fieldname]) {
            return $line [$fieldname];
        } else {
            return FALSE;
        }
    }

    /**
     * Method to create a context
     *
     * @param string $contextCode Context Code
     * @param string $title Title of the Context - Menu and Title is the same
     * @param string $status Status of the Context
     * @param string access Access Settings for the Context
     * @param string $about About the Context
     * @param string $showcomment Enable or Disable Comments on course pages
     * @param string $alerts Enable or Disable alerts
     *
     * @return boolean Result of adding a context
     */
    public function createContext(
    $contextCode, $title, $status = 'Published', $access = 'Private', $about = NULL, $goals=FALSE, $showcomment='Y', $alerts = '', $canvas='') {

        $contextCode = preg_replace('/\W*/', '', $contextCode);
        $contextCode = strtolower($contextCode);

        if (trim($title) == '') {
            $title = $contextCode;
        }

        //check if there is an entry in the database
        if ($this->valueExists('contextcode', $contextCode)) {
            // If Yes, do not create
            return FALSE;
        } else {
            $data = array('contextcode' => $contextCode,
                'title' => $title,
                'menutext' => $title,
                'access' => $access,
                'alerts' => $alerts,
                'status' => $status,
                'about' => $about,
                'userid' => $this->objUser->userId(),
                'dateCreated' => date("Y-m-d H:i:s"),
                'updated' => date("Y-m-d H:i:s"),
                'lastupdatedby' => $this->objUser->userId(),
                'goals' => $goals,
                'showcomment' => $showcomment,
                'canvas' => $canvas,
                'lastaccessed' => date("Y-m-d H:i:s"));

            // Insert Record
            $result = $this->insert($data);

            // If Successful
            if ($result) {
                $this->_indexContext($contextCode);

                // Create Groups
                $contextGroups = $this->getObject('managegroups', 'contextgroups');
                $contextGroups->createGroups($contextCode, $title);


                // Join Context
                $this->joinContext($contextCode);
            }
            // Return Result
            return $result;
        }
    }

    /**
     * Method to update a context
     *
     * @param string $contextCode Context Code
     * @param string $title Title of the Context - Menu and Title is the same
     * @param string $status Status of the Context
     * @param string access Access Settings for the Context
     * @param string $about About the Context
     * @param string $showcomment Enable or Disable Comments on course pages
     *
     * @return boolean Result of Update
     */
    public function updateContext(
    $contextCode, $title=FALSE, $status=FALSE, $access=FALSE, $about=FALSE, $goals=FALSE, $showcomment=FALSE, $alerts=FALSE, $lastaccessed=FALSE, $canvas=FALSE) {
        $fields = array();

        $fields['updated'] = date('Y-m-d H:i:s');
        $fields['lastupdatedby'] = $this->objUser->userId();

        if ($title !== FALSE) {
            $fields['title'] = $title;
            $fields['menutext'] = $title;
        }
        if ($status !== FALSE) {
            $fields['status'] = $status;
        }
        if ($access !== FALSE) {
            $fields['access'] = $access;
        }
        if ($about !== FALSE) {
            $fields['about'] = $about;
        }
        if ($goals !== FALSE) {
            $fields['goals'] = $goals;
        }
        if ($showcomment !== FALSE) {
            $fields['showcomment'] = $showcomment;
        }
        if ($lastaccessed !== FALSE) {
            $fields['lastaccessed'] = $lastaccessed;
        }
        if ($alerts !== FALSE) {
            $fields['alerts'] = $alerts;
        }
        if ($canvas !== FALSE) {
            $fields['canvas'] = $canvas;
        }

        $result = $this->update('contextcode', $contextCode, $fields);

        if ($result) {
            $this->_indexContext($contextCode);
        }

        return $result;
    }

    /**
     * Method to update the about text of a context
     *
     * @param  string $contextCode The context code
     * @param  string $about The about text
     * @return boolean
     * @access public
     */
    public function updateAbout($contextCode, $about) {
        $fields = array();
        $fields['about'] = $about;
        $result = $this->update('contextcode', $contextCode, $fields);

        return $result;
    }

    /**
     * Method to update the goals text of a context
     *
     * @param  string $contextCode The context code
     * @param  string $about The about text
     * @return boolean
     * @access public
     */
    public function updateGoals($contextCode, $goals) {
        $fields = array();
        $fields['goals'] = $goals;
        $result = $this->update('contextcode', $contextCode, $fields);
        return $result;
    }

    /**
     * Method to update the goals text of a context
     *
     * @param  string $contextCode The context code
     * @return boolean
     * @access public
     */
    public function updateLastAccessed($contextCode) {
        $fields = array();
        $fields['lastaccessed'] = date('Y-m-d H:i:s');
        $result = $this->update('contextcode', $contextCode, $fields);
        return $result;
    }

    /**
     * Method to allow users to enter a context
     *
     * @param string $contextCode Context user want to enter
     * @return boolean Result of entering context
     */
    public function joinContext($contextCode = '') {
        if ($contextCode == '') {
            $contextCode = $this->getParam('contextCode');
        }

        if (!isset($contextCode)) {
            $contextCode = $this->getParam('context_dropdown');
        }

        if (isset($contextCode)) {
            $this->leaveContext();
            $line = $this->getRow('contextCode', $contextCode);

            if ($line == FALSE) {
                return FALSE;
            }

            if ($line ['access'] == 'Private') {
                $objUserContext = $this->getObject('usercontext');
                if (!$objUserContext->isContextMember($this->objUser->userId(), $contextCode)) {
                    if (!$this->objUser->isAdmin()) {
                        return FALSE;
                    }
                }
            }

            $this->setSession('contextId', $line ['id']);
            $this->setSession('contextCode', $contextCode);
            $results = $this->updateLastAccessed($contextCode);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to leave the current context
     *
     * @access public
     */
    public function leaveContext() {
        $this->unsetSession('contextCode');
        $this->unsetSession('contextId');
        $this->unsetSession('contextTitle');
        $this->unsetSession('contextmenuText');
        $this->unsetSession('contextabout');
        $this->unsetSession('contextIsActive');
        $this->unsetSession('contextIsClosed');
        $this->unsetSession('contextDateCreated');
        $this->unsetSession('contextCreatorId');

        // Unset Workgroup Session If it Exists
        $objModule = $this->getObject('modules', 'modulecatalogue');
        if ($objModule->checkIfRegistered('workgroup', 'workgroup')) {
            $objDbWorkgroup = $this->getObject('dbWorkgroup', 'workgroup');
            $objDbWorkgroup->unsetWorkgroupId();
        }
    }

    /**
     * Method to check whether a context exists
     *
     * @param string $contextCode Context Code to check
     * @return boolean
     */
    public function contextExists($contextCode) {
        return $this->valueExists('contextcode', $contextCode);
    }

    /**
     * Method to retrieve the contextCode from the Session Variable
     *
     * @return contextCode
     * @access public
     */
    public function getContextCode() {
        return $this->getSession('contextCode');
    }

    /**
     * Method to get the contextId
     *
     * @return string
     * @access public
     */
    public function getContextId() {
        return $this->getSession('contextId');
    }

    /**
     * Method to get the Title of course that user is currently logged into
     *
     * @access public
     * @return context Title
     */
    public function getTitle($contextCode = NULL, $useSession = true) {
        if (!isset($contextCode) || $useSession) {
            $contextCode = $this->getSession('contextCode');
        }

        return $this->getField('title', $contextCode);
    }

    /**
     * Method to get the MenuText
     *
     * @param  string $contextCode : The contextCode
     * @return array
     * @access public
     */
    public function getMenuText($contextCode = NULL) {
        if (!isset($contextCode)) {
            $contextCode = $this->getSession('contextCode');
        }

        return $this->getField('menutext', $contextCode);
    }

    /**
     * Method to get the About
     *
     * @param  string $contextCode : The contextCode
     * @return array
     * @access public
     */
    public function getAbout($contextCode = NULL) {
        if (!isset($contextCode)) {
            $contextCode = $this->getSession('contextCode');
        }

        return $this->getField('about', $contextCode);
    }

    /**
     * Method to get the Goals
     *
     * @param  string $contextCode : The contextCode
     * @return array
     * @access public
     */
    public function getGoals($contextCode = NULL) {
        if (!isset($contextCode)) {
            $contextCode = $this->getSession('contextCode');
        }

        return $this->getField('goals', $contextCode);
    }

    /**
     * Method to check if one is in a context
     *
     * @access public
     * @return boolean
     */
    public function isInContext() {
        if ($this->getContextCode()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to return a list all of courses
     *
     * @return array
     * @access public
     */
    public function getListOfContext() {
        return $this->getAll(' ORDER BY title');
    }

    /**
     * Method to return an array of Recently Active Contexts
     *
     * @param integer $numOfRecords The number of records to return
     * @return array
     * @access public
     *
     */
    public function getArrayOfMostRecentlyActiveContexts($numOfRecords=6)
    {
        $sql = "WHERE status!='Unpublished' ORDER BY lastaccessed DESC, menutext ASC LIMIT {$numOfRecords}";
        return $this->getAll($sql);
    }

    /**
     * Method to return an array of Public Contexts
     *
     * @param integer $numOfRecords The number of records to return
     * @return array
     * @access public
     *
     */
    public function getArrayOfPublicContexts($numOfRecords=6)
    {
        $sql = "WHERE access='Public' AND status!='Unpublished' ORDER BY datecreated, menutext LIMIT {$numOfRecords}";
        return $this->getAll($sql);
    }

    /**
     * Method to return an array of Open Contexts
     *
     * @param integer $numOfRecords The number of records to return
     * @return array
     * @access public
     *
     */
    public function getArrayOfOpenContexts($numOfRecords=6)
    {
        $sql = "WHERE access='Open' AND status!='Unpublished' ORDER BY  datecreated, menutext LIMIT {$numOfRecords}";
        return $this->getAll($sql);
    }

    /**
     * Method to return an array of Private Contexts
     *
     * @param integer $numOfRecords The number of records to return
     * @return array
     * @access public
     *
     */
    public function getArrayOfPrivateContexts($numOfRecords=6)
    {
        $sql = "WHERE access='Private' AND status!='Unpublished' ORDER BY  datecreated, menutext LIMIT {$numOfRecords}";
        return $this->getAll($sql);
    }

    public function getArrayOfMostActiveContexts($numOfRecords=6)
    {
        $sql = 'SELECT *, count(a.id ) FROM `tbl_activity` AS a '
          . ' LEFT JOIN `tbl_context` AS c ON a.contextcode=c.contextcode '
          . ' WHERE a.module = \'context\' '
          . ' AND a.contextcode IS NOT NULL '
          . ' GROUP BY a.contextcode LIMIT ' . $numOfRecords;
        return $this->getArray($sql);
    }

    /**
     * Method to return a list of public courses
     *
     * @return array
     * @access public
     */
    public function getListOfPublicContext() {
        //return $this->getAll ( "WHERE access='Open' OR access='Public' ORDER BY menutext" );
        $objUserContext = $this->getObject('usercontext', 'context');
        // Get all user contents
        $sql = "WHERE access='Open' OR access='Public' AND status!='Unpublished'  ORDER BY menutext";

        $publicCourses = $this->getAll($sql);


        if ($this->objUser->isLoggedIn()) {
            $contexts = $objUserContext->getUserContext($this->objUser->userId());
            $tmp = array();
            $row = 0;
            foreach ($contexts as $con) {
                $data = $this->getContextDetails($con);
                $tmp[$row] = array('contextcode' => $con, 'menutext' => $data['title']);
                $row++;
            }
            //then add public courese
            foreach ($publicCourses as $p) {
                $data = $this->getContextDetails($p['contextcode']);
                $tmp[$row] = array('contextcode' => $p['contextcode'], 'menutext' => $data['title']);
                $row++;
            }

            return $tmp;
        } else {
            return $publicCourses;
        }
    }

    /**
     * Method to delete a context
     *
     * @param  string $contextCode: The Context Code
     * @return boolean
     * @access public
     */
    public function deleteContext($contextCode) {
        $result = $this->delete('contextCode', $contextCode);

        if ($result) {
            //delete groups
            $contextGroups = $this->getObject('manageGroups', 'contextgroups');
            $contextGroups->deleteGroups($contextCode);
        }

        // Remove from Search Results
        $objIndexData = $this->getObject('indexdata', 'search');
        $objIndexData->removeIndex('context_contextcode_' . $contextCode);

        // Clear List of Modules
        $objContextModules = $this->getObject('dbcontextmodules');
        $objContextModules->deleteModulesForContext($contextCode);

        // Remove Context Blocks
        $objContextBlocks = $this->getObject('dbcontextblocks');
        $objContextBlocks->removeContextBlocks($contextCode);

        return $result;
    }

    /**
     * Method to archive a context - at the moment it deletes a it, not archives
     *
     * Done this way because of method call at the moment
     *
     * @param  string $contextCode: The Context Code
     * @return boolean
     * @access public
     */
    public function archiveContext($contextCode) {
        return $this->deleteContext($contextCode);
    }

    /**
     * Method to search a context
     */
    public function searchContext($search) {
        return $this->getAll(" WHERE title LIKE '%{$search}%' OR contextcode LIKE '%{$search}%' OR about LIKE '%{$search}%' ORDER BY title");
    }

    /**
     * Context search method
     */
    public function getContextStartingWith($letter, $limit = 10, $page = 1) {
        return $this->getAll(" WHERE title LIKE '{$letter}%' and access != 'Private' AND status!='Unpublished' ORDER BY title  ");
    }

    /**
     * returns canvas name for this context
     * @return <type>
     */
    public function getCanvasName() {

        return $this->getField("canvas");
    }

    /**
     * Method to index a context
     */
    private function _indexContext($contextCode) {
        $context = $this->getContext($contextCode);
        if ($context != FALSE) {
            $objIndexData = $this->getObject('indexdata', 'search');

            $docId = 'context_contextcode_' . $context ['contextcode'];
            $docDate = date('Y-m-d H:M:S');
            $url = $this->uri(array('action' => 'joincontext', 'contextcode' => $context ['contextcode']), 'context');
            $title = $context ['title'];
            $contents = $context ['title'] . ' ' . $context ['about'];
            $teaser = $context ['about'];
            $userId = $context ['userid'];
            $alerts = $context ['alerts'];
            $canvas = $context ['canvas'];
            $module = 'context';

            // Todo - Set permissions on entering course, e.g. iscontextmember.
            $permissions = NULL;

            if (strtolower($context ['access']) == 'private') {
                $permissions = 'iscontextmember';
            }

            if (strtolower($context ['status']) == 'unpublished') {
                $permissions = 'iscontextlecturer';
            }

            $extra = array('status' => $context ['status'], 'access' => $context ['access'], 'contextcode' => $context ['contextcode'], 'alerts' => $context ['alerts'], 'canvas' => $context ['canvas']);

            $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, NULL, 'root', NULL, $permissions, NULL, NULL, $extra);
        }
    }

}

?>