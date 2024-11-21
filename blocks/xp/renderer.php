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
 * Block XP renderer.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_xp\local\course_world;
use block_xp\local\activity\activity;
use block_xp\local\utils\user_utils;
use block_xp\local\xp\level;
use block_xp\local\xp\level_with_badge;
use block_xp\local\xp\level_with_name;
use block_xp\local\xp\state;
use block_xp\output\xp_widget;

/**
 * Block XP renderer class.
 *
 * @package    block_xp
 * @copyright  2014 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_xp_renderer extends plugin_renderer_base {

    /** Notice flag. */
    const NOTICE_FLAG_QUEST = 'block_xp_notice_quest';

    /** @var string Notices flag. */
    protected $noticesflag = 'block_xp_notices';

    /**
     * Advanced heading.
     *
     * @param string $heading The heading.
     * @param array $options The options.
     */
    public function advanced_heading($heading, $options = []) {
        $options = array_merge(['level' => 3, 'actions' => [], 'intro' => null, 'help' => null, 'visible' => null,
            'menu' => []], $options);
        $level = (int) $options['level'];
        $actions = (array) $options['actions'];
        $menu = (array) $options['menu'];
        $intro = !empty($options['intro']) ? $options['intro'] : null;
        $visible = isset($options['visible']) ? (bool) $options['visible'] : null;
        $help = $options['help'] instanceof \help_icon ? $options['help'] : null;

        $menuitems = array_values(array_filter(array_map(function($item) {
            $attrs = [];
            $classes = [];
            if (empty($item['label'])) {
                return ['isdivider' => true];
            }
            foreach ($item as $key => $value) {
                if ($key === 'label' || $key === 'class' || $key === 'disabled' || $key === 'addonrequired') {
                    continue;
                } else if ($key === 'danger') {
                    $classes[] = $value ? 'text-danger' : null;
                    continue;
                }
                $attrs[] = [
                    'name' => $key,
                    'value' => $value instanceof moodle_url ? $value->out(false) : (string) $value,
                ];
            }
            return [
                'label' => $item['label'],
                'disabled' => !empty($item['disabled']),
                'addonrequired' => !empty($item['addonrequired']),
                'attributes' => $attrs,
                'classes' => array_filter($classes),
            ];
        }, $menu)));

        // Filter out orphan or doubled dividers.
        $menuitems = array_values(array_filter($menuitems, function($v, $k) use ($menuitems) {
            if (empty($v['isdivider'])) {
                return true;
            }
            if ($k === 0 || $k === count($menuitems) - 1) {
                return false;
            } else if (array_key_exists('isdivider', $menuitems[$k - 1] ?? [])) {
                return false;
            }
            return true;
        }, ARRAY_FILTER_USE_BOTH));

        return $this->render_from_template('block_xp/advanced-heading', [
            'title' => $heading,
            'level' => $level,
            'islevel2' => $level === 2,

            'hasintro' => !empty($intro),
            'intro' => $intro,
            'helpicon' => $help ? $help->export_for_template($this) : null,

            'hasvisibility' => $visible !== null,
            'isvisible' => $visible,

            'hasactions' => !empty($actions),
            'actions' => array_map([$this, 'render'], $actions),

            'hasmenu' => !empty($menuitems),
            'menuitems' => $menuitems,
        ]);
    }

    /**
     * Confirm reset.
     *
     * @param string $title The title.
     * @param string $message The message.
     * @param \moodle_url $confirmurl The confirm URL.
     * @param \moodle_url $cancelurl The cancel URL.
     * @param array $options Some options.
     * @return string
     */
    public function confirm_reset($title, $message, \moodle_url $confirmurl, \moodle_url $cancelurl, $options = []) {
        return $this->confirm_step($title, $message, $confirmurl, $cancelurl, $options + [
            'confirmlabel' => get_string('reset', 'core'),
        ]);
    }

    /**
     * Confirm step.
     *
     * This supercedes the default confirm renderer to accomodate for variation between versions,
     * and to set our own sensible defaults. For instance, the confirm button is red by default.
     *
     * Note that the parameters are not the same as the {@see \core_renderer::confirm}.
     *
     * @param string $title The title.
     * @param string $message The message.
     * @param \moodle_url $confirmurl The confirm URL.
     * @param \moodle_url $cancelurl The cancel URL.
     * @param array $options Some options.
     * @return string
     */
    public function confirm_step($title, $message, \moodle_url $confirmurl, \moodle_url $cancelurl, $options = []) {
        global $CFG;
        if ($CFG->branch < 400) {
            return parent::confirm($message, $confirmurl, $cancelurl);
        }
        return parent::confirm($message, $confirmurl, $cancelurl, [
            'confirmtitle' => $title,
            'continuestr' => $options['confirmlabel'] ?? null,
            'cancelstr' => $options['cancellabel'] ?? null,
            'type' => defined('single_button::BUTTON_DANGER') ? single_button::BUTTON_DANGER : null,
        ]);
    }

    /**
     * Render a control menu.
     *
     * @param action_menu_link $actions
     */
    public function control_menu($actions) {
        $menu = new action_menu();

        // Without this, the control menu can wrap on the next line when placed next to another item.
        $menu->attributes['class'] .= ' xp-inline-block';

        // Styles copied from core_courseformat\output\local\content\cm::get_action_menu() in 4.1dev.
        $icon = $this->pix_icon('i/menu', get_string('menu', 'block_xp'));
        $menu->set_menu_trigger($icon, 'btn btn-icon d-flex align-items-center justify-content-center after:xp-hidden');

        foreach ($actions as $action) {
            $action->primary = false;
            $menu->add_secondary_action($action);
        }

        return $this->render($menu);
    }

    /**
     * Heading with divider.
     *
     * @param string $text The heading text.
     * @param array $options The options.
     * @return string
     */
    public function heading_with_divider($text, $options = []) {
        $options = array_merge(['level' => 3], $options);
        return html_writer::tag('div', $this->heading($text, $options['level'], 'xp-m-0'),
            ['class' => 'xp-mb-6 xp-mt-8 xp-border-0 xp-border-solid xp-border-t xp-border-gray-100 xp-pt-8']);
    }

    /**
     * Get a user's picture.
     *
     * @param object $user The user.
     * @return moodle_url The URL to the picture.
     */
    public function get_user_picture($user) {
        $pic = new user_picture($user);
        $pic->size = 1;
        return $pic->get_url($this->page);
    }

    /**
     * Print a level's badge.
     *
     * @param level $level The level.
     * @return string
     */
    public function level_badge(level $level) {
        return $this->level_badge_with_options($level);
    }

    /**
     * Small level badge.
     *
     * @param level $level The level.
     * @return string.
     */
    public function small_level_badge(level $level) {
        return $this->level_badge_with_options($level, ['small' => true]);
    }

    /**
     * Medium level badge.
     *
     * @param level $level The level.
     * @return string.
     */
    public function medium_level_badge(level $level) {
        return $this->level_badge_with_options($level, ['medium' => true]);
    }

    /**
     * Print a level's badge.
     *
     * @param level $level The level.
     * @param array $options The options.
     * @return string
     */
    protected function level_badge_with_options(level $level, array $options = []) {
        $size = null;
        if (!empty($options['medium'])) {
            $size = 'medium';
        } else if (!empty($options['small'])) {
            $size = 'small';
        }

        $badgeurl = $level instanceof level_with_badge ? $level->get_badge_url() : null;
        return $this->render_from_template('block_xp/level-badge', [
            'badgeurl' => $badgeurl ? $badgeurl->out(false) : null,
            'level' => $level->get_level(),
            'size' => $size,
        ]);
    }

    /**
     * Level name.
     *
     * @param level $level The level.
     * @param bool $force When forced, there will always be an name displayed.
     * @return string
     */
    public function level_name(level $level, $force = false) {
        $name = $level instanceof level_with_name ? $level->get_name() : null;
        if (empty($name) && $force) {
            $name = get_string('levelx', 'block_xp', $level->get_level());
        }
        if (empty($name)) {
            return '';
        }
        return html_writer::tag('div', $name, ['class' => 'level-name']);
    }


    /**
     * Levels grid.
     *
     * @param array $levels The levels.
     * @return string
     */
    public function levels_grid(array $levels) {

        // If at least one level has a custom name, we will always show the name.
        $alwaysshowname = array_reduce($levels, function($carry, $level) {
            $name = $level instanceof \block_xp\local\xp\level_with_name ? $level->get_name() : '';
            return $carry + !empty($name) ? 1 : 0;
        }, 0) > 0;

        $o = '';
        $o .= html_writer::start_div('block_xp-level-grid');
        foreach ($levels as $level) {
            $desc = $level instanceof \block_xp\local\xp\level_with_description ? $level->get_description() : '';
            $classes = ['block_xp-level-boxed'];
            if ($desc) {
                $classes[] = 'block_xp-level-boxed-with-desc';
            }

            $o .= html_writer::start_div(implode(' ', $classes));
            $o .= html_writer::start_div('block_xp-level-box');
            $o .= html_writer::start_div('block_xp-level-no');
            $o .= '#' . $level->get_level();
            $o .= html_writer::end_div();
            $o .= html_writer::start_div();
            $o .= $this->level_badge($level);
            $o .= html_writer::end_div();
            $o .= $this->level_name($level, $alwaysshowname);
            $o .= html_writer::start_div();
            $o .= $this->xp($level->get_xp_required());
            $o .= html_writer::end_div();
            $o .= html_writer::start_div('block_xp-level-desc');
            $o .= $desc;
            $o .= html_writer::end_div();
            $o .= html_writer::end_div();
            $o .= html_writer::end_div();
        }
        $o .= html_writer::end_div();
        return $o;
    }

    /**
     * Levels preview.
     *
     * @param level[] $levels The levels.
     * @return string
     */
    public function levels_preview(array $levels) {
        $o = '';

        $o .= html_writer::start_div('xp-grid xp-gap-2 xp-grid-cols-6 sm:xp-grid-cols-10');
        foreach ($levels as $level) {
            $o .= html_writer::start_div('xp-relative xp-bg-gray-100 xp-rounded xp-p-1');
            $o .= html_writer::div('' . $level->get_level(), 'xp-whitespace-nowrap xp-text-center xp-mb-1 xp-absolute'
                . ' xp-top-0.5 xp-left-0.5 xp-text-2xs xp-text-gray-500');
            $o .= $this->small_level_badge($level);
            $o .= html_writer::end_div();
        }
        $o .= html_writer::end_div();

        return $o;
    }

    /**
     * Return the notices.
     *
     * @param \block_xp\local\course_world $world The world.
     * @return string The notices.
     */
    public function notices(\block_xp\local\course_world $world) {
        global $CFG;
        $o = '';

        if (!$world->get_access_permissions()->can_manage()) {
            return $o;
        }

        $notice = null;
        $candidates = [
            [
                static::NOTICE_FLAG_QUEST,
                function() {
                    $questblogurl = new moodle_url('https://www.levelup.plus/blog/quest-moodle-gamification-plugin?ref=xp_notice');
                    $questurl = new moodle_url('https://www.levelup.plus/quest?ref=xp_notice');
                    return strip_tags(markdown_to_html(get_string('questreleasenotice', 'block_xp', (object) [
                        'questblogurl' => $questblogurl->out(false),
                        'questurl' => $questurl->out(false),
                    ])), '<a><em><strong>');
                },
            ], [
                $this->noticesflag,
                function() {
                    $moodleorgurl = new moodle_url('https://moodle.org/plugins/view.php?plugin=block_xp');
                    $githuburl = new moodle_url('https://github.com/FMCorz/moodle-block_xp');
                    return get_string('likenotice', 'block_xp', (object) [
                        'moodleorg' => $moodleorgurl->out(),
                        'github' => $githuburl->out(),
                    ]);
                },
            ],
        ];
        foreach ($candidates as $candidate) {
            if (!get_user_preferences($candidate[0], false)) {
                $notice = $candidate;
                break;
            }
        }

        if ($notice) {
            list($flag, $textfn) = $notice;

            if ($CFG->branch >= 403) {
                $this->page->requires->js_amd_inline("require(['core_user/repository'], function(UserRepo) {
                    const flag = '$flag';
                    const n = document.querySelector('.block-xp-rocks');
                    if (!n) return;
                    n.addEventListener('click', function(e) {
                        e.preventDefault();
                        UserRepo.setUserPreference(flag, true);
                        const notice = document.querySelector('.block-xp-notices');
                        if (!notice) return;
                        notice.style.display = 'none';
                    });
                });");

            } else {
                require_once($CFG->libdir . '/ajax/ajaxlib.php');
                user_preference_allow_ajax_update($flag, PARAM_BOOL);

                $this->page->requires->js_amd_inline("require([], function() {
                    const flag = '$flag';
                    const n = document.querySelector('.block-xp-rocks');
                    if (!n) return;
                    n.addEventListener('click', function(e) {
                        e.preventDefault();
                        M.util.set_user_preference(flag, 1);
                        const notice = document.querySelector('.block-xp-notices');
                        if (!notice) return;
                        notice.style.display = 'none';
                    });
                });");
            }

            $icon = new pix_icon('t/close', get_string('dismissnotice', 'block_xp'), 'block_xp');
            $actionicon = $this->action_icon(new moodle_url($this->page->url), $icon, null, ['class' => 'block-xp-rocks']);

            $text = html_writer::start_div('xp-flex xp-gap-1');
            $text .= html_writer::div($textfn(), 'xp-flex-1 [&_a]:xp-font-normal [&_a]:xp-underline');
            $text .= html_writer::div($actionicon, 'xp-grow-0 dismiss-action');
            $text .= html_writer::end_div();

            $o .= html_writer::div($this->notification_without_close($text, 'success'),
                'block_xp-dismissable-notice block-xp-notices');
        }

        return $o;
    }

    /**
     * Outputs the navigation.
     *
     * This specifically requires a course_world and not a world.
     *
     * @param course_world $world The world.
     * @param string $page The page we are on.
     * @return string The navigation.
     * @deprecated Since Level Up XP 3.12, use tab_navigation instead.
     */
    public function course_world_navigation(course_world $world, $page) {
        debugging('The method course_world_navigation is deprecated, please use tab_navigation instead.', DEBUG_DEVELOPER);
        $factory = \block_xp\di::get('course_world_navigation_factory');
        $links = $factory->get_course_navigation($world);
        // If there is only one page, then that is the page we are on.
        if (count($links) <= 1) {
            return '';
        }
        return $this->tab_navigation($links, $page);
    }

    /**
     * Output a JSON script.
     *
     * @param mixed $data The data.
     * @param string $id The HTML ID to use.
     * @return string
     */
    public function json_script($data, $id) {
        $jsondata = json_encode($data, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
        return html_writer::tag('script', $jsondata, ['id' => $id, 'type' => 'application/json']);
    }

    /**
     * Get the context of the navbar widget.
     *
     * @param course_world $world The world.
     * @param state $state The user's state.
     * @return array
     */
    protected function get_navbar_widget_context(course_world $world, state $state) {
        $urlresolver = \block_xp\di::get('url_resolver');
        $worldconfig = $world->get_config();

        $infopageurl = null;
        if ($worldconfig->get('enableinfos')) {
            $infopageurl = $urlresolver->reverse('infos', ['courseid' => $world->get_courseid()]);
        }

        $leaderboardurl = null;
        if ($worldconfig->get('enableladder')) {
            $leaderboardurl = $urlresolver->reverse('ladder', ['courseid' => $world->get_courseid()]);
        }

        $validurls = array_filter([$infopageurl, $leaderboardurl]);
        $linkurl = reset($validurls) ?: null;

        return [
            'badgehtml' => $this->small_level_badge($state->get_level()),
            'linkurl' => $linkurl ? $linkurl->out(false) : null,
            'infopageurl' => $infopageurl ? $infopageurl->out(false) : null,
            'leaderboardurl' => $leaderboardurl ? $leaderboardurl->out(false) : null,
        ];
    }

    /**
     * Make a single button.
     *
     * This is mostly for the convenience of handling multiple versions.
     *
     * @param moodle_url $url The URL.
     * @param string $text The text.
     * @param array $options The options, see code.
     * @return single_button
     */
    public function make_single_button($url, $text, $options = []) {
        $method = $options['method'] ?? 'get';
        $button = new single_button($url, $text, $method);

        if (!empty($options['primary'])) {
            if (defined('single_button::BUTTON_PRIMARY')) {
                $button->type = single_button::BUTTON_PRIMARY;
            } else {
                $button->primary = true;
            }
        } else if (!empty($options['danger'])) {
            if (defined('single_button::BUTTON_DANGER')) {
                $button->type = single_button::BUTTON_DANGER;
            }
        }

        return $button;
    }

    /**
     * Navbar widget.
     *
     * @param course_world $world The world.
     * @param state $state The user's state.
     * @return string
     */
    public function navbar_widget(course_world $world, state $state) {
        return $this->render_from_template('block_xp/navbar-widget', $this->get_navbar_widget_context($world, $state));
    }

    /**
     * New dot.
     *
     * @return string
     */
    public function new_dot() {
        return html_writer::div(html_writer::tag('span', get_string('new'), ['class' => 'accesshide']), 'has-new');
    }

    /**
     * Print a notification without a close button.
     *
     * @param string|lang_string $message The message.
     * @param string $type The notification type.
     * @return string
     */
    public function notification_without_close($message, $type) {
        if (class_exists('core\output\notification')) {

            // Of course, it would be too easy if they didn't add and change constants
            // between two releases... Who reads the upgrade.txt, seriously?
            if (defined('core\output\notification::NOTIFY_INFO')) {
                $info = core\output\notification::NOTIFY_INFO;
            } else {
                $info = core\output\notification::NOTIFY_MESSAGE;
            }
            if (defined('core\output\notification::NOTIFY_SUCCESS')) {
                $success = core\output\notification::NOTIFY_SUCCESS;
            } else {
                $success = core\output\notification::NOTIFY_MESSAGE;
            }
            if (defined('core\output\notification::NOTIFY_WARNING')) {
                $warning = core\output\notification::NOTIFY_WARNING;
            } else {
                $warning = core\output\notification::NOTIFY_REDIRECT;
            }
            if (defined('core\output\notification::NOTIFY_ERROR')) {
                $error = core\output\notification::NOTIFY_ERROR;
            } else {
                $error = core\output\notification::NOTIFY_PROBLEM;;
            }

            $typemappings = [
                'success'           => $success,
                'info'              => $info,
                'warning'           => $warning,
                'error'             => $error,
                'notifyproblem'     => $error,
                'notifytiny'        => $error,
                'notifyerror'       => $error,
                'notifysuccess'     => $success,
                'notifymessage'     => $info,
                'notifyredirect'    => $info,
                'redirectmessage'   => $info,
            ];
        } else {
            // Old-style notifications.
            $typemappings = [
                'success'           => 'notifysuccess',
                'info'              => 'notifymessage',
                'warning'           => 'notifyproblem',
                'error'             => 'notifyproblem',
                'notifyproblem'     => 'notifyproblem',
                'notifytiny'        => 'notifyproblem',
                'notifyerror'       => 'notifyproblem',
                'notifysuccess'     => 'notifysuccess',
                'notifymessage'     => 'notifymessage',
                'notifyredirect'    => 'notifyredirect',
                'redirectmessage'   => 'redirectmessage',
            ];
        }

        $type = $typemappings[$type];

        if (class_exists('core\output\notification')) {
            $notification = new \core\output\notification($message, $type);
            if (method_exists($notification, 'set_show_closebutton')) {
                $notification->set_show_closebutton(false);
            }
            if (method_exists($notification, 'set_announce')) {
                $notification->set_announce(false);
            }
            return $this->render($notification);
        }

        return $this->notification($message, $type);
    }

    /**
     * Page size selector.
     *
     * @param array $options Array of [(int) $perpage, (moodle_url) $url].
     * @param int $current The current selectin.
     * @return string
     */
    public function pagesize_selector($options, $current) {
        $o = '';
        $o .= html_writer::start_div('xp-text-center');
        $o .= html_writer::start_tag('small');
        $o .= get_string('perpagecolon', 'block_xp') . ' ';

        $options = array_values($options);
        $lastindex = count($options) - 1;

        foreach ($options as $i => $option) {
            list($perpage, $url) = $option;
            $o .= $current == $perpage ? $current : html_writer::link($url, (string) $perpage);
            $o .= $i < $lastindex ? ' - ' : '';
        }

        $o .= html_writer::end_tag('small');
        $o .= html_writer::end_div();
        return $o;
    }

    /**
     * Override pix_url to auto-handle deprecation.
     *
     * It's just simpler than having to deal with differences between
     * Moodle < 3.3, and Moodle >= 3.3.
     *
     * @param string $image The file.
     * @param string $component The component.
     * @return string
     */
    public function pix_url($image, $component = 'moodle') {
        if (method_exists($this, 'image_url')) {
            return $this->image_url($image, $component);
        }
        return parent::pix_url($image, $component);
    }

    /**
     * Override render method.
     *
     * @param renderable $renderable The renderable.
     * @param array $options Options.
     * @return string
     */
    public function render(renderable $renderable, $options = []) {
        if ($renderable instanceof block_xp_ruleset) {
            return $this->render_block_xp_ruleset($renderable, $options);
        } else if ($renderable instanceof block_xp_rule) {
            return $this->render_block_xp_rule($renderable, $options);
        }
        return parent::render($renderable);
    }

    /**
     * Renders a block XP filter.
     *
     * Not very proud of the way I implement this... The HTML is tied to Javascript
     * and to the rule objects themselves. Careful when changing something!
     *
     * @param block_xp_filter $filter The filter.
     * @return string
     */
    public function render_block_xp_filter($filter) {
        static $i = 0;
        $o = '';
        $basename = 'filters[' . $i++ . ']';

        $o .= html_writer::start_tag('li', ['class' => 'filter', 'data-basename' => $basename]);

        if ($filter->is_editable()) {
            $content = '';
            $content .= html_writer::start_div('xp-flex xp-min-h-10 xp-group');

            $content .= html_writer::start_div('xp-flex-none xp-h-10 xp-flex xp-items-center');
            $content .= $this->render(new pix_icon('i/dragdrop', get_string('moverule', 'block_xp'), '',
                ['class' => 'iconsmall filter-move']));
            $content .= html_writer::end_div();

            $content .= html_writer::start_div('xp-flex-1 xp-overflow-hidden xp-min-h-full xp-flex'
                . ' xp-items-center xp-leading-tight');
            $content .= get_string('awardaxpwhen', 'block_xp',
                html_writer::empty_tag('input', [
                    'type' => 'text',
                    'value' => $filter->get_points(),
                    'size' => 3,
                    'name' => $basename . '[points]',
                    'class' => 'form-control block_xp-form-control-inline !xp-mr-1', ])
            );
            $content .= html_writer::end_div();

            $content .= html_writer::start_div('xp-flex-none xp-h-10 xp-flex xp-items-center');
            $content .= $this->render_block_xp_rule_delete_action_link('deleterule', 'filter-delete');
            $content .= html_writer::end_div();

            $content .= html_writer::end_div();

            $o .= html_writer::div($content, 'xp-group');
            $o .= html_writer::empty_tag('input', [
                    'type' => 'hidden',
                    'value' => $filter->get_id(),
                    'name' => $basename . '[id]', ]);
            $o .= html_writer::empty_tag('input', [
                    'type' => 'hidden',
                    'value' => $filter->get_sortorder(),
                    'name' => $basename . '[sortorder]', ]);
            $basename .= '[rule]';

        } else {
            $o .= html_writer::tag('p', get_string('awardaxpwhen', 'block_xp', $filter->get_points()));
        }
        $o .= html_writer::start_tag('ul', ['class' => 'filter-rules']);
        $o .= $this->render($filter->get_rule(), ['iseditable' => $filter->is_editable(), 'basename' => $basename]);
        $o .= html_writer::end_tag('ul');
        $o .= html_writer::end_tag('li');
        return $o;
    }

    /**
     * Renders a block XP rule.
     *
     * @param block_xp_rule_base $rule The rule.
     * @param array $options
     * @return string
     */
    public function render_block_xp_rule($rule, $options) {
        static $i = 0;
        $iseditable = !empty($options['iseditable']);
        $basename = isset($options['basename']) ? $options['basename'] : '';
        if ($iseditable) {
            $content = '';
            $content .= html_writer::start_div('xp-flex xp-min-h-10');

            $content .= html_writer::start_div('xp-flex-none xp-h-10 xp-flex xp-items-center');
            $content .= $this->render(new pix_icon('i/dragdrop', get_string('movecondition', 'block_xp'), '',
                ['class' => 'iconsmall rule-move']));
            $content .= html_writer::end_div();

            $content .= html_writer::start_div('xp-flex-1 xp-overflow-hidden xp-min-h-full xp-flex xp-items-center'
                . ' xp-leading-tight');
            $content .= html_writer::div($rule->get_form($basename), 'xp-w-full xp-max-w-full');
            $content .= html_writer::end_div();

            $content .= html_writer::start_div('xp-flex-none xp-h-10 xp-flex xp-items-center');
            $content .= $this->render_block_xp_rule_delete_action_link('deletecondition', 'rule-delete');
            $content .= html_writer::end_div();

            $content .= html_writer::end_div();
        } else {
            $content = s($rule->get_description());
        }
        $o = '';
        $o .= html_writer::start_tag('li', ['class' => 'rule rule-type-rule xp-group']);
        $o .= html_writer::tag('div', $content, ['class' => 'rule-definition', 'data-basename' => $basename]);
        $o .= html_writer::end_tag('li');
        return $o;
    }

    /**
     * Renders a block XP ruleset.
     *
     * @param block_xp_ruleset $ruleset The ruleset.
     * @param array $options The options
     * @return string
     */
    public function render_block_xp_ruleset($ruleset, $options) {
        static $i = 0;
        $iseditable = !empty($options['iseditable']);
        $basename = isset($options['basename']) ? $options['basename'] : '';
        $o = '';
        $o .= html_writer::start_tag('li', ['class' => 'rule rule-type-ruleset']);
        if ($iseditable) {
            $content = '';
            $content .= html_writer::start_div('xp-flex xp-min-h-10');

            $content .= html_writer::start_div('xp-flex-none xp-h-10 xp-flex xp-items-center');
            $content .= $this->render(new pix_icon('i/dragdrop', get_string('movecondition', 'block_xp'), '',
                ['class' => 'iconsmall rule-move']));
            $content .= html_writer::end_div();

            $content .= html_writer::start_div('xp-flex-1 xp-overflow-hidden xp-min-h-full xp-flex'
                . ' xp-items-center xp-leading-tight');
            $content .= html_writer::div($ruleset->get_form($basename));
            $content .= html_writer::end_div();

            $content .= html_writer::start_div('xp-flex-none xp-h-10 xp-flex xp-items-center');
            $content .= $this->render_block_xp_rule_delete_action_link('deletecondition', 'rule-delete');
            $content .= html_writer::end_div();

            $content .= html_writer::end_div();
        } else {
            $content = s($ruleset->get_description());
        }
        $o .= html_writer::tag('div', $content, ['class' => 'rule-definition xp-h-10 xp-group', 'data-basename' => $basename]);
        $o .= html_writer::start_tag('ul', ['class' => 'rule-rules', 'data-basename' => $basename . '[rules]']);
        foreach ($ruleset->get_rules() as $rule) {
            if ($iseditable) {
                $options['basename'] = $basename . '[rules][' . $i++ . ']';
            }
            $o .= $this->render($rule, $options);
        }
        if ($iseditable) {
            $o .= html_writer::start_tag('li', ['class' => 'rule-add']);
            $o .= $this->action_link('#', get_string('addacondition', 'block_xp'), null, null,
                new pix_icon('t/add', '', '', ['class' => 'iconsmall']));
            $o .= html_writer::end_tag('li');
        }
        $o .= html_writer::end_tag('ul');
        $o .= html_writer::end_tag('li');
        return $o;
    }

    /**
     * Render the delete action icon in rules.
     *
     * @param string $str The string identifier for the title.
     * @param string $classname Classes to add to the action link.
     * @return string
     */
    public function render_block_xp_rule_delete_action_link($str, $classname = '') {
        $icon = new pix_icon('t/delete', get_string($str, 'block_xp'), '', ['class' => 'iconsmall']);
        return $this->action_link('#', '', null, [
            'class' => $classname . ' supports-hover:xp-opacity-0 supports-hover:xp-pointer-events-none'
                . ' focus:xp-opacity-100 focus:xp-pointer-events-auto'
                . ' group-hover:xp-opacity-100 group-hover:xp-pointer-events-auto xp-transition-opacity',
        ], $icon);
    }

    /**
     * Render a dismissable notice.
     *
     * Yes, we cannot use CSS IDs in there because they are stripped out... turns out they
     * are considered dangerous. Oh well, we use a class instead. Not pretty, but it works...
     *
     * @param renderable $notice The notice.
     * @return string
     */
    public function render_dismissable_notice(renderable $notice) {
        $id = html_writer::random_id();

        // Tell the indicator that it should be expecing this notice.
        $indicator = \block_xp\di::get('user_notice_indicator');
        if ($indicator instanceof \block_xp\local\indicator\user_indicator_with_acceptance) {
            $indicator->set_acceptable_user_flag($notice->name);
        }

        $url = \block_xp\di::get('ajax_url_resolver')->reverse('notice/dismiss', ['name' => $notice->name]);
        $this->page->requires->js_init_call(<<<EOT
            Y.one('.$id .dismiss-action a').on('click', function(e) {
                e.preventDefault();
                Y.one('.$id').hide();
                var url = '$url';
                var cfg = {
                    method: 'POST'
                };
                Y.io(url, cfg);
            });
EOT
        );

        $icon = new pix_icon('t/close', get_string('dismissnotice', 'block_xp'), 'block_xp');
        $actionicon = $this->action_icon('#', $icon, null);
        $text = html_writer::div($actionicon, 'dismiss-action') . $notice->message;

        return html_writer::div($this->notification_without_close($text, $notice->type),
            'block_xp-dismissable-notice ' . $id);
    }

    /**
     * Render the filters widget.
     *
     * /!\ We only support one editable widget per page!
     *
     * @param renderable $widget The widget.
     * @return string
     */
    public function render_filters_widget(renderable $widget) {
        $containerid = html_writer::random_id();

        if ($widget->editable) {
            $templatefilter = $this->render($widget->filter);

            $templatetypes = [];
            foreach ($widget->rules as $rule) {
                $templatetypes[] = [
                    'name' => $rule->name,
                    'info' => !empty($rule->info) ? $rule->info : null,
                    'template' => $this->render($rule->rule, ['iseditable' => true, 'basename' => 'XXXXX']),
                ];
            }

            // Prepare Javascript.
            $this->page->requires->yui_module('moodle-block_xp-filters', 'Y.M.block_xp.Filters.init', [[
                'containerSelector' => '#' . $containerid,
                'filter' => $templatefilter,
                'rules' => $templatetypes,
            ], ]);
            $this->page->requires->strings_for_js(['pickaconditiontype', 'deleterule', 'deletecondition'], 'block_xp');
            $this->page->requires->strings_for_js(['areyousure'], 'core');
        }

        echo html_writer::start_div('block-xp-filters-wrapper', ['id' => $containerid]);

        $addlink = '';
        if ($widget->editable) {
            $addlink = html_writer::start_tag('li', ['class' => 'filter-add']);
            $addlink .= $this->action_link('#', get_string('addarule', 'block_xp'), null, null,
                new pix_icon('t/add', '', '', ['class' => 'iconsmall']));
            $addlink .= html_writer::end_tag('li');
        }

        $class = $widget->editable ? 'filters-editable' : 'filters-readonly';
        echo html_writer::start_tag('ul', ['class' => 'filters-list ' . $class]);
        echo $addlink;

        foreach ($widget->filters as $filter) {
            echo $this->render($filter);
            echo $addlink;
        }

        echo html_writer::end_tag('ul');

        echo html_writer::end_div();
    }

    /**
     * Render the filters widget group.
     *
     * @param renderable $element The group.
     * @return string
     */
    public function render_filters_widget_element(renderable $element) {
        if (!empty($element->title)) {
            $title = $element->title . ($element->helpicon ? $this->render($element->helpicon) : '');
            echo html_writer::tag('h4', $title);
            if (!empty($element->description)) {
                echo $element->description;
            }
        }
        $this->render($element->widget);
    }

    /**
     * Render the filters widget group.
     *
     * @param renderable $group The group.
     * @return string
     */
    public function render_filters_widget_group(renderable $group) {
        global $CFG;

        $formid = html_writer::random_id();

        // The form change checker YUI module is deprecated since Moodle 4.0.
        if ($CFG->branch >= 400) {
            $this->page->requires->js_call_amd('core_form/changechecker', 'watchFormById', [$formid]);
        } else {
            $this->page->requires->string_for_js('changesmadereallygoaway', 'moodle');
            $this->page->requires->yui_module('moodle-core-formchangechecker', 'M.core_formchangechecker.init',
                [['formid' => $formid]]);
        }

        echo html_writer::start_div('block-xp-filters-group');
        echo html_writer::start_tag('form', ['method' => 'POST', 'class' => 'block-xp-filters', 'id' => $formid]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

        $elements = $group->elements;
        foreach ($elements as $element) {
            echo $this->render($element);
        }

        echo html_writer::start_tag('p', ['class' => 'block-xp-filters-submit-actions']);
        echo html_writer::empty_tag('input', ['value' => get_string('savechanges'), 'type' => 'submit', 'name' => 'save',
            'class' => 'btn btn-primary', ]);
        echo ' ';
        echo html_writer::empty_tag('input', ['value' => get_string('cancel'), 'type' => 'submit', 'name' => 'cancel',
            'class' => 'btn btn-default btn-secondary', ]);
        echo html_writer::end_tag('p');
        echo html_writer::end_tag('form');

        echo html_writer::end_div();
    }

    /**
     * Render a notice.
     *
     * @param renderable $notice The notice.
     * @return string
     */
    public function render_notice(renderable $notice) {
        return $this->notification_without_close($notice->message, $notice->type);
    }

    /**
     * Get the progress bar mustache context.
     *
     * @param state $state The renderable object.
     * @param bool $percentagetogo Show the percentage to go.
     * @return array
     */
    protected function get_progress_bar_context(state $state, $percentagetogo = false) {
        global $CFG;

        $pc = $state->get_ratio_in_level() * 100;
        $nextinvalue = $this->xp($state->get_total_xp_in_level() - $state->get_xp_in_level());
        if ($percentagetogo) {
            $value = format_float(max(0, 100 - $pc), 1);
            // Quick hack to support localisation of percentages without having to define a new language
            // string for older versions. When the string is not available, we provide a sensible fallback.
            if ($CFG->branch >= 36) {
                $nextinvalue = get_string('percents', 'core', $value);
            } else {
                $nextinvalue = $value . '%';
            }
        }

        return [
            // 100% completion of a level is 0% of the next one, unless it's the final one.
            'atmaxlevel' => $pc >= 100,
            'nonfull' => $pc < 100,
            'nonzero' => $pc != 0,
            'percentage' => $pc,
            'percentagehuman' => $pc > 0 ? floor($pc) : ceil($pc),
            'nextinvaluehtml' => $nextinvalue,
        ];
    }

    /**
     * Returns the progress bar rendered.
     *
     * @param state $state The renderable object.
     * @param bool $percentagetogo Show the percentage to go.
     * @return string HTML produced.
     */
    public function progress_bar(state $state, $percentagetogo = false) {
        return $this->render_from_template('block_xp/progress-bar', $this->get_progress_bar_context($state, $percentagetogo));
    }

    /**
     * Initialise a react module.
     *
     * @param string $module The AMD name of the module.
     * @param object|array $props The props.
     * @return void
     */
    public function react_module($module, $props) {
        $id = html_writer::random_id('block_xp-react-app');
        $propsid = html_writer::random_id('block_xp-react-app-props');

        $o = '';
        $o .= html_writer::start_div('block_xp-react', ['id' => $id]);
        $o .= html_writer::start_div('block_xp-react-loading');
        $o .= html_writer::start_div('xp-grid xp-grid-cols-2 xp-gap-4 xp-animate-pulse');
        $o .= html_writeR::div('', 'xp-col-span-2 xp-bg-gray-100 xp-rounded xp-h-4');
        $o .= html_writeR::div('', 'xp-bg-gray-100 xp-rounded xp-h-4');
        $o .= html_writeR::div('', 'xp-bg-gray-100 xp-rounded xp-h-4');
        $o .= html_writer::end_div();
        $o .= html_writer::end_div();
        $o .= html_writer::end_div();

        $o .= $this->json_script($props, $propsid);

        $this->page->requires->js_amd_inline("
            require(['block_xp/react-launcher'], function(Launcher) {
                Launcher.launch('$module', '$id', '$propsid');
            });
        ");

        return $o;
    }

    /**
     * Recent activity.
     *
     * @param activity[] $activity The activity entries.
     * @param moodle_url $moreurl The URL to view more (deprecated).
     * @return string
     */
    public function recent_activity(array $activity, moodle_url $moreurl = null) {
        return $this->render_from_template('block_xp/recent-activity', [
            'hasrecentactivities' => !empty($activity),
            'recentactivities' => array_values(array_map(function($entry) {
                $xp = $entry instanceof \block_xp\local\activity\activity_with_xp ? $entry->get_xp() : null;
                return [
                    'date' => userdate($entry->get_date()->getTimestamp()),
                    'dateagotiny' => $this->tiny_time_ago($entry->get_date()),
                    'description' => $entry->get_description(),
                    'xphtml' => $xp !== null ? $this->xp($xp) : '',
                ];
            }, $activity)),
        ]);
    }

    /**
     * Render XP widget.
     *
     * @param xp_widget $widget The widget.
     * @return string
     */
    public function render_xp_widget(xp_widget $widget) {
        return $this->render_from_template('block_xp/xp-widget', $widget->export_for_template($this));
    }

    /**
     * Rules page loading check init.
     *
     * @return html
     */
    public function rules_page_loading_check_init() {
        return $this->render_from_template('block_xp/rules-page-loading-error', []);
    }

    /**
     * Rules page loading check success.
     *
     * @return html
     */
    public function rules_page_loading_check_success() {
        return $this->render_from_template('block_xp/rules-page-loading-success', []);
    }

    /**
     * Sub navigation.
     *
     * @param array $items The items.
     * @param string $activenode The active node.
     * @return string
     */
    public function sub_navigation($items, $activenode) {
        return $this->render_from_template('block_xp/sub-navigation', [
            'items' => array_map(function($item) use ($activenode) {
                $url = $item['url'];
                if ($url instanceof moodle_url) {
                    $url = $url->out(false);
                }
                return array_merge($item, [
                    'url' => $url,
                    'current' => $item['id'] == $activenode,
                ]);
            }, $items),
        ]);
    }

    /**
     * Outputs the navigation.
     *
     * @param array $items The items.
     * @param string $activenode The active node.
     * @return string The navigation.
     */
    public function tab_navigation($items, $activenode) {
        $tabs = array_map(function($link) {
            // If we don't have a URL, but we have children take the first child's.
            if (empty($link['url']) && !empty($link['children'])) {
                $firstchild = reset($link['children']);
                $url = $firstchild['url'];
                $link = array_merge($link, ['url' => $url]);
            }
            return new tabobject($link['id'], $link['url'], $link['text'], clean_param($link['text'], PARAM_NOTAGS));
        }, array_filter($items, function($item) {
            // Remove the items that define children but do not have any.
            return !isset($item['children']) || !empty($item['children']);
        }));
        return html_writer::div($this->tabtree($tabs, $activenode), 'block_xp-page-nav');
    }

    /**
     * Tiny time ago string.
     *
     * @param DateTime $dt The date object.
     * @return string
     */
    public function tiny_time_ago(DateTime $dt) {
        $now = new \DateTime();
        $diff = $now->getTimestamp() - $dt->getTimestamp();
        $ago = '?';

        if ($diff < 15) {
            $ago = get_string('tinytimenow', 'block_xp');
        } else if ($diff < 45) {
            $ago = get_string('tinytimeseconds', 'block_xp', $diff);
        } else if ($diff < HOURSECS * 0.7) {
            $ago = get_string('tinytimeminutes', 'block_xp', round($diff / 60));
        } else if ($diff < DAYSECS * 0.7) {
            $ago = get_string('tinytimehours', 'block_xp', round($diff / HOURSECS));
        } else if ($diff < DAYSECS * 7 * 0.7) {
            $ago = get_string('tinytimedays', 'block_xp', round($diff / DAYSECS));
        } else if ($diff < DAYSECS * 30 * 0.7) {
            $ago = get_string('tinytimeweeks', 'block_xp', round($diff / (DAYSECS * 7)));
        } else if ($diff < DAYSECS * 365) {
            $ago = userdate($dt->getTimestamp(), get_string('tinytimewithinayearformat', 'block_xp'));
        } else {
            $ago = userdate($dt->getTimestamp(), get_string('tinytimeolderyearformat', 'block_xp'));
        }

        return $ago;
    }

    /**
     * Renders a user's avatar.
     *
     * This is similar to user_picture, except that it takes a URL
     * as argument instead of taking a user. It's expected to be used
     * alongside text that describes the user, so that the avatar does
     * not need to be announced to screen readers.
     *
     * This always returns an image.
     *
     * @param moodle_url|null $url The URL.
     * @param moodle_url|null $link The link.
     * @return string
     */
    public function user_avatar(moodle_url $url = null, moodle_url $link = null) {
        if (!$url) {
            $url = user_utils::default_picture();
        }

        // Simulate the behaviour of user_picture.
        $img = html_writer::empty_tag('img', [
            'src' => $url->out(false),
            'role' => 'presentation',
            'class' => 'userpicture',
            'width' => 35,
            'height' => 35,
            'alt' => '',
        ]);

        if ($link) {
            return html_writer::link($link->out(false), $img, [
                'class' => 'd-inline-block aabtn',
            ]);
        }

        return $img;
    }

    /**
     * Format an amount of XP.
     *
     * @param int $amount The XP.
     * @return string
     */
    public function xp($amount) {
        $xp = $this->xp_human($amount);
        $o = '';
        $o .= html_writer::start_tag('span', ['class' => 'block_xp-xp']);
        $o .= html_writer::tag('span', $xp, ['class' => 'pts']);
        $o .= html_writer::tag('span', 'xp', ['class' => 'sign sign-sup']);
        $o .= html_writer::end_tag('span');
        return $o;
    }

    /**
     * A highlight of the points.
     *
     * @param int $amount The XP.
     * @param bool $bright Whether the highlight should be "bright".
     */
    public function xp_highlight($amount, $bright = true) {
        $colourclass = $bright ? 'xp-bg-yellow-200' : 'xp-bg-gray-200';
        return html_writer::tag(
            'span',
            html_writer::tag('span', $this->xp($amount), [
                'class' => "xp-inline-block $colourclass xp-px-2 xp-py-0.5 xp-rounded-xl xp-leading-none",
            ]),
            ['class' => 'block_xp block_xp-xp-highlight']
        );
    }

    /**
     * Formats points for human.
     *
     * @param int $points
     */
    public function xp_human($points) {
        $xp = (int) $points;
        if (abs($xp) > 999) {
            $thousandssep = get_string('thousandssep', 'langconfig');
            $xp = number_format($xp, 0, '.', $thousandssep);
        }
        return $xp;
    }

    /**
     * Render XP widget navigation.
     *
     * @param array $actions The actions.
     * @return string
     */
    public function xp_widget_navigation(array $actions) {
        $o = '';
        $o .= html_writer::start_tag('nav');
        $o .= implode('', array_map(function(action_link $action) {
            $content = html_writer::div($this->render($action->icon));
            $content .= html_writer::div($action->text);
            return html_writer::link($action->url, $content, ['class' => 'nav-button']);
        }, $actions));
        $o .= html_writer::end_tag('nav');
        return $o;
    }

}
