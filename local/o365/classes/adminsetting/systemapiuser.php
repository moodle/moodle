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
 * Admin setting to set the system API user.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\adminsetting;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/lib/adminlib.php');

/**
 * Admin setting to set the system API user.
 */
class systemapiuser extends \admin_setting {
    /** @var mixed int means PARAM_XXX type, string is a allowed format in regex */
    public $paramtype;

    /** @var int default field size */
    public $size;

    /**
     * Config text constructor
     *
     * @param string $name unique ascii name
     * @param string $visiblename localised
     * @param string $description long localised info
     * @param string $defaultsetting
     * @param mixed $paramtype int means PARAM_XXX type, string is a allowed format in regex
     * @param int $size default field size
     */
    public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW, $size=null) {
        $this->paramtype = $paramtype;
        if (!is_null($size)) {
            $this->size = $size;
        } else {
            $this->size = ($paramtype === PARAM_INT) ? 5 : 30;
        }
        parent::__construct($name, $visiblename, $description, $defaultsetting);
    }

    /**
     * Return the setting
     *
     * @return mixed returns config if successful else null
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Store new setting
     *
     * @param mixed $data string or array, must not be NULL
     * @return string empty string if ok, string error message otherwise
     */
    public function write_setting($data) {
        $this->config_write($this->name, '0');
        return '';
    }

    /**
     * Return an XHTML string for the setting.
     *
     * @param mixed $data
     * @param string $query
     * @return string
     */
    public function output_html($data, $query='') {
        global $OUTPUT;
        $tokens = get_config('local_o365', 'systemtokens');
        $setuser = '';
        if (!empty($tokens)) {
            $tokens = unserialize($tokens);
            if (isset($tokens['idtoken'])) {
                try {
                    $idtoken = \auth_oidc\jwt::instance_from_encoded($tokens['idtoken']);
                    $setuser = $idtoken->claim('upn');
                } catch (\Exception $e) {
                    // There is a check below for an empty $setuser.
                }
            }
        }

        $settinghtml = '<input type="hidden" id="'.$this->get_id().'" name="'.$this->get_full_name().'" value="0" />';
        $setuserurl = new \moodle_url('/local/o365/acp.php', ['mode' => 'setsystemuser']);
        if (!empty($setuser)) {
            $message = \html_writer::tag('span', get_string('settings_systemapiuser_userset', 'local_o365', $setuser)).' ';
            $linkstr = get_string('settings_systemapiuser_change', 'local_o365');
            $message .= \html_writer::link($setuserurl, $linkstr, ['class' => 'btn', 'style' => 'margin-left: 0.5rem']);
            $messageattrs = ['class' => 'local_o365_statusmessage alert alert-success'];
            $icon = $OUTPUT->pix_icon('t/check', 'success', 'moodle');
            $settinghtml .= \html_writer::tag('div', $icon.$message, $messageattrs);
        } else {
            $message = \html_writer::tag('span', get_string('settings_systemapiuser_usernotset', 'local_o365')).' ';
            $linkstr = get_string('settings_systemapiuser_setuser', 'local_o365');
            $message .= \html_writer::link($setuserurl, $linkstr, ['class' => 'btn', 'style' => 'margin-left: 0.5rem']);
            $messageattrs = ['class' => 'local_o365_statusmessage alert alert-info'];
            $icon = $OUTPUT->pix_icon('i/warning', 'warning', 'moodle');
            $settinghtml .= \html_writer::tag('div', $icon.$message, $messageattrs);
        }
        return format_admin_setting($this, $this->visiblename, $settinghtml, $this->description);
    }
}
