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
 * Provides {@link testable_code_manager} class.
 *
 * @package     core_plugin
 * @category    test
 * @copyright   2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\update;

defined('MOODLE_INTERNAL') || die();

/**
 * Testable variant of \core\update\code_manager class.
 *
 * Provides access to some protected methods we want to explicitly test and
 * bypass the actual cURL calls by providing fake responses.
 *
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_code_manager extends code_manager {

    /** @var int how many times $this->download_file_content() was called */
    public $downloadscounter = 0;

    /**
     * Fake method to simulate fetching file via cURL.
     *
     * It simply creates a new file in the given location, the contents of
     * which is the URL itself.
     *
     * @param string $url URL to the file
     * @param string $tofile full path to where to store the downloaded file
     * @return bool
     */
    protected function download_file_content($url, $tofile) {
        $this->downloadscounter++;
        file_put_contents($tofile, $url);
        return true;
    }
}
