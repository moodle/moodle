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
 * ClamAV antivirus integration.
 *
 * @package    core
 * @subpackage antivirus_clamav
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Class implemeting ClamAV antivirus.
 * @copyright  2015 Ruslan Kabalin, Lancaster University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class antivirus_clamav extends antivirus {
    /**
     * Are the necessary antivirus settings configured?
     *
     * @return bool True if all necessary config settings been entered
     */
    public function is_configured() {
        return !empty($this->config->pathtoclam);
    }
    /**
     * Scan file, throws exception in case of infected file.
     *
     * Please note that the scanning engine must be able to access the file,
     * permissions of the file are not modified here!
     *
     * @param string $file Full path to the file.
     * @param bool $deleteinfected whether infected file needs to be deleted.
     * @throws moodle_exception If file is infected.
     * @return void
     */
    public function scan_file($file, $deleteinfected) {
        // Here goes the scanning code.
    }
}
