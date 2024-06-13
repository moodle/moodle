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

namespace core\output;

use core_block\output\block_contents;
use core\exception\coding_exception;
use moodle_page;
use moodle_url;
use stdClass;

/**
 * The maintenance renderer.
 *
 * The purpose of this renderer is to block out the core renderer methods that are not usable when the site
 * is running a maintenance related task.
 * It must always extend the core_renderer as we switch from the core_renderer to this renderer in a couple of places.
 *
 * @since Moodle 2.6
 * @package core
 * @category output
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer_maintenance extends core_renderer {
    /**
     * Initialises the renderer instance.
     *
     * @param moodle_page $page
     * @param string $target
     * @throws coding_exception
     */
    public function __construct(moodle_page $page, $target) {
        if ($target !== RENDERER_TARGET_MAINTENANCE || $page->pagelayout !== 'maintenance') {
            throw new coding_exception('Invalid request for the maintenance renderer.');
        }
        parent::__construct($page, $target);
    }

    /**
     * Does nothing. The maintenance renderer cannot produce blocks.
     *
     * @param block_contents $bc
     * @param string $region
     * @return string
     */
    public function block(block_contents $bc, $region) {
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce blocks.
     *
     * @param string $region
     * @param array $classes
     * @param string $tag
     * @param boolean $fakeblocksonly
     * @return string
     */
    public function blocks($region, $classes = [], $tag = 'aside', $fakeblocksonly = false) {
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce blocks.
     *
     * @param string $region
     * @param boolean $fakeblocksonly Output fake block only.
     * @return string
     */
    public function blocks_for_region($region, $fakeblocksonly = false) {
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a course content header.
     *
     * @param bool $onlyifnotcalledbefore
     * @return string
     */
    public function course_content_header($onlyifnotcalledbefore = false) {
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a course content footer.
     *
     * @param bool $onlyifnotcalledbefore
     * @return string
     */
    public function course_content_footer($onlyifnotcalledbefore = false) {
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a course header.
     *
     * @return string
     */
    public function course_header() {
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a course footer.
     *
     * @return string
     */
    public function course_footer() {
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a custom menu.
     *
     * @param string $custommenuitems
     * @return string
     */
    public function custom_menu($custommenuitems = '') {
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a file picker.
     *
     * @param array $options
     * @return string
     */
    public function file_picker($options) {
        return '';
    }

    /**
     * Overridden confirm message for upgrades.
     *
     * @param string $message The question to ask the user
     * @param single_button|moodle_url|string $continue The single_button component representing the Continue answer.
     * @param single_button|moodle_url|string $cancel The single_button component representing the Cancel answer.
     * @param array $displayoptions optional extra display options
     * @return string HTML fragment
     */
    public function confirm($message, $continue, $cancel, array $displayoptions = []) {
        // We need plain styling of confirm boxes on upgrade because we don't know which stylesheet we have (it could be
        // from any previous version of Moodle).
        if ($continue instanceof single_button) {
            $continue->type = single_button::BUTTON_PRIMARY;
        } else if (is_string($continue)) {
            $continue = new single_button(
                new moodle_url($continue),
                get_string('continue'),
                'post',
                $displayoptions['type'] ?? single_button::BUTTON_PRIMARY
            );
        } else if ($continue instanceof moodle_url) {
            $continue = new single_button(
                $continue,
                get_string('continue'),
                'post',
                $displayoptions['type'] ?? single_button::BUTTON_PRIMARY
            );
        } else {
            throw new coding_exception('The continue param to $OUTPUT->confirm() must be either a URL' .
                                       ' (string/moodle_url) or a single_button instance.');
        }

        if ($cancel instanceof single_button) {
            $output = '';
        } else if (is_string($cancel)) {
            $cancel = new single_button(new moodle_url($cancel), get_string('cancel'), 'get');
        } else if ($cancel instanceof moodle_url) {
            $cancel = new single_button($cancel, get_string('cancel'), 'get');
        } else {
            throw new coding_exception('The cancel param to $OUTPUT->confirm() must be either a URL' .
                                       ' (string/moodle_url) or a single_button instance.');
        }

        $output = $this->box_start('generalbox', 'notice');
        $output .= html_writer::tag('h4', get_string('confirm'));
        $output .= html_writer::tag('p', $message);
        $output .= html_writer::tag('div', $this->render($cancel) . $this->render($continue), ['class' => 'buttons']);
        $output .= $this->box_end();
        return $output;
    }

    /**
     * Does nothing. The maintenance renderer does not support JS.
     *
     * @param block_contents $bc
     */
    public function init_block_hider_js(block_contents $bc) {
        // Does nothing.
    }

    /**
     * Does nothing. The maintenance renderer cannot produce language menus.
     *
     * @return string
     */
    public function lang_menu() {
        return '';
    }

    /**
     * Does nothing. The maintenance renderer has no need for login information.
     *
     * @param mixed $withlinks
     * @return string
     */
    public function login_info($withlinks = null) {
        return '';
    }

    /**
     * Secure login info.
     *
     * @return string
     */
    public function secure_login_info() {
        return $this->login_info(false);
    }

    /**
     * Does nothing. The maintenance renderer cannot produce user pictures.
     *
     * @param stdClass $user
     * @param null|array $options
     * @return string
     */
    public function user_picture(stdClass $user, ?array $options = null) {
        return '';
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(core_renderer_maintenance::class, \core_renderer_maintenance::class);
