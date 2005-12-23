<?php
///This file only manages the installation of 1.6 lang packs.
///in downloads.moodle.org, they are store in separate directory
///in local server, they are stored in $CFG->dataroot/lang
///This helps to avoid confusion.


    /*********************************************
     * Problem with directory permisssion        *
     * /temp, /lang                              *
     *********************************************/

    include('../config.php');
    $mode = optional_param('mode',0,PARAM_INT);    //phase
    $pack = optional_param('pack','',PARAM_NOTAGS);    //pack to install
    $displaylang = optional_param('displaylang','',PARAM_ALPHA);    //display language
    $uninstalllang = optional_param('uninstalllang','',PARAM_NOTAGS);
    require_login();

    if (!isadmin()) {
        error('You must be an admin');
    }
    
    $strlang = get_string('langimport','admin');
    
    print_header($strlang, $strlang, $strlang);
    
    print_heading('');

    switch ($mode){
    
        case 2:    //mode 2 confirmation
        
        if (confirm_sesskey()){
            print_simple_box_start('center','100%');
            echo '<div align="center">';
            echo '<form name="langform" action="langimport.php?mode=3" method="POST">';
            echo '<input name="pack" type="hidden" value="'.$pack.'" />';
            echo '<input name="displaylang" type="hidden" value="'.$displaylang.'" />';
            echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
            print_heading(get_string('confirminstall','admin',$displaylang),2);
            echo '<input type="submit" value="'.get_string('ok').'"/>';
            echo '&nbsp;<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
            echo '</form>';
            echo '</div>';
            print_simple_box_end();
        }
        break;
        
        case 3:    //mode 3 process (copy, unzip, write md5 file, cleanup)
        
        if (confirm_sesskey()){
            @mkdir ($CFG->dataroot.'/temp/');
            @mkdir ($CFG->dataroot.'/lang/');
            $source = 'http://download.moodle.org/lang16/'.$pack.'.zip';
            $langpack = $CFG->dataroot.'/temp/'.$pack.'.zip';
            $destination = $CFG->dataroot.'/lang/';
            if ($contents = file_get_contents($source)) {    // Grab whole page
                if ($file = fopen($langpack, 'w')) {    // Make local copy
                    if (!fwrite($file, $contents)){    //copy zip to temp folder..
                        error ('could not copy file');
                    }
                    fclose($file);

                    ///recursively remove the whole directory since unzip does not overwrites anything
                    if (file_exists($destination.$pack)){
                        @remove_dir($destination.$pack.'/');
                    }
                    //unpack the zip
                    if (unzip_file($langpack, $destination, false)){
                        print_heading(get_string('langimportsuccess','admin'));
                    }
                    else {
                        error('language installation failed');
                    }
                    //now, we update the md5key of the lang pack, this is used to check version
                    $md5file = $CFG->dataroot.'/lang/'.$pack.'/'.$pack.'.md5';
                    if ($file = fopen($md5file, 'w')){
                        fwrite($file, md5($contents));    //we should not pass md5 value from moodle.org, because some sites can't fopen,, and the value will not be obtainable
                    }
                    fclose($file);
                    @unlink ($langpack);    //remove the zip file
                    echo '<div align="center"><form action="langimport.php" method="POST">';
                    echo '<input type="submit" value="'.get_string('ok').'" />';
                    echo '</form></div>';
                }
            }
        }
        
        break;
        case 4:

        if (!optional_param('confirm') && confirm_sesskey()){
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
        else if (confirm_sesskey()){
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
            if ($rm1 or $rm2){
                echo '<div align="center">';
                print_string('langpackremoved','admin');
                echo '<form action="langimport.php" method="POST">';
                echo '<input type="submit" value="'.get_string('ok').'" />';
                echo '</form></div>';
            }
            else {
                error ('An error has occured, language pack is not completely uninstalled');
            }
        }
        break;
        
        default:    //display choice mode

            $source = 'http://download.moodle.org/lang16/languages.md5';
            $remote = 0;    //flag for reading from remote or local
            if ($fp = fopen($source, 'r')){    /// attempt to get the list from Moodle.org.
                while(!feof ($fp)) {
                    $availablelangs[] = split(',', fgets($fp,1024));
                }
                $remote = 1;
            }
            else {    /// fopen failed, we find local copy of list.
                $availablelangs = get_local_list_of_languages();
            }
            
            if (!$remote){
                print_simple_box_start('center','60%');
                echo '<div align="center">';
                print_string('remotelangnotavailable');
                echo '</div>';
                print_simple_box_end();
            }
            
            print_simple_box_start('center','60%');
            echo '<table width="100%"><tr><td align="center">';
            echo get_string('installedlangs','admin');
            echo '</td><td align="center">';
            echo get_string('availablelangs','admin');
            echo '</td></tr>';
            echo '<tr><td align="right" valign="top">';
            echo '<form name="uninstallform" action="langimport.php?mode=4" method="POST">';
            echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
            $installedlangs = get_list_of_languages();    ///THIS FUNCTION NEEDS TO BE CHANGED
            /// display nstalled Components here
            echo '<select name="uninstalllang" size="15">';
            foreach ($installedlangs as $clang =>$ilang){
                echo '<option value="'.$clang.'">'.$ilang.'</option>';
            }
            echo '</select>';
            echo '<input type="submit" value="'.get_string('uninstall','admin').'" />';
            echo '</form></td><td align="center">';

            /// display to be installed Components here
            //echo '<form name="langform" action="langimport.php?mode=2" method="POST">';
            //echo '<input name="pack" type="hidden" value="" />';
            //echo '<input name="displaylang" type="hidden" value="" />';
            echo '<table>';    //availabe langs table

            $empty = 1;    //something to pring
            /// if this language pack is not already installed, then we allow installation

            ///PROBLEM is can't FOPEN, then can't obtain newest MD5 to check against
            ///Don't know if the version is current

            foreach ($availablelangs as $alang){
                if (!is_installed_lang($alang[0], $alang[1])){    //if not already installed
                    echo '<form method="POST" action="langimport.php?mode=2&pack='.$alang[0].'&displaylang='.$alang[2].'">';
                    echo '<input name="sesskey" type="hidden" value="'.sesskey().'" />';
                    if ($remote){
                        echo '<tr><td>'.$alang[2].'&nbsp;'.$alang[0].'</td><td><input type="submit" value="'.get_string('install','admin').'"></td></tr>';
                    }
                    else {    //print list in local format, and instruction to install
                        echo '<tr><td>'.$alang[2].'</td><td><a href="http://download.moodle.org/lang/'.$alang[0].'.zip">'.get_string('download').'</a>';
                    }
                    $empty = 0;
                    echo '</form>';
                }
            }
            if ($empty){
                echo '<tr><td align="center">';
                print_string('nolanguagetodownload');
                echo '</td></tr>';
            }

            echo '</table>';    //close available langs table
            echo '<form>';
            echo '</td></tr></table>';
            print_simple_box_end();

        break;

    }    //close of main switch

    print_footer();
    
    //returns a list of available language packs from a local copy shipped with standard moodle distro
    //this is for site that can't perform fopen
    function get_local_list_of_languages(){
        global $CFG;
        $source = $CFG->wwwroot.'/lib/languages.txt';
        $availablelangs = array();
        if ($fp = fopen($source, 'r')){
            while(!feof ($fp)) {
                $availablelangs[] = split(',', fgets($fp,1024));
            }
        }
        return $availablelangs;
    }
    
    //checks the md5 of the zip file, grabbed from download.moodle.org, and
    function is_installed_lang($lang, $md5check){
        global $CFG;
        $md5file = $CFG->dataroot.'/lang/'.$lang.'/'.$lang.'.md5';
        if (file_exists($md5file)){
            return (file_get_contents($md5file) == $md5check);
        }

        return false;
    }
    
?>
