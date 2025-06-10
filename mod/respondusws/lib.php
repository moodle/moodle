<?php
// Respondus 4.0 Web Service Extension For Moodle
// Copyright (c) 2009-2023 Respondus, Inc.  All Rights Reserved.
// Date: December 15, 2023.
defined("MOODLE_INTERNAL") || die();
function respondusws_add_instance($instance) {
    global $DB;
    $dbman = $DB->get_manager();
    if (!$dbman->table_exists("respondusws")) {
        return false;
    }
    if ($DB->count_records("respondusws") > 0) {
        return get_string("onlyoneinstance", "respondusws");
    }
    $instance->timecreated = time();
    $instance->timemodified = $instance->timecreated;
    $record_id = $DB->insert_record("respondusws", $instance);
    respondusws_grade_item_update($instance);
    return $record_id;
}
function respondusws_update_instance($instance) {
    global $DB;
    $dbman = $DB->get_manager();
    if (!$dbman->table_exists("respondusws")) {
        return false;
    }
    $instance->timemodified = time();
    $instance->id = $instance->instance;
    $DB->update_record("respondusws", $instance);
    respondusws_grade_item_update($instance);
    return true;
}
function respondusws_delete_instance($id) {
    global $DB;
    $dbman = $DB->get_manager();
    if (!$dbman->table_exists("respondusws")) {
        return false;
    }
    if ($DB->count_records("respondusws") == 1) {
        return get_string("oneinstancerequired", "respondusws");
    }
    $instance = $DB->get_record("respondusws", array("id" => $id));
    if ($instance === false) {
        return false;
    }
    $DB->delete_records("respondusws", array("id" => $instance->id));
    respondusws_grade_item_update($instance);
    return true;
}
function respondusws_user_outline($course, $user, $mod, $instance) {
    $summary = new stdClass;
    $summary->time = time();
    $summary->info = get_string("nouseractivity", "respondusws");
    return $summary;
}
function respondusws_user_complete($course, $user, $mod, $instance) {
    print_string("nouseractivity", "respondusws");
    return true;
}
function respondusws_print_recent_activity($course, $viewfullnames, $timestart) {
    print_string("nomoduleactivity", "respondusws");
    return true;
}
function respondusws_cron() {
    return true;
}
function respondusws_update_grades($instance, $user_id = 0) {
}
function respondusws_grade_item_update($instance, $grades = null) {
    return 0;
}
function respondusws_get_participants($instance_id) {
    return array();
}
function respondusws_scale_used($instance_id, $scale_id) {
    return false;
}
function respondusws_scale_used_anywhere($scale_id) {
    return false;
}
function respondusws_get_view_actions() {
    return array(
      "view",
      "view all"
      );
}
function respondusws_get_post_actions() {
    return array(
      "publish",
      "retrieve"
      );
}
if ($CFG->version >= 2016120500) {
    function respondusws_course_content_deleted(\core\event\course_content_deleted $event) {
    }
} else {
    function respondusws_delete_course($course, $showfeedback) {
    }
}
function respondusws_process_options(&$instance) {
}
function respondusws_process_email($modargs, $body) {
}
function respondusws_refresh_events($course_id = 0) {
    return true;
}
function respondusws_print_overview($courses, &$htmlarray) {
    global $CFG;
    if (empty($courses) || !is_array($courses) || count($courses) == 0) {
        return;
    }
    $records = get_all_instances_in_courses("respondusws", $courses);
    if (count($records) == 0) {
        return;
    }
    foreach ($records as $instance) {
        $summary =
          '<div class="respondusws overview">' .
          '<div class="name">' . get_string("modulename", "respondusws") .
          ': <a ' . ($instance->visible ? '' : ' class="dimmed"') .
          ' href="' . $CFG->wwwroot . '/mod/respondusws/view.php?id=' .
          $instance->coursemodule . '">' . format_string($instance->name) .
          '</a></div></div>';
        if (empty($htmlarray[$instance->course]["respondusws"])) {
            $htmlarray[$instance->course]["respondusws"] = $summary;
        } else {
            $htmlarray[$instance->course]["respondusws"] .= $summary;
        }
    }
}
function respondusws_get_coursemodule_info($course_module) {
    global $DB;
    $dbman = $DB->get_manager();
    if (!$dbman->table_exists("respondusws")) {
        return null;
    }
    $instance = $DB->get_record("respondusws",
      array("id" => $course_module->instance));
    if ($instance === false) {
        return null;
    }
    $info = new stdClass;
    $info->name = $instance->name;
    return $info;
}
if ($CFG->version >= 2020061500) {
    function respondusws_get_course_content_items(\core_course\local\entity\content_item $defaultmodulecontentitem, \stdClass $user, \stdClass $course) {
        $items = array();
        return $items;
    }
    function mod_respondusws_get_all_content_items(\core_course\local\entity\content_item $defaultmodulecontentitem) {
        $items = array();
        return $items;
    }
} else if ($CFG->version >= 2016052300) {
    function respondusws_get_shortcuts($defaultitem) {
        $items = array();
        return $items;
    }
} else {
    function respondusws_get_types() {
        $types = array();
        return $types;
    }
}
function respondusws_get_recent_mod_activity(&$activities, &$index, $timestart, $course_id, $cmid, $user_id = 0, $group_id = 0) {
}
function respondusws_print_recent_mod_activity($activity, $course_id, $detail, $modnames, $viewfullnames) {
    print_string("nomoduleactivity", "respondusws");
}
function respondusws_reset_course_form_definition(&$mform) {
}
function respondusws_reset_course_form_defaults($course) {
    $defaults = array();
    return $defaults;
}
function respondusws_reset_userdata($data) {
    $component = get_string("modulenameplural", "respondusws");
    $status = array();
    if ($data->timeshift) {
        shift_course_mod_dates("respondusws",
          array("timecreated", "timemodified"),
          $data->timeshift, $data->courseid);
        $status[] = array(
          "component" => $component,
          "item" => get_string("datechanged"),
          "error"=> false
          );
    }
    return $status;
}
function respondusws_check_file_access($attempt_id, $question_id, $context = null) {
    return true;
}
if (respondusws_floatcompare($CFG->version, 2011070100, 2) >= 0) {
    function respondusws_questions_in_use($question_ids) {
        return false;
    }
} else {
    function respondusws_question_list_instances($question_id) {
        return array();
    }
}
function respondusws_supports($feature) {
    global $CFG;
    switch($feature) {
        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMMENT:
        case FEATURE_COMPLETION_HAS_RULES:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_GRADE_OUTCOMES:
        case FEATURE_GROUPS:
        case FEATURE_GROUPINGS:
        case FEATURE_IDNUMBER:
        case FEATURE_MODEDIT_DEFAULT_COMPLETION:
        case FEATURE_RATE:
            return false;
        case FEATURE_MOD_ARCHETYPE:
            return MOD_ARCHETYPE_OTHER;
        default:
            if (defined("FEATURE_USES_QUESTIONS")) {
                if ($feature == FEATURE_USES_QUESTIONS) {
                    return true;
                }
            }
            if (defined("FEATURE_GROUPMEMBERSONLY")) {
                if ($feature == FEATURE_GROUPMEMBERSONLY) {
                    return false;
                }
            }
            return null;
    }
}
if (respondusws_floatcompare($CFG->version, 2014051200, 2) >= 0) {
} else {
    function respondusws_extend_navigation($navigation, $course, $module, $cm) {
        $navigation->nodetype = navigation_node::NODETYPE_LEAF;
    }
}
function respondusws_extend_settings_navigation(settings_navigation $settings, navigation_node $navigation) {
}
function respondusws_get_extra_capabilities() {
    global $CFG;
    $caps = array(
        "mod/quiz:view",
        "mod/quiz:preview",
        "mod/quiz:manage",
        "moodle/site:viewfullnames",
        "moodle/course:activityvisibility",
        "moodle/course:viewhiddencourses",
        "moodle/course:viewhiddenactivities",
        "moodle/course:viewhiddensections",
        "moodle/course:update",
        "moodle/course:manageactivities",
        "moodle/course:managefiles",
        "moodle/question:managecategory",
        "moodle/question:add",
        "moodle/question:editmine",
        "moodle/question:editall",
        "moodle/question:viewmine",
        "moodle/question:viewall",
        "moodle/question:usemine",
        "moodle/question:useall",
        "moodle/question:movemine",
        "moodle/question:moveall"
        );
    if (respondusws_floatcompare($CFG->version, 2012062501.07, 2) >= 0) {
        $caps[] = "mod/quiz:addinstance";
    }
    return $caps;
}
function respondusws_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    return false;
}
function respondusws_floatcompare($f1, $f2, $precision) {
    if (function_exists("bccomp")) {
        return bccomp($f1, $f2, $precision);
    }
    if ($precision < 0) {
        $precision = 0;
    }
    $epsilon = 1 / pow(10, $precision);
    $diff    = ($f1 - $f2);
    if (abs($diff) < $epsilon) {
        return 0;
    } else if ($diff < 0) {
        return -1;
    } else {
        return 1;
    }
}
