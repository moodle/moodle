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
 * Abstract assessable uploaded event.
 *
 * @package    core
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\event;

defined('MOODLE_INTERNAL') || die();

/**
 * Abstract assessable uploaded event class.
 *
 * This class has to be extended by any event which represent that some content,
 * on which someone will be assessed, has been uploaded. This is different
 * than other events such as assessable_submitted, which means that the content
 * has been submitted and made available for grading.
 *
 * Both events could be triggered in a row, first the uploaded, then the submitted.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - array pathnamehashes: uploaded files path name hashes.
 *      - string content: the content.
 * }
 *
 * @package    core
 * @since      Moodle 2.6
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class assessable_uploaded extends base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Validation that should be shared among child classes.
     *
     * @throws \coding_exception when validation fails.
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        if ($this->contextlevel != CONTEXT_MODULE) {
            throw new \coding_exception('Context level must be CONTEXT_MODULE.');
        } else if (!isset($this->other['pathnamehashes']) || !is_array($this->other['pathnamehashes'])) {
            throw new \coding_exception('The \'pathnamehashes\' value must be set in other and must be an array.');
        } else if (!isset($this->other['content']) || !is_string($this->other['content'])) {
            throw new \coding_exception('The \'content\' value must be set in other and must be a string.');
        }
    }

    public static function get_other_mapping() {
        // Nothing to map.
        return false;
    }
}
