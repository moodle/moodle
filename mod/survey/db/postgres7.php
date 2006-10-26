<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function survey_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2004021601) {
        modify_database("", "INSERT INTO `prefix_survey` (`course`, `template`, `days`, `timecreated`, `timemodified`, `name`, `intro`, `questions`) VALUES (0, 0, 0, 985017600, 985017600, 'ciqname', 'ciqintro', '69,70,71,72,73')");
        modify_database("", "INSERT INTO `prefix_survey_questions` (`id`, `text`, `shorttext`, `multi`, `intro`, `type`, `options`) VALUES (69, 'ciq1', 'ciq1short', '', '', 0, '')");
        modify_database("", "INSERT INTO `prefix_survey_questions` (`id`, `text`, `shorttext`, `multi`, `intro`, `type`, `options`) VALUES (70, 'ciq2', 'ciq2short', '', '', 0, '')");
        modify_database("", "INSERT INTO `prefix_survey_questions` (`id`, `text`, `shorttext`, `multi`, `intro`, `type`, `options`) VALUES (71, 'ciq3', 'ciq3short', '', '', 0, '')");
        modify_database("", "INSERT INTO `prefix_survey_questions` (`id`, `text`, `shorttext`, `multi`, `intro`, `type`, `options`) VALUES (72, 'ciq4', 'ciq4short', '', '', 0, '')");
        modify_database("", "INSERT INTO `prefix_survey_questions` (`id`, `text`, `shorttext`, `multi`, `intro`, `type`, `options`) VALUES (73, 'ciq5', 'ciq5short', '', '', 0, '')");
    }

    if ($oldversion < 2004021602) {
        table_column("survey_answers", "answer1", "answer1", "text", "", "", "");
        table_column("survey_answers", "answer2", "answer2", "text", "", "", "");
    }
    if ($oldversion < 2004021900) {
        modify_database("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('survey', 'add', 'survey', 'name');");
        modify_database("", "INSERT INTO prefix_log_display (module, action, mtable, field) VALUES ('survey', 'update', 'survey', 'name');");
    }

    if ($oldversion < 2004111200) { 
        execute_sql("DROP INDEX {$CFG->prefix}survey_course_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}survey_analysis_survey_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}survey_analysis_userid_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}survey_answers_userid_idx;",false);
        execute_sql("DROP INDEX {$CFG->prefix}survey_answers_survey_idx;",false); 
        execute_sql("DROP INDEX {$CFG->prefix}survey_answers_question_idx;",false);

        modify_database('','CREATE INDEX prefix_survey_course_idx ON prefix_survey (course);');
        modify_database('','CREATE INDEX prefix_survey_analysis_survey_idx ON prefix_survey_analysis (survey);');
        modify_database('','CREATE INDEX prefix_survey_analysis_userid_idx ON prefix_survey_analysis (userid);');
        modify_database('','CREATE INDEX prefix_survey_answers_userid_idx ON prefix_survey_answers (userid);');
        modify_database('','CREATE INDEX prefix_survey_answers_survey_idx ON prefix_survey_answers (survey);');
        modify_database('','CREATE INDEX prefix_survey_answers_question_idx ON prefix_survey_answers (question);');
    }

    if ($oldversion < 2005031600) {
        execute_sql('SELECT setval(\''.$CFG->prefix.'survey_id_seq\', (select max(id) from '.$CFG->prefix.'survey))');
        execute_sql('SELECT setval(\''.$CFG->prefix.'survey_questions_id_seq\', (select max(id) from '.$CFG->prefix.'survey_questions))');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}


?>
