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
     * Display the 'You are about to upgrade Moodle' page. The first page
     * during upgrade.
     * @param string $strnewversion
     * @param int $maturity
     * @return string HTML to output.
     */
    public function upgrade_confirm_page($strnewversion, $maturity) {
        $output = '';

        $continueurl = new moodle_url('index.php', array('confirmupgrade' => 1));
        $cancelurl = new moodle_url('index.php');

        $output .= $this->header();
        $output .= $this->maturity_warning($maturity);
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
     * @param int $version the version of the Moodle code from version.php.
     * @param bool $showallplugins
     * @param moodle_url $reloadurl
     * @param moodle_url $continueurl
     * @return string HTML to output.
     */
    public function upgrade_plugin_check_page(plugin_manager $pluginman, $version, $showallplugins, $reloadurl, $continueurl) {
        $output = '';

        $output .= $this->header();
        $output .= $this->box_start('generalbox');
        $output .= $this->container(get_string('pluginchecknotice', 'core_plugin'), 'generalbox', 'notice');
        $output .= $this->plugins_check_table($pluginman, $version, array('full' => $showallplugins));
        $output .= $this->box_end();
        $output .= $this->upgrade_reload($reloadurl);

        if ($pluginman->all_plugins_ok($version)) {
            $button = new single_button($continueurl, get_string('upgradestart', 'admin'), 'get');
            $button->class = 'continuebutton';
            $output .= $this->render($button);
        } else {
            $output .= $this->box(get_string('pluginschecktodo', 'admin'), 'environmentbox errorbox');
        }

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
     * @return string HTML to output.
     */
    public function admin_notifications_page($maturity, $insecuredataroot, $errorsdisplayed,
            $cronoverdue, $dbproblems, $maintenancemode) {
        $output = '';

        $output .= $this->header();
        $output .= $this->maturity_info($maturity);
        $output .= $this->insecure_dataroot_warning($insecuredataroot);
        $output .= $this->display_errors_warning($errorsdisplayed);
        $output .= $this->cron_overdue_warning($cronoverdue);
        $output .= $this->db_problems($dbproblems);
        $output .= $this->maintenance_mode_warning($maintenancemode);

        //////////////////////////////////////////////////////////////////////////////////////////////////
        ////  IT IS ILLEGAL AND A VIOLATION OF THE GPL TO HIDE, REMOVE OR MODIFY THIS COPYRIGHT NOTICE ///
        $output .= $this->moodle_copyright();
        //////////////////////////////////////////////////////////////////////////////////////////////////

        $output .= $this->footer();

        return $output;
    }

    /**
     * Display the plugin management page (admin/plugins.php).
     * @param plugin_manager $pluginman
     * @return string HTML to output.
     */
    public function plugin_management_page(plugin_manager $pluginman) {
        $output = '';

        $output .= $this->header();
        $output .= $this->heading(get_string('pluginsoverview', 'core_admin'));
        $output .= $this->box_start('generalbox');
        $output .= $this->plugins_control_panel($pluginman);
        $output .= $this->box_end();
        $output .= $this->footer();

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
                         'and <a href="http://docs.moodle.org/dev/Credits">many other contributors</a>.<br />'.
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
                'generalbox adminwarning maturityinfo');
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
                    html_writer::link($url, $this->pix_icon('i/reload', '') .
                            get_string('reload'), array('title' => get_string('reload'))),
                array('class' => 'continuebutton')) . html_writer::empty_tag('br');
    }

    /**
     * Displays all known plugins and information about their installation or upgrade
     *
     * This default implementation renders all plugins into one big table. The rendering
     * options support:
     *     (bool)full = false: whether to display up-to-date plugins, too
     *
     * @param plugin_manager $pluginman provides information about the plugins.
     * @param int $version the version of the Moodle code from version.php.
     * @param array $options rendering options
     * @return string HTML code
     */
    public function plugins_check_table(plugin_manager $pluginman, $version, array $options = null) {
        $plugininfo = $pluginman->get_plugins();

        if (empty($plugininfo)) {
            return '';
        }

        if (empty($options)) {
            $options = array(
                'full' => false,
            );
        }

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

                if ($this->page->theme->resolve_image_location('icon', $plugin->type . '_' . $plugin->name)) {
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

                $status = new html_table_cell(get_string('status_' . $statuscode, 'core_plugin'));

                $requires = new html_table_cell($this->required_column($plugin, $pluginman, $version));

                $statusisboring = in_array($statuscode, array(
                        plugin_manager::PLUGIN_STATUS_NODB, plugin_manager::PLUGIN_STATUS_UPTODATE));
                $dependenciesok = $pluginman->are_dependencies_satisfied(
                        $plugin->get_other_required_plugins());
                if ($isstandard and $statusisboring and $dependenciesok) {
                    if (empty($options['full'])) {
                        continue;
                    }
                } else {
                    $numofhighlighted[$type]++;
                }

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

        if ($sumofhighlighted == 0) {
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
     * @param plugin_information $plugin the plugin we are rendering the row for.
     * @param plugin_manager $pluginman provides data on all the plugins.
     * @param string $version
     * @return string HTML code
     */
    protected function required_column(plugin_information $plugin, plugin_manager $pluginman, $version) {
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
            $requires[] = html_writer::tag('li',
                    get_string($str, 'core_plugin',
                            array('component' => $component, 'version' => $requiredversion)),
                    array('class' => $class));
        }

        if (!$requires) {
            return '';
        }
        return html_writer::tag('ul', implode("\n", $requires));
    }

    /**
     * Displays all known plugins and links to manage them
     *
     * This default implementation renders all plugins into one big table.
     *
     * @param plugin_manager $pluginman provides information about the plugins.
     * @return string HTML code
     */
    public function plugins_control_panel(plugin_manager $pluginman) {
        $plugininfo = $pluginman->get_plugins();

        if (empty($plugininfo)) {
            return '';
        }

        $table = new html_table();
        $table->id = 'plugins-control-panel';
        $table->head = array(
            get_string('displayname', 'core_plugin'),
            get_string('systemname', 'core_plugin'),
            get_string('source', 'core_plugin'),
            get_string('version', 'core_plugin'),
            get_string('availability', 'core_plugin'),
            get_string('settings', 'core_plugin'),
            get_string('uninstall','core_plugin'),
        );
        $table->colclasses = array(
            'displayname', 'systemname', 'source', 'version', 'availability', 'settings', 'uninstall',
        );

        foreach ($plugininfo as $type => $plugins) {

            $header = new html_table_cell($pluginman->plugintype_name_plural($type));
            $header->header = true;
            $header->colspan = count($table->head);
            $header = new html_table_row(array($header));
            $header->attributes['class'] = 'plugintypeheader type-' . $type;
            $table->data[] = $header;

            if (empty($plugins)) {
                $msg = new html_table_cell(get_string('noneinstalled', 'core_plugin'));
                $msg->colspan = count($table->head);
                $row = new html_table_row(array($msg));
                $row->attributes['class'] .= 'msg msg-noneinstalled';
                $table->data[] = $row;
                continue;
            }

            foreach ($plugins as $name => $plugin) {
                $row = new html_table_row();
                $row->attributes['class'] = 'type-' . $plugin->type . ' name-' . $plugin->type . '_' . $plugin->name;

                if ($this->page->theme->resolve_image_location('icon', $plugin->type . '_' . $plugin->name)) {
                    $icon = $this->output->pix_icon('icon', '', $plugin->type . '_' . $plugin->name, array('class' => 'smallicon pluginicon'));
                } else {
                    $icon = $this->output->pix_icon('spacer', '', 'moodle', array('class' => 'smallicon pluginicon noicon'));
                }
                if ($plugin->get_status() === plugin_manager::PLUGIN_STATUS_MISSING) {
                    $msg = html_writer::tag('span', get_string('status_missing', 'core_plugin'), array('class' => 'notifyproblem'));
                    $row->attributes['class'] .= ' missingfromdisk';
                } else {
                    $msg = '';
                }
                $displayname  = $icon . ' ' . $plugin->displayname . ' ' . $msg;
                $displayname = new html_table_cell($displayname);

                $systemname = new html_table_cell($plugin->type . '_' . $plugin->name);

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
                    $icon = $this->output->pix_icon('i/hide', get_string('pluginenabled', 'core_plugin'));
                    $availability = new html_table_cell($icon . ' ' . get_string('pluginenabled', 'core_plugin'));
                } else {
                    $row->attributes['class'] .= ' disabled';
                    $icon = $this->output->pix_icon('i/show', get_string('plugindisabled', 'core_plugin'));
                    $availability = new html_table_cell($icon . ' ' . get_string('plugindisabled', 'core_plugin'));
                }

                $settingsurl = $plugin->get_settings_url();
                if (is_null($settingsurl)) {
                    $settings = new html_table_cell('');
                } else {
                    $settings = html_writer::link($settingsurl, get_string('settings', 'core_plugin'));
                    $settings = new html_table_cell($settings);
                }

                $uninstallurl = $plugin->get_uninstall_url();
                $requriedby = $pluginman->other_plugins_that_require($plugin->component);
                if (is_null($uninstallurl)) {
                    $uninstall = new html_table_cell('');
                } else if ($requriedby) {
                    $uninstall = new html_table_cell(get_string('requiredby', 'core_plugin', implode(', ', $requriedby)));
                    $uninstall->attributes['class'] = 'requiredby';
                } else {
                    $uninstall = html_writer::link($uninstallurl, get_string('uninstall', 'core_plugin'));
                    $uninstall = new html_table_cell($uninstall);
                }

                $row->cells = array(
                    $displayname, $systemname, $source, $version, $availability, $settings, $uninstall
                );
                $table->data[] = $row;
            }
        }

        return html_writer::table($table);
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
        $servertable->align = array('center', 'center', 'left', 'center');
        $servertable->wrap  = array('nowrap', '', '', 'nowrap');
        $servertable->size  = array('10', 10, '100%', '10');
        $servertable->attributes['class'] = 'environmenttable generaltable';

        $serverdata = array('ok'=>array(), 'warn'=>array(), 'error'=>array());

        $othertable = new html_table();//table for custom checks
        $othertable->head  = array(
            get_string('info'),
            get_string('report'),
            get_string('status'),
        );
        $othertable->align = array('center', 'left', 'center');
        $othertable->wrap  = array('', '', 'nowrap');
        $othertable->size  = array(10, '100%', '10');
        $othertable->attributes['class'] = 'environmenttable generaltable';

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
