<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This script creates config.php file and prepares database.
 *
 * This script is not intended for beginners!
 * Potential problems:
 * - su to apache account or sudo before execution
 * - not compatible with Windows platform
 *
 * @package    core
 * @subpackage cli
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

// extra execution prevention - we can not just require config.php here
if (isset($_SERVER['REMOTE_ADDR'])) {
    exit(1);
}

// Force OPcache reset if used, we do not want any stale caches
// when preparing test environment.
if (function_exists('opcache_reset')) {
    opcache_reset();
}

$help =
"Command line Moodle installer, creates config.php and initializes database.
Please note you must execute this script with the same uid as apache
or use chmod/chown after installation.

Site defaults may be changed via local/defaults.php.

Options:
--chmod=OCTAL-MODE    Permissions of new directories created within dataroot.
                      Default is 2777. You may want to change it to 2770
                      or 2750 or 750. See chmod man page for details.
--lang=CODE           Installation and default site language.
--wwwroot=URL         Web address for the Moodle site,
                      required in non-interactive mode.
--dataroot=DIR        Location of the moodle data folder,
                      must not be web accessible. Default is moodledata
                      in the parent directory.
--dbtype=TYPE         Database type. Default is mysqli
--dbhost=HOST         Database host. Default is localhost
--dbname=NAME         Database name. Default is moodle
--dbuser=USERNAME     Database user. Default is root
--dbpass=PASSWORD     Database password. Default is blank
--dbport=NUMBER       Use database port.
--dbsocket=PATH       Use database socket, 1 means default. Available for some databases only.
--prefix=STRING       Table prefix for above database tables. Default is mdl_
--fullname=STRING     The fullname of the site
--shortname=STRING    The shortname of the site
--summary=STRING      The summary to be displayed on the front page
--adminuser=USERNAME  Username for the moodle admin account. Default is admin
--adminpass=PASSWORD  Password for the moodle admin account,
                      required in non-interactive mode.
--adminemail=STRING   Email address for the moodle admin account.
--upgradekey=STRING   The upgrade key to be set in the config.php, leave empty to not set it.
--non-interactive     No interactive questions, installation fails if any
                      problem encountered.
--agree-license       Indicates agreement with software license,
                      required in non-interactive mode.
--allow-unstable      Install even if the version is not marked as stable yet,
                      required in non-interactive mode.
--skip-database       Stop the installation before installing the database.
-h, --help            Print out this help

Example:
\$sudo -u www-data /usr/bin/php admin/cli/install.php --lang=cs
"; //TODO: localize, mark as needed in install - to be translated later when everything is finished


// distro specific customisation
$distrolibfile = __DIR__.'/../../install/distrolib.php';
$distro = null;
if (file_exists($distrolibfile)) {
    require_once($distrolibfile);
    if (function_exists('distro_get_config')) {
        $distro = distro_get_config();
    }
}

// Nothing to do if config.php exists
$configfile = __DIR__.'/../../config.php';
if (file_exists($configfile)) {
    require($configfile);
    require_once($CFG->libdir.'/clilib.php');
    list($options, $unrecognized) = cli_get_params(array('help'=>false), array('h'=>'help'));

    if ($options['help']) {
        echo $help;
        echo "\n\n";
    }

    if ($DB->get_manager()->table_exists('config')) {
        cli_error(get_string('clialreadyinstalled', 'install'));
    } else {
        cli_error(get_string('clialreadyconfigured', 'install'));
    }
}

$olddir = getcwd();

// change directory so that includes below work properly
chdir(dirname($_SERVER['argv'][0]));

// Servers should define a default timezone in php.ini, but if they don't then make sure something is defined.
if (!function_exists('date_default_timezone_set') or !function_exists('date_default_timezone_get')) {
    fwrite(STDERR, "Timezone functions are not available.\n");
    exit(1);
}
date_default_timezone_set(@date_default_timezone_get());

// make sure PHP errors are displayed - helps with diagnosing of problems
@error_reporting(E_ALL);
@ini_set('display_errors', '1');
// we need a lot of memory
@ini_set('memory_limit', '128M');

/** Used by library scripts to check they are being called by Moodle */
define('MOODLE_INTERNAL', true);

// Disables all caching.
define('CACHE_DISABLE_ALL', true);

define('PHPUNIT_TEST', false);

define('IGNORE_COMPONENT_CACHE', true);

// Check that PHP is of a sufficient version as soon as possible.
require_once(__DIR__.'/../../lib/phpminimumversionlib.php');
moodle_require_minimum_php_version();

// set up configuration
global $CFG;
$CFG = new stdClass();
$CFG->lang                 = 'en';
$CFG->dirroot              = dirname(dirname(__DIR__));
$CFG->libdir               = "$CFG->dirroot/lib";
$CFG->wwwroot              = "http://localhost";
$CFG->httpswwwroot         = $CFG->wwwroot;
$CFG->docroot              = 'http://docs.moodle.org';
$CFG->running_installer    = true;
$CFG->early_install_lang   = true;
$CFG->ostype               = (stristr(PHP_OS, 'win') && !stristr(PHP_OS, 'darwin')) ? 'WINDOWS' : 'UNIX';
$CFG->dboptions            = array();
$CFG->debug                = (E_ALL | E_STRICT);
$CFG->debugdisplay         = true;
$CFG->debugdeveloper       = true;

$parts = explode('/', str_replace('\\', '/', dirname(__DIR__)));
$CFG->admin                = array_pop($parts);

//point pear include path to moodles lib/pear so that includes and requires will search there for files before anywhere else
//the problem is that we need specific version of quickforms and hacked excel files :-(
ini_set('include_path', $CFG->libdir.'/pear' . PATH_SEPARATOR . ini_get('include_path'));

require_once($CFG->libdir.'/classes/component.php');
require_once($CFG->libdir.'/classes/text.php');
require_once($CFG->libdir.'/classes/string_manager.php');
require_once($CFG->libdir.'/classes/string_manager_install.php');
require_once($CFG->libdir.'/classes/string_manager_standard.php');
require_once($CFG->libdir.'/installlib.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/setuplib.php');
require_once($CFG->libdir.'/weblib.php');
require_once($CFG->libdir.'/dmllib.php');
require_once($CFG->libdir.'/moodlelib.php');
require_once($CFG->libdir.'/deprecatedlib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/componentlib.class.php');
require_once($CFG->dirroot.'/cache/lib.php');

// Register our classloader, in theory somebody might want to replace it to load other hacked core classes.
// Required because the database checks below lead to session interaction which is going to lead us to requiring autoloaded classes.
if (defined('COMPONENT_CLASSLOADER')) {
    spl_autoload_register(COMPONENT_CLASSLOADER);
} else {
    spl_autoload_register('core_component::classloader');
}

require($CFG->dirroot.'/version.php');
$CFG->target_release = $release;

\core\session\manager::init_empty_session();
global $SESSION;
global $USER;

global $COURSE;
$COURSE = new stdClass();
$COURSE->id = 1;

global $SITE;
$SITE = $COURSE;
define('SITEID', 1);

//Database types
$databases = array('mysqli' => moodle_database::get_driver_instance('mysqli', 'native'),
                   'mariadb'=> moodle_database::get_driver_instance('mariadb', 'native'),
                   'pgsql'  => moodle_database::get_driver_instance('pgsql',  'native'),
                   'oci'    => moodle_database::get_driver_instance('oci',    'native'),
                   'sqlsrv' => moodle_database::get_driver_instance('sqlsrv', 'native'), // MS SQL*Server PHP driver
                  );
foreach ($databases as $type=>$database) {
    if ($database->driver_installed() !== true) {
        unset($databases[$type]);
    }
}
if (empty($databases)) {
    $defaultdb = '';
} else {
    reset($databases);
    $defaultdb = key($databases);
}

// now get cli options
list($options, $unrecognized) = cli_get_params(
    array(
        'chmod'             => isset($distro->directorypermissions) ? sprintf('%04o',$distro->directorypermissions) : '2777', // let distros set dir permissions
        'lang'              => $CFG->lang,
        'wwwroot'           => '',
        'dataroot'          => empty($distro->dataroot) ? str_replace('\\', '/', dirname(dirname(dirname(__DIR__))).'/moodledata'): $distro->dataroot, // initialised later after including libs or by distro
        'dbtype'            => empty($distro->dbtype) ? $defaultdb : $distro->dbtype, // let distro skip dbtype selection
        'dbhost'            => empty($distro->dbhost) ? 'localhost' : $distro->dbhost, // let distros set dbhost
        'dbname'            => 'moodle',
        'dbuser'            => empty($distro->dbuser) ? 'root' : $distro->dbuser, // let distros set dbuser
        'dbpass'            => '',
        'dbport'            => '',
        'dbsocket'          => '',
        'prefix'            => 'mdl_',
        'fullname'          => '',
        'shortname'         => '',
        'summary'           => '',
        'adminuser'         => 'admin',
        'adminpass'         => '',
        'adminemail'        => '',
        'upgradekey'        => '',
        'non-interactive'   => false,
        'agree-license'     => false,
        'allow-unstable'    => false,
        'skip-database'     => false,
        'help'              => false
    ),
    array(
        'h' => 'help'
    )
);

$interactive = empty($options['non-interactive']);
$skipdatabase = $options['skip-database'];

// set up language
$lang = clean_param($options['lang'], PARAM_SAFEDIR);
$languages = get_string_manager()->get_list_of_translations();
if (array_key_exists($lang, $languages)) {
    $CFG->lang = $lang;
}

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    echo $help;
    die;
}

//Print header
cli_logo();
echo PHP_EOL;
echo get_string('cliinstallheader', 'install', $CFG->target_release)."\n";

//Fist select language
if ($interactive) {
    cli_separator();
    // Do not put the langs into columns because it is not compatible with RTL.
    $default = $CFG->lang;
    cli_heading(get_string('chooselanguagehead', 'install'));
    if (array_key_exists($default, $languages)) {
        echo $default.' - '.$languages[$default]."\n";
    }
    if ($default !== 'en') {
        echo 'en - English (en)'."\n";
    }
    echo '? - '.get_string('availablelangs', 'install')."\n";
    $prompt = get_string('clitypevaluedefault', 'admin', $CFG->lang);
    $error = '';
    do {
        echo $error;
        $input = cli_input($prompt, $default);

        if ($input === '?') {
            echo implode("\n", $languages)."\n";
            $error = "\n";

        } else {
            $input = clean_param($input, PARAM_SAFEDIR);

            if (!array_key_exists($input, $languages)) {
                $error = get_string('cliincorrectvalueretry', 'admin')."\n";
            } else {
                $error = '';
            }
        }
    } while ($error !== '');
    $CFG->lang = $input;
} else {
    // already selected and verified
}

// Set directorypermissions first
$chmod = octdec(clean_param($options['chmod'], PARAM_INT));
if ($interactive) {
    cli_separator();
    cli_heading(get_string('datarootpermission', 'install'));
    $prompt = get_string('clitypevaluedefault', 'admin', decoct($chmod));
    $error = '';
    do {
        echo $error;
        $input = cli_input($prompt, decoct($chmod));
        $input = octdec(clean_param($input, PARAM_INT));
        if (empty($input)) {
            $error = get_string('cliincorrectvalueretry', 'admin')."\n";
        } else {
            $error = '';
        }
    } while ($error !== '');
    $chmod = $input;

} else {
    if (empty($chmod)) {
        $a = (object)array('option' => 'chmod', 'value' => decoct($chmod));
        cli_error(get_string('cliincorrectvalueerror', 'admin', $a));
    }
}
$CFG->directorypermissions = $chmod;
$CFG->filepermissions      = ($CFG->directorypermissions & 0666);
$CFG->umaskpermissions     = (($CFG->directorypermissions & 0777) ^ 0777);

//We need wwwroot before we test dataroot
$wwwroot = clean_param($options['wwwroot'], PARAM_URL);
$wwwroot = trim($wwwroot, '/');
if ($interactive) {
    cli_separator();
    cli_heading(get_string('wwwroot', 'install'));
    if (strpos($wwwroot, 'http') === 0) {
        $prompt = get_string('clitypevaluedefault', 'admin', $wwwroot);
    } else {
        $wwwroot = null;
        $prompt = get_string('clitypevalue', 'admin');
    }
    $error = '';
    do {
        echo $error;
        $input = cli_input($prompt, $wwwroot);
        $input = clean_param($input, PARAM_URL);
        $input = trim($input, '/');
        if (strpos($input, 'http') !== 0) {
            $error = get_string('cliincorrectvalueretry', 'admin')."\n";
        } else {
            $error = '';
        }
    } while ($error !== '');
    $wwwroot = $input;

} else {
    if (strpos($wwwroot, 'http') !== 0) {
        $a = (object)array('option'=>'wwwroot', 'value'=>$wwwroot);
        cli_error(get_string('cliincorrectvalueerror', 'admin', $a));
    }
}
$CFG->wwwroot       = $wwwroot;
$CFG->httpswwwroot  = $CFG->wwwroot;

//We need dataroot before lang download
$CFG->dataroot = $options['dataroot'];
if ($interactive) {
    cli_separator();
    $i=0;
    while(is_dataroot_insecure()) {
        $parrent = dirname($CFG->dataroot);
        $i++;
        if ($parrent == '/' or $parrent == '.' or preg_match('/^[a-z]:\\\?$/i', $parrent) or ($i > 100)) {
            $CFG->dataroot = ''; //can not find secure location for dataroot
            break;
        }
        $CFG->dataroot = dirname($parrent).'/moodledata';
    }
    cli_heading(get_string('dataroot', 'install'));
    $error = '';
    do {
        if ($CFG->dataroot !== '') {
            $prompt = get_string('clitypevaluedefault', 'admin', $CFG->dataroot);
        } else {
            $prompt = get_string('clitypevalue', 'admin');
        }
        echo $error;
        $CFG->dataroot = cli_input($prompt, $CFG->dataroot);
        if ($CFG->dataroot === '') {
            $error = get_string('cliincorrectvalueretry', 'admin')."\n";
        } else if (is_dataroot_insecure()) {
            $CFG->dataroot = '';
            $error = get_string('pathsunsecuredataroot', 'install')."\n";
        } else {
            if (install_init_dataroot($CFG->dataroot, $CFG->directorypermissions)) {
                $error = '';
            } else {
                $a = (object)array('dataroot' => $CFG->dataroot);
                $error = get_string('pathserrcreatedataroot', 'install', $a)."\n";
            }
        }

    } while ($error !== '');

} else {
    if (is_dataroot_insecure()) {
        cli_error(get_string('pathsunsecuredataroot', 'install'));
    }
    if (!install_init_dataroot($CFG->dataroot, $CFG->directorypermissions)) {
        $a = (object)array('dataroot' => $CFG->dataroot);
        cli_error(get_string('pathserrcreatedataroot', 'install', $a));
    }
}
$CFG->tempdir       = $CFG->dataroot.'/temp';
$CFG->backuptempdir = $CFG->tempdir.'/backup';
$CFG->cachedir      = $CFG->dataroot.'/cache';
$CFG->localcachedir = $CFG->dataroot.'/localcache';

// download required lang packs
if ($CFG->lang !== 'en') {
    $installer = new lang_installer($CFG->lang);
    $results = $installer->run();
    foreach ($results as $langcode => $langstatus) {
        if ($langstatus === lang_installer::RESULT_DOWNLOADERROR) {
            $a       = new stdClass();
            $a->url  = $installer->lang_pack_url($langcode);
            $a->dest = $CFG->dataroot.'/lang';
            cli_problem(get_string('remotedownloaderror', 'error', $a));
        }
    }
}

// switch the string_manager instance to stop using install/lang/
$CFG->early_install_lang = false;
$CFG->langotherroot      = $CFG->dataroot.'/lang';
$CFG->langlocalroot      = $CFG->dataroot.'/lang';
get_string_manager(true);

// make sure we are installing stable release or require a confirmation
if (isset($maturity)) {
    if (($maturity < MATURITY_STABLE) and !$options['allow-unstable']) {
        $maturitylevel = get_string('maturity'.$maturity, 'admin');

        if ($interactive) {
            cli_separator();
            cli_heading(get_string('notice'));
            echo get_string('maturitycorewarning', 'admin', $maturitylevel) . PHP_EOL;
            echo get_string('morehelp') . ': ' . get_docs_url('admin/versions') . PHP_EOL;
            echo get_string('continue') . PHP_EOL;
            $prompt = get_string('cliyesnoprompt', 'admin');
            $input = cli_input($prompt, '', array(get_string('clianswerno', 'admin'), get_string('cliansweryes', 'admin')));
            if ($input == get_string('clianswerno', 'admin')) {
                exit(1);
            }
        } else {
            cli_problem(get_string('maturitycorewarning', 'admin', $maturitylevel));
            cli_error(get_string('maturityallowunstable', 'admin'));
        }
    }
}

// ask for db type - show only drivers available
if ($interactive) {
    $options['dbtype'] = strtolower($options['dbtype']);
    cli_separator();
    cli_heading(get_string('databasetypehead', 'install'));
    foreach ($databases as $type=>$database) {
        echo " $type \n";
    }
    if (!empty($databases[$options['dbtype']])) {
        $prompt = get_string('clitypevaluedefault', 'admin', $options['dbtype']);
    } else {
        $prompt = get_string('clitypevalue', 'admin');
    }
    $CFG->dbtype = cli_input($prompt, $options['dbtype'], array_keys($databases));

} else {
    if (empty($databases[$options['dbtype']])) {
        $a = (object)array('option'=>'dbtype', 'value'=>$options['dbtype']);
        cli_error(get_string('cliincorrectvalueerror', 'admin', $a));
    }
    $CFG->dbtype = $options['dbtype'];
}
$database = $databases[$CFG->dbtype];


// We cannot do any validation until all DB connection data is provided.
$hintdatabase = '';
do {
    echo $hintdatabase;

    // Ask for db host.
    if ($interactive) {
        cli_separator();
        cli_heading(get_string('databasehost', 'install'));
        if ($options['dbhost'] !== '') {
            $prompt = get_string('clitypevaluedefault', 'admin', $options['dbhost']);
        } else {
            $prompt = get_string('clitypevalue', 'admin');
        }
        $CFG->dbhost = cli_input($prompt, $options['dbhost']);

    } else {
        $CFG->dbhost = $options['dbhost'];
    }

    // Ask for db name.
    if ($interactive) {
        cli_separator();
        cli_heading(get_string('databasename', 'install'));
        if ($options['dbname'] !== '') {
            $prompt = get_string('clitypevaluedefault', 'admin', $options['dbname']);
        } else {
            $prompt = get_string('clitypevalue', 'admin');
        }
        $CFG->dbname = cli_input($prompt, $options['dbname']);

    } else {
        $CFG->dbname = $options['dbname'];
    }

    // Ask for db prefix.
    if ($interactive) {
        cli_separator();
        cli_heading(get_string('dbprefix', 'install'));
        //TODO: solve somehow the prefix trouble for oci.
        if ($options['prefix'] !== '') {
            $prompt = get_string('clitypevaluedefault', 'admin', $options['prefix']);
        } else {
            $prompt = get_string('clitypevalue', 'admin');
        }
        $CFG->prefix = cli_input($prompt, $options['prefix']);

    } else {
        $CFG->prefix = $options['prefix'];
    }

    // Ask for db port.
    if ($interactive) {
        cli_separator();
        cli_heading(get_string('databaseport', 'install'));
        $prompt = get_string('clitypevaluedefault', 'admin', $options['dbport']);
        $CFG->dboptions['dbport'] = (int) cli_input($prompt, $options['dbport']);

    } else {
        $CFG->dboptions['dbport'] = (int) $options['dbport'];
    }
    if ($CFG->dboptions['dbport'] <= 0) {
        $CFG->dboptions['dbport'] = '';
    }

    // Ask for db socket.
    if ($CFG->ostype === 'WINDOWS') {
        $CFG->dboptions['dbsocket'] = '';

    } else if ($interactive and empty($CFG->dboptions['dbport'])) {
        cli_separator();
        cli_heading(get_string('databasesocket', 'install'));
        $prompt = get_string('clitypevaluedefault', 'admin', $options['dbsocket']);
        $CFG->dboptions['dbsocket'] = cli_input($prompt, $options['dbsocket']);

    } else {
        $CFG->dboptions['dbsocket'] = $options['dbsocket'];
    }

    // Ask for db user.
    if ($interactive) {
        cli_separator();
        cli_heading(get_string('databaseuser', 'install'));
        if ($options['dbuser'] !== '') {
            $prompt = get_string('clitypevaluedefault', 'admin', $options['dbuser']);
        } else {
            $prompt = get_string('clitypevalue', 'admin');
        }
        $CFG->dbuser = cli_input($prompt, $options['dbuser']);

    } else {
        $CFG->dbuser = $options['dbuser'];
    }

    // Ask for db password.
    if ($interactive) {
        cli_separator();
        cli_heading(get_string('databasepass', 'install'));

        if ($options['dbpass'] !== '') {
            $prompt = get_string('clitypevaluedefault', 'admin', $options['dbpass']);
        } else {
            $prompt = get_string('clitypevalue', 'admin');
        }

        $CFG->dbpass = cli_input($prompt, $options['dbpass']);
        if (function_exists('distro_pre_create_db')) { // Hook for distros needing to do something before DB creation.
            $distro = distro_pre_create_db($database, $CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->prefix,
                    array('dbpersist' => 0, 'dbport' => $CFG->dboptions['dbport'], 'dbsocket' => $CFG->dboptions['dbsocket']),
                    $distro);
        }
        $hintdatabase = install_db_validate($database, $CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->prefix,
                array('dbpersist' => 0, 'dbport' => $CFG->dboptions['dbport'], 'dbsocket' => $CFG->dboptions['dbsocket']));

    } else {
        $CFG->dbpass = $options['dbpass'];
        $hintdatabase = install_db_validate($database, $CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->prefix,
                array('dbpersist' => 0, 'dbport' => $CFG->dboptions['dbport'], 'dbsocket' => $CFG->dboptions['dbsocket']));
        if ($hintdatabase !== '') {
            cli_error(get_string('dbconnectionerror', 'install'));
        }
    }
} while ($hintdatabase !== '');

// If --skip-database option is provided, we do not need to ask for site fullname, shortname, adminuser, adminpass, adminemail.
// These fields will be requested during the database install part.
if (!$skipdatabase) {
    // Ask for fullname.
    if ($interactive) {
        cli_separator();
        cli_heading(get_string('fullsitename', 'moodle'));

        if ($options['fullname'] !== '') {
            $prompt = get_string('clitypevaluedefault', 'admin', $options['fullname']);
        } else {
            $prompt = get_string('clitypevalue', 'admin');
        }

        do {
            $options['fullname'] = cli_input($prompt, $options['fullname']);
        } while (empty($options['fullname']));
    } else {
        if (empty($options['fullname'])) {
            $a = (object)['option' => 'fullname', 'value' => $options['fullname']];
            cli_error(get_string('cliincorrectvalueerror', 'admin', $a));
        }
    }

    // Ask for shortname.
    if ($interactive) {
        cli_separator();
        cli_heading(get_string('shortsitename', 'moodle'));

        if ($options['shortname'] !== '') {
            $prompt = get_string('clitypevaluedefault', 'admin', $options['shortname']);
        } else {
            $prompt = get_string('clitypevalue', 'admin');
        }

        do {
            $options['shortname'] = cli_input($prompt, $options['shortname']);
        } while (empty($options['shortname']));
    } else {
        if (empty($options['shortname'])) {
            $a = (object)['option' => 'shortname', 'value' => $options['shortname']];
            cli_error(get_string('cliincorrectvalueerror', 'admin', $a));
        }
    }

    // Ask for admin user name.
    if ($interactive) {
        cli_separator();
        cli_heading(get_string('cliadminusername', 'install'));
        if (!empty($options['adminuser'])) {
            $prompt = get_string('clitypevaluedefault', 'admin', $options['adminuser']);
        } else {
            $prompt = get_string('clitypevalue', 'admin');
        }
        do {
            $options['adminuser'] = cli_input($prompt, $options['adminuser']);
        } while (empty($options['adminuser']) or $options['adminuser'] === 'guest');
    } else {
        if ((empty($options['adminuser']) || $options['adminuser'] === 'guest')) {
            $a = (object)['option' => 'adminuser', 'value' => $options['adminuser']];
            cli_error(get_string('cliincorrectvalueerror', 'admin', $a));
        }
    }

    // Ask for admin user password.
    if ($interactive) {
        cli_separator();
        cli_heading(get_string('cliadminpassword', 'install'));
        $prompt = get_string('clitypevalue', 'admin');
        do {
            $options['adminpass'] = cli_input($prompt);
        } while (empty($options['adminpass']) or $options['adminpass'] === 'admin');
    } else {
        if ((empty($options['adminpass']) or $options['adminpass'] === 'admin')) {
            $a = (object)['option' => 'adminpass', 'value' => $options['adminpass']];
            cli_error(get_string('cliincorrectvalueerror', 'admin', $a));
        }
    }

    // Ask for the admin email address.
    if ($interactive) {
        cli_separator();
        cli_heading(get_string('cliadminemail', 'install'));
        $prompt = get_string('clitypevaluedefault', 'admin', $options['adminemail']);
        $options['adminemail'] = cli_input($prompt, $options['adminemail']);
    }

    // Validate that the address provided was an e-mail address.
    if (!empty($options['adminemail']) && !validate_email($options['adminemail'])) {
        $a = (object)['option' => 'adminemail', 'value' => $options['adminemail']];
        cli_error(get_string('cliincorrectvalueerror', 'admin', $a));
    }
}

// Ask for the upgrade key.
if ($interactive) {
    cli_separator();
    cli_heading(get_string('upgradekeyset', 'admin'));
    if ($options['upgradekey'] !== '') {
        $prompt = get_string('clitypevaluedefault', 'admin', $options['upgradekey']);
        $options['upgradekey'] = cli_input($prompt, $options['upgradekey']);
    } else {
        $prompt = get_string('clitypevalue', 'admin');
        $options['upgradekey'] = cli_input($prompt);
    }
}

// Set the upgrade key if it was provided.
if ($options['upgradekey'] !== '') {
    $CFG->upgradekey = $options['upgradekey'];
}

// The user does not also need to pass agree-license when --skip-database is provided as the user will need to accept
// the license again in the database install part.
if (!$skipdatabase) {
    if ($interactive) {
        if (!$options['agree-license']) {
            cli_separator();
            cli_heading(get_string('copyrightnotice'));
            echo "Moodle  - Modular Object-Oriented Dynamic Learning Environment\n";
            echo get_string('gpl3')."\n\n";
            echo get_string('doyouagree')."\n";
            $prompt = get_string('cliyesnoprompt', 'admin');
            $input = cli_input($prompt, '', array(get_string('clianswerno', 'admin'), get_string('cliansweryes', 'admin')));
            if ($input == get_string('clianswerno', 'admin')) {
                exit(1);
            }
        }
    } else {
        if (!$options['agree-license'] && !$skipdatabase) {
            cli_error(get_string('climustagreelicense', 'install'));
        }
    }
}

// Finally we have all info needed for config.php
$configphp = install_generate_configphp($database, $CFG);
umask(0137);
if (($fh = fopen($configfile, 'w')) !== false) {
    fwrite($fh, $configphp);
    fclose($fh);
}

if (!file_exists($configfile)) {
    cli_error('Can not create config file.');
}

// remember selected language
$installlang = $CFG->lang;
// return back to original dir before executing setup.php which changes the dir again
chdir($olddir);
// We have config.php, it is a real php script from now on :-)
require($configfile);

// use selected language
$CFG->lang = $installlang;
$SESSION->lang = $CFG->lang;

require("$CFG->dirroot/version.php");

// Test environment first.
require_once($CFG->libdir . '/environmentlib.php');
list($envstatus, $environment_results) = check_moodle_environment(normalize_version($release), ENV_SELECT_RELEASE);
if (!$envstatus) {
    $errors = environment_get_errors($environment_results);
    cli_heading(get_string('environment', 'admin'));
    foreach ($errors as $error) {
        list($info, $report) = $error;
        echo "!! $info !!\n$report\n\n";
    }
    exit(1);
}

// Test plugin dependencies.
$failed = array();
if (!core_plugin_manager::instance()->all_plugins_ok($version, $failed)) {
    cli_problem(get_string('pluginscheckfailed', 'admin', array('pluginslist' => implode(', ', array_unique($failed)))));
    cli_error(get_string('pluginschecktodo', 'admin'));
}

if (!$skipdatabase) {
    install_cli_database($options, $interactive);
    // This needs to happen at the end to ensure it occurs after all caches
    // have been purged for the last time.
    // This will build a cached version of the current theme for the user
    // to immediately start browsing the site.
    require_once($CFG->libdir.'/upgradelib.php');
    upgrade_themes();
} else {
    echo get_string('cliskipdatabase', 'install')."\n";
}

echo get_string('cliinstallfinished', 'install')."\n";
exit(0); // 0 means success
