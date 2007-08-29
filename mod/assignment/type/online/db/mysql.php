<?php  // $Id$


function assignment_type_online_upgrade($oldversion)  {
    global $CFG, $db;

    if ($oldversion < 2005042900 and empty($CFG->noconvertjournals)) {  // Phase out Journals, convert them 

        $affectedcourses = array();
                                                                        // to Online Assignment
        if ($journals = get_records('journal')) {

            require_once($CFG->dirroot.'/course/lib.php');

            $assignmentmodule = get_record('modules', 'name', 'assignment');

            foreach ($journals as $journal) {

                $affectedcourses[$journal->course] = $journal->course;

            /// First create the assignment instance
                $assignment = new object();
                $assignment->course = $journal->course;
                $assignment->name = addslashes($journal->name);
                $assignment->description = addslashes($journal->intro);
                $assignment->format = FORMAT_MOODLE;
                $assignment->assignmenttype = 'online';
                $assignment->resubmit = 1;
                $assignment->preventlate = 0;
                $assignment->emailteachers = 0;
                $assignment->var1 = 1;
                $assignment->var2 = 0;
                $assignment->var3 = 0;
                $assignment->var4 = 0;
                $assignment->var5 = 0;
                $assignment->var5 = 0;
                $assignment->maxbytes = 0;
                $assignment->timedue = 0;          /// Don't have time to work this out .... :-(
                $assignment->timeavailable = 0;
                $assignment->grade = $journal->assessed;
                $assignment->timemodified = $journal->timemodified;

                $assignment->id = insert_record('assignment', $assignment);

            /// Now create a new course module record

                $oldcm = get_coursemodule_from_instance('journal', $journal->id, $journal->course);

                $newcm = clone($oldcm);
                $newcm->module   = $assignmentmodule->id; 
                $newcm->instance = $assignment->id;
                $newcm->added    = time();

                if (! $newcm->id = add_course_module($newcm) ) {
                    error("Could not add a new course module");
                }
                
            /// And locate it above the old one

                if (!$section = get_record('course_sections', 'id', $oldcm->section)) {
                    $section->section = 0;  // So it goes somewhere!
                }

                $newcm->coursemodule = $newcm->id;
                $newcm->section      = $section->section;  // need relative reference

                if (! $sectionid = add_mod_to_section($newcm, $oldcm) ) {  // Add it before Journal
                    error("Could not add the new course module to that section");
                }
                
            /// Convert any existing entries from users
                if ($entries = get_records('journal_entries', 'journal', $journal->id)) {
                    foreach ($entries as $entry) {
                        $submission = new object;
                        $submission->assignment    = $assignment->id;
                        $submission->userid        = $entry->userid;
                        $submission->timecreated   = $entry->modified;
                        $submission->timemodified  = $entry->modified;
                        $submission->numfiles      = 0;
                        $submission->data1         = addslashes($entry->text);
                        $submission->data2         = $entry->format;
                        $submission->grade         = $entry->rating;
                        $submission->submissioncomment       = addslashes($entry->comment);
                        $submission->format        = FORMAT_MOODLE;
                        $submission->teacher       = $entry->teacher;
                        $submission->timemarked    = $entry->timemarked;
                        $submission->mailed        = $entry->mailed;
                    
                        $submission->id = insert_record('assignment_submissions', $submission);
                    }
                }
            }

        /// Clear the cache so this stuff appears

            foreach ($affectedcourses as $courseid) {
                rebuild_course_cache($courseid);
            }
        }

    /// Hide the whole Journal module (but not individual items, just to make undo easier)
        set_field('modules', 'visible', 0, 'name', 'journal');

        if($journals === false) {
            notify('The Journal module is becoming obsolete and being replaced by the superior Online Assignments, and  
                    it has been disabled on your site.  If you really want Journal back, you can enable it using the 
                    "eye" icon here:  Admin >> Modules >> Journal.');
        }
        else {
            notify('The Journal module is becoming obsolete and being replaced by the superior Online Assignments.  
                    It has been disabled on your site, and the '.count($journals).' Journal activites you had have been
                    converted into Online Assignments.  If you really want Journal back, you can enable it using the 
                    "eye" icon here:  Admin >> Modules >> Journal.');
        }
    }

    return true;

}
