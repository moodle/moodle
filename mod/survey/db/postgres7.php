<?PHP // $Id$

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
        modify_database("", "INSERT INTO prefix_log_display VALUES ('survey', 'add', 'survey', 'name');");
        modify_database("", "INSERT INTO prefix_log_display VALUES ('survey', 'update', 'survey', 'name');");
    }

    return true;
}


?>

