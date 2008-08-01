<?php
/**
 * cliupgrade.php
 * Command Line Installer and Upgrader for Moodle
 * @author Dilan Anuruddha
 *
 */


//Intruders OUT!!!
if (!empty($_SERVER['GATEWAY_INTERFACE'])){
    error_log("cli-installer should not be called from apache!");
    echo 'This script is not accessible from the webserver';
    exit;
}


/**
 * BEFORE YOU ADD/EDIT/DELETE ANYTHING IN THIS DOCUMENT PLEASE READ
 *
 * When you add some code that print something on to standard out always wrap it around if clause with $verbose
 * argument check. If the $verbose is CLI_NO, you shouldn't print anything. If $verboser is CLI_SEMI it's ok to print a
 * summarized version. If $verbose is CLI_FULL you can print anything you want.
 *
 * When you add a code that read input from the standard input you should wrap  it with appropriate if clause, allowing
 * required level of interaction. Also remember to add the same option as a command line argument list.
 * In CLI_FULL interaction mode, whether you have set the argument in commandline or not you
 * should prompt for user input. In CLI_SEMI interaction only the arguments which are not set are prompted for user input.
 * No interaction mode doesn't prompt user for anyinput. If any argument is not specified then the default value should be assumed.
 * So do the appropriate thing considering this when you edit or delete this code
 *
 */
//=============================================================================//
// Set values for initial structures

//Suppress all errors
error_reporting(E_ALL);

//define constants
define('CLI_VAL_OPT',0);
define('CLI_VAL_REQ',1);
define('CLI_NO',0);
define('CLI_SEMI',1);
define('CLI_FULL',2);
define('CLI_UPGRADE',1);

/// Default array contain default values for all installation options
/// Please see after library inclusion for more default values - which require libraries

$DEFAULT=array();
// Set default values
$DEFAULT['lang']            = 'en_utf8';
$DEFAULT['dbhost']          = 'localhost';
$DEFAULT['dbuser']          = '';
$DEFAULT['dbpass']          = '';
$DEFAULT['dbtype']          = 'mysql';
$DEFAULT['dbname']          = 'moodle';
$DEFAULT['prefix']          = 'mdl_';
$DEFAULT['downloadlangpack']= false;
$DEFAULT['wwwroot']         = '';
$DEFAULT['dirroot']         = dirname(dirname((__FILE__)));
$DEFAULT['dataroot']        = dirname(dirname(dirname(__FILE__))) . '/moodledata';
$DEFAULT['admindirname']    = 'admin';
$DEFAULT['verbose']         = CLI_SEMI;
$DEFAULT['interactivelevel']= CLI_SEMI;

$DEFAULT['agreelicense']    = true;
$DEFAULT['confirmrelease']  = true;
$DEFAULT['sitefullname']    = 'Moodle Site (Please Change Site Name!!)';
$DEFAULT['siteshortname']   = 'moodle';
$DEFAULT['sitesummary']     = 'Brief Description of the site';
$DEFAULT['sitenewsitems']   = 3;
$DEFAULT['adminusername']   = 'admin';
$DEFAULT['adminpassword']   = 'admin';
$DEFAULT['adminemail']      = 'root@localhost';


///set valid long options ans state whether value for options is required or optional
/// Example :If value is required it would be legal to write the script like $php cliupgrade --lang=en_utf8
///     but writing the script like $php cliupgrade --lang would be illegal.
///     As you may have already seen $php cliupgrade --help is valid since hep argument had CLI_VAL_OPT set

$LONG_OPTIONS	= array(
'lang'              =>CLI_VAL_REQ,
'webaddr'           =>CLI_VAL_REQ,
'moodledir'         =>CLI_VAL_REQ,
'datadir'           =>CLI_VAL_REQ,
'dbtype'            =>CLI_VAL_REQ,
'dbhost'            =>CLI_VAL_REQ,
'dbname'            =>CLI_VAL_REQ,
'dbuser'            =>CLI_VAL_REQ,
'dbpass'            =>CLI_VAL_REQ,
'prefix'            =>CLI_VAL_REQ,
'agreelicense'      =>CLI_VAL_REQ,
'confirmrelease'    =>CLI_VAL_REQ,
'sitefullname'      =>CLI_VAL_REQ,
'siteshortname'     =>CLI_VAL_REQ,
'sitesummary'       =>CLI_VAL_REQ,
'sitenewsitems'     =>CLI_VAL_REQ,
'adminfirstname'    =>CLI_VAL_REQ,
'adminlastname'     =>CLI_VAL_REQ,
'adminusername'     =>CLI_VAL_REQ,
'adminpassword'     =>CLI_VAL_REQ,
'adminemail'        =>CLI_VAL_REQ,
'verbose'           =>CLI_VAL_REQ,
'interactivelevel'  =>CLI_VAL_REQ,
'help'              =>CLI_VAL_OPT);

//Initialize the intall array
$INSTALL=array();

$SESSION->lang              = $DEFAULT['lang'];
$CFG->dirroot               = $DEFAULT['dirroot'];
$CFG->libdir                = $DEFAULT['dirroot'].'/lib';
$CFG->dataroot              = $DEFAULT['dataroot'];
$CFG->admin                 = $DEFAULT['admindirname'];
$CFG->directorypermissions  = 00777;
$CFG->running_installer     = true;
$COURSE->id                 = 0;

// include standard Moodle libraries

require_once($CFG->libdir.'/installlib.php');
require_once($CFG->libdir.'/clilib.php');           //cli-library
require_once($CFG->libdir.'/setuplib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->libdir.'/weblib.php');
require_once($CFG->libdir.'/environmentlib.php');
require_once($CFG->libdir.'/componentlib.class.php');
require_once($CFG->dirroot.'/version.php');

error('TODO fix CLI installer'); //TODO: fix cli installer


/// Set default values - things that require the libraries
$DEFAULT['adminfirstname'] = get_string('admin');
$DEFAULT['adminlastname']   = get_string('user');



/// Set version and release
$INSTALL['version'] = $version;
$INSTALL['release'] = $release;




//========================================================================================//
//Command line option processing//

//fetch arguments
$args = Console_Getopt::readPHPArgv();

//checking errors for argument fetching
if (PEAR::isError($args)) {
    console_write(STDOUT,'pearargerror','install');
    die();
}

//short options
$short_opts = '';
//long options
$long_opts = create_long_options($LONG_OPTIONS);


//get the argumets to options array
if ( realpath($_SERVER['argv'][0]) == __FILE__ && count($args)>1) {
    $console_opt = Console_Getopt::getOpt($args,$short_opts,$long_opts);
} else {
    $console_opt = Console_Getopt::getOpt($args,$short_opts,$long_opts);
}

//detect erros in the options
if (PEAR::isError($console_opt)) {
    console_write(STDOUT,'invalidargumenthelp');
    console_write(STDOUT, "\n", '', false);
    die();
}

//Get the option values to an array of option keys and values
$options=get_options($console_opt);

// if user want help print the help without validating option values
if (is_array($options)) {
    if(array_key_exists('help',$options)){
        console_write(STDOUT,'usagehelp');
        console_write(STDOUT, "\n", '', false);
        die ;
    }
}

//check for validity of options and exit if errors found
validate_option_values($options);

// insert options array options into INSTALL array
foreach ( $options as $key=>$value) {

    //map input argument value to INSTALL array values, Argument names kept bcoz they make sense!!!
    if ( $key == 'moodledir') {
        $key='dirroot';
    } else if ($key == 'webaddr'){
        $key='wwwroot';
    } else if ($key == 'datadir') {
        $key = 'dataroot';
    }
    $INSTALL[$key]=$value;
}


// if verbose is not set at commandline assume default values
if (!isset($INSTALL['verbose'])) {
    $INSTALL['verbose']=$DEFAULT['verbose'];
}
//if interactive level is not set at commandline assume default value
if (!isset($INSTALL['interactivelevel'])) {
    $INSTALL['interactivelevel'] = $DEFAULT['interactivelevel'];
}


// set references for interactive level and verbose install array
$interactive    = &$INSTALL['interactivelevel'];
$verbose        = &$INSTALL['verbose'];

if (!file_exists(dirname(dirname(__FILE__)) . '/config.php')) {

    $configfile = dirname(dirname(__FILE__)) . '/config.php';



    //welcome message
    if ($verbose > CLI_NO) {
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT,'welcometext','install');
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT, "\n", '', false);
    }
    //============================================================================//
    //Language selection for the installation

    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['lang'])) ) ) {
        $langs=get_installer_list_of_languages();

        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT,'selectlanguage','install');
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT,'availablelangs','install');
        console_write(STDOUT, "\n", '', false);
        //output available languages
        foreach ( $langs as $language ) {
            console_write(STDOUT,"\t",'',false);
            console_write(STDOUT,$language,'',false);
            console_write(STDOUT,"\n",'',false);
        }
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT,'yourchoice','install');
        $short_code_langs = get_short_codes($langs);

        $INSTALL['lang']=read_element($short_code_langs);
        $SESSION->lang = $INSTALL['lang'];
    }
    //==============================================================================//
    //Checking PHP settings


    $silent=false;
    if ($verbose == CLI_NO) {
        $silent=true;
    }else{
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT,'checkingphpsettings','install');
        console_write(STDOUT, "\n", '', false);
    }
    /// Check that PHP is of a sufficient version
    check_compatibility(inst_check_php_version(), get_string('phpversion', 'install'), get_string('php52versionerror', 'install'),false,$silent);
    /// Check session auto start
    check_compatibility(!ini_get_bool('session.auto_start'), get_string('sessionautostart', 'install'), get_string('sessionautostarterror', 'install'),false,$silent);
    /// Check magic quotes
    check_compatibility(!ini_get_bool('magic_quotes_runtime'), get_string('magicquotesruntime', 'install'), get_string('magicquotesruntimeerror', 'install'),false,$silent);
    /// Check unsupported PHP configuration
    check_compatibility(ini_get_bool('magic_quotes_gpc') || (!ini_get_bool('register_globals')), get_string('globalsquotes', 'install'), get_string('globalsquoteserror', 'install'),false,$silent);
    /// Check safe mode
    check_compatibility(!ini_get_bool('safe_mode'), get_string('safemode', 'install'), get_string('safemodeerror', 'install'), true,$silent);
    /// Check file uploads
    check_compatibility(ini_get_bool('file_uploads'), get_string('fileuploads', 'install'), get_string('fileuploadserror', 'install'), true,$silent);
    /// Check GD version
    check_compatibility(check_gd_version(), get_string('gdversion', 'install'), get_string('gdversionerror', 'install'),  true,$silent);
    /// Check memory limit
    check_compatibility(check_memory_limit(), get_string('memorylimit', 'install'), get_string('memorylimiterror', 'install'), true,$silent);




    //================================================================================//
    // Moodle directories and web address


    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['dirroot']) || !isset($INSTALL['wwwroot']) || !isset($INSTALL['dataroot']) ) ) ) {
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT,'locationanddirectories','install');
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT, "\n", '', false);
    }

    //input the web directory
    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['dirroot'])) ) ) {
        console_write(STDOUT,'inputwebdirectory','install');
        //if directories validation lib is found change this to read_dir() and
        //edit read_dir() in lib/installlib.php to point to validation code
        $INSTALL['dirroot']=read();
    }
    //input the web adress
    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['wwwroot'])) ) ) {
        console_write(STDOUT,'inputwebadress','install');
        $INSTALL['wwwroot']=read_url();
    }
    //input data directory
    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['dataroot'])) ) ) {
        console_write(STDOUT,'inputdatadirectory','install');
        //if directories validation lib is found change this to read_dir() and
        //edit read_dir() in lib/installlib.php to point to validation code
        $INSTALL['dataroot']=read();
    }


    /// check wwwroot
    if (ini_get('allow_url_fopen') && false) {  /// This was not reliable
        if (($fh = @fopen($INSTALL['wwwroot'].'/admin/cliupgrade.php', 'r')) === false) {
            console_write(STDERR,get_string('wwwrooterror'),'install',false);
        }
    }
    if (isset($fh)) fclose($fh);

    /// check dirroot
    if (($fh = @fopen($INSTALL['dirroot'].'/admin/cliupgrade.php', 'r')) === false ) {
        console_write(STDERR,get_string('dirrooterror'),'install',false);
    }
    if (isset($fh)) fclose($fh);

    /// check dataroot
    $CFG->dataroot = $INSTALL['dataroot'];
    if (make_upload_directory('sessions', false) === false ) {
        console_write(STDERR,get_string('datarooterror'),'install',false);
    }

    //================================================================================//
    // Database settings Moodle database


    // Database section heading
    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['dbhost']) || !isset($INSTALL['dbname']) || !isset($INSTALL['dbtype']) || !isset($INSTALL['dbuser']) ||  !isset($INSTALL['dbpass']) || !isset($INSTALL['prefix']) ) ) ) {
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT,'databasesettingsformoodle','install');
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT, "\n", '', false);
    }

    //Input dialogs
    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['dbhost']) ))) {
        console_write(STDOUT,'databasehost','install');
        $INSTALL['dbhost']=read(); // localhost
    }
    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['dbname']) ))) {
        console_write(STDOUT,'databasename','install');
        $INSTALL['dbname']=read(); //'moodletest3';
    }
    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['dbtype']) ))) {
        $dbtypes=array('mysql','oci8po','postgres7','mssql','mssql_n','odbc_mssql');
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT,'availabledbtypes','install');
        console_write(STDOUT, "\n", '', false);
        foreach ($dbtypes as $dbtype) {
            console_write(STDOUT,"\t",'',false);
            console_write(STDOUT,$dbtype,'install');
            console_write(STDOUT,"\n",'',false);
        }

        console_write(STDOUT,'yourchoice','install');
        $INSTALL['dbtype']=read_element($dbtypes);//'mysql';//
    }

    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['dbuser']) ))) {
        console_write(STDOUT,'databaseuser','install');
        $INSTALL['dbuser']=read();//'root';
    }

    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['dbpass']) ))) {
        console_write(STDOUT,'databasepass','install');
        $INSTALL['dbpass']=read();//'';
    }

    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['prefix']) ))) {
        console_write(STDOUT,'tableprefix','install');
        $INSTALL['prefix']=read();//'mdl_';//
    }


    // Running validation tests

    /// different format for postgres7 by socket
    if ($INSTALL['dbtype'] == 'postgres7' and ($INSTALL['dbhost'] == 'localhost' || $INSTALL['dbhost'] == '127.0.0.1')) {
        $INSTALL['dbhost'] = "user='{$INSTALL['dbuser']}' password='{$INSTALL['dbpass']}' dbname='{$INSTALL['dbname']}'";
        $INSTALL['dbuser'] = '';
        $INSTALL['dbpass'] = '';
        $INSTALL['dbname'] = '';

        if ($INSTALL['prefix'] == '') { /// must have a prefix
            $INSTALL['prefix'] = 'mdl_';
        }
    }

    if ($INSTALL['dbtype'] == 'mysql') {  /// Check MySQL extension is present
        if (!extension_loaded('mysql')) {
            $errormsg = get_string('mysqlextensionisnotpresentinphp', 'install');
        }
    }

    if ($INSTALL['dbtype'] == 'postgres7') {  /// Check PostgreSQL extension is present
        if (!extension_loaded('pgsql')) {
            $errormsg = get_string('pgsqlextensionisnotpresentinphp', 'install');
        }
    }

    if ($INSTALL['dbtype'] == 'mssql') {  /// Check MSSQL extension is present
        if (!function_exists('mssql_connect')) {
            $errormsg = get_string('mssqlextensionisnotpresentinphp', 'install');
        }
    }

    if ($INSTALL['dbtype'] == 'mssql_n') {  /// Check MSSQL extension is present
        if (!function_exists('mssql_connect')) {
            $errormsg = get_string('mssqlextensionisnotpresentinphp', 'install');
        }
    }

    if ($INSTALL['dbtype'] == 'odbc_mssql') {  /// Check ODBC extension is present
        if (!extension_loaded('odbc')) {
            $errormsg = get_string('odbcextensionisnotpresentinphp', 'install');
        }
    }

    if ($INSTALL['dbtype'] == 'oci8po') {  /// Check OCI extension is present
        if (!extension_loaded('oci8')) {
            $errormsg = get_string('ociextensionisnotpresentinphp', 'install');
        }
    }

    if (empty($INSTALL['prefix']) && $INSTALL['dbtype'] != 'mysql') { // All DBs but MySQL require prefix (reserv. words)
        $errormsg = get_string('dbwrongprefix', 'install');
    }

    if ($INSTALL['dbtype'] == 'oci8po' && strlen($INSTALL['prefix']) > 2) { // Oracle max prefix = 2cc (30cc limit)
        $errormsg = get_string('dbwrongprefix', 'install');
    }

    if ($INSTALL['dbtype'] == 'oci8po' && !empty($INSTALL['dbhost'])) { // Oracle host must be blank (tnsnames.ora has it)
        $errormsg = get_string('dbwronghostserver', 'install');
    }

    if (empty($errormsg)) {
        error_reporting(0);  // Hide errors

        if (! $dbconnected = $DB->connect($INSTALL['dbhost'], $INSTALL['dbuser'], $INSTALL['dbpass'], $INSTALL['dbname'], false, $INSTALL['prefix'])) {
            if (!$DB->create_database($INSTALL['dbhost'], $INSTALL['dbuser'], $INSTALL['dbpass'], $INSTALL['dbname'])) {
                 $errormsg = get_string('dbcreationerror', 'install');
                 $nextstage = DATABASE;
            } else {
                $dbconnected = $DB->connect($INSTALL['dbhost'], $INSTALL['dbuser'], $INSTALL['dbpass'], $INSTALL['dbname'], false, $INSTALL['prefix']);
            }
        } else {
// TODO: db encoding checks ??
        }
    }

    // check for errors in db section
    if (isset($errormsg)) {
        console_write(STDERR,$errormsg,'',false);
    }


    //==========================================================================//
    // Check the environment

    //check connection to database

    if ($dbconnected) {
        /// Execute environment check, printing results
        if (!check_moodle_environment($INSTALL['release'], $environment_results, false)) {
            $errormsg = get_string('errorsinenvironment', 'install') . "\n";
        }
    } else {
        /// We never should reach this because DB has been tested before arriving here
        $errormsg = get_string('dbconnectionerror', 'install');
    }

    // check for errors in environment
    if (isset($errormsg)) {
        console_write(STDERR,$errormsg,'',false);
    }

    // Print Environment Status
    if ($verbose > CLI_NO) {
        print_environment_status($environment_results);
    }


    //==============================================================================//
    //download the language pack if it doesn't exist

    if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['downloadlangaugepack']) ))) {
        $site_langs=get_list_of_languages();
        if (!key_exists($INSTALL['lang'],$site_langs)) {
            console_write(STDOUT, "\n", '', false);
            console_write(STDOUT, "\n", '', false);
            console_write(STDOUT,'downloadlanguagepack','install');
            $download_lang_pack=read_yes_no();
            if($download_lang_pack == 'yes'){

                $downloadsuccess = false;

                /// Create necessary lang dir
                if (!make_upload_directory('lang', false)) {
                    console_write(STDERR,get_string('cannotcreatelangdir','error'),false);
                }

                /// Download and install component
                if (($cd = new component_installer('http://download.moodle.org', 'lang16',
                $INSTALL['lang'].'.zip', 'languages.md5', 'lang')) && empty($errormsg)) {
                    $status = $cd->install(); //returns ERROR | UPTODATE | INSTALLED
                    switch ($status) {
                        case ERROR:
                        if ($cd->get_error() == 'remotedownloadnotallowed') {
                            $a = new stdClass();
                            $a->url = 'http://download.moodle.org/lang16/'.$pack.'.zip';
                            $a->dest= $CFG->dataroot.'/lang';
                            console_write(STDOUT,get_string($cd->get_error(), 'error', $a),false);
                        } else {
                            $downloaderror = get_string($cd->get_error(), 'error');
                            console_write(STDOUT,get_string($cd->get_error(), 'error'),false);
                        }
                        break;
                        case UPTODATE:
                        case INSTALLED:
                        $downloadsuccess = true;
                        break;
                        default:
                        //We shouldn't reach this point
                    }
                } else {
                    //We shouldn't reach this point
                }


            }
        }
    }

    if ( $verbose > CLI_NO && !empty($downloadsuccess)) {
        //print success message if language pack download is successful
        console_write(STDOUT,'downloadsuccess');
        print_newline();
    }

    $CONFFILE = array();
    //==================================================================================//
    //set INSTALL array values to CONFFILE array
    foreach ($INSTALL as $key => $value) {
        $CONFFILE[$key] = $value;
    }

    //==================================================================================//
    //if any value is not set, set default values

    foreach ($DEFAULT as $key => $value){
        if (!isset($INSTALL[$key])){
            $CONFFILE[$key]=$value;
        }
    }


    //==================================================================================//
    //create configuration file depending on the previous settings


    if ($verbose > CLI_NO) {
        console_write(STDOUT,'creatingconfigfile','install');
        console_write(STDOUT, "\n", '', false);
    }

    $str  = '<?php  /// Moodle Configuration File '."\r\n";
    $str .= "\r\n";

    $str .= 'unset($CFG);'."\r\n";
    $str .= '$CFG = new stdClass();'."\r\n"; // prevent PHP5 strict warnings
    $str .= "\r\n";

    $database = $databases[$CONFFILE['dbtype']];
    $dbconfig = $database->export_dbconfig($CONFFILE['dbhost'], $CONFFILE['dbuser'], $CONFFILE['dbpass'], $CONFFILE['dbname'], false, $CONFFILE['prefix']);

    foreach ($dbconfig as $key=>$value) {
        $key = str_pad($key, 9);
        $str .= '$CFG->'.$key.' = '.var_export($value, true).";\r\n";
    }
    $str .= "\r\n";

    $str .= '$CFG->wwwroot   = '.var_export($CONFFILE['wwwrootform'], true).";\r\n";
    $str .= '$CFG->dirroot   = '.var_export($CONFFILE['dirrootform'], true).";\r\n";
    $str .= '$CFG->dataroot  = '.var_export($CONFFILE['dataroot'], true).";\r\n";
    $str .= '$CFG->admin     = '.var_export($CONFFILE['admindirname'], true).";\r\n";
    $str .= "\r\n";

    $str .= '$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode'."\r\n";
    $str .= "\r\n";

    $str .= 'require_once("$CFG->dirroot/lib/setup.php");'."\r\n";
    $str .= '// MAKE SURE WHEN YOU EDIT THIS FILE THAT THERE ARE NO SPACES, BLANK LINES,'."\r\n";
    $str .= '// RETURNS, OR ANYTHING ELSE AFTER THE TWO CHARACTERS ON THE NEXT LINE.'."\r\n";
    $str .= '?>';

    umask(0133);

    //save the configuration file
    if (( $configsuccess = ($fh = @fopen($configfile, 'w')) ) !== false) {
        fwrite($fh, $str);
        fclose($fh);
        if ($verbose > CLI_NO) {
            console_write(STDOUT,'configfilecreated','install');
            console_write(STDOUT, "\n", '', false);
        }
    } else {
        console_write(STDOUT,'configfilenotwritten','install');
        console_write(STDOUT, "\n", '', false);
        console_write(STDOUT, "\n", '', false);
        console_write(STDERR, $str, '', false);
    }
    if ( $verbose ) {
        console_write(STDOUT,'installationiscomplete','install');
        console_write(STDOUT, "\n", '', false);
    }
}


if ( file_exists(dirname(dirname(__FILE__)) . '/config.php')) {
    // This is what happens if there is no upgrade....
    //console_write(STDERR,'configurationfileexist','install');
    //console_write(STDOUT, "\n", '', false);
    //die;



    // If the configuration file does not exists exit, this should never occur !!
    if (!file_exists(dirname(dirname(__FILE__)) . '/config.php')) {
        console_write(STDERR,'configfiledoesnotexist','install');
    }

    /// Check that PHP is of a sufficient version
    /// Moved here because older versions do not allow while(@ob_end_clean());
    if (version_compare(phpversion(), "5.2.4") < 0) {
        $phpversion = phpversion();
        console_write(STDERR,"Sorry, Moodle requires PHP 5.2.4 or later (currently using version $phpversion)",'',false);
    }
    /// Turn off time limits and try to flush everything all the time, sometimes upgrades can be slow.

    @set_time_limit(0);
    @ob_implicit_flush(true);
    //check with someone who know? that does this do?
    // while(@ob_end_clean()); // ob_end_flush prevents sending of headers

    //unset();


    require_once(dirname(dirname(__FILE__)) . '/config.php');
    require_once($CFG->libdir.'/adminlib.php');  // Contains various admin-only functions

    /**
     * @todo check upgrade status, if upgrader is running already, notify user and exit.
     * existing thing might work for this with some modifications
     *
     */

    ///check PHP Settings
    if (ini_get_bool('session.auto_start')) {
        console_write(STDERR,"The PHP server variable 'session.auto_start' should be Off ",'',false);
    }

    if (ini_get_bool('magic_quotes_runtime')) {
        console_write(STDERR,"The PHP server variable 'magic_quotes_runtime' should be Off ",'',false);
    }

    if (!ini_get_bool('file_uploads')) {

        console_write(STDERR,"The PHP server variable 'file_uploads' is not turned On" ,'',false);
    }

    /// Check that config.php has been edited

    if ($CFG->wwwroot == "http://example.com/moodle") {
        console_write(STDERR,"Moodle has not been configured yet.  You need to edit config.php first.",'',false);
    }


    /// Check settings in config.php

    $dirroot = dirname(realpath("../index.php"));
    if (!empty($dirroot) and $dirroot != $CFG->dirroot) {
        console_write(STDERR,"Please fix your settings in config.php:
              \nYou have:
              \n\$CFG->dirroot = \"".addslashes($CFG->dirroot)."\";
              \nbut it should be:
              \n\$CFG->dirroot = \"".addslashes($dirroot)."\";",'',false);
    }

    /// Set some necessary variables during set-up to avoid PHP warnings later on this page


    if (!isset($CFG->release)) {
        $CFG->release = "";
    }
    if (!isset($CFG->version)) {
        $CFG->version = "";
    }

    if (is_readable("$CFG->dirroot/version.php")) {
        include_once("$CFG->dirroot/version.php");              # defines $version
    }

    if (!$version or !$release) {
        console_write(STDERR,'Main version.php was not readable or specified','',false);# without version, stop
    }


    if ( $verbose == CLI_NO ) {
        $DB->set_debug(false);
    } else if ( $verbose == CLI_FULL ) {
        $DB->set_debug (true);
    }

    /// Check if the main tables have been installed yet or not.

    if (!$tables = $DB->get_tables() ) {    // No tables yet at all.
        $maintables = false;

    } else {                                 // Check for missing main tables
        $maintables = true;
        $mtables = array("config", "course", "course_categories", "course_modules",
        "course_sections", "log", "log_display", "modules",
        "user");
        foreach ($mtables as $mtable) {
            if (!in_array($mtable, $tables)) {
                $maintables = false;
                break;
            }
        }
    }

    if (! $maintables) {
        /// hide errors from headers in case debug enabled in config.php
        $origdebug = $CFG->debug;
        $CFG->debug = DEBUG_MINIMAL;
        error_reporting($CFG->debug);

        if ( $interactive == CLI_FULL || ($interactive == CLI_SEMI && (!isset($INSTALL['agreelicense']) || empty($INSTALL['agreelicense']))) ) {
            //Print copyright notice and ask to continue
            console_write(STDOUT,get_string('copyrightnotice'),'',false);
            print_newline();
            console_write(STDOUT,get_string('gpl'),'',false);
            print_newline();
            console_write(STDOUT,'doyouagree','install',true);
            $agreelicense = read_boolean();
        }

        if ( !isset($agreelicense)) {
            $agreelicense = $DEFAULT['agreelicense'];
        }

        if (!$agreelicense) {
            console_write(STDERR,'disagreelicense');
        }

        if ( $interactive == CLI_FULL || ( $interactive == CLI_SEMI && (!isset($INSTALL['confirmrelease']) || empty($INSTALL['confirmrelease'])))) {
            console_write(STDOUT,get_string("currentrelease"),'',false);
            print_newline();
            console_write(STDOUT,"Moodle $release",'',false);
            print_newline();
            console_write(STDOUT,'askcontinue');
            $confirmrelease = read_boolean();
        }

        if (!isset($confirmrelease)) {
            $confirmrelease = $DEFAULT['confirmrelease'];
        }
        if (!$confirmrelease) {
            console_write(STDERR,'versionerror');
        }
        $autopilot = 1 ;

        $strdatabasesetup    = get_string("databasesetup");
        $strdatabasesuccess  = get_string("databasesuccess");
        //  print_header($strdatabasesetup, $strdatabasesetup, $strdatabasesetup,
        //                 "", upgrade_get_javascript(), false, "&nbsp;", "&nbsp;");
        /// return to original debugging level
        $CFG->debug = $origdebug;
        error_reporting($CFG->debug);
        upgrade_log_start();

        /// Both old .sql files and new install.xml are supported
        /// But we prioritise install.xml (XMLDB) if present

        if (!$DB->setup_is_unicodedb()) {
            if (!$DB->change_db_encoding()) {
                // If could not convert successfully, throw error, and prevent installation
                console_write(STDERR,'unicoderequired', 'admin');
            }
        }

        $DB->get_manager()->install_from_xmldb_file("$CFG->libdir/db/install.xml"); //New method

        // all new installs are in unicode - keep for backwards compatibility and 1.8 upgrade checks
        set_config('unicodedb', 1);

        /// Continue with the instalation

            // Install the roles system.
            moodle_install_roles();

            // Write default settings unconditionally (i.e. even if a setting is already set, overwrite it)
            // (this should only have any effect during initial install).
            $adminroot = admin_get_root();
            $adminroot->prune('backups'); // backup settings table not created yet
            admin_apply_default_settings($adminroot);

            /// This is used to handle any settings that must exist in $CFG but which do not exist in
            /// admin_get_root()/$ADMIN as admin_setting objects (there are some exceptions).
            apply_default_exception_settings(array('alternateloginurl' => '',
            'auth' => 'email',
            'auth_pop3mailbox' => 'INBOX',
            'changepassword' => '',
            'enrol' => 'manual',
            'enrol_plugins_enabled' => 'manual',
            'guestloginbutton' => 1,
            'registerauth' => 'email',
            'style' => 'default',
            'template' => 'default',
            'theme' => 'standardwhite',
            'filter_multilang_converted' => 1));

            notify($strdatabasesuccess, "green");
            require_once $CFG->dirroot.'/mnet/lib.php';

    }




    /// Check version of Moodle code on disk compared with database
    /// and upgrade if possible.

    if (file_exists("$CFG->dirroot/lib/db/upgrade.php")) {
        include_once("$CFG->dirroot/lib/db/upgrade.php");  # defines new upgrades
    }

    $stradministration = get_string("administration");

    if ($CFG->version) {
        if ($version > $CFG->version) {  // upgrade

            /// If the database is not already Unicode then we do not allow upgrading!
            /// Instead, we print an error telling them to upgrade to 1.7 first.  MDL-6857
            if (empty($CFG->unicodedb)) {
                console_write(STDERR,'unicodeupgradeerror', 'error');
            }

            $a->oldversion = "$CFG->release ($CFG->version)";
            $a->newversion = "$release ($version)";
            $strdatabasechecking = get_string("databasechecking", "", $a);

            // hide errors from headers in case debug is enabled
            $origdebug = $CFG->debug;
            $CFG->debug = DEBUG_MINIMAL;
            error_reporting($CFG->debug);

            // logout in case we are upgrading from pre 1.7 version - prevention of weird session problems
            if ($CFG->version < 2006050600) {
                require_logout();
            }

            if (empty($confirmupgrade)) {

                if ( $interactive == CLI_FULL || ($interactive == CLI_SEMI && !isset($INSTALL['confirmupgrade']))) {
                    print_newline();
                    console_write(STDOUT,$strdatabasechecking,'',false);
                    print_newline();
                    console_write(STDOUT,'askcontinue');
                    $confirmupgrade = read_boolean();
                }
            }
            if (empty($confirmrelease)) {

                if ( $interactive == CLI_FULL || ($interactive == CLI_SEMI && !isset($INSTALL['confirmrelease']))) {
                    $strcurrentrelease = get_string("currentrelease");
                    console_write(STDOUT,$strcurrentrelease,'',false);
                    print_newline();
                    console_write(STDOUT,"Moodle $release",'',false);
                    print_newline();
                    console_write(STDOUT,get_string('releasenoteslink', 'install', 'http://docs.moodle.org/en/Release_Notes'),'',false);
                    print_newline();
                    console_write(STDOUT,'askcontinue');
                    $confirmrelease = read_boolean();
                }
                require_once($CFG->libdir.'/environmentlib.php');

                console_write(STDOUT,'environment', 'admin');
                if (!check_moodle_environment($release, $environment_results, false)) {
                    // Print Environment Status
                    if ($verbose > CLI_NO) {
                        print_newline();
                        print_environment_status_detailed($environment_results);
                        print_newline();
                        console_write(STDOUT,'environmenterrorupgrade', 'admin');
                    }
                    if(!read_boolean()){
                        console_write(STDERR,'','',false);
                    }
                } else {

                    if ( $interactive == CLI_FULL || ($interactive == CLI_SEMI && !isset($INSTALL['autopilot']))) {
                        console_write(STDOUT,'environmentok', 'admin');
                        console_write(STDOUT,'unattendedoperation','admin');
                        $autopilot = read_boolean();
                    }
                }
            }

            $strdatabasesuccess  = get_string("databasesuccess");



            /// return to original debugging level
            $CFG->debug = $origdebug;
            error_reporting($CFG->debug);
            upgrade_log_start();

            /// Upgrade current language pack if we can
            upgrade_language_pack();

            if ( $verbose > CLI_NO ) {
                console_write(STDOUT,$strdatabasechecking,'',false);
            }

            /// Launch the old main upgrade (if exists)
            $status = true;
            if (function_exists('main_upgrade')) {
                $status = main_upgrade($CFG->version);
            }
            /// If succesful and exists launch the new main upgrade (XMLDB), called xmldb_main_upgrade
            if ($status && function_exists('xmldb_main_upgrade')) {
                $status = xmldb_main_upgrade($CFG->version);
            }

            /// If successful, continue upgrading roles and setting everything properly
            if ($status) {
                if (!update_capabilities()) {
                    console_write(STDERR,'Had trouble upgrading the core capabilities for the Roles System','',false);
                }
                if (set_config("version", $version)) {
                    remove_dir($CFG->dataroot . '/cache', true); // flush cache
                    notify($strdatabasesuccess, "green");
                    /// print_continue("upgradesettings.php");
                } else {
                    console_write(STDERR,'Upgrade failed!  (Could not update version in config table)','',false);
                }
                /// Main upgrade not success
            } else {
                console_write(STDERR,'Main Upgrade failed!  See lib/db/upgrade.php','',false);

            }
            upgrade_log_finish();

        } else if ($version < $CFG->version) {
            upgrade_log_start();
            notify("WARNING!!!  The code you are using is OLDER than the version that made these databases!");
            upgrade_log_finish();
        }
    } else {
        if (!set_config("version", $version)) {
            console_write(STDERR,"A problem occurred inserting current version into databases",'',false);
        }
    }


    /// Find and check all main modules and load them up or upgrade them if necessary
    /// first old *.php update and then the new upgrade.php script
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradingactivitymodule','install'),'',1);
    }
    upgrade_activity_modules('');// Don't return anywhere

    /// Check all questiontype plugins and upgrade if necessary
    /// first old *.php update and then the new upgrade.php script
    /// It is important that this is done AFTER the quiz module has been upgraded
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradingqtypeplugin','install'),'',1);
    }
    upgrade_plugins('qtype', 'question/type', '');  // Don't return anywhere

    /// Upgrade backup/restore system if necessary
    /// first old *.php update and then the new upgrade.php script
    require_once("$CFG->dirroot/backup/lib.php");
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradingbackupdb','install'),'',1);
    }
    upgrade_backup_db('');  // Don't return anywhere

    /// Upgrade blocks system if necessary
    /// first old *.php update and then the new upgrade.php script
    require_once("$CFG->dirroot/lib/blocklib.php");
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradingblocksdb','install'),'',1);
    }
    upgrade_blocks_db('');  // Don't return anywhere

    /// Check all blocks and load (or upgrade them if necessary)
    /// first old *.php update and then the new upgrade.php script
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradingblocksplugin','install'),'',1);
    }
    upgrade_blocks_plugins('');  // Don't return anywhere

    /// Check all enrolment plugins and upgrade if necessary
    /// first old *.php update and then the new upgrade.php script
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradingenrolplugin','install'),'',1);
    }
    upgrade_plugins('enrol', 'enrol', '');  // Don't return anywhere

    /// Check all course formats and upgrade if necessary
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradingcourseformatplugin','install'),'',1);
    }
    upgrade_plugins('format','course/format',''); // Don't return anywhere

    /// Check for local database customisations
    /// first old *.php update and then the new upgrade.php script
    require_once("$CFG->dirroot/lib/locallib.php");
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradinglocaldb','install'),'',1);
    }
    upgrade_local_db('');  // Don't return anywhere

    /// Check for changes to RPC functions
    require_once($CFG->dirroot.'/admin/mnet/adminlib.php');
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradingrpcfunctions','install'),'',1);
    }
    upgrade_RPC_functions('');  // Don't return anywhere

    /// Upgrade all plugins for gradebook
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradinggradeexportplugin','install'),'',1);
    }
    upgrade_plugins('gradeexport', 'grade/export', ''); // Don't return anywhere
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradinggradeimportplugin','install'),'',1);
    }
    upgrade_plugins('gradeimport', 'grade/import', ''); // Don't return anywhere
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradinggradereportplugin','install'),'',1);
    }
    upgrade_plugins('gradereport', 'grade/report', ''); // Don't return anywhere

    /// Check all message output plugins and upgrade if necessary
    if ( $verbose > CLI_NO ) {
        print_heading(get_string('upgradingmessageoutputpluggin','install'),'',1);
    }
    upgrade_plugins('message','message/output',''); // Don't return anywhere


    /// just make sure upgrade logging is properly terminated
    upgrade_log_finish();

    unset($SESSION->installautopilot);

    /// Set up the site
    if (! $site = get_site()) {
        // We are about to create the site "course"
        require_once($CFG->libdir.'/blocklib.php');

        if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( (!isset($INSTALL['sitefullname'])) || (!isset($INSTALL['siteshortname'])) || (!isset($INSTALL['sitesummary'])) || (!isset($INSTALL['sitenewsitems'])) )) ) {
            console_write(STDOUT,'siteinfo');
            print_newline();
        }

        if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['sitefullname'])) ) ) {
            console_write(STDOUT,'sitefullname');
            $sitefullname = read();
        } else if (isset($INSTALL['sitefullname'])) {
            $sitefullname = $INSTALL['sitefullname'];
        }

        if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['siteshortname'])) ) ) {
            console_write(STDOUT,'siteshortname');
            $siteshortname = read();
        } else if (isset($INSTALL['siteshortname'])) {
            $siteshortname = $INSTALL['siteshortname'];
        }
        if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['sitesummary'])) ) ) {
            console_write(STDOUT,'sitesummary');
            $sitesummary =read();
        } else if (isset($INSTALL['sitesummary'])) {
            $sitesummary = $INSTALL['sitesummary'];
        }
        if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['sitenewsitems'])) ) ) {
            console_write(STDOUT,'sitenewsitems');
            $sitenewsitems = read_int();
        } else if (isset($INSTALL['sitenewsitems'])) {
            $sitenewsitems = $INSTALL['sitenewsitems'];
        }

        if (!isset($sitefullname)) {
            $sitefullname = $DEFAULT['sitefullname'];
        }
        if (!isset($siteshortname)) {
            $siteshortname = $DEFAULT['siteshortname'];
        }
        if (!isset($sitesummary)) {
            $sitesummary = $DEFAULT['sitesummary'];
        }
        if (!isset($sitenewsitems)) {
            $sitenewsitems = $DEFAULT['sitenewsitems'];
        }

        $newsite = new Object();
        $newsite->fullname = $sitefullname;
        $newsite->shortname = $siteshortname;
        $newsite->summary = $sitesummary;
        $newsite->newsitems = $sitenewsitems;
        $newsite->numsections = 0;
        $newsite->category = 0;
        $newsite->format = 'site';  // Only for this course
        $newsite->teacher = get_string("defaultcourseteacher");
        $newsite->teachers = get_string("defaultcourseteachers");
        $newsite->student = get_string("defaultcoursestudent");
        $newsite->students = get_string("defaultcoursestudents");
        $newsite->timemodified = time();

        if ($newid = $DB->insert_record('course', $newsite)) {
            // Site created, add blocks for it
            $page = page_create_object(PAGE_COURSE_VIEW, $newid);
            blocks_repopulate_page($page); // Return value not checked because you can always edit later

            // create default course category
            $cat = get_course_category();

        } else {
            print_error('cannotsetupsite', 'error');
        }
    }

    /// Define the unique site ID code if it isn't already
    if (empty($CFG->siteidentifier)) {    // Unique site identification code
        set_config('siteidentifier', random_string(32).$_SERVER['HTTP_HOST']);
    }

    /// Check if the guest user exists.  If not, create one.
    if (!$DB->record_exists("user", array("username"=>"guest"))) {
        if (! $guest = create_guest_record()) {
            notify("Could not create guest user record !!!");
        }
    }


    /// Set up the admin user
    if (empty($CFG->rolesactive)) {

        // If full interactive or semi interactive with at least one option is not set print the admininfo message
        if ( ($interactive == CLI_FULL) || ($interactive == CLI_SEMI && (!isset($INSTALL['adminfirstname']) || !isset($INSTALL['adminlastname']) || !isset($INSTALL['adminusername']) || !isset($INSTALL['adminpassword']) || !isset($INSTALL['adminemail']) ))) {
            console_write(STDOUT,'admininfo');
            print_newline();
        }
        // Assign the first name
        if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['adminfirstname'])) ) ) {
            console_write(STDOUT,'adminfirstname');
            $adminfirstname = read();
        } else if (isset($INSTALL['adminfirstname'])) {
            $adminfirstname = $INSTALL['adminfirstname'];
        }

        // Assign the last name
        if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['adminlastname'])) ) ) {
            console_write(STDOUT,'adminlastname');
            $adminlastname = read();
        } else if (isset($INSTALL['adminlastname'])) {
            $adminlastname = $INSTALL['adminlastname'];
        }

        // Assign user name
        if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['adminusername'])) ) ) {
            console_write(STDOUT,'adminusername');
            $adminusername = read();
        } else if (isset($INSTALL['adminusername'])) {
            $adminusername = $INSTALL['adminusername'];
        }

        // Assign password
        if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['adminpassword'])) ) ) {
            console_write(STDOUT,'adminpassword');
            $adminpassword = read();
        } else if (isset($INSTALL['adminpassword'])) {
            $adminpassword = $INSTALL['adminpassword'];
        }

        // Assign email
        if ( ( $interactive == CLI_FULL ) || ($interactive == CLI_SEMI && ( !isset($INSTALL['adminemail'])) ) ) {
            console_write(STDOUT,'adminemail');
            $adminemail = read();
        } else if (isset($INSTALL['adminemail'])) {
            $adminemail = $INSTALL['adminemail'];
        }

        /// If values not set in above set all values to their defaults
        if (!isset($adminfirstname)) {
            $adminfirstname = $DEFAULT['adminfirstname'];
        }
        if (!isset($adminlastname)) {
            $adminlastname = $DEFAULT['adminlastname'];
        }
        if (!isset($adminusername)) {
            $adminusername = $DEFAULT['adminusername'];
        }
        if (!isset($adminpassword)) {
            $adminpassword = $DEFAULT['adminpassword'];
        }
        if (!isset($adminemail)) {
            $adminemail = $DEFAULT['adminemail'];
        }

        $user = new object();
        $user->auth         = 'manual';
        $user->firstname    = $adminfirstname;  //get_string('admin');
        $user->lastname     = $adminlastname;   //get_string('user');
        $user->username     = $adminusername;   //'admin';
        $user->password     = hash_internal_user_password($adminpassword);   // 'admin'
        $user->email        = $adminemail;      //'root@localhost';
        $user->confirmed    = 1;
        $user->mnethostid   = $CFG->mnet_localhost_id;
        $user->lang         = $CFG->lang;
        $user->maildisplay  = 1;
        $user->timemodified = time();

        create_admin_user($user);
    }
    if ( $verbose > CLI_NO ) {
        print_newline();
        console_write(STDOUT,'upgradingcompleted');
        console_write(STDOUT, "\n", '', false);
    }
}


?>
