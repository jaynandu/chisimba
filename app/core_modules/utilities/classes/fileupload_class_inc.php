<?php
/**
* Class fileupload based on a script by Harish Chauhan
* @package filemanager
* @author Paul Scott
*/

class fileupload
{
    var $objLanguage;
    var $directory_name;
    var $max_filesize;
    var $error;
    var $file_name;
    var $full_name;
    var $file_size;
    var $file_type;
    var $check_file_type;
    var $thumb_name;
    var $tmp_name;

  
    /**
    * Method the set the directory that we want to work with
    * @param $dir_name
    * @return bool True on success
    */
    function set_directory($dir_name = ".")
    {
    	$this->directory_name = $dir_name;
    }

    /**
    * Method to set max uploadable file size
    * @access public
    * @param $max_file
    * @return null
    */
    function set_max_size($max_file = 300000000000)
    {
    	$this->max_filesize = $max_file;
    }
    
    /**
    * Method to check for a directory
    * @access Public
    * @param void
    * @return true
    */
    function check_for_directory()
    {
        if (!file_exists($this->directory_name))
        {
            //if the dir does not exist create one
            mkdir($this->directory_name,0777);
        }
        //change the mode of the directory(set permissions) to world writeable
        @chmod($this->directory_name,0777);
    }

    /**
    * Method to catch errors
    */
    function error()
    {
    	return $this->error();
    }

    /**
    * Method to set the file size
    */
    function set_file_size($file_size)
    {
    	$this->file_size = $file_size;
    }

    /** 
    * Method to set the file type
    */
    function set_file_type($file_type)
    {
    	$this->file_type = $file_type;
    }
    
    /**
    * Method to get the file type
    */
    function get_file_type()
    {
    	return $this->file_type;
    }

    /** 
    * Method to set the temp filename
    */
    function set_temp_name($temp_name)
    {
    	$this->tmp_name = $temp_name;
    }

    /**
    * Method to set the final filename
    */
    function set_file_name($file)
    {
        $this->file_name = $file;
        $this->full_name = $this->directory_name."/".$file;
    }
    
/**
    * Method to upload the file...
    * @PARAMS : 
    * 	$uploaddir : Directory Name in which uploaded file is placed
    * 	NOTE: file input type field name should be set to fileupload
    * 	$rename : you may pass string or boolean 
    * 			 true : rename the file if it already exists and returns the renamed file name.
    * 			 String : rename the file to given string.
    * 	$replace =true : replace the file if it is already existing
    * 	$file_max_size : file size in bytes. 0 for default
    * 	$check_type : checks file type exp ."(jpg|gif|jpeg)"
    * 
    * @example UPLOAD::upload_file("temp","file",true,true,0,"jpg|jpeg|bmp|gif")
    * 
    * @return : On success it will return file name else return (boolean)false
    */
    
    function upload_file($uploaddir,$rename=null,$replace=false,$file_max_size=0,$check_type="")
    {
		if (!is_uploaded_file($_FILES['upload']['tmp_name'])) {
			throw new CustomException($this->objLanguage->languageText('mod_utilities_errorupload'));
		}
		else if ($_FILES['upload']['error'] != UPLOAD_ERR_OK) {
			throw new CustomException($objFileUpload->checkError($_FILES['file']['error']));
		}
        
		$this->set_file_name($_FILES['upload']['name']);
        $this->set_file_type($_FILES['upload']['type']);
        $this->set_file_size($_FILES['upload']['size']);
        $this->error=$_FILES['upload']['error'];
        $this->set_temp_name($_FILES['upload']['tmp_name']);
        $this->set_max_size($file_max_size);

        $this->set_directory($uploaddir);
        $this->check_for_directory();
        $filename = $_FILES['upload']['name'];
        $temp_name = $_FILES['upload']['tmp_name'];
              
        if(!empty($check_type))
        {
        	if(!eregi("\.($check_type)$",$filename))
        	{
        
            	$this->error(); 
            	return false;
            }
        }

        if(!is_bool($rename)&&!empty($rename))
        {
            $matches = NULL;
        	if(preg_match("/\..*+$/",$this->file_name,$matches))
            $this->set_file_name($rename.$matches[0]);
        }
        elseif($rename && file_exists($this->full_name))
        {
            if(preg_match("/\..*+$/",$this->file_name,$matches))
            $this->set_file_name(substr_replace($this->file_name,"_".rand(0, rand(0,99)),-strlen($matches[0]),0));
        }

        if(file_exists($this->full_name))
        {
        	if($replace)
            	@unlink($this->full_name);
        	else
        	{
            	$this->error=$this->objLanguage->languageText("word_file_exists");
            	return false;
        	}
        }

        
        $this->start_upload($filename,$temp_name,$uploaddir);
        
        	if($this->error!="") {
				echo $this->error;
        		return false;
			}
        	else
        		return $this->file_name;
    }

    /**
     * Function startupload
     * This is an internal function that is called by the upload function in filemanager
     * @param $filename, $temp_name $uploaddir
     * @return bool
     */
    function start_upload($filename,$temp_name,$uploaddir)
    {
         
        if(!isset($filename))
        	$this->error = FALSE;

    	if ($this->file_size <= 0)
        	$this->error = FALSE;
        
    	if ($this->file_size > $this->max_filesize && $this->max_filesize!=0)
        	$this->error = FALSE;

    	if ($this->error=="0")
    	{
         
            $destination= $uploaddir . $filename;

            move_uploaded_file($temp_name,$destination);
            
       }
    }

    function checkError($code)
    {
        return $this->objLanguage->languageText("mod_utilities_fileuploaderror_$code");
    }
}//end class
?>
