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
 * a controller for the play view
 *
 * @package mod_flashcard
 * @category mod
 * @author Valery Fremaux
 * @author Tomasz Muras
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 *
 * @usecase add
 * @usecase delete
 * @usecase save
 * @usecase import
 * @usecase doimport
 */
defined('MOODLE_INTERNAL') || die();

/* ******************************* Delete a set of records **************************** */

if ($action == 'delete') {

    if (!isset($items)) {
        $items = required_param_array('items', PARAM_INT);
    }

    foreach ($items as $item) {

        $card = $DB->get_record('flashcard_deckdata', array('id' => $item));

        flashcard_delete_attached_files($cm, $flashcard, $card);

        if (!$DB->delete_records('flashcard_deckdata', array('id' => $item))) {
            print_error('errordeletecard', 'flashcard');
        }

        if (!$DB->delete_records('flashcard_card', array('entryid' => $item))) {
            print_error('errordeletecard', 'flashcard');
        }
    }
}

/* ******************************* Prepare import **************************** */

if ($action == 'import') {
    include_once($CFG->dirroot.'/mod/flashcard/import_form.php');
    $mform = new flashcard_import_form();
    echo $out;
    echo $OUTPUT->heading(get_string('importingcards', 'flashcard') . $OUTPUT->help_icon('import', 'flashcard'));
    $formdata = new StdClass;
    $formdata->id = $cm->id;
    $formdata->view = 'manage';
    $formdata->what = 'doimport';
    $mform->set_data($formdata);
    $mform->display();
    echo $OUTPUT->footer($course);
    exit(0);
}

/* ******************************* Perform import **************************** */

if ($action == 'doimport') {
    include_once($CFG->dirroot.'/mod/flashcard/import_form.php');
    $form = new flashcard_import_form();

    $fieldseppatterns[0] = ',';
    $fieldseppatterns[1] = ':';
    $fieldseppatterns[2] = ';';

    if ($data = $form->get_data()) {

        if (!empty($data->confirm)) {

            $fieldsep = $fieldseppatterns[$data->fieldsep];

            // Filters comments and non significant lines.
            $data->import = preg_replace("/^#.*\$/m", '', $data->import);
            $data->import = preg_replace("/^\\/.*\$/m", '', $data->import);
            $data->import = preg_replace('/^\\s+$/m', '', $data->import);
            $data->import = preg_replace("/(\\r?\\n)\\r?\\n/", '$1', $data->import);
            $data->import = trim($data->import);

            $pairs = preg_split("/\r?\n/", $data->import);
            if (!empty($pairs)) {
                // First integrity check.
                $report = new StdClass;
                $report->cards = count($pairs);
                $report->badcards = 0;
                $report->goodcards = 0;
                $inputs = array();
                foreach ($pairs as $pair) {
                    if (strstr($pair, $fieldsep) === false) {
                        $report->badcards++;
                    } else {
                        $input = new StdClass;
                        list($input->question, $input->answer) = explode($fieldsep, $pair);
                        if (empty($input->question) || empty($input->answer)) {
                            $report->badcards++;
                        } else {
                            $inputs[] = $input;
                            $report->goodcards++;
                        }
                    }
                }

                if ($report->badcards == 0) {
                    // Everything ok.
                    // Reset all data.
                    $DB->delete_records('flashcard_card', array('flashcardid' => $flashcard->id));
                    $DB->delete_records('flashcard_deckdata', array('flashcardid' => $flashcard->id));

                    // Insert new cards.
                    foreach ($inputs as $input) {
                        $deckcard = new StdClass;
                        $deckcard->flashcardid = $flashcard->id;
                        $deckcard->questiontext = $input->question;
                        $deckcard->answertext = $input->answer;
                        $DB->insert_record('flashcard_deckdata', $deckcard);
                    }

                    // Reset questionid in flashcard instance.
                    $DB->set_field('flashcard', 'questionid', 0, array('id' => $flashcard->id));
                }

                $reportstr = get_string('importreport', 'flashcard').'<br/>';
                $reportstr = get_string('cardsread', 'flashcard').$report->cards.'<br/>';
                if ($report->badcards) {
                    $reportstr .= get_string('goodcards', 'flashcard').$report->goodcards.'<br/>';
                    $reportstr .= get_string('badcards', 'flashcard').$report->badcards.'<br/>';
                }

                echo '<center>';
                echo $OUTPUT->box($reportstr, 'reportbox');
                echo '</center>';
            }
        }
    }
}
