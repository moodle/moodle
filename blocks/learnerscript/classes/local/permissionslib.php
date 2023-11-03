<?php

namespace block_learnerscript\local;

use core_course_category;
use context_system;
use cache;
use context_helper;
use context_coursecat;

class permissionslib{

    private $contextlevel;

    private $roleid;

    private $contextid;

    private $archetype;

    private $userid;

    public function __construct($contextlevel, $roleid, $userid=null)
    {
        $this->set_context($contextlevel);
        $this->set_roleid($roleid);
        $this->set_userid($userid);
    }

    private function set_context($contextlevel){
        $this->contextlevel = $contextlevel; 
    }

    private function set_roleid($roleid){
        $this->roleid = $roleid;
        $this->set_role_archtype();
    }

    private function set_userid($userid){
        global $USER;
       $this->userid = !is_null($userid) ? $userid : $USER->id;
    }

    private function set_role_archtype(){
        global $DB;
        $this->archetype = $DB->get_field('role', 'archetype', ['id' => $this->roleid]);
    }

    public function has_permission(){
      global $DB;
      if(is_siteadmin($this->userid)){
        return true;
      }
      switch ($this->contextlevel) {
            case CONTEXT_SYSTEM:
                $context = context_system::instance();
                if(user_has_role_assignment($this->userid, $this->roleid, $context->id)){
                    return true;
                }
                break;
            case CONTEXT_COURSECAT:
                return $DB->record_exists_sql('SELECT ra.id FROM {role_assignments} AS ra 
                                                 JOIN {context} AS ctx ON ra.contextid = ctx.id 
                                                WHERE ra.userid = :userid AND ctx.contextlevel = :contextlevel AND ra.roleid = :roleid',
                                            array('userid' => $this->userid, 'contextlevel' => $this->contextlevel, 'roleid' => $this->roleid));
            break;
            case CONTEXT_COURSE:
                return $DB->record_exists_sql('SELECT ra.id FROM {role_assignments} AS ra 
                                                 JOIN {context} AS ctx ON ra.contextid = ctx.id 
                                                WHERE ra.userid = :userid AND ctx.contextlevel = :contextlevel AND ra.roleid = :roleid',
                                            array('userid' => $this->userid, 'contextlevel' => $this->contextlevel, 'roleid' => $this->roleid));
                break;
            default:
                return false;
            break;
        }

    }

    public function get_rolewise_courses(){
        global $DB;
        if(!$this->has_permission()){
            return false;
        }
        switch ($this->contextlevel) {
            case CONTEXT_SYSTEM:
                if($this->archetype == 'manager' && $this->contextlevel == CONTEXT_SYSTEM){
                    return true;
                }else{
                    return $this->get_rolecourses();
                }
                break;
            case CONTEXT_COURSE:
                return $this->get_rolecourses();
                break;
            case CONTEXT_COURSECAT:
                if($this->archetype == 'manager' && $this->contextlevel == CONTEXT_COURSECAT){
                    $capability = 'moodle/category:manage';
                    $categories = $this->make_categories_list('moodle/category:manage');
                    $categoryids = implode(',', array_keys($categories)); 
                    return  $DB->get_fieldset_sql('SELECT id FROM {course} WHERE category IN ('.$categoryids.')');
                }else{
                    $categories = $this->make_categories_list('moodle/category:viewhiddencategories');
                    $categoryids = implode(',', array_keys($categories));
                    $categorycourses =  $DB->get_fieldset_sql('SELECT id FROM {course} WHERE category IN ('.$categoryids.')');
                     /**
                     * Little trick here for categoty level course creator for child categories.
                     * Considering editing teacher role instead of coursecreator.
                     */                   
                    if($this->roleid == $DB->get_field('role', 'id', ['shortname' => 'coursecreator'])){
                        $this->roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
                        $this->contextlevel = CONTEXT_COURSE;
                        $assignedcourses = $this->get_rolecourses();
                        return array_intersect($categorycourses, $assignedcourses);
                    }
                    
                }
            break;
            default:
                return false;
            break;
        }
    }

    public function get_rolecourses() {
        global $DB;
        $params['contextlevel'] = $this->contextlevel;
        $params['userid'] = $this->userid;
        $params['userid1'] = $this->userid;
        $params['roleid'] = $this->roleid;
        $params['active'] = ENROL_USER_ACTIVE;
        $params['enabled'] = ENROL_INSTANCE_ENABLED;
        $params['now1'] = round(time(), -2); // improves db caching
        $params['now2'] = $params['now1']; 
        $role = $DB->get_field_sql("SELECT shortname FROM {role} WHERE id = $this->roleid");
        $enroljoin = " JOIN (SELECT DISTINCT e.courseid
                               FROM {enrol} AS e
                               JOIN {user_enrolments} AS ue ON (ue.enrolid = e.id AND ue.userid = :userid1)
                               WHERE ue.status = :active AND e.status = :enabled AND ue.timestart < :now1 AND
                                (ue.timeend = 0 OR ue.timeend > :now2)) en ON (en.courseid = c.id)";
        $sql =" SELECT c.id 
                 FROM {course} AS c";
        if($this->contextlevel == CONTEXT_SYSTEM){
            $sql .= " $enroljoin LEFT JOIN {context} AS ctx ON ctx.instanceid = 0 AND ctx.contextlevel = :contextlevel";
        }else if($this->contextlevel == CONTEXT_COURSECAT){
            $sql .=" $enroljoin JOIN {course_categories} as cc ON cc.id = c.category
                LEFT JOIN {context} AS ctx ON ctx.instanceid = cc.id AND ctx.contextlevel = :contextlevel";
        }else{
         $sql .= " $enroljoin LEFT JOIN {context} AS ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
        }
        $sql .=" JOIN {role_assignments} AS ra ON ra.contextid = ctx.id
                 JOIN {role} AS r ON r.id = ra.roleid
                WHERE ra.userid = :userid AND r.shortname = '".$role."'";

        $courses = $DB->get_fieldset_sql($sql, $params);
        return $courses;
    }
   /**
    * Notice: Overriding existing report with userid update
    */
   //Overriding default method - Just to make sure, schedule reports and emails will take userID based on configuration and not the current user
    public function make_categories_list($requiredcapability = '', $excludeid = 0, $separator = ' / ') {
        global $DB;
        $coursecatcache = cache::make('core', 'coursecat');

        // Check if we cached the complete list of user-accessible category names ($baselist) or list of ids
        // with requried cap ($thislist).
        $currentlang = current_language();
        $basecachekey = $currentlang . '_catlist';
        $baselist = $coursecatcache->get($basecachekey);
        $thislist = false;
        $thiscachekey = null;
        if (!empty($requiredcapability)) {
            $requiredcapability = (array)$requiredcapability;
            $thiscachekey = 'catlist:'. serialize($requiredcapability);
            if ($baselist !== false && ($thislist = $coursecatcache->get($thiscachekey)) !== false) {
                $thislist = preg_split('|,|', $thislist, -1, PREG_SPLIT_NO_EMPTY);
            }
        } else if ($baselist !== false) {
            $thislist = array_keys($baselist);
        }

        if ($baselist === false) {
            // We don't have $baselist cached, retrieve it. Retrieve $thislist again in any case.
            $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
            $sql = "SELECT cc.id, cc.sortorder, cc.name, cc.visible, cc.parent, cc.path, $ctxselect
                    FROM {course_categories} cc
                    JOIN {context} ctx ON cc.id = ctx.instanceid AND ctx.contextlevel = :contextcoursecat
                    WHERE cc.visible = 1 
                    ORDER BY cc.sortorder";
            $rs = $DB->get_recordset_sql($sql, array('contextcoursecat' => CONTEXT_COURSECAT));
            $baselist = array();
            $thislist = array();
            foreach ($rs as $record) {
                // If the category's parent is not visible to the user, it is not visible as well.
                if (!$record->parent || isset($baselist[$record->parent])) {
                    context_helper::preload_from_record($record);
                    $context = context_coursecat::instance($record->id);
                    if (!$record->visible && !has_capability('moodle/category:viewhiddencategories', $context, $this->userid)) {
                        // No cap to view category, added to neither $baselist nor $thislist.
                        continue;
                    }
                    $baselist[$record->id] = array(
                        'name' => format_string($record->name, true, array('context' => $context)),
                        'path' => $record->path
                    );
                    if (!empty($requiredcapability) && !has_all_capabilities($requiredcapability, $context, $this->userid)) {
                        // No required capability, added to $baselist but not to $thislist.
                        continue;
                    }
                    $thislist[] = $record->id;
                }
            }
            $rs->close();
            $coursecatcache->set($basecachekey, $baselist);
            if (!empty($requiredcapability)) {
                $coursecatcache->set($thiscachekey, join(',', $thislist));
            }
        } else if ($thislist === false) {
            // We have $baselist cached but not $thislist. Simplier query is used to retrieve.
            $ctxselect = context_helper::get_preload_record_columns_sql('ctx');
            $sql = "SELECT ctx.instanceid AS id, $ctxselect
                    FROM {context} ctx WHERE ctx.contextlevel = :contextcoursecat";
            $contexts = $DB->get_records_sql($sql, array('contextcoursecat' => CONTEXT_COURSECAT));
            $thislist = array();
            foreach (array_keys($baselist) as $id) {
                context_helper::preload_from_record($contexts[$id]);
                if (has_all_capabilities($requiredcapability, context_coursecat::instance($id), $this->userid)) {
                    $thislist[] = $id;
                }
            }
            $coursecatcache->set($thiscachekey, join(',', $thislist));
        }

        // Now build the array of strings to return, mind $separator and $excludeid.
        $names = array();
        foreach ($thislist as $id) {
            $path = preg_split('|/|', $baselist[$id]['path'], -1, PREG_SPLIT_NO_EMPTY);
            if (!$excludeid || !in_array($excludeid, $path)) {
                $namechunks = array();
                foreach ($path as $parentid) {
                    $namechunks[] = $baselist[$parentid]['name'];
                }
                $names[$id] = join($separator, $namechunks);
            }
        }
        return $names;
    }
}