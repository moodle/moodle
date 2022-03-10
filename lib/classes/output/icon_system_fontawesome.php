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
 * Contains class \core\output\icon_system
 *
 * @package    core
 * @category   output
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\output;

use renderer_base;
use pix_icon;

defined('MOODLE_INTERNAL') || die();

/**
 * Class allowing different systems for mapping and rendering icons.
 *
 * Possible icon styles are:
 *   1. standard - image tags are generated which point to pix icons stored in a plugin pix folder.
 *   2. fontawesome - font awesome markup is generated with the name of the icon mapped from the moodle icon name.
 *   3. inline - inline tags are used for svg and png so no separate page requests are made (at the expense of page size).
 *
 * @package    core
 * @category   output
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class icon_system_fontawesome extends icon_system_font {

    /**
     * @var array $map Cached map of moodle icon names to font awesome icon names.
     */
    private $map = [];

    public function get_core_icon_map() {
        return [
            'core:docs' => 'fa-info-circle',
            'core:book' => 'fa-book',
            'core:help' => 'fa-question-circle text-info',
            'core:req' => 'fa-exclamation-circle text-danger',
            'core:a/add_file' => 'fa-file-o',
            'core:a/create_folder' => 'fa-folder-o',
            'core:a/download_all' => 'fa-download',
            'core:a/help' => 'fa-question-circle text-info',
            'core:a/logout' => 'fa-sign-out',
            'core:a/refresh' => 'fa-refresh',
            'core:a/search' => 'fa-search',
            'core:a/setting' => 'fa-cog',
            'core:a/view_icon_active' => 'fa-th',
            'core:a/view_list_active' => 'fa-list',
            'core:a/view_tree_active' => 'fa-folder',
            'core:b/bookmark-new' => 'fa-bookmark',
            'core:b/document-edit' => 'fa-pencil',
            'core:b/document-new' => 'fa-file-o',
            'core:b/document-properties' => 'fa-info',
            'core:b/edit-copy' => 'fa-files-o',
            'core:b/edit-delete' => 'fa-trash',
            'core:e/abbr' => 'fa-comment',
            'core:e/absolute' => 'fa-crosshairs',
            'core:e/accessibility_checker' => 'fa-universal-access',
            'core:e/acronym' => 'fa-comment',
            'core:e/advance_hr' => 'fa-arrows-h',
            'core:e/align_center' => 'fa-align-center',
            'core:e/align_left' => 'fa-align-left',
            'core:e/align_right' => 'fa-align-right',
            'core:e/anchor' => 'fa-chain',
            'core:e/backward' => 'fa-undo',
            'core:e/bold' => 'fa-bold',
            'core:e/bullet_list' => 'fa-list-ul',
            'core:e/cancel' => 'fa-times',
            'core:e/cancel_solid_circle' => 'fas fa-times-circle',
            'core:e/cell_props' => 'fa-info',
            'core:e/cite' => 'fa-quote-right',
            'core:e/cleanup_messy_code' => 'fa-eraser',
            'core:e/clear_formatting' => 'fa-i-cursor',
            'core:e/copy' => 'fa-clone',
            'core:e/cut' => 'fa-scissors',
            'core:e/decrease_indent' => 'fa-outdent',
            'core:e/delete_col' => 'fa-minus',
            'core:e/delete_row' => 'fa-minus',
            'core:e/delete' => 'fa-minus',
            'core:e/delete_table' => 'fa-minus',
            'core:e/document_properties' => 'fa-info',
            'core:e/emoticons' => 'fa-smile-o',
            'core:e/find_replace' => 'fa-search-plus',
            'core:e/file-text' => 'fa-file-text',
            'core:e/forward' => 'fa-arrow-right',
            'core:e/fullpage' => 'fa-arrows-alt',
            'core:e/fullscreen' => 'fa-arrows-alt',
            'core:e/help' => 'fa-question-circle',
            'core:e/increase_indent' => 'fa-indent',
            'core:e/insert_col_after' => 'fa-columns',
            'core:e/insert_col_before' => 'fa-columns',
            'core:e/insert_date' => 'fa-calendar',
            'core:e/insert_edit_image' => 'fa-picture-o',
            'core:e/insert_edit_link' => 'fa-link',
            'core:e/insert_edit_video' => 'fa-file-video-o',
            'core:e/insert_file' => 'fa-file',
            'core:e/insert_horizontal_ruler' => 'fa-arrows-h',
            'core:e/insert_nonbreaking_space' => 'fa-square-o',
            'core:e/insert_page_break' => 'fa-level-down',
            'core:e/insert_row_after' => 'fa-plus',
            'core:e/insert_row_before' => 'fa-plus',
            'core:e/insert' => 'fa-plus',
            'core:e/insert_time' => 'fa-clock-o',
            'core:e/italic' => 'fa-italic',
            'core:e/justify' => 'fa-align-justify',
            'core:e/layers_over' => 'fa-level-up',
            'core:e/layers' => 'fa-window-restore',
            'core:e/layers_under' => 'fa-level-down',
            'core:e/left_to_right' => 'fa-chevron-right',
            'core:e/manage_files' => 'fa-files-o',
            'core:e/math' => 'fa-calculator',
            'core:e/merge_cells' => 'fa-compress',
            'core:e/new_document' => 'fa-file-o',
            'core:e/numbered_list' => 'fa-list-ol',
            'core:e/page_break' => 'fa-level-down',
            'core:e/paste' => 'fa-clipboard',
            'core:e/paste_text' => 'fa-clipboard',
            'core:e/paste_word' => 'fa-clipboard',
            'core:e/prevent_autolink' => 'fa-exclamation',
            'core:e/preview' => 'fa-search-plus',
            'core:e/print' => 'fa-print',
            'core:e/question' => 'fa-question',
            'core:e/redo' => 'fa-repeat',
            'core:e/remove_link' => 'fa-chain-broken',
            'core:e/remove_page_break' => 'fa-remove',
            'core:e/resize' => 'fa-expand',
            'core:e/restore_draft' => 'fa-undo',
            'core:e/restore_last_draft' => 'fa-undo',
            'core:e/right_to_left' => 'fa-chevron-left',
            'core:e/row_props' => 'fa-info',
            'core:e/save' => 'fa-floppy-o',
            'core:e/screenreader_helper' => 'fa-braille',
            'core:e/search' => 'fa-search',
            'core:e/select_all' => 'fa-arrows-h',
            'core:e/show_invisible_characters' => 'fa-eye-slash',
            'core:e/source_code' => 'fa-code',
            'core:e/special_character' => 'fa-pencil-square-o',
            'core:e/spellcheck' => 'fa-check',
            'core:e/split_cells' => 'fa-columns',
            'core:e/strikethrough' => 'fa-strikethrough',
            'core:e/styleparagraph' => 'fa-font',
            'core:e/subscript' => 'fa-subscript',
            'core:e/superscript' => 'fa-superscript',
            'core:e/table_props' => 'fa-table',
            'core:e/table' => 'fa-table',
            'core:e/template' => 'fa-sticky-note',
            'core:e/text_color_picker' => 'fa-paint-brush',
            'core:e/text_color' => 'fa-paint-brush',
            'core:e/text_highlight_picker' => 'fa-lightbulb-o',
            'core:e/text_highlight' => 'fa-lightbulb-o',
            'core:e/tick' => 'fa-check',
            'core:e/toggle_blockquote' => 'fa-quote-left',
            'core:e/underline' => 'fa-underline',
            'core:e/undo' => 'fa-undo',
            'core:e/visual_aid' => 'fa-universal-access',
            'core:e/visual_blocks' => 'fa-audio-description',
            'theme:fp/add_file' => 'fa-file-o',
            'theme:fp/alias' => 'fa-share',
            'theme:fp/alias_sm' => 'fa-share',
            'theme:fp/check' => 'fa-check',
            'theme:fp/create_folder' => 'fa-folder-o',
            'theme:fp/cross' => 'fa-remove',
            'theme:fp/download_all' => 'fa-download',
            'theme:fp/help' => 'fa-question-circle',
            'theme:fp/link' => 'fa-link',
            'theme:fp/link_sm' => 'fa-link',
            'theme:fp/logout' => 'fa-sign-out',
            'theme:fp/path_folder' => 'fa-folder',
            'theme:fp/path_folder_rtl' => 'fa-folder',
            'theme:fp/refresh' => 'fa-refresh',
            'theme:fp/search' => 'fa-search',
            'theme:fp/setting' => 'fa-cog',
            'theme:fp/view_icon_active' => 'fa-th',
            'theme:fp/view_list_active' => 'fa-list',
            'theme:fp/view_tree_active' => 'fa-folder',
            'core:i/addblock' => 'fa-plus-square',
            'core:i/assignroles' => 'fa-user-plus',
            'core:i/backup' => 'fa-file-zip-o',
            'core:i/badge' => 'fa-shield',
            'core:i/breadcrumbdivider' => 'fa-angle-right',
            'core:i/bullhorn' => 'fa-bullhorn',
            'core:i/calc' => 'fa-calculator',
            'core:i/calendar' => 'fa-calendar',
            'core:i/calendareventdescription' => 'fa-align-left',
            'core:i/calendareventtime' => 'fa-clock-o',
            'core:i/caution' => 'fa-exclamation text-warning',
            'core:i/checked' => 'fa-check',
            'core:i/checkedcircle' => 'fa-check-circle',
            'core:i/checkpermissions' => 'fa-unlock-alt',
            'core:i/cohort' => 'fa-users',
            'core:i/competencies' => 'fa-check-square-o',
            'core:i/completion_self' => 'fa-user-o',
            'core:i/contentbank' => 'fa-paint-brush',
            'core:i/dashboard' => 'fa-tachometer',
            'core:i/categoryevent' => 'fa-cubes',
            'core:i/course' => 'fa-graduation-cap',
            'core:i/courseevent' => 'fa-graduation-cap',
            'core:i/customfield' => 'fa-hand-o-right',
            'core:i/db' => 'fa-database',
            'core:i/delete' => 'fa-trash',
            'core:i/down' => 'fa-arrow-down',
            'core:i/dragdrop' => 'fa-arrows',
            'core:i/duration' => 'fa-clock-o',
            'core:i/emojicategoryactivities' => 'fa-futbol-o',
            'core:i/emojicategoryanimalsnature' => 'fa-leaf',
            'core:i/emojicategoryflags' => 'fa-flag',
            'core:i/emojicategoryfooddrink' => 'fa-cutlery',
            'core:i/emojicategoryobjects' => 'fa-lightbulb-o',
            'core:i/emojicategorypeoplebody' => 'fa-male',
            'core:i/emojicategoryrecent' => 'fa-clock-o',
            'core:i/emojicategorysmileysemotion' => 'fa-smile-o',
            'core:i/emojicategorysymbols' => 'fa-heart',
            'core:i/emojicategorytravelplaces' => 'fa-plane',
            'core:i/edit' => 'fa-pencil',
            'core:i/email' => 'fa-envelope',
            'core:i/empty' => 'fa-fw',
            'core:i/enrolmentsuspended' => 'fa-pause',
            'core:i/enrolusers' => 'fa-user-plus',
            'core:i/expired' => 'fa-exclamation text-warning',
            'core:i/export' => 'fa-download',
            'core:i/externallink' => 'fa-external-link',
            'core:i/files' => 'fa-file',
            'core:i/filter' => 'fa-filter',
            'core:i/flagged' => 'fa-flag',
            'core:i/folder' => 'fa-folder',
            'core:i/grade_correct' => 'fa-check text-success',
            'core:i/grade_incorrect' => 'fa-remove text-danger',
            'core:i/grade_partiallycorrect' => 'fa-check-square',
            'core:i/grades' => 'fa-table',
            'core:i/grading' => 'fa-magic',
            'core:i/gradingnotifications' => 'fa-bell-o',
            'core:i/groupevent' => 'fa-group',
            'core:i/groupn' => 'fa-user',
            'core:i/group' => 'fa-users',
            'core:i/groups' => 'fa-user-circle',
            'core:i/groupv' => 'fa-user-circle-o',
            'core:i/home' => 'fa-home',
            'core:i/hide' => 'fa-eye',
            'core:i/hierarchylock' => 'fa-lock',
            'core:i/import' => 'fa-level-up',
            'core:i/incorrect' => 'fa-exclamation',
            'core:i/info' => 'fa-info',
            'core:i/invalid' => 'fa-times text-danger',
            'core:i/item' => 'fa-circle',
            'core:i/loading' => 'fa-circle-o-notch fa-spin',
            'core:i/loading_small' => 'fa-circle-o-notch fa-spin',
            'core:i/location' => 'fa-map-marker',
            'core:i/lock' => 'fa-lock',
            'core:i/log' => 'fa-list-alt',
            'core:i/mahara_host' => 'fa-id-badge',
            'core:i/manual_item' => 'fa-square-o',
            'core:i/marked' => 'fa-circle',
            'core:i/marker' => 'fa-circle-o',
            'core:i/mean' => 'fa-calculator',
            'core:i/menu' => 'fa-ellipsis-v',
            'core:i/menubars' => 'fa-bars',
            'core:i/messagecontentaudio' => 'fa-headphones',
            'core:i/messagecontentimage' => 'fa-image',
            'core:i/messagecontentvideo' => 'fa-film',
            'core:i/messagecontentmultimediageneral' => 'fa-file-video-o',
            'core:i/mnethost' => 'fa-external-link',
            'core:i/moodle_host' => 'fa-graduation-cap',
            'core:i/moremenu' => 'fa-ellipsis-h',
            'core:i/move_2d' => 'fa-arrows',
            'core:i/muted' => 'fa-microphone-slash',
            'core:i/navigationitem' => 'fa-fw',
            'core:i/ne_red_mark' => 'fa-remove',
            'core:i/new' => 'fa-bolt',
            'core:i/news' => 'fa-newspaper-o',
            'core:i/next' => 'fa-chevron-right',
            'core:i/nosubcat' => 'fa-plus-square-o',
            'core:i/notifications' => 'fa-bell-o',
            'core:i/open' => 'fa-folder-open',
            'core:i/otherevent' => 'fa-calendar',
            'core:i/outcomes' => 'fa-tasks',
            'core:i/payment' => 'fa-money',
            'core:i/permissionlock' => 'fa-lock',
            'core:i/permissions' => 'fa-pencil-square-o',
            'core:i/persona_sign_in_black' => 'fa-male',
            'core:i/portfolio' => 'fa-id-badge',
            'core:i/preview' => 'fa-search-plus',
            'core:i/previous' => 'fa-chevron-left',
            'core:i/privatefiles' => 'fa-file-o',
            'core:i/progressbar' => 'fa-spinner fa-spin',
            'core:i/publish' => 'fa-share',
            'core:i/questions' => 'fa-question',
            'core:i/reload' => 'fa-refresh',
            'core:i/report' => 'fa-area-chart',
            'core:i/repository' => 'fa-hdd-o',
            'core:i/restore' => 'fa-level-up',
            'core:i/return' => 'fa-arrow-left',
            'core:i/risk_config' => 'fa-exclamation text-muted',
            'core:i/risk_managetrust' => 'fa-exclamation-triangle text-warning',
            'core:i/risk_personal' => 'fa-exclamation-circle text-info',
            'core:i/risk_spam' => 'fa-exclamation text-primary',
            'core:i/risk_xss' => 'fa-exclamation-triangle text-danger',
            'core:i/role' => 'fa-user-md',
            'core:i/rss' => 'fa-rss',
            'core:i/rsssitelogo' => 'fa-graduation-cap',
            'core:i/scales' => 'fa-balance-scale',
            'core:i/scheduled' => 'fa-calendar-check-o',
            'core:i/search' => 'fa-search',
            'core:i/section' => 'fa-folder-o',
            'core:i/sendmessage' => 'fa-paper-plane',
            'core:i/settings' => 'fa-cog',
            'core:i/show' => 'fa-eye-slash',
            'core:i/siteevent' => 'fa-globe',
            'core:i/star' => 'fa-star',
            'core:i/star-o' => 'fa-star-o',
            'core:i/star-rating' => 'fa-star',
            'core:i/stats' => 'fa-line-chart',
            'core:i/switch' => 'fa-exchange',
            'core:i/switchrole' => 'fa-user-secret',
            'core:i/trash' => 'fa-trash',
            'core:i/twoway' => 'fa-arrows-h',
            'core:i/unchecked' => 'fa-square-o',
            'core:i/uncheckedcircle' => 'fa-circle-o',
            'core:i/unflagged' => 'fa-flag-o',
            'core:i/unlock' => 'fa-unlock',
            'core:i/up' => 'fa-arrow-up',
            'core:i/upload' => 'fa-upload',
            'core:i/userevent' => 'fa-user',
            'core:i/user' => 'fa-user',
            'core:i/users' => 'fa-users',
            'core:i/valid' => 'fa-check text-success',
            'core:i/warning' => 'fa-exclamation text-warning',
            'core:i/window_close' => 'fa-window-close',
            'core:i/withsubcat' => 'fa-plus-square',
            'core:i/language' => 'fa-language',
            'core:m/USD' => 'fa-usd',
            'core:t/addcontact' => 'fa-address-card',
            'core:t/add' => 'fa-plus',
            'core:t/approve' => 'fa-thumbs-up',
            'core:t/assignroles' => 'fa-user-circle',
            'core:t/award' => 'fa-trophy',
            'core:t/backpack' => 'fa-shopping-bag',
            'core:t/backup' => 'fa-arrow-circle-down',
            'core:t/block' => 'fa-ban',
            'core:t/block_to_dock_rtl' => 'fa-chevron-right',
            'core:t/block_to_dock' => 'fa-chevron-left',
            'core:t/blocks_drawer' => 'fa-chevron-left',
            'core:t/blocks_drawer_rtl' => 'fa-chevron-right',
            'core:t/calc_off' => 'fa-calculator', // TODO: Change to better icon once we have stacked icon support or more icons.
            'core:t/calc' => 'fa-calculator',
            'core:t/check' => 'fa-check',
            'core:t/clipboard' => 'fa-clipboard',
            'core:t/cohort' => 'fa-users',
            'core:t/collapsed_empty_rtl' => 'fa-caret-square-o-left',
            'core:t/collapsed_empty' => 'fa-caret-square-o-right',
            'core:t/collapsed_rtl' => 'fa-caret-left',
            'core:t/collapsed' => 'fa-caret-right',
            'core:t/collapsedcaret' => 'fa-caret-right',
            'core:t/collapsedchevron' => 'fa-chevron-right',
            'core:t/collapsedchevron_rtl' => 'fa-chevron-left',
            'core:t/completion_complete' => 'fa-circle',
            'core:t/completion_fail' => 'fa-times',
            'core:t/completion_incomplete' => 'fa-circle-thin',
            'core:t/contextmenu' => 'fa-cog',
            'core:t/copy' => 'fa-copy',
            'core:t/delete' => 'fa-trash',
            'core:t/dockclose' => 'fa-window-close',
            'core:t/dock_to_block_rtl' => 'fa-chevron-right',
            'core:t/dock_to_block' => 'fa-chevron-left',
            'core:t/download' => 'fa-download',
            'core:t/down' => 'fa-arrow-down',
            'core:t/downlong' => 'fa-long-arrow-down',
            'core:t/dropdown' => 'fa-cog',
            'core:t/editinline' => 'fa-pencil',
            'core:t/edit_menu' => 'fa-cog',
            'core:t/editstring' => 'fa-pencil',
            'core:t/edit' => 'fa-cog',
            'core:t/emailno' => 'fa-ban',
            'core:t/email' => 'fa-envelope-o',
            'core:t/emptystar' => 'fa-star-o',
            'core:t/enrolusers' => 'fa-user-plus',
            'core:t/expanded' => 'fa-caret-down',
            'core:t/expandedchevron' => 'fa-chevron-down',
            'core:t/go' => 'fa-play',
            'core:t/grades' => 'fa-table',
            'core:t/groupn' => 'fa-user',
            'core:t/groups' => 'fa-user-circle',
            'core:t/groupv' => 'fa-user-circle-o',
            'core:t/hide' => 'fa-eye',
            'core:t/index_drawer' => 'fa-list',
            'core:t/left' => 'fa-arrow-left',
            'core:t/less' => 'fa-caret-up',
            'core:t/life-ring' => 'fa-life-ring',
            'core:t/locked' => 'fa-lock',
            'core:t/lock' => 'fa-unlock',
            'core:t/locktime' => 'fa-lock',
            'core:t/markasread' => 'fa-check',
            'core:t/messages' => 'fa-comments',
            'core:t/message' => 'fa-comment-o',
            'core:t/more' => 'fa-caret-down',
            'core:t/move' => 'fa-arrows-v',
            'core:t/online' => 'fa-circle',
            'core:t/passwordunmask-edit' => 'fa-pencil',
            'core:t/passwordunmask-reveal' => 'fa-eye',
            'core:t/play' => 'fa-play',
            'core:t/portfolioadd' => 'fa-plus',
            'core:t/preferences' => 'fa-wrench',
            'core:t/preview' => 'fa-search-plus',
            'core:t/print' => 'fa-print',
            'core:t/removecontact' => 'fa-user-times',
            'core:t/reload' => 'fa-refresh',
            'core:t/reset' => 'fa-repeat',
            'core:t/restore' => 'fa-arrow-circle-up',
            'core:t/right' => 'fa-arrow-right',
            'core:t/sendmessage' => 'fa-paper-plane',
            'core:t/show' => 'fa-eye-slash',
            'core:t/sort_by' => 'fa-sort-amount-asc',
            'core:t/sort_asc' => 'fa-sort-asc',
            'core:t/sort_desc' => 'fa-sort-desc',
            'core:t/sort' => 'fa-sort',
            'core:t/stop' => 'fa-stop',
            'core:t/switch_minus' => 'fa-minus',
            'core:t/switch_plus' => 'fa-plus',
            'core:t/switch_whole' => 'fa-square-o',
            'core:t/tags' => 'fa-tags',
            'core:t/unblock' => 'fa-commenting',
            'core:t/unlocked' => 'fa-unlock-alt',
            'core:t/unlock' => 'fa-lock',
            'core:t/up' => 'fa-arrow-up',
            'core:t/uplong' => 'fa-long-arrow-up',
            'core:t/user' => 'fa-user',
            'core:t/viewdetails' => 'fa-list',
        ];
    }

    /**
     * Overridable function to get a mapping of all icons.
     * Default is to do no mapping.
     */
    public function get_icon_name_map() {
        if ($this->map === []) {
            $cache = \cache::make('core', 'fontawesomeiconmapping');

            // Create different mapping keys for different icon system classes, there may be several different
            // themes on the same site.
            $mapkey = 'mapping_'.preg_replace('/[^a-zA-Z0-9_]/', '_', get_class($this));
            $this->map = $cache->get($mapkey);

            if (empty($this->map)) {
                $this->map = $this->get_core_icon_map();
                $callback = 'get_fontawesome_icon_map';

                if ($pluginsfunction = get_plugins_with_function($callback)) {
                    foreach ($pluginsfunction as $plugintype => $plugins) {
                        foreach ($plugins as $pluginfunction) {
                            $pluginmap = $pluginfunction();
                            $this->map += $pluginmap;
                        }
                    }
                }
                $cache->set($mapkey, $this->map);
            }

        }
        return $this->map;
    }


    public function get_amd_name() {
        return 'core/icon_system_fontawesome';
    }

    public function render_pix_icon(renderer_base $output, pix_icon $icon) {
        $subtype = 'pix_icon_fontawesome';
        $subpix = new $subtype($icon);

        $data = $subpix->export_for_template($output);

        if (!$subpix->is_mapped()) {
            $data['unmappedIcon'] = $icon->export_for_template($output);
        }
        if (isset($icon->attributes['aria-hidden'])) {
            $data['aria-hidden'] = $icon->attributes['aria-hidden'];
        }
        return $output->render_from_template('core/pix_icon_fontawesome', $data);
    }

}
