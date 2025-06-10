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
 * The table that displays the templates in a given context.
 *
 * @package    mod_customcert
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_customcert;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . '/tablelib.php');

/**
 * Class for the table that displays the templates in a given context.
 *
 * @package    mod_customcert
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manage_templates_table extends \table_sql {

    /**
     * @var \context $context
     */
    protected $context;

    /**
     * Sets up the table.
     *
     * @param \context $context
     */
    public function __construct($context) {
        parent::__construct('mod_customcert_manage_templates_table');

        $columns = [
            'name',
            'actions',
        ];

        $headers = [
            get_string('name'),
            '',
        ];

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->collapsible(false);
        $this->sortable(true);

        $this->context = $context;
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $template
     * @return string
     */
    public function col_name($template) {
        return format_string($template->name, true, ['context' => $this->context]);
    }

    /**
     * Generate the actions column.
     *
     * @param \stdClass $template
     * @return string
     */
    public function col_actions($template) {
        global $OUTPUT;

        // Link to edit the template.
        $editlink = new \moodle_url('/mod/customcert/edit.php', ['tid' => $template->id]);
        $editicon = $OUTPUT->action_icon($editlink, new \pix_icon('t/edit', get_string('edit')));

        // Link to duplicate the template.
        $duplicatelink = new \moodle_url('/mod/customcert/manage_templates.php',
            [
                'tid' => $template->id,
                'action' => 'duplicate',
                'sesskey' => sesskey(),
            ]
        );
        $duplicateicon = $OUTPUT->action_icon($duplicatelink, new \pix_icon('t/copy', get_string('duplicate')), null,
            ['class' => 'action-icon duplicate-icon']);

        // Link to delete the template.
        $deletelink = new \moodle_url('/mod/customcert/manage_templates.php',
            [
                'tid' => $template->id,
                'action' => 'delete',
                'sesskey' => sesskey(),
            ]
        );
        $deleteicon = $OUTPUT->action_icon($deletelink, new \pix_icon('t/delete', get_string('delete')), null,
            ['class' => 'action-icon delete-icon']);

        return $editicon . $duplicateicon . $deleteicon;
    }

    /**
     * Query the reader.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        global $DB;

        $total = $DB->count_records('customcert_templates', ['contextid' => $this->context->id]);

        $this->pagesize($pagesize, $total);

        $this->rawdata = $DB->get_records('customcert_templates', ['contextid' => $this->context->id],
            $this->get_sql_sort(), '*', $this->get_page_start(), $this->get_page_size());

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars($total > $pagesize);
        }
    }
}
