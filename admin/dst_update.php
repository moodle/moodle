<?php // $Id$

    // Automatic update of DST presets

    require_once('../config.php');
    require_once($CFG->libdir.'/filelib.php');
    require_once($CFG->libdir.'/olson.php');

    define('STEP_OLSON_TO_CSV',    1);
    define('STEP_DOWNLOAD_CSV',    2);
    define('STEP_IMPORT_CSV_LIB',  3);
    define('STEP_IMPORT_CSV_TEMP', 4);
    define('STEP_COMPLETED',       5);
    
    require_login();

    if (!isadmin()) {
        error('Only administrators can use this page!');
    }

    if (!$site = get_site()) {
        error('Site isn\'t defined!');
    }

    // These control what kind of operations import_dst_records will be allowed
    $insert = true;
    $update = true;

    // Actions in REVERSE ORDER of execution
    $actions = array(STEP_IMPORT_CSV_LIB, STEP_DOWNLOAD_CSV, STEP_IMPORT_CSV_TEMP, STEP_OLSON_TO_CSV);

    while(!empty($actions)) {
        $action = array_pop($actions);
        switch($action) {

            case STEP_OLSON_TO_CSV:
                if(is_writable($CFG->dataroot.'/temp/olson.txt')) {
                    $records = olson_simple_rule_parser($CFG->dataroot.'/temp/olson.txt');
                    if(put_records_csv('dst.txt', $records, 'dst_preset')) {
                        // Successful convert
                        unlink($CFG->dataroot.'/temp/olson.txt');
                        array_push($actions, STEP_IMPORT_CSV_TEMP);
                    }
                    else {
                        // Error: put_records_csv complained
                        error('44');
                    }
                }
            break;

            case STEP_IMPORT_CSV_TEMP:
                if(is_writable($CFG->dataroot.'/temp/dst.txt')) {
                    $records = get_records_csv($CFG->dataroot.'/temp/dst.txt', 'dst_preset');
                    // Import and go to summary page
                    $results = import_dst_records($records, $insert, $update);
                    unlink($CFG->dataroot.'/temp/dst.txt');
                    array_push($actions, STEP_COMPLETED);
                }
            break;

            case STEP_DOWNLOAD_CSV:
                if(ini_get('allow_url_fopen')) {
                    $contents = @file_get_contents('http://download.moodle.org/dst/');
                    if(!empty($contents)) {
                        // Got something
                        if($fp = fopen($CFG->dataroot.'/temp/dst.txt', 'w')) {
                            fwrite($fp, $contents);
                            fclose($fp);
                            array_push($actions, STEP_IMPORT_CSV_TEMP);
                        }
                        else {
                            // Error: Couldn't open file correctly
                            error('74');
                        }
                    }
                    else {
                        // Error: nothing from download.moodle.org
                        error('73');
                    }
                }
            break;

            case STEP_IMPORT_CSV_LIB:
                if(file_exists($CFG->dirroot.'/lib/dst.txt')) {
                    $records = get_records_csv($CFG->dirroot.'/lib/dst.txt', 'dst_preset');
                    $results = import_dst_records($records, $insert, $update);
                    array_push($actions, STEP_COMPLETED);
                }
            break;

            case STEP_COMPLETED:
                echo get_string('updatedstpresetscompleted');
                print_object($results);
            break;
        }
    }

function import_dst_records(&$records, $allowinsert = true, $allowupdate = true) {
    $results = array();
    $proto   = array('insert' => 0, 'update' => 0, 'errors' => 0);

    foreach($records as $record) {

        if(!check_dst_preset($record)) {
            continue;
        }

        $dbpreset = get_record('dst_preset', 'family', $record->family, 'year', $record->year);

        if(empty($dbpreset)) {

            if(!$allowinsert) {
                continue;
            }

            if(!isset($results[$record->family])) {
                $results[$record->family] = $proto;
            }


            unset($record->id);
            if(insert_record('dst_preset', $record)) {
                ++$results[$record->family]['insert'];
            }
            else {
                ++$results[$record->family]['errors'];
            }
        }

        else {
            // Already exists

            if(!$allowupdate) {
                continue;
            }

            if(hash_dst_preset($record) != hash_dst_preset($dbpreset)) {

                // And is different

                if(!isset($results[$record->family])) {
                    $results[$record->family] = $proto;
                }

                $record->id = $dbpreset->id;
                if(update_record('dst_preset', $record)) {
                    ++$results[$record->family]['update'];
                }
                else {
                    ++$results[$record->family]['update'];
                }
            }

        }

    }
    return $results;
}

function hash_dst_preset($record) {
    return md5(implode('!', array(
        $record->family,
        $record->year,
        $record->apply_offset,
        $record->activate_index,
        $record->activate_day,
        $record->activate_month,
        $record->activate_time,
        $record->deactivate_index,
        $record->deactivate_day,
        $record->deactivate_month,
        $record->deactivate_time
    )));
}

function check_dst_preset($record) {
    // TODO: make this a real check
    return true;
}

?>
