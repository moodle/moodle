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

namespace mod_data\output;

use core_tag_tag;
use mod_data\manager;
use templatable;
use renderable;

/**
 * Renderable class for the default templates in the database activity.
 *
 * @package    mod_data
 * @copyright  2022 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class defaulttemplate implements templatable, renderable {

    /** @var array $fields The array containing the existing fields. */
    private $fields;

    /** @var string $templatename The template name (addtemplate, listtemplate...). */
    private $templatename;

    /** @var bool $isform Whether a form should be displayed instead of data. */
    private $isform;

    /**
     * The class constructor.
     *
     * @param array $fields The array containing the existing fields.
     * @param string $templatename The template name (addtemplate, listtemplate...).
     * @param bool $isform Whether a form should be displayed instead of data.
     */
    public function __construct(array $fields, string $templatename, bool $isform) {
        $this->fields = $fields;
        $this->templatename = $templatename;
        $this->isform = $isform;
    }

    /**
     * Obtains the mustache template name for this database template.
     *
     * @return string the file mustache path for this template.
     */
    public function get_templatename(): string {
        return 'mod_data/defaulttemplate_' . $this->templatename;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output The renderer to be used to render the action bar elements.
     * @return array The data to display.
     */
    public function export_for_template(\renderer_base $output): array {
        $result = [];
        $exportedfields = [];
        foreach ($this->fields as $field) {
            $fieldname = $field->field->name;
            if ($this->isform) {
                $fieldcontent = $field->display_add_field();
            } else {
                $fieldcontent = '[[' . $fieldname . ']]';
            }
            $exportedfields[] = [
                'fieldname' => $fieldname,
                'fieldcontent' => $fieldcontent,
            ];
        }

        if (!empty($exportedfields)) {
            $result['fields'] = $exportedfields;
        }

        if (core_tag_tag::is_enabled(manager::PLUGINNAME, 'data_records')) {
            // Add tags information only if they are enabled.
            if ($this->isform) {
                $tags = data_generate_tag_form();
            } else {
                $tags = '##tags##';
            }
            $result['tags'] = $tags;
        }

        return $result;
    }
}
