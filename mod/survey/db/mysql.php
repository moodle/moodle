<?PHP // $Id$

function survey_upgrade($oldversion) {
// This function does anything necessary to upgrade
// older versions to match current functionality

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

    return true;
}


?>

