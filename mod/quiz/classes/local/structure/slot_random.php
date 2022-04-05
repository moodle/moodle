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

namespace mod_quiz\local\structure;

/**
 * Class slot_random, represents a random question slot type.
 *
 * @package    mod_quiz
 * @copyright  2018 Shamim Rezaie <shamim@moodle.com>
 * @author     2021 Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slot_random {

    /** @var \stdClass Slot's properties. A record retrieved from the quiz_slots table. */
    protected $record;

    /**
     * @var \stdClass set reference record
     */
    protected $referencerecord;

    /**
     * @var \stdClass The quiz this question slot belongs to.
     */
    protected $quiz = null;

    /**
     * @var \core_tag_tag[] List of tags for this slot.
     */
    protected $tags = [];

    /**
     * @var string filter condition
     */
    protected $filtercondition = null;

    /**
     * slot_random constructor.
     *
     * @param \stdClass $slotrecord Represents a record in the quiz_slots table.
     */
    public function __construct($slotrecord = null) {
        $this->record = new \stdClass();
        $this->referencerecord = new \stdClass();

        $slotproperties = ['id', 'slot', 'quizid', 'page', 'requireprevious', 'maxmark'];
        $setreferenceproperties = ['usingcontextid', 'questionscontextid'];

        foreach ($slotproperties as $property) {
            if (isset($slotrecord->$property)) {
                $this->record->$property = $slotrecord->$property;
            }
        }

        foreach ($setreferenceproperties as $referenceproperty) {
            if (isset($slotrecord->$referenceproperty)) {
                $this->referencerecord->$referenceproperty = $slotrecord->$referenceproperty;
            }
        }
    }

    /**
     * Returns the quiz for this question slot.
     * The quiz is fetched the first time it is requested and then stored in a member variable to be returned each subsequent time.
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function get_quiz() {
        global $DB;

        if (empty($this->quiz)) {
            if (empty($this->record->quizid)) {
                throw new \coding_exception('quizid is not set.');
            }
            $this->quiz = $DB->get_record('quiz', array('id' => $this->record->quizid));
        }

        return $this->quiz;
    }

    /**
     * Sets the quiz object for the quiz slot.
     * It is not mandatory to set the quiz as the quiz slot can fetch it the first time it is accessed,
     * however it helps with the performance to set the quiz if you already have it.
     *
     * @param \stdClass $quiz The qui object.
     */
    public function set_quiz($quiz) {
        $this->quiz = $quiz;
        $this->record->quizid = $quiz->id;
    }

    /**
     * Set some tags for this quiz slot.
     *
     * @param \core_tag_tag[] $tags
     */
    public function set_tags($tags) {
        $this->tags = [];
        foreach ($tags as $tag) {
            // We use $tag->id as the key for the array so not only it handles duplicates of the same tag being given,
            // but also it is consistent with the behaviour of set_tags_by_id() below.
            $this->tags[$tag->id] = $tag;
        }
    }

    /**
     * Set some tags for this quiz slot. This function uses tag ids to find tags.
     *
     * @param int[] $tagids
     */
    public function set_tags_by_id($tagids) {
        $this->tags = \core_tag_tag::get_bulk($tagids, 'id, name');
    }

    /**
     * Set filter condition.
     *
     * @param \stdClass $filters
     */
    public function set_filter_condition($filters) {
        if (!empty($this->tags)) {
            $filters->tags = $this->tags;
        }

        $this->filtercondition = json_encode($filters);
    }

    /**
     * Inserts the quiz slot at the $page page.
     * It is required to call this function if you are building a quiz slot object from scratch.
     *
     * @param int $page The page that this slot will be inserted at.
     */
    public function insert($page) {
        global $DB;

        $slots = $DB->get_records('quiz_slots', array('quizid' => $this->record->quizid),
                'slot', 'id, slot, page');
        $quiz = $this->get_quiz();

        $trans = $DB->start_delegated_transaction();

        $maxpage = 1;
        $numonlastpage = 0;
        foreach ($slots as $slot) {
            if ($slot->page > $maxpage) {
                $maxpage = $slot->page;
                $numonlastpage = 1;
            } else {
                $numonlastpage += 1;
            }
        }

        if (is_int($page) && $page >= 1) {
            // Adding on a given page.
            $lastslotbefore = 0;
            foreach (array_reverse($slots) as $otherslot) {
                if ($otherslot->page > $page) {
                    $DB->set_field('quiz_slots', 'slot', $otherslot->slot + 1, array('id' => $otherslot->id));
                } else {
                    $lastslotbefore = $otherslot->slot;
                    break;
                }
            }
            $this->record->slot = $lastslotbefore + 1;
            $this->record->page = min($page, $maxpage + 1);

            quiz_update_section_firstslots($this->record->quizid, 1, max($lastslotbefore, 1));
        } else {
            $lastslot = end($slots);
            if ($lastslot) {
                $this->record->slot = $lastslot->slot + 1;
            } else {
                $this->record->slot = 1;
            }
            if ($quiz->questionsperpage && $numonlastpage >= $quiz->questionsperpage) {
                $this->record->page = $maxpage + 1;
            } else {
                $this->record->page = $maxpage;
            }
        }

        $this->record->id = $DB->insert_record('quiz_slots', $this->record);

        $this->referencerecord->component = 'mod_quiz';
        $this->referencerecord->questionarea = 'slot';
        $this->referencerecord->itemid = $this->record->id;
        $this->referencerecord->filtercondition = $this->filtercondition;
        $DB->insert_record('question_set_references', $this->referencerecord);

        $trans->allow_commit();

        // Log slot created event.
        $cm = get_coursemodule_from_instance('quiz', $quiz->id);
        $event = \mod_quiz\event\slot_created::create([
            'context' => \context_module::instance($cm->id),
            'objectid' => $this->record->id,
            'other' => [
                'quizid' => $quiz->id,
                'slotnumber' => $this->record->slot,
                'page' => $this->record->page
            ]
        ]);
        $event->trigger();
    }
}
