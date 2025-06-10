<?php

///////////////////////////////////////////////////////////////////////////
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

class grade_report_grade_breakdown extends grade_report {
    // Cache grade item pull from db
    var $grade_items;

    // Cache the group pulled from db
    var $group;

    // id of the current group
    var $currentgroup;

    // id of the current grade chosen
    var $currentgrade;

    /**
     * This is a view only report
     */
    function process_data($data) {
    }

    function process_action($target, $action) {
    }

    function _s($key, $a = null) {
        return get_string($key, 'gradereport_grade_breakdown', $a);
    }

    function __construct($courseid, $gpr, $context, $gradeid=null, $groupid=null) {
        parent::__construct($courseid, $gpr, $context);

        global $DB;

        // Cache these capabilities
        $this->caps = array(
            'is_teacher' => has_capability('moodle/grade:viewall', $context),
            'hidden'     => has_capability('moodle/grade:viewhidden', $context)
        );

        // By default we'll be pulling from every item in the course
        $query = array('courseid' => $courseid);

        // They selected a grade item; store it
        if ($gradeid !== null) {
            set_user_preference('report_grade_breakdown_gradeid', $gradeid);
        } else {
            // Get previous grade item id; if, in fact, the item has been removed,
            // then we must still default to the course
            $gradeid = get_user_preferences('report_grade_breakdown_gradeid', 0);
        }

        $item_query = array('id' => $gradeid);

        if ($gradeid != 0 && $DB->get_field('grade_items', 'id', $item_query)) {
            $query += $item_query;
        }

        // Get the percentage for only this group
        if ($groupid) {
            $this->group = $DB->get_record('groups', array('id' => $groupid));
        }

        if (!$this->caps['hidden']) {
            $query += array('hidden' => 0);
        }

        $this->currentgroup = $groupid;
        $this->currentgrade = $gradeid;

        $this->grade_items = grade_item::fetch_all($query);

        $this->baseurl = '/grade/report/grade_breakdown/index.php';

        $this->course->groupmode = 2;
    }

    function setup_grade_items() {
        global $OUTPUT, $DB;

        $item_id_query = array(
            'itemtype' => 'course', 'courseid' => $this->course->id
        );

        $course_item_id = $DB->get_field('grade_items', 'id', $item_id_query);

        $sql = "SELECT g.id, g.itemname, gc.fullname FROM
                    {grade_items} g,
                    {grade_categories} gc
                 WHERE g.itemtype != 'course'
                   AND ((gc.id = g.iteminstance AND g.categoryid IS NULL)
                   OR gc.id = g.categoryid)
                   AND g.courseid = :courseid ";

        // User can't see hiddens: this means they can't see hidden
        if (!$this->caps['hidden']) {
            $sql .= " AND g.hidden = 0 ";
        }

        $sql .= " ORDER BY sortorder ";

        $params = array('courseid' => $this->courseid);

        $sql_grades = $DB->get_records_sql($sql, $params);

        $grades = array(0 => self::_s('all_grades'));

        foreach ($sql_grades as $id => $sql_g) {
            $grades[$id] = ($sql_g->itemname != null) ? $sql_g->itemname : $sql_g->fullname;
        }

        $grades += array($course_item_id => self::_s('course_total'));

        // Cache the grade selector html for later use
        $url = new moodle_url($this->baseurl, array(
            'id' => $this->courseid,
            'group' => $this->currentgroup
        ));

        $select = new single_select(
            $url, 'grade', $grades, $this->currentgrade, null
        );

        $select->attributes = array('id' => 'selectgrade');

        $this->grade_selector = $OUTPUT->render($select);
    }

    /**
     * Changing the setup groups method to look at group membership
     */
    function setup_groups() {
        global $OUTPUT, $USER, $DB;

        $params = array('courseid' => $this->courseid);

        $sql = "SELECT DISTINCT(g.id), g.name
                    FROM {groups} g,
                         {groups_members} gr
                    WHERE g.courseid = :courseid
                      AND gr.groupid = g.id ";

        if (!has_capability('moodle/site:accessallgroups', $this->context)) {
            $params += array('userid' => $USER->id);
            $sql .= " AND gr.userid = :userid ";
        }

        $sql .= " ORDER BY g.name";

        $sql_groups = $DB->get_records_sql_menu($sql, $params);

        $count = count($sql_groups);

        if ($count > 1 or $count  == 0) {
            $groups = array(0 => get_string('allparticipants')) + $sql_groups;
        } else {
            $groups = $sql_groups;
            $this->currentgroup = current(array_keys($sql_groups));

            $this->group = $DB->get_record('groups', array('id' => $this->currentgroup));
        }

        // Cache the grade selector html for later use
        $url = new moodle_url($this->baseurl, array(
            'id' => $this->courseid,
            'grade' => $this->currentgrade
        ));

        $select = new single_select(
            $url, 'group', $groups, $this->currentgroup, null
        );

        $select->attributes = array('id' => 'selectgroup');

        $this->group_selector = $OUTPUT->render($select);
    }

    function print_table() {
        global $OUTPUT, $DB;

        $params = array('contextid' => $this->context->id);
        // Filter by those who are actually enrolled

        $role_select = ", {role_assignments} ra ";
        $role_where = " AND ra.contextid = :contextid
                        AND ra.roleid IN ({$this->gradebookroles})
                        AND ra.userid = g.userid ";

        // Print a table for each grade item
        foreach ($this->grade_items as $item) {  
            
            $params['itemid'] = $item->id;

            if (!empty($this->group)) {
                $groupname = $this->group->name;

                $params['groupid'] = $this->group->id;

                // Get all the grades for that grade item, for this group
                $sql = "SELECT DISTINCT(g.id) AS uniqueid, g.* FROM
                            {grade_grades} g,
                            {groups_members} gm
                            $role_select
                        WHERE g.userid = gm.userid
                          $role_where
                          AND g.itemid = :itemid
                          AND gm.groupid = :groupid ";
            } else {
                $groupname = get_string('allparticipants');

                $sql = "SELECT g.* FROM
                            {grade_grades} g
                            $role_select
                        WHERE g.itemid = :itemid
                          $role_where ";
            }

            $sql .= " AND g.finalgrade IS NOT NULL
                      AND g.excluded = 0 ";

            if (!$this->caps['hidden']) {
                $sql .= " AND g.hidden = 0 ";
            }

            // Check preference
            // Get all the grades for that grade item
            $grades = $DB->get_records_sql($sql, $params);

            // Cache the decimal value of the grade item for later use
            $decimals = $item->get_decimals();

            // How many grades for this item?
            $total_grades = ($grades != null) ? count($grades) : 0;

            // Get the letter grade info for the course
            $letters = grade_get_letters($this->context);

            $data = array();
            // Prepare the data
            foreach ($letters as $boundary => $letter) {
                
                if (!isset($data[$letter])) {
                    $info = new stdClass;
                    $info->count = 0;
                    $info->boundary = $boundary;
                    $info->percent_total = 0;
                    $info->real_total = 0;
                    $info->high_percent = 0;
                    $info->high_real = 0;
                    $info->low_percent = 0;
                    $info->low_real = $item->grademax;
                    $data[$letter] = $info;
                }
            }

            // Filter the grades based on the letter
            if ($grades) {
                foreach ($grades as $grade) {
                    
                    // if we're reporting ALL grade items
                    if ( ! $this->currentgrade) {
                        $value = grade_grade::standardise_score($grade->finalgrade, 
                                $grade->rawgrademin, $grade->rawgrademax, 0, 100);

                    // else, if we're reporting a course or category grade item
                    } elseif(in_array($item->itemtype, ['course', 'category'])) {
                        $value = grade_grade::standardise_score($grade->finalgrade, 
                                $grade->rawgrademin, $grade->rawgrademax, 0, 100);

                    } else {
                        $value = grade_grade::standardise_score($grade->finalgrade, 
                                $item->grademin, $item->grademax, 0, 100);
                    }

                    $value = round($value, $item->get_decimals());

                    foreach ($letters as $boundary => $letter) {

                        // Add it to the data
                        if ($value >= $boundary) {
                            // Get the highest grade for this boundary
                            if ($data[$letter]->high_real <= $grade->finalgrade) {
                                $data[$letter]->high_real = $grade->finalgrade;
                                $data[$letter]->high_percent = $value;
                            }

                            // Get the lowest grade for this boundary which might
                            // be the same as the highest grade
                            if ($grade->finalgrade <= $data[$letter]->low_real) {
                                $data[$letter]->low_real = $grade->finalgrade;
                                $data[$letter]->low_percent = $value;
                            }

                            $data[$letter]->count += 1;
                            $data[$letter]->percent_total += $value;
                            $data[$letter]->real_total += $grade->finalgrade;
                            continue 2;
                        }
                    }
                }
            }

            // After the filter process, we must build the display data
            $max = 100;
            $final_data = array();
            foreach ($data as $letter => $info) {

                $boundary = format_float($info->boundary, $decimals);
                $gmax = format_float($max, $decimals);
                $boundary_max = format_float($item->grademax * ($info->boundary / 100), $decimals);
                $pmax = format_float($item->grademax * ($max / 100), $decimals);
                $high_percent = round($info->high_percent, $decimals);
                $low_percent = round($info->low_percent , $decimals);
                $high_real = round($info->high_real, $decimals);
                $low_real = round($info->low_real, $decimals);

                $line = array();

                $make_link = function ($self, $str) use ($info, $item) {
                    if ($info->count == 0) {
                         return $str;
                    } else {
                        return $self->link_to_letter($str, $info->boundary, $item->id);
                    }
                };


                $line[] = $make_link($this, format_string($letter));
                $line[] = $make_link($this, $boundary . '% - '.$gmax . '%');
                $line[] = $make_link($this, $boundary_max . ' - ' . $pmax);
                $line[] = $make_link($this, $high_percent . '%');
                $line[] = $make_link($this, $high_real);
                $line[] = $make_link($this, $low_percent. '%');
                $line[] = $make_link($this, $low_real);

                if ($info->count == 0) {
                    $line[] = '0%';
                    $line[] = 0;
                } else {
                    $rounded_percents = round(($info->percent_total / $info->count), $decimals);
                    $rounded_real = round(($info->real_total / $info->count), $decimals);

                    $line[] = $rounded_percents . '%';
                    $line[] = $rounded_real;
                }

                $line[] = round(($info->count / (($total_grades) ? $total_grades : 1)) * 100, $decimals) . '%';
                $line[] = $info->count;

                $final_data[] = $line;

                $max = $info->boundary - (1 / (pow(10, $decimals)));
            }

            // Footer info
            $final_data[] = array('<strong>'.get_string('total').'</strong>',
                                  '','','','','', '','','','',
                                  '<strong>' . $total_grades . '</strong>');

            // Get the name of the item
            if (!$item->itemname && $item->itemtype == 'course') {
                $name = self::_s('course_total');
            } else if (!$item->itemname) {
                $inst = array('id' => $item->iteminstance);
                $name = $DB->get_field('grade_categories', 'fullname', $inst);
            } else {
                $name = $item->itemname;
            }

            echo $OUTPUT->heading($name . ' for '. $groupname);

            // Prepare the table for viewing
            $table = new html_table();
            $table->head = array(
                get_string('letter', 'grades'),
                self::_s('percent_range'),
                self::_s('real_range'),
                self::_s('highest_percent'),
                self::_s('highest_real'),
                self::_s('lowest_percent'),
                self::_s('lowest_real'),
                self::_s('percent_average'),
                self::_s('real_average'),
                self::_s('total_percent'),
                self::_s('count')
            );

            $table->size = array('10%', '30%', '20%', '5%', '5%', '5%', '5%', '5%',
                                 '5%', '5%', '5%');

            $table->align = array('left', 'right', 'right', 'right', 'right', 'right',
                                  'right', 'right', 'right', 'right', 'right');

            $table->data = $final_data;

            echo html_writer::table($table);
        }
    }

    // Link the letter grade to even further break down info
    function link_to_letter($letter, $boundary, $grade) {
        if ($this->caps['is_teacher']) {
            $url = new moodle_url('letter_report.php', array(
                'id' => $this->courseid,
                'bound' => $boundary,
                'group' => $this->currentgroup,
                'grade' => $grade

            ));
            return html_writer::link($url, format_string($letter));
        } else {
            return $letter;
        }
    }
}

function print_edit_link($courseid, $grade_item, $grade_gradeid) {
    global $OUTPUT;

    if (!in_array($grade_item->itemtype, array('course', 'category'))) {
        $url = new moodle_url('/grade/edit/tree/grade.php', array(
            'courseid' => $courseid,
            'id' => $grade_gradeid,
            'gpr_type' => 'report',
            'gpr_courseid' => $courseid,
            'gpr_plugin' => 'grade_breakdown'
        ));

        $icon = $OUTPUT->pix_icon('i/edit', get_string('edit', 'grades'));

        return html_writer::link($url, $icon);
    } else {
        return $OUTPUT->pix_icon('i/invalid', get_string('edit', 'grades'));
    }
}

function find_rank($context, $grade_item, $grade_grade, $groupid) {
    global $DB;

    $params = array(
        'finalgrade' => $grade_grade->finalgrade,
        'itemid' => $grade_item->id,
        'contextid' => $context->id
    );

    $gradebookroles = get_config('moodle', 'gradebookroles');
    $group_select = '';
    $group_where = '';

    if ($groupid) {
        $params += array('groupid' => $groupid);

        $group_select = " INNER JOIN {groups_members} gr
                            ON gr.userid = g.userid ";
        $group_where = " AND gr.groupid = :groupid ";
    }

    $sql = "SELECT COUNT(DISTINCT(g.userid))
              FROM {grade_grades} g
                INNER JOIN {role_assignments} r
                  ON r.userid = g.userid
                $group_select
              WHERE g.finalgrade IS NOT NULL
                AND g.finalgrade > :finalgrade
                AND g.itemid = :itemid
                $group_where
                AND (r.contextid = :contextid
                AND r.roleid IN ({$gradebookroles}))";

    return $DB->count_records_sql($sql, $params) + 1;
}

// Course settings moodle form definition
function grade_report_grade_breakdown_settings_definition(&$mform) {
    global $CFG;

    $options = array(
        -1 => get_string('default', 'grades'),
        0 => get_string('no'),
        1 => get_string('yes')
    );

    $allowstudents = get_config('moodle', 'grade_report_grade_greakdown_allowstudents');

    if (empty($allowstudents)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement(
        'select', 'report_grade_breakdown_allowstudents',
        get_string('allowstudents', 'gradereport_grade_breakdown'),
        $options
    );
}

