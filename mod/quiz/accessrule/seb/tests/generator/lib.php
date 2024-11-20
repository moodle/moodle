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
 * Data generator the quizaccess_seb plugin.
 *
 * @package    quizaccess_seb
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Data generator the quizaccess_seb plugin.
 *
 * @package    quizaccess_seb
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_seb_generator extends component_generator_base {

    /**
     * Create SEB template.
     *
     * @param array $data Template data.
     * @return \quizaccess_seb\template
     */
    public function create_template(array $data) {
        global $CFG;

        if (!isset($data['name'])) {
            $data['name'] = 'test';
        }

        if (!isset($data['content'])) {
            $data['content'] = file_get_contents(
                $CFG->dirroot . '/mod/quiz/accessrule/seb/tests/fixtures/unencrypted.seb'
            );
        }

        if (!isset($data['enabled'])) {
            $data['enabled'] = 1;
        }

        $template = new \quizaccess_seb\template();
        $template->set('content', $data['content']);
        $template->set('name', $data['name']);
        $template->set('enabled', $data['enabled']);
        $template->save();

        return $template;
    }

}
