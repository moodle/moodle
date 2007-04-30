<?php // $Id$

if (!isset($CFG)) {

    require('../config.php');
    require_once($CFG->libdir.'/adminlib.php');

    admin_externalpage_setup('oacleanup');

    admin_externalpage_print_header();
    online_assignment_cleanup(true);
    admin_externalpage_print_footer();

}



function online_assignment_cleanup($output=false) {
    global $CFG;

    if ($output) {
        print_heading('Online Assignment Cleanup');
        echo '<center>';
    }


    /// We don't want to run this code if we are doing an upgrade from an assignment
    /// version earlier than 2005041400
    /// because the assignment type field will not exist
    $amv = get_field('modules', 'version', 'name', 'assignment');
    if ((int)$amv < 2005041400) {
        if ($output) {
            echo '</center>';
        }
        return;
    }


    /// get the module id for assignments from db
    $arecord = get_record('modules', 'name', 'assignment');
    $aid = $arecord->id;


    /// get a list of all courses on this site
    $courses = get_records('course');

    /// cycle through each course
    foreach ($courses as $course) {

        $fullname = empty($course->fullname) ? 'Course: '.$course->id : $course->fullname;
        if ($output) print_heading($fullname);

        /// retrieve a list of sections beyond what is currently being shown
        $sql = 'SELECT * FROM '.$CFG->prefix.'course_sections WHERE course='.$course->id.' AND section>'.$course->numsections.' ORDER BY section ASC';
        if (!($xsections = get_records_sql($sql))) {
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
                        FROM  {$CFG->prefix}course_modules cm,
                    {$CFG->prefix}assignment a
                    WHERE cm.id = '$instance' AND
                        cm.module = '$aid' AND
                        cm.instance = a.id AND
                        a.assignmenttype = 'online'";


                    /// if record exists then we need to move instance to it's correct section
                    if (record_exists_sql($sql)) {

                        /// check the new section id
                        /// the journal update erroneously stored it in course_sections->section
                        $newsection = $xsection->section;
                        /// double check the new section
                        if ($newsection > $course->numsections) {
                            /// get the record for section 0 for this course
                            if (!($zerosection = get_record('course_sections', 'course', $course->id, 'section', '0'))) {
                                continue;
                            }
                            $newsection = $zerosection->id;
                        }

                        /// grab the section record
                        if (!($section = get_record('course_sections', 'id', $newsection))) {
                            if ($output) echo 'Serious error: Cannot retrieve section: '.$newsection.' for course: '. format_string($course->fullname) .'<br />';
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

                        set_field('course_sections', 'sequence', $section->sequence, 'id', $section->id);

                        /// now we need to remove the instance from the old sequence

                        /// grab the old section record
                        if (!($section = get_record('course_sections', 'id', $xsection->id))) {
                            if ($output) echo 'Serious error: Cannot retrieve old section: '.$xsection->id.' for course: '.$course->fullname.'<br />';
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

                        set_field('course_sections', 'sequence', $section->sequence, 'id', $section->id);


                        if ($output) echo 'Online Assignment (instance '.$instance.') moved from section '.$section->id.': to section '.$newsection.'<br />';

                    }
                }
            }

            /// if the summary and sequence are empty then remove this section
            if (empty($xsection->summary) and empty($xsection->sequence)) {
                delete_records('course_sections', 'id', $xsection->id);
                if ($output) echo 'Deleting empty section '.$xsection->section.'<br />';
            }
        }
    }

    echo '</center>';
}

?>
