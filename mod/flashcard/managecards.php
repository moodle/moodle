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
 * @package   mod_flashbard
 * @category  mod
 */
defined('MOODLE_INTERNAL') || die ();

if ($action) {
    include($CFG->dirroot.'/mod/flashcard/manageview.controller.php');
}

$pagesize = 20;
$allcards = $DB->count_records('flashcard_deckdata', array('flashcardid' => $flashcard->id));

$page = optional_param('page', 0, PARAM_INT);
$from = $page * $pagesize;

// Deferred header output.

echo $out;

$cards = $DB->get_records('flashcard_deckdata', array('flashcardid' => $flashcard->id), 'id', '*', $from, $pagesize);

$backstr = get_string('backside', 'flashcard');
$frontstr = get_string('frontside', 'flashcard');

$table = new html_table();
$table->head = array('', "<b>$backstr</b>", "<b>$frontstr</b>", '');
$table->size = array('10%', '40%', '40%', '10%');
$table->width = '100%';
$table->align = array('center', 'center', 'center', 'center');

$i = 0;
if ($cards) {
    foreach ($cards as $card) {
        $check = "<input type=\"checkbox\" name=\"items[]\" value=\"{$card->id}\" />";

        if ($flashcard->questionsmediatype == FLASHCARD_MEDIA_IMAGE) {
            $back = $renderer->print_image($flashcard, "questionimagefile/{$card->id}", true);
        } else if ($flashcard->questionsmediatype == FLASHCARD_MEDIA_SOUND) {
            $back = $renderer->play_sound($flashcard, "questionsoundfile/{$card->id}", 'false', true, "bell_b$i");
        } else if ($flashcard->questionsmediatype == FLASHCARD_MEDIA_VIDEO) {
            $back = $renderer->play_video($flashcard, "questionvideofile/{$card->id}", 'false', true, "bell_b$i", true);
        } else if ($flashcard->questionsmediatype == FLASHCARD_MEDIA_IMAGE_AND_SOUND) {
            $back = $renderer->print_image($flashcard, "questionimagefile/{$card->id}", true);
            $back .= "<br/>";
            $back = $renderer->play_sound($flashcard, "questionsoundfile/{$card->id}", 'false', true, "bell_b$i");
        } else {
            $back = format_text($card->questiontext, FORMAT_MOODLE);
        }

        if ($flashcard->answersmediatype == FLASHCARD_MEDIA_IMAGE) {
            $front = $renderer->print_image($flashcard, "answerimagefile/{$card->id}", true);
        } else if ($flashcard->answersmediatype == FLASHCARD_MEDIA_SOUND) {
            $front = $renderer->play_sound($flashcard, "answersoundfile/{$card->id}", 'false', true, "bell_f$i");
        } else if ($flashcard->answersmediatype == FLASHCARD_MEDIA_VIDEO) {
            $front = $renderer->play_video($flashcard, "answervideofile/{$card->id}", 'false', true, "bell_f$i", true);
        } else if ($flashcard->answersmediatype == FLASHCARD_MEDIA_IMAGE_AND_SOUND) {
            $front = $renderer->print_image($flashcard, "answerimagefile/{$card->id}", true);
            $front .= "<br/>";
            $front = $renderer->play_sound($flashcard, "answersoundfile/{$card->id}", 'false', true, "bell_f$i");
        } else {
            $front = format_text($card->answertext, FORMAT_MOODLE);
        }

        $pix = '<img src="'.$OUTPUT->image_url('t/edit').'" />';
        $params = array('id' => $id, 'view' => 'edit', 'what' => 'update', 'cardid' => $card->id);
        $editurl = new moodle_url('/mod/flashcard/view.php', $params);
        $command = '<a href="'.$editurl.'">'.$pix.'</a>';

        $pix = '<img src="'.$OUTPUT->image_url('t/delete').'" />';
        $params = array('id' => $id, 'view' => 'manage', 'what' => 'delete', 'items[]' => $card->id);
        $deleteurl = new moodle_url('/mod/flashcard/view.php', $params);
        $command .= ' <a href="'.$deleteurl.'">'.$pix.'</a>';
        $table->data[] = array($check, $back, $front, $command);
        $i++;
    }

    echo '<center>';
    echo $OUTPUT->paging_bar($allcards, $page, $pagesize, $url.'?id='.$id.'&view=manage', 'page');
    echo '</center>';
    echo '<form name="deletecards" action="'.$url.'" method="get">';
    echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
    echo '<input type="hidden" name="view" value="manage" />';
    echo '<input type="hidden" name="what" value="delete" />';
    echo '<input type="hidden" name="id" value="'.$id.'" />';
    echo html_writer::table($table);
    echo '</form>';
    echo '<center>';
    $url = new moodle_url('/mod/flashcard/view.php', array('id' => $id, 'view' => 'manage'));
    echo $OUTPUT->paging_bar($allcards, $page, $pagesize, $url, 'page');
    echo '</center>';
} else {
    echo $OUTPUT->box(get_string('nocards', 'flashcard'));
    echo '<br/>';
}

$addone = get_string('addone', 'flashcard');
$addthree = get_string('addthree', 'flashcard');
$deleteselectionstr = get_string('deleteselection', 'flashcard');
$sesskey = sesskey();
echo '<div class="rightlinks">';
if ($cards) {
    $jshandler = 'javascript:document.forms[\'deletecards\'].submit();';
    echo '<a href="'.$jshandler.'">'.$deleteselectionstr.'</a> - ';
}

$params = array('id' => $id, 'view' => 'edit', 'what' => 'addone', 'sesskey' => sesskey());
$addurl = new moodle_url('/mod/flashcard/view.php', $params);
echo '<a href="'.$addurl.'">'.$addone.'</a> - ';

$params = array('id' => $id, 'view' => 'edit', 'what' => 'addthree', 'sesskey' => sesskey());
$addthreeurl = new moodle_url('/mod/flashcard/view.php', $params);
echo '<a href="'.$addthreeurl.'">'.$addthree.'</a></div>';
