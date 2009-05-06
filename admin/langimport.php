<?php //$Id$
///This file only manages the installation of 1.6 lang packs.
///in downloads.moodle.org, they are store in separate directory /lang16
///in local server, they are stored in $CFG->dataroot/lang
///This helps to avoid confusion.

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->libdir.'/componentlib.class.php');

    admin_externalpage_setup('langimport');

    if (!empty($CFG->skiplangupgrade)) {
        admin_externalpage_print_header();
        print_box(get_string('langimportdisabled', 'admin'));
        print_footer();
        die;
    }

    $mode          = optional_param('mode', 0, PARAM_INT);     //phase
    $pack          = optional_param('pack', array(), PARAM_FILE);   //pack to install
    $displaylang   = $pack;
    $uninstalllang = optional_param('uninstalllang', '', PARAM_FILE);
    $confirm       = optional_param('confirm', 0, PARAM_BOOL);
    $sitelang      = optional_param('sitelangconfig', '', PARAM_FILE);

    define('INSTALLATION_OF_SELECTED_LANG', 2);
    define('DELETION_OF_SELECTED_LANG', 4);
    define('UPDATE_ALL_LANG', 5);

    $strlang         = get_string('langimport','admin');
    $strlanguage     = get_string('language');
    $strthislanguage = get_string('thislanguage');
    $title           = $strlang;

    //reset and diagnose lang cache permissions
    @unlink($CFG->dataroot.'/cache/languages');
    if (file_exists($CFG->dataroot.'/cache/languages')) {
        error('Language cache can not be deleted, please fix permissions in dataroot/cache/languages!');
    }
    get_list_of_languages(true); //refresh lang cache

    $notice_ok     = array();
    $notice_error = array();

    switch ($mode){

        case INSTALLATION_OF_SELECTED_LANG:    ///installation of selected language pack

            if (confirm_sesskey() and !empty($pack)) {
                set_time_limit(0);
                @mkdir ($CFG->dataroot.'/temp/', $CFG->directorypermissions);    //make it in case it's a fresh install, it might not be there
                @mkdir ($CFG->dataroot.'/lang/', $CFG->directorypermissions);

                if (is_array($pack)) {
                    $packs = $pack;
                } else {
                    $packs = array($pack);
                }

                foreach ($packs as $pack) {
                    if ($cd = new component_installer('http://download.moodle.org', 'lang16',
                                                        $pack.'.zip', 'languages.md5', 'lang')) {
                        $status = $cd->install(); //returns COMPONENT_(ERROR | UPTODATE | INSTALLED)
                        switch ($status) {

                            case COMPONENT_ERROR:
                                if ($cd->get_error() == 'remotedownloaderror') {
                                    $a = new object();
                                    $a->url = 'http://download.moodle.org/lang16/'.$pack.'.zip';
                                    $a->dest= $CFG->dataroot.'/lang';
                                    print_error($cd->get_error(), 'error', 'langimport.php', $a);
                                } else {
                                    print_error($cd->get_error(), 'error', 'langimport.php');
                                }
                            break;

                            case COMPONENT_INSTALLED:
                                $notice_ok[] = get_string('langpackinstalled','admin',$pack);
                            break;

                            case COMPONENT_UPTODATE:
                            break;

                        }
                    } else {
                        notify('Had an unspecified error with the component installer, sorry.');
                    }
                }
            }
        break;

        case DELETION_OF_SELECTED_LANG:    //delete a directory(ies) containing a lang pack completely

            if ($uninstalllang == 'en_utf8') {
                $notice_error[] = 'en_utf8 can not be uninstalled!';

            } else if (!$confirm && confirm_sesskey()) {
                admin_externalpage_print_header();
                notice_yesno(get_string('uninstallconfirm', 'admin', $uninstalllang),
                             'langimport.php?mode='.DELETION_OF_SELECTED_LANG.'&amp;uninstalllang='.$uninstalllang.'&amp;confirm=1&amp;sesskey='.sesskey(),
                             'langimport.php');
                print_footer();
                die;

            } else if (confirm_sesskey()) {
                $dest1 = $CFG->dataroot.'/lang/'.$uninstalllang;
                $dest2 = $CFG->dirroot.'/lang/'.$uninstalllang;
                $rm1 = false;
                $rm2 = false;
                if (file_exists($dest1)){
                    $rm1 = remove_dir($dest1);
                }
                if (file_exists($dest2)){
                    $rm2 = remove_dir($dest2);
                }
                get_list_of_languages(true); //refresh lang cache
                //delete the direcotries
                if ($rm1 or $rm2) {
                    $notice_ok[] = get_string('langpackremoved','admin');
                } else {    //nothing deleted, possibly due to permission error
                    $notice_error[] = 'An error has occurred, language pack is not completely uninstalled, please check file permissions';
                }
            }
        break;

        case UPDATE_ALL_LANG:    //1 click update for all updatable language packs
            set_time_limit(0);

            //0th pull a list from download.moodle.org,
            //key = langname, value = md5
            $md5array = array();
            $updated = 0;    //any packs updated?
            $alllangs = array_keys(get_list_of_languages(false, true)); //get all available langs
            $lang16 = array();   //all the Moodle 1.6 unicode lang packs (updated and not updated)
            $packs = array();    //all the packs that needs updating


            if (!$availablelangs = get_remote_list_of_languages()) {
                print_error('cannotdownloadlanguageupdatelist');
            }

            //and build an associative array
            foreach ($availablelangs as $alang) {
                $md5array[$alang[0]] = $alang[1];
            }

            //filtering out non-16 and unofficial packs
            foreach ($alllangs as $clang) {
                if (!array_key_exists($clang, $md5array)) {
                    $notice_ok[] = get_string('langpackupdateskipped', 'admin', $clang);
                    continue;
                }
                $dest1 = $CFG->dataroot.'/lang/'.$clang;
                $dest2 = $CFG->dirroot.'/lang/'.$clang;

                if (file_exists($dest1.'/langconfig.php') || file_exists($dest2.'/langconfig.php')){
                    $lang16[] = $clang;
                }
            }

            //then filter out packs that have the same md5 key
            foreach ($lang16 as $clang) {
                if (!is_installed_lang($clang, $md5array[$clang])){
                    $packs[] = $clang;
                }
            }

            @mkdir ($CFG->dataroot.'/temp/', $CFG->directorypermissions);
            @mkdir ($CFG->dataroot.'/lang/', $CFG->directorypermissions);
            foreach ($packs as $pack){    //for each of the remaining in the list, we
                if ($pack == 'en_utf8') {    // no update for en_utf8
                    continue;
                }

                //1. delete old director(ies)

                $dest1 = $CFG->dataroot.'/lang/'.$pack;
                $dest2 = $CFG->dirroot.'/lang/'.$pack;
                $rm1 = false;
                $rm2 = false;
                if (file_exists($dest1)) {
                    if (!remove_dir($dest1)) {
                        $notice_error[] = 'Could not delete old directory '.$dest1.', update of '.$pack.' failed, please check permissions.';
                        continue;
                    }
                }
                if (file_exists($dest2)) {
                    if (!remove_dir($dest2)) {
                        $notice_error[] = 'Could not delete old directory '.$dest2.', update of '.$pack.' failed, please check permissions.';
                        continue;
                    }
                }

                //2. copy & unzip into new

                if ($cd = new component_installer('http://download.moodle.org', 'lang16',
                                       $pack.'.zip', 'languages.md5', 'lang')) {
                $status = $cd->install(); //returns COMPONENT_(ERROR | UPTODATE | INSTALLED)
                switch ($status) {

                    case COMPONENT_ERROR:
                        if ($cd->get_error() == 'remotedownloaderror') {
                            $a = new stdClass();
                            $a->url = 'http://download.moodle.org/lang16/'.$pack.'.zip';
                            $a->dest= $CFG->dataroot.'/lang';
                            print_error($cd->get_error(), 'error', "", $a); // not probable
                        } else {
                            print_error($cd->get_error(), 'error'); // not probable
                        }
                    break;
                    case COMPONENT_UPTODATE:
                        //Print error string or whatever you want to do
                    break;
                    case COMPONENT_INSTALLED:
                        $notice_ok[] = get_string('langpackupdated', 'admin', $pack);
                        $updated = true;
                        //Print/do whatever you want
                    break;
                    default:
                    }
                } else {

                }
            }

            if ($updated) {
                $notice_ok[] = get_string('langupdatecomplete','admin');
            } else {
                $notice_ok[] = get_string('nolangupdateneeded','admin');
            }

        break;
    }    //close of main switch


    admin_externalpage_print_header();

    $installedlangs = get_list_of_languages(true, true);

    $missingparents = array();
    $oldlang = isset($SESSION->lang) ? $SESSION->lang : null; // override current lang

    foreach($installedlangs as $l=>$unused) {
        $SESSION->lang = $l;
        $parent = get_string('parentlanguage');
        if ($parent == 'en_utf8') {
            continue;
        }
        if (strpos($parent, '[[') !== false) {
            continue; // no parent
        }
        if (!isset($installedlangs[$parent])) {
            $missingparents[$l] = $parent;
        }
    }
    if (isset($oldlang)) {
        $SESSION->lang = $oldlang;
    } else {
        unset($SESSION->lang);
    }

    if ($availablelangs = get_remote_list_of_languages()) {
        $remote = 1;
    } else {
        $remote = 0;    //flag for reading from remote or local
        $availablelangs = get_local_list_of_languages();
    }

    if (!$remote) {
        print_box_start();
        print_string('remotelangnotavailable', 'admin', $CFG->dataroot.'/lang/');
        print_box_end();
    }

    if ($notice_ok) {
        $info = implode('<br />', $notice_ok);
        notify($info, 'notifysuccess');
    }

    if ($notice_error) {
        $info = implode('<br />', $notice_error);
        notify($info, 'notifyproblem');
    }

    if ($missingparents) {
        foreach ($missingparents as $l=>$parent) {
            $a = new object();
            $a->lang   = $installedlangs[$l];
            $a->parent = $parent;
            foreach ($availablelangs as $alang) {
                if ($alang[0] == $parent) {
                    if (substr($alang[0], -5) == '_utf8') {   //Remove the _utf8 suffix from the lang to show
                        $shortlang = substr($alang[0], 0, -5);
                    } else {
                        $shortlang = $alang[0];
                    }
                    $a->parent = $alang[2].' ('.$shortlang.')';
                }
            }
            $info = get_string('missinglangparent', 'admin', $a);
            notify($info, 'notifyproblem');
        }
    }

    print_box_start();
    echo '<table summary="">';
    echo '<tr><td align="center" valign="top">';
    echo '<form id="uninstallform" action="langimport.php?mode='.DELETION_OF_SELECTED_LANG.'" method="post">';
    echo '<fieldset class="invisiblefieldset">';
    echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';

    /// display installed langs here

    echo '<label for="uninstalllang">'.get_string('installedlangs','admin')."</label><br />\n";
    echo '<select name="uninstalllang" id="uninstalllang" size="15">';
    foreach ($installedlangs as $clang =>$ilang){
        echo '<option value="'.$clang.'">'.$ilang.'</option>';
    }
    echo '</select>';
    echo '<br /><input type="submit" value="'.get_string('uninstall','admin').'" />';
    echo '</fieldset>';
    echo '</form>';

    if ($remote) {
        echo '<form id="updateform" action="langimport.php?mode='.UPDATE_ALL_LANG.'" method="post">';
        echo '<div>';
        echo '<br /><input type="submit" value="'.get_string('updatelangs','admin').'" />';
        echo '</div>';
        echo '</form>';
    }

    /// Display option to change site language

    /// display to be installed langs here

    echo '</td><td align="center" valign="top">';
    //availabe langs table
    $empty = 1;    //something to pring

    /// if this language pack is not already installed, then we allow installation

    echo '<form id="installform" method="post" action="langimport.php?mode='.INSTALLATION_OF_SELECTED_LANG.'">';
    echo '<fieldset class="invisiblefieldset">';
    echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
    echo '<label for="pack">'.get_string('availablelangs','admin')."</label><br />\n";
    if ($remote) {
        echo '<select name="pack[]" id="pack" size="15" multiple="multiple">';
    }

    foreach ($availablelangs as $alang) {
        if ($alang[0] == '') {
            continue;
        }
        if (trim($alang[0]) != "en_utf8") {
            if ($remote) {
                if (substr($alang[0], -5) == '_utf8') {   //Remove the _utf8 suffix from the lang to show
                    $shortlang = substr($alang[0], 0, -5);
                } else {
                    $shortlang = $alang[0];
                }
                if (!is_installed_lang($alang[0], $alang[1])){    //if not already installed
                    echo '<option value="'.$alang[0].'">'.$alang[2].' ('.$shortlang.')</option>';
                }
            } else {    //print list in local format, and instruction to install
                echo '<tr><td>'.$alang[2].'</td><td><a href="http://download.moodle.org/lang16/'.$alang[0].'.zip">'.get_string('download','admin').'</a></td></tr>';
            }
            $empty = 0;
        }
    }
    if ($remote) {
        echo '</select>';
        echo '<br /><input type="submit" value="'.$THEME->larrow.' '.get_string('install','admin').'" />';
    }
    echo '</fieldset>';
    echo '</form>';

    if ($empty) {
        echo '<br />';
        print_string('nolanguagetodownload','admin');
    }

    //close available langs table
    echo '</td></tr></table>';
    print_box_end();

    admin_externalpage_print_footer();

    /**
     * Returns a list of available language packs from a
     * local copy shipped with standard moodle distro
     * this is for site that can't download components.
     * @return array
     */
    function get_local_list_of_languages() {
        global $CFG;
        $source = $CFG->dirroot.'/lib/languages.md5';
        $availablelangs = array();
        if ($fp = fopen($source, 'r')) {
            while(!feof ($fp)) {
                $availablelangs[] = split(',', fgets($fp,1024));
            }
        }
        return $availablelangs;
    }

    /**
     * checks the md5 of the zip file, grabbed from download.moodle.org,
     * against the md5 of the local language file from last update
     * @param string $lang
     * @param string $md5check
     * @return bool
     */
    function is_installed_lang($lang, $md5check) {
        global $CFG;
        $md5file = $CFG->dataroot.'/lang/'.$lang.'/'.$lang.'.md5';
        if (file_exists($md5file)){
            return (file_get_contents($md5file) == $md5check);
        }
        return false;
    }

    /**
     * Returns the latest list of available language packs from
     * moodle.org
     * @return array or false if can not download
     */
    function get_remote_list_of_languages() {
        $source = 'http://download.moodle.org/lang16/languages.md5';
        $availablelangs = array();

        if ($content = download_file_content($source)) {
            $alllines = split("\n", $content);
            foreach($alllines as $line) {
                if (!empty($line)){
                    $availablelangs[] = split(',', $line);
                }
            }
            return $availablelangs;

        } else {
            return false;
        }
    }
?>
