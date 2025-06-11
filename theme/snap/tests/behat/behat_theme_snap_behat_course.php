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
 * Overrides for behat course.
 * @copyright Copyright (c) 2017 Open LMS.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Element\NodeElement as NodeElement;

require_once(__DIR__ . '/../../../../course/tests/behat/behat_course.php');

/**
 * Overrides to make behat course steps work with Snap.
 *
 * @copyright Copyright (c) 2017 Open LMS.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_snap_behat_course extends behat_course {

    public function i_add_to_section($activity, $section) {

        if ($this->getSession()->getPage()->find('css', 'body#page-site-index') && (int)$section <= 1) {
            return parent::i_add_to_section($activity, $section);
        }
        $this->execute('behat_general::i_click_on',
            array("//li/a[@section-number='$section']", 'xpath_element'));

        $this->execute("behat_theme_snap_behat_course::i_open_the_activity_chooser_of_the_section", $section);
        $xpath = "(//div[@data-region]/a[div[contains(text(),'$activity')]])[1]";

        $node = $this->find('xpath', $xpath);
        $href = $node->getAttribute('href');
        $url = new moodle_url($href);
        $url->param('section', $section);
        $this->getSession()->visit($this->locate_path($url->out_as_local_url(false)));
    }

    /**
     * Open the activity chooser of the a section in a course.
     *
     * @Given /^I open the activity chooser of the section "(?P<section_number>\d+)"$/
     * @param int $section The section number
     * @throws ExpectationException
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function i_open_the_activity_chooser_of_the_section(int $section) {
        $this->execute('behat_general::i_click_on',
            array("//button[@data-action='open-chooser' and @data-sectionid='$section']", 'xpath_element'));

        $node = $this->get_selected_node('xpath_element', '//div[@data-region="modules"]');
        $this->ensure_node_is_visible($node);
    }
}
