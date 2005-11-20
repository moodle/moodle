<?php  // $Id$
       // config.php - allows admin to edit all configuration variables

    require_once('../config.php');

    if ($site = get_site()) {   // If false then this is a new installation
        require_login();
        if (!isadmin()) {
            error('Only the admin can use this page');
        }
    }

/// This is to overcome the "insecure forms paradox"
    if (isset($secureforms) and $secureforms == 0) {
        $match = 'nomatch';
    } else {
        $match = '';
    }

/// If data submitted, then process and store.

    if ($config = data_submitted($match)) {  

        if (!empty($USER->id)) {             // Additional identity check
            if (!confirm_sesskey()) {
                error(get_string('confirmsesskeybad', 'error'));
            }
        }

        validate_form($config, $err);

        if (count($err) == 0) {
            foreach ($config as $name => $value) {
                if ($name == "sessioncookie") {
                    $value = eregi_replace("[^a-zA-Z]", "", $value);
                }
                if ($name == "defaultallowedmodules") {
                    $value = implode(',',$value);
                }
                if ($name == 'hiddenuserfields') {
                    if (in_array('none', $value)) {
                        $value = '';
                    } else {
                        $value = implode(',',$value);
                    }
                }
                unset($conf);
                $conf->name  = $name;
                $conf->value = $value;
                if ($current = get_record('config', 'name', $name)) {
                    $conf->id = $current->id;
                    if (! update_record('config', $conf)) {
                        notify("Could not update $name to $value");
                    }
                } else {
                    if (! insert_record('config', $conf)) {
                        notify("Error: could not add new variable $name !");
                    }
                }
            }
            redirect('index.php', get_string('changessaved'), 1);
            exit;

        } else {
            foreach ($err as $key => $value) {
                $focus = "form.$key";
            }
        }
    }

/// Otherwise fill and print the form.

    if (empty($config)) {
        $config = $CFG;
        if (!$config->locale = get_field('config', 'value', 'name', 'locale')) {
            $config->locale = $CFG->lang;
        }
    }
    if (empty($focus)) {
        $focus = '';
    }

    $sesskey = !empty($USER->id) ? $USER->sesskey : '';


    $stradmin = get_string('administration');
    $strconfiguration = get_string('configuration');
    $strconfigvariables = get_string('configvariables', 'admin');

    if ($site) {
        print_header("$site->shortname: $strconfigvariables", $site->fullname,
                      "<a href=\"index.php\">$stradmin</a> -> ".
                      "<a href=\"configure.php\">$strconfiguration</a> -> $strconfigvariables", $focus);
        print_heading($strconfigvariables);
    } else {
        print_header();
        print_heading($strconfigvariables);
        print_simple_box(get_string('configintro', 'admin'), 'center', "50%");
        echo '<br />';
    }



/// Get all the configuration fields and helptext
    include('configvars.php');

/// Cycle through the sections to get the sectionnames
    $linktext = '';
    foreach($configvars as $sectionname=>$section) {
        if ($linktext !== '') {
            $linktext .= ' | ';
        }
        $linktext .= '<a href="#configsection'.$sectionname.'">'.get_string('configsection'.$sectionname, 'admin').'</a>';
    }
        
    echo "<center>$linktext</center>\n";

    print_simple_box_start('center');
    
    echo '<form method="post" action="config.php" name="form">';
    echo '<center><input type="submit" value="'.get_string('savechanges').'" /></center>';

/// Cycle through each section of the configuration
    foreach ($configvars as $sectionname=>$section) {

        print_heading('<a name="configsection'.$sectionname.'"></a>'.get_string('configsection'.$sectionname, 'admin'));

        $table = NULL;
        $table->data = array();
        foreach ($section as $configvariable=>$configobject) {
            $table->data[] = array ( $configvariable.': ',
                                     $configobject->field
                                   );
            if ($configobject->display_warning()) {
                $table->data[] = array ( '&nbsp;',
                                         '<span class="configwarning">'.$configobject->warning.'</span>'
                                       );
            }
            $table->data[] = array ( '&nbsp;',
                                     '<span class="confighelp">'.$configobject->help.'</span>'
                                   );
            $table->align = array ('right', 'left');
        }
        print_table($table);

    }
    echo '<center>';
    echo '<input type="hidden" name="sesskey" value="'.$sesskey.'" />';
    echo '<input type="submit" value="'.get_string('savechanges').'" />';
    echo '</center>';
                
    echo '</form>';
    
    print_simple_box_end();


    

    /// Lock some options

    $httpsurl = str_replace('http://', 'https://', $CFG->wwwroot);
    if ($httpsurl != $CFG->wwwroot) {
        if (ini_get('allow_url_fopen')) {
            if ((($fh = @fopen($httpsurl, 'r')) == false) and ($config->loginhttps == 0)) {
                echo '<script type="text/javascript">'."\n";
                echo '<!--'."\n";
                echo "eval('document.form.loginhttps.disabled=true');\n";
                echo '-->'."\n";
                echo '</script>'."\n";
            }
        }
    }


    if ($site) {
        print_footer();
    }

    exit;

/// Functions /////////////////////////////////////////////////////////////////

function validate_form(&$form, &$err) {

    // Currently no checks are needed ...

    return true;
}

?>
