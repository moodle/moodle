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
 * Layout - nav.
 * This layout is based on a Moodle site index.php file but has been adapted to show news items in a different
 * way.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use theme_snap\renderables\settings_link;
use theme_snap\renderables\genius_dashboard_link;

?>
<header id='mr-nav' class='clearfix moodle-has-zindex'>
<div id="snap-header">
<?php
// If the homepage is set to Dashboard, then the home icon link must redirect to dashboard.
$homepage = get_home_page();
if ($homepage === 1) {
    $defaulthomeurl = $CFG->wwwroot.'/my';
} else if ($homepage === 3) {
    $defaulthomeurl = $CFG->wwwroot.'/my/courses.php';
} else {
    $defaulthomeurl = $CFG->wwwroot;
}
$sitefullname = format_string($SITE->fullname);
$attrs = array(
    'id' => 'snap-home',
    'title' => $sitefullname,
);

if (!empty($PAGE->theme->settings->logo)) {
    $sitefullname = '<span class="sr-only">'.format_string($SITE->fullname). ' ' .get_string('homepage', 'theme_snap').'</span>';
    $attrs['class'] = 'logo';
}

echo html_writer::link($defaulthomeurl, $sitefullname, $attrs);
?>

<div class="float-end js-only row">
    <?php
    if (class_exists('local_geniusws\navigation')) {
        $bblink = new genius_dashboard_link();
        echo '<div id="genius_link_wrapper">';
        echo $OUTPUT->render($bblink);
        echo '</div>';
    }
    echo $OUTPUT->my_courses_nav_link();
    echo $OUTPUT->user_menu_nav_dropdown();
    echo $OUTPUT->render_notification_popups();

    echo '<span class="hidden-md-down">';
    echo $OUTPUT->search_box();
    echo '</span>';
    if ($this->page->user_allowed_editing()) {
        echo '<div class="snap_line_separator"></div>';
    }
    echo $OUTPUT->edit_switch();
    ?>
</div>
</div>
<?php
$custommenu = $OUTPUT->custom_menu();

/* Moodle custom menu. */
/* Hide it for the login index, login sign up and login forgot password pages. */
if (!empty($custommenu)) {
    if (!($PAGE->pagetype === 'login-index') &&
        !($PAGE->pagetype === 'login-signup') &&
        !($PAGE->pagetype === 'login-forgot_password')) {
        echo '<div id="snap-custom-menu-header">';
        echo $custommenu;
        echo '</div>';
    }
}
?>
</header>

<?php
// Only proceed with sidebar menu for logged-in users
if (isloggedin() && !isguestuser()) {
    if (!empty($CFG->messaging)) {
        $unreadcount = \core_message\api::count_unread_conversations($USER);
        $requestcount = \core_message\api::get_received_contact_requests_count($USER->id);
        $context = [
            'userid' => $USER->id,
            'unreadcount' => $unreadcount + $requestcount
        ];
        $messages_item = $OUTPUT->render_from_template('core_message/message_popover', $context);
    }
    $addblockbutton = $OUTPUT->addblockbutton();
    $blockshtml = $OUTPUT->blocks('side-pre');
    $settingslink = new settings_link();
    // Excluding settings block from blocks count
    $hasblocks = (strpos($blockshtml, 'data-block=') !== false && 
                 !(strpos($blockshtml, 'data-block="settings"') !== false && 
                   substr_count($blockshtml, 'data-block="') === 1)) || 
                 !empty($addblockbutton);
    // Define page types where blocks should be shown
    // Using patterns with exact matches and prefix matches
    $whitelistpagesforblocks = [
        'exact' => ['site-index', 'my-index'],
        'prefix' => ['course-view']
    ];
    
    $sidebarmenuitems = [];

    // Only add settings link if it has output
    if (!empty($settingslink->output)) {
        $sidebarmenuitems[] = [
            'customcontent' => $OUTPUT->render($settingslink),
            'dataattributes' => [
                ['name' => 'activeselector', 'value' => '#admin-menu-trigger.active']
            ]
        ];
    }
    
    // Check if current page type matches any whitelist pattern
    $pagematcheswhitelist = false;
    
    // Check exact matches
    if (in_array($PAGE->pagetype, $whitelistpagesforblocks['exact'])) {
        $pagematcheswhitelist = true;
    }
    
    // Check prefix matches if not already matched
    if (!$pagematcheswhitelist) {
        foreach ($whitelistpagesforblocks['prefix'] as $prefix) {
            if (strpos($PAGE->pagetype, $prefix) === 0) {
                $pagematcheswhitelist = true;
                break;
            }
        }
    }
    
    // Only add blocks drawer button if there are blocks and page type matches whitelist
    if ($hasblocks && $pagematcheswhitelist) {
        $sidebarmenuitems[] = [
            'title' => get_string('toggleblockdrawer', 'theme_snap'),
            'iconimg' => $OUTPUT->image_url('blocksdrawers', 'theme'),
            'isbutton' => true,
            'dataattributes' => [
                ['name' => 'toggler', 'value' => 'drawers'],
                ['name' => 'action', 'value' => 'toggle'],
                ['name' => 'target', 'value' => 'theme_snap-drawers-blocks'],
                ['name' => 'original-title', 'value' => get_string('toggleblockdrawer', 'theme_snap')],
                ['name' => 'placement', 'value' => 'right'],
                ['name' => 'activeselector', 'value' => '#theme_snap-drawers-blocks.show']
            ],
            'classes' => 'blocks-drawer-button'
        ];
    }

    // Only add feeds side menu trigger if it exists
    $feedsTrigger = $OUTPUT->snap_feeds_side_menu_trigger();
    if (!empty($feedsTrigger)) {
        $sidebarmenuitems[] = [
            'customcontent' => $feedsTrigger,
            'dataattributes' => [
                ['name' => 'activeselector', 'value' => '#snap_feeds_side_menu_trigger.active']
            ]
        ];
    }

    // Only add messages item if messaging is enabled
    if (!empty($messages_item)) {
        $sidebarmenuitems[] = [
            'customcontent' => $messages_item,
            'dataattributes' => [
                ['name' => 'activeselector', 'value' => '[data-region="popover-region-messages"]:not(.collapsed)']
            ]
        ];
    }

    // Only render the sidebar menu if there are items to display
    if (!empty($sidebarmenuitems)) {
        $opensidebar = true; // Opened by default
        $iscoursepage = strpos($PAGE->pagetype, 'course-view') === 0;

        echo $OUTPUT->render_from_template('theme_snap/sidebar_menu', [
            'menuitems' => $sidebarmenuitems,
            'opensidebar' => $opensidebar,
            'iscoursepage' => $iscoursepage
        ]);
    }
}

