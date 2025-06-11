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
 * Overrides for behat forms. Modified from core behat_forms.
 *
 * @copyright  2012 David Monllaó
 * @copyright Copyright (c) 2018 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException,
    Behat\Mink\Element\NodeElement as NodeElement;

require_once(__DIR__ . '/../../../../lib/tests/behat/behat_forms.php');

/**
 * Overrides to make behat forms steps work with Snap.
 *
 * @copyright  2012 David Monllaó
 * @copyright Copyright (c) 2018 Open LMS
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_snap_behat_forms extends behat_forms {
    protected function expand_all_fields() {
        // Expand only if JS mode, else not needed.
        if (!$this->running_javascript()) {
            return;
        }

        // We already know that we waited for the DOM and the JS to be loaded, even the editor
        // so, we will use the reduced timeout as it is a common task and we should save time.
        try {
            $this->wait_for_pending_js();
            // Expand all fieldsets link - which will only be there if there is more than one collapsible section.
            $expandallxpath = "//div[@class='collapsible-actions']" .
                "//a[contains(concat(' ', @class, ' '), ' collapsed ')]" .
                "//span[contains(concat(' ', @class, ' '), ' expandall ')]";

            $collapseexpandlink = $this->find('xpath', $expandallxpath,
                false, false, behat_base::get_reduced_timeout());
            $collapseexpandlink->click();
            $this->wait_for_pending_js();
        } catch (ElementNotFoundException $e) {
            // Try explanding only one section.
            try {
                $expandonlysection = "//legend[@class='ftoggler']" .
                    "//a[contains(concat(' ', @class, ' '), ' icons-collapse-expand ') and @aria-expanded = 'false']";

                $collapseexpandlink = $this->find('xpath', $expandonlysection,
                    false, false, behat_base::get_reduced_timeout());
                $collapseexpandlink->click();
                // @codingStandardsIgnoreStart
            } catch (Exception $e) {
                // The behat_base::find() method throws an exception if there are no elements,
                // we should not fail a test because of this. We continue if there are not expandable fields.

            }
            // @codingStandardsIgnoreEnd
        }

        // Different try & catch as we can have expanded fieldsets with advanced fields on them.
        try {

            // Expand all fields xpath.
            $showmorexpath = "//a[normalize-space(.)='" . get_string('showmore', 'form') . "']" .
                "[contains(concat(' ', normalize-space(@class), ' '), ' moreless-toggler')]";

            // We don't wait here as we already waited when getting the expand fieldsets links.
            if (!$showmores = $this->getSession()->getPage()->findAll('xpath', $showmorexpath)) {
                return;
            }

            if ($this->getSession()->getDriver() instanceof \DMore\ChromeDriver\ChromeDriver) {
                // Chrome Driver produces unique xpaths for each element.
                foreach ($showmores as $showmore) {
                    $showmore->click();
                }
            } else {
                // Funny thing about this, with findAll() we specify a pattern and each element matching the pattern
                // is added to the array with of xpaths with a [0], [1]... sufix, but when we click on an element it
                // does not matches the specified xpath anymore (now is a "Show less..." link) so [1] becomes [0],
                // that's why we always click on the first XPath match, will be always the next one.
                $iterations = count($showmores);
                for ($i = 0; $i < $iterations; $i++) {
                    $showmores[0]->click();
                }
            }
            // @codingStandardsIgnoreStart
        } catch (ElementNotFoundException $e) {
            // We continue with the test.
        }
        // @codingStandardsIgnoreEnd
    }
}
