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
 * Fixture script for Behat test for testing the filetypes element.
 *
 * @package     core_form
 * @category    test
 * @copyright   2017 David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No login check is expected here as this is for Behat tests only.
// @codingStandardsIgnoreLine
require(__DIR__.'/../../../../config.php');
require_once($CFG->libdir.'/formslib.php');

// Behat test fixture only.
defined('BEHAT_SITE_RUNNING') || die('Only available on Behat test server');

/**
 * Defines a test form to be used in automatic tests.
 *
 * @copyright 2017 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_form extends moodleform {

    /**
     * Defines the form fields.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('filetypes', 'filetypes0', 'Choose from all file types');
        $mform->setDefault('filetypes0', '.pdf');

        $mform->addElement('filetypes', 'filetypes1', 'Choose from a limited set',
            ['onlytypes' => array('.pdf', 'web_image', 'image')]);

        $mform->addElement('filetypes', 'filetypes2', 'Choose without "all"',
            ['allowall' => false]);

        $mform->addElement('filetypes', 'filetypes3', 'Unknown file types are allowed here',
            ['allowunknown' => true]);

        $mform->addElement('filemanager', 'fileman1', 'Picky file manager', null, ['accepted_types' => '.txt']);

        $this->add_action_buttons(false);
    }
}

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/form/tests/fixtures/filetypes.php');
$PAGE->set_title('Filetypes element test page');

$form = new test_form($PAGE->url);
$formdata = $form->get_data();

echo $OUTPUT->header();
echo $OUTPUT->heading($PAGE->title);
$form->display();
echo $OUTPUT->footer();