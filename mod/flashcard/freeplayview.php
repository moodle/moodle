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
 * This view allows free playing with a deck
 *
 * @package mod_flashcard
 * @category mod
 * @author Gustav Delius
 * @author Valery Fremaux
 */
defined('MOODLE_INTERNAL') || die();

$subquestions = $DB->get_records('flashcard_deckdata', array('flashcardid' => $flashcard->id));

if (empty($subquestions)) {
    echo $out;
    echo $OUTPUT->notification(get_string('nosubquestions', 'flashcard'));
    echo $OUTPUT->footer();
    die;
}
$subquestions = draw_rand_array($subquestions, count($subquestions));

// Print deferred header.

echo $out;

// Print summary.

if (!empty($flashcard->summary)) {
    echo $OUTPUT->box_start();
    echo format_text($flashcard->summary, $flashcard->summaryformat, null, $course->id);
    echo $OUTPUT->box_end();
}

?>

<script language="javascript">
//<![CDATA[
currentitem = 0;
maxitems = <?php echo count($subquestions); ?>;
remaining = maxitems;

var qtype = "<?php echo $flashcard->questionsmediatype ?>";
var atype = "<?php echo $flashcard->answersmediatype ?>";
//]]>
</script>
<script src="<?php echo $CFG->wwwroot.'/mod/flashcard/js/module.js' ?>"></script>

<p><?php print_string('freeplayinstructions', 'flashcard'); ?>.</p>
<?php
echo '<table class="flashcard_board" width="100%">';
echo '<tr>';
echo '<td rowspan="6">';

$i = 0;

if ($flashcard->flipdeck) {
    // Flip media types once.
    $tmp = $flashcard->answersmediatype;
    $flashcard->answersmediatype = $flashcard->questionsmediatype;
    $flashcard->questionsmediatype = $tmp;
}

foreach ($subquestions as $subquestion) {
    echo '<center>';
    $divid = "f$i";
    $divstyle = ($i > 0) ? 'display:none' : '';
    $imageurl = $renderer->print_custom_url($flashcard, 'customback', 0);
    echo '<div id="'.$divid.'"
               class="flashcard-question"
               style="'.$divstyle.';background-repeat:no-repeat;background-image:url('.$imageurl.')"
               onclick="javascript:clicked(\'f\', \''.$i.'\')">';

    $back = 'question';
    $front = 'answer';

    if ($flashcard->flipdeck) {
        // Flip card side values.
        $tmp = $subquestion->answertext;
        $subquestion->answertext = $subquestion->questiontext;
        $subquestion->questiontext = $tmp;
        $back = 'answer';
        $front = 'question';
        // Flip media types.
        $tmp = $flashcard->answersmediatype;
        $flashcard->answersmediatype = $flashcard->questionsmediatype;
        $flashcard->questionsmediatype = $tmp;
    }

    if ($flashcard->flipdeck) {
        // Flip card side values.
        $tmp = $subquestion->answertext;
        $subquestion->answertext = $subquestion->questiontext;
        $subquestion->questiontext = $tmp;
    }

    echo '<table width="100%" height="100%">';
    echo '<tr>';
    echo '<td align="center" valign="center">';
    if ($flashcard->questionsmediatype == FLASHCARD_MEDIA_IMAGE) {
        echo $renderer->print_image($flashcard, "{$back}imagefile/{$subquestion->id}");
    } else if ($flashcard->questionsmediatype == FLASHCARD_MEDIA_SOUND) {
        echo $renderer->play_sound($flashcard, "{$back}soundfile/{$subquestion->id}", 'false', false, "bell_b$i");
    } else if ($flashcard->questionsmediatype == FLASHCARD_MEDIA_VIDEO) {
        echo $renderer->play_video($flashcard, "{$back}videofile/{$subquestion->id}", $autoplay, false, "bell_b$i");
    } else if ($flashcard->questionsmediatype == FLASHCARD_MEDIA_IMAGE_AND_SOUND) {
        echo $renderer->print_image($flashcard, "{$back}imagefile/{$subquestion->id}");
        echo "<br/>";
        echo $renderer->play_sound($flashcard, "{$back}soundfile/{$subquestion->id}", $autoplay, false, "bell_b$i");
    } else {
        echo format_text($subquestion->questiontext, FORMAT_HTML);
    }
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '</div>';
    echo '</center>';
    echo '<center>';

    $imageurl = $renderer->print_custom_url($flashcard, 'customfront', 0);
    echo '<div id="b'.$i.'"
               class="flashcard-answer"
               style="display:none;background-repeat:no-repeat;background-image:url('.$imageurl.')"
               onclick="javascript:clicked(\'b\', \''.$i.'\')">';
    echo '<table width="100%" height="100%">';
    echo '<tr>';
    echo '<td align="center" valign="center" style="">';

    if ($flashcard->answersmediatype == FLASHCARD_MEDIA_IMAGE) {
        echo $renderer->print_image($flashcard, "{$front}imagefile/{$subquestion->id}");
    } else if ($flashcard->answersmediatype == FLASHCARD_MEDIA_SOUND) {
        echo $renderer->play_sound($flashcard, "{$front}soundfile/{$subquestion->id}", 'false', false, "bell_f$i");
    } else if ($flashcard->answersmediatype == FLASHCARD_MEDIA_VIDEO) {
        echo $renderer->play_video($flashcard, "{$front}videofile/{$subquestion->id}", $autoplay, false, "bell_f$i");
    } else if ($flashcard->answersmediatype == FLASHCARD_MEDIA_IMAGE_AND_SOUND) {
        echo $renderer->print_image($flashcard, "{$front}imagefile/{$subquestion->id}");
        echo "<br/>";
        echo $renderer->play_sound($flashcard, "{$front}soundfile/{$subquestion->id}", $autoplay, false, "bell_f$i");
    } else {
        echo format_text($subquestion->answertext, FORMAT_HTML);
    }

    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '</div>';
    echo '</center>';

    $i++;
}

echo '<center>';
echo $renderer->finishtable();
echo '</center>';
echo '</td>';
echo '</tr>';

echo $renderer->freebuttons($subquestions);

echo '</table>';
