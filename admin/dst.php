<?PHP // $Id$

    // Allows the admin to configure DST presets

    require_once('../config.php');

    require_login();

    if (!isadmin()) {
        error('Only administrators can use this page!');
    }

    if (!$site = get_site()) {
        error('Site isn\'t defined!');
    }

    include_once('../calendar/lib.php');

/// Print headings

    $stradministration = get_string('administration');
    $strconfiguration = get_string('configuration');
    $strcalendarsettings = get_string('calendarsettings', 'admin');
    $strcalendardstpresets = get_string('dstpresets', 'admin');

    print_header("$site->shortname: $strcalendardstpresets", "$site->fullname",
                 "<a href=\"index.php\">$stradministration</a> -> ".
                 "<a href=\"configure.php\">$strconfiguration</a> -> ".
                 "<a href=\"calendar.php\">$strcalendarsettings</a> -> $strcalendardstpresets");

    $mode = '';
    $form = false;

    // $action may instruct us to do something first, then go on to display the preset list
    $action = optional_param('action', '');
    
    switch($action) {
        case 'delete':
            $presetid = optional_param('preset', 0, PARAM_INT);
            $preset = get_record('dst_preset', 'id', $presetid);
            if($preset !== false) {
                // First delete the preset
                delete_records('dst_preset', 'id', $presetid);
                // And then disable DST for all users who had selected that preset
                execute_sql('UPDATE '.$CFG->prefix.'user_preferences SET value = 0 WHERE name = \'calendar_dstpreset\' AND value = '.$presetid, false);
            }
        break;
    }
        

    // $mode, on the other hand, may make us do something INSTEAD of displaying the preset list
    if(($form = data_submitted()) && confirm_sesskey()) {
        if(isset($form->result_cancel)) {
            $mode = '';
        }
        else if(isset($form->mode_return)) {
            redirect('calendar.php');
            die();
        }
        else if(isset($form->mode_delete)) {
            $mode = 'delete';
        }
        else if(isset($form->mode_editform)) {
            // Present add/edit form
            $mode = 'edit';
        }
        else if(isset($form->mode_edit)) {
            // Fetch data for existing preset and display edit form
            $presetid = optional_param('preset', 0, PARAM_INT);
            $preset = get_record('dst_preset', 'id', $presetid);
            if($preset !== false) {
                // This variable used inside dst_edit.html
                $weekdays = array(
                    -1 => get_string('day', 'calendar'),
                     0 => get_string('sunday', 'calendar'),
                     1 => get_string('monday', 'calendar'),
                     2 => get_string('tuesday', 'calendar'),
                     3 => get_string('wednesday', 'calendar'),
                     4 => get_string('thursday', 'calendar'),
                     5 => get_string('friday', 'calendar'),
                     6 => get_string('saturday', 'calendar')
                );

                $preset->apply_offset_sign = ($preset->apply_offset >= 0 ? 1 : -1);
                $preset->apply_offset = abs($preset->apply_offset);
                list($preset->activate_hour, $preset->activate_minute) = explode(':', $preset->activate_time);
                list($preset->deactivate_hour, $preset->deactivate_minute) = explode(':', $preset->deactivate_time);
                $mode = 'continueedit';
            }
            else {
                $mode = '';
            }
        }
        else if(isset($form->mode_add)) {
            // Move to adding a new dst
            $preset = new stdClass;
            $preset->id = 0;
            $mode = 'add';
        }
        // Normal configuration, just save the variables
        else if(isset($form->adminseesallcourses)) {
            set_config('calendar_adminseesall', intval($form->adminseesallcourses) != 0);
            unset($SESSION->cal_courses_shown);
        }
    }

    switch($mode) {
        case 'delete':
            $presetid = optional_param('preset', 0, PARAM_INT);
            $preset = get_record('dst_preset', 'id', $presetid);
            if($preset !== false) {
                print_heading(get_string('confirmation', 'admin'));
                if(!empty($CFG->calendar_dstforusers) && $preset->id == $CFG->calendar_dstforusers) {
                    notice_yesno(get_string('confirmdeletedstdefault', 'admin', $preset->name), 'dst.php?action=delete&amp;preset='.$presetid.'&amp;sesskey='.$USER->sesskey, 'dst.php?sesskey='.$USER->sesskey);
                }
                else {
                    notice_yesno(get_string('confirmdeletedst', 'admin', $preset->name), 'dst.php?action=delete&amp;preset='.$presetid.'&amp;sesskey='.$USER->sesskey, 'dst.php?sesskey='.$USER->sesskey);
                }
            }
            else {
                $mode = '';
            }
        break;
        case 'edit':
            // These variables are used inside dst_edit.html
            $preset = new stdClass;
            $preset->id                = optional_param('preset', 0, PARAM_INT);
        case 'add':
            $preset->name              = optional_param('name', get_string('dstdefaultpresetname', 'calendar'));
            $preset->apply_offset      = abs(optional_param('apply_offset', 60, PARAM_INT));
            $preset->apply_offset_sign = min(max(optional_param('apply_offset_sign', 1, PARAM_INT), -1), 1);
            $preset->activate_index    = min(max(optional_param('activate_index', 1, PARAM_INT), -1), 1);
            $preset->activate_day      = min(max(optional_param('activate_day', 0, PARAM_INT), -1), 6);
            $preset->activate_month    = min(max(optional_param('activate_month', 1, PARAM_INT), 1), 12);
            $preset->activate_hour     = min(max(optional_param('activate_hour', 3, PARAM_INT), 0), 23);
            $preset->activate_minute   = min(max(optional_param('activate_minute', 0, PARAM_INT), 0), 59);
            $preset->deactivate_index  = min(max(optional_param('deactivate_index', 1, PARAM_INT), -1), 1);
            $preset->deactivate_day    = min(max(optional_param('deactivate_day', 0, PARAM_INT), -1), 6);
            $preset->deactivate_month  = min(max(optional_param('deactivate_month', 2, PARAM_INT), 1), 12);
            $preset->deactivate_hour   = min(max(optional_param('deactivate_hour', 3, PARAM_INT), 0), 23);
            $preset->deactivate_minute = min(max(optional_param('deactivate_minute', 0, PARAM_INT), 0), 59);

            $preset->apply_offset *= $preset->apply_offset_sign;
        case 'continueedit':
            if(!empty($form->result_ok)) {
                // Complete the transaction

                // Validation here
                $errors = array();
                if(empty($name)) {
                    $errors[] = get_string('errordstpresetnameempty', 'admin');
                }
                else {
                    $other = get_record('dst_preset', 'name', $preset->name);
                    if($other !== false && $preset->id != $other->id) {
                        $errors[] = get_string('errordstpresetnameexists', 'admin');
                    }
                }
                if($preset->activate_month >= $preset->deactivate_month) {
                    $errors[] = get_string('errordstpresetactivateearlier', 'admin');
                }

                // Are we error-free?
                if(empty($errors)) {
                    // Calculate the last/next/current_offset variables
                    $preset->activate_time   = sprintf('%02d:%02d', $preset->activate_hour, $preset->activate_minute);
                    $preset->deactivate_time = sprintf('%02d:%02d', $preset->deactivate_hour, $preset->deactivate_minute);
                    $preset = calendar_dst_update_preset($preset);
                    print_object("record is:");
                    print_object($preset);
                    print_object('The last change time was: ');
                    print_object(gmdate('M d Y H:i', $preset->last_change));
                    print_object('The next change time is: ');
                    print_object(gmdate('M d Y H:i', $preset->next_change));

                    // Write it!
                    if($preset->id) {
                        print_object("UPDATED!");
                        update_record('dst_preset', $preset);
                    }
                    else {
                        print_object("INSERT!");
                        insert_record('dst_preset', $preset);
                    }
                    echo '<a href="dst.php">Proceed</a>';
                    die();
                }
                else {
                    echo '<div class="errorbox">';
                    echo '<h1>'.get_string('therewereerrors', 'admin').':</h1>';
                    echo '<ul>';
                    foreach($errors as $error) {
                        echo '<li>'.$error.'</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                }
            }

            // Show the edit screen
            $weekdays = array(
                -1 => get_string('day', 'calendar'),
                 0 => get_string('sunday', 'calendar'),
                 1 => get_string('monday', 'calendar'),
                 2 => get_string('tuesday', 'calendar'),
                 3 => get_string('wednesday', 'calendar'),
                 4 => get_string('thursday', 'calendar'),
                 5 => get_string('friday', 'calendar'),
                 6 => get_string('saturday', 'calendar')
            );
            print_heading(get_string('editingdstpreset', 'admin'));
            print_simple_box_start('center', '70%');
            include('./dst_edit.html');
            print_simple_box_end();

        break;
    }
    

    // Default behavior here
    if(empty($mode)) {
        $presets = get_records('dst_preset', '', '', 'name');
        
        if(!empty($presets)) {
            $presetdescriptions = array();
            foreach($presets as $id => $preset) {
                $presetdescriptions[$id] = calendar_human_readable_dst($preset);
            }
        }
        print_heading($strcalendardstpresets);
        print_simple_box_start('center', '70%');
        include('./dst.html');
        print_simple_box_end();
    }

    print_footer();

?>
