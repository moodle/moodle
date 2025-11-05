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

namespace mod_board\local\form;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . "/formslib.php");

/**
 * Template file import form.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class template_import extends \moodleform {
    use \mod_board\local\ajax_form_trait;

    /**
     * Form definition.
     */
    protected function definition(): void {
        $mform = $this->_form;

        $mform->addElement(
            'filepicker',
            'importfile',
            get_string('file'),
            null,
            ['accepted_types' => ['.json']]
        );
        $mform->addRule('importfile', null, 'required');
    }

    #[\Override]
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        /** @var \stored_file[] $files */
        $files = $this->get_draft_files('importfile');
        if ($files) {
            $file = reset($files);
            $content = $file->get_content();
            $template = \mod_board\local\template::decode_import_file($content);
            if (!$template) {
                $errors['importfile'] = get_string('error');
            }
        } else {
            $errors['importfile'] = get_string('required');
        }

        return $errors;
    }
}
