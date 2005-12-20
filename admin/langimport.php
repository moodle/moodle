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
    $pack = optional_param('pack',PARAM_ALPHAEXT);    //pack to install
    $displaylang = optional_param('displaylang',PARAM_ALPHA);    //display language
    require_login();

    if (!isadmin()) {
        error('You must be an admin');
    }
    
    $strlang = get_string('languages');
    
    print_header($strlang, $strlang, $strlang);
    
    print_heading('');

    switch ($mode){
    
        case 2:    //mode 2 confirmation
        
        print_simple_box_start('center','100%');
        echo '<div align="center">';
        echo '<form name="langform" action="languages.php?mode=3" method="POST">';
        echo '<input name="pack" type="hidden" value="'.$pack.'" />';
        echo '<input name="displaylang" type="hidden" value="'.$displaylang.'" />';
        print_heading(get_string('confirm').'&nbsp;'.$displaylang,2);
        echo '<input type="submit" value="'.get_string('ok').'"/>';
        echo '&nbsp;<input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
        echo '</form>';
        echo '</div>';
        print_simple_box_end();
        break;
        
        case 3:    //mode 3 process (copy, unzip, write md5 file, cleanup)
        
        @mkdir ($CFG->dataroot.'/temp/');
        @mkdir ($CFG->dataroot.'/lang/');
        $source = 'http://download.moodle.org/lang/'.$pack.'.zip';
        $langpack = $CFG->dataroot.'/temp/'.$pack.'.zip';
        $destination = $CFG->dataroot.'/lang/';
        if ($contents = file_get_contents($source)) {    // Grab whole page
            if ($file = fopen($langpack, 'w')) {    // Make local copy
                if (!fwrite($file, $contents)){    //copy zip to temp folder..
                    error ('could not copy file');
                }
                fclose($file);
                
                ///recursively remove the whole directory since unzip does not overwrites anything
                remove_dir($destination.$pack.'/');
                //unpack the zip
                if (unzip_file($langpack, $destination, false)){
                    print_heading(get_string('success'));
                }
                //now, we update the md5key of the lang pack, this is used to check version
                $md5file = $CFG->dataroot.'/lang/'.$pack.'/'.$pack.'.md5';
                if ($file = fopen($md5file, 'w')){
                    fwrite($file, md5($contents));    //we should not pass md5 value from moodle.org, because some sites can't fopen,, and the value will not be obtainable
                    print_string('langversionupgraded');
                }
                fclose($file);
                @unlink ($langpack);    //remove the zip file
                echo '<div align="center"><form action="languages.php" method="POST">';
                echo '<input type="submit" value="'.get_string('ok').'" />';
                echo '</form></div>';
            }
        }
        
        break;

        default:    //display choice mode

            $source = 'http://download.moodle.org/lang/list.txt';
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
            echo get_string('installedlangs');
            echo '</td><td align="center">';
            echo get_string('availablelangs');
            echo '</td></tr>';
            echo '<tr><td align="right" valign="top">';
            $installedlangs = get_list_of_languages();    ///THIS FUNCTION NEEDS TO BE CHANGED
            /// display nstalled Components here
            echo '<select disabled="disabled" size="15">';
            
            foreach ($installedlangs as $ilang){
                echo '<option>'.$ilang.'</option>';
            }
            echo '</select>';

            echo '</td><td align="center">';

            /// display to be installed Components here
            echo '<form name="langform" action="languages.php?mode=2" method="POST">';
            echo '<input name="pack" type="hidden" value="" />';
            echo '<input name="displaylang" type="hidden" value="" />';
            echo '<table>';    //availabe langs table

            $empty = 1;    //something to pring
            /// if this language pack is not already installed, then we allow installation

            ///PROBLEM is can't FOPEN, then can't obtain newest MD5 to check against
            ///Don't know if the version is current

            foreach ($availablelangs as $alang){
                if (!is_installed_lang(trim($alang[1]), $alang[2])){    //if not already installed
                    if ($remote){
                        echo '<tr><td>'.$alang[0].'&nbsp;'.$alang[1].'</td><td><input type="submit" value="'.get_string('install').'" onclick="javascript:langform.pack.value=\''.trim($alang[1]).'\';langform.displaylang.value=\''.$alang[0].'\'"></td></tr>';
                    }
                    else {    //print list in local format, and instruction to install
                        echo '<tr><td>'.$alang[0].'</td><td><a href="http://download.moodle.org/lang/'.trim($alang[1]).'.zip">'.get_string('download').'</a>';
                    }
                    $empty = 0;
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
        $source = $CFG->wwwroot.'/blah.txt';
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
        $md5file = $CFG->dataroot.'/lang/'.$lang.'/'.$lang.'.m5d';
        if (file_exists($md5file)){
            return (file_get_contents($md5file) == $md5check);
        }
        return false;
    }
    
?>
