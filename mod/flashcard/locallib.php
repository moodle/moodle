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
 * internal library of functions and constants for module flashcard
 * @package mod_flashcard
 * @category mod
 * @author Gustav Delius
 * @author Valery Fremaux
 */
defined('MOODLE_INTERNAL') || die();

define('FLASHCARD_MEDIA_TEXT', 0);
define('FLASHCARD_MEDIA_IMAGE', 1);
define('FLASHCARD_MEDIA_SOUND', 2);
define('FLASHCARD_MEDIA_IMAGE_AND_SOUND', 3);
define('FLASHCARD_MEDIA_VIDEO', 4);

define('FLASHCARD_MODEL_BOTH', 0x03);
define('FLASHCARD_MODEL_LEITNER', 0x01);
define('FLASHCARD_MODEL_FREEUSE', 0x02);

/**
 * computes the last accessed date for a deck as the oldest card being in the deck
 * @param reference $flashcard the flashcard object
 * @param int $deck the deck number
 * @param int $userid the user the deck belongs to
 * @uses $USER for setting default user
 * @uses $CFG, $DB
 */
function flashcard_get_lastaccessed(&$flashcard, $deck, $userid = 0) {
    global $USER, $DB;

    if ($userid == 0) {
        $userid = $USER->id;
    }

    $sql = "
        SELECT
            MIN(lastaccessed) as lastaccessed
        FROM
            {flashcard_card}
        WHERE
            flashcardid = ? AND
            userid = ? AND
            deck = ?
    ";
    $rec = $DB->get_record_sql($sql, array($flashcard->id, $userid, $deck));
    return $rec->lastaccessed;
}

/**
 * initialize decks for a given user. The initialization is soft as it will
 * be able to add new subquestions
 * @param reference $flashcard
 * @param int $userid
 * @ues $DB
 */
function flashcard_initialize(&$flashcard, $userid) {
    global $DB;

    // Get all cards (all decks).
    $select = 'flashcardid = ? AND userid = ?';
    $cards = $DB->get_records_select('flashcard_card', $select, array($flashcard->id, $userid));
    $registered = array();
    if (!empty($cards)) {
        foreach ($cards as $card) {
            $registered[] = $card->entryid;
        }
    }

    // Get all subquestions.
    $params = array('flashcardid' => $flashcard->id);
    if ($subquestions = $DB->get_records('flashcard_deckdata', $params, '', 'id,id')) {
        foreach ($subquestions as $subquestion) {
            if (in_array($subquestion->id, $registered)) {
                continue;
            }
            $card = new StdClass();
            $card->userid = $userid;
            $card->flashcardid = $flashcard->id;
            $card->lastaccessed = time() - ($flashcard->deck1_delay * HOURSECS);
            $card->deck = 1;
            $card->entryid = $subquestion->id;
            if (! $DB->insert_record('flashcard_card', $card)) {
                print_error('dbcouldnotinsert', 'flashcard');
            }
        }
    } else {
        return false;
    }

    return true;
}

/**
 * imports data into the deck from a matching question. This allows making a quiz with questions
 * then importing data to form a card deck.
 * @param reference $flashcard
 * @uses $DB
 * @return void
 */
function flashcard_import(&$flashcard) {
    global $DB;

    $question = $DB->get_record('question', array('id' => $flashcard->questionid));

    if ($question->qtype != 'match') {
        notice("Not a match question. Internal error");
        return;
    }

    $options = $DB->get_record('question_match', array('question' => $question->id));
    list($usql, $params) = $DB->get_in_or_equal(explode(',', $options->subquestions));
    $select = "id $usql AND answertext != '' AND questiontext != ''";
    if ($subquestions = $DB->get_records_select('question_match_sub', $select, $params)) {

        // Cleanup the flashcard.
        $DB->delete_records('flashcard_card', array('flashcardid' => $flashcard->id));
        $DB->delete_records('flashcard_deckdata', array('flashcardid' => $flashcard->id));

        // Transfer data.
        foreach ($subquestions as $subquestion) {
            $deckdata->flashcardid = $flashcard->id;
            $deckdata->questiontext = $subquestion->questiontext;
            $deckdata->answertext = $subquestion->answertext;
            $deckdata->lastaccessed = 0;
            $DB->insert_record('flashcard_deckdata', $deckdata);
        }
    }
    return true;
}

/**
 * get count, last access time and reactivability for all decks
 * @param reference $flashcard
 * @param int $userid
 * @uses $USER
 * @uses $DB
 */
function flashcard_get_deck_status(&$flashcard, $userid = 0) {
    global $USER, $DB;

    if ($userid == 0) {
        $userid = $USER->id;
    }

    unset($status);

    $dk3 = 0;
    $dk4 = 0;
    $dk1 = $DB->count_records('flashcard_card', array('flashcardid' => $flashcard->id, 'userid' => $userid, 'deck' => 1));
    $status = new StdClass();
    $status->decks[0] = new StdClass();
    $status->decks[0]->count = $dk1;
    $status->decks[0]->deckid = 1;
    $dk2 = $DB->count_records('flashcard_card', array('flashcardid' => $flashcard->id, 'userid' => $userid, 'deck' => 2));
    $status->decks[1] = new StdClass();
    $status->decks[1]->count = $dk2;
    $status->decks[1]->deckid = 2;
    if ($flashcard->decks >= 3) {
        $dk3 = $DB->count_records('flashcard_card', array('flashcardid' => $flashcard->id, 'userid' => $userid, 'deck' => 3));
        $status->decks[2] = new StdClass();
        $status->decks[2]->count = $dk3;
        $status->decks[2]->deckid = 3;
    }
    if ($flashcard->decks >= 4) {
        $dk4 = $DB->count_records('flashcard_card', array('flashcardid' => $flashcard->id, 'userid' => $userid, 'deck' => 4));
        $status->decks[3] = new StdClass();
        $status->decks[3]->count = $dk4;
        $status->decks[3]->deckid = 4;
    }

    // Not initialized for this user.
    if ($dk1 + $dk2 + $dk3 + $dk4 == 0) {
        return null;
    }

    if ($dk1 > 0) {
        $status->decks[0]->lastaccess = flashcard_get_lastaccessed($flashcard, 1, $userid);
        $status->decks[0]->reactivate = (time() > ($status->decks[0]->lastaccess + $flashcard->deck1_delay * HOURSECS));
    }
    if ($dk2 > 0) {
        $status->decks[1]->lastaccess = flashcard_get_lastaccessed($flashcard, 2, $userid);
        $status->decks[1]->reactivate = (time() > ($status->decks[1]->lastaccess + $flashcard->deck2_delay * HOURSECS));
    }
    if ($flashcard->decks >= 3 && $dk3 > 0) {
        $status->decks[2]->lastaccess = flashcard_get_lastaccessed($flashcard, 3, $userid);
        $status->decks[2]->reactivate = (time() > ($status->decks[2]->lastaccess + $flashcard->deck3_delay * HOURSECS));
    }
    if ($flashcard->decks >= 4 && $dk4 > 0) {
        $status->decks[3]->lastaccess = flashcard_get_lastaccessed($flashcard, 4, $userid);
        $status->decks[3]->reactivate = (time() > ($status->decks[3]->lastaccess + $flashcard->deck4_delay));
    }

    return $status;
}

/**
 * get card status structure
 * @param reference $flashcard
 * @uses $DB
 */
function flashcard_get_card_status(&$flashcard) {
    global $DB;

    // Get decks by card.
    if ($CFG->dbtype == 'sqlsrv') {
        $sql = "
            SELECT
                dd.questiontext + '_' + CAST(c.deck AS NVARCHAR(MAX)),
                dd.questiontext AS question,
                COUNT(c.id) AS amount,
                c.deck AS deck
            FROM
                {flashcard_deckdata} dd
            LEFT JOIN
                {flashcard_card} c
            ON
                c.entryid = dd.id
            WHERE
                c.flashcardid = ?
            GROUP BY
                c.deck, dd.questiontext, dd.questiontext
        ";
    } else {
        $sql = "
            SELECT
                CONCAT(CONCAT(dd.questiontext, '_'), c.deck),
                dd.questiontext AS question,
                COUNT(c.id) AS amount, c.deck AS deck
            FROM
                {flashcard_deckdata} dd
            LEFT JOIN
                {flashcard_card} c
            ON
                c.entryid = dd.id
            WHERE
                c.flashcardid = ?
            GROUP BY
                c.deck, dd.questiontext
        ";
    }
    $recs = $DB->get_records_sql($sql, array($flashcard->id));

    // Get accessed by card.
    $sql = "
        SELECT
           dd.questiontext,
           SUM(accesscount) AS accessed
        FROM
            {flashcard_deckdata} dd
        LEFT JOIN
            {flashcard_card} c
        ON
            c.entryid = dd.id
        WHERE
            c.flashcardid = ?
        GROUP BY
            c.entryid, dd.questiontext
    ";
    $accesses = $DB->get_records_sql($sql, array($flashcard->id));

    $cards = array();
    foreach (array_values($recs) as $rec) {
        if ($rec->deck == 1) {
            $cards[$rec->question]->deck[0] = $rec->amount;
        }
        if ($rec->deck == 2) {
            $cards[$rec->question]->deck[1] = $rec->amount;
        }
        if ($rec->deck == 3) {
            $cards[$rec->question]->deck[2] = $rec->amount;
        }
        if ($rec->deck == 4) {
            $cards[$rec->question]->deck[3] = $rec->amount;
        }
        $cards[$rec->question]->accesscount = $accesses[$rec->question]->accessed;
    }
    return $cards;
}

/**
 * new media renderers cannot be used because not tunable in autoplay
 * @TODO : remove as deprecated. Dewplayer more stable.
 */
function flashcard_mp3_player(&$flashcard, $url, $htmlid) {
    global $CFG;

    $audiostart = ($flashcard->audiostart) ? 'no' : 'yes&autoPlay=yes';
    $c = 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
         'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
         'waitForPlay='.$audiostart;

    static $count = 0;
    $count++;
    // We need something unique because it might be stored in text cache.
    $id = ($htmlid) ? $htmlid : 'flashcard_filter_mp3_'.time().$count;

    $url = addslashes_js($url);

    return '<span class="mediaplugin mediaplugin_mp3" id="'.$id.'_player">('.'mp3audio'.')</span>
<script type="text/javascript">
//<![CDATA[
  var FO = { movie:"'.$CFG->wwwroot.'/mod/flashcard/players/mp3player/mp3player.swf?src='.$url.'",
    width:"90", height:"15", majorversion:"6", build:"40", flashvars:"'.$c.'", quality: "high" };
  UFO.create(FO, "'.$id.'_player");
//]]>
</script>';
}

function flashcard_mp3_dewplayer(&$flashcard, $url, $htmlid) {
    global $CFG;

    $audiostart = ($flashcard->audiostart) ? 1 : 0;

    $playerflashurl = $CFG->wwwroot.'/mod/flashcard/players/dewplayer/dewplayer-mini.swf';
    $return = '<object type="application/x-shockwave-flash"
                       data="'.$playerflashurl.'"
                       width="160"
                       height="20"
                       id="'.$htmlid.'"
                       name="dewplayer">';
    $return .= '<param name="wmode" value="transparent" />';
    $return .= '<param name="movie" value="dewplayer-mini.swf" />';
    $return .= '<param name="flashvars" value="mp3='.urlencode($url).'&amp;autostart='.$audiostart.'" />';
    $return .= '</object>';

    return $return;
}

function flashcard_flowplayer($flashcard, $videofileurl, $videotype, $htmlname, $thumb) {
    global $CFG;

    $playerclass = ($thumb) ? 'flashcard-flowplayer-thumb' : 'flashcard-flowplayer';

    $str = '';

    $str .= '<div id="'.$htmlname.'_player"
                  style="z-index:10000"
                  data-swf="'.$CFG->wwwroot.'/mod/flashcard/players/flowplayer/flowplayer.swf"
                  class="flowplayer '.$playerclass.' play-button"
                  data-ratio="0.416">';
    $str .= '<video preload="none">';
    $str .= '<source type="video/'.$videotype.'" src="'.$videofileurl.'"/>';
    $str .= '</video>';

    $str .= '</div>';

    return $str;
}

function flashcard_delete_attached_files(&$cm, &$flashcard, $card) {

    $fs = get_file_storage();

    $context = context_module::instance($cm->id);

    switch ($flashcard->questionsmediatype) {
        case FLASHCARD_MEDIA_TEXT:
            break;
        case FLASHCARD_MEDIA_SOUND:
            $fs->delete_area_files($context->id, 'flashcard', 'questionsoundfile', $card->id);
            break;
        case FLASHCARD_MEDIA_IMAGE:
            $fs->delete_area_files($context->id, 'flashcard', 'questionimagefile', $card->id);
            break;
        case FLASHCARD_MEDIA_VIDEO:
            $fs->delete_area_files($context->id, 'flashcard', 'questionvideofile', $card->id);
            break;
        case FLASHCARD_MEDIA_IMAGE_AND_SOUND:
            $fs->delete_area_files($context->id, 'flashcard', 'questionimagefile', $card->id);
            $fs->delete_area_files($context->id, 'flashcard', 'questionsoundfile', $card->id);
            break;
    }

    switch ($flashcard->answersmediatype) {
        case FLASHCARD_MEDIA_TEXT:
            break;
        case FLASHCARD_MEDIA_SOUND:
            $fs->delete_area_files($context->id, 'flashcard', 'answersoundfile', $card->id);
            break;
        case FLASHCARD_MEDIA_IMAGE:
            $fs->delete_area_files($context->id, 'flashcard', 'answerimagefile', $card->id);
            break;
        case FLASHCARD_MEDIA_VIDEO:
            $fs->delete_area_files($context->id, 'flashcard', 'answervideofile', $card->id);
            break;
        case FLASHCARD_MEDIA_IMAGE_AND_SOUND:
            $fs->delete_area_files($context->id, 'flashcard', 'answersoundfile', $card->id);
            $fs->delete_area_files($context->id, 'flashcard', 'answerimagefile', $card->id);
            break;
    }
}

function flashcard_save_draft_customimage(&$flashcard, $customimage) {
    global $USER;

    $usercontext = context_user::instance($USER->id);
    $context = context_module::instance($flashcard->coursemodule);

    $filepickeritemid = optional_param($customimage, 0, PARAM_INT);

    if (!$filepickeritemid) {
        return;
    }

    $fs = get_file_storage();

    $flashcard->$customimage = 0;
    if (!$fs->is_area_empty($usercontext->id, 'user', 'draft', $filepickeritemid, true)) {
        $filearea = str_replace('fileid', '', $customimage);
        file_save_draft_area_files($filepickeritemid, $context->id, 'mod_flashcard', $filearea, 0);
        $savedfiles = $fs->get_area_files($context->id, 'mod_flashcard', $filearea, 0);
        $savedfile = array_pop($savedfiles);
        $flashcard->$customimage = $savedfile->get_id();
    }
}

function flashcard_get_file_url($filerecid, $asobject = false) {
    global $CFG;

    $fs = get_file_storage();

    $url = '';
    $file = $fs->get_file_by_id($filerecid);
    if ($file) {
        $filename = $file->get_filename();
        $contextid = $file->get_contextid();
        $filearea = $file->get_filearea();
        $itemid = $file->get_itemid();

        $url = $CFG->wwwroot."/pluginfile.php/{$contextid}/mod_flashcard/{$filearea}/{$itemid}/{$filename}";

        if ($asobject) {
            $f = new StdClass();
            $f->pathname = $file->get_pathname();
            $f->filename = $filename;
            $f->url = $url;
            return $f;
        }
    }

    return $url;
}
