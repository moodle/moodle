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

namespace core\output;

/**
 * Data structure representing an icon font.
 *
 * @copyright 2016 Damyon Wiese
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 * @category output
 */
class pix_icon_font implements templatable {
    /**
     * @var pix_icon $pixicon The original icon.
     */
    private $pixicon = null;

    /**
     * @var string $key The mapped key.
     */
    private $key;

    /**
     * @var bool $mapped The icon could not be mapped.
     */
    private $mapped;

    /**
     * Constructor
     *
     * @param pix_icon $pixicon The original icon
     */
    public function __construct(pix_icon $pixicon) {
        global $PAGE;

        $this->pixicon = $pixicon;
        $this->mapped = false;
        $iconsystem = icon_system::instance();

        $this->key = $iconsystem->remap_icon_name($pixicon->pix, $pixicon->component);
        if (!empty($this->key)) {
            $this->mapped = true;
        }
    }

    /**
     * Return true if this pix_icon was successfully mapped to an icon font.
     *
     * @return bool
     */
    public function is_mapped() {
        return $this->mapped;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(renderer_base $output) {

        $pixdata = $this->pixicon->export_for_template($output);

        $title = isset($this->pixicon->attributes['title']) ? $this->pixicon->attributes['title'] : '';
        $alt = isset($this->pixicon->attributes['alt']) ? $this->pixicon->attributes['alt'] : '';
        if (empty($title)) {
            $title = $alt;
        }
        $data = [
            'extraclasses' => $pixdata['extraclasses'],
            'title' => $title,
            'alt' => $alt,
            'key' => $this->key,
        ];

        return $data;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(pix_icon_font::class, \pix_icon_font::class);
