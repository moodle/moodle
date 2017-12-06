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
 * @package dataformview
 * @subpackage rss
 * @copyright 2013 Itamar Tzadok {@link http://substantialmethods.com}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/rsslib.php");

/**
 * A dataform view class for rss feed.
 */
class dataformview_rss_rss extends mod_dataform\pluginbase\dataformview implements mod_dataform\interfaces\rss {

    protected $_editors = array('section', 'param2', 'param6');
    protected $_entrytemplate = null;

    /**
     *
     */
    public static function get_file_areas() {
        return array('section', 'param2', 'param6');
    }

    /**
     * Generates the default entry template for a new view instance or when reseting an existing instance.
     *
     * @return void
     */
    public function set_default_entry_template($content = null) {
        // Get all the fields.
        if (!$fields = $this->df->field_manager->get_fields()) {
            return;
        }

        if ($content === null) {
            // Set content.
            $table = new html_table();
            $table->attributes['align'] = 'center';
            $table->attributes['cellpadding'] = '2';
            // Fields.
            foreach ($fields as $field) {
                if ($field->id > 0) {
                    $name = new html_table_cell($field->name. ':');
                    $name->style = 'text-align:right;';
                    $content = new html_table_cell("[[{$field->name}]]");
                    $row = new html_table_row();
                    $row->cells = array($name, $content);
                    $table->data[] = $row;
                }
            }
            // Actions.
            $row = new html_table_row();
            $entryactions = get_string('fieldname', 'dataformfield_entryactions');
            $actions = new html_table_cell("[[$entryactions:edit]]  [[$entryactions:delete]]");
            $actions->colspan = 2;
            $row->cells = array($actions);
            $table->data[] = $row;
            // Construct the table.
            $entrydefault = html_writer::table($table);
            $content = html_writer::tag('div', $entrydefault, array('class' => 'entry'));
        }
        $this->param2 = $content;
    }

    /**
     *
     */
    protected function group_entries_definition($entriesset, $name = '') {
        global $OUTPUT;

        $elements = array();

        // Flatten the set to a list of elements.
        foreach ($entriesset as $entrydefinitions) {
            $elements = array_merge($elements, $entrydefinitions);
        }

        // Dropped: Add group heading.

        // Wrap with entriesview.
        array_unshift($elements, html_writer::start_tag('div', array('class' => 'entriesview')));
        array_push($elements, html_writer::end_tag('div'));

        return $elements;
    }

    /**
     *
     */
    protected function entry_definition($fielddefinitions, array $options = null) {
        $elements = array();

        $entrytemplate = $this->entry_template;

        // If not editing, do simple replacement and return the html.
        if (empty($options['edit'])) {
            $elements[] = str_replace(array_keys($fielddefinitions), $fielddefinitions, $entrytemplate);
            return $elements;
        }

        // Editing so split the entry template to tags and html
        // split the entry template to tags and html.
        $tags = array_keys($fielddefinitions);
        $parts = $this->split_tags($tags, $entrytemplate);

        foreach ($parts as $part) {
            if (in_array($part, $tags)) {
                if ($def = $fielddefinitions[$part]) {
                    $elements[] = $def;
                }
            } else {
                $elements[] = $part;
            }
        }
        return $elements;
    }

    /**
     *
     */
    protected function new_entry_definition($entryid = -1) {
        $elements = array();

        // Get patterns definitions.
        $fields = $this->get_fields();
        $tags = array();
        $patterndefinitions = array();
        $entry = new \stdClass;

        if ($fieldpatterns = $this->get_pattern_set('field')) {
            foreach ($fieldpatterns as $fieldid => $patterns) {
                $field = $fields[$fieldid];
                $entry->id = $entryid;
                $options = array('edit' => true);
                if ($fielddefinitions = $field->get_definitions($patterns, $entry, $options)) {
                    $patterndefinitions = array_merge($patterndefinitions, $fielddefinitions);
                }
                $tags = array_merge($tags, $patterns);
            }
        }

        // Split the entry template to tags and html.
        $parts = $this->split_tags($tags, $this->entry_template);

        foreach ($parts as $part) {
            if (in_array($part, $tags)) {
                if ($def = $patterndefinitions[$part]) {
                    $elements[] = $def;
                }
            } else {
                $elements[] = $part;
            }
        }

        return $elements;
    }


    // RSS METHODS.

    /**
     * Returns a specific rss link.
     *
     * @return string HTML fragment
     */
    public function get_rss_link() {
        global $CFG, $USER;
        // Link to the RSS feed.
        if (!empty($CFG->enablerssfeeds) && !empty($CFG->dataform_enablerssfeeds)) {
            $dataformid = $this->df->id;
            $viewid = $this->id;
            $componentinstance = "$dataformid/$viewid";
            return rss_get_link($this->df->context->id, $USER->id, 'mod_dataform', $componentinstance);
        }
        return null;
    }

    /**
     * Generate a stamp for content from entry ids.
     * {@link mod_dataform\interfaces\rss::has_new_content()}
     */
    public function get_content_stamp() {
        $entryman = $this->entry_manager;

        // Generate the stamp from entry ids.
        $newstamp = null;
        $entryman->set_content(array('filter' => $this->filter));
        if ($entryman->entries) {
            $stamps = array();
            foreach ($entryman->entries as $entryid => $entry) {
                $stamps[] = "$entryid-$entry->timemodified";
            }
            $newstamp = base64_encode(implode(' ', $stamps));
        }
        return $newstamp;
    }

    /**
     * Formats and returns the content entries as rss items.
     * Assumes that {@link dataformview_rss::set_content()} has already been called
     * Typically by calling first {@link dataformview_rss::get_content_stamp()}.
     *
     * {@link mod_dataform\interfaces\rss::get_rss_items()}
     */
    public function get_rss_items() {
        $items = array();

        // Create all the articles.
        if ($entries = $this->entry_manager->entries) {
            foreach ($entries as $entryid => $entry) {
                $item = new stdclass;
                $item->title = $this->get_item_title($entry);
                $item->link = $this->get_item_url($entry);
                $item->description = $this->get_item_description($entry);
                $item->pubdate = $this->get_item_pubdate($entry);
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * {@link mod_dataform\interfaces\rss::get_rss_header_title()}
     */
    public function get_rss_header_title() {
        $df = mod_dataform_dataform::instance($this->dataid);
        if ($this->param4) {
            $title = format_string($this->param4, true, array('context' => $df->context));
        } else {
            $course = $df->course;
            $coursecontext = context_course::instance($course->id);
            $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));
            $title = $courseshortname . ': ' . format_string($df->name, true, array('context' => $df->context));
        }
        return $title;
    }

    /**
     * {@link mod_dataform\interfaces\rss::get_rss_header_link()}
     */
    public function get_rss_header_link() {
        $url = $this->get_baseurl();
        if ($this->param5) {
            $url->param('view', $this->param5);
        }

        return $url->out(false);
    }

    /**
     * {@link mod_dataform\interfaces\rss::get_rss_header_description()}
     */
    public function get_rss_header_description() {
        $description = '';
        if ($this->param6) {
            $description = format_text($this->param6, FORMAT_HTML, array('context' => $this->df->context));
        }
        return $description;
    }

    /**
     *
     */
    protected function get_item_title($entry) {
        $title = "Entry $entry->id";
        if ($this->param1) {
            $title = format_string($this->param1, true, array('context' => $this->df->context));
        }

        return $title;
    }

    /**
     * Returns the entry content formatted in the item template.
     *
     * @param stdClass $entry Entry content
     * @return string
     */
    protected function get_item_description($entry) {
        if (!$this->param2) {
            return null;
        }

        $options = array('edit' => false, 'managable' => false);
        $fielddefinitions = $this->get_field_definitions($entry, $options);
        $entrydefinition = $this->entry_definition($fielddefinitions, $options);
        $description = reset($entrydefinition);

        return $description;
    }

    /**
     * Returns a url for linking the entry from the feed to a view in the Dataform.
     *
     * @param stdClass $entry Entry content
     * @return string url
     */
    protected function get_item_url($entry) {
        $url = $this->get_baseurl();
        if ($this->param3) {
            $url->param('view', $this->param3);
        }
        // Add the entry id.
        $url->param('eids', $entry->id);

        return $url->out(false);
    }

    /**
     * Returns the time value that should mark the item publication date.
     *
     * @param stdClass $entry Entry content
     * @return int unix timestamp
     */
    protected function get_item_pubdate($entry) {
        return $entry->timecreated;
    }

    /**
     * Returns the content of the view's entry template with text filters applied.
     *
     * @return string HTML fragment.
     */
    protected function get_entry_template() {
        if ($this->_entrytemplate === null) {
            $this->_entrytemplate = '';
            if ($this->param2) {
                // Apply text filters to template.
                $formatoptions = array(
                    'para' => false,
                    'allowid' => true,
                    'trusted' => true,
                    'noclean' => true
                );
                $this->_entrytemplate = format_text($this->param2, FORMAT_HTML, $formatoptions);
            }
        }
        return $this->_entrytemplate;
    }
}
