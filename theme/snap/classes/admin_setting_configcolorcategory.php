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

namespace theme_snap;

use theme_snap\color_contrast;

/**
 * Class to render a text box which validates a JSON string for categories color configuration.
 * @package theme_snap
 * @author jonathan.garcia@openlms.net
 * @copyright Open LMS, 2018
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class admin_setting_configcolorcategory extends \admin_setting_configtext {
    /** @var int The number of rows to make the editor. */
    private $rows;
    /** @var int The number of columns to make the editor. */
    private $cols;

    /**
     * Config color category constructor.
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting string or array
     * @param mixed $paramtype
     * @param string $cols The number of columns to make the editor
     * @param string $rows The number of rows to make the editor
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_TEXT, $cols='60', $rows='8') {
        $this->rows = $rows;
        $this->cols = $cols;
        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype);
    }

    /**
     * Returns an XHTML string for the editor.
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query='') {
        global $OUTPUT;

        $default = $this->get_defaultsetting();
        $defaultinfo = $default;
        if (!is_null($default) && $default !== '') {
            $defaultinfo = "\n".$default;
        }

        $context = (object) [
            'cols' => $this->cols,
            'rows' => $this->rows,
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'forceltr' => $this->get_force_ltr(),
        ];
        $element = '';
        $failedcontrast = color_contrast::compare_cat_colors();
        if (!empty($failedcontrast['white']) || !empty($failedcontrast['custombar']) || !empty($failedcontrast['customnav'])) {
            $contrastmessage = get_string('catinvalidratio', 'theme_snap', [
                    'white' => implode(', ', $failedcontrast['white']),
                    'custombar' => implode(', ', $failedcontrast['custombar']),
                    'customnav' => implode(', ', $failedcontrast['customnav']),
                ]);

            $element .= $OUTPUT->notification($contrastmessage, \core\output\notification::NOTIFY_WARNING);
        }
        $element .= $OUTPUT->render_from_template('core_admin/setting_configtextarea', $context);
        return format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $defaultinfo, $query);
    }

    /**
     * Saves the setting.
     *
     * @param string $data
     * @return bool|string
     */
    public function write_setting($data) {
        global $DB;
        if (!empty($data)) {
            $categories = json_decode($data);
            if ($categories === false || $categories == null) {
                return get_string('error:categorycolorinvalidjson', 'theme_snap');
            }
            foreach ($categories as $categoryid => $color) {
                if (!is_numeric($categoryid) || !preg_match('/#([a-fA-F0-9]{3}){1,2}\b/', $color)  || $color == '#FFFFFF') {
                    return get_string('error:categorycolorinvalidvalue', 'theme_snap', $categoryid);
                }
                try {
                    \core_course_category::get($categoryid);
                } catch (\moodle_exception $e) {
                    return get_string('error:categorynotfound', 'theme_snap', $categoryid);
                }
            }
            $sizeelements = $this->validate_text($data);
            if ($sizeelements !== count((array)$categories)) {
                return get_string('error:duplicatedcategoryids', 'theme_snap');
            }
        } else {
            $data = 0;
        }
        return ($this->config_write($this->name, $data) ? '' : get_string('errorsetting', 'admin'));
    }

    /**
     * Returns the quantity of pairs (id => color) in the JSON string.
     * @param string $data
     * @return int
     */
    private function validate_text($data) {
        $temp = $data;
        $symbols = array('{', '}', '"');
        foreach ($symbols as $symbol) {
            $temp = str_replace($symbol, '', $temp);
        }
        $temp = trim(str_replace(',', ' ', $temp));
        $split = preg_split('/\s+/', $temp);
        $cont = 0;
        foreach ($split as $key => $value) {
            if (!empty($value)) {
                $cont++;
            }
        }
        return $cont;
    }

}
