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
 * Azure AD user sync options.
 *
 * @package local_o365
 * @author Nagesh Tembhurnikar <nagesh@introp.net>
 * @author Lai Wei <lai.wei@enovation.ie>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\adminsetting;

use admin_setting_configmulticheckbox;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/lib/adminlib.php');

/**
 * Azure AD sync options.
 */
class aadsyncoptions extends admin_setting_configmulticheckbox {
    /** @var array Array of choices value=>label */
    public $choices;

    /**
     * Constructor: uses parent::__construct
     *
     * @param string $name unique ascii name, either 'mysetting' for settings that in config, or 'myplugin/mysetting' for ones in
     * config_plugins.
     * @param string $visiblename localised
     * @param string $description long localised info
     */
    public function __construct($name, $visiblename, $description) {
        $choices = [
            'create' => new \lang_string('settings_aadsync_create', 'local_o365'),
            'update' => new \lang_string('settings_aadsync_update', 'local_o365'),
            'suspend' => new \lang_string('settings_aadsync_suspend', 'local_o365'),
            'delete' => new \lang_string('settings_aadsync_delete', 'local_o365'),
            'reenable' => new \lang_string('settings_aadsync_reenable', 'local_o365'),
            'disabledsync' => new \lang_string('settings_aadsync_disabledsync', 'local_o365'),
            'match' => new \lang_string('settings_aadsync_match', 'local_o365'),
            'matchswitchauth' => new \lang_string('settings_aadsync_matchswitchauth', 'local_o365'),
            'appassign' => new \lang_string('settings_aadsync_appassign', 'local_o365'),
            'photosync' => new \lang_string('settings_aadsync_photosync', 'local_o365'),
            'photosynconlogin' => new \lang_string('settings_aadsync_photosynconlogin', 'local_o365'),
            'tzsync' => new \lang_string('settings_addsync_tzsync', 'local_o365'),
            'tzsynconlogin' => new \lang_string('settings_addsync_tzsynconlogin', 'local_o365'),
            'nodelta' => new \lang_string('settings_aadsync_nodelta', 'local_o365'),
            'emailsync' => new \lang_string('settings_aadsync_emailsync', 'local_o365'),
            'guestsync' => new \lang_string('settings_aadsync_guestsync', 'local_o365'),
        ];
        parent::__construct($name, $visiblename, $description, [], $choices);
    }

    /**
     * Returns XHTML field(s) as required by choices.
     *
     * Rely on data being an array should data ever be another valid vartype with acceptable value this may cause a warning/error
     * if (!is_array($data)) would fix the problem.
     *
     * @param array $data An array of checked values
     * @param string $query
     * @return string XHTML field
     */
    public function output_html($data, $query = '') {
        global $OUTPUT;
        if (!$this->load_choices() or empty($this->choices)) {
            return '';
        }
        $default = $this->get_defaultsetting();
        if (is_null($default)) {
            $default = array();
        }
        if (is_null($data)) {
            $data = array();
        }
        $options = array();
        $defaults = array();
        foreach ($this->choices as $key => $description) {
            if (!empty($data[$key])) {
                $checked = 'checked="checked"';
            } else {
                $checked = '';
            }
            if (!empty($default[$key])) {
                $defaults[] = $description;
            }
            $helphtml = $OUTPUT->help_icon('help_user_' . $key, 'local_o365');
            $options[] = '<input type="checkbox" id="' . $this->get_id() . '_' . $key . '" name="' . $this->get_full_name()
                . '[' . $key . ']" value="1" ' . $checked . ' />' . ' <label for="' . $this->get_id() . '_' . $key . '">'
                . highlightfast($query, $description) . '</label>' . $helphtml;
        }
        if (is_null($default)) {
            $defaultinfo = null;
        } else if (!empty($defaults)) {
            $defaultinfo = implode(', ', $defaults);
        } else {
            $defaultinfo = get_string('none');
        }
        // Something must be submitted even if nothing selected.
        $return = '<div class="form-multicheckbox">';
        $return .= '<input type="hidden" name="' . $this->get_full_name() . '[xxxxx]" value="1" />';
        if ($options) {
            $return .= '<ul>';
            foreach ($options as $option) {
                $return .= '<li>' . $option . '</li>';
            }
            $return .= '</ul>';
        }
        $return .= '</div>';

        return format_admin_setting($this, $this->visiblename, $return, $this->description, false, '', $defaultinfo, $query);
    }

    /**
     * Data cleanup before saving.
     *
     * @param array $data
     *
     * @return mixed|string
     */
    public function write_setting($data) {
        // Option 'delete' can only be set if option 'suspend' is check.
        if (!isset($data['suspend']) && isset($data['delete'])) {
            unset($data['delete']);
        }

        return parent::write_setting($data);
    }
}
