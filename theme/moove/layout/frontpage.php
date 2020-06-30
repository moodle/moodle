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
 * Frontpage layout for the moove theme.
 *
 * @package   theme_moove
 * @copyright 2017 Willian Mano - http://conecti.me
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

user_preference_allow_ajax_update('drawer-open-nav', PARAM_ALPHA);
user_preference_allow_ajax_update('sidepre-open', PARAM_ALPHA);

require_once($CFG->libdir . '/behat/lib.php');

$extraclasses = [];

$themesettings = new \theme_moove\util\theme_settings();

if (isloggedin()) {
    $blockshtml = $OUTPUT->blocks('side-pre');
    $hasblocks = strpos($blockshtml, 'data-block=') !== false;

    $navdraweropen = (get_user_preferences('drawer-open-nav', 'true') == 'true');
    $draweropenright = (get_user_preferences('sidepre-open', 'true') == 'true');

    if ($navdraweropen) {
        $extraclasses[] = 'drawer-open-left';
    }

    if ($draweropenright && $hasblocks) {
        $extraclasses[] = 'drawer-open-right';
    }

    $bodyattributes = $OUTPUT->body_attributes($extraclasses);
    $regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();
    $templatecontext = [
        'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
        'output' => $OUTPUT,
        'sidepreblocks' => $blockshtml,
        'hasblocks' => $hasblocks,
        'bodyattributes' => $bodyattributes,
        'hasdrawertoggle' => true,
        'navdraweropen' => $navdraweropen,
        'draweropenright' => $draweropenright,
        'regionmainsettingsmenu' => $regionmainsettingsmenu,
        'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu)
    ];

    // Improve boost navigation.
    theme_moove_extend_flat_navigation($PAGE->flatnav);

    $templatecontext['flatnavigation'] = $PAGE->flatnav;

    $templatecontext = array_merge($templatecontext, $themesettings->footer_items(), $themesettings->slideshow());

    echo $OUTPUT->render_from_template('theme_moove/frontpage', $templatecontext);
} else {
    $sliderfrontpage = false;
    if ((theme_moove_get_setting('sliderenabled', true) == true) && (theme_moove_get_setting('sliderfrontpage', true) == true)) {
        $sliderfrontpage = true;
        $extraclasses[] = 'slideshow';
    }

    $numbersfrontpage = false;
    if (theme_moove_get_setting('numbersfrontpage', true) == true) {
        $numbersfrontpage = true;
    }

    $sponsorsfrontpage = false;
    if (theme_moove_get_setting('sponsorsfrontpage', true) == true) {
        $sponsorsfrontpage = true;
    }

    $clientsfrontpage = false;
    if (theme_moove_get_setting('clientsfrontpage', true) == true) {
        $clientsfrontpage = true;
    }

    $bannerheading = '';
    if (!empty($PAGE->theme->settings->bannerheading)) {
        $bannerheading = theme_moove_get_setting('bannerheading', true);
    }

    $bannercontent = '';
    if (!empty($PAGE->theme->settings->bannercontent)) {
        $bannercontent = theme_moove_get_setting('bannercontent', true);
    }

    $shoulddisplaymarketing = false;
    if (theme_moove_get_setting('displaymarketingbox', true) == true) {
        $shoulddisplaymarketing = true;
    }

    $disablefrontpageloginbox = false;
    if (theme_moove_get_setting('disablefrontpageloginbox', true) == true) {
        $disablefrontpageloginbox = true;
        $extraclasses[] = 'disablefrontpageloginbox';
    }

    $bodyattributes = $OUTPUT->body_attributes($extraclasses);
    $regionmainsettingsmenu = $OUTPUT->region_main_settings_menu();

    $templatecontext = [
        'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
        'output' => $OUTPUT,
        'bodyattributes' => $bodyattributes,
        'hasdrawertoggle' => false,
        'canloginasguest' => $CFG->guestloginbutton and !isguestuser(),
        'cansignup' => $CFG->registerauth == 'email' || !empty($CFG->registerauth),
        'bannerheading' => $bannerheading,
        'bannercontent' => $bannercontent,
        'shoulddisplaymarketing' => $shoulddisplaymarketing,
        'sliderfrontpage' => $sliderfrontpage,
        'numbersfrontpage' => $numbersfrontpage,
        'sponsorsfrontpage' => $sponsorsfrontpage,
        'clientsfrontpage' => $clientsfrontpage,
        'disablefrontpageloginbox' => $disablefrontpageloginbox,
        'logintoken' => \core\session\manager::get_login_token()
    ];

    $templatecontext = array_merge($templatecontext, $themesettings->footer_items(), $themesettings->marketing_items());

    if ($sliderfrontpage) {
        $templatecontext = array_merge($templatecontext, $themesettings->slideshow());
    }

    if ($numbersfrontpage) {
        $templatecontext = array_merge($templatecontext, $themesettings->numbers());
    }

    if ($sponsorsfrontpage) {
        $templatecontext = array_merge($templatecontext, $themesettings->sponsors());
    }

    if ($clientsfrontpage) {
        $templatecontext = array_merge($templatecontext, $themesettings->clients());
    }

    echo $OUTPUT->render_from_template('theme_moove/frontpage_guest', $templatecontext);
}
