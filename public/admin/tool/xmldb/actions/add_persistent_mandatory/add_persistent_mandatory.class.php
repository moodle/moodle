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
 * @package    tool_xmldb
 * @copyright  2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Add the mandatory fields for persistent to the table.
 *
 * @package    tool_xmldb
 * @copyright  2019 Michael Aherne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_persistent_mandatory extends XMLDBAction {

    function init() {

        parent::init();

        // Get needed strings.
        $this->loadStrings(array(
            'addpersistent' => 'tool_xmldb',
            'persistentfieldsconfirm' => 'tool_xmldb',
            'persistentfieldscomplete' => 'tool_xmldb',
            'persistentfieldsexist' => 'tool_xmldb',
            'back' => 'core'
        ));

    }

    function getTitle() {
        return $this->str['addpersistent'];
    }

    function invoke() {

        parent::invoke();

        $this->does_generate = ACTION_GENERATE_HTML;

        global $CFG, $XMLDB, $OUTPUT;

        $dir = required_param('dir', PARAM_PATH);
        $dirpath = $CFG->dirroot . $dir;

        if (empty($XMLDB->dbdirs)) {
            return false;
        }

        if (!empty($XMLDB->editeddirs)) {
            $editeddir = $XMLDB->editeddirs[$dirpath];
            $structure = $editeddir->xml_file->getStructure();
        }

        $tableparam = required_param('table', PARAM_ALPHANUMEXT);

        /** @var xmldb_table $table */
        $table = $structure->getTable($tableparam);

        $result = true;
        // Launch postaction if exists (leave this here!)
        if ($this->getPostAction() && $result) {
            return $this->launch($this->getPostAction());
        }

        $confirm = optional_param('confirm', false, PARAM_BOOL);

        $fields = ['usermodified', 'timecreated', 'timemodified'];
        $existing = [];
        foreach ($fields as $field) {
            if ($table->getField($field)) {
                $existing[] = $field;
            }
        }

        $returnurl = new \moodle_url('/admin/tool/xmldb/index.php', [
            'table' => $tableparam,
            'dir' => $dir,
            'action' => 'edit_table'
        ]);

        $backbutton = html_writer::link($returnurl, '[' . $this->str['back'] . ']');
        $actionbuttons = html_writer::tag('p', $backbutton, ['class' => 'centerpara buttons']);

        if (!$confirm) {

            if (!empty($existing)) {

                $message = html_writer::span($this->str['persistentfieldsexist']);
                $message .= html_writer::alist($existing);
                $this->output .= $OUTPUT->notification($message);

                if (count($existing) == count($fields)) {
                    $this->output .= $actionbuttons;
                    return true;
                }
            }

            $confirmurl = new \moodle_url('/admin/tool/xmldb/index.php', [
                'table' => $tableparam,
                'dir' => $dir,
                'action' => 'add_persistent_mandatory',
                'sesskey' => sesskey(),
                'confirm' => '1'
            ]);

            $message = html_writer::span($this->str['persistentfieldsconfirm']);
            $message .= html_writer::alist(array_diff($fields, $existing));
            $this->output .= $OUTPUT->confirm($message, $confirmurl, $returnurl);

        } else {

            $fieldsadded = [];
            foreach ($fields as $field) {
                if (!in_array($field, $existing)) {
                    $fieldsadded[] = $field;
                    $table->add_field($field, XMLDB_TYPE_INTEGER, 10, null, XMLDB_NOTNULL, null, 0);
                }
            }

            if (!$table->getKey('usermodified')) {
                $table->add_key('usermodified', XMLDB_KEY_FOREIGN, ['usermodified'], 'user', ['id']);
            }

            $structure->setVersion(userdate(time(), '%Y%m%d', 99, false));
            $structure->setChanged(true);

            $message = html_writer::span($this->str['persistentfieldscomplete']);
            $message .= html_writer::alist(array_diff($fields, $existing));
            $this->output .= $OUTPUT->notification($message, 'success');

            $this->output .= $actionbuttons;
        }

        return $result;
    }

}
