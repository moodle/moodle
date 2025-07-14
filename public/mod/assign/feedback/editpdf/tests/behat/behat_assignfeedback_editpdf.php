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
 * Behat editpdf-related steps definitions.
 *
 * @package    assignfeedback_editpdf
 * @category   test
 * @copyright  2013 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../../lib/behat/behat_base.php');

/**
 * Steps definitions related with the editpdf.
 *
 * @package    assignfeedback_editpdf
 * @category   test
 * @copyright  2013 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_assignfeedback_editpdf extends behat_base {

    /**
     * Checks that Ghostscript is installed.
     *
     * @Given /^ghostscript is installed$/
     */
    public function ghostscript_is_installed() {
        $testpath = assignfeedback_editpdf\pdf::test_gs_path();
        if (!extension_loaded('zlib') or
            $testpath->status !== assignfeedback_editpdf\pdf::GSPATH_OK) {
            throw new \Moodle\BehatExtension\Exception\SkippedException;
        }
    }

    /**
     * Draw on the pdf.
     *
     * @When /^I draw on the pdf$/
     */
    public function i_draw_on_the_pdf() {
        // There appears to be a bug with detecting changes to
        // annotations. If a PDF is annotated, then the student
        // updates the submission, if the teacher then draws the
        // exact same annotations on the new PDF, the readonly
        // pages are not updated and the student's view of the
        // annotated PDF still shows the old PDF. So we add some
        // randomness in this step to ensure the annotations are
        // different every time.
        //
        // This was added to test MDL-75898. See MDL-76659 for
        // more details about the bug.
        //
        // Note - the start, move and end locations must all be different.
        // If they are the same, it's possible the PDF tool selected does not activate.
        $startx = 100 + rand(0, 50);
        $starty = 250 + rand(0, 50);
        $js = ' (function() {
    var instance = M.assignfeedback_editpdf.instance;
    var event = { clientX: ' . $startx . ', clientY: ' . $starty . ', preventDefault: function() {} };
    instance.edit_start(event);
}()); ';
        $this->execute_script($js);
        sleep(1);

        // Move slightly in one direction.
        $movex = $startx + 50;
        $movey = $starty + 30;
        $js = ' (function() {
    var instance = M.assignfeedback_editpdf.instance;
    var event = { clientX: ' . $movex . ', clientY: ' . $movey . ', preventDefault: function() {} };
    instance.edit_move(event);
}()); ';
        $this->execute_script($js);
        sleep(1);

        // Move a little further to stop.
        $endx = $movex + 15;
        $endy = $movey + 15;
        $js = ' (function() {
    var instance = M.assignfeedback_editpdf.instance;
    var event = { clientX: ' . $endx . ', clientY: ' . $endy . ', preventDefault: function() {} };
    instance.edit_end(event);
}()); ';
        $this->execute_script($js);
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
            'typeof M.assignfeedback_editpdf !== "undefined"',
            'typeof M.assignfeedback_editpdf.instance !== "undefined"',
            'M.assignfeedback_editpdf.instance.documentstatus === 2',
            'M.assignfeedback_editpdf.instance.pagecount === M.assignfeedback_editpdf.instance.pages.length',
        ];
        $js = implode(' && ', $conditions);

        $this->getSession()->wait(self::get_timeout() * 1000, "({$js})");
    }
}
