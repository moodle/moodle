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
 * Template admin setting.
 *
 * @package    theme_adaptable
 * @copyright  2020 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

/**
 * Template admin setting.
 */
class adaptable_admin_setting_configtemplate extends admin_setting_configtextarea {
    /**
     * @var $templatename The name of the template.
     */
    private $templatename;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $visiblename
     * @param string $description
     * @param mixed $defaultsetting string or array
     * @param string $templatename
     * @param mixed $paramtype
     * @param string $cols The number of columns to make the editor
     * @param string $rows The number of rows to make the editor
     */
    public function __construct(
        $name,
        $visiblename,
        $description,
        $defaultsetting,
        $templatename,
        $paramtype = PARAM_RAW,
        $cols = '60',
        $rows = '8'
    ) {
        $this->rows = $rows;
        $this->cols = $cols;

        $this->templatename = $templatename;

        parent::__construct($name, $visiblename, $description, $defaultsetting, $paramtype);
    }

    /**
     * Returns an XHTML string for the editor
     *
     * @param string $data
     * @param string $query
     * @return string XHTML string for the editor
     */
    public function output_html($data, $query = '') {
        global $OUTPUT, $PAGE;

        $default = $this->get_defaultsetting();
        $defaultinfo = $default;
        if (!is_null($default) && $default !== '') {
            $defaultinfo = "\n" . $default;
        }

        $context = (object) [
            'cols' => $this->cols,
            'rows' => $this->rows,
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => $data,
            'forceltr' => $this->get_force_ltr(),
        ];
        $element = $OUTPUT->render_from_template('core_admin/setting_configtextarea', $context);

        $element = format_admin_setting($this, $this->visiblename, $element, $this->description, true, '', $defaultinfo, $query);

        $sourcerenderer = $PAGE->get_renderer('theme_adaptable', 'mustachesource');
        $originalsource = $sourcerenderer->get_template($this->templatename);

        $overridetemplate = get_config('theme_adaptable', $this->name);

        if (!empty($overridetemplate)) {
            $templateoverridden = true;
        } else {
            $templateoverridden = false;
            $overridetemplate = $originalsource;
        }

        $mustacherenderer = $PAGE->get_renderer('theme_adaptable', 'mustache');

        preg_match('/Example context \(json\):([\s\S]*)/', $overridetemplate, $matched);  // From 'display.js' in the template tool.

        if (!empty($matched[1])) {
            $json = trim(substr($matched[1], 0, strpos($matched[1], '}}')));
            $data = json_decode($json);

            $context = (object) [
                'templatepreview' => $mustacherenderer->render_from_template($overridetemplate, $data),
                'templateoverridden' => $templateoverridden,
            ];
            $element .= $OUTPUT->render_from_template('theme_adaptable/adaptable_admin_setting_configtemplate', $context);
        } else {
            $context = [];
            $element .= $OUTPUT->render_from_template('theme_adaptable/adaptable_admin_setting_configtemplate_nopreview', $context);
        }

        $context = (object) [
            'templatesource' => $originalsource,
        ];
        $element .= $OUTPUT->render_from_template('theme_adaptable/adaptable_admin_setting_configtemplate_source', $context);

        return $element;
    }
}
