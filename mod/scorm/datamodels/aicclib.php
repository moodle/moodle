<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * functions used by AICC packages.
 *
 * @package    mod_scorm
 * @copyright 1999 onwards Roberto Pinna
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function scorm_add_time($a, $b) {
    $aes = explode(':', $a);
    $bes = explode(':', $b);
    $aseconds = explode('.', $aes[2]);
    $bseconds = explode('.', $bes[2]);
    $change = 0;

    $acents = 0;  //Cents
    if (count($aseconds) > 1) {
        $acents = $aseconds[1];
    }
    $bcents = 0;
    if (count($bseconds) > 1) {
        $bcents = $bseconds[1];
    }
    $cents = $acents + $bcents;
    $change = floor($cents / 100);
    $cents = $cents - ($change * 100);
    if (floor($cents) < 10) {
        $cents = '0'. $cents;
    }

    $secs = $aseconds[0] + $bseconds[0] + $change;  //Seconds
    $change = floor($secs / 60);
    $secs = $secs - ($change * 60);
    if (floor($secs) < 10) {
        $secs = '0'. $secs;
    }

    $mins = $aes[1] + $bes[1] + $change;   //Minutes
    $change = floor($mins / 60);
    $mins = $mins - ($change * 60);
    if ($mins < 10) {
        $mins = '0' .  $mins;
    }

    $hours = $aes[0] + $bes[0] + $change;  //Hours
    if ($hours < 10) {
        $hours = '0' . $hours;
    }

    if ($cents != '0') {
        return $hours . ":" . $mins . ":" . $secs . '.' . $cents;
    } else {
        return $hours . ":" . $mins . ":" . $secs;
    }
}

/**
 * Take the header row of an AICC definition file
 * and returns sequence of columns and a pointer to
 * the sco identifier column.
 *
 * @param string $row AICC header row
 * @param string $mastername AICC sco identifier column
 * @return mixed
 */
function scorm_get_aicc_columns($row, $mastername='system_id') {
    $tok = strtok(strtolower($row), "\",\n\r");
    $result = new stdClass();
    $result->columns = array();
    $i=0;
    while ($tok) {
        if ($tok !='') {
            $result->columns[] = $tok;
            if ($tok == $mastername) {
                $result->mastercol = $i;
            }
            $i++;
        }
        $tok = strtok("\",\n\r");
    }
    return $result;
}

/**
 * Given a colums array return a string containing the regular
 * expression to match the columns in a text row.
 *
 * @param array $column The header columns
 * @param string $remodule The regular expression module for a single column
 * @return string
 */
function scorm_forge_cols_regexp($columns, $remodule='(".*")?,') {
    $regexp = '/^';
    foreach ($columns as $column) {
        $regexp .= $remodule;
    }
    $regexp = substr($regexp, 0, -1) . '/';
    return $regexp;
}

/**
 * Sets up AICC packages
 * Called whenever package changes
 * @param object $scorm instance - fields are updated and changes saved into database

 * @return bool
 */
function scorm_parse_aicc(&$scorm) {
    global $DB;

    if ($scorm->scormtype == SCORM_TYPE_AICCURL) {
        return scorm_aicc_generate_simple_sco($scorm);
    }
    if (!isset($scorm->cmid)) {
        $cm = get_coursemodule_from_instance('scorm', $scorm->id);
        $scorm->cmid = $cm->id;
    }
    $context = context_module::instance($scorm->cmid);

    $fs = get_file_storage();

    $files = $fs->get_area_files($context->id, 'mod_scorm', 'content', 0, 'sortorder, itemid, filepath, filename', false);

    $version = 'AICC';
    $ids = array();
    $courses = array();
    $extaiccfiles = array('crs', 'des', 'au', 'cst', 'ort', 'pre', 'cmp');

    foreach ($files as $file) {
        $filename = $file->get_filename();
        $ext = substr($filename, strrpos($filename, '.'));
        $extension = strtolower(substr($ext, 1));
        if (in_array($extension, $extaiccfiles)) {
            $id = strtolower(basename($filename, $ext));
            if (!isset($ids[$id])) {
                $ids[$id] = new stdClass();
            }
            $ids[$id]->$extension = $file;
        }
    }

    foreach ($ids as $courseid => $id) {
        if (!isset($courses[$courseid])) {
            $courses[$courseid] = new stdClass();
        }
        if (isset($id->crs)) {
            $contents = $id->crs->get_content();
            $rows = explode("\r\n", $contents);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    if (preg_match("/^(.+)=(.+)$/", $row, $matches)) {
                        switch (strtolower(trim($matches[1]))) {
                            case 'course_id':
                                $courses[$courseid]->id = trim($matches[2]);
                            break;
                            case 'course_title':
                                $courses[$courseid]->title = trim($matches[2]);
                            break;
                            case 'version':
                                $courses[$courseid]->version = 'AICC_'.trim($matches[2]);
                            break;
                        }
                    }
                }
            }
        }
        if (isset($id->des)) {
            $contents = $id->des->get_content();
            $rows = explode("\r\n", $contents);
            $columns = scorm_get_aicc_columns($rows[0]);
            $regexp = scorm_forge_cols_regexp($columns->columns);
            for ($i=1; $i<count($rows); $i++) {
                if (preg_match($regexp, $rows[$i], $matches)) {
                    for ($j=0; $j<count($columns->columns); $j++) {
                        $column = $columns->columns[$j];
                        if (!isset($courses[$courseid]->elements[substr(trim($matches[$columns->mastercol+1]), 1 , -1)])) {
                            $courses[$courseid]->elements[substr(trim($matches[$columns->mastercol+1]), 1 , -1)] = new stdClass();
                        }
                        $courses[$courseid]->elements[substr(trim($matches[$columns->mastercol+1]), 1 , -1)]->$column = substr(trim($matches[$j+1]), 1, -1);
                    }
                }
            }
        }
        if (isset($id->au)) {
            $contents = $id->au->get_content();
            $rows = explode("\r\n", $contents);
            $columns = scorm_get_aicc_columns($rows[0]);
            $regexp = scorm_forge_cols_regexp($columns->columns);
            for ($i=1; $i<count($rows); $i++) {
                if (preg_match($regexp, $rows[$i], $matches)) {
                    for ($j=0; $j<count($columns->columns); $j++) {
                        $column = $columns->columns[$j];
                        $courses[$courseid]->elements[substr(trim($matches[$columns->mastercol+1]), 1, -1)]->$column = substr(trim($matches[$j+1]), 1, -1);
                    }
                }
            }
        }
        if (isset($id->cst)) {
            $contents = $id->cst->get_content();
            $rows = explode("\r\n", $contents);
            $columns = scorm_get_aicc_columns($rows[0], 'block');
            $regexp = scorm_forge_cols_regexp($columns->columns, '(.+)?,');
            for ($i=1; $i<count($rows); $i++) {
                if (preg_match($regexp, $rows[$i], $matches)) {
                    for ($j=0; $j<count($columns->columns); $j++) {
                        if ($j != $columns->mastercol) {
                            $element = substr(trim($matches[$j+1]), 1 , -1);
                            if (!empty($element)) {
                                $courses[$courseid]->elements[$element]->parent = substr(trim($matches[$columns->mastercol+1]), 1, -1);
                            }
                        }
                    }
                }
            }
        }
        if (isset($id->ort)) {
            $contents = $id->ort->get_content();
            $rows = explode("\r\n", $contents);
            $columns = scorm_get_aicc_columns($rows[0], 'course_element');
            $regexp = scorm_forge_cols_regexp($columns->columns, '(.+)?,');
            for ($i=1; $i<count($rows); $i++) {
                if (preg_match($regexp, $rows[$i], $matches)) {
                    for ($j=0; $j<count($matches)-1; $j++) {
                        if ($j != $columns->mastercol) {
                            $courses[$courseid]->elements[substr(trim($matches[$j+1]), 1, -1)]->parent = substr(trim($matches[$columns->mastercol+1]), 1, -1);
                        }
                    }
                }
            }
        }
        if (isset($id->pre)) {
            $contents = $id->pre->get_content();
            $rows = explode("\r\n", $contents);
            $columns = scorm_get_aicc_columns($rows[0], 'structure_element');
            $regexp = scorm_forge_cols_regexp($columns->columns, '(.+),');
            for ($i=1; $i<count($rows); $i++) {
                if (preg_match($regexp, $rows[$i], $matches)) {
                    $courses[$courseid]->elements[$columns->mastercol+1]->prerequisites = substr(trim($matches[1-$columns->mastercol+1]), 1, -1);
                }
            }
        }
        if (isset($id->cmp)) {
            $contents = $id->cmp->get_content();
            $rows = explode("\r\n", $contents);
        }
    }

    $oldscoes = $DB->get_records('scorm_scoes', array('scorm'=>$scorm->id));
    $sortorder = 0;
    $launch = 0;
    if (isset($courses)) {
        foreach ($courses as $course) {
            $sortorder++;
            $sco = new stdClass();
            $sco->identifier = $course->id;
            $sco->scorm = $scorm->id;
            $sco->organization = '';
            $sco->title = $course->title;
            $sco->parent = '/';
            $sco->launch = '';
            $sco->scormtype = '';
            $sco->sortorder = $sortorder;

            if ($ss = $DB->get_record('scorm_scoes', array('scorm'=>$scorm->id,
                                                           'identifier'=>$sco->identifier))) {
                $id = $ss->id;
                $sco->id = $id;
                $DB->update_record('scorm_scoes', $sco);
                unset($oldscoes[$id]);
            } else {
                $id = $DB->insert_record('scorm_scoes', $sco);
            }

            if ($launch == 0) {
                $launch = $id;
            }
            if (isset($course->elements)) {
                foreach ($course->elements as $element) {
                    unset($sco);
                    $sco = new stdClass();
                    $sco->identifier = $element->system_id;
                    $sco->scorm = $scorm->id;
                    $sco->organization = $course->id;
                    $sco->title = $element->title;

                    if (!isset($element->parent)) {
                        $sco->parent = '/';
                    } else if (strtolower($element->parent) == 'root') {
                        $sco->parent = $course->id;
                    } else {
                        $sco->parent = $element->parent;
                    }
                    $sco->launch = '';
                    $sco->scormtype = '';
                    $sco->previous = 0;
                    $sco->next = 0;
                    $id = null;
                    // Is it an Assignable Unit (AU)?
                    if (isset($element->file_name)) {
                        $sco->launch = $element->file_name;
                        $sco->scormtype = 'sco';
                    }
                    if ($oldscoid = scorm_array_search('identifier', $sco->identifier, $oldscoes)) {
                        $sco->id = $oldscoid;
                        $DB->update_record('scorm_scoes', $sco);
                        $id = $oldscoid;
                        $DB->delete_records('scorm_scoes_data', array('scoid'=>$oldscoid));
                        unset($oldscoes[$oldscoid]);
                    } else {
                        $id = $DB->insert_record('scorm_scoes', $sco);
                    }
                    if (!empty($id)) {
                        $scodata = new stdClass();
                        $scodata->scoid = $id;
                        if (isset($element->web_launch)) {
                            $scodata->name = 'parameters';
                            $scodata->value = $element->web_launch;
                            $dataid = $DB->insert_record('scorm_scoes_data', $scodata);
                        }
                        if (isset($element->prerequisites)) {
                            $scodata->name = 'prerequisites';
                            $scodata->value = $element->prerequisites;
                            $dataid = $DB->insert_record('scorm_scoes_data', $scodata);
                        }
                        if (isset($element->max_time_allowed)) {
                            $scodata->name = 'max_time_allowed';
                            $scodata->value = $element->max_time_allowed;
                            $dataid = $DB->insert_record('scorm_scoes_data', $scodata);
                        }
                        if (isset($element->time_limit_action)) {
                            $scodata->name = 'time_limit_action';
                            $scodata->value = $element->time_limit_action;
                            $dataid = $DB->insert_record('scorm_scoes_data', $scodata);
                        }
                        if (isset($element->mastery_score)) {
                            $scodata->name = 'mastery_score';
                            $scodata->value = $element->mastery_score;
                            $dataid = $DB->insert_record('scorm_scoes_data', $scodata);
                        }
                        if (isset($element->core_vendor)) {
                            $scodata->name = 'datafromlms';
                            $scodata->value = preg_replace('/<cr>/i', "\r\n", $element->core_vendor);
                            $dataid = $DB->insert_record('scorm_scoes_data', $scodata);
                        }
                    }
                    if ($launch==0) {
                        $launch = $id;
                    }
                }
            }
        }
    }
    if (!empty($oldscoes)) {
        foreach ($oldscoes as $oldsco) {
            $DB->delete_records('scorm_scoes', array('id'=>$oldsco->id));
            $DB->delete_records('scorm_scoes_track', array('scoid'=>$oldsco->id));
        }
    }

    // Find first launchable object.
    $sqlselect = 'scorm = ? AND '.$DB->sql_isnotempty('scorm_scoes', 'launch', false, true);
    // We use get_records here as we need to pass a limit in the query that works cross db.
    $scoes = $DB->get_records_select('scorm_scoes', $sqlselect, array($scorm->id), 'sortorder', 'id', 0, 1);
    if (!empty($scoes)) {
        $sco = reset($scoes); // We only care about the first record - the above query only returns one.
        $scorm->launch = $sco->id;
    } else {
        $scorm->launch = $launch;
    }

    $scorm->version = 'AICC';

    return true;
}

/**
 * Given a scormid creates an AICC Session record to allow HACP
 *
 * @param int $scormid - id from scorm table
 * @return string hacpsession
 */
function scorm_aicc_get_hacp_session($scormid) {
    global $USER, $DB, $SESSION;
    $cfg_scorm = get_config('scorm');
    if (empty($cfg_scorm->allowaicchacp)) {
        return false;
    }
    $now = time();

    $hacpsession = $SESSION->scorm;
    $hacpsession->scormid = $scormid;
    $hacpsession->hacpsession = random_string(20);
    $hacpsession->userid      = $USER->id;
    $hacpsession->timecreated = $now;
    $hacpsession->timemodified = $now;
    $DB->insert_record('scorm_aicc_session', $hacpsession);

    return $hacpsession->hacpsession;
}

/**
 * Check the hacp_session for whether it is valid.
 *
 * @param string $hacpsession The hacpsession value to check (optional). Normally leave this blank
 *      and this function will do required_param('sesskey', ...).
 * @return mixed - false if invalid, otherwise returns record from scorm_aicc_session table.
 */
function scorm_aicc_confirm_hacp_session($hacpsession) {
    global $DB;
    $cfg_scorm = get_config('scorm');
    if (empty($cfg_scorm->allowaicchacp)) {
        return false;
    }
    $time = time()-($cfg_scorm->aicchacptimeout * 60);
    $sql = "hacpsession = ? AND timemodified > ?";
    $hacpsession = $DB->get_record_select('scorm_aicc_session', $sql, array($hacpsession, $time));
    if (!empty($hacpsession)) { //update timemodified as this is still an active session - resets the timeout.
        $hacpsession->timemodified = time();
        $DB->update_record('scorm_aicc_session', $hacpsession);
    }
    return $hacpsession;
}

/**
 * generate a simple single activity AICC object
 * structure to wrap around and externally linked
 * AICC package URL
 *
 * @param object $scorm package record
 */
function scorm_aicc_generate_simple_sco($scorm) {
    global $DB;
    // Find the oldest one.
    $scos = $DB->get_records('scorm_scoes', array('scorm' => $scorm->id), 'id');
    if (!empty($scos)) {
        $sco = array_shift($scos);
    } else {
        $sco = new stdClass();
    }
    // Get rid of old ones.
    foreach ($scos as $oldsco) {
        $DB->delete_records('scorm_scoes', array('id' => $oldsco->id));
        $DB->delete_records('scorm_scoes_track', array('scoid' => $oldsco->id));
    }

    $sco->identifier = 'A1';
    $sco->scorm = $scorm->id;
    $sco->organization = '';
    $sco->title = $scorm->name;
    $sco->parent = '/';
    // Add the HACP signal to the activity launcher.
    if (preg_match('/\?/', $scorm->reference)) {
        $sco->launch = $scorm->reference.'&CMI=HACP';
    } else {
        $sco->launch = $scorm->reference.'?CMI=HACP';
    }
    $sco->scormtype = 'sco';
    if (isset($sco->id)) {
        $DB->update_record('scorm_scoes', $sco);
        $id = $sco->id;
    } else {
        $id = $DB->insert_record('scorm_scoes', $sco);
    }
    return $id;
}