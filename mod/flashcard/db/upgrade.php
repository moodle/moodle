<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package mod-flashcard
 * @category mod
 * @author Tomasz Muras <nexor1984@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_flashcard_upgrade($oldversion = 0) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    $result = true;

    $table = new xmldb_table('question_match');
    if ($dbman->table_exists($table)) {
        $field = new xmldb_field('numquestions');
        $field->set_attributes (XMLDB_TYPE_INTEGER, '10', 'true', 'true', null, null, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field, true, true);
        }
    }

    if ($oldversion < 2008050400) {

        // Define field starttime to be added to flashcard.
        $table = new xmldb_table('flashcard');

        // Launch add field starttime.
        $field = new xmldb_field('starttime');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, null, 'timemodified');
        $result = $result && $dbman->add_field($table, $field);

        // Launch add field endtime.
        $field = new xmldb_field('endtime');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, null, 'starttime');
        $result = $result && $dbman->add_field($table, $field);

        // Launch add field autodowngrade.
        $field = new xmldb_field('autodowngrade');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 1, 'questionid');
        $result = $result && $dbman->add_field($table, $field);

        // Launch add field deck2_release.
        $field = new xmldb_field('deck2_release');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 96, 'autodowngrade');
        $result = $result && $dbman->add_field($table, $field);

        // Launch add field deck3_release.
        $field = new xmldb_field('deck3_release');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 96, 'deck2_release');
        $result = $result && $dbman->add_field($table, $field);

        // Launch add field deck1_delay.
        $field = new xmldb_field('deck1_delay');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 48, 'deck3_release');
        $result = $result && $dbman->add_field($table, $field);

        // Launch add field deck2_delay.
        $field = new xmldb_field('deck2_delay');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 96, 'deck1_delay');
        $result = $result && $dbman->add_field($table, $field);

        // Launch add field deck3_delay.
        $field = new xmldb_field('deck3_delay');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 168, 'deck2_delay');
        $result = $result && $dbman->add_field($table, $field);

        // Define table flashcard_card to be created.
        $table = new xmldb_table('flashcard_card');

        // Adding fields to table flashcard_card.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('flashcardid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, null);
        $table->add_field('entryid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('deck', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, null);
        $table->add_field('lastaccessed', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, '0');

        // Adding keys to table flashcard_card.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Launch create table for flashcard_card.
        $result = $result && $dbman->create_table($table);

        upgrade_mod_savepoint(true, 2008050400, 'flashcard');
    }

    if ($oldversion < 2008050500) {
        // Define field starttime to be added to flashcard.
        $table = new xmldb_table('flashcard');

        // Launch add field deck4_release.
        $field = new xmldb_field('deck4_release');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 96, 'deck3_release');
        $result = $result && $dbman->add_field($table, $field);

        // Launch add field deck4_delay.
        $field = new xmldb_field('deck4_delay');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, 336, 'deck3_delay');
        $result = $result && $dbman->add_field($table, $field);

        // Launch add field questionsasimages.
        $field = new xmldb_field('questionsasimages');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'deck4_delay');
        $result = $result && $dbman->add_field($table, $field);

        // Launch add field answersasimages.
        $field = new xmldb_field('answersasimages');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'questionsasimages');
        $result = $result && $dbman->add_field($table, $field);

        upgrade_mod_savepoint(true, 2008050500, 'flashcard');
    }

    if ($oldversion < 2008050501) {

        // Define field starttime to be added to flashcard.
        $table = new xmldb_table('flashcard');

        // Launch add field decks.
        $field = new xmldb_field('decks');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '3', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '3', 'autodowngrade');
        $result = $result && $dbman->add_field($table, $field);

        upgrade_mod_savepoint(true, 2008050501, 'flashcard');
    }

    if ($oldversion < 2008050800) {

        // Define table flashcard_deckdata to be created.
        $table = new xmldb_table('flashcard_deckdata');

        // Adding fields to table flashcard_deckdata.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
        $table->add_field('flashcardid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
        $table->add_field('questiontext', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);
        $table->add_field('answertext', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);

        // Adding keys to table flashcard_deckdata.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Launch create table for flashcard_deckdata.
        $dbman->create_table($table);

        upgrade_mod_savepoint(true, 2008050800, 'flashcard');
    }

    if ($oldversion < 2008050900) {

        // Define field accesscount to be added to flashcard_card.
        $table = new xmldb_table('flashcard_card');
        $field = new xmldb_field('accesscount');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'lastaccessed');

        // Launch add field accesscount.
        $dbman->add_field($table, $field);

        upgrade_mod_savepoint(true, 2008050900, 'flashcard');
    }

    if ($oldversion < 2008051100) {

        // Rename field questionsasimages on table flashcard to questionsmediatype.
        $table = new xmldb_table('flashcard');
        $field = new xmldb_field('questionsasimages');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'deck4_delay');

        // Launch rename field questionsmediatype.
        $dbman->rename_field($table, $field, 'questionsmediatype');

        // Rename field answersasimages on table flashcard to answersmediatype.
        $table = new xmldb_table('flashcard');
        $field = new xmldb_field('answersasimages');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'deck4_delay');

        // Launch rename field questionsmediatype.
        $dbman->rename_field($table, $field, 'answersmediatype');

        // Define field flipdeck to be added to flashcard.
        $table = new xmldb_table('flashcard');
        $field = new xmldb_field('flipdeck');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, null, null, null, null, '0', 'answersmediatype');
        $result = $result && $dbman->add_field($table, $field);

        upgrade_mod_savepoint(true, 2008051100, 'flashcard');
    }

    // First of all we need reencode all files stored in all cards and bring back files in our fileareas.

    if ($oldversion < 2012040200) {

        include_once($CFG->dirroot.'/mod/flashcard/locallib.php');

        // Which flashcards have file attached.
        $select = "
            questionsmediatype != ".FLASHCARD_MEDIA_TEXT." OR
            answersmediatype != ".FLASHCARD_MEDIA_TEXT."
        ";
        $flashcards = $DB->get_records_select('flashcard', $select, array());

        $fs = get_file_storage();

        if ($flashcards) {
            foreach ($flashcards as $f) {
                if (!$cm = get_coursemodule_from_instance('flashcard', $f->id)) {
                    continue;
                }
                $context = context_module::instance($cm->id);
                $cards = $DB->get_records('flashcard_deckdata', array('flashcardid' => $f->id));
                if ($cards) {
                    foreach ($cards as $c) {
                        if ($f->questionsmediatype != FLASHCARD_MEDIA_TEXT) {
                            convert_flashcard_file('question', $c, $f, $context->id, $fs);
                        }
                    }
                }
            }
        }

        upgrade_mod_savepoint(true, 2012040200, 'flashcard');
    }

    // Then continue upgrade.

    if ($oldversion < 2012040200) {
        // Rename summary into intro and    summaryformat into introformat.
        $table = new xmldb_table('flashcard');

        $field = new xmldb_field('summary');
        if ($dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'name');
            $dbman->rename_field($table, $field, 'intro');

            $field = new xmldb_field('summaryformat');
            $field->set_attributes( XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'intro');
            $dbman->rename_field($table, $field, 'introformat');
            $DB->execute("UPDATE {flashcard} SET introformat = 1");
        }

        // Workaround for MDL-26469.
        $record = $DB->get_record('modules', array('name' => 'flashcard'));
        $record->cron = 3600;
        $DB->update_record('modules', $record);

        upgrade_mod_savepoint(true, 2012040200, 'flashcard');
    }

    if ($oldversion < 2012040201) {
        $table = new xmldb_table('flashcard');

        $field = new xmldb_field('audiostart');

        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'answersmediatype');
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2012040201, 'flashcard');
    }

    if ($oldversion < 2013093000) {
        $table = new xmldb_table('flashcard');

        // Launch add field extracss.
        $field = new xmldb_field('extracss');
        $field->set_attributes(XMLDB_TYPE_TEXT, 'small', null, null, null, null, 'customreviewemptyfileid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2013093000, 'flashcard');
    }

    if ($oldversion < 2013101100) {
        $table = new xmldb_table('flashcard');

        // Launch add field extracss.
        $field = new xmldb_field('remindusers');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'completionallgood');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table flashcard_deckdata to be created.
        $table = new xmldb_table('flashcard_userdeck_state');

        // Adding fields to table flashcard_userdeck_data.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('flashcardid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('deck', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('state', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        // Adding keys to table flashcard_deckdata.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Launch create table for flashcard_deckdata.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2013101100, 'flashcard');
    }

    // Version Unconditionnaly play this upgrade script for handling Tomas Muraz version hook reintegration.
    // Code contribution from RemoteLearner. (Logan Reynolds).
    $table = new xmldb_table('flashcard');
    if (!$dbman->field_exists($table, 'audiostart') and $dbman->field_exists($table, 'flipdeck')) {

        // Create remaining columns to bring table structure up to assumed version# 2014051200 from previous author's code.
        $field = new xmldb_field('audiostart');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'answersmediatype');
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('custombackfileid');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'flipdeck');
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customfrontfileid');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'custombackfileid');
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customemptyfileid');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'customfrontfileid');
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customreviewfileid');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'customemptyfileid');
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customreviewedfileid');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'customreviewfileid');
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('customreviewemptyfileid');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'customreviewedfileid');
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('completionallviewed');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'customreviewemptyfileid');
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('completionallgood');
        if (!$dbman->field_exists($table, $field)) {
            $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 0, 'completionallviewed');
            $dbman->add_field($table, $field);
        }
    }

    if ($oldversion < 2014051200) {
        upgrade_mod_savepoint(true, 2014051200, 'flashcard');
    }

    if ($oldversion < 2017022000) {
        $table = new xmldb_table('flashcard');

        // Launch add field models.
        $field = new xmldb_field('models');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, 3, 'questionid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2017022000, 'flashcard');
    }

    return true;
}

function convert_flashcard_file($side, $card, $flashcard, $contextid, $fs) {
    global $DB;

    $mediakey = $side.'smediatype';
    $infokey = $side.'text';

    if ($flashcard->$mediakey != FLASHCARD_MEDIA_IMAGE_AND_SOUND) {
        switch ($flashcard->$mediakey) {
            case FLASHCARD_MEDIA_IMAGE: {
                $filearea = $side.'imagefile';
                break;
            }
            case FLASHCARD_MEDIA_SOUND: {
                $filearea = $side.'soundfile';
                break;
            }
        }

        if ($filerec = process_flashcard_file($card->$infokey, $filearea, $card, $flashcard, $contextid, $fs)) {
            $storedfile = $fs->get_file($filerec->contextid, $filerec->component, $filerec->filearea, $filerec->itemid,
                                         $filerec->filepath, $filerec->filename);
            if ($storedfile) {
                $card->$infokey = $storedfile->get_id();
            }
        }
    } else {
        $soundid = '';
        $imageid = '';
        list($image, $sound) = explode('@', $card->$infokey);
        if (!empty($image)) {
            if ($filerec = process_flashcard_file($image, $side.'imagefile', $card, $flashcard, $contextid, $fs)) {
                $storedfile = $fs->get_file($filerec->contextid, $filerec->component, $filerec->filearea,
                                             $filerec->itemid, '/', $filerec->filename);
                $imageid = $storedfile->get_id();
            }
        }
        if (!empty($sound)) {
            if ($filerec = process_flashcard_file($sound, $side.'soundfile', $card, $flashcard, $contextid, $fs)) {
                $storedfile = $fs->get_file($filerec->contextid, $filerec->component, $filerec->filearea,
                                             $filerec->itemid, '/', $filerec->filename);
                $soundid = $storedfile->get_id();
            }
        }
        $card->$infokey = "$imageid@$soundid";
    }

    $DB->update_record('flashcard_deckdata', $card);
}

function process_flashcard_file($filename, $filearea, $card, $flashcard, $contextid, $fs) {
    global $CFG;

    // Prepare the filerec adapted to the situation.
    $filerec = new StdClass;
    $filerec->contextid = $contextid;
    $filerec->component = 'mod_flashcard';
    $filerec->filearea = $filearea;
    $filerec->itemid = $card->id;

    if (preg_match('#^https?://#', $filename)) {
        // We do not know yet what to do but probably create a new file from an URL.
        assert(1);
    } else if (preg_match('#moddata/flashcard/(\d+)/(.*)$#', $filename, $matches)) {
        $dirname = dirname($matches[2]);
        // Force to root in the filearea.
        $filerec->filepath = '/';
        $filerec->filename = basename($matches[2]);
        if (file_exists($CFG->dataroot.'/'.$flashcard->course.'/'.$filename)) {
            $fs->create_file_from_pathname($filerec, $CFG->dataroot.'/'.$flashcard->course.'/'.$filename);
        } else {
            return false;
        }
    } else {
        // Get parts.
        $dirname = dirname($filename);
        // Force to root in the filearea.
        $filerec->filepath = '/';
        $filerec->filename = basename($filename);
        // Files should be in legacy files.
        $coursecontext = context_course::instance($flashcard->course);
        if (!empty($filerec->filename)) {
            $file = $fs->get_file($coursecontext->id, 'course', 'legacy', 0, $filerec->filepath, $filerec->filename);
            if ($file) {
                // Quick fix on non compatible names.
                $filerec->filename = str_replace('?', '', $filerec->filename);
                $filerec->filename = str_replace('!', '', $filerec->filename);
                $filerec->filename = str_replace('/', '', $filerec->filename);
                $filerec->filename = str_replace(' ', '_', $filerec->filename);
                $fs->create_file_from_storedfile($filerec, $file);
            } else {
                mtrace("Missing file : $flashcard->course / $filename<br/>");
                return false;
            }
        }
    }
    return $filerec;
}
