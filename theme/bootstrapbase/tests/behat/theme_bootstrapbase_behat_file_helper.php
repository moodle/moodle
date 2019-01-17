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
 * Files interactions with behat overrides.
 *
 * @package    theme_bootstrapbase
 * @category   test
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/core_behat_file_helper.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Element\NodeElement as NodeElement;

/**
 * Files-related actions overrides.
 *
 * @package    theme_bootstrapbase
 * @category   test
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait theme_bootstrapbase_behat_file_helper {

    use core_behat_file_helper {
        core_behat_file_helper::get_filepicker_node as core_get_filepicker_node;
    }

    protected function get_filepicker_node($filepickerelement) {

        // More info about the problem (in case there is a problem).
        $exception = new ExpectationException('"' . $filepickerelement . '" filepicker can not be found', $this->getSession());

        // If no file picker label is mentioned take the first file picker from the page.
        if (empty($filepickerelement)) {
            $filepickercontainer = $this->find(
                    'xpath',
                    "//*[@data-fieldtype=\"filemanager\"]",
                    $exception
            );
        } else {
            // Gets the ffilemanager node specified by the locator which contains the filepicker container.
            $filepickerelement = behat_context_helper::escape($filepickerelement);
            $filepickercontainer = $this->find(
                    'xpath',
                    "//input[./@id = //label[normalize-space(.)=$filepickerelement]/@for]" .
                    '//ancestor::div[@data-fieldtype="filemanager" or @data-fieldtype="filepicker"]',
                    $exception
            );
        }

        return $filepickercontainer;
    }

}
