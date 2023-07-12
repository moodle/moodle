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
 * Settings mod form
 *
 * @package     local_edwiserbridge
 * @copyright   2021 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Wisdmlabs
 */

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

/**
 * form shown while adding Edwiser Bridge settings.
 */
class edwiserbridge_navigation_form extends moodleform {

    /**
     * Defining Navigation form.
     */
    public function definition() {
        global $CFG;
        $mform = $this->_form;

        $currenttab = optional_param('tab', '', PARAM_TEXT);

        $summarystatus = 'eb-tabs eb_summary_tab summary_tab_' . eb_get_summary_status();

        $summary = 'summary' === $currenttab ? 'active-tab ' . $summarystatus : $summarystatus;

        $tabs = array(
            array(
                'link'  => $CFG->wwwroot . "/local/edwiserbridge/edwiserbridge.php?tab=settings",
                'label' => get_string('tab_mdl_required_settings', 'local_edwiserbridge'),
                'css'   => 'settings' === $currenttab ? 'active-tab eb-tabs ' : 'eb-tabs',
            ),
            array(
                'link'  => $CFG->wwwroot . "/local/edwiserbridge/edwiserbridge.php?tab=service",
                'label' => get_string('tab_service', 'local_edwiserbridge'),
                'css'   => 'service' === $currenttab ? 'active-tab eb-tabs ' : 'eb-tabs',
            ),
            array(
                'link'  => $CFG->wwwroot . "/local/edwiserbridge/edwiserbridge.php?tab=connection",
                'label' => get_string('tab_conn', 'local_edwiserbridge'),
                'css'   => 'connection' === $currenttab ? 'active-tab eb-tabs ' : 'eb-tabs',
            ),
            array(
                'link'  => $CFG->wwwroot . "/local/edwiserbridge/edwiserbridge.php?tab=synchronization",
                'label' => get_string('tab_synch', 'local_edwiserbridge'),
                'css'   => 'synchronization' === $currenttab ? 'active-tab eb-tabs ' : 'eb-tabs',
            ),
            array(
                'link'  => $CFG->wwwroot . "/local/edwiserbridge/edwiserbridge.php?tab=summary",
                'label' => get_string('summary', 'local_edwiserbridge'),
                'css'   => $summary,
            ),
        );

        $mform->addElement('html', '<div class="eb-tabs-cont">' . $this->print_tabs($tabs) . '</div>');
    }

    /**
     *
     * Preapares and print the list of the tab links.
     *
     * @param array $tabs an array of settings array.
     */
    private function print_tabs($tabs) {
        ob_start();
        foreach ($tabs as $tab) {
            ?>
            <a href="<?php echo $tab['link']; ?>" class="<?php echo $tab['css']; ?>">
                <?php echo $tab['label']; ?>
            </a>
            <?php
        }
        return ob_get_clean();
    }
}
