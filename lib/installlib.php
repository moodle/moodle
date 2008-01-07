<?php
/**
 * Functions to support installation process
 * @author Dilan
 * 
 */
//========================================================================================//
/**
 * Check the validity of the language
 * return true or false
 *
 * @param string $lang (short code for language)
 * @return true/false
 */
function valid_language($lang) {
    global $DEFAULT;
    $langdir = dir($DEFAULT['dirroot'].'/install/lang');
    $i=0;
    $validllangs = array();

    while (false !== ($file=$langdir->read())) {
        if ($file[0] != '.' ) {
            $validllangs[$i++]=$file;
        }
    }
    if (in_array($lang,$validllangs)) {
        return true;
    } else {
        return false;
    }
}
//========================================================================================//
/**
 * Read from array of language strings and return a array of string elements in which 
 * both values and keys are set to input array's key 
 *
 * @param array $lang string elements
 * @return array of string element
 */
function get_short_codes ($lang = array()) {
    $short_codes = array();

    foreach ($lang as $key => $value) {
        $short_codes[$key] = $key;
    }
    return  $short_codes;
}
//========================================================================================//
/**
 * Check value for valid yes/no argument
 * Return true or false
 *
 * @param string $value
 * @return true/false
 */
function valid_yes_no($value){
    $valid=array('yes','y','n','no');
    $value=strtolower($value);

    if (in_array($value,$valid)) {
        if ($value[0]=='y') {
            return true;
        } else if ($value[0]=='n') {
            return true;
        }
    } else {
        return false;
    }
}
//========================================================================================//
/**
 * Can value have a valid integer in the given range
 * Return true or false
 * @link valid_param()
 *
 * 
 * @param mixedtype $value
 * @param int $start
 * @param int $end
 * @return true/false
 */
function valid_int_range($value,$start,$end) {
    if (valid_param($value,PARAM_INT)) {
        if ($value < $end && $value > $start) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Take a value and and check it with the given set of values
 * If value if found in the set return true. False otherwise
 *
 * @param mixed type $value
 * @param array  $set of valid elements
 * @return boolean
 */

function valid_element($value,$set) {
    if(!empty($set)) {
        //convert all the elements from set to lower case
        foreach ($set as $key=>$opt) {
            $set[$key]=strtolower($opt);
        }
        $value=strtolower($value);
        if (in_array($value,$set)) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Take a value and Type of the value 
 * If value match the type return true, false otherwise
 * uses {@link clean_param()} in moodlelib.php
 * @param mixedtype $value
 * @param int $type
 * @return boolean
 */
function valid_param($value,$type){
    $clean_val = clean_param($value,$type);
    if ($clean_val == $value) {
        return true;
    }else {
        return false;
    }
}
//========================================================================================//
/**
 * Creat long arument list for PEAR method invocation using LONGOPTOIN array
 *
 * @param long option array $long_opt
 * @return PEAR method compatible long option array 
 */
function create_long_options($long_opt) {
    $opt=array();
    $i=0;
    if (is_array($long_opt)) {
        foreach ($long_opt as $key=>$value) {
            if ($value == CLI_VAL_REQ) {
                $opt[$i++]=$key.'=';
            } else if ($value == CLI_VAL_OPT) {
                $opt[$i++]=$key.'==';
            }

        }
    }
    return $opt;
}
//========================================================================================//
/**
 * This funtion return an array of options with option as key containing the value of 
 * respective option
 *
 * @param array of option arguments as defined by PEAR GetOpt calss $opt
 * @return return a options arguments with options as keys and values as respective value for key
 */
function get_options($opt=array()) {

    global $LONG_OPTIONS;
    $ret_arr=array();

    //get the options from the defined list of arguments
    if (!empty($opt[0]) && is_array($opt[0])) {

        foreach ($opt[0] as $key=>$value) {

            if (substr($value[0],0,2)=='--') {			//if the argument is a long option
                $input_option=substr($value[0],2);
            } else if (substr($value[0],0,1)=='-'){		//if the argument is a short option
                $input_option=substr($value[0],1);
            }

            //check with valid set of options
            if (in_array($input_option,$LONG_OPTIONS)) {
                $ret_arr[$input_option]=$value[1];
            }
        }

    }
    //return array
    return $ret_arr;

}

//========================================================================================//

/**
 * This function returns a list of languages and their full names. 
 * The list of available languages is fetched from install/lang/xx/installer.php
 * and it's used exclusively by the installation process 
 * @return array An associative array with contents in the form of LanguageCode => LanguageName
 */
function get_installer_list_of_languages() {

    global $CFG;

    $languages = array();

    /// Get raw list of lang directories
    $langdirs = get_list_of_plugins('install/lang');
    asort($langdirs);
    /// Get some info from each lang
    foreach ($langdirs as $lang) {
        if (file_exists($CFG->dirroot .'/install/lang/'. $lang .'/installer.php')) {
            include($CFG->dirroot .'/install/lang/'. $lang .'/installer.php');
            if (substr($lang, -5) == '_utf8') {   //Remove the _utf8 suffix from the lang to show
                $shortlang = substr($lang, 0, -5);
            } else {
                $shortlang = $lang;
            }
            //if ($lang == 'en') {  //Explain this is non-utf8 en
            //    $shortlang = 'non-utf8 en';
            //}
            if (!empty($string['thislanguage'])) {
                $languages[$lang] = $string['thislanguage'] .' ('. $lang .')';
            }
            unset($string);
        }
    }
    /// Return array
    return $languages;
}
//=========================================================================//
/**
 * Validate options values 
 *
 * @param array $options
 */
function validate_option_values($options){
    $values=array();
    $i=0;
    foreach ($options as $val) {
        $values[$i++]=$val;
    }
    if (isset($values['lang'])) {
        if (!valid_language($INSTALL['lang'])) {
            console_write(STDERR,'invalidvalueforlanguage');
        }
    }
    if (isset($values['webdir'])) {
        /**
		 * @todo check valid directory path
		 */
    }
    if (isset($values['webaddr'])) {
        /**
		 * @todo check valid http url
		 */
    }
    if (isset($values['moodledir'])) {
        /**
		 * @todo check valid directory path
		 */
    }
    if (isset($values['datadir'])) {
        /**
		 * @todo check valid directory path
		 */
    }
    if (isset($values['dbtype'])) {
        $dbtypes=array('mysql','oci8po','postgres7','mssql','mssql_n','odbc_mssql');
        if (!in_array($values['dbtype'],$dbtypes)) {
            console_write(STDERR,'invaliddbtype');
        }
    }
    if (isset($values['dbhost'])) {
        /**
		 * @todo check host?
		 */
    }
    if (isset($values['dbname'])) {
        /**
		 * @todo check name for valid ones if required
		 */
    }
    if (isset($values['dbuser'])) {
        /**
		 * @todo check validity of db user if required
		 */
    }
    if (isset($values['dbpass'])) {
        /**
		 * @todo check validity of database password if required 
		 */
    }
    if (isset($values['prefix'])) {
        /**
		 * @todo check for valid prefix
		 */
    }
    if (isset($values['sitefullname'])) {
        /**
		 * @todo check for valid fullname for site
		 */
    }
     if (isset($values['siteshortname'])) {
        /**
		 * @todo check for valid short name for site
		 */
    }
     if (isset($values['sitesummary'])) {
        /**
		 * @todo check for valid summary
		 */
    }
     if (isset($values['sitenewsitems'])) {
        /**
		 * @todo check for valid news items
		 */
    }
    if (isset($values['adminfirstname'])) {
        /**
         * @todo check for valid admin first name
         */
    }
     if (isset($values['adminlastname'])) {
        /**
		 * @todo check for valid last name
		 */
    }
     if (isset($values['adminusername'])) {
        /**
		 * @todo check for valid username
		 */
    }
     if (isset($values['adminpassword'])) {
        /**
		 * @todo check for valid password
		 */
    }
     if (isset($values['adminemail'])) {
        /**
		 * @todo check for valid email
		 */
    }
    if (isset($values['verbose'])) {
        if(!valid_int_range($values['verbose'],CLI_NO,CLI_FULL)){
            console_write(STDERR,'invalidverbosevalue');
        }
    }
    if (isset($values['interactivelevel'])) {
        if(!valid_int_range($values['verbose'],CLI_NO,CLI_FULL)){
            console_write(STDERR,'invalidinteractivevalue');
        }
    }

    if (isset($values['help'])) {
        /**
		 * @todo  nothing really
		 */
    }
}
//=========================================================================//
/**
 * Write to standard out and error with exit in error
 *
 * @param standard out/err $stream
 * @param string  $identifier
 * @param name of module $module
 */
function console_write($stream,$identifier,$module='install',$use_string_lib=true) {
    if ($use_string_lib) {
        fwrite($stream,get_string($identifier,$module));
    } else {
        fwrite($stream,$identifier);
    }
    if ($stream == STDERR) {
        fwrite($stream,get_string('aborting',$module));
        die;
    }
}
//=========================================================================//
/**
 * Read a mixed type
 *
 * @param stream $from
 * @param int $size
 * @return mixed type
 */
function read($from=STDIN,$size=1024) {
    $input= trim(fread($from,$size));
    return $input;
}
/**
 * Read an integer
 *
 * @return integer
 */
function read_int() {
    $input=read();
    if (valid_param($input,PARAM_INT)) {
        return $input;
    } else {
        console_write(STDERR,'invalidint');
    }
}
//=========================================================================//
/**
 * Read and integer value within range
 *
 * @param int $start
 * @param int $end
 * @return int
 */
function read_int_range($start,$end) {
    $input=read_int();
    if (valid_int_range($input,$start,$end)) {
        return $input;
    } else {
        console_write(STDERR,'invalidintrange');
    }

}
//=========================================================================//
/**
 * Read yes/no argument
 *
 * @return string yes/no
 */
function read_yes_no() {
    $input=strtolower(read());
    if (valid_yes_no($input)) {
        if ($input[0]=='y') {
            return 'yes';
        } else if($input[0]=='n') {
            return 'no';
        }
    } else {
        console_write(STDERR,'invalidyesno');
    }
}

//=========================================================================//
/**
 * Read a boolean parameter from the input
 *
 * @return boolean
 */
function read_boolean(){
    $input=read_yes_no();
    return clean_param($input,PARAM_BOOL);

}
//=========================================================================//
/**
 * Reading an element from a given set
 *
 * @param mixed type array $set
 * @return mixed type
 */
function read_element($set=array()) {
    $input=read();
    if (valid_element($input,$set)) {
        return $input;
    } else {
        console_write(STDERR,'invalidsetelement');
    }
}
//=========================================================================//
function read_url() {
    $input = read();
    $localhost = false;
    if ( strpos($input,'localhost') !== false) {
        $input = str_replace('localhost','127.0.0.1',$input);
        $localhost=true;
    }
    if (valid_param($input,PARAM_URL)) {
        if ($localhost) {
            return str_replace('127.0.0.1','localhost',$input);
        } else {
            return  $input;
        }
    } else {
        console_write(STDERR,'invalidurl');
    }

}
//=========================================================================//
/**
 * Enter description here...
 *
 * @return string
 */
function read_dir() {
    $input = read();
    return  $input;
}
//===========================================================================//
/**
 * Print compatibility message to standard out, and errors to standard error
 *
 * @param boolean $success
 * @param string $testtext
 * @param string $errormessage
 * @param boolean $caution
 * @param boolean $silent
 * @return boolean
 */
function check_compatibility($success, $testtext,$errormessage,$caution=false,$silent=false) {
    if ($success) {
        if (!$silent) {
            console_write(STDOUT,get_string('pass', 'install'),'',false);
        }
    } else {
        if ($caution) {
            if (!$silent) {
                console_write(STDOUT,get_string('caution', 'install'),'',false);
            }
        } else {
            console_write(STDOUT,get_string('fail', 'install'),'',false);
            console_write(STDERR,$errormessage,'',false);
        }
    }
    if (!$silent) {
        console_write(STDOUT,"\t\t",'',false);
        console_write(STDOUT,$testtext,'',false);
        console_write(STDOUT,"\n",'',false);
    }
    return $success;
}

//==========================================================================//
/**
 * Get memeory limit
 *
 * @return int
 */
function get_memory_limit() {
    if ($limit = ini_get('memory_limit')) {
        return $limit;
    } else {
        return get_cfg_var('memory_limit');
    }
}

//==========================================================================//
/**
 * Check memory limit
 *
 * @return boolean
 */
function check_memory_limit() {

    /// if limit is already 40 or more then we don't care if we can change it or not
    if ((int)str_replace('M', '', get_memory_limit()) >= 40) {
        return true;
    }

    /// Otherwise, see if we can change it ourselves
    @ini_set('memory_limit', '40M');
    return ((int)str_replace('M', '', get_memory_limit()) >= 40);
}

//==========================================================================//
/**
 * Check php version
 *
 * @return boolean
 */
function inst_check_php_version() {
    if (!check_php_version("4.3.0")) {
        return false;
    } else if (check_php_version("5.0.0")) {
        return check_php_version("5.1.0"); // 5.0.x is too buggy
    }
    return true; // 4.3.x or 4.4.x is fine
}
/**
 * Print environment status to standard out
 *
 * @param array $env, of type object
 */
function print_environment_status($env = array()) {
    console_write(STDOUT,"Status\t\tInfo\t\tPart\n\r",'',false);
    foreach ( $env as  $object) {

        if ($object->status == 1 ) {
            console_write(STDOUT,'ok','',false);
        } else {
            console_write(STDOUT,'fail','',false);
        }
        console_write(STDOUT,"\t\t",'',false);
        console_write(STDOUT,$object->info,'',false);
        console_write(STDOUT,"\t\t",'',false);
        console_write(STDOUT,$object->part,'',false);
        console_write(STDOUT,"\n\r",'',false);
    }
}

/**
 * Print environment status to standard out
 *
 * @param array $env, of type object
 */
function print_environment_status_detailed($env = array()) {
    console_write(STDOUT,"Status\t\tLevel\t\tCurrent ver\tRequired ver\t\tPart\t\tInfo\n\r",'',false);
    foreach ( $env as  $object) {

        if ($object->status == 1 ) {
            console_write(STDOUT,'ok ','',false);
        } else if ($object->errorcode != 0) {
            console_write(STDOUT,'fail ','',false);
        } else {
            console_write(STDOUT,'----','',false);
        }
        console_write(STDOUT,"\t\t",'',false);
        console_write(STDOUT,$object->level,'',false);
        console_write(STDOUT,"\t\t",'',false);
        console_write(STDOUT,$object->current_version,'',false);
        console_write(STDOUT,"\t",'',false);
        console_write(STDOUT,$object->needed_version,'',false);
        console_write(STDOUT,"\t\t",'',false);
        console_write(STDOUT,$object->part,'',false);
        console_write(STDOUT,"\t\t",'',false);
        console_write(STDOUT,$object->info,'',false);
        console_write(STDOUT,"\n\r",'',false);
    }
}
/**
 * Print a new line in the standard output
 *
 */

function print_newline() {
    console_write(STDOUT,'newline','install');
}
?>
