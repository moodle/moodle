<?php // $Id$
      // Enables/disables maintenance mode

    require('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    $action = optional_param('action', '', PARAM_ALPHA);

    admin_externalpage_setup('maintenancemode');

    //Check folder exists
    if (! make_upload_directory(SITEID)) {   // Site folder
            error("Could not create site folder.  The site administrator needs to fix the file permissions");
        }

    $filename = $CFG->dataroot.'/'.SITEID.'/maintenance.html';

    if ($form = data_submitted()) {
        if (confirm_sesskey()) {
            if ($form->action == "disable") {
                unlink($filename);
                redirect('maintenance.php', get_string('sitemaintenanceoff','admin'));
            } else {
                $file = fopen($filename, 'w');
                fwrite($file, stripslashes($form->text));
                fclose($file);
                redirect('maintenance.php', get_string('sitemaintenanceon', 'admin'));
            }
        }
    }

/// Print the header stuff

    admin_externalpage_print_header();

/// Print the appropriate form

    if (file_exists($filename)) {   // We are in maintenance mode
        echo '<div style="margin-left:auto;margin-right:auto">';
        echo '<form action="maintenance.php" method="post">';
        echo '<input type="hidden" name="action" value="disable" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<p><input type="submit" value="'.get_string('disable').'" /></p>';
        echo '</form>';
        echo '</div>';
    } else {                        // We are not in maintenance mode
        $usehtmleditor = can_use_html_editor();

        echo '<div style="text-align:center;margin-left:auto;margin-right:auto">';
        echo '<form action="maintenance.php" method="post">';
        echo '<div>';
        echo '<input type="hidden" name="action" value="enable" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
        echo '<p><input type="submit" value="'.get_string('enable').'" /></p>';
        echo '<p>'.get_string('optionalmaintenancemessage', 'admin').':</p>';
        echo '<table><tr><td>';
        print_textarea($usehtmleditor, 20, 50, 600, 400, "text");
        echo '</td></tr></table>';
        echo '</div>';
        echo '</form>';
        echo '</div>';

        if ($usehtmleditor) { 
            use_html_editor();
        }
    }

    admin_externalpage_print_footer();
?>
