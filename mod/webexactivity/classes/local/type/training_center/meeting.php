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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_webexactivity\local\type\training_center;

defined('MOODLE_INTERNAL') || die();

/**
 * Class that represents and controls a Training Center meeting instance.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class meeting extends \mod_webexactivity\local\type\base\meeting {

    /**
     * The XML generator class name to use.
     */
    const GENERATOR = '\mod_webexactivity\local\type\training_center\xml_gen';

    /**
     * Prefix for retrieved XML fields.
     */
    const XML_PREFIX = 'train';

    /**
     * The meetings type.
     */
    const TYPE = \mod_webexactivity\webex::WEBEXACTIVITY_TYPE_TRAINING;

    /**
     * The meetings type code.
     */
    const TYPE_CODE = 'TC';

    /**
     * Builds the meeting object.
     *
     * @param stdClass|int    $meeting Object of meeting record, or id of record to load.
     */
    public function __construct($meeting = false) {
        parent::__construct($meeting);

        if (!isset($this->type)) {
            $this->type = static::TYPE;
        }
    }

    /**
     * Process a response from WebEx into the meeting.
     *
     * @param array    $response XML array of the response from WebEx for meeting information.
     */
    protected function process_response($response) {
        if (!parent::process_response($response)) {
            return false;
        }

        if (empty($response)) {
            return true;
        }

        $prefix = static::XML_PREFIX;

        // Type specific code goes here.
        if (isset($response[$prefix.':sessionkey']['0']['#'])) {
            $this->meetingkey = $response[$prefix.':sessionkey']['0']['#'];
        }

        return true;
    }

}
