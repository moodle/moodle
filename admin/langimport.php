<?php
///This file only manages the installation of 1.6 lang packs.
///in downloads.moodle.org, they are store in separate directory /lang16
///in local server, they are stored in $CFG->dataroot/lang
///This helps to avoid confusion.

    define('INSTALLATION_OF_SELECTED_LANG', 2);
    define('CHANGE_SITE_LANG', 3);
    define('DELETION_OF_SELECTED_LANG', 4);
    define('UPDATE_ALL_LANG', 5);

    include('../config.php');
    $mode = optional_param('mode',0,PARAM_INT);    //phase
    $pack = optional_param('pack','',PARAM_NOTAGS);    //pack to install
    $displaylang = $pack;
    $uninstalllang = optional_param('uninstalllang','',PARAM_NOTAGS);
    require_login();

    if (!isadmin()) {
        error('You must be an admin');
    }
    
    if (!$site = get_site()) {
        error("Site not defined!");
    }
    
    $strlang = get_string('langimport','admin');
    
    $stradministration = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strlanguage = get_string("language");
    $strthislanguage = get_string("thislanguage");
    $title = $strlang;
    
    print_header("$site->shortname: $title", "$site->fullname",
                 "<a href=\"index.php\">$stradministration</a> -> ".
                 "<a href=\"configure.php\">$strconfiguration</a> -> ".
                 "<a href=\"lang.php\">$strlanguage</a> -> $strlang",
                 '', '', true, '');
    
    print_heading('');

    switch ($mode){
    
        case INSTALLATION_OF_SELECTED_LANG:    ///installation of selected language pack
        
            if (confirm_sesskey()) {
                if (optional_param('confirm')) {
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
                        case UPTODATE:
                            
                        break;
                        case INSTALLED:
                            print_string('langpackupdated','admin',$pack);
                            print_continue('langimport.php');
                        break;
                        default:
                            //We shouldn't reach this point
                        }
                    } else {
                        //We shouldn't reach this point
                    }

                } else {    //print confirm box, no confirmation yet
                    if (confirm_sesskey()) {
                        print_simple_box_start('center','100%');
                        echo '<div align="center">';
                        echo '<form name="langform" action="langimport.php?mode=2" method="POST">';
                        echo '<input name="pack" type="hidden" value="'.$pack.'" />';
                        echo '<input name="displaylang" type="hidden" value="'.$displaylang.'" />';
                        echo '<input name="confirm" type="hidden" value="1" />';
                        echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
                        print_heading(get_string('confirminstall','admin',$displaylang),2);
                        echo '<input type="submit" value="'.get_string('ok').'"/>';
                        echo '&nbsp;<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
                        echo '</form>';
                        echo '</div>';
                        print_simple_box_end();
                    }
                }
            }
        break;

        case CHANGE_SITE_LANG:    //change site language

            if (confirm_sesskey) {
                $langconfig = get_record('config','name','lang');
                $sitelang = required_param('sitelangconfig',PARAM_NOTAGS);
                $langconfig->value = $sitelang;
                if (update_record('config',$langconfig)){
                    echo '<div align="center">';
                    notify (get_string('sitelangchanged','admin'));
                    echo '<form action="langimport.php" method="POST">';
                    echo '<input type="submit" value="'.get_string('ok').'" />';
                    echo '</form></div>';
                } else {
                    error ('can not update site language');
                }
            }

        break;
        case DELETION_OF_SELECTED_LANG:    //delete a directory(ies) containing a lang pack completely

            if (!optional_param('confirm') && confirm_sesskey()) {
                print_simple_box_start('center','100%');
                echo '<div align="center">';
                echo '<form name="langform" action="langimport.php?mode=4" method="POST">';
                echo '<input name="uninstalllang" type="hidden" value="'.$uninstalllang.'" />';
                echo '<input name="confirm" type="hidden" value="1" />';
                print_heading(get_string('uninstallconfirm','admin',$uninstalllang),2);
                echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
                echo '<input type="submit" value="'.get_string('uninstall','admin').'"/>';
                echo '&nbsp;<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
                echo '</form>';
                echo '</div>';
                print_simple_box_end();
            }
            else if (confirm_sesskey()) {
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
                    echo '<div align="center">';
                    print_string('langpackremoved','admin');
                    echo '<form action="langimport.php" method="POST">';
                    echo '<input type="submit" value="'.get_string('ok').'" />';
                    echo '</form></div>';
                } else {    //nothing deleted, possibly due to permission error
                    error ('An error has occurred, language pack is not completely uninstalled, please check file permission');
                }
            }
        break;
        
        case UPDATE_ALL_LANG:    //1 click update for all updatable language packs

            //0th pull a list from download.moodle.org,
            //key = langname, value = md5
            $source = 'http://download.moodle.org/lang16/languages.md5';
            $md5array = array();
            $updated = 0;    //any packs updated?
            $alllangs = array_keys(get_list_of_languages());
            $lang16 = array();   //all the Moodle 1.6 unicode lang packs (updated and not updated)
            $packs = array();    //all the packs that needs updating

            if ($fp = fopen($source, 'r')) {    /// attempt to get the list from Moodle.org.
                while(!feof ($fp)) {
                    $availablelangs[] = split(',', fgets($fp,1024));
                }
            } else {
                error('can not fopen!');
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
                        print_string('langpackupdated','admin',$pack);
                        $updated = true;
                        //Print/do whatever you want
                    break;
                    default:
                    }
                } else {

                }
            }

            echo '<div align="center"><form action="langimport.php" method="POST">';
            if ($updated) {
                notify(get_string('langupdatecomplete','admin'));
            } else {
                notify(get_string('nolangupdateneeded','admin'));
            }
            echo '<input type="submit" value="'.get_string('ok').'" />';
            echo '</form></div>';

        break;
        
        default:    //display choice mode

            $source = 'http://download.moodle.org/lang16/languages.md5';
            $remote = 0;    //flag for reading from remote or local

            if ($fp = fopen($source, 'r')){    /// attempt to get the list from Moodle.org.
                while(!feof ($fp)) {
                    $availablelangs[] = split(',', fgets($fp,1024));
                }
                $remote = 1;    //can read from download.moodle.org
            } else {    /// fopen failed, we find local copy of list.
                $availablelangs = get_local_list_of_languages();
            }
            
            if (!$remote) {
                print_simple_box_start('center','60%');
                echo '<div align="center">';
                print_string('remotelangnotavailable','admin',$CFG->dataroot.'/lang/');
                echo '</div>';
                print_simple_box_end();
            }
            
            print_simple_box_start('center','60%');
            echo '<table width="100%"><tr><td align="center">';
            echo get_string('installedlangs','admin');
            echo '</td><td align="center">';
            echo get_string('availablelangs','admin');
            echo '</td></tr>';
            echo '<tr><td align="center" valign="top">';
            echo '<form name="uninstallform" action="langimport.php?mode=4" method="POST">';
            echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
            $installedlangs = get_list_of_languages();

            /// display installed langs here

            echo '<select name="uninstalllang" size="15">';
            foreach ($installedlangs as $clang =>$ilang){
                echo '<option value="'.$clang.'">'.$ilang.'</option>';
            }
            echo '</select>';
            echo '<br /><input type="submit" value="'.get_string('uninstall','admin').'" />';
            echo '</form>';
            echo '<form name="updateform" action="langimport.php?mode=5" method="POST">';
            echo '<br /><input type="submit" value="'.get_string('updatelangs','admin').'" />';
            echo '</form>';
            echo '<p />';
            
            /// Display option to change site language
            
            print_string('changesitelang','admin');
            $sitelanguage = get_record('config','name','lang');
            echo '<form name="changelangform" action="langimport.php?mode=3" method="POST">';
            echo '<select name="sitelangconfig">';
            
            foreach ($installedlangs as $clang =>$ilang) {
                if ($clang == $sitelanguage->value){
                    echo '<option value="'.$clang.'" selected="selected">'.$ilang.'</option>';
                } else {
                    echo '<option value="'.$clang.'">'.$ilang.'</option>';
                }
            }
            echo '</select>';
            echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
            echo '<input type="submit" value="'.get_string('change','admin').'" />';
            echo '</form>';

            /// display to be installed langs here

            echo '</td><td align="center" valign="top">';
            echo '<table><tr><td valign="top" align="center">';    //availabe langs table
            $empty = 1;    //something to pring

            /// if this language pack is not already installed, then we allow installation

            echo '<form name="installform" method="POST" action="langimport.php?mode=2">';
            echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
            if ($remote) {
                echo '<select name="pack" size="15">';
            }

            foreach ($availablelangs as $alang) {
                if ($remote){
                    if (!is_installed_lang($alang[0], $alang[1])){    //if not already installed
                        echo '<option value="'.$alang[0].'">'.$alang[2].' ('.$alang[0].')</option>';
                    }
                } else {    //print list in local format, and instruction to install
                    echo '<tr><td>'.$alang[2].'</td><td><a href="http://download.moodle.org/lang16/'.$alang[0].'.zip">'.get_string('download','admin').'</a>';
                }
                $empty = 0;
            }
            if ($remote) {
                echo '</select>';
                echo '<br/ ><input type="submit" value="'.get_string('install','admin').'">';
            }
            echo '</form>';

            if ($empty) {
                echo '<tr><td align="center">';
                print_string('nolanguagetodownload','admin');
                echo '</td></tr>';
            }

            echo '</td><tr></table>';    //close available langs table
            echo '<form>';
            echo '</td></tr></table>';
            print_simple_box_end();

        break;

    }    //close of main switch

    print_footer();
    
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
    
?>
