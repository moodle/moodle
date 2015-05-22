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
 * A {@link \question_variant_selection_strategy} that randomly selects variants that were not used yet.
 *
 * @package   core_question
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace core_question\engine\variants;
defined('MOODLE_INTERNAL') || die();


/**
 * A {@link \question_variant_selection_strategy} that randomly selects variants that were not used yet.
 *
 * If all variants have been used at least once in the set of usages under
 * consideration, then then it picks one of the least often used.
 *
 * Within one particular use of this class, each seed will always select the
 * same variant. This is so that shared datasets work in calculated questions,
 * and similar features in question types like varnumeric and STACK.
 *
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class least_used_strategy implements \question_variant_selection_strategy {

    /** @var array seed => variant number => number of uses. */
    protected $variantsusecounts = array();

    /** @var array seed => variant number. */
    protected $selectedvariant = array();

    /**
     * Constructor.
     * @param question_usage_by_activity $quba the question usage we will be picking variants for.
     * @param qubaid_condition $qubaids ids of the usages to consider when counting previous uses of each variant.
     */
    public function __construct(\question_usage_by_activity $quba, \qubaid_condition $qubaids) {
        $questionidtoseed = array();
        foreach ($quba->get_attempt_iterator() as $qa) {
            $question = $qa->get_question();
            if ($question->get_num_variants() > 1) {
                $questionidtoseed[$question->id] = $question->get_variants_selection_seed();
            }
        }

        if (empty($questionidtoseed)) {
            return;
        }

        $this->variantsusecounts = array_fill_keys($questionidtoseed, array());

        $variantsused = \question_engine::load_used_variants(array_keys($questionidtoseed), $qubaids);
        foreach ($variantsused as $questionid => $usagecounts) {
            $seed = $questionidtoseed[$questionid];
            foreach ($usagecounts as $variant => $count) {
                if (isset($this->variantsusecounts[$seed][$variant])) {
                    $this->variantsusecounts[$seed][$variant] += $count;
                } else {
                    $this->variantsusecounts[$seed][$variant] = $count;
                }
            }
        }
    }

    public function choose_variant($maxvariants, $seed) {
        if ($maxvariants == 1) {
            return 1;
        }

        if (isset($this->selectedvariant[$seed])) {
            return $this->selectedvariant[$seed];
        }

        if ($maxvariants > 2 * count($this->variantsusecounts[$seed])) {
            // Many many more variants exist than have been used so far.
            // It will be quicker to just pick until we miss a collision.
            do {
                $variant = rand(1, $maxvariants);
            } while (isset($this->variantsusecounts[$seed][$variant]));

        } else {
            // We need to work harder to find a least-used one.
            $leastusedvariants = array();
            for ($variant = 1; $variant <= $maxvariants; ++$variant) {
                if (!isset($this->variantsusecounts[$seed][$variant])) {
                    $leastusedvariants[$variant] = 1;
                }
            }
            if (empty($leastusedvariants)) {
                // All variants used at least once, try again.
                $leastuses = min($this->variantsusecounts[$seed]);
                foreach ($this->variantsusecounts[$seed] as $variant => $uses) {
                    if ($uses == $leastuses) {
                        $leastusedvariants[$variant] = 1;
                    }
                }
            }
            $variant = array_rand($leastusedvariants);
        }

        $this->selectedvariant[$seed] = $variant;
        if (isset($variantsusecounts[$seed][$variant])) {
            $variantsusecounts[$seed][$variant] += 1;
        } else {
            $variantsusecounts[$seed][$variant] = 1;
        }
        return $variant;
    }
}
