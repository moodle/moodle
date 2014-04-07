<?php
// This file is part of The Bootstrap 3 Moodle theme
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
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_bootstrap
 * @copyright  2012
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . "/admin/renderer.php");


class theme_bootstrap_core_admin_renderer extends core_admin_renderer {

    protected function maturity_info($maturity) {
        if ($maturity == MATURITY_STABLE) {
            return ''; // No worries.
        }

        if ($maturity == MATURITY_ALPHA) {
            $level = 'notifyproblem';
        } else {
            $level = 'notifywarning';
        }

        $maturitylevel = get_string('maturity' . $maturity, 'admin');
        $warningtext = get_string('maturitycoreinfo', 'admin', $maturitylevel);
        $doclink = $this->doc_link('admin/versions', get_string('morehelp'));

        return $this->notification($warningtext . ' ' . $doclink, $level);
    }

    protected function maturity_warning($maturity) {
        if ($maturity == MATURITY_STABLE) {
            return ''; // No worries.
        }

        $maturitylevel = get_string('maturity' . $maturity, 'admin');
        $maturitywarning = get_string('maturitycorewarning', 'admin', $maturitylevel);
        $maturitywarning .= $this->doc_link('admin/versions', get_string('morehelp'));

        return $this->notification($maturitywarning, 'notifyproblem');
    }

    protected function warning($message, $type = 'warning') {
        if ($type == 'warning') {
            return $this->notification($message, 'notifywarning');
        } else if ($type == 'error') {
            return $this->notification($message, 'notifyproblem');
        }
    }

    protected function test_site_warning($testsite) {
        if (!$testsite) {
            return '';
        }
        $warningtext = get_string('testsiteupgradewarning', 'admin', $testsite);
        return $this->notification($warningtext, 'notifyproblem');
    }

    protected function release_notes_link() {
        $releasenoteslink = get_string('releasenoteslink', 'admin', 'http://docs.moodle.org/dev/Releases');
        return $this->notification($releasenoteslink, 'notifymessage');
    }

    public function plugins_check_table(core_plugin_manager $pluginman, $version, array $options = array()) {
        $html = parent::plugins_check_table($pluginman, $version, $options);

        $replacements = array(
            'generaltable' => 'table table-striped',
            'status-missing' => 'danger',
            'status-downgrade' => 'danger',
            'status-upgrade' => 'info',
            'status-delete' => 'info',
            'status-new' => 'success',
        );

        $find = array_keys($replacements);
        $replace = array_values($replacements);

        return str_replace($find, $replace, $html);
    }

    public function environment_check_table($result, $environment) {
        $html = parent::environment_check_table($result, $environment);

        $replacements = array(
            '<span class="ok">' => '<span class="label label-success">',
            '<span class="warn">' => '<span class="label label-warning">',
            '<span class="error">' => '<span class="label label-danger">',
            '<p class="ok">' => '<p class="text-success">',
            '<p class="warn">' => '<p class="text-warning">',
            '<p class="error">' => '<p class="text-danger">',
        );

        $find = array_keys($replacements);
        $replace = array_values($replacements);

        return str_replace($find, $replace, $html);
    }
}
