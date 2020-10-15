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
 * The definition of a text area which can be exported.
 *
 * @package     core
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

namespace core\content\export\exportable_items;

use context;
use core\content\export\exportable_item;
use core\content\export\exported_item;
use core\content\export\zipwriter;

/**
 * The definition of a text area which can be exported.
 *
 * @copyright   2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exportable_textarea extends exportable_item {

    /** @var string The name of the table that ha the textarea within it */
    protected $tablename;

    /** @var int The id in the table */
    protected $id;

    /** @var string The name of the text field within the table */
    protected $textfield;

    /** @var null|string The name of the format field relating to the text field */
    protected $textformatfield;

    /** @var null|string The name of a file area for this content */
    protected $filearea;

    /** @var null|int The itemid for files in this text field */
    protected $itemid;

    /** @var null|int The itemid used for constructing pluginfiles */
    protected $pluginfileitemid;

    /**
     * Create a new exportable_item instance.
     *
     * If no filearea or itemid  is specified the no attempt will be made to export files.
     *
     * @param   context $context The context that this content belongs to
     * @param   string $component The component that this textarea belongs to
     * @param   string $uservisiblename The name displayed to the user when filtering
     * @param   string $tablename The name of the table that this textarea is in
     * @param   string $textfield The field within the tbale
     * @param   int $id The id in the database
     * @param   null|string $textformatfield The field in the database relating to the format field if one is present
     * @param   null|string $filearea The name of the file area for files associated with this text area
     * @param   null|int $itemid The itemid for files associated with this text area
     * @param   null|int $pluginfileitemid The itemid to use when constructing the pluginfile URL
     *          Some fileareas do not use any itemid in the URL and should therefore provide a `null` value here.
     */
    public function __construct(
        context $context,
        string $component,
        string $uservisiblename,
        string $tablename,
        string $textfield,
        int $id,
        ?string $textformatfield = null,
        ?string $filearea = null,
        ?int $itemid = null,
        ?int $pluginfileitemid = null
    ) {
        parent::__construct($context, $component, $uservisiblename);

        $this->tablename = $tablename;
        $this->textfield = $textfield;
        $this->textformatfield = $textformatfield;
        $this->id = $id;
        $this->filearea = $filearea;
        $this->itemid = $itemid;
        $this->pluginfileitemid = $pluginfileitemid;
    }

    /**
     * Add the content to the archive.
     *
     * @param   zipwriter $archive
     */
    public function add_to_archive(zipwriter $archive): ?exported_item {
        global $DB;

        // Fetch the field.
        $fields = [$this->textfield];
        if (!empty($this->textformatfield)) {
            $fields[] = $this->textformatfield;
        }
        $record = $DB->get_record($this->tablename, ['id' => $this->id], implode(', ', $fields));

        if (empty($record)) {
            return null;
        }

        // Export all of the files for this text area.
        $text = $record->{$this->textfield};
        if (empty($text)) {
            $text = '';
        }

        if ($this->may_include_files()) {
            // This content may include inline files.
            $exporteditem = $archive->add_pluginfiles_for_content(
                $this->get_context(),
                "",
                $text,
                $this->component,
                $this->filearea,
                $this->itemid,
                $this->pluginfileitemid
            );
        } else {
            $exporteditem = new exported_item();
            $exporteditem->set_content($text);
        }

        $exporteditem->set_title($this->get_user_visible_name());
        return $exporteditem;
    }

    /**
     * Whether files may be included in this textarea.
     *
     * Both a filearea, and itemid are required for files to be exportable.
     *
     * @return  bool
     */
    protected function may_include_files(): bool {
        if ($this->filearea === null) {
            return false;
        }

        if ($this->itemid === null) {
            return false;
        }

        return true;
    }
}
