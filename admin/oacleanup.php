<?php

if (!isset($CFG)) {

    require('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('oacleanup');

    echo $OUTPUT->header();
    online_assignment_cleanup(true);
    echo $OUTPUT->footer();

}



function online_assignment_cleanup($output=false) {
    global $CFG, $DB, $OUTPUT;

    if ($output) {
        echo $OUTPUT->heading('Online Assignment Cleanup');
        echo '<center>';
    }


    /// We don't want to run this code if we are doing an upgrade from an assignment
    /// version earlier than 2005041400
    /// because the assignment type field will not exist
    $amv = $DB->get_field('modules', 'version', array('name'=>'assignment'));
    if ((int)$amv < 2005041400) {
        if ($output) {
            echo '</center>';
        }
        return;
    }


    /// get the module id for assignments from db
    $arecord = $DB->get_record('modules', array('name', 'assignment'));
    $aid = $arecord->id;


    /// get a list of all courses on this site
    list($ctxselect, $ctxjoin) = context_instance_preload_sql('c.id', CONTEXT_COURSE, 'ctx');
    $sql = "SELECT c.* $ctxselect FROM {course} c $ctxjoin";
    $courses = $DB->get_records_sql($sql);

    /// cycle through each course
    foreach ($courses as $course) {
        context_instance_preload($course);
        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        if (empty($course->fullname)) {
            $fullname = get_string('course').': '.$course->id;
        } else {
            $fullname = format_string($course->fullname, true, array('context' => $context));
        }
        if ($output) echo $OUTPUT->heading($fullname);

        /// retrieve a list of sections beyond what is currently being shown
        $sql = "SELECT *
                  FROM {course_sections}
                 WHERE course=? AND section>?
              ORDER BY section ASC";
        $params = array($course->id, $course->numsections);
        if (!($xsections = $DB->get_records_sql($sql, $params))) {
            if ($output) echo 'No extra sections<br />';
            continue;
        }

        /// cycle through each of the xtra sections
        foreach ($xsections as $xsection) {

            if ($output) echo 'Checking Section: '.$xsection->section.'<br />';

            /// grab any module instances from the sequence field
            if (!empty($xsection->sequence)) {
                $instances = explode(',', $xsection->sequence);

                /// cycle through the instances
                foreach ($instances as $instance) {
                    /// is this an instance of an online assignment
                    $sql = "SELECT a.id
                              FROM  {course_modules} cm, {assignment} a
                             WHERE cm.id = ? AND cm.module = ? AND
                                   cm.instance = a.id AND a.assignmenttype = 'online'";
                    $params = array($instance, $aid);

                    /// if record exists then we need to move instance to it's correct section
                    if ($DB->record_exists_sql($sql, $params)) {

                        /// check the new section id
                        /// the journal update erroneously stored it in course_sections->section
                        $newsection = $xsection->section;
                        /// double check the new section
                        if ($newsection > $course->numsections) {
                            /// get the record for section 0 for this course
                            if (!($zerosection = $DB->get_record('course_sections', array('course'=>$course->id, 'section'=>'0')))) {
                                continue;
                            }
                            $newsection = $zerosection->id;
                        }

                        /// grab the section record
                        if (!($section = $DB->get_record('course_sections', array('id'=>$newsection)))) {
                            if ($output) {
                                echo 'Serious error: Cannot retrieve section: '.$newsection.' for course: '. $fullname .'<br />';
                            }
                            continue;
                        }

                        /// explode the sequence
                        if  (($sequence = explode(',', $section->sequence)) === false) {
                            $sequence = array();
                        }

                        /// add instance to correct section
                        array_push($sequence, $instance);

                        /// implode the sequence
                        $section->sequence = implode(',', $sequence);

                        $DB->set_field('course_sections', 'sequence', $section->sequence, array('id'=>$section->id));

                        /// now we need to remove the instance from the old sequence

                        /// grab the old section record
                        if (!($section = $DB->get_record('course_sections', array('id'=>$xsection->id)))) {
                            if ($output) echo 'Serious error: Cannot retrieve old section: '.$xsection->id.' for course: '.$fullname.'<br />';
                            continue;
                        }

                        /// explode the sequence
                        if  (($sequence = explode(',', $section->sequence)) === false) {
                            $sequence = array();
                        }

                        /// remove the old value from the array
                        $key = array_search($instance, $sequence);
                        unset($sequence[$key]);

                        /// implode the sequence
                        $section->sequence = implode(',', $sequence);

                        $DB->set_field('course_sections', 'sequence', $section->sequence, array('id'=>$section->id));


                        if ($output) echo 'Online Assignment (instance '.$instance.') moved from section '.$section->id.': to section '.$newsection.'<br />';

                    }
                }
            }

            /// if the summary and sequence are empty then remove this section
            if (empty($xsection->summary) and empty($xsection->sequence)) {
                $DB->delete_records('course_sections', array('id'=>$xsection->id));
                if ($output) echo 'Deleting empty section '.$xsection->section.'<br />';
            }
        }
    }

    echo '</center>';
}


