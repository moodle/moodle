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
 * Entity model representing template settings for the seb plugin.
 *
 * @package    quizaccess_seb
 * @author     Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2020 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quizaccess_seb;

use core\persistent;

defined('MOODLE_INTERNAL') || die();

/**
 * Entity model representing template settings for the seb plugin.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class template extends persistent {

    /** Table name for the persistent. */
    const TABLE = 'quizaccess_seb_template';

    /** @var property_list $plist The SEB config represented as a Property List object. */
    private $plist;

    /**
     * Return the definition of the properties of this model.
     *
     * @return array
     */
    protected static function define_properties() {
        return [
            'name' => [
                'type' => PARAM_TEXT,
                'default' => '',
            ],
            'description' => [
                'type' => PARAM_TEXT,
                'default' => '',
            ],
            'content' => [
                'type' => PARAM_RAW,
            ],
            'enabled' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
            'sortorder' => [
                'type' => PARAM_INT,
                'default' => 0,
            ],
        ];
    }

    /**
     * Hook to execute before an update.
     *
     * Please note that at this stage the data has already been validated and therefore
     * any new data being set will not be validated before it is sent to the database.
     */
    protected function before_update() {
        $this->before_save();
    }

    /**
     * Hook to execute before a create.
     *
     * Please note that at this stage the data has already been validated and therefore
     * any new data being set will not be validated before it is sent to the database.
     */
    protected function before_create() {
        $this->before_save();
    }

    /**
     * As there is no hook for before both create and update, this function is called by both hooks.
     */
    private function before_save() {
        $this->plist = new property_list($this->get('content'));
        $this->set('content', $this->plist->to_xml());
    }

    /**
     * Validate template content.
     *
     * @param string $content Content string to validate.
     *
     * @return bool|\lang_string
     */
    protected function validate_content(string $content) {
        if (helper::is_valid_seb_config($content)) {
            return true;
        } else {
            return new \lang_string('invalidtemplate', 'quizaccess_seb');
        }
    }

    /**
     * Check if we can delete the template.
     *
     * @return bool
     */
    public function can_delete(): bool {
        $result = true;

        if ($this->get('id')) {
            $settings = seb_quiz_settings::get_records(['templateid' => $this->get('id')]);
            $result = empty($settings);
        }

        return $result;
    }

}
