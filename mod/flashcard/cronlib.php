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
 * Library of functions and constants for module flashcard
 * @package mod_flashcard
 * @category mod
 * @author Gustav Delius
 * @contributors Valery Fremaux
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/flashcard/lib.php');
require_once($CFG->dirroot.'/mod/flashcard/locallib.php');

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 * @uses $CFG
 *
 */
function flashcard_cron_task() {
    global $CFG, $DB, $SITE;

    // Get all flashcards.
    $flashcards = $DB->get_records('flashcard');

    foreach ($flashcards as $flashcard) {
        if ($flashcard->starttime != 0 && time() < $flashcard->starttime) {
            continue;
        }
        if ($flashcard->endtime != 0 && time() > $flashcard->endtime) {
            continue;
        }

        if ($flashcard->autodowngrade) {
            $cards = $DB->get_records_select('flashcard_card', 'flashcardid = ? AND deck > 1', array($flashcard->id));
            foreach ($cards as $card) {
                // Downgrades to deck 3 (middle low).
                if ($flashcard->decks > 3) {
                    $t = $card->lastaccessed + ($flashcard->deck4_delay * HOURSECS + $flashcard->deck4_release * HOURSECS);
                    if ($card->deck == 4 && time() > $t) {
                        $DB->set_field('flashcard_card', 'deck', 3, array('id' => $card->id));
                    }
                }
                // Downgrades to deck 2 (middle).
                if ($flashcard->decks > 2) {
                    $t = $card->lastaccessed + ($flashcard->deck3_delay * HOURSECS + $flashcard->deck3_release * HOURSECS);
                    if ($card->deck == 3 && time() > $t) {
                        $DB->set_field('flashcard_card', 'deck', 2, array('id' => $card->id));
                    }
                }
                // Downgrades to deck 1 (difficult).
                $t = $card->lastaccessed + ($flashcard->deck2_delay * HOURSECS + $flashcard->deck2_release * HOURSECS);
                if ($card->deck == 2 && time() > $t) {
                    $DB->set_field('flashcard_card', 'deck', 1, array('id' => $card->id));
                }
            }
        }

        if ($flashcard->remindusers) {
            if ($users = flashcard_get_participants($flashcard->id)) {
                // Restrict to real participants.

                $participants = count($users);
                mtrace("Participants : $participants users ");

                $voiduser = new StdClass();
                $voiduser->email = $CFG->noreplyaddress;
                $voiduser->firstname = '';
                $voiduser->lastname = '';
                $voiduser->id = 1;

                $coursename = $DB->get_field('course', 'fullname', array('id' => $flashcard->course));

                $notified = 0;

                foreach ($users as $u) {
                    $decks = flashcard_get_deck_status($flashcard, $u->id);
                    foreach ($decks->decks as $deck) {
                        if (!empty($deck->reactivate)) {
                            $params = array('userid' => $u->id, 'flashcardid' => $flashcard->id, 'deck' => $deck->deckid);
                            if ($state = $DB->get_record('flashcard_userdeck_state', $params)) {
                                if ($state->state) {
                                    continue;
                                }
                            } else {
                                $state = new StdClass();
                                $state->flashcardid = $flashcard->id;
                                $state->userid = $u->id;
                                $state->deck = $deck->deckid;
                                $DB->insert_record('flashcard_userdeck_state', $state);
                            }

                            $vars = array(
                                'FULLNAME' => fullname($u),
                                'COURSE' => format_string($coursename),
                                'URL' => $CFG->wwwroot.'/mod/flashcard/view.php?f='.$flashcard->id
                            );
                            $notification = flashcard_compile_mail_template('notifyreview', $vars, $u->lang);
                            $notificationhtml = flashcard_compile_mail_template('notifyreview_html', $vars, $u->lang);
                            if ($CFG->debugsmtp) {
                                mtrace("Sending Review Notification Mail Notification to ".fullname($u).'<br/>'.$notificationhtml);
                            }
                            $label = $SITE->shortname.':'.format_string($flashcard->name);
                            $subject = get_string('flashcardneedsreview', 'flashcard', $label);
                            email_to_user($u, $voiduser, $subject, $notification, $notificationhtml);

                            // Mark it has been sent.
                            $params = array('userid' => $u->id, 'flashcardid' => $flashcard->id, 'deck' => $deck->deckid);
                            $DB->set_field('flashcard_userdeck_state', 'state', 1, $params);
                            $notified++;
                        }
                    }
                }
                mtrace("Notified : $notified users ");
            }
        }
    }

    return true;
}
