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
 * A collection of all the question statistics calculated for an activity instance ie. the stats calculated for slots and
 * sub-questions and variants of those questions.
 *
 * @package    core_question
 * @copyright  2014 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\statistics\questions;

/**
 * A collection of all the question statistics calculated for an activity instance.
 *
 * @package    core_question
 * @copyright  2014 The Open University
 * @author     James Pratt me@jamiep.org
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class all_calculated_for_qubaid_condition {

    /** @var int Time after which statistics are automatically recomputed. */
    const TIME_TO_CACHE = 900; // 15 minutes.

    /**
     * The limit of rows of sub-question and variants rows to display on main page of report before switching to showing min,
     * median and max variants.
     */
    const SUBQ_AND_VARIANT_ROW_LIMIT = 10;

    /**
     * @var object[]
     */
    public $subquestions;

    /**
     * Holds slot (position) stats and stats for variants of questions in slots.
     *
     * @var calculated[]
     */
    public $questionstats = array();

    /**
     * Holds sub-question stats and stats for variants of subqs.
     *
     * @var calculated_for_subquestion[]
     */
    public $subquestionstats = array();

    /**
     * Set up a calculated_for_subquestion instance ready to store a randomly selected question's stats.
     *
     * @param object     $step
     * @param int|null   $variant Is this to keep track of a variant's stats? If so what is the variant, if not null.
     */
    public function initialise_for_subq($step, $variant = null) {
        $newsubqstat = new calculated_for_subquestion($step, $variant);
        if ($variant === null) {
            $this->subquestionstats[$step->questionid] = $newsubqstat;
        } else {
            $this->subquestionstats[$step->questionid]->variantstats[$variant] = $newsubqstat;
        }
    }

    /**
     * Set up a calculated instance ready to store a slot question's stats.
     *
     * @param int      $slot
     * @param object   $question
     * @param int|null $variant Is this to keep track of a variant's stats? If so what is the variant, if not null.
     */
    public function initialise_for_slot($slot, $question, $variant = null) {
        $newqstat = new calculated($question, $slot, $variant);
        if ($variant === null) {
            $this->questionstats[$slot] = $newqstat;
        } else {
            $this->questionstats[$slot]->variantstats[$variant] = $newqstat;
        }
    }

    /**
     * Reference for a item stats instance for a questionid and optional variant no.
     *
     * @param int  $questionid The id of the sub question.
     * @param int|null $variant if not null then we want the object to store a variant of a sub-question's stats.
     * @return calculated_for_subquestion|null null if the stats object does not yet exist.
     */
    public function for_subq($questionid, $variant = null) {
        if ($variant === null) {
            if (!isset($this->subquestionstats[$questionid])) {
                return null;
            } else {
                return $this->subquestionstats[$questionid];
            }
        } else {
            if (!isset($this->subquestionstats[$questionid]->variantstats[$variant])) {
                return null;
            } else {
                return $this->subquestionstats[$questionid]->variantstats[$variant];
            }
        }
    }

    /**
     * ids of all randomly selected question for all slots.
     *
     * @return int[] An array of all sub-question ids.
     */
    public function get_all_subq_ids() {
        return array_keys($this->subquestionstats);
    }

    /**
     * All slots nos that stats have been calculated for.
     *
     * @return int[] An array of all slot nos.
     */
    public function get_all_slots() {
        return array_keys($this->questionstats);
    }

    /**
     * Get position stats instance for a slot and optional variant no.
     *
     * @param int  $slot The slot no.
     * @param null $variant if provided then we want the object which stores a variant of a position's stats.
     * @return calculated|null An instance of the class storing the calculated position stats.
     */
    public function for_slot($slot, $variant = null) {
        if ($variant === null) {
            if (!isset($this->questionstats[$slot])) {
                return null;
            } else {
                return $this->questionstats[$slot];
            }
        } else {
            if (!isset($this->questionstats[$slot]->variantstats[$variant])) {
                return null;
            } else {
                return $this->questionstats[$slot]->variantstats[$variant];
            }
        }
    }

    /**
     * Load cached statistics from the database.
     *
     * @param \qubaid_condition $qubaids Which question usages to load stats for?
     */
    public function get_cached($qubaids) {
        global $DB;

        $timemodified = time() - self::TIME_TO_CACHE;
        $questionstatrecs = $DB->get_records_select('question_statistics', 'hashcode = ? AND timemodified > ?',
                                                    array($qubaids->get_hash_code(), $timemodified));

        $questionids = array();
        foreach ($questionstatrecs as $fromdb) {
            if (is_null($fromdb->variant) && !$fromdb->slot) {
                $questionids[] = $fromdb->questionid;
            }
        }
        $this->subquestions = question_load_questions($questionids);
        foreach ($questionstatrecs as $fromdb) {
            if (is_null($fromdb->variant)) {
                if ($fromdb->slot) {
                    $this->questionstats[$fromdb->slot]->populate_from_record($fromdb);
                    // Array created in constructor and populated from question.
                } else {
                    $this->subquestionstats[$fromdb->questionid] = new calculated_for_subquestion();
                    $this->subquestionstats[$fromdb->questionid]->populate_from_record($fromdb);
                    $this->subquestionstats[$fromdb->questionid]->question = $this->subquestions[$fromdb->questionid];
                }
            }
        }
        // Add cached variant stats to data structure.
        foreach ($questionstatrecs as $fromdb) {
            if (!is_null($fromdb->variant)) {
                if ($fromdb->slot) {
                    $newcalcinstance = new calculated();
                    $this->questionstats[$fromdb->slot]->variantstats[$fromdb->variant] = $newcalcinstance;
                    $newcalcinstance->question = $this->questionstats[$fromdb->slot]->question;
                } else {
                    $newcalcinstance = new calculated_for_subquestion();
                    $this->subquestionstats[$fromdb->questionid]->variantstats[$fromdb->variant] = $newcalcinstance;
                    $newcalcinstance->question = $this->subquestions[$fromdb->questionid];
                }
                $newcalcinstance->populate_from_record($fromdb);
            }
        }
    }

    /**
     * Find time of non-expired statistics in the database.
     *
     * @param \qubaid_condition $qubaids Which question usages to look for stats for?
     * @return int|bool Time of cached record that matches this qubaid_condition or false if non found.
     */
    public function get_last_calculated_time($qubaids) {
        global $DB;

        $timemodified = time() - self::TIME_TO_CACHE;
        return $DB->get_field_select('question_statistics', 'timemodified', 'hashcode = ? AND timemodified > ?',
                                     array($qubaids->get_hash_code(), $timemodified), IGNORE_MULTIPLE);
    }

    /**
     * Save stats to db.
     *
     * @param \qubaid_condition $qubaids Which question usages are we caching the stats of?
     */
    public function cache($qubaids) {
        foreach ($this->get_all_slots() as $slot) {
            $this->for_slot($slot)->cache($qubaids);
        }

        foreach ($this->get_all_subq_ids() as $subqid) {
            $this->for_subq($subqid)->cache($qubaids);
        }
    }

    /**
     * Return all sub-questions used.
     *
     * @return \object[] array of questions.
     */
    public function get_sub_questions() {
        return $this->subquestions;
    }

    /**
     * Return all stats for one slot, stats for the slot itself, and either :
     *  - variants of question
     *  - variants of randomly selected questions
     *  - randomly selected questions
     *
     * @param int      $slot          the slot no
     * @param bool|int $limitvariants limit number of variants and sub-questions displayed?
     * @return calculated|calculated_for_subquestion[] stats to display
     */
    public function structure_analysis_for_one_slot($slot, $limitvariants = false) {
        return array_merge(array($this->for_slot($slot)), $this->all_subq_and_variant_stats_for_slot($slot, $limitvariants));
    }

    /**
     * Call after calculations to output any error messages.
     *
     * @return string[] Array of strings describing error messages found during stats calculation.
     */
    public function any_error_messages() {
        $errors = array();
        foreach ($this->get_all_slots() as $slot) {
            foreach ($this->for_slot($slot)->get_sub_question_ids() as $subqid) {
                if ($this->for_subq($subqid)->differentweights) {
                    $name = $this->for_subq($subqid)->question->name;
                    $errors[] = get_string('erroritemappearsmorethanoncewithdifferentweight', 'question', $name);
                }
            }
        }
        return $errors;
    }

    /**
     * Are there too many rows of sub-questions and / or variant rows.
     *
     * @param array $rows the rows we intend to add.
     * @return bool Are there too many?
     */
    protected function too_many_subq_and_or_variant_rows($rows) {
        return (count($rows) > static::SUBQ_AND_VARIANT_ROW_LIMIT);
    }

    /**
     * From a number of calculated instances find the three instances with min, median and maximum facility index values.
     *
     * @param calculated[] $questionstats The stats from which to find the ones with minimum, median and maximum facility index.
     * @return calculated[] 3 stat objects with minimum, median and maximum facility index.
     */
    protected function find_min_median_and_max_facility_stats_objects($questionstats) {
        $facilities = array();
        foreach ($questionstats as $key => $questionstat) {
            $facilities[$key] = (float)$questionstat->facility;
        }
        asort($facilities);
        $facilitykeys = array_keys($facilities);
        $keyformin = $facilitykeys[0];
        $keyformedian = $facilitykeys[(int)(round(count($facilitykeys) / 2) - 1)];
        $keyformax = $facilitykeys[count($facilitykeys) - 1];
        $toreturn = array();
        foreach (array($keyformin => 'minimumfacility',
                       $keyformedian => 'medianfacility',
                       $keyformax => 'maximumfacility') as $key => $stringid) {
            $questionstats[$key]->minmedianmaxnotice = $stringid;
            $toreturn[] = $questionstats[$key];
        }
        return $toreturn;
    }

    /**
     * Return all stats for variants of question in slot $slot.
     *
     * @param int $slot The slot no.
     * @return calculated[] The instances storing the calculated stats.
     */
    protected function all_variant_stats_for_one_slot($slot) {
        $toreturn = array();
        foreach ($this->for_slot($slot)->get_variants() as $variant) {
            $toreturn[] = $this->for_slot($slot, $variant);
        }
        return $toreturn;
    }

    /**
     * Return all stats for variants of randomly selected questions for one slot $slot.
     *
     * @param int $slot The slot no.
     * @return calculated[] The instances storing the calculated stats.
     */
    protected function all_subq_variants_for_one_slot($slot) {
        $toreturn = array();
        $displayorder = 1;
        foreach ($this->for_slot($slot)->get_sub_question_ids() as $subqid) {
            if ($variants = $this->for_subq($subqid)->get_variants()) {
                foreach ($variants as $variant) {
                    $toreturn[] = $this->make_new_subq_stat_for($displayorder, $slot, $subqid, $variant);
                }
            }
            $displayorder++;
        }
        return $toreturn;
    }

    /**
     * Return all stats for randomly selected questions for one slot $slot.
     *
     * @param int $slot The slot no.
     * @return calculated[] The instances storing the calculated stats.
     */
    protected function all_subqs_for_one_slot($slot) {
        $displayorder = 1;
        $toreturn = array();
        foreach ($this->for_slot($slot)->get_sub_question_ids() as $subqid) {
            $toreturn[] = $this->make_new_subq_stat_for($displayorder, $slot, $subqid);
            $displayorder++;
        }
        return $toreturn;
    }

    /**
     * Return all variant or 'sub-question' stats one slot, either :
     *  - variants of question
     *  - variants of randomly selected questions
     *  - randomly selected questions
     *
     * @param int $slot the slot no
     * @param bool $limited limit number of variants and sub-questions displayed?
     * @return calculated|calculated_for_subquestion[] stats to display
     */
    protected function all_subq_and_variant_stats_for_slot($slot, $limited) {
        // Random question in this slot?
        if ($this->for_slot($slot)->get_sub_question_ids()) {
            if ($limited) {
                $subqvariantstats = $this->all_subq_variants_for_one_slot($slot);
                if ($this->too_many_subq_and_or_variant_rows($subqvariantstats)) {
                    // Too many variants from randomly selected questions.
                    return $this->find_min_median_and_max_facility_stats_objects($subqvariantstats);
                }
                $subqstats = $this->all_subqs_for_one_slot($slot);
                if ($this->too_many_subq_and_or_variant_rows($subqstats)) {
                    // Too many randomly selected questions.
                    return $this->find_min_median_and_max_facility_stats_objects($subqstats);
                }
            }
            $toreturn = array();
            $displaynumber = 1;
            foreach ($this->for_slot($slot)->get_sub_question_ids() as $subqid) {
                $toreturn[] = $this->make_new_subq_stat_for($displaynumber, $slot, $subqid);
                if ($variants = $this->for_subq($subqid)->get_variants()) {
                    foreach ($variants as $variant) {
                        $toreturn[] = $this->make_new_subq_stat_for($displaynumber, $slot, $subqid, $variant);
                    }
                }
                $displaynumber++;
            }
            return $toreturn;
        } else {
            $variantstats = $this->all_variant_stats_for_one_slot($slot);
            if ($limited && $this->too_many_subq_and_or_variant_rows($variantstats)) {
                return $this->find_min_median_and_max_facility_stats_objects($variantstats);
            } else {
                return $variantstats;
            }
        }
    }

    /**
     * We need a new object for display. Sub-question stats can appear more than once in different slots.
     * So we create a clone of the object and then we can set properties on the object that are per slot.
     *
     * @param int  $displaynumber                   The display number for this sub question.
     * @param int  $slot                            The slot number.
     * @param int  $subqid                          The sub question id.
     * @param null|int $variant                     The variant no.
     * @return calculated_for_subquestion           The object for display.
     */
    protected function make_new_subq_stat_for($displaynumber, $slot, $subqid, $variant = null) {
        $slotstat = fullclone($this->for_subq($subqid, $variant));
        $slotstat->question->number = $this->for_slot($slot)->question->number;
        $slotstat->subqdisplayorder = $displaynumber;
        return $slotstat;
    }
}
