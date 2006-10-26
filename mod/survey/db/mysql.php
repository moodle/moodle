<?php // $Id$

// THIS FILE IS DEPRECATED!  PLEASE DO NOT MAKE CHANGES TO IT!
//
// IT IS USED ONLY FOR UPGRADES FROM BEFORE MOODLE 1.7, ALL 
// LATER CHANGES SHOULD USE upgrade.php IN THIS DIRECTORY.

function survey_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

    global $CFG;

    if ($oldversion < 2002081400) {

        execute_sql("  ALTER TABLE `survey_questions` DROP `owner` ");
        execute_sql("  ALTER TABLE `survey_questions` ADD `shorttext` VARCHAR(30) NOT NULL AFTER `text` ");

        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'focus on interesting issues' WHERE id = 1 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'important to my practice' WHERE id = 2 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'improve my practice' WHERE id = 3 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'connects with my practice' WHERE id = 4 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'I\'m critical of my learning' WHERE id = 5 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'I\'m critical of my own ideas' WHERE id = 6 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'I\'m critical of other students' WHERE id = 7 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'I\'m critical of readings' WHERE id = 8 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'I explain my ideas' WHERE id = 9 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'I ask for explanations' WHERE id =10 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'I\'m asked to explain' WHERE id =11 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'students respond to me' WHERE id =12 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'tutor stimulates thinking' WHERE id =13 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'tutor encourages me' WHERE id =14 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'tutor models discourse' WHERE id =15 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'tutor models self-reflection' WHERE id =16 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'students encourage me' WHERE id =17 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'students praise me' WHERE id =18 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'students value me' WHERE id =19 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'student empathise' WHERE id =20 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'I understand other students' WHERE id =21 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'students understand me' WHERE id =22 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'I understand the tutor' WHERE id =23 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'tutor understands me' WHERE id =24 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'Relevance' WHERE id =25 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'Reflective Thinking' WHERE id =26 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'Interactivity' WHERE id =27 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'Tutor Support' WHERE id =28 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'Peer Support' WHERE id =29 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'Interpretation' WHERE id =30 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'Relevance' WHERE id =31 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'Reflective Thinking' WHERE id =32 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'Interactivity' WHERE id =33 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'Tutor Support' WHERE id =34 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =35 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =36 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =37 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =38 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =39 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =40 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =41 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =42 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =43 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =44 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'focus quality of argument' WHERE id =45 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'play devil\'s advocate' WHERE id =46 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'where people come from' WHERE id =47 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'understand different people' WHERE id =48 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'interact with variety' WHERE id =49 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'enjoy hearing opinions' WHERE id =50 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'strengthen by argue' WHERE id =51 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'know why people do' WHERE id =52 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'argue with authors' WHERE id =53 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'remain objective' WHERE id =54 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'think WITH people' WHERE id =55 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'use criteria to evaluate' WHERE id =56 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'try to understand' WHERE id =57 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'point out weaknesses' WHERE id =58 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'put myself in their shoes' WHERE id =59 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'putting on trial' WHERE id =60 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'i value logic most' WHERE id =61 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'insight from empathy' WHERE id =62 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'make effort to extend' WHERE id =63 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = 'what\'s wrong\?' WHERE id =64 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =65 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =66 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =67 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =68 ");
        execute_sql("  UPDATE `survey_questions` SET `shorttext` = '' WHERE id =69 ");

    }

    if ($oldversion < 2002110903) {
        if (! execute_sql("ALTER TABLE `survey_questions` ADD `shorttext` VARCHAR(30) NOT NULL AFTER `text` ")) {
            notify("If you get an error above, don't worry, just ignore it.  Everything is OK.");
        }

        execute_sql("UPDATE `survey` SET `name` = 'collesaname', `intro` = 'collesaintro' WHERE name = 'COLLES (Actual)' AND template = 0 ");
        execute_sql("UPDATE `survey` SET `name` = 'collespname', `intro` = 'collespintro' WHERE name = 'COLLES (Preferred)' AND template = 0");
        execute_sql("UPDATE `survey` SET `name` = 'collesapname', `intro` = 'collesapintro' WHERE name = 'COLLES (Preferred and Actual)' AND template = 0");
        execute_sql("UPDATE `survey` SET `name` = 'attlsname', `intro` = 'attlsintro' WHERE name = 'ATTLS (20 item version)' AND template = 0");

        execute_sql("UPDATE `survey_questions` SET `text` = 'colles1', `shorttext` = 'colles1short', `options` = 'scaletimes5' WHERE `shorttext` = 'focus on interesting issues'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles2', `shorttext` = 'colles2short', `options` = 'scaletimes5' WHERE `shorttext` = 'important to my practice'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles3', `shorttext` = 'colles3short', `options` = 'scaletimes5' WHERE `shorttext` = 'improve my practice'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles4', `shorttext` = 'colles4short', `options` = 'scaletimes5' WHERE `shorttext` = 'connects with my practice'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles5', `shorttext` = 'colles5short', `options` = 'scaletimes5' WHERE `shorttext` = 'I\'m critical of my learning'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles6', `shorttext` = 'colles6short', `options` = 'scaletimes5' WHERE `shorttext` = 'I\'m critical of my own ideas'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles7', `shorttext` = 'colles7short', `options` = 'scaletimes5' WHERE `shorttext` = 'I\'m critical of other students'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles8', `shorttext` = 'colles8short', `options` = 'scaletimes5' WHERE `shorttext` = 'I\'m critical of readings'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles9', `shorttext` = 'colles9short', `options` = 'scaletimes5' WHERE `shorttext` = 'I explain my ideas'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles10', `shorttext` = 'colles10short', `options` = 'scaletimes5' WHERE `shorttext` = 'I ask for explanations'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles11', `shorttext` = 'colles11short', `options` = 'scaletimes5' WHERE `shorttext` = 'I\'m asked to explain'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles12', `shorttext` = 'colles12short', `options` = 'scaletimes5' WHERE `shorttext` = 'students respond to me'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles13', `shorttext` = 'colles13short', `options` = 'scaletimes5' WHERE `shorttext` = 'tutor stimulates thinking'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles14', `shorttext` = 'colles14short', `options` = 'scaletimes5' WHERE `shorttext` = 'tutor encourages me'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles15', `shorttext` = 'colles15short', `options` = 'scaletimes5' WHERE `shorttext` = 'tutor models discourse'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles16', `shorttext` = 'colles16short', `options` = 'scaletimes5' WHERE `shorttext` = 'tutor models self-reflection'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles17', `shorttext` = 'colles17short', `options` = 'scaletimes5' WHERE `shorttext` = 'students encourage me'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles18', `shorttext` = 'colles18short', `options` = 'scaletimes5' WHERE `shorttext` = 'students praise me'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles19', `shorttext` = 'colles19short', `options` = 'scaletimes5' WHERE `shorttext` = 'students value me'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles20', `shorttext` = 'colles20short', `options` = 'scaletimes5' WHERE `shorttext` = 'student empathise'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles21', `shorttext` = 'colles21short', `options` = 'scaletimes5' WHERE `shorttext` = 'I understand other students'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles22', `shorttext` = 'colles22short', `options` = 'scaletimes5' WHERE `shorttext` = 'students understand me'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles23', `shorttext` = 'colles23short', `options` = 'scaletimes5' WHERE `shorttext` = 'I understand the tutor'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'colles24', `shorttext` = 'colles24short', `options` = 'scaletimes5' WHERE `shorttext` = 'tutor understands me'");

        execute_sql("UPDATE `survey_questions` SET `text` = 'collesm1', `shorttext` = 'collesm1short', `intro` = 'collesmintro', `options` = 'scaletimes5' WHERE `text` = 'Relevance'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'collesm2', `shorttext` = 'collesm2short', `intro` = 'collesmintro', `options` = 'scaletimes5' WHERE `text` = 'Reflective Thinking'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'collesm3', `shorttext` = 'collesm3short', `intro` = 'collesmintro', `options` = 'scaletimes5' WHERE `text` = 'Interactivity'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'collesm4', `shorttext` = 'collesm4short', `intro` = 'collesmintro', `options` = 'scaletimes5' WHERE `text` = 'Tutor Support'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'collesm5', `shorttext` = 'collesm5short', `intro` = 'collesmintro', `options` = 'scaletimes5' WHERE `text` = 'Peer Support'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'collesm6', `shorttext` = 'collesm6short', `intro` = 'collesmintro', `options` = 'scaletimes5' WHERE `text` = 'Interpretation'");

        execute_sql("UPDATE `survey_questions` SET `text` = 'howlong', `options` = 'howlongoptions' WHERE `text` = 'How long did this survey take you to complete\?'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'othercomments' WHERE `text` = 'Do you have any other comments\?'");

        execute_sql("UPDATE `survey_questions` SET `text` = 'attls1', `shorttext` = 'attls1short', `options` = 'scaleagree5' WHERE `shorttext` = 'focus quality of argument'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls2', `shorttext` = 'attls2short', `options` = 'scaleagree5' WHERE `shorttext` = 'play devil\'s advocate'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls3', `shorttext` = 'attls3short', `options` = 'scaleagree5' WHERE `shorttext` = 'where people come from'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls4', `shorttext` = 'attls4short', `options` = 'scaleagree5' WHERE `shorttext` = 'understand different people'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls5', `shorttext` = 'attls5short', `options` = 'scaleagree5' WHERE `shorttext` = 'interact with variety'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls6', `shorttext` = 'attls6short', `options` = 'scaleagree5' WHERE `shorttext` = 'enjoy hearing opinions'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls7', `shorttext` = 'attls7short', `options` = 'scaleagree5' WHERE `shorttext` = 'strengthen by argue'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls8', `shorttext` = 'attls8short', `options` = 'scaleagree5' WHERE `shorttext` = 'know why people do'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls9', `shorttext` = 'attls9short', `options` = 'scaleagree5' WHERE `shorttext` = 'argue with authors'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls10', `shorttext` = 'attls10short', `options` = 'scaleagree5' WHERE `shorttext` = 'remain objective'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls11', `shorttext` = 'attls11short', `options` = 'scaleagree5' WHERE `shorttext` = 'think WITH people'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls12', `shorttext` = 'attls12short', `options` = 'scaleagree5' WHERE `shorttext` = 'use criteria to evaluate'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls13', `shorttext` = 'attls13short', `options` = 'scaleagree5' WHERE `shorttext` = 'try to understand'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls14', `shorttext` = 'attls14short', `options` = 'scaleagree5' WHERE `shorttext` = 'point out weaknesses'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls15', `shorttext` = 'attls15short', `options` = 'scaleagree5' WHERE `shorttext` = 'put myself in their shoes'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls16', `shorttext` = 'attls16short', `options` = 'scaleagree5' WHERE `shorttext` = 'putting on trial'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls17', `shorttext` = 'attls17short', `options` = 'scaleagree5' WHERE `shorttext` = 'i value logic most'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls18', `shorttext` = 'attls18short', `options` = 'scaleagree5' WHERE `shorttext` = 'insight from empathy'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls19', `shorttext` = 'attls19short', `options` = 'scaleagree5' WHERE `shorttext` = 'make effort to extend'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attls20', `shorttext` = 'attls20short', `options` = 'scaleagree5' WHERE `shorttext` = 'what\'s wrong\?'");

        execute_sql("UPDATE `survey_questions` SET `text` = 'attlsm1', `shorttext` = 'attlsm1', `options` = 'scaleagree5', `intro` = 'attlsmintro' WHERE `text` = 'Attitudes Towards Thinking and Learning'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attlsm2', `shorttext` = 'attlsm2', `options` = 'scaleagree5', `intro` = 'attlsmintro' WHERE `text` = 'Connected Learning'");
        execute_sql("UPDATE `survey_questions` SET `text` = 'attlsm3', `shorttext` = 'attlsm3', `options` = 'scaleagree5', `intro` = 'attlsmintro' WHERE `text` = 'Separate Learning'");
    }
    if ($oldversion < 2002122300) {
        execute_sql("ALTER TABLE `survey_analysis` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
        execute_sql("ALTER TABLE `survey_answers` CHANGE `user` `userid` INT(10) UNSIGNED DEFAULT '0' NOT NULL ");
    }

    if ($oldversion < 2004021601) {
        execute_sql("INSERT INTO `{$CFG->prefix}survey` (`course`, `template`, `days`, `timecreated`, `timemodified`, `name`, `intro`, `questions`) VALUES (0, 0, 0, 985017600, 985017600, 'ciqname', 'ciqintro', '69,70,71,72,73')");
        execute_sql("INSERT INTO `{$CFG->prefix}survey_questions` (`id`, `text`, `shorttext`, `multi`, `intro`, `type`, `options`) VALUES (69, 'ciq1', 'ciq1short', '', '', 0, '')");
        execute_sql("INSERT INTO `{$CFG->prefix}survey_questions` (`id`, `text`, `shorttext`, `multi`, `intro`, `type`, `options`) VALUES (70, 'ciq2', 'ciq2short', '', '', 0, '')");
        execute_sql("INSERT INTO `{$CFG->prefix}survey_questions` (`id`, `text`, `shorttext`, `multi`, `intro`, `type`, `options`) VALUES (71, 'ciq3', 'ciq3short', '', '', 0, '')");
        execute_sql("INSERT INTO `{$CFG->prefix}survey_questions` (`id`, `text`, `shorttext`, `multi`, `intro`, `type`, `options`) VALUES (72, 'ciq4', 'ciq4short', '', '', 0, '')");
        execute_sql("INSERT INTO `{$CFG->prefix}survey_questions` (`id`, `text`, `shorttext`, `multi`, `intro`, `type`, `options`) VALUES (73, 'ciq5', 'ciq5short', '', '', 0, '')");
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
        execute_sql("ALTER TABLE {$CFG->prefix}survey DROP INDEX course;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}survey_analysis DROP INDEX survey;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}survey_analysis DROP INDEX userid;",false);
        execute_sql("ALTER TABLE {$CFG->prefix}survey_answers DROP INDEX userid;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}survey_answers DROP INDEX survey;",false); 
        execute_sql("ALTER TABLE {$CFG->prefix}survey_answers DROP INDEX question;",false);

        modify_database('','ALTER TABLE prefix_survey ADD INDEX course (course);');
        modify_database('','ALTER TABLE prefix_survey_analysis ADD INDEX survey (survey);');
        modify_database('','ALTER TABLE prefix_survey_analysis ADD INDEX userid (userid);');
        modify_database('','ALTER TABLE prefix_survey_answers ADD INDEX userid (userid);');
        modify_database('','ALTER TABLE prefix_survey_answers ADD INDEX survey (survey);');
        modify_database('','ALTER TABLE prefix_survey_answers ADD INDEX question (question);');
    }
    
    if ($oldversion < 2006042800) {

        execute_sql("UPDATE {$CFG->prefix}survey SET questions='' WHERE questions IS NULL");
        table_column('survey','questions','questions','varchar','255','','','not null');

        execute_sql("UPDATE {$CFG->prefix}survey SET intro='' WHERE intro IS NULL");
        table_column('survey','intro','intro','text','','','','not null');

        execute_sql("UPDATE {$CFG->prefix}survey_answers SET time='0' WHERE time IS NULL");
        table_column('survey_answers','time','time','int','10','unsigned','0','not null');

        execute_sql("UPDATE {$CFG->prefix}survey_answers SET answer1='' WHERE answer1 IS NULL");
        table_column('survey_answers','answer1','answer1','text','','','','not null');

        execute_sql("UPDATE {$CFG->prefix}survey_answers SET answer2='' WHERE answer2 IS NULL");
        table_column('survey_answers','answer2','answer2','text','','','','not null');

        execute_sql("UPDATE {$CFG->prefix}survey_questions SET intro='' WHERE intro IS NULL");
        table_column('survey_questions','intro','intro','varchar','50','','','not null');
    }

    //////  DO NOT ADD NEW THINGS HERE!!  USE upgrade.php and the lib/ddllib.php functions.

    return true;
}


?>
