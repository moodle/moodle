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
 * Renderer for core_admin subsystem
 *
 * @package    core
 * @subpackage admin
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/pluginlib.php');

/**
 * Standard HTML output renderer for core_admin subsystem
 */
class core_admin_renderer extends plugin_renderer_base {

    /**
     * Display the 'Do you acknowledge the terms of the GPL' page. The first page
     * during install.
     * @return string HTML to output.
     */
    public function install_licence_page() {
        global $CFG;
        $output = '';

        $copyrightnotice = text_to_html(get_string('gpl3'));
        $copyrightnotice = str_replace('target="_blank"', 'onclick="this.target=\'_blank\'"', $copyrightnotice); // extremely ugly validation hack

        $continue = new single_button(new moodle_url('/admin/index.php', array('lang'=>$CFG->lang, 'agreelicense'=>1)), get_string('continue'), 'get');

        $output .= $this->header();
        $output .= $this->heading('<a href="http://moodle.org">Moodle</a> - Modular Object-Oriented Dynamic Learning Environment');
        $output .= $this->heading(get_string('copyrightnotice'));
        $output .= $this->box($copyrightnotice, 'copyrightnotice');
        $output .= html_writer::empty_tag('br');
        $output .= $this->confirm(get_string('doyouagree'), $continue, "http://docs.moodle.org/dev/License");
        $output .= $this->footer();

        return $output;
    }

    /**
     * Display page explaining proper upgrade process,
     * there can not be any PHP file leftovers...
     *
     * @return string HTML to output.
     */
    public function upgrade_stale_php_files_page() {
        $output = '';
        $output .= $this->header();
        $output .= $this->heading(get_string('upgradestalefiles', 'admin'));
        $output .= $this->box_start('generalbox', 'notice');
        $output .= format_text(get_string('upgradestalefilesinfo', 'admin', get_docs_url('Upgrading')), FORMAT_MARKDOWN);
        $output .= html_writer::empty_tag('br');
        $output .= html_writer::tag('div', $this->single_button($this->page->url, get_string('reload'), 'get'), array('class' => 'buttons'));
        $output .= $this->box_end();
        $output .= $this->footer();

        return $output;
    }

    /**
     * Display the 'environment check' page that is displayed during install.
     * @param int $maturity
     * @param boolean $envstatus final result of the check (true/false)
     * @param array $environment_results array of results gathered
     * @param string $release moodle release
     * @return string HTML to output.
     */
    public function install_environment_page($maturity, $envstatus, $environment_results, $release) {
        global $CFG;
        $output = '';

        $output .= $this->header();
        $output .= $this->maturity_warning($maturity);
        $output .= $this->heading("Moodle $release");
        $output .= $this->release_notes_link();

        $output .= $this->environment_check_table($envstatus, $environment_results);

        if (!$envstatus) {
            $output .= $this->upgrade_reload(new moodle_url('/admin/index.php', array('agreelicense' => 1, 'lang' => $CFG->lang)));
        } else {
            $output .= $this->notification(get_string('environmentok', 'admin'), 'notifysuccess');
            $output .= $this->continue_button(new moodle_url('/admin/index.php', array('agreelicense'=>1, 'confirmrelease'=>1, 'lang'=>$CFG->lang)));
        }

        $output .= $this->footer();
        return $output;
    }

    /**
     * Displays the list of plugins with unsatisfied dependencies
     *
     * @param double|string|int $version Moodle on-disk version
     * @param array $failed list of plugins with unsatisfied dependecies
     * @param moodle_url $reloadurl URL of the page to recheck the dependencies
     * @return string HTML
     */
    public function unsatisfied_dependencies_page($version, array $failed, moodle_url $reloadurl) {
        $output = '';

        $output .= $this->header();
        $output .= $this->heading(get_string('pluginscheck', 'admin'));
        $output .= $this->warning(get_string('pluginscheckfailed', 'admin', array('pluginslist' => implode(', ', array_unique($failed)))));
        $output .= $this->plugins_check_table(plugin_manager::instance(), $version, array('xdep' => true));
        $output .= $this->warning(get_string('pluginschecktodo', 'admin'));
        $output .= $this->continue_button($reloadurl);

        $output .= $this->footer();

        return $output;
    }

    /**
     * Display the 'You are about to upgrade Moodle' page. The first page
     * during upgrade.
     * @param string $strnewversion
     * @param int $maturity
     * @param string $testsite
     * @return string HTML to output.
     */
    public function upgrade_confirm_page($strnewversion, $maturity, $testsite) {
        $output = '';

        $continueurl = new moodle_url('index.php', array('confirmupgrade' => 1));
        $cancelurl = new moodle_url('index.php');

        $output .= $this->header();
        $output .= $this->maturity_warning($maturity);
        $output .= $this->test_site_warning($testsite);
        $output .= $this->confirm(get_string('upgradesure', 'admin', $strnewversion), $continueurl, $cancelurl);
        $output .= $this->footer();

        return $output;
    }

    /**
     * Display the environment page during the upgrade process.
     * @param string $release
     * @param boolean $envstatus final result of env check (true/false)
     * @param array $environment_results array of results gathered
     * @return string HTML to output.
     */
    public function upgrade_environment_page($release, $envstatus, $environment_results) {
        global $CFG;
        $output = '';

        $output .= $this->header();
        $output .= $this->heading("Moodle $release");
        $output .= $this->release_notes_link();
        $output .= $this->environment_check_table($envstatus, $environment_results);

        if (!$envstatus) {
            $output .= $this->upgrade_reload(new moodle_url('/admin/index.php'), array('confirmupgrade' => 1));

        } else {
            $output .= $this->notification(get_string('environmentok', 'admin'), 'notifysuccess');

            if (empty($CFG->skiplangupgrade) and current_language() !== 'en') {
                $output .= $this->box(get_string('langpackwillbeupdated', 'admin'), 'generalbox', 'notice');
            }

            $output .= $this->continue_button(new moodle_url('/admin/index.php', array('confirmupgrade' => 1, 'confirmrelease' => 1)));
        }

        $output .= $this->footer();

        return $output;
    }

    /**
     * Display the upgrade page that lists all the plugins that require attention.
     * @param plugin_manager $pluginman provides information about the plugins.
     * @param available_update_checker $checker provides information about available updates.
     * @param int $version the version of the Moodle code from version.php.
     * @param bool $showallplugins
     * @param moodle_url $reloadurl
     * @param moodle_url $continueurl
     * @return string HTML to output.
     */
    public function upgrade_plugin_check_page(plugin_manager $pluginman, available_update_checker $checker,
            $version, $showallplugins, $reloadurl, $continueurl) {
        global $CFG;

        $output = '';

        $output .= $this->header();
        $output .= $this->box_start('generalbox');
        $output .= $this->container_start('generalbox', 'notice');
        $output .= html_writer::tag('p', get_string('pluginchecknotice', 'core_plugin'));
        if (empty($CFG->disableupdatenotifications)) {
            $output .= $this->container_start('checkforupdates');
            $output .= $this->single_button(new moodle_url($reloadurl, array('fetchupdates' => 1)), get_string('checkforupdates', 'core_plugin'));
            if ($timefetched = $checker->get_last_timefetched()) {
                $output .= $this->container(get_string('checkforupdateslast', 'core_plugin',
                    userdate($timefetched, get_string('strftimedatetime', 'core_langconfig'))));
            }
            $output .= $this->container_end();
        }
        $output .= $this->container_end();

        $output .= $this->plugins_check_table($pluginman, $version, array('full' => $showallplugins));
        $output .= $this->box_end();
        $output .= $this->upgrade_reload($reloadurl);

        if ($pluginman->some_plugins_updatable()) {
            $output .= $this->container_start('upgradepluginsinfo');
            $output .= $this->help_icon('upgradepluginsinfo', 'core_admin', get_string('upgradepluginsfirst', 'core_admin'));
            $output .= $this->container_end();
        }

        $button = new single_button($continueurl, get_string('upgradestart', 'admin'), 'get');
        $button->class = 'continuebutton';
        $output .= $this->render($button);
        $output .= $this->footer();

        return $output;
    }

    /**
     * Prints a page with a summary of plugin deployment to be confirmed.
     *
     * @param available_update_deployer $deployer
     * @param array $data deployer's data package as returned by {@link available_update_deployer::submitted_data()}
     * @return string
     */
    public function upgrade_plugin_confirm_deploy_page(available_update_deployer $deployer, array $data) {

        if (!$deployer->initialized()) {
            throw new coding_exception('Unable to render a page for non-initialized deployer.');
        }

        if (empty($data['updateinfo'])) {
            throw new coding_exception('Missing required data component.');
        }

        $updateinfo = $data['updateinfo'];

        $output  = '';
        $output .= $this->header();
        $output .= $this->container_start('generalbox updateplugin', 'notice');

        $a = new stdClass();
        if (get_string_manager()->string_exists('pluginname', $updateinfo->component)) {
            $a->name = get_string('pluginname', $updateinfo->component);
        } else {
            $a->name = $updateinfo->component;
        }

        if (isset($updateinfo->release)) {
            $a->version = $updateinfo->release . ' (' . $updateinfo->version . ')';
        } else {
            $a->version = $updateinfo->version;
        }
        $a->url = $updateinfo->download;

        $output .= $this->output->heading(get_string('updatepluginconfirm', 'core_plugin'));
        $output .= $this->output->container(format_text(get_string('updatepluginconfirminfo', 'core_plugin', $a)), 'updatepluginconfirminfo');
        $output .= $this->output->container(get_string('updatepluginconfirmwarning', 'core_plugin', 'updatepluginconfirmwarning'));

        if ($repotype = $deployer->plugin_external_source($data['updateinfo'])) {
            $output .= $this->output->container(get_string('updatepluginconfirmexternal', 'core_plugin', $repotype), 'updatepluginconfirmexternal');
        }

        $widget = $deployer->make_execution_widget($data['updateinfo'], $data['returnurl']);
        $output .= $this->output->render($widget);

        $output .= $this->output->single_button($data['callerurl'], get_string('cancel', 'core'), 'get');

        $output .= $this->container_end();
        $output .= $this->footer();

        return $output;
    }

    /**
     * Display the admin notifications page.
     * @param int $maturity
     * @param bool $insecuredataroot warn dataroot is invalid
     * @param bool $errorsdisplayed warn invalid dispaly error setting
     * @param bool $cronoverdue warn cron not running
     * @param bool $dbproblems warn db has problems
     * @param bool $maintenancemode warn in maintenance mode
     * @param bool $buggyiconvnomb warn iconv problems
     * @param array|null $availableupdates array of available_update_info objects or null
     * @param int|null $availableupdatesfetch timestamp of the most recent updates fetch or null (unknown)
     *
     * @return string HTML to output.
     */
    public function admin_notifications_page($maturity, $insecuredataroot, $errorsdisplayed,
            $cronoverdue, $dbproblems, $maintenancemode, $availableupdates, $availableupdatesfetch,
            $buggyiconvnomb, $registered) {
        global $CFG;
        $output = '';

        $output .= $this->header();
        $output .= $this->maturity_info($maturity);
        $output .= empty($CFG->disableupdatenotifications) ? $this->available_updates($availableupdates, $availableupdatesfetch) : '';
        $output .= $this->insecure_dataroot_warning($insecuredataroot);
        $output .= $this->display_errors_warning($errorsdisplayed);
        $output .= $this->buggy_iconv_warning($buggyiconvnomb);
        $output .= $this->cron_overdue_warning($cronoverdue);
        $output .= $this->db_problems($dbproblems);
        $output .= $this->maintenance_mode_warning($maintenancemode);
        $output .= $this->registration_warning($registered);

        //////////////////////////////////////////////////////////////////////////////////////////////////
        ////  IT IS ILLEGAL AND A VIOLATION OF THE GPL TO HIDE, REMOVE OR MODIFY THIS COPYRIGHT NOTICE ///
        $output .= $this->moodle_copyright();
        //////////////////////////////////////////////////////////////////////////////////////////////////

        $output .= $this->footer();

        return $output;
    }

    /**
     * Display the plugin management page (admin/plugins.php).
     *
     * The filtering options array may contain following items:
     *  bool contribonly - show only contributed extensions
     *  bool updatesonly - show only plugins with an available update
     *
     * @param plugin_manager $pluginman
     * @param available_update_checker $checker
     * @param array $options filtering options
     * @return string HTML to output.
     */
    public function plugin_management_page(plugin_manager $pluginman, available_update_checker $checker, array $options = array()) {
        global $CFG;

        $output = '';

        $output .= $this->header();
        $output .= $this->heading(get_string('pluginsoverview', 'core_admin'));
        $output .= $this->plugins_overview_panel($pluginman, $options);

        if (empty($CFG->disableupdatenotifications)) {
            $output .= $this->container_start('checkforupdates');
            $output .= $this->single_button(
                new moodle_url($this->page->url, array_merge($options, array('fetchremote' => 1))),
                get_string('checkforupdates', 'core_plugin')
            );
            if ($timefetched = $checker->get_last_timefetched()) {
                $output .= $this->container(get_string('checkforupdateslast', 'core_plugin',
                    userdate($timefetched, get_string('strftimedatetime', 'core_langconfig'))));
            }
            $output .= $this->container_end();
        }

        $output .= $this->box($this->plugins_control_panel($pluginman, $options), 'generalbox');
        $output .= $this->footer();

        return $output;
    }

    /**
     * Display a page to confirm the plugin uninstallation.
     *
     * @param plugin_manager $pluginman
     * @param plugin_info $pluginfo
     * @param moodle_url $continueurl URL to continue after confirmation
     * @return string
     */
    public function plugin_uninstall_confirm_page(plugin_manager $pluginman, plugininfo_base $pluginfo, moodle_url $continueurl) {
        $output = '';

        $pluginname = $pluginman->plugin_name($pluginfo->component);

        $output .= $this->output->header();
        $output .= $this->output->heading(get_string('uninstalling', 'core_plugin', array('name' => $pluginname)));
        $output .= $this->output->confirm(get_string('uninstallconfirm', 'core_plugin', array('name' => $pluginname)),
            $continueurl, $this->page->url);
        $output .= $this->output->footer();

        return $output;
    }

    /**
     * Display a page with results of plugin uninstallation and offer removal of plugin files.
     *
     * @param plugin_manager $pluginman
     * @param plugin_info $pluginfo
     * @param progress_trace_buffer $progress
     * @param moodle_url $continueurl URL to continue to remove the plugin folder
     * @return string
     */
    public function plugin_uninstall_results_removable_page(plugin_manager $pluginman, plugininfo_base $pluginfo,
                                                            progress_trace_buffer $progress, moodle_url $continueurl) {
        $output = '';

        $pluginname = $pluginman->plugin_name($pluginfo->component);

        $output .= $this->output->header();
        $output .= $this->output->heading(get_string('uninstalling', 'core_plugin', array('name' => $pluginname)));

        $output .= $this->output->box($progress->get_buffer(), 'generalbox uninstallresultmessage');

        $confirm = $this->output->container(get_string('uninstalldeleteconfirm', 'core_plugin',
            array('name' => $pluginname, 'rootdir' => $pluginfo->rootdir)), 'uninstalldeleteconfirm');

        if ($repotype = $pluginman->plugin_external_source($pluginfo->component)) {
            $confirm .= $this->output->container(get_string('uninstalldeleteconfirmexternal', 'core_plugin', $repotype),
                'uninstalldeleteconfirmexternal');
        }

        $output .= $this->output->confirm($confirm, $continueurl, $this->page->url);
        $output .= $this->output->footer();

        return $output;
    }

    /**
     * Display a page with results of plugin uninstallation and inform about the need to remove plugin files manually.
     *
     * @param plugin_manager $pluginman
     * @param plugin_info $pluginfo
     * @param progress_trace_buffer $progress
     * @return string
     */
    public function plugin_uninstall_results_page(plugin_manager $pluginman, plugininfo_base $pluginfo, progress_trace_buffer $progress) {
        $output = '';

        $pluginname = $pluginman->plugin_name($pluginfo->component);

        $output .= $this->output->header();
        $output .= $this->output->heading(get_string('uninstalling', 'core_plugin', array('name' => $pluginname)));

        $output .= $this->output->box($progress->get_buffer(), 'generalbox uninstallresultmessage');

        $output .= $this->output->box(get_string('uninstalldelete', 'core_plugin',
            array('name' => $pluginname, 'rootdir' => $pluginfo->rootdir)), 'generalbox uninstalldelete');
        $output .= $this->output->continue_button($this->page->url);
        $output .= $this->output->footer();

        return $output;
    }

    /**
     * Display the plugin management page (admin/environment.php).
     * @param array $versions
     * @param string $version
     * @param boolean $envstatus final result of env check (true/false)
     * @param array $environment_results array of results gathered
     * @return string HTML to output.
     */
    public function environment_check_page($versions, $version, $envstatus, $environment_results) {
        $output = '';
        $output .= $this->header();

        // Print the component download link
        $output .= html_writer::tag('div', html_writer::link(
                    new moodle_url('/admin/environment.php', array('action' => 'updatecomponent', 'sesskey' => sesskey())),
                    get_string('updatecomponent', 'admin')),
                array('class' => 'reportlink'));

        // Heading.
        $output .= $this->heading(get_string('environment', 'admin'));

        // Box with info and a menu to choose the version.
        $output .= $this->box_start();
        $output .= html_writer::tag('div', get_string('adminhelpenvironment'));
        $select = new single_select(new moodle_url('/admin/environment.php'), 'version', $versions, $version, null);
        $select->label = get_string('moodleversion');
        $output .= $this->render($select);
        $output .= $this->box_end();

        // The results
        $output .= $this->environment_check_table($envstatus, $environment_results);

        $output .= $this->footer();
        return $output;
    }

    /**
     * Output a warning message, of the type that appears on the admin notifications page.
     * @param string $message the message to display.
     * @param string $type type class
     * @return string HTML to output.
     */
    protected function warning($message, $type = 'warning') {
        return $this->box($message, 'generalbox admin' . $type);
    }

    /**
     * Render an appropriate message if dataroot is insecure.
     * @param bool $insecuredataroot
     * @return string HTML to output.
     */
    protected function insecure_dataroot_warning($insecuredataroot) {
        global $CFG;

        if ($insecuredataroot == INSECURE_DATAROOT_WARNING) {
            return $this->warning(get_string('datarootsecuritywarning', 'admin', $CFG->dataroot));

        } else if ($insecuredataroot == INSECURE_DATAROOT_ERROR) {
            return $this->warning(get_string('datarootsecurityerror', 'admin', $CFG->dataroot), 'error');

        } else {
            return '';
        }
    }

    /**
     * Render an appropriate message if dataroot is insecure.
     * @param bool $errorsdisplayed
     * @return string HTML to output.
     */
    protected function display_errors_warning($errorsdisplayed) {
        if (!$errorsdisplayed) {
            return '';
        }

        return $this->warning(get_string('displayerrorswarning', 'admin'));
    }

    /**
     * Render an appropriate message if iconv is buggy and mbstring missing.
     * @param bool $buggyiconvnomb
     * @return string HTML to output.
     */
    protected function buggy_iconv_warning($buggyiconvnomb) {
        if (!$buggyiconvnomb) {
            return '';
        }

        return $this->warning(get_string('warningiconvbuggy', 'admin'));
    }

    /**
     * Render an appropriate message if cron has not been run recently.
     * @param bool $cronoverdue
     * @return string HTML to output.
     */
    public function cron_overdue_warning($cronoverdue) {
        if (!$cronoverdue) {
            return '';
        }

        return $this->warning(get_string('cronwarning', 'admin') . '&nbsp;' .
                $this->help_icon('cron', 'admin'));
    }

    /**
     * Render an appropriate message if there are any problems with the DB set-up.
     * @param bool $dbproblems
     * @return string HTML to output.
     */
    public function db_problems($dbproblems) {
        if (!$dbproblems) {
            return '';
        }

        return $this->warning($dbproblems);
    }

    /**
     * Render an appropriate message if the site in in maintenance mode.
     * @param bool $maintenancemode
     * @return string HTML to output.
     */
    public function maintenance_mode_warning($maintenancemode) {
        if (!$maintenancemode) {
            return '';
        }

        $url = new moodle_url('/admin/settings.php', array('section' => 'maintenancemode'));
        $url = $url->out(); // get_string() does not support objects in params

        return $this->warning(get_string('sitemaintenancewarning2', 'admin', $url));
    }

    /**
     * Display a warning about installing development code if necesary.
     * @param int $maturity
     * @return string HTML to output.
     */
    protected function maturity_warning($maturity) {
        if ($maturity == MATURITY_STABLE) {
            return ''; // No worries.
        }

        $maturitylevel = get_string('maturity' . $maturity, 'admin');
        return $this->box(
                    $this->container(get_string('maturitycorewarning', 'admin', $maturitylevel)) .
                    $this->container($this->doc_link('admin/versions', get_string('morehelp'))),
                'generalbox maturitywarning');
    }

    /*
     * If necessary, displays a warning about upgrading a test site.
     *
     * @param string $testsite
     * @return string HTML
     */
    protected function test_site_warning($testsite) {

        if (!$testsite) {
            return '';
        }

        return $this->box(
            $this->container(get_string('testsiteupgradewarning', 'admin', $testsite)),
            'generalbox testsitewarning'
        );
    }

    /**
     * Output the copyright notice.
     * @return string HTML to output.
     */
    protected function moodle_copyright() {
        global $CFG;

        //////////////////////////////////////////////////////////////////////////////////////////////////
        ////  IT IS ILLEGAL AND A VIOLATION OF THE GPL TO HIDE, REMOVE OR MODIFY THIS COPYRIGHT NOTICE ///
        $copyrighttext = '<a href="http://moodle.org/">Moodle</a> '.
                         '<a href="http://docs.moodle.org/dev/Releases" title="'.$CFG->version.'">'.$CFG->release.'</a><br />'.
                         'Copyright &copy; 1999 onwards, Martin Dougiamas<br />'.
                         'and <a href="http://moodle.org/dev">many other contributors</a>.<br />'.
                         '<a href="http://docs.moodle.org/dev/License">GNU Public License</a>';
        //////////////////////////////////////////////////////////////////////////////////////////////////

        return $this->box($copyrighttext, 'copyright');
    }

    /**
     * Display a warning about installing development code if necesary.
     * @param int $maturity
     * @return string HTML to output.
     */
    protected function maturity_info($maturity) {
        if ($maturity == MATURITY_STABLE) {
            return ''; // No worries.
        }

        $maturitylevel = get_string('maturity' . $maturity, 'admin');
        return $this->box(
                    get_string('maturitycoreinfo', 'admin', $maturitylevel) . ' ' .
                    $this->doc_link('admin/versions', get_string('morehelp')),
                'generalbox adminwarning maturityinfo maturity'.$maturity);
    }

    /**
     * Displays the info about available Moodle core and plugin updates
     *
     * The structure of the $updates param has changed since 2.4. It contains not only updates
     * for the core itself, but also for all other installed plugins.
     *
     * @param array|null $updates array of (string)component => array of available_update_info objects or null
     * @param int|null $fetch timestamp of the most recent updates fetch or null (unknown)
     * @return string
     */
    protected function available_updates($updates, $fetch) {

        $updateinfo = $this->box_start('generalbox adminwarning availableupdatesinfo');
        $someupdateavailable = false;
        if (is_array($updates)) {
            if (is_array($updates['core'])) {
                $someupdateavailable = true;
                $updateinfo .= $this->heading(get_string('updateavailable', 'core_admin'), 3);
                foreach ($updates['core'] as $update) {
                    $updateinfo .= $this->moodle_available_update_info($update);
                }
            }
            unset($updates['core']);
            // If something has left in the $updates array now, it is updates for plugins.
            if (!empty($updates)) {
                $someupdateavailable = true;
                $updateinfo .= $this->heading(get_string('updateavailableforplugin', 'core_admin'), 3);
                $pluginsoverviewurl = new moodle_url('/admin/plugins.php', array('updatesonly' => 1));
                $updateinfo .= $this->container(get_string('pluginsoverviewsee', 'core_admin',
                    array('url' => $pluginsoverviewurl->out())));
            }
        }

        if (!$someupdateavailable) {
            $now = time();
            if ($fetch and ($fetch <= $now) and ($now - $fetch < HOURSECS)) {
                $updateinfo .= $this->heading(get_string('updateavailablenot', 'core_admin'), 3);
            }
        }

        $updateinfo .= $this->container_start('checkforupdates');
        $fetchurl = new moodle_url('/admin/index.php', array('fetchupdates' => 1, 'sesskey' => sesskey(), 'cache' => 1));
        $updateinfo .= $this->single_button($fetchurl, get_string('checkforupdates', 'core_plugin'));
        if ($fetch) {
            $updateinfo .= $this->container(get_string('checkforupdateslast', 'core_plugin',
                userdate($fetch, get_string('strftimedatetime', 'core_langconfig'))));
        }
        $updateinfo .= $this->container_end();

        $updateinfo .= $this->box_end();

        return $updateinfo;
    }

    /**
     * Display a warning about not being registered on Moodle.org if necesary.
     *
     * @param boolean $registered true if the site is registered on Moodle.org
     * @return string HTML to output.
     */
    protected function registration_warning($registered) {

        if (!$registered) {

            $registerbutton = $this->single_button(new moodle_url('registration/register.php',
                    array('huburl' =>  HUB_MOODLEORGHUBURL, 'hubname' => 'Moodle.org')),
                    get_string('register', 'admin'));

            return $this->warning( get_string('registrationwarning', 'admin')
                    . '&nbsp;' . $this->help_icon('registration', 'admin') . $registerbutton );
        }

        return '';
    }

    /**
     * Helper method to render the information about the available Moodle update
     *
     * @param available_update_info $updateinfo information about the available Moodle core update
     */
    protected function moodle_available_update_info(available_update_info $updateinfo) {

        $boxclasses = 'moodleupdateinfo';
        $info = array();

        if (isset($updateinfo->release)) {
            $info[] = html_writer::tag('span', get_string('updateavailable_release', 'core_admin', $updateinfo->release),
                array('class' => 'info release'));
        }

        if (isset($updateinfo->version)) {
            $info[] = html_writer::tag('span', get_string('updateavailable_version', 'core_admin', $updateinfo->version),
                array('class' => 'info version'));
        }

        if (isset($updateinfo->maturity)) {
            $info[] = html_writer::tag('span', get_string('maturity'.$updateinfo->maturity, 'core_admin'),
                array('class' => 'info maturity'));
            $boxclasses .= ' maturity'.$updateinfo->maturity;
        }

        if (isset($updateinfo->download)) {
            $info[] = html_writer::link($updateinfo->download, get_string('download'), array('class' => 'info download'));
        }

        if (isset($updateinfo->url)) {
            $info[] = html_writer::link($updateinfo->url, get_string('updateavailable_moreinfo', 'core_plugin'),
                array('class' => 'info more'));
        }

        $box  = $this->output->box_start($boxclasses);
        $box .= $this->output->box(implode(html_writer::tag('span', ' ', array('class' => 'separator')), $info), '');
        $box .= $this->output->box_end();

        return $box;
    }

    /**
     * Display a link to the release notes.
     * @return string HTML to output.
     */
    protected function release_notes_link() {
        $releasenoteslink = get_string('releasenoteslink', 'admin', 'http://docs.moodle.org/dev/Releases');
        $releasenoteslink = str_replace('target="_blank"', 'onclick="this.target=\'_blank\'"', $releasenoteslink); // extremely ugly validation hack
        return $this->box($releasenoteslink, 'generalbox releasenoteslink');
    }

    /**
     * Display the reload link that appears on several upgrade/install pages.
     * @return string HTML to output.
     */
    function upgrade_reload($url) {
        return html_writer::empty_tag('br') .
                html_writer::tag('div',
                    html_writer::link($url, $this->pix_icon('i/reload', '', '', array('class' => 'icon icon-pre')) .
                            get_string('reload'), array('title' => get_string('reload'))),
                array('class' => 'continuebutton')) . html_writer::empty_tag('br');
    }

    /**
     * Displays all known plugins and information about their installation or upgrade
     *
     * This default implementation renders all plugins into one big table. The rendering
     * options support:
     *     (bool)full = false: whether to display up-to-date plugins, too
     *     (bool)xdep = false: display the plugins with unsatisified dependecies only
     *
     * @param plugin_manager $pluginman provides information about the plugins.
     * @param int $version the version of the Moodle code from version.php.
     * @param array $options rendering options
     * @return string HTML code
     */
    public function plugins_check_table(plugin_manager $pluginman, $version, array $options = array()) {
        global $CFG;

        $plugininfo = $pluginman->get_plugins();

        if (empty($plugininfo)) {
            return '';
        }

        $options['full'] = isset($options['full']) ? (bool)$options['full'] : false;
        $options['xdep'] = isset($options['xdep']) ? (bool)$options['xdep'] : false;

        $table = new html_table();
        $table->id = 'plugins-check';
        $table->head = array(
            get_string('displayname', 'core_plugin'),
            get_string('rootdir', 'core_plugin'),
            get_string('source', 'core_plugin'),
            get_string('versiondb', 'core_plugin'),
            get_string('versiondisk', 'core_plugin'),
            get_string('requires', 'core_plugin'),
            get_string('status', 'core_plugin'),
        );
        $table->colclasses = array(
            'displayname', 'rootdir', 'source', 'versiondb', 'versiondisk', 'requires', 'status',
        );
        $table->data = array();

        $numofhighlighted = array();    // number of highlighted rows per this subsection

        foreach ($plugininfo as $type => $plugins) {

            $header = new html_table_cell($pluginman->plugintype_name_plural($type));
            $header->header = true;
            $header->colspan = count($table->head);
            $header = new html_table_row(array($header));
            $header->attributes['class'] = 'plugintypeheader type-' . $type;

            $numofhighlighted[$type] = 0;

            if (empty($plugins) and $options['full']) {
                $msg = new html_table_cell(get_string('noneinstalled', 'core_plugin'));
                $msg->colspan = count($table->head);
                $row = new html_table_row(array($msg));
                $row->attributes['class'] .= 'msg msg-noneinstalled';
                $table->data[] = $header;
                $table->data[] = $row;
                continue;
            }

            $plugintyperows = array();

            foreach ($plugins as $name => $plugin) {
                $row = new html_table_row();
                $row->attributes['class'] = 'type-' . $plugin->type . ' name-' . $plugin->type . '_' . $plugin->name;

                if ($this->page->theme->resolve_image_location('icon', $plugin->type . '_' . $plugin->name, null)) {
                    $icon = $this->output->pix_icon('icon', '', $plugin->type . '_' . $plugin->name, array('class' => 'smallicon pluginicon'));
                } else {
                    $icon = $this->output->pix_icon('spacer', '', 'moodle', array('class' => 'smallicon pluginicon noicon'));
                }
                $displayname  = $icon . ' ' . $plugin->displayname;
                $displayname = new html_table_cell($displayname);

                $rootdir = new html_table_cell($plugin->get_dir());

                if ($isstandard = $plugin->is_standard()) {
                    $row->attributes['class'] .= ' standard';
                    $source = new html_table_cell(get_string('sourcestd', 'core_plugin'));
                } else {
                    $row->attributes['class'] .= ' extension';
                    $source = new html_table_cell(get_string('sourceext', 'core_plugin'));
                }

                $versiondb = new html_table_cell($plugin->versiondb);
                $versiondisk = new html_table_cell($plugin->versiondisk);

                $statuscode = $plugin->get_status();
                $row->attributes['class'] .= ' status-' . $statuscode;
                $status = get_string('status_' . $statuscode, 'core_plugin');

                $availableupdates = $plugin->available_updates();
                if (!empty($availableupdates) and empty($CFG->disableupdatenotifications)) {
                    foreach ($availableupdates as $availableupdate) {
                        $status .= $this->plugin_available_update_info($availableupdate);
                    }
                }

                $status = new html_table_cell($status);

                $requires = new html_table_cell($this->required_column($plugin, $pluginman, $version));

                $statusisboring = in_array($statuscode, array(
                        plugin_manager::PLUGIN_STATUS_NODB, plugin_manager::PLUGIN_STATUS_UPTODATE));

                $coredependency = $plugin->is_core_dependency_satisfied($version);
                $otherpluginsdependencies = $pluginman->are_dependencies_satisfied($plugin->get_other_required_plugins());
                $dependenciesok = $coredependency && $otherpluginsdependencies;

                if ($options['xdep']) {
                    // we want to see only plugins with failed dependencies
                    if ($dependenciesok) {
                        continue;
                    }

                } else if ($statusisboring and $dependenciesok and empty($availableupdates)) {
                    // no change is going to happen to the plugin - display it only
                    // if the user wants to see the full list
                    if (empty($options['full'])) {
                        continue;
                    }
                }

                // ok, the plugin should be displayed
                $numofhighlighted[$type]++;

                $row->cells = array($displayname, $rootdir, $source,
                    $versiondb, $versiondisk, $requires, $status);
                $plugintyperows[] = $row;
            }

            if (empty($numofhighlighted[$type]) and empty($options['full'])) {
                continue;
            }

            $table->data[] = $header;
            $table->data = array_merge($table->data, $plugintyperows);
        }

        $sumofhighlighted = array_sum($numofhighlighted);

        if ($options['xdep']) {
            // we do not want to display no heading and links in this mode
            $out = '';

        } else if ($sumofhighlighted == 0) {
            $out  = $this->output->container_start('nonehighlighted', 'plugins-check-info');
            $out .= $this->output->heading(get_string('nonehighlighted', 'core_plugin'));
            if (empty($options['full'])) {
                $out .= html_writer::link(new moodle_url('/admin/index.php',
                    array('confirmupgrade' => 1, 'confirmrelease' => 1, 'showallplugins' => 1)),
                    get_string('nonehighlightedinfo', 'core_plugin'));
            }
            $out .= $this->output->container_end();

        } else {
            $out  = $this->output->container_start('somehighlighted', 'plugins-check-info');
            $out .= $this->output->heading(get_string('somehighlighted', 'core_plugin', $sumofhighlighted));
            if (empty($options['full'])) {
                $out .= html_writer::link(new moodle_url('/admin/index.php',
                    array('confirmupgrade' => 1, 'confirmrelease' => 1, 'showallplugins' => 1)),
                    get_string('somehighlightedinfo', 'core_plugin'));
            } else {
                $out .= html_writer::link(new moodle_url('/admin/index.php',
                    array('confirmupgrade' => 1, 'confirmrelease' => 1, 'showallplugins' => 0)),
                    get_string('somehighlightedonly', 'core_plugin'));
            }
            $out .= $this->output->container_end();
        }

        if ($sumofhighlighted > 0 or $options['full']) {
            $out .= html_writer::table($table);
        }

        return $out;
    }

    /**
     * Formats the information that needs to go in the 'Requires' column.
     * @param plugininfo_base $plugin the plugin we are rendering the row for.
     * @param plugin_manager $pluginman provides data on all the plugins.
     * @param string $version
     * @return string HTML code
     */
    protected function required_column(plugininfo_base $plugin, plugin_manager $pluginman, $version) {
        $requires = array();

        if (!empty($plugin->versionrequires)) {
            if ($plugin->versionrequires <= $version) {
                $class = 'requires-ok';
            } else {
                $class = 'requires-failed';
            }
            $requires[] = html_writer::tag('li',
                get_string('moodleversion', 'core_plugin', $plugin->versionrequires),
                array('class' => $class));
        }

        foreach ($plugin->get_other_required_plugins() as $component => $requiredversion) {
            $ok = true;
            $otherplugin = $pluginman->get_plugin_info($component);

            if (is_null($otherplugin)) {
                $ok = false;
            } else if ($requiredversion != ANY_VERSION and $otherplugin->versiondisk < $requiredversion) {
                $ok = false;
            }

            if ($ok) {
                $class = 'requires-ok';
            } else {
                $class = 'requires-failed';
            }

            if ($requiredversion != ANY_VERSION) {
                $str = 'otherpluginversion';
            } else {
                $str = 'otherplugin';
            }
            $componenturl = new moodle_url('https://moodle.org/plugins/view.php?plugin='.$component);
            $componenturl = html_writer::tag('a', $component, array('href' => $componenturl->out()));
            $requires[] = html_writer::tag('li',
                    get_string($str, 'core_plugin',
                            array('component' => $componenturl, 'version' => $requiredversion)),
                    array('class' => $class));
        }

        if (!$requires) {
            return '';
        }
        return html_writer::tag('ul', implode("\n", $requires));
    }

    /**
     * Prints an overview about the plugins - number of installed, number of extensions etc.
     *
     * @param plugin_manager $pluginman provides information about the plugins
     * @param array $options filtering options
     * @return string as usually
     */
    public function plugins_overview_panel(plugin_manager $pluginman, array $options = array()) {
        global $CFG;

        $plugininfo = $pluginman->get_plugins();

        $numtotal = $numdisabled = $numextension = $numupdatable = 0;

        foreach ($plugininfo as $type => $plugins) {
            foreach ($plugins as $name => $plugin) {
                if ($plugin->get_status() === plugin_manager::PLUGIN_STATUS_MISSING) {
                    continue;
                }
                $numtotal++;
                if ($plugin->is_enabled() === false) {
                    $numdisabled++;
                }
                if (!$plugin->is_standard()) {
                    $numextension++;
                }
                if (empty($CFG->disableupdatenotifications) and $plugin->available_updates()) {
                    $numupdatable++;
                }
            }
        }

        $info = array();
        $filter = array();
        $somefilteractive = false;
        $info[] = html_writer::tag('span', get_string('numtotal', 'core_plugin', $numtotal), array('class' => 'info total'));
        $info[] = html_writer::tag('span', get_string('numdisabled', 'core_plugin', $numdisabled), array('class' => 'info disabled'));
        $info[] = html_writer::tag('span', get_string('numextension', 'core_plugin', $numextension), array('class' => 'info extension'));
        if ($numextension > 0) {
            if (empty($options['contribonly'])) {
                $filter[] = html_writer::link(
                    new moodle_url($this->page->url, array('contribonly' => 1)),
                    get_string('filtercontribonly', 'core_plugin'),
                    array('class' => 'filter-item show-contribonly')
                );
            } else {
                $filter[] = html_writer::tag('span', get_string('filtercontribonlyactive', 'core_plugin'),
                    array('class' => 'filter-item active show-contribonly'));
                $somefilteractive = true;
            }
        }
        if ($numupdatable > 0) {
            $info[] = html_writer::tag('span', get_string('numupdatable', 'core_plugin', $numupdatable), array('class' => 'info updatable'));
            if (empty($options['updatesonly'])) {
                $filter[] = html_writer::link(
                    new moodle_url($this->page->url, array('updatesonly' => 1)),
                    get_string('filterupdatesonly', 'core_plugin'),
                    array('class' => 'filter-item show-updatesonly')
                );
            } else {
                $filter[] = html_writer::tag('span', get_string('filterupdatesonlyactive', 'core_plugin'),
                    array('class' => 'filter-item active show-updatesonly'));
                $somefilteractive = true;
            }
        }
        if ($somefilteractive) {
            $filter[] = html_writer::link($this->page->url, get_string('filterall', 'core_plugin'), array('class' => 'filter-item show-all'));
        }

        $output  = $this->output->box(implode(html_writer::tag('span', ' ', array('class' => 'separator')), $info), '', 'plugins-overview-panel');

        if (!empty($filter)) {
            $output .= $this->output->box(implode(html_writer::tag('span', ' ', array('class' => 'separator')), $filter), '', 'plugins-overview-filter');
        }

        return $output;
    }

    /**
     * Displays all known plugins and links to manage them
     *
     * This default implementation renders all plugins into one big table.
     *
     * @param plugin_manager $pluginman provides information about the plugins.
     * @param array $options filtering options
     * @return string HTML code
     */
    public function plugins_control_panel(plugin_manager $pluginman, array $options = array()) {
        global $CFG;

        $plugininfo = $pluginman->get_plugins();

        // Filter the list of plugins according the options.
        if (!empty($options['updatesonly'])) {
            $updateable = array();
            foreach ($plugininfo as $plugintype => $pluginnames) {
                foreach ($pluginnames as $pluginname => $pluginfo) {
                    if (!empty($pluginfo->availableupdates)) {
                        foreach ($pluginfo->availableupdates as $pluginavailableupdate) {
                            if ($pluginavailableupdate->version > $pluginfo->versiondisk) {
                                $updateable[$plugintype][$pluginname] = $pluginfo;
                            }
                        }
                    }
                }
            }
            $plugininfo = $updateable;
        }

        if (!empty($options['contribonly'])) {
            $contribs = array();
            foreach ($plugininfo as $plugintype => $pluginnames) {
                foreach ($pluginnames as $pluginname => $pluginfo) {
                    if (!$pluginfo->is_standard()) {
                        $contribs[$plugintype][$pluginname] = $pluginfo;
                    }
                }
            }
            $plugininfo = $contribs;
        }

        if (empty($plugininfo)) {
            return '';
        }

        $table = new html_table();
        $table->id = 'plugins-control-panel';
        $table->head = array(
            get_string('displayname', 'core_plugin'),
            get_string('source', 'core_plugin'),
            get_string('version', 'core_plugin'),
            get_string('availability', 'core_plugin'),
            get_string('actions', 'core_plugin'),
            get_string('notes','core_plugin'),
        );
        $table->headspan = array(1, 1, 1, 1, 2, 1);
        $table->colclasses = array(
            'pluginname', 'source', 'version', 'availability', 'settings', 'uninstall', 'notes'
        );

        foreach ($plugininfo as $type => $plugins) {

            $header = new html_table_cell($pluginman->plugintype_name_plural($type));
            $header->header = true;
            $header->colspan = array_sum($table->headspan);
            $header = new html_table_row(array($header));
            $header->attributes['class'] = 'plugintypeheader type-' . $type;
            $table->data[] = $header;

            if (empty($plugins)) {
                $msg = new html_table_cell(get_string('noneinstalled', 'core_plugin'));
                $msg->colspan = array_sum($table->headspan);
                $row = new html_table_row(array($msg));
                $row->attributes['class'] .= 'msg msg-noneinstalled';
                $table->data[] = $row;
                continue;
            }

            foreach ($plugins as $name => $plugin) {
                $row = new html_table_row();
                $row->attributes['class'] = 'type-' . $plugin->type . ' name-' . $plugin->type . '_' . $plugin->name;

                if ($this->page->theme->resolve_image_location('icon', $plugin->type . '_' . $plugin->name)) {
                    $icon = $this->output->pix_icon('icon', '', $plugin->type . '_' . $plugin->name, array('class' => 'icon pluginicon'));
                } else {
                    $icon = $this->output->pix_icon('spacer', '', 'moodle', array('class' => 'icon pluginicon noicon'));
                }
                $status = $plugin->get_status();
                $row->attributes['class'] .= ' status-'.$status;
                if ($status === plugin_manager::PLUGIN_STATUS_MISSING) {
                    $msg = html_writer::tag('span', get_string('status_missing', 'core_plugin'), array('class' => 'statusmsg'));
                } else if ($status === plugin_manager::PLUGIN_STATUS_NEW) {
                    $msg = html_writer::tag('span', get_string('status_new', 'core_plugin'), array('class' => 'statusmsg'));
                } else {
                    $msg = '';
                }
                $pluginname  = html_writer::tag('div', $icon . '' . $plugin->displayname . ' ' . $msg, array('class' => 'displayname')).
                               html_writer::tag('div', $plugin->component, array('class' => 'componentname'));
                $pluginname  = new html_table_cell($pluginname);

                if ($plugin->is_standard()) {
                    $row->attributes['class'] .= ' standard';
                    $source = new html_table_cell(get_string('sourcestd', 'core_plugin'));
                } else {
                    $row->attributes['class'] .= ' extension';
                    $source = new html_table_cell(get_string('sourceext', 'core_plugin'));
                }

                $version = new html_table_cell($plugin->versiondb);

                $isenabled = $plugin->is_enabled();
                if (is_null($isenabled)) {
                    $availability = new html_table_cell('');
                } else if ($isenabled) {
                    $row->attributes['class'] .= ' enabled';
                    $availability = new html_table_cell(get_string('pluginenabled', 'core_plugin'));
                } else {
                    $row->attributes['class'] .= ' disabled';
                    $availability = new html_table_cell(get_string('plugindisabled', 'core_plugin'));
                }

                $settingsurl = $plugin->get_settings_url();
                if (!is_null($settingsurl)) {
                    $settings = html_writer::link($settingsurl, get_string('settings', 'core_plugin'), array('class' => 'settings'));
                } else {
                    $settings = '';
                }
                $settings = new html_table_cell($settings);

                if ($pluginman->can_uninstall_plugin($plugin->component)) {
                    $uninstallurl = $plugin->get_uninstall_url();
                    $uninstall = html_writer::link($uninstallurl, get_string('uninstall', 'core_plugin'));
                } else {
                    $uninstall = '';
                }
                $uninstall = new html_table_cell($uninstall);

                $requriedby = $pluginman->other_plugins_that_require($plugin->component);
                if ($requriedby) {
                    $requiredby = html_writer::tag('div', get_string('requiredby', 'core_plugin', implode(', ', $requriedby)),
                        array('class' => 'requiredby'));
                } else {
                    $requiredby = '';
                }

                $updateinfo = '';
                if (empty($CFG->disableupdatenotifications) and is_array($plugin->available_updates())) {
                    foreach ($plugin->available_updates() as $availableupdate) {
                        $updateinfo .= $this->plugin_available_update_info($availableupdate);
                    }
                }

                $notes = new html_table_cell($requiredby.$updateinfo);

                $row->cells = array(
                    $pluginname, $source, $version, $availability, $settings, $uninstall, $notes
                );
                $table->data[] = $row;
            }
        }

        return html_writer::table($table);
    }

    /**
     * Helper method to render the information about the available plugin update
     *
     * The passed objects always provides at least the 'version' property containing
     * the (higher) version of the plugin available.
     *
     * @param available_update_info $updateinfo information about the available update for the plugin
     */
    protected function plugin_available_update_info(available_update_info $updateinfo) {

        $boxclasses = 'pluginupdateinfo';
        $info = array();

        if (isset($updateinfo->release)) {
            $info[] = html_writer::tag('span', get_string('updateavailable_release', 'core_plugin', $updateinfo->release),
                array('class' => 'info release'));
        }

        if (isset($updateinfo->maturity)) {
            $info[] = html_writer::tag('span', get_string('maturity'.$updateinfo->maturity, 'core_admin'),
                array('class' => 'info maturity'));
            $boxclasses .= ' maturity'.$updateinfo->maturity;
        }

        if (isset($updateinfo->download)) {
            $info[] = html_writer::link($updateinfo->download, get_string('download'), array('class' => 'info download'));
        }

        if (isset($updateinfo->url)) {
            $info[] = html_writer::link($updateinfo->url, get_string('updateavailable_moreinfo', 'core_plugin'),
                array('class' => 'info more'));
        }

        $box  = $this->output->box_start($boxclasses);
        $box .= html_writer::tag('div', get_string('updateavailable', 'core_plugin', $updateinfo->version), array('class' => 'version'));
        $box .= $this->output->box(implode(html_writer::tag('span', ' ', array('class' => 'separator')), $info), '');

        $deployer = available_update_deployer::instance();
        if ($deployer->initialized()) {
            $impediments = $deployer->deployment_impediments($updateinfo);
            if (empty($impediments)) {
                $widget = $deployer->make_confirm_widget($updateinfo);
                $box .= $this->output->render($widget);
            } else {
                if (isset($impediments['notwritable'])) {
                    $box .= $this->output->help_icon('notwritable', 'core_plugin', get_string('notwritable', 'core_plugin'));
                }
                if (isset($impediments['notdownloadable'])) {
                    $box .= $this->output->help_icon('notdownloadable', 'core_plugin', get_string('notdownloadable', 'core_plugin'));
                }
            }
        }

        $box .= $this->output->box_end();

        return $box;
    }

    /**
     * This function will render one beautiful table with all the environmental
     * configuration and how it suits Moodle needs.
     *
     * @param boolean $result final result of the check (true/false)
     * @param array $environment_results array of results gathered
     * @return string HTML to output.
     */
    public function environment_check_table($result, $environment_results) {
        global $CFG;

        // Table headers
        $servertable = new html_table();//table for server checks
        $servertable->head  = array(
            get_string('name'),
            get_string('info'),
            get_string('report'),
            get_string('status'),
        );
        $servertable->colclasses = array('centeralign name', 'centeralign status', 'leftalign report', 'centeralign info');
        $servertable->attributes['class'] = 'admintable environmenttable generaltable';
        $servertable->id = 'serverstatus';

        $serverdata = array('ok'=>array(), 'warn'=>array(), 'error'=>array());

        $othertable = new html_table();//table for custom checks
        $othertable->head  = array(
            get_string('info'),
            get_string('report'),
            get_string('status'),
        );
        $othertable->colclasses = array('aligncenter info', 'alignleft report', 'aligncenter status');
        $othertable->attributes['class'] = 'admintable environmenttable generaltable';
        $othertable->id = 'otherserverstatus';

        $otherdata = array('ok'=>array(), 'warn'=>array(), 'error'=>array());

        // Iterate over each environment_result
        $continue = true;
        foreach ($environment_results as $environment_result) {
            $errorline   = false;
            $warningline = false;
            $stringtouse = '';
            if ($continue) {
                $type = $environment_result->getPart();
                $info = $environment_result->getInfo();
                $status = $environment_result->getStatus();
                $error_code = $environment_result->getErrorCode();
                // Process Report field
                $rec = new stdClass();
                // Something has gone wrong at parsing time
                if ($error_code) {
                    $stringtouse = 'environmentxmlerror';
                    $rec->error_code = $error_code;
                    $status = get_string('error');
                    $errorline = true;
                    $continue = false;
                }

                if ($continue) {
                    if ($rec->needed = $environment_result->getNeededVersion()) {
                        // We are comparing versions
                        $rec->current = $environment_result->getCurrentVersion();
                        if ($environment_result->getLevel() == 'required') {
                            $stringtouse = 'environmentrequireversion';
                        } else {
                            $stringtouse = 'environmentrecommendversion';
                        }

                    } else if ($environment_result->getPart() == 'custom_check') {
                        // We are checking installed & enabled things
                        if ($environment_result->getLevel() == 'required') {
                            $stringtouse = 'environmentrequirecustomcheck';
                        } else {
                            $stringtouse = 'environmentrecommendcustomcheck';
                        }

                    } else if ($environment_result->getPart() == 'php_setting') {
                        if ($status) {
                            $stringtouse = 'environmentsettingok';
                        } else if ($environment_result->getLevel() == 'required') {
                            $stringtouse = 'environmentmustfixsetting';
                        } else {
                            $stringtouse = 'environmentshouldfixsetting';
                        }

                    } else {
                        if ($environment_result->getLevel() == 'required') {
                            $stringtouse = 'environmentrequireinstall';
                        } else {
                            $stringtouse = 'environmentrecommendinstall';
                        }
                    }

                    // Calculate the status value
                    if ($environment_result->getBypassStr() != '') {            //Handle bypassed result (warning)
                        $status = get_string('bypassed');
                        $warningline = true;
                    } else if ($environment_result->getRestrictStr() != '') {   //Handle restricted result (error)
                        $status = get_string('restricted');
                        $errorline = true;
                    } else {
                        if ($status) {                                          //Handle ok result (ok)
                            $status = get_string('ok');
                        } else {
                            if ($environment_result->getLevel() == 'optional') {//Handle check result (warning)
                                $status = get_string('check');
                                $warningline = true;
                            } else {                                            //Handle error result (error)
                                $status = get_string('check');
                                $errorline = true;
                            }
                        }
                    }
                }

                // Build the text
                $linkparts = array();
                $linkparts[] = 'admin/environment';
                $linkparts[] = $type;
                if (!empty($info)){
                   $linkparts[] = $info;
                }
                if (empty($CFG->docroot)) {
                    $report = get_string($stringtouse, 'admin', $rec);
                } else {
                    $report = $this->doc_link(join($linkparts, '/'), get_string($stringtouse, 'admin', $rec));
                }

                // Format error or warning line
                if ($errorline || $warningline) {
                    $messagetype = $errorline? 'error':'warn';
                } else {
                    $messagetype = 'ok';
                }
                $status = '<span class="'.$messagetype.'">'.$status.'</span>';
                // Here we'll store all the feedback found
                $feedbacktext = '';
                // Append the feedback if there is some
                $feedbacktext .= $environment_result->strToReport($environment_result->getFeedbackStr(), $messagetype);
                //Append the bypass if there is some
                $feedbacktext .= $environment_result->strToReport($environment_result->getBypassStr(), 'warn');
                //Append the restrict if there is some
                $feedbacktext .= $environment_result->strToReport($environment_result->getRestrictStr(), 'error');

                $report .= $feedbacktext;

                // Add the row to the table
                if ($environment_result->getPart() == 'custom_check'){
                    $otherdata[$messagetype][] = array ($info, $report, $status);
                } else {
                    $serverdata[$messagetype][] = array ($type, $info, $report, $status);
                }
            }
        }

        //put errors first in
        $servertable->data = array_merge($serverdata['error'], $serverdata['warn'], $serverdata['ok']);
        $othertable->data = array_merge($otherdata['error'], $otherdata['warn'], $otherdata['ok']);

        // Print table
        $output = '';
        $output .= $this->heading(get_string('serverchecks', 'admin'));
        $output .= html_writer::table($servertable);
        if (count($othertable->data)){
            $output .= $this->heading(get_string('customcheck', 'admin'));
            $output .= html_writer::table($othertable);
        }

        // Finally, if any error has happened, print the summary box
        if (!$result) {
            $output .= $this->box(get_string('environmenterrortodo', 'admin'), 'environmentbox errorbox');
        }

        return $output;
    }
}
