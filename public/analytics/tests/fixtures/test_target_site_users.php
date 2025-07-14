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
 * Test target.
 *
 * @package   core_analytics
 * @copyright 2018 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/test_site_users_analyser.php');

/**
 * Test target.
 *
 * @package   core_analytics
 * @copyright 2018 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_target_site_users extends \core_analytics\local\target\binary {

    /**
     * Returns a lang_string object representing the name for the indicator.
     *
     * Used as column identificator.
     *
     * If there is a corresponding '_help' string this will be shown as well.
     *
     * @return \lang_string
     */
    public static function get_name(): \lang_string {
        // Using a string that exists and contains a corresponding '_help' string.
        return new \lang_string('adminhelplogs');
    }

    /**
     * predictions
     *
     * @var array
     */
    protected $predictions = array();

    /**
     * get_analyser_class
     *
     * @return string
     */
    public function get_analyser_class() {
        return 'test_site_users_analyser';
    }

    /**
     * Everything yep, this is just for testing.
     *
     * @param  \core_analytics\local\time_splitting\base $timesplitting
     * @return bool
     */
    public function can_use_timesplitting(\core_analytics\local\time_splitting\base $timesplitting): bool {
        return true;
    }

    /**
     * classes_description
     *
     * @return string[]
     */
    public static function classes_description() {
        return array(
            'firstname first char is A',
            'firstname first char is not A'
        );
    }

    /**
     * We don't want to discard results.
     * @return float
     */
    protected function min_prediction_score() {
        return null;
    }

    /**
     * We don't want to discard results.
     * @return array
     */
    public function ignored_predicted_classes() {
        return array();
    }

    /**
     * is_valid_analysable
     *
     * @param \core_analytics\analysable $analysable
     * @param bool $fortraining
     * @return bool
     */
    public function is_valid_analysable(\core_analytics\analysable $analysable, $fortraining = true) {
        // This is testing, let's make things easy.
        return true;
    }

    /**
     * is_valid_sample
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $analysable
     * @param bool $fortraining
     * @return bool
     */
    public function is_valid_sample($sampleid, \core_analytics\analysable $analysable, $fortraining = true) {
        // We skip not-visible courses during training as a way to emulate the training data / prediction data difference.
        // In normal circumstances is_valid_sample will return false when they receive a sample that can not be
        // processed.
        if (!$fortraining) {
            return true;
        }

        $sample = $this->retrieve('user', $sampleid);
        if ($sample->lastname == 'b') {
            return false;
        }
        return true;
    }

    /**
     * calculate_sample
     *
     * @param int $sampleid
     * @param \core_analytics\analysable $analysable
     * @param int $starttime
     * @param int $endtime
     * @return float
     */
    protected function calculate_sample($sampleid, \core_analytics\analysable $analysable, $starttime = false, $endtime = false) {

        $sample = $this->retrieve('user', $sampleid);

        $firstchar = substr($sample->firstname, 0, 1);
        if ($firstchar === 'a') {
            return 1;
        } else {
            return 0;
        }
    }
}
