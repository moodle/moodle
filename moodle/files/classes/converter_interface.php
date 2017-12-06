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
 * Class for converting files between different file formats.
 *
 * @package    core_files
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_files;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for converting files between different file formats.
 *
 * @package    docconvert_unoconv
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface converter_interface {

    /**
     * Whether the plugin is configured and requirements are met.
     *
     * Note: This function may be called frequently and caching is advisable.
     *
     * @return  bool
     */
    public static function are_requirements_met();

    /**
     * Convert a document to a new format and return a conversion object relating to the conversion in progress.
     *
     * @param   conversion $conversion The file to be converted
     * @return  $this
     */
    public function start_document_conversion(conversion $conversion);

    /**
     * Poll an existing conversion for status update.
     *
     * @param   conversion $conversion The file to be converted
     * @return  $this
     */
    public function poll_conversion_status(conversion $conversion);

    /**
     * Determine whether a conversion between the two supplied formats is achievable.
     *
     * Note: This function may be called frequently and caching is advisable.
     *
     * @param   string $from The source type
     * @param   string $to The destination type
     * @return  bool
     */
    public static function supports($from, $to);

    /**
     * A list of the supported conversions.
     *
     * Note: This information is only displayed to administrators.
     *
     * @return  string
     */
    public function get_supported_conversions();
}
