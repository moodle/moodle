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

namespace mod_bigbluebuttonbn\local\exceptions;

use mod_bigbluebuttonbn\plugin;

/**
 * Class bigbluebutton_exception generic exception. This is supposed to be recoverable.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David  (laurent [at] call-learning [dt] fr)
 */
class bigbluebutton_exception extends \moodle_exception {
    /**
     * Constructor
     *
     * @param string $errorcode The name of the string from error.php to print
     * @param mixed $additionalinfo Extra words and phrases that might be required in the error string
     */
    public function __construct($errorcode, $additionalinfo = null) {
        parent::__construct($errorcode, plugin::COMPONENT, '', $additionalinfo);
    }

}
