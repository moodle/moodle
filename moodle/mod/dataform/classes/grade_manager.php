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
 * @package mod_dataform
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Grade manager class.
 */
class mod_dataform_grade_manager {

    const GRADE_ITEM_ALL = -1;

    /** @var int The id of the Dataform this manager works for */
    protected $_dataformid;

    /** @var int The id of the course containting the Dataform instance. */
    protected $_courseid;

    /** @var array The list of grade items of the Dataform this manager works for */
    protected $_gradeitems = null;

    /**
     * Returns and caches (for the current script) if not already, a patterns manager for the specified Dataform.
     *
     * @param int Dataform id
     * @return mod_dataform_grade_manager
     */
    public static function instance($dataformid) {
        if (!$instance = \mod_dataform_instance_store::instance($dataformid, 'grade_manager')) {
            $instance = new mod_dataform_grade_manager($dataformid);
            \mod_dataform_instance_store::register($dataformid, 'grade_manager', $instance);
        }

        return $instance;
    }

    /**
     * Constructor
     */
    public function __construct($dataformid) {
        $this->_dataformid = $dataformid;
    }

    /**
     * Magic property method
     *
     * Attempts to call a set_$key method if one exists otherwise falls back
     * to simply set the property
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value) {
        if (method_exists($this, 'set_'.$key)) {
            $this->{'set_'.$key}($value);
        }
    }

    /**
     * Magic get method
     *
     * Attempts to call a get_$key method to return the property and ralls over
     * to return the raw property
     *
     * @param str $key
     * @return mixed
     */
    public function __get($key) {
        if (method_exists($this, 'get_'.$key)) {
            return $this->{'get_'.$key}();
        }
        return null;
    }

    /**
     * Returns the course id of the instance.
     *
     * @return int.
     */
    public function get_courseid() {
        global $DB;
        if (!$this->_courseid) {
            $courseid = $DB->get_field('dataform', 'course', array('id' => $this->_dataformid));
            $this->_courseid = $courseid;
        }
        return $this->_courseid;
    }

    /**
     * Returns the dataform employer if this manager, or false if the dataform
     * does not exist.
     *
     * @return \mod_dataform_dataform|false.
     */
    public function get_df() {
        try {
            $df = \mod_dataform_dataform::instance($this->_dataformid);
        } catch (Exception $e) {
            $df = false;
        }
        return $df;
    }

    /**
     * Returns a list of grade information objects
     * (scaleid, name, grade and locked status, etc.) for the Dataform instance.
     *
     * @return array Array of objects indexed by itemnumber.
     */
    public function get_grade_items() {
        global $CFG;

        if ($this->_gradeitems == null) {
            require_once("$CFG->libdir/gradelib.php");

            // Get the items.
            $params = array(
                'courseid' => $this->courseid,
                'itemtype' => 'mod',
                'itemmodule' => 'dataform',
                'iteminstance' => $this->_dataformid
            );

            $items = array();
            if ($gitems = \grade_item::fetch_all($params)) {
                // Get grade guides and calcs from the Dataform.
                $gdef = ($df = $this->df) ? $df->grade_items : null;

                foreach ($gitems as $gitem) {
                    $itemnumber = $gitem->itemnumber;
                    // Attach guide.
                    $gitem->gradeguide = !empty($gdef[$itemnumber]['ru']) ? $gdef[$itemnumber]['ru'] : null;
                    // Attach calc.
                    $gitem->gradecalc = !empty($gdef[$itemnumber]['ca']) ? $gdef[$itemnumber]['ca'] : null;
                    // Sort by itemnumber.
                    $items[$itemnumber] = $gitem;
                }
                ksort($items);
            }
            $this->_gradeitems = $items;
        }

        return $this->_gradeitems;
    }

    /**
     * Sets the manager's list of grade items. If null is passed the list is effectively reset,
     * and the next call to {@link mod_dataform_grade_manager::get_grade_items()} will re-fetch
     * the items.
     *
     * @return void
     */
    public function set_grade_items($items) {
        $this->_gradeitems = $items;
    }

    /**
     * Deletes all the instance grade items and sets the instance grade to 0.
     *
     * @return void
     */
    public function delete_grade_items($itemnumber = self::GRADE_ITEM_ALL) {
        $gradeitems = $this->grade_items;

        if ($itemnumber != self::GRADE_ITEM_ALL) {
            if (!empty($gradeitems[$itemnumber])) {
                $gradeitems[$itemnumber]->delete();
                unset($gradeitems[$itemnumber]);
                $this->grade_items = $gradeitems;
            }
        } else {
            foreach ($this->grade_items as $gradeitem) {
                $res = $gradeitem->delete('mod/dataform');
            }
            $this->grade_items = null;
        }
    }

    /**
     * Updates one or more existing grade items in the given dataform, or creates a new one.
     * Item number -1 means that all the instance grade items are updated according to the
     * specified options and grades.
     * then it is updated. If it does not exist, it is created.
     *
     * @param int $itemnumber Grade item number
     * @param array $options Grade item info
     * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
     * @return int GRADE_UPDATE_OK, GRADE_UPDATE_FAILED, GRADE_UPDATE_MULTIPLE or GRADE_UPDATE_ITEM_LOCKED.
     */
    public function update_grade_item($itemnumber, array $options = null) {

        $dataformid = $this->_dataformid;
        $courseid = 0;

        if ($df = $this->df) {
            $courseid = $df->course->id;
        }

        if (!$courseid) {
            $courseid = $this->courseid;
        }

        $params = array();
        if ($reset = !empty($options['reset'])) {
            $params['reset'] = true;
        }

        $gradeitems = $this->grade_items;

        if (!$gradeitems or !array_key_exists($itemnumber, $gradeitems)) {
            // Add a new item.
            $itemornumber = $gradeitems ? count($gradeitems) : 0;
        } else {
            // Update existing.
            $itemornumber = $gradeitems[$itemnumber];
        }

        $itemparams = $this->get_grade_item_update_params($itemornumber, $options);
        $params = array_merge($params, $itemparams);
        $itemnumber = $params['itemnumber'];

        $res = grade_update(
            'mod/dataform',
            $courseid,
            'mod',
            'dataform',
            $dataformid,
            $itemnumber,
            null,
            $params
        );

        if ($res != GRADE_UPDATE_OK) {
            return $res;
        }

        // Additional grade item updates (e.g. category).
        $gradeitem = $this->adjust_grade_item($itemnumber, $options);

        // Update grades.
        $this->update_grades();

        // Update the local cache.
        $this->grade_items = null;

        return GRADE_UPDATE_OK;
    }

    /**
     * Adjusts dataform grade settings according to changes in the grade items.
     * This involves setting/reseting the first item definition that is stored
     * in the Dataform instance and grade calcs for all items.
     *
     * @param int $itemnumber
     * @param array $options
     * @return void
     */
    public function adjust_dataform_settings($itemnumber, array $options) {
        if (!$df = $this->df) {
            return;
        }

        $updates = array();
        if (!empty($options['deleted'])) {
            $updates['grade'] = 0;
            $updates['gradeitems'] = null;

            $df->update($updates);

        } else {
            $instance = $df->data;

            // Grade part (only first item).
            if ($itemnumber == 0) {
                $grade = $instance->grade;
                if (isset($options['gradetype'])) {
                    if ($options['gradetype'] == GRADE_TYPE_VALUE) {
                        $grade = $options['grademax'];
                    } else if ($options['gradetype'] == GRADE_TYPE_SCALE) {
                        $grade = -$options['scaleid'];
                    }
                }
                if ($grade != $instance->grade) {
                    $updates['grade'] = $grade;
                }
            }

            $gradeitems = $df->grade_items;

            // Guides part (all items).
            if (isset($options['gradeguide'])) {
                $guide = $options['gradeguide'];
                if (empty($gradeitems[$itemnumber])) {
                    $gradeitems[$itemnumber] = array();
                }
                $gradeitems[$itemnumber]['ru'] = $guide;
                $updates['gradeitems'] = serialize($gradeitems);
            }

            // Calcs part (all items).
            if (isset($options['gradecalc'])) {
                $calc = $options['gradecalc'];
                if (empty($gradeitems[$itemnumber])) {
                    $gradeitems[$itemnumber] = array();
                }
                $gradeitems[$itemnumber]['ca'] = $calc;
                $updates['gradeitems'] = serialize($gradeitems);
            }

            if ($updates) {
                $df->update($updates);
            }
        }
    }

    /**
     * Updates the user's grades in the dataform instance.
     *
     * @param int $userid The user id whose grades should be retrieved or 0 for all grades.
     * @param bool $nulifnone.
     * @return int
     */
    public function update_grades($userid = 0, $nullifnone = true) {
        if (!$df = $this->df) {
            return GRADE_UPDATE_FAILED;
        }

        // There should be no grade items, if the instance grade is reset.
        if (!$df->grade) {
            $this->delete_grade_items();
            return GRADE_UPDATE_OK;
        }

        // Get user grades for all grade items.
        $grades = $this->get_user_grades($userid);

        foreach ($this->grade_items as $itemnumber => $unused) {
            if ($grades) {
                foreach ($grades as $userid => $itemgrades) {
                    if (!empty($itemgrades[$itemnumber])) {
                        // Update the user grade.
                        $grade = $itemgrades[$itemnumber];
                        $res = grade_update(
                            'mod/dataform',
                            $df->course->id,
                            'mod',
                            'dataform',
                            $df->id,
                            $itemnumber,
                            $grade
                        );
                        if ($res != GRADE_UPDATE_OK) {
                            return $res;
                        }
                    }
                }
            } else if ($userid and $nullifnone) {
                // No grades and need to nullify.
                $grade = (object) array(
                    'userid' => $userid,
                    'rawgrade' => null,
                );
                $res = grade_update(
                    'mod/dataform',
                    $df->course->id,
                    'mod',
                    'dataform',
                    $df->id,
                    $itemnumber,
                    $grade
                );
                if ($res != GRADE_UPDATE_OK) {
                    return $res;
                }
            }
        }
        return GRADE_UPDATE_OK;
    }

    /**
     * Checks if calculated grades is used and calls for update grades if required.
     * This method can be called from anywhere in the Dataform that may affect calculated grades,
     * e.g., changing the number of entries (adding/deleting), changing content of gradable fields
     * (entry updating). If the call does not specify pattern to update on,
     * all field patterns are checked.
     *
     * @param array|stdClass $data The user id whose grades should be retrieved or 0 for all grades.
     * @param string $pattern.
     * @return void
     */
    public function update_calculated_grades($data, $pattern = null) {
        // Must have grade items.
        if (!$gradeitems = $this->grade_items) {
            return;
        }

        $data = (object) $data;
        if (!$df = $this->df) {
            return;
        }

        // Check if there is a grade calc to update.
        $requiresupdate = false;
        foreach ($gradeitems as $gradeitem) {
            if (empty($gradeitem->gradecalc)) {
                continue;
            }
            if (!$pattern or preg_match("%$pattern%", $gradeitem->gradecalc) !== false) {
                $requiresupdate = true;
                break;
            }
        }

        // Update grades for the affected users.
        if ($requiresupdate) {
            // Get the affected user ids.
            if ($df->grouped) {
                if (empty($data->groupid)) {
                    return;
                }
                if (!$userids = groups_get_members($data->groupid, 'u.id,u.id as uid', 'u.id')) {
                    return;
                }
                $userids = array_keys($userids);
            } else {
                if (empty($data->userid)) {
                    return;
                }
                $userids = array($data->userid);
            }

            // Update grades for the affected users.
            foreach ($userids as $userid) {
                $this->update_grades($userid);
            }

            // Update specific grade completion if tracked.
            if ($df->completionspecificgrade) {
                $completion = new \completion_info($df->course);
                if ($completion->is_enabled($df->cm) != COMPLETION_TRACKING_AUTOMATIC) {
                    return;
                }

                foreach ($userids as $userid) {
                    $completion->update_state($df->cm, COMPLETION_UNKNOWN, $userid);
                }
            }
        }
    }

    /**
     * Returns a list of users by gradebook roles.
     */
    public function get_gradebook_users(array $userids = null) {
        global $DB, $CFG;

        // Must have gradebook roles.
        if (empty($CFG->gradebookroles)) {
            return null;
        }

        $gradebookroles = explode(", ", $CFG->gradebookroles);

        if (!$df = $this->df) {
            return;
        }

        if (!empty($CFG->enablegroupings) and $df->cm->groupmembersonly) {
            $groupingsusers = groups_get_grouping_members($df->cm->groupingid, 'u.id', 'u.id');
            $gusers = $groupingsusers ? array_keys($groupingsusers) : null;
        }

        if (!empty($userids)) {
            if (!empty($gusers)) {
                $gusers = array_intersect($userids, $gusers);
            } else {
                $gusers = $userids;
            }
        }

        $roleusers = array();
        if (isset($gusers)) {
            if (!empty($gusers)) {
                list($inuids, $params) = $DB->get_in_or_equal($gusers, SQL_PARAMS_NAMED, 'u');
                foreach ($gradebookroles as $roleid) {
                    $roleusers = $roleusers + get_role_users(
                        $roleid,
                        $df->context,
                        true,
                        user_picture::fields('u'),
                        'u.lastname ASC',
                        true,
                        $df->currentgroup,
                        '',
                        '',
                        "u.id $inuids",
                        $params
                    );
                }
            }
        } else {
            foreach ($gradebookroles as $roleid) {
                $roleusers = $roleusers + get_role_users(
                    $roleid,
                    $df->context,
                    true,
                    user_picture::fields('u'),
                    'u.lastname ASC',
                    true,
                    $df->currentgroup
                );
            }
        }
        return $roleusers;
    }

    /**
     * Returns user's grades in the dataform instance per grade items and settings.
     * For simple direct grading you can use grade calculation to automate the grading.
     * Simple direct with no calculation returns nothing because the grades are overriden
     * in the gradebook and cannot be changed from the activity.
     * Returns array indexed by grade item number, of arrays of grades indexed by user id.
     *
     * @param int $userid optional user id, 0 means all users
     * @return array
     */
    public function get_user_grades($userid = 0) {
        $df = \mod_dataform_dataform::instance($this->_dataformid);

        if (!$df->grade) {
            return array();
        }

        // Gradable user ids.
        if ($userid) {
            $userids = array($userid);
        } else {
            if (!$gusers = $this->get_gradebook_users()) {
                return array();
            }
            $userids = array_keys($gusers);
        }

        $grades = array();
        foreach ($this->grade_items as $itemnumber => $gradeitem) {
            $itemgrades = array();
            if (!empty($gradeitem->gradingarea)) {
                // Advanced grading.
                $itemgrades = $this->get_user_grades_advanced($gradeitem, $userids);
            } else if (!empty($gradeitem->gradecalc)) {
                // Get calculated grades where applicable.
                $itemgrades = $this->get_user_grades_calculated($gradeitem, $userids);
            }

            if ($itemgrades) {
                foreach ($itemgrades as $userid => $grade) {
                    if (empty($grades[$userid])) {
                        $grades[$userid] = array($itemnumber => $grade);
                    } else {
                        $grades[$userid][$itemnumber] = $grade;
                    }
                }
            }
        }

        if ($grades) {
            return $grades;
        }

        return false;
    }

    /**
     * Returns the user's advanced grades for the specified grade item.
     *
     * @param grade_item $gradeitem
     * @param array $userids The user ids whose grades should be retrieved.
     * @return array|null
     */
    protected function get_user_grades_advanced($gradeitem, array $userids) {
        // Users container.
        $users = array_fill_keys($userids, array());

        // Grades container.
        $grades = array();
        foreach ($users as $userid => $unused) {
            $grades[$userid] = (object) array(
                'id' => $userid,
                'userid' => $userid,
                'rawgrade' => null
            );
        }

        $areaname = $gradeitem->gradingarea;
        $gradingman = get_grading_manager($df->context, 'mod_dataform', $areaname);
        $controller = $gradingman->get_active_controller();
        if (empty($controller)) {
            return array();
        }

        // Now get the gradefordisplay.
        $gradesmenu = make_grades_menu($gradeitem->grade);
        $controller->set_grade_range($gradesmenu, $gradeitem->grade > 0);
        $grade->gradefordisplay = $controller->render_grade(
            $PAGE,
            $grade->id,
            $gradingitem,
            $grade->grade,
            $cangrade
        );
        //} else {
        //    $grade->gradefordisplay = $this->display_grade($grade->grade, false);
        //}

        return $grades;
    }

    /**
     * Returns user's calculated grades for the specified grade item.
     *
     * @param grade_item $gradeitem
     * @param array $userids The user ids whose grades should be retrieved.
     * @return array|null
     */
    protected function get_user_grades_calculated($gradeitem, array $userids) {
        global $CFG;

        if (!$df = $this->df) {
            return null;
        }

        require_once("$CFG->libdir/mathslib.php");

        $formula = $gradeitem->gradecalc;

        // Patterns container.
        $patterns = array();

        // Users container.
        $users = array_fill_keys($userids, array());

        // Grades container.
        $grades = array();
        foreach ($users as $userid => $unused) {
            $grades[$userid] = (object) array(
                'id' => $userid,
                'userid' => $userid,
                'rawgrade' => null
            );

            // Num entries pattern.
            if (strpos($formula, '##numentries##') !== false) {
                $patterns['##numentries##'] = 0;
                if ($numentries = $df->get_entries_count_per_user($df::COUNT_ALL, $userid)) {
                    foreach ($numentries as $userid => $count) {
                        $users[$userid]['##numentries##'] = $count->numentries;
                    }
                }
            }

            // Extract grading field patterns from the formula.
            if (preg_match_all("/##\d*:[^#]+##/", $formula, $matches)) {
                // Get the entry ids per user.
                $entryids = $df->get_entry_ids_per_user($userid);

                foreach ($matches[0] as $pattern) {
                    $patterns[$pattern] = 0;

                    list($targetval, $fieldpattern) = explode(':', trim($pattern, '#'), 2);

                    // Get the field from the pattern.
                    if (!$field = $df->field_manager->get_field_by_pattern("[[$fieldpattern]]")) {
                        continue;
                    }

                    $uservalues = null;

                    // Get user values for the pattern.
                    // The field must either has helper\contentperuser component,
                    // or be an instance of interface grading.
                    $helper = "dataformfield_$field->type\\helper\\contentperuser";
                    if (class_exists($helper)) {
                        $uservalues = $helper::get_content($field, $fieldpattern, $entryids);
                    } else if ($field instanceof mod_dataform\interfaces\grading) {
                        // BC - this method for grading user values is depracated.
                        $uservalues = $field->get_user_values($fieldpattern, $entryids, $userid);
                    }

                    // Leave pattern value at 0 if no user values.
                    if (!$uservalues) {
                        continue;
                    }
                    // Register pattern values for users.
                    foreach ($uservalues as $userid => $values) {
                        // Keep only target val if specified.
                        if ($targetval) {
                            foreach ($values as $key => $value) {
                                if ($value != $targetval) {
                                    unset($values[$key]);
                                }
                            }
                        }
                        if ($values) {
                            $users[$userid][$pattern] = implode(',', $values);
                        }
                    }
                }
            }
        }

        // For each user calculate the formula and create a grade object.
        foreach ($grades as $userid => $grade) {
            // If no values, no grade for this user.
            if (empty($users[$userid])) {
                continue;
            }

            $values = $users[$userid];
            $replacements = array_merge($patterns, $values);
            $calculation = str_replace(array_keys($replacements), $replacements, $formula);

            $calc = new calc_formula("=$calculation");
            $result = $calc->evaluate();
            // False as result indicates some problem.
            if ($result !== false) {
                $grade->rawgrade = $result;
            }
        }

        return $grades;
    }

    /**
     * Compiles the item params for update grade item.
     *
     * @param grade_item|int $itemornumber
     * @param array $options.
     * @return array
     */
    public function get_grade_item_update_params($itemornumber, $options) {
        $gradeitems = $this->grade_items;
        $existingitem = false;

        if ($itemornumber instanceof \grade_item) {
            $itemnumber = $itemornumber->itemnumber;
            $existingitem = true;
        } else if ($gradeitems and array_key_exists($itemornumber, $gradeitems)) {
            $itemnumber = $itemornumber;
            $itemornumber = $gradeitems[$itemnumber];
            $existingitem = true;
        } else {
            $itemnumber = $itemornumber;
        }

        $altname = !empty($options['itemname']) ? $options['itemname'] : get_string('pluginname', 'dataform');

        $params = array();
        $params['itemnumber'] = $itemnumber;
        if ($existingitem) {
            // Params from existing item.
            $params['itemname'] = $itemornumber->itemname;
            $params['gradetype'] = $itemornumber->gradetype;
            $params['grademax'] = $itemornumber->grademax;
            $params['scaleid'] = $itemornumber->scaleid;
            $params['gradeguide'] = $itemornumber->gradeguide;
            $params['gradecalc'] = $itemornumber->gradecalc;
        } else {
            $params['itemname'] = $altname;
            $params['gradetype'] = GRADE_TYPE_NONE;
            $params['grademax'] = 0;
            $params['scaleid'] = 0;
            $params['gradeguide'] = null;
            $params['gradecalc'] = null;
        }

        // Make sure the name is unique.
        $itemname = !empty($options['itemname']) ? $options['itemname'] : $params['itemname'];
        $params['itemname'] = $this->get_name_for_item($itemornumber, $itemname);

        // Make sure the idnumber is unique.
        $idnumber = !empty($options['idnumber']) ? $options['idnumber'] : null;
        $params['idnumber'] = $this->get_idnumber_for_item($itemornumber, $idnumber);

        // Apply grade options.
        if (!empty($options['gradetype'])) {
            $params['gradetype'] = $options['gradetype'];
        }
        if (!empty($options['grademax'])) {
            $params['grademax'] = $options['grademax'];
        }
        if (!empty($options['scaleid'])) {
            $params['scaleid'] = $options['scaleid'];
        }

        return $params;
    }

    /**
     * Returns grade item grade params from instance grade. This converts the instance
     * grade property to the proper grade item paramters such as grademax and scaleid.
     *
     * @param int $grade.
     * @return array
     */
    public function get_grade_item_params_from_data($data) {
        $data = (object) $data;

        $params = array();

        if (!empty($data->name)) {
            $params['itemname'] = $data->name;
        }

        $params['gradetype'] = GRADE_TYPE_NONE;
        $params['grademax']  = 0;
        $params['grademin']  = 0;
        $params['scaleid'] = 0;

        if (!empty($data->grade)) {
            if ($data->grade > 0) {
                $params['gradetype'] = GRADE_TYPE_VALUE;
                $params['grademax']  = $data->grade;
                $params['grademin']  = 0;

            } else if ($data->grade < 0) {
                $params['gradetype'] = GRADE_TYPE_SCALE;
                $params['scaleid'] = -$data->grade;
            }
        }

        return $params;
    }

    /**
     * Returns a name for the item. If it's a new name it is checked for uniqueness
     * and if needed it is suffixed with a number. Thus for instance adding items
     * without specifying names will result in the items names instancename, instancename1,
     * instancename2 and so on.
     *
     * @param grade_item|int $itemornumber
     * @param array $options
     * @return string
     */
    protected function get_name_for_item($itemornumber, $name) {
        global $CFG;

        // With single grade, always take the instance name.
        if (!$CFG->dataform_multigradeitems) {
            return $name;
        }

        if ($existingitem = is_object($itemornumber)) {
            $itemnumber = $itemornumber->itemnumber;
        } else {
            $itemnumber = $itemornumber;
        }

        // If same as existing, just return.
        if ($existingitem and $itemornumber->itemname == $name) {
            return $name;
        }

        // If no grade items, the name is unique enough to use.
        if (!$gradeitems = $this->grade_items) {
            return $name;
        }

        // Get all current names.
        $names = array();
        foreach ($gradeitems as $ginumber => $gradeitem) {
            if ($ginumber == $itemnumber) {
                continue;
            }
            $names[] = $gradeitem->itemname;
        }

        // Make unique.
        $counter = 1;
        while (in_array($name, $names)) {
            $name = $name. "_$counter";
            $counter++;
        }

        return $name;
    }

    /**
     * Returns an idnumber for the item.
     *
     * @param grade_item|int $itemornumber
     * @param array $options.
     * @return string
     */
    protected function get_idnumber_for_item($itemornumber, $options) {
        $existingitem = is_object($itemornumber);
        $itemnumber = $existingitem ? $itemornumber->itemnumber : $itemornumber;
        $nooption = empty($options['idnumber']);

        if ($nooption) {
            if ($existingitem) {
                return $itemornumber->idnumber;
            } else {
                $value = null;
            }
        } else {
            $value = $options['idnumber'];

            // If same as existing, just return.
            if ($existingitem and $itemornumber->idnumber == $value) {
                return $value;
            }
        }

        // If no grade items, the idnumber is unique enough to use.
        if (!$gradeitems = $this->grade_items) {
            return $value;
        }

        // Get all current idnumbers.
        $values = array();
        foreach ($gradeitems as $ginumber => $gradeitem) {
            if ($ginumber == $itemnumber) {
                continue;
            }
            $values[] = $gradeitem->idnumber;
        }

        // Make unique.
        $counter = 1;
        while (in_array($value, $values)) {
            $value = $value. "_$counter";
            $counter++;
        }

        return $value;
    }

    /**
     *
     */
    protected function fetch_item_by_number($itemnumber) {
        $params = array(
            'itemtype' => 'mod',
            'itemmodule' => 'dataform',
            'iteminstance' => $this->_dataformid,
            'courseid' => $this->courseid,
            'itemnumber' => $itemnumber
        );
        if ($gitem = grade_item::fetch($params)) {
            // Get grade guides and calcs from the Dataform.
            $gdef = ($df = $this->df) ? $df->grade_items : null;

            // Attach guide.
            $gitem->gradeguide = !empty($gdef[$itemnumber]['ru']) ? $gdef[$itemnumber]['ru'] : null;
            // Attach calc.
            $gitem->gradecalc = !empty($gdef[$itemnumber]['ca']) ? $gdef[$itemnumber]['ca'] : null;
        }
        return $gitem;
    }

    /**
     *
     */
    protected function adjust_grade_item($itemnumber, $options) {
        if (!$gradeitem = $this->fetch_item_by_number($itemnumber)) {
            return null;
        }

        $update = false;
        if (isset($options['categoryid'])) {
            if ($options['categoryid'] != $gradeitem->categoryid) {
                $gradeitem->categoryid = $options['categoryid'];
                $update = true;
            }
        }

        if ($update) {
            $gradeitem->update();
        }

        return $gradeitem;
    }

    /**
     * Adds form elements in the specified form for selecting a grading area (gradeguide).
     * This is used in the Dataform settings form to set the default grade item (0) and
     * in the grade items form for all grade items.
     * Adding these form elements requires that there are grading areas for the instance
     * (realized by instances of the Advanced grading field). If no grading areas exist,
     * no form elements are added and the method returns false.
     *
     * @return boolean
     */
    public function get_form_definition_grading_areas(&$mform, $elementname, $calcname) {
        $options = $this->get_available_grading_areas();
        if ($options) {
            $options = array_merge(array('' => get_string('choosedots')), $options);
            $mform->addElement('select', $elementname, get_string('gradeguide', 'dataform'), $options);
            $mform->addHelpButton($elementname, 'gradeguide', 'dataform');
            $mform->disabledIf($elementname, $calcname, 'neq', '');
            return true;
        }
        return false;
    }

    /**
     * Adds form elements in the specified form for setting a grade calculation.
     * This is used in the Dataform settings form to set the default grade item (0) and
     * in the grade items form for all grade items.
     *
     * @return void
     */
    public function get_form_definition_grading_calc(&$mform, $elementname, $guidename = null) {
        $mform->addElement('textarea', $elementname, get_string('gradecalc', 'dataform'));
        $mform->addHelpButton($elementname, 'gradecalc', 'dataform');
        if ($guidename) {
            $mform->disabledIf($elementname, $guidename, 'neq', '');
        }
    }

    /**
     * Returns a list of grading area names indexed by the same. This list is essentially the
     * list of instances of the Advanced grading field in the Dataform instance context.
     *
     * @return array Assoicative array of grading areas.
     */
    public function get_available_grading_areas() {
        global $DB;

        $params = array('type' => 'gradingform', 'dataid' => $this->_dataformid);
        if ($areas = $DB->get_records_menu('dataform_fields', $params, 'name', 'id,name')) {
            return array_combine($areas, $areas);
        }
        return array();
    }

}
