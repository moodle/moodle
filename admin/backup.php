<?PHP  // $Id$
       // backup.php - allows admin to edit all configuration variables for scheduled backups

    require_once("../config.php");
    require_once("../backup/lib.php");

    require_login();

    if (!isadmin()) {
        error("Only an admin can use this page");
    }

    if (!$site = get_site()) {
        error("Site isn't defined!");
    }

    //Initialise error variables
    $error = false;
    $sche_destination_error = "";

    /// If data submitted, then process and store.

    if ($config = data_submitted()) {

        //First of all we check that everything is correct
        //Check for trailing slash and backslash in backup_sche_destination
        if (!empty($backup_sche_destination) and 
            (substr($backup_sche_destination,-1) == "/" or substr($backup_sche_destination,-1) == "\\")) {
            $error = true;
            $sche_destination_error = get_string("pathslasherror");
        //Now check that backup_sche_destination dir exists
        } else if (!empty($backup_sche_destination) and
            !is_dir($backup_sche_destination)) {
            $error = true;
            $sche_destination_error = get_string("pathnotexists");
        }

        //We need to do some weekdays conversions prior to continue
        $i = 0;
        $temp = "";
        $a_config = (array)$config;
        while ($i<7) {
            $tocheck = "dayofweek_".$i;
            if (isset($a_config[$tocheck])) {
                $temp .= "1";
            } else {
                $temp .= "0"; 
            }
            unset($a_config[$tocheck]);
            $i++;
        }
        $a_config['backup_sche_weekdays'] = $temp;
        $config = (object)$a_config;
        //weekdays conversions done. Continue

        print_header();
        foreach ($config as $name => $value) {
            backup_set_config($name, $value);
        }
        if (!$error) {
            redirect("$CFG->wwwroot/$CFG->admin/index.php", get_string("changessaved"), 1);
            exit;
        }
    }

/// Otherwise print the form.

    $stradmin = get_string("administration");
    $strconfiguration = get_string("configuration");
    $strbackup = get_string("backup");

    print_header("$site->shortname: $strconfiguration: $strbackup", $site->fullname,
                  "<a href=\"index.php\">$stradmin</a> -> ".
                  "<a href=\"configure.php\">$strconfiguration</a> -> ".
                  "<a href=\"backup.php\">$strbackup</a>");

    print_heading($strbackup);

    print_simple_box("<center>".get_string("adminhelpbackup")."</center>", "center", "50%");
    echo "<br />";

    print_simple_box_start("center", "", "$THEME->cellheading");
    include("$CFG->dirroot/backup/config.html");
    print_simple_box_end();

    print_footer();

?>
