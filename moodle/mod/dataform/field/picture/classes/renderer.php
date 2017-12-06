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
 * @subpackage picture
 * @copyright 2011 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') or die;

/**
 *
 */
class dataformfield_picture_renderer extends dataformfield_file_renderer {

    /**
     *
     */
    protected function replacements(array $patterns, $entry, array $options = null) {
        $field = $this->_field;
        $fieldname = $field->name;
        $edit = !empty($options['edit']);

        // There is only one possible pattern here so no check.
        $replacements = parent::replacements($patterns, $entry, $options);

        if ($edit) {
            // Just return because the edite pattern has already been processed by the parent.
            return $replacements;
        }

        // Browse mode.
        foreach ($patterns as $pattern => $cleanpattern) {
            $displaybrowse = '';
            switch ($cleanpattern) {
                case "[[$fieldname:tn-url]]":
                    $displaybrowse = $this->display_browse($entry, array('tn' => 1, 'url' => 1));
                    break;
                case "[[$fieldname:linked]]":
                    $displaybrowse = $this->display_browse($entry, array('linked' => 1));
                    break;
                case "[[$fieldname:base64]]":
                    $displaybrowse = $this->display_browse($entry, array('base64' => 1));
                    break;
                case "[[$fieldname:tn]]":
                    $displaybrowse = $this->display_browse($entry, array('tn' => 1));
                    break;
                case "[[$fieldname:tn-linked]]":
                    $displaybrowse = $this->display_browse($entry, array('tn' => 1, 'linked' => 1));
                    break;
                case "[[$fieldname:tn-base64]]":
                    $displaybrowse = $this->display_browse($entry, array('tn' => 1, 'base64' => 1));
                    break;
            }

            if (!empty($displaybrowse)) {
                $replacements[$pattern] = $displaybrowse;
            }
        }

        return $replacements;
    }

    /**
     *
     */
    public function pluginfile_patterns() {
        $fieldname = $this->_field->name;
        return array(
            "[[{$fieldname}]]",
            "[[{$fieldname}:linked]]",
            "[[{$fieldname}:tn-linked]]",
        );
    }

    /**
     *
     */
    protected function display_file($file, $path, $altname, $params = null) {
        $field = $this->_field;

        if ($file->is_valid_image()) {
            $filename = $file->get_filename();
            $imgattr = array('style' => array());

            if (!empty($params['tn'])) {
                // Decline if the file is not really a thumbnail.
                if (strpos($filename, 'thumb_') === false) {
                    return '';
                }
            } else {
                // Decline if the file is a thumbnail.
                if (strpos($filename, 'thumb_') !== false) {
                    return '';
                }

                // The picture's display dimension may be set in the field.
                if ($field->appearance->dispw) {
                    $imgattr['style'][] = 'width:'. s($field->appearance->dispw). s($field->appearance->dispu);
                }
                if ($field->appearance->disph) {
                    $imgattr['style'][] = 'height:'. s($field->appearance->disph). s($field->appearance->dispu);
                }
            }

            // Calculate src: either moodle url or base64.
            if (!empty($params['download'])) {
                return $this->display_link($file, $path, $altname, $params);
            } else if (!empty($params['base64'])) {
                $src = 'data:'. $file->get_mimetype(). ';base64,'. base64_encode($file->get_content());
            } else {
                $pluginfileurl = new moodle_url('/pluginfile.php');
                $src = moodle_url::make_file_url($pluginfileurl, "$path/$filename");

                // For url request return it here.
                if (!empty($params['url'])) {
                    return $src;
                }
            }

            $imgattr['src'] = $src;
            $imgattr['style'] = implode(';', $imgattr['style']);

            $str = html_writer::empty_tag('img', $imgattr);

            if (!empty($params['linked'])) {
                return html_writer::link($src, $str);
            } else {
                return $str;
            }
        } else {
            return '';
        }
    }

    /**
     * Array of patterns this field supports
     */
    protected function patterns() {
        $fieldname = $this->_field->name;

        $patterns = parent::patterns();
        $patterns["[[{$fieldname}:linked]]"] = array(false);
        $patterns["[[{$fieldname}:base64]]"] = array(false);
        $patterns["[[{$fieldname}:tn]]"] = array(true, $fieldname);
        $patterns["[[{$fieldname}:tn-url]]"] = array(false);
        $patterns["[[{$fieldname}:tn-linked]]"] = array(false);
        $patterns["[[{$fieldname}:tn-base64]]"] = array(false);

        return $patterns;
    }
}
