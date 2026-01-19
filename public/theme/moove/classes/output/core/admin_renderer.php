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
 * Overriden theme boost core renderer.
 *
 * @package    theme_moove
 * @copyright  2024 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\output\core;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/admin/renderer.php');

/**
 * Standard HTML output renderer for core_admin subsystem.
 *
 * @package    core
 * @subpackage admin
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_renderer extends \core_admin_renderer {
    /**
     * Display the admin notifications page.
     * @param int $maturity
     * @param bool $insecuredataroot warn dataroot is invalid
     * @param bool $errorsdisplayed warn invalid dispaly error setting
     * @param bool $cronoverdue warn cron not running
     * @param bool $dbproblems warn db has problems
     * @param bool $maintenancemode warn in maintenance mode
     * @param array|null $availableupdates array of \core\update\info objects or null
     * @param int|null $availableupdatesfetch timestamp of the most recent updates fetch or null (unknown)
     * @param bool $buggyiconvnomb warn iconv problems
     * @param boolean $registered true if the site is registered on Moodle.org
     * @param string[] $cachewarnings An array containing warnings from the Cache API.
     * @param array $eventshandlers Events 1 API handlers.
     * @param bool $themedesignermode Warn about the theme designer mode.
     * @param bool $devlibdir Warn about development libs directory presence.
     * @param bool $mobileconfigured Whether the mobile web services have been enabled
     * @param bool $overridetossl Whether or not ssl is being forced.
     * @param bool $invalidforgottenpasswordurl Whether the forgotten password URL does not link to a valid URL.
     * @param bool $croninfrequent If true, warn that cron hasn't run in the past few minutes
     * @param bool $showcampaigncontent Whether the campaign content should be visible or not.
     * @param bool $showfeedbackencouragement Whether the feedback encouragement content should be displayed or not.
     * @param bool $showservicesandsupport Whether the services and support content should be displayed or not.
     * @param string $xmlrpcwarning XML-RPC deprecation warning message.
     *
     * @return string HTML to output.
     */
    public function admin_notifications_page(
        $maturity,
        $insecuredataroot,
        $errorsdisplayed,
        $cronoverdue,
        $dbproblems,
        $maintenancemode,
        $availableupdates,
        $availableupdatesfetch,
        $buggyiconvnomb,
        $registered,
        array $cachewarnings = [],
        $eventshandlers = 0,
        $themedesignermode = false,
        $devlibdir = false,
        $mobileconfigured = false,
        $overridetossl = false,
        $invalidforgottenpasswordurl = false,
        $croninfrequent = false,
        $showcampaigncontent = false,
        bool $showfeedbackencouragement = false,
        bool $showservicesandsupport = false,
        $xmlrpcwarning = ''
    ) {
        global $CFG;
        $output = '';

        $output .= $this->header();
        $output .= $this->output->heading(get_string('notifications', 'admin'));
        $output .= $this->conectime_services_and_support_content();
        $output .= $this->conectime_partners_content();
        $output .= $this->maturity_info($maturity);
        $output .= empty($CFG->disableupdatenotifications) ?
                        $this->available_updates($availableupdates, $availableupdatesfetch)
                        : '';
        $output .= $this->insecure_dataroot_warning($insecuredataroot);
        $output .= $this->development_libs_directories_warning($devlibdir);
        $output .= $this->themedesignermode_warning($themedesignermode);
        $output .= $this->display_errors_warning($errorsdisplayed);
        $output .= $this->buggy_iconv_warning($buggyiconvnomb);
        $output .= $this->cron_overdue_warning($cronoverdue);
        $output .= $this->cron_infrequent_warning($croninfrequent);
        $output .= $this->db_problems($dbproblems);
        $output .= $this->maintenance_mode_warning($maintenancemode);
        $output .= $this->overridetossl_warning($overridetossl);
        $output .= $this->cache_warnings($cachewarnings);
        $output .= $this->events_handlers($eventshandlers);
        $output .= $this->registration_warning($registered);
        $output .= $this->mobile_configuration_warning($mobileconfigured);
        $output .= $this->forgotten_password_url_warning($invalidforgottenpasswordurl);
        $output .= $this->mnet_deprecation_warning($xmlrpcwarning);
        $output .= $this->userfeedback_encouragement($showfeedbackencouragement);
        $output .= $this->campaign_content($showcampaigncontent);
        // It is illegal and a violation of the GPL to hide, remove or modify this copyright notice.
        $output .= $this->moodle_copyright();

        $output .= $this->footer();

        return $output;
    }

    /**
     * Display services and support content.
     *
     * @return string the campaign content raw html.
     */
    private function conectime_services_and_support_content(): string {
        return $this->render_from_template('theme_moove/moove/conectime_services_and_support_content_banner', []);
    }

    /**
     * Display services and support content.
     *
     * @return string the campaign content raw html.
     */
    private function conectime_partners_content(): string {
        return $this->render_from_template('theme_moove/moove/conectime_partners_banner', []);
    }
}
