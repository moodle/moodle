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
 * Override definitions for the upload repository type for the Adaptable theme.
 *
 * @package    theme_adaptable
 * @category   test
 * @copyright  2019 Michael Hawkins (copied from theme_clasic)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../repository/upload/tests/behat/behat_repository_upload.php');

use Behat\Mink\Exception\ExpectationException;

/**
 * Override definitions for the upload repository type for the Adaptable theme.
 *
 * @package    theme_adaptable
 * @category   test
 * @copyright  2019 Michael Hawkins (copied from theme_clasic)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class behat_theme_adaptable_behat_repository_upload extends behat_repository_upload {
    /**
     * Gets the NodeElement for filepicker of filemanager moodleform element.
     *
     * @throws ExpectationException
     * @param  string $filepickerelement The filepicker form field label
     * @return NodeElement The hidden element node.
     */
    protected function get_filepicker_node($filepickerelement) {

        // More info about the problem (in case there is a problem).
        $exception = new ExpectationException('"' . $filepickerelement . '" filepicker can not be found', $this->getSession());

        // If no file picker label is mentioned take the first file picker from the page.
        if (empty($filepickerelement)) {
            $filepickercontainer = $this->find(
                'xpath',
                "//*[@class=\"form-filemanager\"]",
                $exception
            );
        } else {
            // Gets the filemanager node specified by the locator which contains the filepicker container.
            $filepickerelement = behat_context_helper::escape($filepickerelement);
            $filepickercontainer = $this->find(
                'xpath',
                "//input[./@id = substring-before(//p[normalize-space(.)=$filepickerelement]/@id, '_label')]" .
                    "//ancestor::div[contains(concat(' ', normalize-space(@class), ' '), ' felement ')]",
                $exception
            );
        }

        return $filepickercontainer;
    }
}
