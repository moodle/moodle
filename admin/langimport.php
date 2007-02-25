<?php //$Id$
///This file only manages the installation of 1.6 lang packs.
///in downloads.moodle.org, they are store in separate directory /lang16
///in local server, they are stored in $CFG->dataroot/lang
///This helps to avoid confusion.

    require_once('../config.php');
    require_once($CFG->libdir.'/adminlib.php');
    $adminroot = admin_get_root();
    admin_externalpage_setup('langimport', $adminroot);

    $mode          = optional_param('mode', 0, PARAM_INT);     //phase
    $pack          = optional_param('pack', '', PARAM_FILE);   //pack to install
    $displaylang   = $pack;
    $uninstalllang = optional_param('uninstalllang', '', PARAM_FILE);
    $confirm       = optional_param('confirm', 0, PARAM_BOOL);
    $sitelang      = optional_param('sitelangconfig', '', PARAM_FILE);

    define('INSTALLATION_OF_SELECTED_LANG', 2);
    define('CHANGE_SITE_LANG', 3);
    define('DELETION_OF_SELECTED_LANG', 4);
    define('UPDATE_ALL_LANG', 5);

    $strlang = get_string('langimport','admin');

    $strlanguage = get_string("language");
    $strthislanguage = get_string("thislanguage");
    $title = $strlang;

    admin_externalpage_print_header($adminroot);


    switch ($mode){

        case INSTALLATION_OF_SELECTED_LANG:    ///installation of selected language pack

            if (confirm_sesskey()) {
                @mkdir ($CFG->dataroot.'/temp/');    //make it in case it's a fresh install, it might not be there
                @mkdir ($CFG->dataroot.'/lang/');

                require_once($CFG->libdir.'/componentlib.class.php');
                if ($cd = new component_installer('http://download.moodle.org', 'lang16',
                                                    $pack.'.zip', 'languages.md5', 'lang')) {
                    $status = $cd->install(); //returns ERROR | UPTODATE | INSTALLED
                    switch ($status) {

                        case ERROR:
                            if ($cd->get_error() == 'remotedownloadnotallowed') {
                                $a = new stdClass();
                                $a->url = 'http://download.moodle.org/lang16/'.$pack.'zip';
                                $a->dest= $CFG->dataroot.'/lang';
                                error(get_string($cd->get_error(), 'error', $a));
                            } else {
                                error(get_string($cd->get_error(), 'error'));
                            }
                        break;

                        case INSTALLED:
                            @unlink($CFG->dataroot.'/cache/languages');
                            redirect('langimport.php', get_string('langpackupdated','admin',$pack), -1, $adminroot);
                        break;

                        case UPTODATE:
                        break;

                    }
                } else {
                    notify('Had an unspecified error with the component installer, sorry.');
                }
            }
        break;

        case CHANGE_SITE_LANG:    //change site language

            if (confirm_sesskey()) {
                $langconfig = get_record('config','name','lang');
                $langconfig->value = $sitelang;
                if (!empty($sitelang) && update_record('config',$langconfig)){
                    redirect('langimport.php', get_string('sitelangchanged','admin'));
                } else {
                    error('Could not update the default site language!');
                }
            }

        break;
        case DELETION_OF_SELECTED_LANG:    //delete a directory(ies) containing a lang pack completely

            if (!$confirm && confirm_sesskey()) {
                notice_yesno(get_string('uninstallconfirm', 'admin', $uninstalllang),
                             'langimport.php?mode=4&amp;uninstalllang='.$uninstalllang.'&amp;confirm=1&amp;sesskey='.sesskey(),
                             'langimport.php');
            } else if (confirm_sesskey()) {
                if ($uninstalllang == 'en_utf8') {
                    error ('en_utf8 can not be uninstalled!');
                }
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
                //delete the direcotries
                if ($rm1 or $rm2) {
                    redirect('langimport.php', get_string('langpackremoved','admin'));
                } else {    //nothing deleted, possibly due to permission error
                    error('An error has occurred, language pack is not completely uninstalled, please check file permissions');
                }
            }
            @unlink($CFG->dataroot.'/cache/languages');
        break;

        case UPDATE_ALL_LANG:    //1 click update for all updatable language packs

            //0th pull a list from download.moodle.org,
            //key = langname, value = md5
            $source = 'http://download.moodle.org/lang16/languages.md5';
            $md5array = array();
            $updated = 0;    //any packs updated?
            unset($CFG->langlist);   // ignore admin's langlist
            $alllangs = array_keys(get_list_of_languages());
            $lang16 = array();   //all the Moodle 1.6 unicode lang packs (updated and not updated)
            $packs = array();    //all the packs that needs updating


            if (!$availablelangs = proxy_url($source)) {
                error ('can not read from course');
            }

            //and build an associative array
            foreach ($availablelangs as $alang) {
                $md5array[$alang[0]] = $alang[1];
            }

            //filtering out non-16 packs
            foreach ($alllangs as $clang) {
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

            @mkdir ($CFG->dataroot.'/temp/');
            @mkdir ($CFG->dataroot.'/lang/');
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
                    $rm1 = remove_dir($dest1);
                }
                if (file_exists($dest2)) {
                    $rm2 = remove_dir($dest2);
                }
                if (!($rm1 || $rm2)) {
                    error ('could not delete old directory, update failed');
                }

                //2. copy & unzip into new

                require_once($CFG->libdir.'/componentlib.class.php');
                if ($cd = new component_installer('http://download.moodle.org', 'lang16',
                                       $pack.'.zip', 'languages.md5', 'lang')) {
                $status = $cd->install(); //returns ERROR | UPTODATE | INSTALLED
                switch ($status) {

                    case ERROR:
                        if ($cd->get_error() == 'remotedownloadnotallowed') {
                            $a = new stdClass();
                            $a->url = 'http://download.moodle.org/lang16/'.$pack.'zip';
                            $a->dest= $CFG->dataroot.'/lang';
                            error(get_string($cd->get_error(), 'error', $a));
                        } else {
                            error(get_string($cd->get_error(), 'error'));
                        }
                    break;
                    case UPTODATE:
                        //Print error string or whatever you want to do
                    break;
                    case INSTALLED:
                        notify(get_string('langpackupdated','admin',$pack), 'notifysuccess');
                        $updated = true;
                        //Print/do whatever you want
                    break;
                    default:
                    }
                } else {

                }
            }

            if ($updated) {
                notice(get_string('langupdatecomplete','admin'), 'langimport.php', NULL, $adminroot);
            } else {
                notice(get_string('nolangupdateneeded','admin'), 'langimport.php', NULL, $adminroot);
            }

        break;

        default:    //display choice mode

            $source = 'http://download.moodle.org/lang16/languages.md5';
            $remote = 0;    //flag for reading from remote or local

            if ($availablelangs = proxy_url($source)) {
                $remote = 1;
            } else {
                $availablelangs = get_local_list_of_languages();
            }
/*
            if ($fp = fopen($source, 'r')){    /// attempt to get the list from Moodle.org.
                while(!feof ($fp)) {
                    $availablelangs[] = split(',', fgets($fp,1024));
                }
                $remote = 1;    //can read from download.moodle.org
            } else {    /// fopen failed, we find local copy of list.
                $availablelangs = get_local_list_of_languages();
            }
*/
            if (!$remote) {
                print_box_start();
                print_string('remotelangnotavailable','admin',$CFG->dataroot.'/lang/');
                print_box_end();
            }

            print_box_start();
            echo '<table summary="">';
            echo '<tr><td align="center" valign="top">';
            echo '<form id="uninstallform" action="langimport.php?mode=4" method="post">';
            echo '<fieldset class="invisiblefieldset">';
            echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
            unset($CFG->langlist);   // ignore admin's langlist
            $installedlangs = get_list_of_languages();

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
            echo '<form id="updateform" action="langimport.php?mode=5" method="post">';
            echo '<fieldset class="invisiblefieldset">';
            echo '<br /><input type="submit" value="'.get_string('updatelangs','admin').'" />';
            echo '</fieldset>';
            echo '</form>';

            /// Display option to change site language

            /// display to be installed langs here

            echo '</td><td align="center" valign="top">';
            //availabe langs table
            $empty = 1;    //something to pring

            /// if this language pack is not already installed, then we allow installation

            echo '<form id="installform" method="post" action="langimport.php?mode=2">';
            echo '<fieldset class="invisiblefieldset">';
            echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
            echo '<label for="pack">'.get_string('availablelangs','admin')."</label><br />\n";
            if ($remote) {
                echo '<select name="pack" id="pack" size="15">';
            }

            foreach ($availablelangs as $alang) {
                if (trim($alang[0]) != "en_utf8") {
                    if ($remote){
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
                echo '<br /><input type="submit" value="&larr; '.get_string('install','admin').'" />';
            }
            echo '</fieldset>';
            echo '</form>';

            if ($empty) {
                echo '<br />';
                print_string('nolanguagetodownload','admin');
            }

            //close available langs table
            echo '</td></tr></table>';
            print_simple_box_end();

        break;

    }    //close of main switch

    admin_externalpage_print_footer($adminroot);

    /* returns a list of available language packs from a
     * local copy shipped with standard moodle distro
     * this is for site that can't perform fopen
     * @return array
     */
    function get_local_list_of_languages() {
        global $CFG;
        $source = $CFG->wwwroot.'/lib/languages.md5';
        $availablelangs = array();
        if ($fp = fopen($source, 'r')){
            while(!feof ($fp)) {
                $availablelangs[] = split(',', fgets($fp,1024));
            }
        }
        return $availablelangs;
    }

    /* checks the md5 of the zip file, grabbed from download.moodle.org,
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

    //returns an array of languages, or false if can not read from source
    //uses a socket if proxy is set as a config variable
    function proxy_url($url) {
        global $CFG;

        if ($CFG->proxyhost && $CFG->proxyport) {

            $proxy_fp = fsockopen($CFG->proxyhost, $CFG->proxyport);
            if (!$proxy_fp) {
                return false;    //failed
            }
            fputs($proxy_fp, "GET $url HTTP/1.0\r\nHost: $CFG->proxyhost\r\n\r\n");
            $i = 0;
                while(!feof($proxy_fp)) {
                $string = fgets($proxy_fp, 1024);
                if ($i > 11) {    //12 lines of info skipped
                    $availablelangs[] = split(',', $string);
                }
                $i++;
            }
            fclose($proxy_fp);

        } else {    //proxy not in use
            if ($fp = fopen($url, 'r')){    /// attempt to get the list from Moodle.org.
                while(!feof ($fp)) {
                    $availablelangs[] = split(',', fgets($fp,1024));
                }
            } else {    /// fopen failed, return false.
                return false;
            }
        }
        return $availablelangs;
    }
?>
