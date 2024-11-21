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
 * Promo controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

use block_xp\di;
use html_writer;
use block_xp\local\routing\url;
use moodle_url;
use single_button;

/**
 * Promo controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class promo_controller extends route_controller {

    /** Seen flag. */
    const SEEN_FLAG = 'promo-page-seen';
    /** Page version. */
    const VERSION = 20231006;

    /** @var string The normal route name. */
    protected $routename = 'promo';
    /** @var string The admin section name. */
    protected $sectionname = 'block_xp_promo';
    /** @var string The email. */
    protected $email = 'levelup@branchup.tech';
    /** @var url_resolver The URL resolver. */
    protected $urlresolver;
    /** @var world The world. */
    protected $world;

    protected function define_optional_params() {
        return [
            ['sent', 0, PARAM_INT, false],
        ];
    }

    /**
     * Whether we are in an admin page.
     *
     * @return bool
     */
    protected function is_admin_page() {
        $params = $this->request->get_route()->get_params();
        return empty($params['courseid']);
    }

    protected function require_login() {
        global $CFG, $PAGE, $USER, $SITE, $OUTPUT;
        if ($this->is_admin_page()) {
            admin_externalpage_setup($this->sectionname, '', null, $this->pageurl->get_compatible_url());
        } else {
            $courseid = intval($this->get_param('courseid'));
            require_login($courseid);
        }
    }

    /**
     * The course page navigation.
     *
     * @return void
     */
    protected function page_course_navigation() {
        $output = $this->get_renderer();
        $items = di::get('course_world_navigation_factory')->get_course_navigation($this->world);
        if (count($items) > 1) {
            return $output->tab_navigation($items, $this->routename);
        }
        return '';
    }

    protected function post_login() {
        $this->urlresolver = \block_xp\di::get('url_resolver');
        if (!$this->is_admin_page()) {
            $this->world = \block_xp\di::get('course_world_factory')->get_world($this->get_param('courseid'));
        }
    }

    /**
     * Permission checks.
     *
     * @throws moodle_exception When the conditions are not met.
     * @return void
     */
    protected function permissions_checks() {
        if (!$this->is_admin_page()) {
            $this->world->get_access_permissions()->require_manage();
        }
    }

    /**
     * Moodle page specifics.
     *
     * @return void
     */
    protected function page_setup() {
        global $COURSE, $PAGE;
        if (!$this->is_admin_page()) {
            // Note that the context was set by require_login().
            $PAGE->set_url($this->pageurl->get_compatible_url());
            $PAGE->set_pagelayout('course');
            $PAGE->set_title(get_string('levelupplus', 'block_xp'));
            $PAGE->set_heading(format_string($COURSE->fullname));
            $PAGE->add_body_class('limitedwidth');
        }
    }

    protected function content() {
        self::mark_as_seen();

        $addon = \block_xp\di::get('addon');
        if ($addon->is_activated()) {
            $this->content_installed();
            return;
        }

        $this->content_not_installed();
    }

    /**
     * Content when not installed.
     *
     * @return void
     */
    protected function content_not_installed() {
        $output = \block_xp\di::get('renderer');
        $siteurl = "https://www.levelup.plus/xp/?ref=plugin_promopage";
        $getxpstr = get_string('promogetnow', 'block_xp');

        if (!$this->is_admin_page()) {
            $config = $this->world->get_config();
            $context = $this->world->get_context();
            $blocktitle = $config->get('blocktitle');
            if (empty($blocktitle)) {
                $blocktitle = get_string('levelup', 'block_xp');
            }
            echo $output->heading(format_string($blocktitle, true, ['context' => $context]));
            echo $this->page_course_navigation();
            echo $output->notices($this->world);
        }

        echo $output->advanced_heading(get_string('discoverlevelupplus', 'block_xp'), [
            'intro' => get_string('promointro', 'block_xp'),
            'actions' => [$output->make_single_button(new moodle_url($siteurl), $getxpstr, ['primary' => true])],
        ]);

        $new = '🆕';

        $renderitemstart = function($icon, $title, $subtitle) use ($output) {
            return <<<EOT
            <div class="xp-bg-slate-50 xp-rounded xp-p-4">
                <div class="xp-pb-4 xp-mb-4 xp-flex xp-gap-4 xp-border-b-white xp-border-0 xp-border-b-2 xp-border-solid">
                    <div class="xp-w-16 xp-flex-0">
                        <img src="{$output->pix_url($icon, 'block_xp')}" alt="" class="xp-max-w-full">
                    </div>
                    <div class="xp-grow">
                        <h4>{$title}</h4>
                        <p class="xp-m-0 xp-text-gray-700 xp-text-base">{$subtitle}</p>
                    </div>
                </div>
                <div>
EOT;
        };
        $renderitemend = function() {
            return "</div></div>";
        };

        echo <<<EOT
<div class="xp-grid sm:xp-grid-cols-2 xp-gap-4 [&_ul]:xp-pl-4 [&_li]:xp-mb-1">
    {$renderitemstart("trophy", "Greater motivation", "Make learners even more engaged and motivated!")}
        <ul>
            <li>Insert customised <strong>congratulation messages</strong> when learners receive
                the level up notification.</li>
            <li><strong>Award a Moodle badge</strong> when learners attain a particular level</li>
        </ul>
    {$renderitemend()}
    {$renderitemstart("noun/checklist", "Extended points strategy", "More control and methods to award points!")}
        <ul>
            <li><strong>Drops</strong>: award points by placing code snippets anywhere</li>
            <li>Convert <strong>grades</strong> into points</li>
            <li>Reward <strong>activity</strong> and <strong>course completion</strong></li>
            <li>Award point via web services <strong>API</strong></li>
        </ul>
        <p>Plus convenient rules to:</p>
        <ul>
            <li>Target specific courses</li>
            <li>Target activities by name</li>
        </ul>
    {$renderitemend()}
    {$renderitemstart("noun/manual", "Individual rewards", "Manually award points to one or more learners.")}
            <ul>
                <li>A great way to <strong>reward offline</strong> or punctual <strong>actions</strong></li>
                <li>Use our <strong>import</strong> feature to award points <strong>from a spreadsheet</strong></li>
            </ul>
    {$renderitemend()}
    {$renderitemstart("noun/group", "Team leaderboards", "Rank teams of learners based on their combined points.")}
            <ul>
                <li>Create the <strong>teams from groups</strong> and cohorts</li>
                <li>Collaboration and cohesion in a friendly competition</li>
            </ul>
    {$renderitemend()}
    {$renderitemstart("noun/privacy", "Improved cheat guard", "Get better control over learners' rewards.")}
            <ul>
                <li><strong>Limit</strong> your learners' <strong>rewards</strong> per day (or other time frames)</li>
                <li>Get peace of mind with a more <strong>robust</strong> and resilient anti-cheat</li>
                <li><strong>Increase</strong> the <strong>time limits</strong> to greater values</li>
            </ul>
    {$renderitemend()}
    {$renderitemstart("noun/export", "Import, export &amp; report", "Keep track of your learners' actions.")}
            <ul>
                <li><strong>Export everything</strong>: leaderboards, logs and reports</li>
                <li>Allocate <strong>points in bulk</strong> from an imported CSV file</li>
                <li>Logs contain <strong>human-friendly</strong> descriptions and originating locations</li>
            </ul>
    {$renderitemend()}
    {$renderitemstart("noun/carrots", "Change the meaning of points", "Swap the \"XP\" symbol to give it another meaning.")}
            <ul>
                <li>Choose one of the built-in symbols: 🧱, 💧, 🍃, 💡, 🧩, ⭐</li>
                <li>Or make your own symbol by uploading an image.</li>
            </ul>
    {$renderitemend()}
    {$renderitemstart("level", "Additional level badges", "Celebrate learners achievements with more badges.")}
            <ul>
                <li><strong>Five new sets</strong> of level badges</li>
                <li>From cute characters, to progressive levels such as a seed growing into a tree</li>
            </ul>
    {$renderitemend()}
    {$renderitemstart("noun/help", "Email support", "Let us help if something goes wrong.")}
            <ul>
                <li>Get direct <strong>email support</strong> from our team.</li>
            </ul>
    {$renderitemend()}
    {$renderitemstart("noun/heart", "Support us", "Purchases directly contribute to the plugin's development.")}
            <ul>
                <li>Bugs will be fixed</li>
                <li>Requested features will be added</li>
            </ul>
    {$renderitemend()}
</div>

<div style="text-align: center; margin: 1rem 0">
    <p><a class="btn btn-primary btn-large btn-lg" href="{$siteurl}">
        {$getxpstr}
    </a></p>
</div>
EOT;

    }

    protected function content_installed() {
        $output = \block_xp\di::get('renderer');
        $addon = \block_xp\di::get('addon');
        $docsurl = new url('https://docs.levelup.plus/xp/docs?ref=plugin_promopage');
        $releasenotesurl = new url('https://docs.levelup.plus/xp/release-notes?ref=plugin_promopage');
        $upgradeurl = new url('https://docs.levelup.plus/xp/docs/upgrade?ref=plugin_promopage');
        $outofsyncurl = new url('https://docs.levelup.plus/xp/docs/requirements-compatibility?ref=plugin_promopage#out-of-sync');

        if (!$this->is_admin_page()) {
            $config = $this->world->get_config();
            $context = $this->world->get_context();
            $blocktitle = $config->get('blocktitle');
            if (empty($blocktitle)) {
                $blocktitle = get_string('levelup', 'block_xp');
            }
            echo $output->heading(format_string($blocktitle, true, ['context' => $context]));
            echo $this->page_course_navigation();
        }

        if (!$addon->is_installed_and_upgraded()) {
            echo $output->notification_without_close(get_string('addoninstallationerror', 'block_xp'), 'error');
            echo html_writer::tag('p', get_string('version', 'core') . ' ' . $addon->get_release());
            return;
        }

        if ($addon->is_out_of_sync()) {
            echo $output->notification_without_close(markdown_to_html(get_string('pluginsoutofsync', 'block_xp', [
                'url' => $outofsyncurl->out(false),
            ])), 'error');
        }

        echo $output->heading(get_string('thankyou', 'block_xp'), 3);
        echo markdown_to_html(get_string('promointroinstalled', 'block_xp'));

        echo html_writer::tag('p', get_string('version', 'core') . ' ' . $addon->get_release());

        echo $output->heading(get_string('additionalresources', 'block_xp'), 4);
        echo html_writer::start_tag('ul');
        echo html_writer::tag('li', html_writer::link($docsurl, get_string('documentation', 'block_xp')));
        echo html_writer::tag('li', html_writer::link($releasenotesurl, get_string('releasenotes', 'block_xp')));
        echo html_writer::tag('li', html_writer::link($upgradeurl, get_string('upgradingplugins', 'block_xp')));

        echo html_writer::end_tag('ul');
    }

    /**
     * Check whether there is new content for the user.
     *
     * @return bool
     */
    public static function has_new_content() {
        global $USER;
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        $indicator = \block_xp\di::get('user_generic_indicator');
        $addon = \block_xp\di::get('addon');
        $value = $indicator->get_user_flag($USER->id, self::SEEN_FLAG);

        return $value < self::VERSION || $addon->is_out_of_sync();
    }

    /**
     * Mark as the page seen.
     *
     * @return void
     */
    protected static function mark_as_seen() {
        global $USER;
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        $indicator = \block_xp\di::get('user_generic_indicator');
        $value = $indicator->set_user_flag($USER->id, self::SEEN_FLAG, self::VERSION);
    }

}
