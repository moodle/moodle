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
 * Trait for core renderer.
 *
 * @package    theme_adaptable
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @copyright  2021 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable\output;

use context_course;
use core\output\action_link;
use core\output\custom_menu_item;
use core\output\html_writer;
use core\output\pix_icon;
use core\url;
use core_block\output\block_contents;
use navigation_node;
use stdClass;

/**
 * Trait for core and core maintenance renderers.
 */
trait core_renderer_toolbox {
    /** @var custom_menu_item language The language menu if created */
    protected $language = null;

    /**
     * Returns HTML attributes to use within the body tag. This includes an ID and classes.
     *
     * @since Moodle 2.5.1 2.6
     * @param string|array $additionalclasses Any additional classes to give the body tag,
     * @return string
     */
    public function body_attributes($additionalclasses = []) {
        if (\core_useragent::is_safari()) {
            if (is_array($additionalclasses)) {
                $additionalclasses[] = 'safari';
            } else {
                $additionalclasses .= ' safari';
            }
        }
        return parent::body_attributes($additionalclasses);
    }

    /**
     * Outputs the opening section of a box.
     *
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @param array $attributes An array of other
     * attributes to give the box.
     * @return string the HTML to output.
     */
    public function box_start($classes = 'generalbox', $id = null, $attributes = []) {
        $this->opencontainers->push('box', html_writer::end_tag('div'));
        $attributes['id'] = $id;
        $attributes['class'] = 'box ' . \renderer_base::prepare_classes($classes);
        return html_writer::start_tag('div', $attributes);
    }

    /**
     * Outputs a container.
     *
     * @param string $contents The contents of the box
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @param array $attributes Optional other attributes as array
     * @return string the HTML to output.
     */
    public function container($contents, $classes = null, $id = null, $attributes = []) {
        // Manipulate the grader report.
        if ((!is_null($classes)) && ($classes == 'gradeparent')) {
            $contents = preg_replace('/<th class="(header|userfield)(.*?)>(.*?)<\/th>/is',
                '<th class="$1$2><div class="d-flex">$3</div></th>', $contents);
        }
        return $this->container_start($classes, $id, $attributes) . $contents . $this->container_end();
    }

    /**
     * Returns user profile menu items.
     *
     * returns array of objects suitable for adding to an action_menu as items.
     */
    protected function user_profile_menu_items() {
        global $CFG, $COURSE;
        $retval = [];

        /* False or theme setting name to first array param (not all links have settings).
           Entry type: link, divider or user.
           False or Moodle version number to second param (only some links check version).
           URL for link in third param.
           Link text in fourth parameter.
           Icon in fifth param. */
        $usermenuitems = [];
        $usermenuitems[] = ['enablemy', 'link', false, new url('/my'), get_string('myhome'),
            \theme_adaptable\toolbox::getfontawesomemarkup('dashboard', ['mr-1']), ];
        $usermenuitems[] = ['enableprofile', 'link', false, new url('/user/profile.php'), get_string('viewprofile'),
            \theme_adaptable\toolbox::getfontawesomemarkup('user', ['mr-1']), ];
        $usermenuitems[] = ['enableeditprofile', 'link', false, new url('/user/edit.php'), get_string('editmyprofile'),
            \theme_adaptable\toolbox::getfontawesomemarkup('cog', ['mr-1']), ];
        $usermenuitems[] = ['enableaccesstool', 'link', false, new url('/local/accessibilitytool/manage.php'),
            get_string('enableaccesstool', 'theme_adaptable'),
            \theme_adaptable\toolbox::getfontawesomemarkup('low-vision', ['mr-1']), ];
        $usermenuitems[] = ['enableprivatefiles', 'link', false, new url('/user/files.php'),
            get_string('privatefiles', 'block_private_files'), \theme_adaptable\toolbox::getfontawesomemarkup('file', ['mr-1']), ];
        if (\theme_adaptable\toolbox::kalturaplugininstalled()) {
            $usermenuitems[] = [false, 'link', false, new url('/local/mymedia/mymedia.php'),
                get_string('nav_mymedia', 'local_mymedia'), $this->pix_icon('my-media', '', 'local_mymedia'), ];
        }
        $usermenuitems[] = ['enablegrades', 'link', false, new url('/grade/report/overview/index.php'), get_string('grades'),
            \theme_adaptable\toolbox::getfontawesomemarkup('list-alt', ['mr-1']), ];
        $usermenuitems[] = ['enablebadges', 'link', false, new url('/badges/mybadges.php'), get_string('badges'),
            \theme_adaptable\toolbox::getfontawesomemarkup('certificate', ['mr-1']), ];
        $usermenuitems[] = ['enablepref', 'link', '2015051100', new url('/user/preferences.php'), get_string('preferences'),
            \theme_adaptable\toolbox::getfontawesomemarkup('cog', ['mr-1']), ];
        $usermenuitems[] = ['enablenote', 'link', false, new url('/message/edit.php'), get_string('notifications'),
            \theme_adaptable\toolbox::getfontawesomemarkup('paper-plane', ['mr-1']), ];
        $usermenuitems[] = [false, 'divider'];
        $usermenuitems[] = ['enableblog', 'link', false, new url('/blog/index.php'), get_string('enableblog', 'theme_adaptable'),
            \theme_adaptable\toolbox::getfontawesomemarkup('rss', ['mr-1']), ];
        $usermenuitems[] = ['enableposts', 'link', false, new url('/mod/forum/user.php'),
            get_string('enableposts', 'theme_adaptable'), \theme_adaptable\toolbox::getfontawesomemarkup('commenting', ['mr-1']), ];
        $usermenuitems[] = ['enablefeed', 'link', false, new url('/report/myfeedback/index.php'),
            get_string('enablefeed', 'theme_adaptable'), \theme_adaptable\toolbox::getfontawesomemarkup('bullhorn', ['mr-1']), ];
        $usermenuitems[] = ['enablecalendar', 'link', false, new url('/calendar/view.php'),
            get_string('pluginname', 'block_calendar_month'),
            \theme_adaptable\toolbox::getfontawesomemarkup('calendar', ['mr-1']), ];

        // Custom user menu items postion.
        $usermenuitems[] = [false, 'user'];

        // Return.
        if (is_role_switched($COURSE->id)) {
            $returnurl = $this->page->url->out_as_local_url(false);
            $url = new url('/course/switchrole.php', ['id' => $COURSE->id, 'sesskey' => sesskey(),
            'switchrole' => '0', 'returnurl' => $returnurl]);
            $usermenuitems[] = [false, 'link', false, $url, get_string('switchrolereturn'),
                \theme_adaptable\toolbox::getfontawesomemarkup('user-o', ['mr-1']), ];
        } else {
            $context = context_course::instance($COURSE->id);
            if (has_capability('moodle/role:switchroles', $context)) {
                $returnurl = $this->page->url->out_as_local_url(false);
                $url = new url('/course/switchrole.php', ['id' => $COURSE->id, 'switchrole' => '-1', 'returnurl' => $returnurl]);
                $usermenuitems[] = [false, 'link', false, $url, get_string('switchroleto'),
                    \theme_adaptable\toolbox::getfontawesomemarkup('user-o', ['mr-1']), ];
            }
        }

        $usermenuitems[] = [false, 'link', false, new url('/login/logout.php', ['sesskey' => sesskey()]), get_string('logout'),
            \theme_adaptable\toolbox::getfontawesomemarkup('sign-out', ['mr-1']), ];

        foreach ($usermenuitems as $usermenuitem) {
            switch($usermenuitem[1]) {
                case 'link':
                    $additem = true;

                    // If theme setting is specified in array but not enabled in theme settings do not add to menu.
                    if (!empty($usermenuitem[0])) {
                        $usermenuitemname = $usermenuitem[0];
                        if (empty($this->page->theme->settings->$usermenuitemname)) {
                            $additem = false;
                        }
                    }

                    // If item requires version number and moodle is below that version to not add to menu.
                    if ($usermenuitem[2] && $CFG->version < $usermenuitem[2]) {
                        $additem = false;
                    }

                    if ($additem) {
                        $item = new stdClass;
                        $item->itemtype = 'link';
                        $item->url = $usermenuitem[3];
                        $item->title = $usermenuitem[5] . $usermenuitem[4];
                        $retval[] = $item;
                    }
                break;
                case 'divider':
                    $item = new stdClass;
                    $item->itemtype = 'divider';
                    $retval[] = $item;
                break;
                case 'user':
                    $customitems = $this->user_convert_text_to_menu_items($CFG->customusermenuitems);
                    if ($customitems[0]) {
                        $divider = new stdClass();
                        $divider->itemtype = 'divider';
                        $retval[] = $divider;
                        foreach ($customitems[1] as $item) {
                            $retval[] = $item;
                        }
                        $retval[] = $divider;
                    }
                break;
            }
        }
        return $retval;
    }

    /**
     * Converts a string into a flat array of menu items, where each menu items is a
     * stdClass with fields type, url, title.
     *
     * @param string $text the menu items definition
     * @return array [hasitems - bool, items - array].
     */
    protected function user_convert_text_to_menu_items($text) {
        $hasitems = false;
        $lines = explode("\n", $text);
        $children = [];
        foreach ($lines as $line) {
            $line = trim($line);
            $bits = explode('|', $line, 3);
            $itemtype = 'link';
            if (preg_match("/^#+$/", $line)) {
                $itemtype = 'divider';
            } else if (!array_key_exists(0, $bits) || empty($bits[0])) {
                // Every item must have a name to be valid.
                continue;
            } else {
                $bits[0] = ltrim($bits[0], '-');
            }

            // Create the child.
            $child = new stdClass();
            $child->itemtype = $itemtype;
            if ($itemtype === 'divider') {
                // Add the divider to the list of children and skip link processing.
                $children[] = $child;
                continue;
            }

            // Name processing.
            $namebits = explode(',', $bits[0], 2);
            if (count($namebits) == 2) {
                $namebits[1] = $namebits[1] ?: 'core';
                // Check the validity of the identifier part of the string.
                if (clean_param($namebits[0], PARAM_STRINGID) !== '' && clean_param($namebits[1], PARAM_COMPONENT) !== '') {
                    // Treat this as a language string.
                    $child->title = get_string($namebits[0], $namebits[1]);
                    $child->titleidentifier = implode(',', $namebits);
                }
            }
            if (empty($child->title)) {
                // Use it as is, don't even clean it.
                $child->title = $bits[0];
                $child->titleidentifier = str_replace(" ", "-", $bits[0]);
            }

            // URL processing.
            if (!array_key_exists(1, $bits) || empty($bits[1])) {
                // Unlike core, if invaild then skip.
                unset($child);
                continue;
            } else {
                // Nasty hack to replace the grades with the direct url.
                if (strpos($bits[1], '/grade/report/mygrades.php') !== false) {
                    $bits[1] = user_mygrades_url();
                }

                // Make sure the url is a moodle url.
                $bits[1] = new url(trim($bits[1]));
            }
            $child->url = $bits[1];

            // Font Awesome processing.
            if (array_key_exists(2, $bits)) {
                $fa = trim($bits[2]);
                $child->title = \theme_adaptable\toolbox::getfontawesomemarkup($fa, ['mr-1']) . $child->title;
            }

            // Add this child to the list of children.
            $children[] = $child;
            $hasitems = true;
        }
        return [$hasitems, $children];
    }

    /**
     * Construct a user menu, returning HTML that can be echoed out by a
     * layout file.
     *
     * @param stdClass $user A user object, usually $USER.
     * @param bool $withlinks true if a dropdown should be built.
     * @return string HTML fragment.
     */
    public function user_menu($user = null, $withlinks = null) {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');

        if (is_null($user)) {
            $user = $USER;
        }

        // Note: This behaviour is intended to match that of core_renderer::login_info,
        // but should not be considered to be good practice; layout options are
        // intended to be theme-specific. Please don't copy this snippet anywhere else.
        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        // Add a class for when $withlinks is false.
        $usermenuclasses = 'usermenu';
        if (!$withlinks) {
            $usermenuclasses .= ' withoutlinks';
        }

        $returnstr = "";

        // If during initial install, return the empty return string.
        if (during_initial_install()) {
            return $returnstr;
        }

        // Adaptable modified.
        $themesettings = \theme_adaptable\toolbox::get_settings();

        $avatarclasses = "avatars";
        $userpic = $this->user_picture($user, ['link' => false, 'visibletoscreenreaders' => false,
            'size' => 35, 'class' => 'userpicture', ]);
        $avatarcontents = html_writer::span($userpic, 'avatar current');
        $usertextcontents = format_string(fullname($user));

        // User menu dropdown.
        if (!empty($themesettings->usernameposition)) {
            $usernameposition = $themesettings->usernameposition;
            if ($usernameposition == 'right') {
                $usernamepositionleft = false;
            } else {
                $usernamepositionleft = true;
            }
        } else {
            $usernamepositionleft = true;
        }

        if ($usernamepositionleft) {
            $returnstr .= html_writer::span(
                html_writer::span($usertextcontents, 'usertext mr-1') .
                html_writer::span($avatarcontents, $avatarclasses),
                'userbutton'
            );
        } else {
            $returnstr .= html_writer::span(
                html_writer::span($avatarcontents, $avatarclasses) .
                html_writer::span($usertextcontents, 'usertext mr-1'),
                'userbutton'
            );
        }

        $navitems = $this->user_profile_menu_items();

        // Create a divider (well, a filler).
        $divider = new \action_menu_filler();
        $divider->primary = false;

        $am = new \action_menu();
        $am->set_menu_trigger(
            $returnstr,
            'nav-link'
        );
        $am->set_action_label(get_string('usermenu'));
        $am->set_nowrap_on_items();
        if ($withlinks) {
            $navitemcount = count($navitems);
            $idx = 0;
            foreach ($navitems as $key => $value) {

                switch ($value->itemtype) {
                    case 'divider':
                        // If the nav item is a divider, add one and skip link processing.
                        $am->add($divider);
                        break;

                    case 'invalid':
                        // Silently skip invalid entries (should we post a notification?).
                        break;

                    case 'link':
                        // Process this as a link item.
                        $pix = null;
                        if (isset($value->pix) && !empty($value->pix)) {
                            $pix = new pix_icon($value->pix, '', null, ['class' => 'iconsmall']);
                        } else if (isset($value->imgsrc) && !empty($value->imgsrc)) {
                            $value->title = html_writer::img(
                                $value->imgsrc,
                                $value->title,
                                ['class' => 'iconsmall']
                            ) . $value->title;
                        }

                        $al = new \action_menu_link_secondary(
                            $value->url,
                            $pix,
                            $value->title,
                            ['class' => 'icon']
                        );
                        if (!empty($value->titleidentifier)) {
                            $al->attributes['data-title'] = $value->titleidentifier;
                        }
                        $am->add($al);
                        break;
                }

                $idx++;

                // Add dividers after the first item and before the last item.
                if ($idx == 1 || $idx == $navitemcount - 1) {
                    $am->add($divider);
                }
            }
        }

        return html_writer::div(
            $this->render($am),
            $usermenuclasses
        );
    }

    /**
     * Prints a nice side block with an optional header.
     *
     * The content is described by a core_renderer::block_contents object.
     *
     * <div id="inst{$instanceid}" class="block_{$blockname} block">
     *      <div class="header"></div>
     *      <div class="content">
     *          ...CONTENT...
     *          <div class="footer">
     *          </div>
     *      </div>
     *      <div class="annotation">
     *      </div>
     * </div>
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    public function block(block_contents $bc, $region) {
        $bc = clone($bc); // Avoid messing up the object passed in.
        $skiptitle = strip_tags($bc->title);
        if (empty($bc->blockinstanceid) || !$skiptitle) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        } else {
            global $USER;
            $USER->adaptable_user_pref['block' . $bc->blockinstanceid . 'hidden'] = PARAM_BOOL;
        }
        if (!empty($bc->blockinstanceid)) {
            $bc->attributes['data-instanceid'] = $bc->blockinstanceid;
        }
        if ($bc->blockinstanceid && !empty($skiptitle)) {
            $bc->attributes['aria-labelledby'] = 'instance-' . $bc->blockinstanceid . '-header';
        } else if (!empty($bc->arialabel)) {
            $bc->attributes['aria-label'] = $bc->arialabel;
        }
        if ($bc->dockable) {
            $bc->attributes['data-dockable'] = 1;
        }
        if ($bc->collapsible == block_contents::HIDDEN) {
            $bc->add_class('hidden');
        }
        if (!empty($bc->controls)) {
            $bc->add_class('block_with_controls');
        }
        $bc->add_class('mb-3');

        if (empty($skiptitle)) {
            $skiptitle = get_string('skipblock', 'theme_adaptable', $bc->blockinstanceid);
        }
        $output = html_writer::link(
            '#sb-' . $bc->skipid,
            get_string('skipa', 'access', $skiptitle),
            ['class' => 'skip skip-block', 'id' => 'fsb-' . $bc->skipid]
        );
        $skipdest = html_writer::span(
            '',
            'skip-block-to',
            ['id' => 'sb-' . $bc->skipid]
        );

        if (!empty($bc->attributes['notitle'])) {
            $bc->title = '';
        }

        $output .= html_writer::start_tag('section', $bc->attributes);

        $output .= $this->block_header($bc);
        $output .= $this->block_content($bc);

        $output .= html_writer::end_tag('section');

        $output .= $this->block_annotation($bc);

        $output .= $skipdest;

        return $output;
    }

    /**
     * Produces a header for a block
     *
     * @param block_contents $bc
     * @return string
     */
    protected function block_header(block_contents $bc) {

        $title = '';
        if ($bc->title) {
            $attributes = [];
            $attributes['class'] = 'd-inline';
            if ($bc->blockinstanceid) {
                $attributes['id'] = 'instance-' . $bc->blockinstanceid . '-header';
            }
            $title = html_writer::tag('h2', $bc->title, $attributes);
        }

        $blockid = null;
        if (isset($bc->attributes['id'])) {
            $blockid = $bc->attributes['id'];
        }
        $controlshtml = $this->block_controls($bc->controls, $blockid);

        $output = '';
        if ($title || $controlshtml) {

            $collapse = '';
            if (isset($bc->attributes['id']) && $bc->collapsible != block_contents::NOT_HIDEABLE) {
                $collapse =
                    html_writer::tag('div', '', [
                        'id' => 'instance-'.$bc->blockinstanceid.'-action',
                        'class' => 'block-action block-collapsible',
                        'data-instanceid' => $bc->blockinstanceid,
                        'title' => get_string('blockshowhide', 'theme_adaptable'),
                    ]);
                $this->page->requires->js_call_amd(
                    'theme_adaptable/collapseblock',
                    'collapseBlockInit'
                );
            }

            $output .=
                html_writer::tag(
                    'div',
                    $collapse . html_writer::tag(
                        'div',
                        html_writer::tag('div', '', ['class' => 'block_action']) . $title,
                        ['class' => 'title']
                    ). html_writer::tag('div', $controlshtml, ['class' => 'block-controls']),
                    ['class' => 'header']
                );
        }
        return $output;
    }

    /**
     * Produces the content area for a block
     *
     * @param block_contents $bc
     * @return string
     */
    protected function block_content(block_contents $bc) {
        $output = html_writer::start_tag('div', ['class' => 'content']);
        if (!$bc->title && !$this->block_controls($bc->controls)) {
            $output .= html_writer::tag('div', '', ['class' => 'block_action notitle']);
        }
        $output .= $bc->content;
        $output .= $this->block_footer($bc);
        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * Produces the footer for a block
     *
     * @param block_contents $bc
     * @return string
     */
    protected function block_footer(block_contents $bc) {
        $output = '';
        if ($bc->footer) {
            $output .= html_writer::tag('div', $bc->footer, ['class' => 'footer']);
        }
        return $output;
    }

    /**
     * Produces the annotation for a block
     *
     * @param block_contents $bc
     * @return string
     */
    protected function block_annotation(block_contents $bc) {
        $output = '';
        if ($bc->annotation) {
            $output .= html_writer::tag('div', $bc->annotation, ['class' => 'blockannotation']);
        }
        return $output;
    }

    /**
     * Returns standard navigation between activities in a course.
     *
     * @return string the navigation HTML.
     */
    public function activity_navigation() {
        // First we should check if we want to add navigation.
        if (!$this->page->theme->settings->courseactivitynavigationenabled) {
            return '';
        }

        $context = $this->page->context;
        if (
            ($this->page->pagelayout !== 'incourse' && $this->page->pagelayout !== 'frametop')
            || $context->contextlevel != CONTEXT_MODULE
        ) {
            return '';
        }

        // If the activity is in stealth mode, show no links.
        if ($this->page->cm->is_stealth()) {
            return '';
        }

        // Get a list of all the activities in the course.
        $course = $this->page->cm->get_course();
        $modules = get_fast_modinfo($course->id)->get_cms();

        // Put the modules into an array in order by the position they are shown in the course.
        $mods = [];
        $activitylist = [];
        foreach ($modules as $module) {
            // Only add activities the user can access, aren't in stealth mode and have a url (eg. mod_label does not).
            if (!$module->uservisible || $module->is_stealth() || empty($module->url)) {
                continue;
            }
            $mods[$module->id] = $module;

            // No need to add the current module to the list for the activity dropdown menu.
            if ($module->id == $this->page->cm->id) {
                continue;
            }
            // Module name.
            $modname = $module->get_formatted_name();
            // Display the hidden text if necessary.
            if (!$module->visible) {
                $modname .= ' ' . get_string('hiddenwithbrackets');
            }
            // Module URL.
            $linkurl = new url($module->url, ['forceview' => 1]);
            // Add module URL (as key) and name (as value) to the activity list array.
            $activitylist[$linkurl->out(false)] = $modname;
        }

        $nummods = count($mods);

        // If there is only one mod then do nothing.
        if ($nummods == 1) {
            return '';
        }

        // Get an array of just the course module ids used to get the cmid value based on their position in the course.
        $modids = array_keys($mods);

        // Get the position in the array of the course module we are viewing.
        $position = array_search($this->page->cm->id, $modids);

        $prevmod = null;
        $nextmod = null;

        // Check if we have a previous mod to show.
        if ($position > 0) {
            $prevmod = $mods[$modids[$position - 1]];
        }

        // Check if we have a next mod to show.
        if ($position < ($nummods - 1)) {
            $nextmod = $mods[$modids[$position + 1]];
        }

        $activitynav = new \core_course\output\activity_navigation($prevmod, $nextmod, $activitylist);
        $renderer = $this->page->get_renderer('core', 'course');
        return $renderer->render($activitynav);
    }

    /**
     * Renders preferences groups.
     *
     * @param  preferences_groups $renderable The renderable
     * @return string The output.
     */
    public function render_preferences_groups(\core\output\preferences_groups $renderable) {
        return $this->render_from_template('core/preferences_groups', $renderable);
    }

    /**
     * Returns list of alert messages for the user.
     *
     * @return string Markup if any.
     */
    public function get_alert_messages() {
        $markup = '';
        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();

        if (is_object($localtoolbox)) {
            $themesettings = \theme_adaptable\toolbox::get_settings();
            $markup = $localtoolbox->get_alert_messages($themesettings, $this->page, $this);
        }

        return $markup;
    }

    /**
     * Displays notices to alert teachers of problems with course such as being hidden.
     *
     * @return string Markup if any.
     */
    public function get_course_alerts() {
        $markup = '';
        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();

        if (is_object($localtoolbox)) {
            $themesettings = \theme_adaptable\toolbox::get_settings();
            $markup = $localtoolbox->get_course_alerts($themesettings, $this->page, $this);
        }

        return $markup;
    }

    /**
     * Returns all tracking methods.
     *
     * @return string Markup.
     */
    public function get_all_tracking_methods() {
        $markup = '';
        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();

        if (is_object($localtoolbox)) {
            $themesettings = \theme_adaptable\toolbox::get_settings();
            $markup = $localtoolbox->get_all_tracking_methods($themesettings, $this->page, $this);
        }

        return $markup;
    }

    /**
     * Returns HTML to display a "Turn editing on/off" button in a form.
     *
     * Note: Not called directly by theme but by core in its way of setting the 'page button'
     *       attribute.  This version needed for 'Edit button keep position' in adaptable.js.
     *
     * @param url $url The URL + params to send through when clicking the button.
     * @param string $method Not used.
     * @return string HTML the button
     */
    public function edit_button(url $url, string $method = 'post') {
        $url->param('sesskey', sesskey());
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $btn = 'btn-danger';
            $title = get_string('turneditingoff');
            $icon = 'fa-power-off';
        } else {
            $url->param('edit', 'on');
            $btn = 'btn-success';
            $title = get_string('turneditingon');
            $icon = 'fa-edit';
        }
        $editingtext = \theme_adaptable\toolbox::get_setting('displayeditingbuttontext');
        $buttontitle = '';
        if ($editingtext) {
            $buttontitle = $title;
        } else {
            $icon .= ' only';
        }
        return html_writer::tag('a', html_writer::tag('i', '', ['class' => $icon . ' fa fa-fw']) .
            $buttontitle, ['href' => $url, 'class' => 'btn ' . $btn, 'title' => $title]);
    }

    /**
     * Process user messages
     *
     * @param array $message
     * @return array
     */
    protected function process_message($message) {
        global $DB, $USER;

        $messagecontent = new stdClass();
        if ($message->notification || $message->useridfrom < 1) {
            $messagecontent->text = $message->smallmessage;
            $messagecontent->type = 'notification';

            if (empty($message->contexturl)) {
                $messagecontent->url = new url(
                    '/message/index.php',
                    ['user1' => $USER->id, 'viewing' => 'recentnotifications']
                );
            } else {
                $messagecontent->url = new url($message->contexturl);
            }
        } else {
            $messagecontent->type = 'message';
            if ($message->fullmessageformat == FORMAT_HTML) {
                $message->smallmessage = html_to_text($message->smallmessage);
            }
            if (strlen($message->smallmessage) > 18) {
                $messagecontent->text = \core_text::substr($message->smallmessage, 0, 15) . '...';
            } else {
                $messagecontent->text = $message->smallmessage;
            }
            $messagecontent->from = $DB->get_record('user', ['id' => $message->useridfrom]);
            $messagecontent->url = new url(
                '/message/index.php',
                ['user1' => $USER->id, 'user2' => $message->useridfrom]
            );
        }
        $messagecontent->date = userdate($message->timecreated, get_string('strftimetime', 'langconfig'));
        $messagecontent->unread = empty($message->timeread);
        return $messagecontent;
    }

    /**
     * Returns html to render socialicons
     *
     * @return string
     */
    public function socialicons() {
        $socialiconlist = \theme_adaptable\toolbox::get_setting('socialiconlist');
        if (empty($socialiconlist)) {
            return '';
        }

        $target = \theme_adaptable\toolbox::get_setting('socialtarget', false, null, '_blank');

        $retval = '';
        $lines = explode("\n", $socialiconlist);

        foreach ($lines as $line) {
            if (strstr($line, '|')) {
                $fields = explode('|', $line);
                $retval .= '<a target="' . $target . '" title="' . $fields[1] . '" href="' . $fields[0] . '">';
                $retval .= \theme_adaptable\toolbox::getfontawesomemarkup($fields[2]);
                $retval .= '</a>';
            }
        }

        return $retval;
    }

    /**
     * Returns html to render news ticker.
     * Note: Requires local_adaptable plugin.
     *
     * @return string
     */
    public function get_news_ticker() {
        $retval = '';
        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();

        if (is_object($localtoolbox)) {
            $themesettings = \theme_adaptable\toolbox::get_settings();
            $retval = $localtoolbox->get_news_ticker($themesettings, $this->page, $this);
        }

        return $retval;
    }


    /**
     * Renders block regions on front page (or any other page
     * if specifying a different value for $settingsname). Used for various block region rendering.
     *
     * @param   string $settingsname  Setting name to retrieve from theme settings containing actual layout (e.g. 4-4-4-4)
     * @param   string $classnamebeginswith  Used when building the blockname to retrieve for display
     * @param   string $customrowsetting  If $settingsname value set to 'customrowsetting', then set this to
     *                 the layout required to display a one row layout.
     *                 When using this, ensure the appropriate number of block regions are defined in config.php.
     *                 E.g. if $classnamebeginswith = 'my-block' and $customrowsetting = '4-4-0-0', 2 regions called
     *                 'my-block-a' and 'my-block-a' are expected to exist.
     * @return  string HTML output
     */
    public function get_block_regions(
        $settingsname,
        $classnamebeginswith,
        $customrowsetting = null
    ) {
        $blockcount = 0;
        $fields = [];
        $retval = '';

        if ($settingsname == 'customrowsetting') {
            $fields[] = $customrowsetting;
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $marketrow = $settingsname . $i;

                /* Need to check if the setting exists as this function is now
                   called for variable row numbers in block regions (e.g. course page
                   which is a single row of block regions). */

                if (isset($this->page->theme->settings->$marketrow)) {
                    $marketrow = $this->page->theme->settings->$marketrow;
                } else {
                    $marketrow = '0-0-0-0';
                }

                if ($marketrow != '0-0-0-0') {
                    $fields[] = $marketrow;
                }
            }
        }

        foreach ($fields as $field) {
            $vals = explode('-', $field);
            foreach ($vals as $val) {
                if ($val > 0) {
                    $retval .= '<div class="my-1 col-md-' . $val . '">';

                    // Moodle does not seem to like numbers in region names so using letter instead.
                    $blockcount++;
                    $block = $classnamebeginswith . chr(96 + $blockcount);

                    $retval .= $this->blocks($block, 'block-region-front');
                    $retval .= '</div>';
                }
            }
        }
        return $retval;
    }

    /**
     * Renders block regions for potentially hidden blocks.  For example, 4-4-4-4 to 6-6-0-0
     * would mean the last two blocks get inadvertently hidden. This function can recover and
     * display those blocks.  An override option also available to display blocks for the region, regardless.
     *
     * @param array  $blocksarray Settings names containing the actual layout(s) (i.e. 4-4-4-4)
     * @param array  $classes Used when building the blockname to retrieve for display
     * @param bool   $displayall An override setting to simply display all blocks from the region
     * @return string HTML output
     */
    public function get_missing_block_regions($blocksarray, $classes = [], $displayall = false) {
        $retval = '';
        $editing = $this->page->user_is_editing();

        if (!empty($blocksarray)) {
            $classes = (array)$classes;
            $missingblocks = '';

            foreach ($blocksarray as $block) {
                /* Do this for up to 8 rows (allows for expansion.  Be careful
                   of losing blocks if this value changes from a high to low number!). */
                for ($i = 1; $i <= 8; $i++) {
                    /* For each block region in a row, analyse the current layout (e.g. 6-6-0-0, 3-3-3-3).  Check if less than
                       4 blocks (meaning a change in settings from say 4-4-4-4 to 6-6.  Meaning missing blocks,
                       i.e. 6-6-0-0 means the two end ones may have content that is inadvertantly lost. */
                    $rowsetting = $block['settingsname'] . $i;

                    if (isset($this->page->theme->settings->$rowsetting)) {
                        $rowvalue = $this->page->theme->settings->$rowsetting;

                        $spannumbers = explode('-', $rowvalue);
                        $y = 0;
                        foreach ($spannumbers as $spannumber) {
                            $y++;

                            /* Here's the crucial bit.  Check if span number is 0,
                               or $displayall is true (override) and if so, print it out. */
                            if ($spannumber == 0 || $displayall) {
                                $blockregion = $block['classnamebeginswith'] . chr(96 + $y);
                                $displayregion = $this->page->apply_theme_region_manipulations($blockregion);

                                // Check if the block actually has content to display before displaying.
                                if ($this->page->blocks->region_has_content($displayregion, $this)) {
                                    if ($editing) {
                                        $missingblocks .= get_string(
                                            'orphanedblock',
                                            'theme_adaptable',
                                            get_string('region-' . $blockregion, 'theme_adaptable')
                                        );
                                    }
                                    $missingblocks .= $this->blocks($blockregion, 'block');
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($missingblocks)) {
                $retval .= '<aside class="' . join(' ', $classes) . '">';
                $retval .= $missingblocks;
                $retval .= '</aside>';
            }
        }

        return $retval;
    }

    /**
     * Get the HTML for block title in the given region.
     *
     * @param string $region The region to get HTML for.
     * @param boolean $editing We are editing.
     *
     * @return string HTML.
     */
    protected function block_region_title($region, $editing = true) {
        $title = '';

        $shown = (($editing && \theme_adaptable\toolbox::get_setting('blockregioneditingtitleshown')) || (!$editing));
        if ($shown) {
            $title = html_writer::tag(
                'p',
                get_string('region-' . $region, 'theme_adaptable'),
                ['class' => 'block-region-title col-12 text-center font-italic font-weight-bold']
            );
        }

        return $title;
    }

    /**
     * Renders flexible blocks on front page.
     *
     * @param string $region
     * @param string $layoutrow
     * @param string $settingname
     * @param array $classes
     * @param string $tag
     * @return string Markup.
     */
    public function get_flexible_blocks(
        $region,
        $layoutrow = 'informationblockslayoutrow',
        $settingname = 'information',
        $classes = [],
        $tag = 'aside') {
        $editing = $this->page->user_is_editing();
        $themesettings = \theme_adaptable\toolbox::get_settings();

        if (!$editing) {
            $visiblestate = 3;
            if (!empty($themesettings->informationblocksvisible)) {
                $visiblestate = $themesettings->informationblocksvisible;
            }
            if ($visiblestate != 3) {
                $loggedin = isloggedin();
                if ((($visiblestate == 1) && ($loggedin)) || (($visiblestate == 2) && (!$loggedin))) {
                    return '';
                }
            }
        }

        $blockcontent = '';
        $classes = (array)$classes;
        $classes[] = 'block-region';

        if ($editing) {
            $classes[] = 'editing-flexible-blocks';
            $classes[] = 'pl-2';
            $classes[] = 'pr-2';
            $classes[] = 'pt-2';
        }

        $attributes = [
            'id' => 'block-region-' . $region,
            'class' => join(' ', $classes),
            'data-blockregion' => $region,
            'data-droptarget' => '1',
        ];

        if ($this->page->blocks->region_has_content($region, $this)) {
            $blockcontent .= html_writer::tag('h2', get_string('blocks'), ['class' => 'sr-only']);

            $blockcontents = $this->page->blocks->get_content_for_region($region, $this);
            $lastblock = null;
            $zones = [];
            foreach ($blockcontents as $bc) {
                if ($bc instanceof block_contents) {
                    $zones[] = $bc->title;
                }
            }

            if (!$editing) {
                $blockrows = [];
                $blocksequence = [];
                $blocksequencecount = 0;
                $blockspacescount = 0;

                $blockcontent .= '<div class="flexibleblocks container">';

                for ($i = 1; $i <= 5; $i++) {
                    $blockrowsetting = $layoutrow . $i;
                    $blockrowvalue = $themesettings->$blockrowsetting;
                    if ($blockrowvalue != '0-0-0-0') {
                        $blockrows[] = $blockrowvalue;
                    }
                }

                foreach ($blockrows as $blockrow) {
                    $blocksequence[] = '+'; // Row start.
                    $vals = explode('-', $blockrow);
                    foreach ($vals as $val) {
                        if ($val > 0) {
                            $blocksequence[] = $val; // Block width.
                            $blockspacescount++;
                        }
                    }
                    $blocksequence[] = '-'; // Row end.
                }
            }

            $blockspacesexceeded = false;
            $blockcount = 0;
            foreach ($blockcontents as $bc) {
                if ($bc instanceof block_contents) {
                    if (!$editing) {
                        if (!empty($blocksequence[$blocksequencecount])) {
                            if ($blocksequence[$blocksequencecount] == '+') {
                                $blockcontent .= '<div class="row flexiblerow">';
                                $blocksequencecount++;
                            }
                            $bc->attributes['class'] .= ' col-12 col-sm-'.$blocksequence[$blocksequencecount]; // Will be a number.
                        } else {
                            if ((!$blockspacesexceeded) && ($blockcount >= $blockspacescount)) {
                                $blockspacesexceeded = true;
                                html_writer::tag(
                                    'p',
                                    get_string('flexibleblocksoverflow', 'theme_adaptable'),
                                    ['class' => 'block-region-overflow col-12 text-center font-italic font-weight-bold']
                                );
                                $blockcontent .= '<div class="flexible-blocks-overflow">';
                                if (is_siteadmin()) {
                                    $blockcontent .= html_writer::tag(
                                        'p',
                                        get_string('flexibleblocksoverflow', 'theme_adaptable'),
                                        ['class' => 'block-region-overflow col-12 text-center font-italic font-weight-bold']
                                    );
                                }
                            }
                            $bc->attributes['class'] .= ' col-12 col-sm-4';
                        }
                        $bc->attributes['notitle'] = true;
                    }
                    $blockcontent .= $this->block($bc, $region);
                    $lastblock = $bc->title;
                    $blockcount++;
                    if ((!$editing) && (!$blockspacesexceeded)) {
                        $blocksequencecount++;
                        // Could be a end of row next.
                        if ($blocksequence[$blocksequencecount] == '-') {
                            $blockcontent .= '</div>';
                            $blocksequencecount++;
                        }
                    }
                } else if ($bc instanceof block_move_target) {
                    $blockcontent .= $this->block_move_target($bc, $zones, $lastblock, $region);
                } else {
                    throw new coding_exception(
                        'Unexpected type of thing (' . get_class($bc) . ') found in list of block contents.');
                }
            }

            if (!$editing) {
                if ($blockspacesexceeded) {
                    $blockcontent .= '</div>'; // End of flexible-blocks-overflow.
                }
                $blockcontent .= '</div>'; // End of container.
            }
        } else {
            $blockcontent .= html_writer::tag('h2', get_string('blocks'), ['class' => 'sr-only']);
        }
        $blockcontent = html_writer::tag($tag, $blockcontent, $attributes);

        $content = '';
        if ($editing) {
            $title = $this->block_region_title($region);
            // Add block button in editing mode.
            $addblockbutton = $this->addblockbutton($region);

            $content = html_writer::tag('div', $title.$blockcontent.$addblockbutton, ['class' => 'my-1 adaptable-block-area']);
        } else {
            $content = $blockcontent;
        }

        return $content;
    }

    /**
     * Renders marketing blocks on front page.
     *
     * @param string $layoutrow
     * @param string $settingname
     * @return string Markup.
     */
    public function get_marketing_blocks($layoutrow = 'marketlayoutrow', $settingname = 'market') {
        $themesettings = \theme_adaptable\toolbox::get_settings();
        $visiblestate = 3;
        if (!empty($themesettings->marketingvisible)) {
            $visiblestate = $themesettings->marketingvisible;
        }
        if ($visiblestate != 3) {
            $loggedin = isloggedin();
            if ((($visiblestate == 1) && ($loggedin)) || (($visiblestate == 2) && (!$loggedin))) {
                return '';
            }
        }

        $fields = [];
        $blockcount = 0;

        $extramarketclass = $themesettings->frontpagemarketoption;

        $retval = '<div id="marketblocks" class="container ' . $extramarketclass . '">';

        if (is_siteadmin()) {
            $retval .= html_writer::tag(
                'p',
                get_string('marketingdeprecated', 'theme_adaptable'),
                ['class' => 'marketing-deprecated col-12 text-center font-italic font-weight-bold']
            );
        }

        for ($i = 1; $i <= 5; $i++) {
            $marketrow = $layoutrow . $i;
            $marketrow = $themesettings->$marketrow;
            if ($marketrow != '0-0-0-0') {
                $fields[] = $marketrow;
            }
        }

        foreach ($fields as $field) {
            $retval .= '<div class="row marketrow">';
            $vals = explode('-', $field);
            foreach ($vals as $val) {
                if ($val > 0) {
                    $retval .= '<div class="my-1 col-md-' . $val . ' ' . $extramarketclass . '">';
                    $blockcount++;
                    $fieldname = $settingname . $blockcount;
                    if (isset($themesettings->$fieldname)) {
                        // Add HTML format.
                        $retval .= \theme_adaptable\toolbox::get_setting($fieldname, 'format_moodle');
                    }
                    $retval .= '</div>';
                }
            }
            $retval .= '</div>';
        }
        $retval .= '</div>';
        if ($blockcount == 0) {
            $retval = '';
        }
        return $retval;
    }

    /**
     * Returns footer visibility setting
     *
     * @return boolean Visibility.
     */
    public function get_footer_visibility() {
        global $COURSE;
        $value = $this->page->theme->settings->footerblocksplacement;

        if ($value == 1) {
            return true;
        }

        if ($value == 2 && $COURSE->id != 1) {
            return false;
        }

        if ($value == 3) {
            return false;
        }
        return true;
    }

    /**
     * Renders footer blocks.
     *
     * @param string $layoutrow The footer row.
     * @return string HTML output.
     */
    public function get_footer_blocks($layoutrow = 'footerlayoutrow') {
        $fields = [];
        $blockcount = 0;

        if (!$this->get_footer_visibility()) {
            return '';
        }

        $output = '';

        for ($i = 1; $i <= 3; $i++) {
            $footerrow = $layoutrow . $i;
            $footerrow = (!empty($this->page->theme->settings->$footerrow)) ? $this->page->theme->settings->$footerrow : '3-3-3-3';
            if ($footerrow != '0-0-0-0') {
                $fields[] = $footerrow;
            }
        }

        foreach ($fields as $field) {
            $output .= '<div class="row">';
            $vals = explode('-', $field);
            foreach ($vals as $val) {
                if ($val > 0) {
                    $blockcount++;
                    $footerheader = 'footer' . $blockcount . 'header';
                    $footercontent = 'footer' . $blockcount . 'content';
                    if (!empty($this->page->theme->settings->$footercontent)) {
                        $output .= '<div class="left-col col-' . $val . '">';
                        if (!empty($this->page->theme->settings->$footerheader)) {
                            $output .= '<h3>';
                            $output .= \theme_adaptable\toolbox::get_setting($footerheader, 'format_html');
                            $output .= '</h3>';
                        }
                        $output .= \theme_adaptable\toolbox::get_setting($footercontent, 'format_html');
                        $output .= '</div>';
                    }
                }
            }
            $output .= '</div>';
        }
        if (!empty($output)) {
            $output = '<div class="container blockplace1">' . $output . '</div>';
        }

        return $output;
    }

    /**
     * Renders frontpage slider.
     * @return string HTML output if any.
     */
    public function get_frontpage_slider() {
        if (empty($this->page->theme->settings->sliderenabled)) {
            return '';
        }

        $this->page->requires->js_call_amd('theme_adaptable/slider', 'init');

        $visiblestate = 3;
        if (!empty($this->page->theme->settings->slidervisible)) {
            $visiblestate = $this->page->theme->settings->slidervisible;
        }

        if ($visiblestate != 3) {
            $loggedin = isloggedin();
            if ((($visiblestate == 1) && ($loggedin)) || (($visiblestate == 2) && (!$loggedin))) {
                return '';
            }
        }

        $noslides = $this->page->theme->settings->slidercount;
        $responsiveslider = $this->page->theme->settings->responsiveslider;

        $retval = '';

        // Will we have any slides?
        $haveslides = false;
        for ($i = 1; $i <= $noslides; $i++) {
            $sliderimage = 'p' . $i;
            if (!empty($this->page->theme->settings->$sliderimage)) {
                $haveslides = true;
                break;
            }
        }

        if (!$haveslides) {
            return '';
        }

        if (!empty($this->page->theme->settings->sliderfullscreen)) {
            $retval .= '<div class="slidewrap';
        } else {
            $retval .= '<div class="container slidewrap';
        }

        if ($this->page->theme->settings->slideroption2 == 'slider2') {
            $retval .= " slidestyle2";
        }

        $retval .= ' ' . $responsiveslider . '"><div id="main-slider" class="flexslider"><ul class="slides">';

        for ($i = 1; $i <= $noslides; $i++) {
            $sliderimage = 'p' . $i;
            $sliderurl = 'p' . $i . 'url';

            if (!empty($this->page->theme->settings->$sliderimage)) {
                $slidercaption = 'p' . $i . 'cap';
            }

            $closelink = '';
            if (!empty($this->page->theme->settings->$sliderimage)) {
                $retval .= '<li>';

                if (!empty($this->page->theme->settings->$sliderurl)) {
                    $retval .= '<a href="' . $this->page->theme->settings->$sliderurl . '">';
                    $closelink = '</a>';
                }

                $retval .= '<img src="' . $this->page->theme->setting_file_url($sliderimage, $sliderimage)
                . '" alt="' . $sliderimage . '"/>';

                if (!empty($this->page->theme->settings->$slidercaption)) {
                    $retval .= '<div class="flex-caption">';
                    $retval .= \theme_adaptable\toolbox::get_setting($slidercaption, 'format_html');
                    $retval .= '</div>';
                }
                $retval .= $closelink . '</li>';
            }
        }
        $retval .= '</ul></div></div>';
        return $retval;
    }

    /**
     * Renders the breadcrumb navbar.
     *
     * @return string Markup or empty string if 'nonavbar' for the given page layout in the config.php file is true.
     */
    public function page_navbar() {
        $retval = '';
        if (empty($this->page->layout_options['nonavbar'])) { // Not disabled by 'nonavbar' in config.php.
            if (!isset($this->page->theme->settings->enabletickermy)) {
                $this->page->theme->settings->enabletickermy = 0;
            }

            // Do not show navbar on dashboard / my home if news ticker is rendering.
            if (!($this->page->theme->settings->enabletickermy && $this->page->bodyid == "page-my-index")) {
                $retval = '<div class="row">';
                if (
                    ($this->page->theme->settings->breadcrumbdisplay != 'breadcrumb')
                    && (($this->page->pagelayout == 'course')
                    || ($this->page->pagelayout == 'incourse'))
                ) {
                    global $COURSE;
                    $retval .= '<div id="page-coursetitle" class="col-12">';
                    switch ($this->page->theme->settings->breadcrumbdisplay) {
                        case 'fullname':
                            // Full Course Name.
                            $coursetitle = $COURSE->fullname;
                            break;
                        case 'shortname':
                            // Short Course Name.
                            $coursetitle = $COURSE->shortname;
                            break;
                    }

                    $coursetitlemaxwidth = (!empty($this->page->theme->settings->coursetitlemaxwidth)
                        ? $this->page->theme->settings->coursetitlemaxwidth : 0);
                    // Check max width of course title and trim if appropriate.
                    if (($coursetitlemaxwidth > 0) && ($coursetitle <> '')) {
                        if (strlen($coursetitle) > $coursetitlemaxwidth) {
                            $coursetitle = \core_text::substr($coursetitle, 0, $coursetitlemaxwidth) . " ...";
                        }
                    }

                    switch ($this->page->theme->settings->breadcrumbdisplay) {
                        case 'fullname':
                        case 'shortname':
                            // Full / Short Course Name.
                            $courseurl = new url('/course/view.php', ['id' => $COURSE->id]);
                            $retval .= '<div id="coursetitle" class="p-2 bd-highlight"><h1><a href ="'
                                . $courseurl->out(true) . '">' . format_string($coursetitle) . '</a></h1></div>';
                            break;
                    }
                    $retval .= '</div>';
                } else {
                    if (
                        $this->page->include_region_main_settings_in_header_actions() &&
                        !$this->page->blocks->is_block_present('settings')
                    ) {
                        $this->page->add_header_action(html_writer::div(
                            $this->region_main_settings_menu(),
                            'd-print-none',
                            ['id' => 'region-main-settings-menu']
                        ));
                    }

                    $header = new stdClass();
                    $header->navbar = $this->navbar();
                    $header->headeractions = $this->page->get_header_actions();
                    $header->headerclasses = $this->page->theme->settings->responsivebreadcrumb;
                    $retval .= $this->render_from_template('theme_adaptable/header', $header);
                }
                $retval .= '</div>';
            }
        }

        return $retval;
    }

    /**
     * Render the navbar.
     *
     * @return string Markup.
     */
    public function navbar(): string {
        $items = $this->page->navbar->get_items();
        $breadcrumbseparator = $this->page->theme->settings->breadcrumbseparator;

        $breadcrumbs = "";

        if (empty($items)) {
            return '';
        }

        $start = true;
        foreach ($items as $item) {
            $item->hideicon = true;

            // Text / Icon home.
            if ($start) {
                $breadcrumbs .= '<li>';

                if (\theme_adaptable\toolbox::get_setting('enablehome') && \theme_adaptable\toolbox::get_setting('enablemyhome')) {
                    $breadcrumbs = html_writer::tag('i', '', [
                        'title' => get_string('home', 'theme_adaptable'),
                        'class' => 'fa fa-folder-open fa-lg',
                    ]);
                } else if (\theme_adaptable\toolbox::get_setting('breadcrumbhome') == 'icon') {
                    $breadcrumbs .= html_writer::link(
                        new url('/'),
                        // Adds in a title for accessibility purposes.
                        html_writer::tag('i', '', [
                            'title' => get_string('home', 'theme_adaptable'),
                            'class' => 'fa fa-home fa-lg', ])
                    );
                    $breadcrumbs .= '</li>';
                } else {
                    $breadcrumbs .= html_writer::link(new url('/'), get_string('home', 'theme_adaptable'));
                    $breadcrumbs .= '</li>';
                }
                $start = false;
                continue; // This effectively removes the 'core' Home / Dashboard / User preference for such item.
            }
            $breadcrumbs .= '<span class="separator"><i class="fa-' . $breadcrumbseparator . ' fa"></i></span><li>' .
                $this->render($item) . '</li>';
        }

        $classes = $this->page->theme->settings->responsivebreadcrumb;

        return '<nav role="navigation" aria-label="' . get_string("breadcrumb", "theme_adaptable") .
            '"><ol  class="breadcrumb ' . $classes . ' align-items-center">' . $breadcrumbs . '</ol></nav>';
    }

    /**
     * Renders a navigation node object.
     *
     * @param navigation_node $item The navigation node to render.
     * @return string HTML fragment
     */
    protected function render_navigation_node(navigation_node $item) {
        if ($item->action instanceof action_link) {
            $action = clone($item->action);
            $item = clone($item);
            $item->action = $action;
        }
        return parent::render_navigation_node($item);
    }

    /**
     * Compares two course entries against their access time for a user to see which is first.
     *
     * @param stdClass $a A course.
     * @param stdClass $b A course.
     *
     * @return int -1 'a' is first, 1 'b' is first or 0 they are equal.
     */
    protected static function timeaccesscompare($a, $b) {
        // The timeaccess is lastaccess entry and timestart an enrol entry.
        if ((!empty($a->timeaccess)) && (!empty($b->timeaccess))) {
            // Both last access.
            if ($a->timeaccess == $b->timeaccess) {
                return 0;
            }
            return ($a->timeaccess > $b->timeaccess) ? -1 : 1;
        } else if ((!empty($a->timestart)) && (!empty($b->timestart))) {
            // Both enrol.
            if ($a->timestart == $b->timestart) {
                return 0;
            }
            return ($a->timestart > $b->timestart) ? -1 : 1;
        }

        /* Must be comparing an enrol with a last access.
           -1 is to say that 'a' comes before 'b'. */
        if (!empty($a->timestart)) {
            // If 'a' is the enrol entry.
            return -1;
        }
        // Then 'b' must be the enrol entry.
        return 1;
    }

    /**
     * Returns menu object containing main navigation.
     *
     * @return menu object.
     */
    public function navigation_menu_content() {
        global $CFG, $COURSE;
        $menu = new custom_menu();

        $access = true;
        $overridelist = false;
        $overridetype = 'off';

        if (!empty($this->page->theme->settings->navbardisplayicons)) {
            $navbardisplayicons = true;
        } else {
            $navbardisplayicons = false;
        }

        $mysitesmaxlength = '30';
        if (!empty($this->page->theme->settings->mysitesmaxlength)) {
            $mysitesmaxlength = $this->page->theme->settings->mysitesmaxlength;
        }

        $mysitesmaxlengthhidden = $mysitesmaxlength - 3;

        $branchsort = 9998;

        if (isloggedin() && !isguestuser()) {
            if (!empty($this->page->theme->settings->enablehome)) {
                $branchlabel = '';
                $branchtitle = get_string('home', 'theme_adaptable');
                if ($navbardisplayicons) {
                    $branchlabel .= \theme_adaptable\toolbox::getfontawesomemarkup('home', ['fa-lg', 'mr-1']);
                }
                $branchlabel .= $branchtitle;

                if (!empty($this->page->theme->settings->enablehomeredirect)) {
                    $branchurl = new url('/?redirect=0');
                } else {
                    $branchurl = new url('/');
                }
                $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
            }

            if (!empty($this->page->theme->settings->enablemyhome)) {
                $branchlabel = '';
                $branchtitle = get_string('myhome');
                if ($navbardisplayicons) {
                    $branchlabel .= \theme_adaptable\toolbox::getfontawesomemarkup('dashboard', ['fa-lg', 'mr-1']);
                }
                $branchlabel .= $branchtitle;
                $branchurl = new url('/my/index.php');
                $branchsort++;
                $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
            }

            if (!empty($this->page->theme->settings->enablemycourses)) {
                $branchlabel = '';
                $branchtitle = get_string('courses');
                if ($navbardisplayicons) {
                    $branchlabel .= \theme_adaptable\toolbox::getfontawesomemarkup('th', ['fa-lg', 'mr-1']);
                }
                $branchlabel .= $branchtitle;
                $branchurl = new url('/my/courses.php');
                $branchsort++;
                $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
            }

            if (!empty($this->page->theme->settings->enableevents)) {
                $branchlabel = '';
                $branchtitle = get_string('events', 'theme_adaptable');
                if ($navbardisplayicons) {
                    $branchlabel .= \theme_adaptable\toolbox::getfontawesomemarkup('calendar', ['fa-lg', 'mr-1']);
                }
                $branchlabel .= $branchtitle;

                $branchurl = new url('/calendar/view.php');
                $branchsort++;
                $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
            }

            $overridetype = null;
            $overridelist = null;

            if (!empty($this->page->theme->settings->mysitessortoverride)) {
                $overridetype = $this->page->theme->settings->mysitessortoverride;
            }

            if (!empty($this->page->theme->settings->mysitessortoverridefield)) {
                $overridelist = $this->page->theme->settings->mysitessortoverridefield;
            }

            if (($overridetype == 'profilefields' || $overridetype == 'profilefieldscohort') && (isset($overridelist))) {
                $overridelist = $this->get_profile_field_contents($overridelist);

                if ($overridetype == 'profilefieldscohort') {
                    $overridelist = array_merge($this->get_cohort_enrollments(), $overridelist);
                }
            }

            if ($overridetype == 'strings' && isset($overridelist)) {
                $overridelist = explode(',', $overridelist);
            }

            $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();
            if (is_object($localtoolbox)) {
                $themesettings = \theme_adaptable\toolbox::get_settings();
                $localtoolbox->get_mycourses(
                    $menu,
                    $branchsort,
                    $navbardisplayicons,
                    $overridelist,
                    $overridetype,
                    $mysitesmaxlength,
                    $mysitesmaxlengthhidden,
                    $this->page->theme->settings,
                    $this->page,
                    $this
                );
            }

            if (!empty($this->page->theme->settings->enablethiscourse)) {
                if (isset($COURSE->id) && $COURSE->id != SITEID) {
                    $branchlabel = '';
                    $branchtitle = get_string('thiscourse', 'theme_adaptable');
                    if ($navbardisplayicons) {
                        $branchlabel .=
                            \theme_adaptable\toolbox::getfontawesomemarkup('sitemap', ['mr-1', 'fa-lg']) . '<span class="menutitle">';
                    }
                    $branchlabel .= $branchtitle;
                    if ($navbardisplayicons) {
                        $branchlabel .= '</span>';
                    }

                    // Check the option of displaying a sub-menu arrow symbol.
                    if (!empty($this->page->theme->settings->navbardisplaysubmenuarrow)) {
                        $branchlabel .= \theme_adaptable\toolbox::getfontawesomemarkup('caret-down', ['ml-1']);
                    }

                    $branchurl = $this->page->url;
                    $branchsort++;
                    $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);

                    // Course sections.
                    if ($this->page->theme->settings->enablecoursesections) {
                        $this->create_course_sections_menu($branch);
                    }

                    // Display Participants.
                    $branchmenusort = 10000;
                    if ($this->page->theme->settings->displayparticipants) {
                        $branchtitle = get_string('people', 'theme_adaptable');
                        $branchlabel = \theme_adaptable\toolbox::getfontawesomemarkup(
                            'users',
                            ['icon', 'mr-1'],
                            [],
                            '',
                            $branchtitle
                        ) . $branchtitle;
                        $branchurl = new url('/user/index.php', ['id' => $this->page->course->id]);
                        $branch->add($branchlabel, $branchurl, $branchtitle, $branchmenusort);
                    }

                    // Display Grades.
                    if ($this->page->theme->settings->displaygrades) {
                        $branchtitle = get_string('grades');
                        $branchlabel = $this->pix_icon('i/grades', $branchtitle, '') . $branchtitle;
                        $branchurl = new url('/grade/report/index.php', ['id' => $this->page->course->id]);
                        $branchmenusort++;
                        $branch->add($branchlabel, $branchurl, $branchtitle, $branchmenusort);
                    }

                    // Kaltura video gallery.
                    if (\theme_adaptable\toolbox::kalturaplugininstalled()) {
                        $branchtitle = get_string('nav_mediagallery', 'local_kalturamediagallery');
                        $branchlabel = $this->pix_icon('media-gallery', $branchtitle, 'local_kalturamediagallery') . $branchtitle;
                        $branchurl = new url(
                            '/local/kalturamediagallery/index.php',
                            ['courseid' => $this->page->course->id]
                        );
                        $branchmenusort++;
                        $branch->add($branchlabel, $branchurl, $branchtitle, $branchmenusort);
                    }

                    // Display Competencies.
                    if (get_config('core_competency', 'enabled')) {
                        if ($this->page->theme->settings->enablecompetencieslink) {
                            $branchtitle = get_string('competencies', 'competency');
                            $branchlabel = $this->pix_icon('i/competencies', $branchtitle, '') . $branchtitle;
                            $branchurl = new url(
                                '/admin/tool/lp/coursecompetencies.php',
                                ['courseid' => $this->page->course->id]
                            );
                            $branchmenusort++;
                            $branch->add($branchlabel, $branchurl, $branchtitle, $branchmenusort);
                        }
                    }

                    // Display activities.
                    $data = theme_adaptable_get_course_activities();
                    foreach ($data as $modname => $modfullname) {
                        if ($modname === 'resources') {
                            $icon = $this->pix_icon('monologo', get_string('pluginname', 'mod_page'), 'mod_page');
                            $branchmenusort++;
                            $branch->add(
                                $icon . $modfullname,
                                new url('/course/resources.php', ['id' => $this->page->course->id]),
                                $modfullname,
                                $branchmenusort
                            );
                        } else {
                            $icon = $this->pix_icon('monologo', get_string('pluginname', 'mod_' . $modname), $modname);
                            $branchmenusort++;
                            $branch->add(
                                $icon . $modfullname,
                                new url('/mod/' . $modname . '/index.php', ['id' => $this->page->course->id]),
                                $modfullname,
                                $branchmenusort
                            );
                        }
                    }
                }
            }
        }

        if ($navbardisplayicons) {
            $helpicon = \theme_adaptable\toolbox::getfontawesomemarkup('life-ring', ['fa-lg']);
        } else {
            $helpicon = '';
        }

        if (!empty($this->page->theme->settings->helplinkscount)) {
            for ($helpcount = 1; $helpcount <= $this->page->theme->settings->helplinkscount; $helpcount++) {
                $enablehelpsetting = 'enablehelp' . $helpcount;
                if (!empty($this->page->theme->settings->$enablehelpsetting)) {
                    $access = true;
                    $helpprofilefieldsetting = 'helpprofilefield' . $helpcount;
                    if (!empty($this->page->theme->settings->$helpprofilefieldsetting)) {
                        $fields = explode('=', $this->page->theme->settings->$helpprofilefieldsetting);
                        $ftype = $fields[0];
                        $setvalue = $fields[1];
                        if (!$this->check_menu_access($ftype, $setvalue, 'help' . $helpcount)) {
                            $access = false;
                        }
                    }

                    if ($access && !$this->hideinforum()) {
                        $helplinktitlesetting = 'helplinktitle' . $helpcount;
                        if (empty($this->page->theme->settings->$helplinktitlesetting)) {
                            $branchtitle = get_string('helptitle', 'theme_adaptable', ['number' => $helpcount]);
                        } else {
                            $branchtitle = $this->page->theme->settings->$helplinktitlesetting;
                        }
                        $branchlabel = $helpicon . $branchtitle;
                        $branchurl = new url(
                            $this->page->theme->settings->$enablehelpsetting,
                            ['helptarget' => $this->page->theme->settings->helptarget]
                        );

                        $branchsort++;
                        $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
                    }
                }
            }
        }

        // Custom menu.
        if ((!empty($CFG->custommenuitems)) &&
            (empty($this->page->theme->settings->disablecustommenu))) {
            $custommenutitle = \theme_adaptable\toolbox::get_setting('custommenutitle', 'format_plain');
            $branch = null;
            if (!empty($custommenutitle)) {
                $branchlabel = '';
                $branchtitle = $custommenutitle;
                if ($navbardisplayicons) {
                    $branchlabel .=
                        \theme_adaptable\toolbox::getfontawesomemarkup('bars', ['mr-1', 'fa-lg']) . '<span class="menutitle">';
                }
                $branchlabel .= $branchtitle;
                if ($navbardisplayicons) {
                    $branchlabel .= '</span>';
                }

                // Check the option of displaying a sub-menu arrow symbol.
                if (!empty($this->page->theme->settings->navbardisplaysubmenuarrow)) {
                    $branchlabel .= \theme_adaptable\toolbox::getfontawesomemarkup('caret-down', ['ml-1']);
                }

                $branchurl = $this->page->url;
                $branchsort++;
                $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
            }

            $menu->add_custom_menu_items($CFG->custommenuitems, current_language(), $branch);
        }

        return $menu;
    }

    /**
     * Adds the course sections to the 'This course' menu.
     *
     * @param custom_menu_item $menu The menu to add to.
     */
    protected function create_course_sections_menu($menu) {
        global $COURSE;

        $courseformat = course_get_format($COURSE);
        $modinfo = get_fast_modinfo($COURSE);
        $numsections = $courseformat->get_last_section_number();
        $sectionsformnenu = [];
        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section > $numsections) {
                // Don't link to stealth sections.
                continue;
            }
            /* Show the section if the user is permitted to access it, OR if it's not available
               but there is some available info text which explains the reason & should display. */
            $showsection = $thissection->uservisible ||
                ($thissection->visible && !$thissection->available && !empty($thissection->availableinfo));

            if (($showsection) || ($section == 0)) {
                $sectionsformnenu[$section] = [
                    'sectionname' => $courseformat->get_section_name($section),
                    'url' => $courseformat->get_view_url($section),
                ];
            }
        }

        if (!empty($sectionsformnenu)) { // Rare but possible!
            $branchtitle = get_string('sections', 'theme_adaptable');
            $branchlabel = \theme_adaptable\toolbox::getfontawesomemarkup(
                'list-ol',
                ['icon', 'fa-lg'],
                [],
                '',
                $branchtitle
            ) . $branchtitle;
            $branch = $menu->add($branchlabel, null, $branchtitle, 100003);

            foreach ($sectionsformnenu as $sectionformenu) {
                $branch->add($sectionformenu['sectionname'], $sectionformenu['url'], $sectionformenu['sectionname']);
            }
        }

        return $sectionsformnenu;
    }

    /**
     * Returns html to render main navigation menu
     *
     * @param string $menuid The id to use when creating menu.  Used so this can be called for a nav drawer style display.
     *
     * @return string Markup.
     */
    public function navigation_menu($menuid) {
        static $menu = null;

        if (is_null($menu)) {
            $menu = $this->navigation_menu_content();
        }

        return $this->render_custom_menu($menu, '', '', $menuid);
    }

    /**
     * Returns html to render tools menu in main navigation bar
     *
     * @param string $menuid The id to use when creating menu.  Used so this could be called for a nav drawer style display.
     *
     *
     * @return string
     */
    public function tools_menu($menuid = '') {
        $retval = '';
        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();

        if (is_object($localtoolbox)) {
            $themesettings = \theme_adaptable\toolbox::get_settings();
            $retval = $localtoolbox->tools_menu($themesettings, $this->page, $this, $menuid);
        }

        return $retval;
    }

    /**
     * Returns The HTML to render logo in the header.
     * @param bool/int $currenttopcat The id of the current top category or false if none.
     * @param bool $shownavbar If the navbar is shown.
     *
     * @return string Markup.
     */
    public function get_logo($currenttopcat, $shownavbar) {
        global $CFG, $SITE;
        $logomarkup = '';

        $logosetarea = '';

        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();
        if (is_object($localtoolbox)) {
            $logosetarea = $localtoolbox->get_logo($currenttopcat, $logosetarea, $this->page->theme->settings);
        }

        if ((empty($logosetarea)) && (!empty($this->page->theme->settings->logo))) {
            $logosetarea = 'logo';
        }

        if (!empty($logosetarea)) {
            // Logo.
            $responsivelogo = (empty($this->page->theme->settings->responsivelogo)) ? '' : ' ' .
                $this->page->theme->settings->responsivelogo;
            $logomarkup = '<div class="pb-2 pr-3 pt-2 bd-highlight' . $responsivelogo . '">';
            $logo = '<img src=' . $this->page->theme->setting_file_url($logosetarea, $logosetarea) . ' id="logo"';
            $logo .= ' alt="' . get_string('logo', 'theme_adaptable') . '">';

            if ($shownavbar) {
                // Logo is not a link to site homepage when there is a navbar.
                $logomarkup .= $logo;
            } else {
                // Logo is a link to site homepage when there is no navbar.
                $logomarkup .= '<a href=' . $CFG->wwwroot . ' aria-label="' . get_string('home') . '" title="' .
                    format_string($SITE->fullname) . '">';
                $logomarkup .= $logo;
                $logomarkup .= '</a>';
            }
            $logomarkup .= '</div>';
        }

        return $logomarkup;
    }

    /**
     * Returns html to render title in the header.
     * @param bool/int $currenttopcat The id of the current top category or false if none.
     *
     * @return string Markup.
     */
    public function get_title($currenttopcat) {
        $themesettings = \theme_adaptable\toolbox::get_settings();

        $titlemarkup = '';
        $categoryheadercustomtitle = '';

        // If course id is not the site id then we display course title.
        if ($this->page->course->id != SITEID) {
            $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();
            if (is_object($localtoolbox)) {
                $categoryheadercustomtitle = $localtoolbox->get_title($currenttopcat, $categoryheadercustomtitle, $themesettings);
            }

            $coursetitle = $this->get_course_title();
            if (!empty($coursetitle)) {
                $titlemarkup .= '<div id="headertitle" class="bd-highlight pt-2 ' . $themesettings->responsiveheadertitle . '">';
                $titlemarkup .= '<h1>';
                if (!empty($categoryheadercustomtitle)) {
                     $titlemarkup .= '<span id="categorytitle">' . format_string($categoryheadercustomtitle) . '</span><br>';
                }
                $titlemarkup .= '<span id="coursetitle">' . $coursetitle . '</span>';
                $titlemarkup .= '</h1>';
                $titlemarkup .= '</div>';
            }
        }

        // If the course id is the site id or we're on a course and there is no title then we display the site title.
        if (($this->page->course->id == SITEID) || (empty($titlemarkup))) {
            $sitetitle = $this->get_site_title();
            if (empty($sitetitle)) {
                if (!empty($categoryheadercustomtitle)) {
                    $titlemarkup .= '<div id="headertitle" class="bd-highlight pt-2 ' .
                        $themesettings->responsiveheadertitle . '">';
                    $titlemarkup .= '<h1><span id="categorytitle">' . format_string($categoryheadercustomtitle) . '</span></h1>';
                    $titlemarkup .= '</div>';
                }
            } else {
                $titlemarkup .= '<div id="headertitle" class="bd-highlight pt-2 ' . $themesettings->responsiveheadertitle . '">';
                $titlemarkup .= '<h1>';
                $titlemarkup .= '<span id="sitetitle">' . $sitetitle . '</span>';
                if (!empty($categoryheadercustomtitle)) {
                     $titlemarkup .= '<br><span id="categorytitle">' . format_string($categoryheadercustomtitle) . '</span>';
                }
                $titlemarkup .= '</h1>';
                $titlemarkup .= '</div>';
            }
        }

        return $titlemarkup;
    }

    /**
     * Get the site title.
     *
     * return string Site title.
     */
    protected function get_site_title() {
        global $SITE;

        $sitetitle = '';
        $themesettings = \theme_adaptable\toolbox::get_settings();

        switch ($themesettings->sitetitle) {
            case 'default':
                $sitetitle = format_string($SITE->fullname);
                break;
            case 'custom':
                // Custom site title.
                if (!empty($themesettings->sitetitletext)) {
                    $header = $themesettings->sitetitletext;
                    if (strpos($this->page->pagetype, 'course-view-') !== 0) {
                        $header = preg_replace("/^" . $SITE->fullname . "/", "", $header);
                    }
                    $header = format_string($header);
                    $this->page->set_heading($header);

                    $sitetitle = format_text($themesettings->sitetitletext, FORMAT_HTML);
                }
                break;
        }

        return $sitetitle;
    }

    /**
     * Get the course title.
     *
     * return string Course title.
     */
    protected function get_course_title() {
        global $COURSE;

        $coursetitle = '';
        $themesettings = \theme_adaptable\toolbox::get_settings();

        switch ($themesettings->enablecoursetitle) {
            case 'fullname':
                // Full Course Name.
                $coursetitle = $COURSE->fullname;
                break;

            case 'shortname':
                // Short Course Name.
                $coursetitle = $COURSE->shortname;
                break;
        }

        if (!empty($coursetitle)) {
            // Pre-process to avoid any filter issue.
            $coursetitle = format_string($coursetitle);

            $coursetitlemaxwidth =
                (!empty($themesettings->coursetitlemaxwidth) ? $themesettings->coursetitlemaxwidth : 0);
            // Check max width of course title and trim if appropriate.
            if (($coursetitlemaxwidth > 0) && ($coursetitle <> '')) {
                if (\core_text::strlen($coursetitle) > $coursetitlemaxwidth) {
                    $coursetitle = \core_text::substr($coursetitle, 0, $coursetitlemaxwidth) . " ...";
                }
            }
        }

        return $coursetitle;
    }

    /**
     * Renders the context header for the page.
     *
     * @param array $headerinfo Heading information.
     * @param int $headinglevel What 'h' level to make the heading.
     * @return string A rendered context header.
     */
    public function context_header($headerinfo = null, $headinglevel = 1): string {
        if (empty($headerinfo)) {
            $headerinfo = [];
            $headerinfo['heading'] = $this->get_course_title();
        }
        return parent::context_header($headerinfo, $headinglevel);
    }

    /**
     * Returns html to render top menu items
     *
     * @param bool $showlinktext
     *
     * @return string
     */
    public function get_top_menus($showlinktext = false) {
        global $COURSE;
        $template = new stdClass();
        $menus = [];
        $visibility = true;
        $nummenus = 0;

        if (!empty($this->page->theme->settings->menuuseroverride)) {
            $visibility = $this->check_menu_user_visibility();
        }

        $template->showright = false;
        if (!empty($this->page->theme->settings->menuslinkright)) {
            $template->showright = true;
        }

        if (!empty($this->page->theme->settings->menuslinkicon)) {
            $template->menuslinkicon = $this->page->theme->settings->menuslinkicon;
        } else {
            $template->menuslinkicon = 'fa-link';
        }

        if ($visibility) {
            if (
                !empty($this->page->theme->settings->topmenuscount) && !empty($this->page->theme->settings->enablemenus)
                    && (!$this->page->theme->settings->disablemenuscoursepages || $COURSE->id == 1)
            ) {
                $topmenuscount = $this->page->theme->settings->topmenuscount;

                for ($i = 1; $i <= $topmenuscount; $i++) {
                    $menunumber = 'menu' . $i;
                    $newmenu = 'newmenu' . $i;
                    $fieldsetting = 'newmenu' . $i . 'field';
                    $newmenutitle = 'newmenu' . $i . 'title';
                    $requirelogin = 'newmenu' . $i . 'requirelogin';
                    $custommenuitems = '';
                    $access = true;

                    if (empty($this->page->theme->settings->$requirelogin) || isloggedin()) {
                        if (!empty($this->page->theme->settings->$fieldsetting)) {
                            $fields = explode('=', $this->page->theme->settings->$fieldsetting);
                            $ftype = $fields[0];
                            $setvalue = $fields[1];
                            if (!$this->check_menu_access($ftype, $setvalue, $menunumber)) {
                                $access = false;
                            }
                        }

                        if (!empty($this->page->theme->settings->$newmenu) && $access == true) {
                            $nummenus++;
                            $menu = ($this->page->theme->settings->$newmenu);
                            $title = ($this->page->theme->settings->$newmenutitle);
                            $custommenuitems = $this->parse_custom_menu($menu, format_string($title));
                            $custommenu = new custom_menu($custommenuitems, current_language());
                            $menus[] = $this->render_overlay_menu($custommenu);
                        }
                    }
                }
            }
        }

        if ($nummenus == 0) {
            return '';
        }

        $template->rows = [];

        static $grid = [
            '5' => '3',
            '6' => '3',
            '7' => '4',
            '8' => '4',
            '9' => '3',
            '10' => '4',
            '11' => '4',
            '12' => '4',
        ];

        if ($nummenus <= 4) {
            $row = new stdClass();
            $row->span = (12 / $nummenus);
            $row->menus = $menus;
            $template->rows[] = $row;
        } else {
            $numperrow = $grid[$nummenus];
            $chunks = array_chunk($menus, $numperrow);
            $menucount = 0;
            for ($i = 0; $i < $nummenus; $i++) {
                if ($i % $numperrow == 0) {
                    $row = new stdClass();
                    $row->span = (12 / $numperrow);
                    $row->menus = $chunks[$menucount++];
                    $template->rows[] = $row;
                }
            }
        }

        if ($showlinktext == false) {
            $template->showlinktext = false;
        } else {
            $template->showlinktext = true;
        }

        return $this->render_from_template('theme_adaptable/overlaymenu', $template);
    }

    /**
     * Render the menu items for the overlay menu
     *
     * @param custom_menu $menu
     * @return array of menus
     */
    private function render_overlay_menu(custom_menu $menu) {
        $template = new stdClass();
        if (!$menu->has_children()) {
            return '';
        }
        $template->menuitems = [];
        foreach ($menu->get_children() as $item) {
            $this->render_overlay_menu_item($item, $template->menuitems);
        }
        return $template;
    }

    /**
     * Render the overlay menu items.
     *
     * @param custom_menu_item $item
     * @param array $menuitems
     * @param int $level
     */
    private function render_overlay_menu_item(custom_menu_item $item, &$menuitems, $level = 0) {
        if ($item->has_children()) {
            $node = new stdClass();
            $node->title = $item->get_title();
            $node->text = $item->get_text();
            $node->class = 'level-' . $level;
            $menuitems[] = $node;

            /* Top level menu.  Check if URL contains a valid URL, if not
               then use standard javascript:void(0).  Done to fix current
               jquery / Bootstrap incompatibility with using # in target URLS.
               Ref: Issue 617 on Adaptable theme issues on Bitbucket. */
            if (empty($item->get_url())) {
                $node->url = "javascript:void(0)";
            } else {
                $node->url = $item->get_url();
            }

            $level++;
            foreach ($item->get_children() as $subitem) {
                $menuitems[] = $this->render_overlay_menu_item($subitem, $menuitems, $level);
            }
        } else {
            $node = new stdClass();
            $node->title = $item->get_title();
            $node->text = $item->get_text();
            $node->class = 'level-' . $level;
            $node->url = $item->get_url();
            $menuitems[] = $node;
        }
    }

    /**
     * Checks menu visibility where setup to allow users to control via custom profile setting.
     *
     * @return boolean
     */
    public function check_menu_user_visibility() {
        if (empty($this->page->theme->settings->menuuseroverride)) {
            return true;
        }

        global $USER;
        if (isset($USER->theme_adaptable_menus['menuvisibility'])) {
            $uservalue = $USER->theme_adaptable_menus['menuvisibility'];
        } else {
            $profilefield = $this->page->theme->settings->menuoverrideprofilefield;
            $profilefield = 'profile_field_' . $profilefield;
            $uservalue = $this->get_user_visibility($profilefield);
        }

        if ($uservalue == 0) {
            return true;
        }

        global $COURSE;
        if ($uservalue == 1 && $COURSE->id != 1) {
            return false;
        }

        if ($uservalue == 2) {
            return false;
        }

        // Default to true means we dont have to evaluate sitewide setting and guarantees return value.
        return true;
    }

    /**
     * Check users menu visibility settings, will store in session to avaoid repeated loading of profile data.
     * @param string $profilefield The profile field.
     * @return boolean Visibility.
     */
    public function get_user_visibility($profilefield) {
        global $CFG, $USER;

        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');
        profile_load_data($USER);
        if (empty($USER->$profilefield)) {
            $USER->theme_adaptable_menus['menuvisibility'] = 0;
        } else {
            $USER->theme_adaptable_menus['menuvisibility'] = $USER->$profilefield;
        }

        return $USER->theme_adaptable_menus['menuvisibility'];
    }

    /**
     * Checks menu access based on admin settings and a users custom profile fields.
     *
     * @param string $ftype the custom profile field.
     * @param string $setvalue the expected value a user must have in their profile field.
     * @param string $menu a token to identify the menu used to store access in session.
     * @return boolean.
     */
    public function check_menu_access($ftype, $setvalue, $menu) {
        global $CFG, $USER;
        $menuttl = $menu . 'ttl';
        $time = time();

        if ($this->page->theme->settings->menusession) {
            if (isset($USER->theme_adaptable_menus[$menu])) {
                // If cache hasn't yet expired.
                if ($USER->theme_adaptable_menus[$menuttl] >= $time) {
                    return $USER->theme_adaptable_menus[$menu];
                }
            }
        }

        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');
        profile_load_data($USER);
        $ftype = "profile_field_$ftype";
        if (isset($USER->$ftype)) {
            $usersvalue = $USER->$ftype;
        } else {
            $usersvalue = 'default-zz'; // Just want a value that will not be matched by accident.
        }

        $sessttl = ($time + ($this->page->theme->settings->menusessionttl * 60));
        $USER->theme_adaptable_menus[$menuttl] = $sessttl;
        if ($usersvalue == $setvalue) {
            $USER->theme_adaptable_menus[$menu] = true;
        } else {
            $USER->theme_adaptable_menus[$menu] = false;
        }

        return $USER->theme_adaptable_menus[$menu];
    }

    /**
     * Returns list of cohort enrollments
     *
     * @return array
     */
    public function get_cohort_enrollments() {
        global $DB, $USER;
        $userscohorts = $DB->get_records('cohort_members', ['userid' => $USER->id]);
        $courses = [];
        if ($userscohorts) {
            $cohortedcourseslist = $DB->get_records_sql('select '
                    . 'courseid '
                    . 'from {enrol} '
                    . 'where enrol = "cohort" '
                    . 'and customint1 in (?)', array_keys($userscohorts));
            $cohortedcourses = $DB->get_records_list('course', 'id', array_keys($cohortedcourseslist), null, 'shortname');
            foreach ($cohortedcourses as $course) {
                $courses[] = $course->shortname;
            }
        }
        return($courses);
    }

    /**
     * Returns contents of multiple comma delimited custom profile fields.
     *
     * @param string $profilefields delimited list of fields.
     * @return array of multiple comma delimited custom profile fields.
     */
    public function get_profile_field_contents($profilefields) {
        global $CFG, $USER;
        $timestamp = 'currentcoursestime';
        $list = 'currentcourseslist';
        $time = time();

        if (isset($USER->theme_adaptable_menus[$timestamp])) {
            if ($USER->theme_adaptable_menus[$timestamp] >= $time) {
                if (isset($USER->theme_adaptable_menus[$list])) {
                    return $USER->theme_adaptable_menus[$list];
                }
            }
        }

        $retval = [];

        require_once($CFG->dirroot . '/user/profile/lib.php');
        require_once($CFG->dirroot . '/user/lib.php');
        profile_load_data($USER);

        $fields = explode(',', $profilefields);
        foreach ($fields as $field) {
            $field = trim($field);
            $field = "profile_field_$field";
            if (isset($USER->$field)) {
                $vals = explode(',', $USER->$field);
                foreach ($vals as $value) {
                    $retval[] = trim($value);
                }
            }
        }

        $USER->theme_adaptable_menus[$list] = $retval;
        $USER->theme_adaptable_menus[$timestamp] = $time + 1000 * 60 * 3; // Sess TTL.

        return $retval;
    }

    /**
     * Parses / wraps custom menus in HTML.
     *
     * @param string $menu
     * @param string $label
     * @param string $class
     * @param string $close
     *
     * @return string
     */
    public function parse_custom_menu($menu, $label, $class = '', $close = '') {

        /* Top level menu option.  No URL added after $close (previously was #).
           Done to fix current jquery / Bootstrap version incompatibility with using #
           in target URLS. Ref: Issue 617 on Adaptable theme issues on Bitbucket. */
        $custommenuitems = $class . $label . $close . "||" . $label . "\n";
        $arr = explode("\n", $menu);

        // We want to force everything inputted under this menu.
        foreach ($arr as $key => $value) {
            $arr[$key] = '-' . $arr[$key];
        }

        $custommenuitems .= implode("\n", $arr);
        return $custommenuitems;
    }

    /**
     * Hide tools menu in forum to make room for forum search optoin
     *
     * @return boolean
     */
    public function hideinforum() {
        $hidelinks = false;
        if (!empty($this->page->theme->settings->hideinforum)) {
            if (strpos($this->page->pagetype, 'mod-forum-') !== false) {
                $hidelinks = true;
            }
        }
        return $hidelinks;
    }

    /**
     * Returns language menu
     *
     * @param bool $showtext
     *
     * @return string
     */
    public function lang_menu($showtext = true) {
        global $CFG;
        $langmenu = new custom_menu();

        $addlangmenu = true;
        $langs = get_string_manager()->get_list_of_translations();
        if (count($langs) < 2 || empty($CFG->langmenu) || ($this->page->course != SITEID && !empty($this->page->course->lang))) {
            $addlangmenu = false;
        }

        if ($addlangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();

            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }

            if ($showtext != true) {
                $currentlang = '';
            }

            $this->language = $langmenu->add(
                '<i class="icon fa fa-globe fa-lg"></i><span class="langdesc">' . $currentlang . '</span>',
                new url($this->page->url),
                $strlang,
                10000
            );

            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new url($this->page->url, ['lang' => $langtype]), $langname);
            }
        }
        return $this->render_custom_menu($langmenu, '', '', 'langmenu');
    }

    /**
     * Display custom menu in the format required for the nav drawer. Slight cludge here to make this work.
     * The calling function can't call the default custom_menu() method as there is no way to know to
     * render custom menu items in the format required for the drawer (which is different from displaying on the normal navbar).
     *
     * @return Custom menu html
     */
    public function custom_menu_drawer() {
        global $CFG;

        if (!empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        } else {
            return '';
        }

        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu, '', '', 'custom-menu-drawer');
    }

    /**
     * Render custom menu.
     *
     * @param custom_menu $menu
     * @param string $wrappre
     * @param string $wrappost
     * @param string $menuid
     *
     * @return string
     */
    public function render_custom_menu(\custom_menu $menu, $wrappre = '', $wrappost = '', $menuid = '') {
        if (!$menu->has_children()) {
            return '';
        }

        $content = '';
        foreach ($menu->get_children() as $item) {
            if (stristr($menuid, 'drawer')) {
                $content .= $this->render_custom_menu_item_drawer($item, 0, $menuid, false);
            } else {
                $content .= $this->render_custom_menu_item($item, 0, $menuid);
            }
        }
        $content = $wrappre . $content . $wrappost;

        return $content;
    }

    /**
     * This code renders the custom menu items for the bootstrap dropdown menu.
     *
     * @param custom_menu_item $menunode
     * @param int $level = 0
     * @param int $menuid
     *
     * @return string
     */
    protected function render_custom_menu_item(custom_menu_item $menunode, $level = 0, $menuid = '') {
        static $submenucount = 0;

        // If the node has a url, then use it, even if it has children as the URL could be that of an overview page.
        if ($menunode->get_url() !== null) {
            $url = $menunode->get_url();
        } else {
            $url = '#';
        }
        if ($menunode->has_children()) {
            $submenucount++;
            $content = '<li class="nav-item dropdown my-auto">';
            $content .= html_writer::start_tag('a', ['href' => $url,
                'class' => 'nav-link dropdown-toggle my-auto', 'role' => 'button',
                'id' => $menuid . $submenucount,
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false',
                'aria-controls' => 'dropdown' . $menuid . $submenucount,
                'data-target' => $url,
                'data-toggle' => 'dropdown',
                'title' => $menunode->get_title(), ]);
            $content .= $menunode->get_text();
            $content .= '</a>';
            $content .= '<ul role="menu" class="dropdown-menu" id="dropdown' . $menuid . $submenucount . '" aria-labelledby="'
                . $menuid . $submenucount . '">';

            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode, 1, $menuid . $submenucount);
            }
            $content .= '</ul></li>';
        } else {
            if (preg_match("/^#+$/", $menunode->get_text())) {
                // This is a divider.
                $content = html_writer::start_tag('li', ['class' => 'dropdown-divider']);
            } else {
                if ($level == 0) {
                    $content = '<li class="nav-item">';
                    $linkclass = 'nav-link';
                } else {
                    $content = '<li>';
                    $linkclass = 'dropdown-item';
                }

                /* This is a bit of a cludge, but allows us to pass url, of type url with a param of
                 * "helptarget", which when equal to "_blank", will create a link with target="_blank" to allow the link to open
                 * in a new window.  This param is removed once checked.
                 */
                $attributes = [
                    'title' => $menunode->get_title(),
                    'class' => $linkclass,
                ];
                if (is_object($url) && (get_class($url) == 'core\url')) {
                    $helptarget = $url->get_param('helptarget');
                    if ($helptarget != null) {
                        $url->remove_params('helptarget');
                        $attributes['target'] = $helptarget;
                    }
                }
                $content .= html_writer::link($url, $menunode->get_text(), $attributes);

                $content .= "</li>";
            }
        }
        return $content;
    }

    /**
     * This code renders the custom menu items for the bootstrap dropdown menu.
     *
     * @param custom_menu_item $menunode
     * @param int $level = 0
     * @param int $menuid
     * @param bool $indent
     *
     * @return string
     */
    protected function render_custom_menu_item_drawer(custom_menu_item $menunode, $level = 0, $menuid = '', $indent = false) {
        static $submenucount = 0;

        if ($menunode->has_children()) {
            $submenucount++;
            $content = '<li class="m-l-0">';
            $content .= html_writer::start_tag('a', ['href' => '#' . $menuid . $submenucount,
                'class' => 'list-group-item dropdown-toggle',
                'aria-haspopup' => 'true', 'data-target' => '#', 'data-toggle' => 'collapse',
                'title' => $menunode->get_title(), ]);
            $content .= $menunode->get_text();
            $content .= '</a>';

            $content .= '<ul class="collapse" id="' . $menuid . $submenucount . '">';
            $indent = true;
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item_drawer($menunode, 1, $menuid . $submenucount, $indent);
            }
            $content .= '</ul></li>';
        } else {
            // The node doesn't have children so produce a final menuitem.
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#';
            }

            if ($indent) {
                $dataindent = 1;
                $marginclass = 'm-l-1';
            } else {
                $dataindent = 0;
                $marginclass = 'm-l-0';
            }

            $content = '<li class="' . $marginclass . '">';
            $content .= '<a class="list-group-item list-group-item-action" href="' . $url . '" ';
            $content .= 'data-key="" data-isexpandable="0" data-indent="' . $dataindent;
            $content .= '" data-showdivider="0" data-type="1" data-nodetype="1"';
            $content .= 'data-collapse="0" data-forceopen="1" data-isactive="1" data-hidden="0" ';
            $content .= 'data-preceedwithhr="0" data-parent-key="' . $menuid . '">';
            $content .= '<div class="' . $marginclass . '">';
            $content .= $menunode->get_text();
            $content .= '</div></a></li>';
        }
        return $content;
    }

    /**
     * Generates elements of the login main content.
     *
     * @param string   $logincontent Login content.
     *
     * return stdClass Header and Footer inclusion booleans.
     */
    public function generate_login(&$logincontent) {
        $retr = null;
        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();
        if (is_object($localtoolbox)) {
            $themesettings = \theme_adaptable\toolbox::get_settings();
            $retr = $localtoolbox->generate_login($logincontent, $themesettings);
        } else {
            $retr = new stdClass();
            $retr->header = false;
            $retr->footer = false;
        }
        return $retr;
    }

    /**
     * Renders the login form.
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {
        global $SITE;

        $context = $form->export_for_template($this);

        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context->logourl = $url;
        $context->sitename = format_string(
            $SITE->fullname,
            true,
            ['context' => context_course::instance(SITEID), "escape" => false]
        );

        if ($context->hasidentityproviders) {
            $authsequence = get_enabled_auth_plugins(); // Get all auths.
            if (in_array('oidc', $authsequence)) {
                $authplugin = get_auth_plugin('oidc');
                $oidc = $authplugin->loginpage_idp_list($this->page->url->out(false));
                if (!empty($oidc)) {
                    $context->hasoidc = true;
                }
            }
        }

        return $this->render_from_template('theme_adaptable/core/loginform', $context);
    }

    /**
     * Renders tabtree
     *
     * @param tabtree $tabtree
     * @return string
     */
    protected function render_tabtree(\tabtree $tabtree) {
        if (empty($tabtree->subtree)) {
            return '';
        }
        $firstrow = $secondrow = '';
        foreach ($tabtree->subtree as $tab) {
            $firstrow .= $this->render($tab);
            if (($tab->selected || $tab->activated) && !empty($tab->subtree) && $tab->subtree !== []) {
                $secondrow = $this->tabtree($tab->subtree);
            }
        }
        return html_writer::tag('ul', $firstrow, ['class' => 'nav nav-tabs mb-3']) . $secondrow;
    }

    /**
     * Renders tabobject (part of tabtree)
     *
     * This function is called from core_renderer::render_tabtree()
     * and also it calls itself when printing the $tabobject subtree recursively.
     *
     * @param tabobject $tab
     * @return string HTML fragment
     */
    protected function render_tabobject(\tabobject $tab) {
        if ($tab->selected || $tab->activated) {
            return html_writer::tag('li', html_writer::tag(
                'a',
                $tab->text,
                ['class' => 'nav-link active']
            ), ['class' => 'nav-item']);
        } else if ($tab->inactive) {
            return html_writer::tag('li', html_writer::tag(
                'a',
                $tab->text,
                ['class' => 'nav-link disabled']
            ), ['class' => 'nav-item']);
        } else {
            if (!($tab->link instanceof url)) {
                // Backward compatibility when link was passed as quoted string.
                $link = "<a class=\"nav-link\" href=\"$tab->link\" title=\"$tab->title\">$tab->text</a>";
            } else {
                $link = html_writer::link($tab->link, $tab->text, ['title' => $tab->title, 'class' => 'nav-link']);
            }
            return html_writer::tag('li', $link, ['class' => 'nav-item']);
        }
    }

    /**
     * Returns empty string
     *
     * @return string
     */
    protected function theme_switch_links() {
        // We're just going to return nothing and fail nicely, whats the point in bootstrap if not for responsive?
        return '';
    }

    /**
     * Get the HTML for blocks in the given region.
     *
     * @since Moodle 2.5.1 2.6
     * @param string $region The region to get HTML for.
     * @param array $classes Wrapping tag classes.
     * @param string $tag Wrapping tag.
     * @param boolean $fakeblocksonly Include fake blocks only.
     * @return string HTML.
     */
    public function blocks($region, $classes = [], $tag = 'aside', $fakeblocksonly = false) {
        $displayregion = $this->page->apply_theme_region_manipulations($region);
        $editing = $this->page->user_is_editing();
        $classes = (array)$classes;
        $classes[] = 'block-region';
        if ($editing) {
            $classes[] = 'pl-2';
            $classes[] = 'pr-2';
            $classes[] = 'pt-2';
        }
        $attributes = [
            'id' => 'block-region-'.preg_replace('#[^a-zA-Z0-9_\-]+#', '-', $displayregion),
            'class' => join(' ', $classes),
            'data-blockregion' => $displayregion,
            'data-droptarget' => '1',
        ];

        $blockcontent = '';
        if ($this->page->blocks->region_has_content($displayregion, $this)) {
            $blockcontent .= html_writer::tag('h2', get_string('blocks'), ['class' => 'sr-only']) .
                $this->blocks_for_region($displayregion, $fakeblocksonly);
        } else {
            $blockcontent .= html_writer::tag('h2', get_string('blocks'), ['class' => 'sr-only']);
        }
        $blockcontent = html_writer::tag($tag, $blockcontent, $attributes);

        $content = '';
        if ($editing) {
            $title = $this->block_region_title($region);
            // Add block button in editing mode.
            $addblockbutton = $this->addblockbutton($region);

            $content = html_writer::tag('div', $title.$blockcontent.$addblockbutton, ['class' => 'my-1 adaptable-block-area']);
        } else {
            $content = $blockcontent;
        }

        return $content;
    }

    /**
     * Output all the blocks in a particular region.
     *
     * @param string $region the name of a region on this page.
     * @param boolean $fakeblocksonly Output fake block only.
     * @return string the HTML to be output.
     */
    public function blocks_for_region($region, $fakeblocksonly = false) {
        /* If 'shownavigationblockoncoursepage' is false and we are in a 'course' or 'incourse' page then
           the navigation block will not be shown. */
        if (
            (!empty($this->page->theme->settings->shownavigationblockoncoursepage)) ||
            (($this->page->pagelayout != 'course') && ($this->page->pagelayout != 'incourse'))
        ) {
            return parent::blocks_for_region($region, $fakeblocksonly);
        }
        $blockcontents = $this->page->blocks->get_content_for_region($region, $this);
        $blocks = $this->page->blocks->get_blocks_for_region($region);

        $lastblock = null;
        $zones = [];
        foreach ($blocks as $block) {
            if ($block->instance->blockname == 'navigation') {
                continue;
            }
            $zones[] = $block->title;
        }
        $output = '';

        foreach ($blockcontents as $bc) {
            if ($bc->attributes['data-block'] == 'navigation') {
                continue;
            }
            if ($bc instanceof block_contents) {
                if ($fakeblocksonly && !$bc->is_fake()) {
                    // Skip rendering real blocks if we only want to show fake blocks.
                    continue;
                }
                $output .= $this->block($bc, $region);
                $lastblock = $bc->title;
            } else if ($bc instanceof block_move_target) {
                if (!$fakeblocksonly) {
                    $output .= $this->block_move_target($bc, $zones, $lastblock, $region);
                }
            } else {
                throw new coding_exception('Unexpected type of thing (' . get_class($bc) . ') found in list of block contents.');
            }
        }
        return $output;
    }

    /**
     * Generate the add block button when editing mode is turned on and the user can edit blocks.
     *
     * @param string $region where new blocks should be added.
     * @return string html for the add block button.
     */
    public function addblockbutton($region = ''): string {
        $content = parent::addblockbutton($region);

        if (!empty($content)) {
            // Wrapper.
            $content = html_writer::tag('div', $content, ['class' => 'd-flex justify-content-center']);
        }

        return $content;
    }

    /**
     * This is an optional menu that can be added to a layout by a theme. It contains the
     * menu for the course administration, only on the course main page. Lifted from Boost theme
     * to use for the course actions menu.
     *
     * @return string
     */
    public function context_header_settings_menu() {
        $context = $this->page->context;

        $coursecontext = context_course::instance($this->page->course->id);
        if (!\theme_adaptable\toolbox::get_setting('editcognocourseupdate')) {
            if (!has_capability('moodle/course:update', $coursecontext)) {
                return '';
            }
        }

        $menu = new \action_menu();

        $items = $this->page->navbar->get_items();
        $currentnode = end($items);

        $showcoursemenu = false;
        $showfrontpagemenu = false;
        $showusermenu = false;

        // We are on the course home page.
        if (
            ($context->contextlevel == CONTEXT_COURSE) &&
            !empty($currentnode) &&
            ($currentnode->type == navigation_node::TYPE_COURSE ||
            $currentnode->type == navigation_node::TYPE_SECTION ||
            $currentnode->type == navigation_node::TYPE_SETTING)
        ) { // Show cog on grade report page.
            $showcoursemenu = true;
        }

        $courseformat = course_get_format($this->page->course);
        // This is a single activity course format, always show the course menu on the activity main page.
        if (
            $context->contextlevel == CONTEXT_MODULE &&
            !$courseformat->has_view_page()
        ) {
            $this->page->navigation->initialise();
            $activenode = $this->page->navigation->find_active_node();
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $showcoursemenu = true;
            } else if (
                !empty($activenode) && ($activenode->type == navigation_node::TYPE_ACTIVITY ||
                $activenode->type == navigation_node::TYPE_RESOURCE)
            ) {
                // We only want to show the menu on the first page of the activity. This means
                // the breadcrumb has no additional nodes.
                if ($currentnode && ($currentnode->key == $activenode->key && $currentnode->type == $activenode->type)) {
                    $showcoursemenu = true;
                }
            }
        }

        // This is the site front page.
        if (
            $context->contextlevel == CONTEXT_COURSE &&
            !empty($currentnode) &&
            $currentnode->key === 'home'
        ) {
                $showfrontpagemenu = true;
        }

        // This is the user profile page.
        if (
            $context->contextlevel == CONTEXT_USER &&
            !empty($currentnode) &&
            ($currentnode->key === 'myprofile')
        ) {
                $showusermenu = true;
        }

        if ($showfrontpagemenu) {
            $settingsnode = $this->page->settingsnav->find('frontpage', navigation_node::TYPE_SETTING);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                // We only add a list to the full settings menu if we didn't include every node in the short menu.
                if ($skipped) {
                    $text = get_string('morenavigationlinks');
                    $url = new url('/course/admin.php', ['courseid' => $this->page->course->id]);
                    $link = new action_link($url, $text, null, null, new pix_icon('t/edit', ''));
                    $menu->add_secondary_action($link);
                }
            }
            return $this->render($menu);
        } else if ($showcoursemenu) {
            $settingsnode = $this->page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                // We only add a list to the full settings menu if we didn't include every node in the short menu.
                if ($skipped) {
                    $text = get_string('morenavigationlinks');
                    $url = new url('/course/admin.php', ['courseid' => $this->page->course->id]);
                    $link = new action_link($url, $text, null, null, new pix_icon('t/edit', ''));
                    $menu->add_secondary_action($link);
                }
            }
            return $this->render($menu);
        } else if ($showusermenu) {
            // Get the course admin node from the settings navigation.
            $settingsnode = $this->page->settingsnav->find('useraccount', navigation_node::TYPE_CONTAINER);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $this->build_action_menu_from_navigation($menu, $settingsnode);
            }
            return $this->render($menu);
        }

        return '';
    }

    /**
     * Mobile settings menu.
     *
     * TODO: Possibly make a Mustache template for all of the menu?
     *
     * @return string Markup.
     */
    public function context_mobile_settings_menu() {
        $output = '';

        $showcourseitems = false;
        $context = $this->page->context;
        $items = $this->page->navbar->get_items();
        $currentnode = end($items);

        // We are on the course home page.
        if (
            ($context->contextlevel == CONTEXT_COURSE) &&
            !empty($currentnode) &&
            ($currentnode->type == navigation_node::TYPE_COURSE || $currentnode->type == navigation_node::TYPE_SECTION)
        ) {
            $showcourseitems = true;
        }

        $courseformat = course_get_format($this->page->course);
        // This is a single activity course format, always show the course menu on the activity main page.
        if (
            $context->contextlevel == CONTEXT_MODULE &&
            !$courseformat->has_view_page()
        ) {
            $this->page->navigation->initialise();
            $activenode = $this->page->navigation->find_active_node();
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $showcourseitems = true;
            } else if (
                !empty($activenode) && ($activenode->type == navigation_node::TYPE_ACTIVITY ||
                $activenode->type == navigation_node::TYPE_RESOURCE)
            ) {
                /* We only want to show the menu on the first page of the activity.  This means
                   the breadcrumb has no additional nodes. */
                if ($currentnode && ($currentnode->key == $activenode->key && $currentnode->type == $activenode->type)) {
                    $showcourseitems = true;
                }
            }
        }

        if ($showcourseitems) {
            $settingsnode = $this->page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
            if ($settingsnode) {
                $displaykeys = ['turneditingonoff', 'editsettings']; // In the order we want.
                $displaykeyscount = count($displaykeys);
                $displaynodes = [];
                foreach ($settingsnode->children as $node) {
                    if ($node->display) {
                        if (in_array($node->key, $displaykeys)) {
                            $displaynodes[$node->key] = $node;
                        }
                        if (count($displaynodes) == $displaykeyscount) {
                            break;
                        }
                    }
                }

                foreach ($displaykeys as $displaykey) { // Ensure order.
                    if (!empty($displaynodes[$displaykey])) {
                        $currentnode = $displaynodes[$displaykey];
                        $output .= '<a class="list-group-item list-group-item-action " href="' . $currentnode->action . '">';
                        $output .= '<div class="m-l-0">';
                        $output .= '<div class="media">';
                        $output .= '<span class="media-left">';
                        $output .= $this->render($currentnode->icon);
                        $output .= '</span>';
                        $output .= '<span class="media-body ">' . $currentnode->text . '</span>';
                        $output .= '</div>';
                        $output .= '</div>';
                        $output .= '</a >';
                    }
                }
            }
        }

        return $output;
    }

    /**
     * This is an optional menu that can be added to a layout by a theme. It contains the
     * menu for the most specific thing from the settings block. E.g. Module administration. Lifted from Boost.
     *
     * @return string
     */
    public function region_main_settings_menu() {
        if (!\theme_adaptable\toolbox::get_setting('editcognocourseupdate')) {
            $coursecontext = context_course::instance($this->page->course->id);
            if (!has_capability('moodle/course:update', $coursecontext)) {
                return '';
            }
        }

        $context = $this->page->context;
        $menu = new \action_menu();

        if ($context->contextlevel == CONTEXT_MODULE) {
            $this->page->navigation->initialise();
            $node = $this->page->navigation->find_active_node();
            $buildmenu = true;
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $buildmenu = true;
            } else if (
                !empty($node) && ($node->type == navigation_node::TYPE_ACTIVITY ||
                    $node->type == navigation_node::TYPE_RESOURCE)
            ) {
                $items = $this->page->navbar->get_items();
                $navbarnode = end($items);
                /* We only want to show the menu on the first page of the activity. This means
                   the breadcrumb has no additional nodes. */
                if ($navbarnode && ($navbarnode->key === $node->key && $navbarnode->type == $node->type)) {
                    $buildmenu = true;
                }
            }
            if ($buildmenu) {
                // Get the course admin node from the settings navigation.
                $node = $this->page->settingsnav->find('modulesettings', navigation_node::TYPE_SETTING);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }
        } else if ($context->contextlevel == CONTEXT_COURSECAT) {
            // For course category context, show category settings menu, if we're on the course category page.
            if ($this->page->pagetype === 'course-index-category') {
                $node = $this->page->settingsnav->find('categorysettings', navigation_node::TYPE_CONTAINER);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }
        } else {
            return '';
        }
        return $this->render($menu);
    }

    /**
     * Take a node in the nav tree and make an action menu out of it.
     * The links are injected in the action menu. Lifted from Boost theme.
     *
     * @param action_menu $menu
     * @param navigation_node $node
     * @param boolean $indent
     * @param boolean $onlytopleafnodes
     * @return boolean nodesskipped - True if nodes were skipped in building the menu
     */
    protected function build_action_menu_from_navigation(
        \action_menu $menu,
        navigation_node $node,
        $indent = false,
        $onlytopleafnodes = false
    ) {
        $skipped = false;

        // Build an action menu based on the visible nodes from this navigation tree.
        foreach ($node->children as $menuitem) {
            if ($menuitem->display) {
                if ($onlytopleafnodes && $menuitem->children->count()) {
                    $skipped = true;
                    continue;
                }
                if ($menuitem->action) {
                    if ($menuitem->action instanceof action_link) {
                        $link = $menuitem->action;
                        // Give preference to setting icon over action icon.
                        if (!empty($menuitem->icon)) {
                            $link->icon = $menuitem->icon;
                        }
                    } else {
                        $link = new action_link($menuitem->action, $menuitem->text, null, null, $menuitem->icon);
                    }
                } else {
                    if ($onlytopleafnodes) {
                        $skipped = true;
                        continue;
                    }
                    $link = new action_link(new url('#'), $menuitem->text, null, ['disabled' => true], $menuitem->icon);
                }
                if ($indent) {
                    $link->add_class('ml-4');
                }
                if (!empty($menuitem->classes)) {
                    $link->add_class(implode(" ", $menuitem->classes));
                }

                $menu->add_secondary_action($link);
                $skipped = $skipped || $this->build_action_menu_from_navigation($menu, $menuitem, true);
            }
        }
        return $skipped;
    }

    /**
     * Redirects the user by any means possible given the current state
     *
     * This function should not be called directly, it should always be called using
     * the redirect function in lib/weblib.php
     *
     * The redirect function should really only be called before page output has started
     * however it will allow itself to be called during the state STATE_IN_BODY
     *
     * @param string $encodedurl The URL to send to encoded if required
     * @return string The HTML with javascript refresh...
     */
    public function adaptable_redirect($encodedurl) {
        $url = str_replace('&amp;', '&', $encodedurl);
        $this->page->requires->js_function_call('document.location.replace', [$url], false, '0');
        $output = $this->opencontainers->pop_all_but_last();
        $output .= $this->footer();
        return $output;
    }

    /**
     * Returns a search box.
     *
     * @param  string $id     The search box wrapper div id, defaults to an autogenerated one.
     * @return string         HTML with the search form hidden by default.
     */
    public function search_box($id = false) {
        global $CFG;

        /* Accessing $CFG directly as using \core_search::is_global_search_enabled would
           result in an extra included file for each site, even the ones where global search
           is disabled. */
        if (empty($CFG->enableglobalsearch) || !has_capability('moodle/search:query', \context_system::instance())) {
            $action = new url('/course/search.php');
            $searchstring = get_string('coursesearch', 'theme_adaptable');
        } else {
            $action = new url('/search/index.php');
            $searchstring = get_string('globalsearch', 'core_admin');
        }

        $data = [
            'action' => $action,
            'hiddenfields' => (object) ['name' => 'context', 'value' => $this->page->context->id],
            'inputname' => 'q',
            'searchstring' => $searchstring,
        ];

        return $this->render_from_template('core/search_input_navbar', $data);
    }

    /**
     * Returns the activity header if any.
     *
     * @return string HTML with the activity header if generated.
     */
    public function activity_header() {
        $output = '';

        $activityheadercontext = $this->page->activityheader->export_for_template($this);
        if (!empty($activityheadercontext)) {
            $output = $this->render_from_template('core/activity_header', $activityheadercontext);
        }

        return $output;
    }
}
