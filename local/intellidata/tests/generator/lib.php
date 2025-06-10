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
 * IntelliData data generator class.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/generators/tracking_generator.php');

/**
 * IntelliData data generator class.
 *
 * @package    local_intellidata
 * @copyright  2022 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @see    http://intelliboard.net/
 */
class local_intellidata_generator extends \component_generator_base {

    /**
     * @var number of created tracking.
     */
    protected $trackingcount = 0;

    /**
     * To be called from data reset code only,
     * do not use in tests.
     * @return void
     */
    public function reset() {
        $this->trackingcount = 0;
    }

    /**
     * Create tracking record.
     *
     * @param $data
     * @return \local_intellidata\persistent\tracking
     * @throws coding_exception
     */
    public function create_tracking($data = null) {
        $record = (new tracking_generator())->create($data);

        if ($record->get('id')) {
            $this->trackingcount++;
        }

        return $record->to_record();
    }
}
