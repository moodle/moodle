<?PHP

function lesson_upgrade($oldversion) {
/// This function does anything necessary to upgrade 
/// older versions to match current functionality 

    global $CFG;

    if ($oldversion < 2004021600) {

       delete_records("log_display", "module", "lesson");

       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'start', 'lesson', 'name');");
       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'end', 'lesson', 'name');");
       modify_database ("", "INSERT INTO prefix_log_display VALUES ('lesson', 'view', 'lesson_pages', 'title');");

    }

    if ($oldversion < 2004022200) {

		table_column("lesson", "", "maxattempts", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "maxanswers");
		table_column("lesson", "", "nextpagedefault", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "maxattempts");
		table_column("lesson", "", "maxpages", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "nextpagedefault");
		table_column("lesson_pages", "", "qtype", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "lessonid");
		table_column("lesson_pages", "", "qoption", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "qtype");
		table_column("lesson_answers", "", "grade", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "jumpto");

    }

    if ($oldversion < 2004032000) {           // Upgrade some old beta lessons
		execute_sql(" UPDATE \"{$CFG->prefix}lesson_pages\" SET qtype = 3 WHERE qtype = 0");
    }
    
    if ($oldversion < 2004032400) {
		table_column("lesson", "", "usemaxgrade", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "grade");
		table_column("lesson", "", "minquestions", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "nextpagedefault");
    }
 
    if ($oldversion < 2004032700) {
		table_column("lesson_answers", "", "flags", "INTEGER", "4", "UNSIGNED", "0", "NOT NULL", "grade");
    }

    if ($oldversion < 2004060401) {
        modify_database('','CREATE INDEX prefix_lesson_course_idx ON prefix_lesson (course);');
        modify_database('','CREATE INDEX prefix_lesson_answers_lessonid_idx ON prefix_lesson_answers (lessonid);');
        modify_database('','CREATE INDEX prefix_lesson_answers_pageid_idx ON prefix_lesson_answers (pageid);');
        modify_database('','CREATE INDEX prefix_lesson_attempts_lessonid_idx ON prefix_lesson_attempts (lessonid);');
        modify_database('','CREATE INDEX prefix_lesson_attempts_pageid_idx ON prefix_lesson_attempts (pageid);');
        modify_database('','CREATE INDEX prefix_lesson_attempts_userid_idx ON prefix_lesson_attempts (userid);');
        modify_database('','CREATE INDEX prefix_lesson_grades_lessonid_idx ON prefix_lesson_grades (lessonid);');
        modify_database('','CREATE INDEX prefix_lesson_grades_userid_idx ON prefix_lesson_grades (userid);');
        modify_database('','CREATE INDEX prefix_lesson_pages_lessonid_idx ON prefix_lesson_pages (lessonid);');
    }
     
    return true;
}

?>
