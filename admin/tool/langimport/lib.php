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
 * Utility lang import functions.
 *
 * @package    tool
 * @subpackage langimport
 * @copyright  2011 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Called during upgrade, we need up-to-date lang pack
 * because it may be used during upgrade...
 *
 * @param string $lang
 * @return void
 */
function tool_langimport_preupgrade_update($lang) {
    global $CFG, $OUTPUT;
    require_once($CFG->libdir.'/componentlib.class.php');

    echo $OUTPUT->heading(get_string('langimport', 'tool_langimport').': '.$lang);

    @mkdir ($CFG->tempdir.'/');    //make it in case it's a fresh install, it might not be there
    @mkdir ($CFG->dataroot.'/lang/');

    $installer = new lang_installer($lang);
    $results = $installer->run();
    foreach ($results as $langcode => $langstatus) {
        switch ($langstatus) {
        case lang_installer::RESULT_DOWNLOADERROR:
            echo $OUTPUT->notification($langcode . '.zip');
            break;
        case lang_installer::RESULT_INSTALLED:
            echo $OUTPUT->notification(get_string('langpackinstalled', 'tool_langimport', $langcode), 'notifysuccess');
            break;
        case lang_installer::RESULT_UPTODATE:
            echo $OUTPUT->notification(get_string('langpackuptodate', 'tool_langimport', $langcode), 'notifysuccess');
            break;
        }
    }
}
