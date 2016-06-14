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
 * Question behaviour type for deferred feedback with CBM behaviour.
 *
 * @package    qbehaviour_deferredcbm
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/../deferredfeedback/behaviourtype.php');


/**
 * Question behaviour type information for deferred feedback with CBM behaviour.
 *
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbehaviour_deferredcbm_type extends qbehaviour_deferredfeedback_type {
    public function adjust_random_guess_score($fraction) {
        return question_cbm::adjust_fraction($fraction, question_cbm::default_certainty());
    }

    public function summarise_usage(question_usage_by_activity $quba, question_display_options $options) {
        global $OUTPUT;
        $summarydata = parent::summarise_usage($quba, $options);

        if ($options->marks < question_display_options::MARK_AND_MAX) {
            return $summarydata;
        }

        // Prepare accumulators to hold the data we are about to collect.
        $notansweredcount  = 0;
        $notansweredweight = 0;
        $attemptcount = array(
            question_cbm::HIGH => 0,
            question_cbm::MED  => 0,
            question_cbm::LOW  => 0,
        );
        $totalweight = array(
            question_cbm::HIGH => 0,
            question_cbm::MED  => 0,
            question_cbm::LOW  => 0,
        );
        $totalrawscore = array(
            question_cbm::HIGH => 0,
            question_cbm::MED  => 0,
            question_cbm::LOW  => 0,
        );
        $totalcbmscore = array(
            question_cbm::HIGH => 0,
            question_cbm::MED  => 0,
            question_cbm::LOW  => 0,
        );

        // Loop through the data, and add it to the accumulators.
        foreach ($quba->get_attempt_iterator() as $qa) {
            if (strpos($qa->get_behaviour_name(), 'cbm') === false || $qa->get_max_mark() < 0.0000005) {
                continue;
            }

            $gradedstep = $qa->get_last_step_with_behaviour_var('_rawfraction');

            if (!$gradedstep->has_behaviour_var('_rawfraction')) {
                $notansweredcount  += 1;
                $notansweredweight += $qa->get_max_mark();
                continue;
            }

            $certainty = $qa->get_last_behaviour_var('certainty');
            if (is_null($certainty) || $certainty == -1) {
                // Certainty -1 has never been used in standard Moodle, but is
                // used in Tony-Gardiner Medwin's patches to mean 'No idea' which
                // we intend to implement: MDL-42077. In the mean time, avoid
                // errors for people who have used TGM's patches.
                $certainty = question_cbm::default_certainty();
            }

            $attemptcount[$certainty]  += 1;
            $totalweight[$certainty]   += $qa->get_max_mark();
            $totalrawscore[$certainty] += $qa->get_max_mark() * $gradedstep->get_behaviour_var('_rawfraction');
            $totalcbmscore[$certainty] += $qa->get_mark();
        }

        // Hence compute some statistics.
        $totalquestions   = $notansweredcount + array_sum($attemptcount);
        $grandtotalweight = $notansweredweight + array_sum($totalweight);
        $accuracy         = array_sum($totalrawscore) / $grandtotalweight;
        $averagecbm       = array_sum($totalcbmscore) / $grandtotalweight;
        $cbmbonus         = $this->calculate_bonus($averagecbm, $accuracy);
        $accuracyandbonus = $accuracy + $cbmbonus;

        // Add a note to explain the max mark.
        $summarydata['qbehaviour_cbm_grade_explanation'] = array(
            'title' => '',
            'content' => html_writer::tag('i', get_string('cbmgradeexplanation', 'qbehaviour_deferredcbm')) .
                    $OUTPUT->help_icon('cbmgrades', 'qbehaviour_deferredcbm'),
        );

        // Now we can start generating some of the summary: overall values.
        $summarydata['qbehaviour_cbm_entire_quiz_heading'] = array(
            'title' => '',
            'content' => html_writer::tag('h3',
                    get_string('forentirequiz', 'qbehaviour_deferredcbm', $totalquestions),
                    array('class' => 'qbehaviour_deferredcbm_summary_heading')),
        );
        $summarydata['qbehaviour_cbm_entire_quiz_cbm_average'] = array(
            'title' => get_string('averagecbmmark', 'qbehaviour_deferredcbm'),
            'content' => format_float($averagecbm, $options->markdp),
        );
        $summarydata['qbehaviour_cbm_entire_quiz_accuracy'] = array(
            'title' => get_string('accuracy', 'qbehaviour_deferredcbm'),
            'content' => $this->format_probability($accuracy, 1),
        );
        $summarydata['qbehaviour_cbm_entire_quiz_cbm_bonus'] = array(
            'title' => get_string('cbmbonus', 'qbehaviour_deferredcbm'),
            'content' => $this->format_probability($cbmbonus, 1),
        );
        $summarydata['qbehaviour_cbm_entire_quiz_accuracy_and_bonus'] = array(
            'title' => get_string('accuracyandbonus', 'qbehaviour_deferredcbm'),
            'content' => $this->format_probability($accuracyandbonus, 1),
        );

        if ($notansweredcount && array_sum($attemptcount) > 0) {
            $totalquestions   = array_sum($attemptcount);
            $grandtotalweight = array_sum($totalweight);
            $accuracy         = array_sum($totalrawscore) / $grandtotalweight;
            $averagecbm       = array_sum($totalcbmscore) / $grandtotalweight;
            $cbmbonus         = $this->calculate_bonus($averagecbm, $accuracy);
            $accuracyandbonus = $accuracy + $cbmbonus;

            $summarydata['qbehaviour_cbm_answered_quiz_heading'] = array(
                'title' => '',
                'content' => html_writer::tag('h3',
                        get_string('foransweredquestions', 'qbehaviour_deferredcbm', $totalquestions),
                        array('class' => 'qbehaviour_deferredcbm_summary_heading')),
            );
            $summarydata['qbehaviour_cbm_answered_quiz_cbm_average'] = array(
                'title' => get_string('averagecbmmark', 'qbehaviour_deferredcbm'),
                'content' => format_float($averagecbm, $options->markdp),
            );
            $summarydata['qbehaviour_cbm_answered_quiz_accuracy'] = array(
                'title' => get_string('accuracy', 'qbehaviour_deferredcbm'),
                'content' => $this->format_probability($accuracy, 1),
            );
            $summarydata['qbehaviour_cbm_answered_quiz_cbm_bonus'] = array(
                'title' => get_string('cbmbonus', 'qbehaviour_deferredcbm'),
                'content' => $this->format_probability($cbmbonus, 1),
            );
            $summarydata['qbehaviour_cbm_answered_quiz_accuracy_and_bonus'] = array(
                'title' => get_string('accuracyandbonus', 'qbehaviour_deferredcbm'),
                'content' => $this->format_probability($accuracyandbonus, 1),
            );
        }

        // Now per-certainty level values.
        $summarydata['qbehaviour_cbm_judgement_heading'] = array(
            'title' => '',
            'content' => html_writer::tag('h3', get_string('breakdownbycertainty', 'qbehaviour_deferredcbm'),
                    array('class' => 'qbehaviour_deferredcbm_summary_heading')),
        );

        foreach ($attemptcount as $certainty => $count) {
            $key   = 'qbehaviour_cbm_judgement' . $certainty;
            $title = question_cbm::get_short_string($certainty);

            if ($count == 0) {
                $summarydata[$key] = array(
                    'title' => $title,
                    'content' => get_string('noquestions', 'qbehaviour_deferredcbm'),
                );
                continue;
            }

            $lowerlimit = question_cbm::optimal_probablility_low($certainty);
            $upperlimit = question_cbm::optimal_probablility_high($certainty);
            $fraction = $totalrawscore[$certainty] / $totalweight[$certainty];

            $a = new stdClass();
            $a->responses = $count;
            $a->idealrangelow  = $this->format_probability($lowerlimit);
            $a->idealrangehigh = $this->format_probability($upperlimit);
            $a->fraction       = html_writer::tag('span', $this->format_probability($fraction),
                    array('class' => 'qbehaviour_deferredcbm_actual_percentage'));

            if ($fraction < $lowerlimit - 0.0000005) {
                if ((pow($fraction - $lowerlimit, 2) * $count) > 0.5) { // Rough indicator of significance: t > 1.5 or 1.8.
                    $judgement = 'overconfident';
                } else {
                    $judgement = 'slightlyoverconfident';
                }
            } else if ($fraction > $upperlimit + 0.0000005) {
                if ((pow($fraction - $upperlimit, 2) * $count) > 0.5) {
                    $judgement = 'underconfident';
                } else {
                    $judgement = 'slightlyunderconfident';
                }
            } else {
                $judgement = 'judgementok';
            }
            $a->judgement = html_writer::tag('span', get_string($judgement, 'qbehaviour_deferredcbm'),
                    array('class' => 'qbehaviour_deferredcbm_' . $judgement));

            $summarydata[$key] = array(
                'title' => $title,
                'content' => get_string('judgementsummary', 'qbehaviour_deferredcbm', $a),
            );
        }

        return $summarydata;
    }

    protected function format_probability($probability, $dp = 0) {
        return format_float($probability * 100, $dp) . '%';
    }

    public function calculate_bonus($total, $accuracy) {
        $expectedforaccuracy = max(
            $accuracy * question_cbm::adjust_fraction(1, question_cbm::LOW) +
                (1 - $accuracy) * question_cbm::adjust_fraction(0, question_cbm::LOW),
            $accuracy * question_cbm::adjust_fraction(1, question_cbm::MED) +
                (1 - $accuracy) * question_cbm::adjust_fraction(0, question_cbm::MED),
            $accuracy * question_cbm::adjust_fraction(1, question_cbm::HIGH) +
                (1 - $accuracy) * question_cbm::adjust_fraction(0, question_cbm::HIGH)
        );
        // The constant 0.1 here is determinted empirically from looking at lots
        // for CBM quiz results. See www.ucl.ac.uk/~ucgbarg/tea/IUPS_2013a.pdf.
        // It approximately maximises the reliability of accuracy + bonus.
        return 0.1 * ($total - $expectedforaccuracy);
    }
}
