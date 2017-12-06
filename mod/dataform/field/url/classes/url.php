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
 * @package dataformfield
 * @subpackage url
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformfield_url_url extends mod_dataform\pluginbase\dataformfield {
    /**
     *
     */
    public function content_names() {
        return array('', 'alt');
    }

    /**
     *
     */
    protected function format_content($entry, array $values = null) {
        $fieldid = $this->id;
        $oldcontents = array();
        $contents = array();
        // Old contents.
        if (isset($entry->{"c{$fieldid}_content"})) {
            $oldcontents[] = isset($entry->{"c$fieldid". '_content'}) ? $entry->{"c$fieldid". '_content'} : null;
            $oldcontents[] = isset($entry->{"c$fieldid". '_content1'}) ? $entry->{"c$fieldid". '_content1'} : null;
        }
        // New contents.
        $url = $alttext = null;
        if (!empty($values)) {
            foreach ($values as $name => $value) {
                switch ($name) {
                    case '':
                        if ($value and $value != 'http://') {
                            $url = clean_param($value, PARAM_URL);
                        }
                        break;
                    case 'alt':
                        $alttext = clean_param($value, PARAM_NOTAGS);
                        break;
                }
            }
        }
        if (!is_null($url)) {
            $contents[] = $url;
            $contents[] = $alttext;
        }
        return array($contents, $oldcontents);
    }

    /**
     *
     */
    public function get_content_parts() {
        return array('content', 'content1');
    }

}
