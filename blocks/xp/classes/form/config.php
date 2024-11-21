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
 * Block XP config form.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\form;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/formslib.php');
require_once(__DIR__ . '/itemspertime.php');
require_once(__DIR__ . '/duration.php');

use block_xp\local\config\course_world_config;
use html_writer;
use moodleform;
use moodle_url;

/**
 * Block XP config form class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config extends moodleform {

    /**
     * Form definition.
     *
     * @return void
     */
    public function definition() {
        global $PAGE;
        // Conditional check (on world) for compatibility with older versions of local_xp.
        $world = !empty($this->_customdata['world']) ? $this->_customdata['world'] : null;
        $config = \block_xp\di::get('config');
        $renderer = \block_xp\di::get('renderer');
        $urlresolver = \block_xp\di::get('url_resolver');
        $addon = \block_xp\di::get('addon');
        $addonolder = $addon->is_activated() && $addon->is_older_than(2024090500);

        $mform = $this->_form;
        $mform->setDisableShortforms(true);

        $mform->addElement('header', 'hdrgeneral', get_string('general'));

        $mform->addElement('selectyesno', 'enabled', get_string('enablexpgain', 'block_xp'));
        $mform->addHelpButton('enabled', 'enablexpgain', 'block_xp');

        $mform->addElement('selectyesno', 'enableinfos', get_string('enableinfos', 'block_xp'));
        $mform->addHelpButton('enableinfos', 'enableinfos', 'block_xp');

        $levelupnotifelements = [$mform->createElement('selectyesno', 'enablelevelupnotif',
            get_string('enablelevelupnotif', 'block_xp'))];
        if ($world) {
            $randomtrymeid = html_writer::random_id();
            $level1 = $world->get_levels_info()->get_level(1);
            $level2 = $world->get_levels_info()->get_level(2);
            $trymedatascript = $renderer->json_script([
                'courseid' => $world->get_courseid(),
                'levelnum' => $level2->get_level(),
                'levelbadge' => $renderer->level_badge($level2),
                'prevlevelbadge' => $renderer->level_badge($level1),
            ], $randomtrymeid . 'data');
            $levelupnotifelements[] = $mform->createElement('static', 'levelupnotifunit', '', html_writer::div(
                html_writer::tag('a', get_string('tryme', 'block_xp'), [
                    'href' => '#',
                    'id' => $randomtrymeid,
                    'role' => 'button',
                    'class' => 'xp-text-xs',
                ]) . $trymedatascript
            ));
            $PAGE->requires->js_amd_inline(<<<EOT
                require(['block_xp/popup-notification', 'block_xp/role-button'], (PopupModule, RoleButton) => {
                    RoleButton.registerClick('#$randomtrymeid', () => {
                        PopupModule.show(JSON.parse(document.getElementById('{$randomtrymeid}data').textContent));
                    });
                });
            EOT);
        }
        $mform->addGroup($levelupnotifelements, 'enablelevelupnotifgrp', get_string('enablelevelupnotif', 'block_xp'), null, false);
        $mform->addHelpButton('enablelevelupnotifgrp', 'enablelevelupnotif', 'block_xp');

        $mform->addElement('header', 'hdrladder', get_string('ladder', 'block_xp'));

        $mform->addElement('html', \html_writer::div($renderer->notification_without_close(
            strip_tags(markdown_to_html(get_string('laddersettingsmovednotice', 'block_xp', [
                'url' => ($urlresolver->reverse('ladder', ['courseid' => $world->get_courseid()]))->out(false),
            ])), '<a>'), 'info'),
            'xp-my-4'));

        if ($addonolder) {
            $mform->addElement('html', \html_writer::div($renderer->notification_without_close(
                strip_tags(markdown_to_html(get_string('settingsoutdatedxppnotice', 'block_xp')), '<a>'), 'error'),
                'xp-my-4'));
            $this->define_legacy_ladder_fields($world);
        }

        $mform->addElement('hidden', '__generalend');
        $mform->setType('__generalend', PARAM_BOOL);

        $mform->addElement('header', 'hdrcheating', get_string('cheatguard', 'block_xp'));

        $mform->addElement('html', \html_writer::div($renderer->notification_without_close(
            strip_tags(markdown_to_html(get_string('cheatguardsettingsmovednotice', 'block_xp', [
                'url' => ($urlresolver->reverse('rules', ['courseid' => $world->get_courseid()]))->out(false),
            ])), '<a>'), 'info'),
            'xp-my-4'));

        if ($addonolder) {
            $mform->addElement('html', \html_writer::div($renderer->notification_without_close(
                strip_tags(markdown_to_html(get_string('settingsoutdatedxppnotice', 'block_xp')), '<a>'), 'error'),
                'xp-my-4'));
            $this->define_legacy_cheatguard_fields($world);
        }

        $mform->addElement('hidden', '__cheatguardend');
        $mform->setType('__cheatguardend', PARAM_BOOL);

        $mform->addElement('header', 'hdrblockconfig', get_string('blockappearance', 'block_xp'));

        $mform->addElement('text', 'blocktitle', get_string('configtitle', 'block_xp'));
        $mform->addHelpButton('blocktitle', 'configtitle', 'block_xp');
        $mform->setType('blocktitle', PARAM_TEXT);

        $mform->addElement('textarea', 'blockdescription', get_string('configdescription', 'block_xp'));
        $mform->addHelpButton('blockdescription', 'configdescription', 'block_xp');
        $mform->setType('blockdescription', PARAM_TEXT);

        $mform->addElement('select', 'blockrankingsnapshot', get_string('configblockrankingsnapshot', 'block_xp'), [
            0 => get_string('no'),
            1 => get_string('yes'),
        ]);
        $mform->addHelpButton('blockrankingsnapshot', 'configblockrankingsnapshot', 'block_xp');
        $mform->setType('blockrankingsnapshot', PARAM_INT);
        $mform->disabledIf('blockrankingsnapshot', 'enableladder', 'eq', '0');

        $mform->addElement('select', 'blockrecentactivity', get_string('configrecentactivity', 'block_xp'), [
            0 => get_string('no'),
            3 => get_string('yes'),
        ]);
        $mform->addHelpButton('blockrecentactivity', 'configrecentactivity', 'block_xp');
        $mform->setType('blockrecentactivity', PARAM_INT);

        $mform->addElement('hidden', '__blockappearanceend');
        $mform->setType('__blockappearanceend', PARAM_BOOL);

        $this->add_action_buttons();
    }

    /**
     * Definition after data.
     *
     * @return void
     */
    public function definition_after_data() {
        $mform = $this->_form;

        // Lock the settings that have been locked by an admin. We do this in definition_after_data
        // because as we support Moodle 3.1 in which self::after_definition() is not available.
        $configlocked = \block_xp\di::get('config_locked');
        foreach ($configlocked->get_all() as $key => $islocked) {
            if (!$islocked || !$mform->elementExists($key)) {
                continue;
            }
            $mform->hardFreeze($key);
        }
    }

    /**
     * Get the data.
     *
     * @return stdClass
     */
    public function get_data() {
        $mform = $this->_form;

        $data = parent::get_data();
        if (!$data) {
            return $data;
        }

        unset($data->__generalend);
        unset($data->__cheatguardend);
        unset($data->__blockappearanceend);
        unset($data->__loggingend);

        // Convert back from itemspertime.
        if ($mform->elementExists('maxactionspertime')) {
            if (!isset($data->maxactionspertime) || !is_array($data->maxactionspertime)) {
                $data->maxactionspertime = 0;
                $data->timeformaxactions = 0;
            } else {
                $data->timeformaxactions = (int) $data->maxactionspertime['time'];
                $data->maxactionspertime = (int) $data->maxactionspertime['points'];
            }
        }

        // When not selecting any, the data is not sent.
        if ($mform->elementExists('laddercols')) {
            if (!isset($data->laddercols)) {
                $data->laddercols = [];
            }
            $data->laddercols = implode(',', $data->laddercols);
        }

        // When the cheat guard is disabled, we remove the config fields so that
        // we can keep the defaults and the data previously submitted by the user.
        if (empty($data->enablecheatguard)) {
            unset($data->maxactionspertime);
            unset($data->timeformaxactions);
            unset($data->timebetweensameactions);
        }

        unset($data->submitbutton);
        return $data;
    }

    /**
     * Set the data.
     *
     * @param mixed $data The data.
     */
    public function set_data($data) {
        $data = (array) $data;
        if (isset($data['laddercols'])) {
            $data['laddercols'] = explode(',', $data['laddercols']);
        }

        // Convert to itemspertime.
        if (isset($data['maxactionspertime']) && isset($data['timeformaxactions'])) {
            $data['maxactionspertime'] = [
                'points' => (int) $data['maxactionspertime'],
                'time' => (int) $data['timeformaxactions'],
            ];
            unset($data['timeformaxactions']);
        }

        parent::set_data($data);
    }

    /**
     * Define legacy cheatguard fields.
     *
     * @param world|null $world The world.
     */
    protected function define_legacy_cheatguard_fields($world = null) {
        $mform = $this->_form;
        $config = \block_xp\di::get('config');
        $renderer = \block_xp\di::get('renderer');

        $mform->addElement('selectyesno', 'enablecheatguard', get_string('enablecheatguard', 'block_xp'));
        $mform->addHelpButton('enablecheatguard', 'enablecheatguard', 'block_xp');

        $mform->addElement('block_xp_form_itemspertime', 'maxactionspertime', get_string('maxactionspertime', 'block_xp'), [
            'maxunit' => 60,
            'itemlabel' => get_string('actions', 'block_xp'),
        ]);
        $mform->addHelpButton('maxactionspertime', 'maxactionspertime', 'block_xp');
        $mform->disabledIf('maxactionspertime', 'enablecheatguard', 'eq', 0);

        $mform->addElement('block_xp_form_duration', 'timebetweensameactions', get_string('timebetweensameactions', 'block_xp'), [
            'maxunit' => 60,
            'optional' => false,        // We must set this...
        ]);
        $mform->addHelpButton('timebetweensameactions', 'timebetweensameactions', 'block_xp');
        $mform->disabledIf('timebetweensameactions', 'enablecheatguard', 'eq', 0);

        if ($world && $world->get_config()->get('enablecheatguard') && $config->get('enablepromoincourses')) {
            $worldconfig = $world->get_config();
            $timeframe = max(0, $worldconfig->get('timebetweensameactions'), $worldconfig->get('timeformaxactions'));

            $promourl = new moodle_url('https://www.levelup.plus');
            if (!empty($this->_customdata['promourl'])) {
                $promourl = $this->_customdata['promourl'];
            }

            if ($timeframe > HOURSECS * 6) {
                $mform->addElement('static', '', '', $renderer->notification_without_close(
                    get_string('promocheatguard', 'block_xp', ['url' => $promourl->out()]
                ), 'warning'));
            }
        }
    }

    /**
     * Define legacy ladder fields.
     */
    protected function define_legacy_ladder_fields() {
        $mform = $this->_form;

        $mform->addElement('selectyesno', 'enableladder', get_string('enableladder', 'block_xp'));
        $mform->addHelpButton('enableladder', 'enableladder', 'block_xp');

        $mform->addElement('select', 'identitymode', get_string('anonymity', 'block_xp'), [
            course_world_config::IDENTITY_OFF => get_string('hideparticipantsidentity', 'block_xp'),
            course_world_config::IDENTITY_ON => get_string('displayparticipantsidentity', 'block_xp'),
        ]);
        $mform->addHelpButton('identitymode', 'anonymity', 'block_xp');
        $mform->disabledIf('identitymode', 'enableladder', 'eq', 0);

        $mform->addElement('select', 'neighbours', get_string('limitparticipants', 'block_xp'), [
            0 => get_string('displayeveryone', 'block_xp'),
            1 => get_string('displayoneneigbour', 'block_xp'),
            2 => get_string('displaynneighbours', 'block_xp', '2'),
            3 => get_string('displaynneighbours', 'block_xp', '3'),
            4 => get_string('displaynneighbours', 'block_xp', '4'),
            5 => get_string('displaynneighbours', 'block_xp', '5'),
        ]);
        $mform->addHelpButton('neighbours', 'limitparticipants', 'block_xp');
        $mform->disabledIf('neighbours', 'enableladder', 'eq', 0);

        $mform->addElement('select', 'rankmode', get_string('ranking', 'block_xp'), [
            course_world_config::RANK_OFF => get_string('hiderank', 'block_xp'),
            course_world_config::RANK_ON => get_string('displayrank', 'block_xp'),
            course_world_config::RANK_REL => get_string('displayrelativerank', 'block_xp'),
        ]);
        $mform->addHelpButton('rankmode', 'ranking', 'block_xp');
        $mform->disabledIf('rankmode', 'enableladder', 'eq', 0);

        $el = $mform->addElement('select', 'laddercols', get_string('ladderadditionalcols', 'block_xp'), [
            'xp' => get_string('total', 'block_xp'),
            'progress' => get_string('progress', 'block_xp'),
        ], ['style' => 'height: 4em;']);
        $el->setMultiple(true);
        $mform->addHelpButton('laddercols', 'ladderadditionalcols', 'block_xp');
    }

}
