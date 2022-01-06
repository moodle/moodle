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
 * Behat editpdfplus-related steps definitions.
 *
 * Steps definitions related with the editpdfplus.
 *
 * @package    assignfeedback_editpdfplus
 * @category   test
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/tests/behat/behat_assignfeedback_editpdf.php by Jerome Mouneyrac.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../../lib/behat/behat_base.php');

class behat_assignfeedback_editpdfplus extends behat_base {

    /**
     * Checks that Ghostscript is installed.
     *
     * @Given /^ghostscript is installed$/
     */
    public function ghostscript_is_installed() {
        $testpath = assignfeedback_editpdfplus\pdf::test_gs_path();
        if (!extension_loaded('zlib') or
                $testpath->status !== assignfeedback_editpdfplus\pdf::GSPATH_OK) {
            throw new \Moodle\BehatExtension\Exception\SkippedException;
        }
    }

    /**
     * Draw on the pdf.
     *
     * @When /^I draw on the pdf$/
     */
    public function i_draw_on_the_pdf() {
        $js = ' (function() {
    var instance = M.assignfeedback_editpdfplus.instance;
    var event = { clientX: 100, clientY: 250, preventDefault: function() {} };
    instance.edit_start(event);
}()); ';
        $this->getSession()->executeScript($js);
        sleep(1);
        $js = ' (function() {
    var instance = M.assignfeedback_editpdfplus.instance;
    var event = { clientX: 150, clientY: 275, preventDefault: function() {} };
    instance.edit_move(event);
}()); ';
        $this->getSession()->executeScript($js);
        sleep(1);
        $js = ' (function() {
    var instance = M.assignfeedback_editpdfplus.instance;
    var event = { clientX: 200, clientY: 300, preventDefault: function() {} };
    instance.edit_end(event);
}()); ';
        $this->getSession()->executeScript($js);
        sleep(1);
    }

    /**
     * I wait for all pages in the PDF document to be converted to images and loaded.
     *
     * @Given /^I wait for the complete PDF to load$/
     */
    public function i_wait_for_all_editpdf_pages_to_load() {
        // No need to wait if not running JS.
        if (!$this->running_javascript()) {
            return;
        }

        // Ensure that the document is ready, and all pages are loaded.
        $conditions = [
            'typeof M !== "undefined"',
            'typeof M.assignfeedback_editpdfplus !== "undefined"',
            'typeof M.assignfeedback_editpdfplus.instance !== "undefined"',
            'M.assignfeedback_editpdfplus.instance.documentstatus === 2',
            'M.assignfeedback_editpdfplus.instance.pagecount === M.assignfeedback_editpdfplus.instance.pages.length',
        ];
        $js = implode(' && ', $conditions);

        $this->getSession()->wait(self::TIMEOUT * 1000, "({$js})");
    }

}
