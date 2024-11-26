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
 * Adaptable theme.
 *
 * @package    theme_adaptable
 * @copyright  2023 G J Barnard
 * @author     G J Barnard -
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
namespace theme_adaptable\output;

use core\output\pix_icon;

/**
 * Font Awesome icon system.
 */
class icon_system_fontawesome extends \core\output\icon_system_fontawesome {
    /**
     * @var array $map Cached map of Moodle icon names to Font Awesome icon names.
     */
    private $map = [];

    /**
     * @var int $fav Using FontAwesome from core or our version of 6 - 0 or 2 values.
     */
    private $fav;

    /**
     * Constructor
     */
    protected function __construct() {
        // Has to be this for the external AJAX calls that don't know what theme is set.
        $this->fav = \theme_adaptable\toolbox::get_config_setting('fav');
    }

    /**
     * Return the icon map.
     *
     * @return array the map.
     */
    public function get_core_icon_map() {
        if (empty($this->fav)) {
            $map = parent::get_core_icon_map();
            $map['core:i/navigationitem'] = 'fa-compass';  // Core has 'fa-fw'!
            return $map;
        }

        return [
            'core:book' => 'fas fa-book',
            'core:docs' => 'fas fa-info-circle',
            'core:help' => 'fas fa-question-circle text-info',
            'core:req' => 'fas fa-exclamation-circle text-danger',
            'core:a/add_file' => 'far fa-file',
            'core:a/create_folder' => 'far fa-folder',
            'core:a/download_all' => 'fas fa-download',
            'core:a/help' => 'fas fa-question-circle text-info',
            'core:a/logout' => 'fas fa-sign-out-alt',
            'core:a/refresh' => 'fas fa-sync',
            'core:a/search' => 'fas fa-search',
            'core:a/setting' => 'fas fa-cog',
            'core:a/view_icon_active' => 'fas fa-th',
            'core:a/view_list_active' => 'fas fa-list',
            'core:a/view_tree_active' => 'fas fa-folder',
            'core:b/bookmark-new' => 'fas fa-bookmark',
            'core:b/document-edit' => 'fas fa-pencil-alt',
            'core:b/document-new' => 'far fa-file',
            'core:b/document-properties' => 'fas fa-info',
            'core:b/edit-copy' => 'far fa-file',
            'core:b/edit-delete' => 'fas fa-trash',
            'core:e/abbr' => 'fas fa-comment',
            'core:e/absolute' => 'fas fa-crosshairs',
            'core:e/accessibility_checker' => 'fas fa-universal-access',
            'core:e/acronym' => 'fas fa-comment',
            'core:e/advance_hr' => 'fas fa-arrows-alt-h',
            'core:e/align_center' => 'fas fa-align-center',
            'core:e/align_left' => 'fas fa-align-left',
            'core:e/align_right' => 'fas fa-align-right',
            'core:e/anchor' => 'fas fa-link',
            'core:e/backward' => 'fas fa-undo',
            'core:e/bold' => 'fas fa-bold',
            'core:e/bullet_list' => 'fas fa-list-ul',
            'core:e/cancel' => 'fas fa-times',
            'core:e/cancel_solid_circle' => 'fas fa-times-circle',
            'core:e/cell_props' => 'fas fa-info',
            'core:e/cite' => 'fas fa-quote-right',
            'core:e/cleanup_messy_code' => 'fas fa-eraser',
            'core:e/clear_formatting' => 'fas fa-i-cursor',
            'core:e/copy' => 'fas fa-clone',
            'core:e/cut' => 'fas fa-cut',
            'core:e/decrease_indent' => 'fas fa-outdent',
            'core:e/delete_col' => 'fas fa-minus',
            'core:e/delete_row' => 'fas fa-minus',
            'core:e/delete' => 'fas fa-minus',
            'core:e/delete_table' => 'fas fa-minus',
            'core:e/document_properties' => 'fas fa-info',
            'core:e/emoticons' => 'far fa-smile',
            'core:e/find_replace' => 'fas fa-search-plus',
            'core:e/file-text' => 'fas fa-file-alt',
            'core:e/forward' => 'fas fa-arrow-right',
            'core:e/fullpage' => 'fas fa-expand-arrows-alt',
            'core:e/fullscreen' => 'fas fa-expand-arrows-alt',
            'core:e/help' => 'fas fa-question-circle',
            'core:e/increase_indent' => 'fas fa-indent',
            'core:e/insert_col_after' => 'fas fa-columns',
            'core:e/insert_col_before' => 'fas fa-columns',
            'core:e/insert_date' => 'fas fa-calendar-alt',
            'core:e/insert_edit_image' => 'fas fa-image',
            'core:e/insert_edit_link' => 'fas fa-link',
            'core:e/insert_edit_video' => 'fas fa-video',
            'core:e/insert_file' => 'fas fa-file',
            'core:e/insert_horizontal_ruler' => 'fas fa-arrows-alt-h',
            'core:e/insert_nonbreaking_space' => 'far fa-square',
            'core:e/insert_page_break' => 'fas fa-level-down-alt',
            'core:e/insert_row_after' => 'fas fa-plus',
            'core:e/insert_row_before' => 'fas fa-plus',
            'core:e/insert' => 'fas fa-plus',
            'core:e/insert_time' => 'far fa-clock',
            'core:e/italic' => 'fas fa-italic',
            'core:e/justify' => 'fas fa-align-justify',
            'core:e/layers_over' => 'fas fa-level-up-alt',
            'core:e/layers' => 'fas fa-window-restore',
            'core:e/layers_under' => 'fas fa-level-down-alt',
            'core:e/left_to_right' => 'fas fa-chevron-right',
            'core:e/manage_files' => 'far fa-copy',
            'core:e/math' => 'fas fa-calculator',
            'core:e/merge_cells' => 'fas fa-compress',
            'core:e/new_document' => 'far fa-file',
            'core:e/numbered_list' => 'fas fa-list-ol',
            'core:e/page_break' => 'fas fa-level-down-alt',
            'core:e/paste' => 'far fa-clipboard',
            'core:e/paste_text' => 'far fa-clipboard',
            'core:e/paste_word' => 'far fa-clipboard',
            'core:e/prevent_autolink' => 'fas fa-exclamation',
            'core:e/preview' => 'fas fa-search-plus',
            'core:e/print' => 'fas fa-print',
            'core:e/question' => 'fas fa-question',
            'core:e/redo' => 'fas fa-redo',
            'core:e/remove_link' => 'fas fa-unlink',
            'core:e/remove_page_break' => 'fas fa-times',
            'core:e/resize' => 'fas fa-expand',
            'core:e/restore_draft' => 'fas fa-undo',
            'core:e/restore_last_draft' => 'fas fa-undo',
            'core:e/right_to_left' => 'fas fa-chevron-left',
            'core:e/row_props' => 'fas fa-info',
            'core:e/save' => 'far fa-save',
            'core:e/screenreader_helper' => 'fas fa-braille',
            'core:e/search' => 'fas fa-search',
            'core:e/select_all' => 'fas fa-arrows-alt-h',
            'core:e/show_invisible_characters' => 'fas fa-eye-slash',
            'core:e/source_code' => 'fas fa-code',
            'core:e/special_character' => 'fas fa-pen-square',
            'core:e/spellcheck' => 'fas fa-check',
            'core:e/split_cells' => 'fas fa-columns',
            'core:e/strikethrough' => 'fas fa-strikethrough',
            'core:e/styleparagraph' => 'fas fa-font',
            'core:e/subscript' => 'fas fa-subscript',
            'core:e/superscript' => 'fas fa-superscript',
            'core:e/table_props' => 'fas fa-table',
            'core:e/table' => 'fas fa-table',
            'core:e/template' => 'fas fa-sticky-note',
            'core:e/text_color_picker' => 'fas fa-paint-brush',
            'core:e/text_color' => 'fas fa-paint-brush',
            'core:e/text_highlight_picker' => 'far fa-lightbulb',
            'core:e/text_highlight' => 'far fa-lightbulb',
            'core:e/tick' => 'fas fa-check',
            'core:e/toggle_blockquote' => 'fas fa-quote-left',
            'core:e/underline' => 'fas fa-underline',
            'core:e/undo' => 'fas fa-undo',
            'core:e/visual_aid' => 'fas fa-universal-access',
            'core:e/visual_blocks' => 'fas fa-audio-description',
            'theme:fp/add_file' => 'far fa-file',
            'theme:fp/alias' => 'fas fa-share',
            'theme:fp/alias_sm' => 'fas fa-share',
            'theme:fp/check' => 'fas fa-check',
            'theme:fp/create_folder' => 'far fa-folder',
            'theme:fp/cross' => 'fas fa-times',
            'theme:fp/download_all' => 'fas fa-download',
            'theme:fp/help' => 'fas fa-question-circle',
            'theme:fp/link' => 'fas fa-link',
            'theme:fp/link_sm' => 'fas fa-link',
            'theme:fp/logout' => 'fas fa-sign-out-alt',
            'theme:fp/path_folder' => 'fas fa-folder',
            'theme:fp/path_folder_rtl' => 'fas fa-folder',
            'theme:fp/refresh' => 'fas fa-sync',
            'theme:fp/search' => 'fas fa-search',
            'theme:fp/setting' => 'fas fa-cog',
            'theme:fp/view_icon_active' => 'fas fa-th',
            'theme:fp/view_list_active' => 'fas fa-list',
            'theme:fp/view_tree_active' => 'fas fa-folder',
            'core:i/addblock' => 'fas fa-plus-square',
            'core:i/assignroles' => 'fas fa-user-plus',
            'core:i/asterisk' => 'fas fa-asterisk',
            'core:i/backup' => 'fas fa-file-archive',
            'core:i/badge' => 'fas fa-shield-alt',
            'core:i/breadcrumbdivider' => 'fa-angle-right',
            'core:i/bullhorn' => 'fas fa-bullhorn',
            'core:i/calc' => 'fas fa-calculator',
            'core:i/calendar' => 'fas fa-calendar-alt',
            'core:i/calendareventdescription' => 'fas fa-align-left',
            'core:i/calendareventtime' => 'far fa-clock',
            'core:i/caution' => 'fas fa-exclamation text-warning',
            'core:i/checked' => 'fas fa-check',
            'core:i/checkedcircle' => 'fas fa-check-circle',
            'core:i/checkpermissions' => 'fas fa-unlock-alt',
            'core:i/cohort' => 'fas fa-users',
            'core:i/competencies' => 'far fa-check-square',
            'core:i/completion_self' => 'far fa-user',
            'core:i/categoryevent' => 'fas fa-cubes',
            'core:i/contentbank' => 'fas fa-paint-brush',
            'core:i/course' => 'fas fa-graduation-cap',
            'core:i/courseevent' => 'fas fa-university',
            'core:i/customfield' => 'far fa-hand-point-right',
            'core:i/dashboard' => 'fas fa-tachometer-alt',
            'core:i/db' => 'fas fa-database',
            'core:i/delete' => 'fas fa-trash',
            'core:i/down' => 'fas fa-arrow-down',
            'core:i/dragdrop' => 'fas fa-arrows-alt',
            'core:i/duration' => 'far fa-clock',
            'core:i/emojicategoryactivities' => 'far fa-futbol',
            'core:i/emojicategoryanimalsnature' => 'fas fa-leaf',
            'core:i/emojicategoryflags' => 'fas fa-flag',
            'core:i/emojicategoryfooddrink' => 'fas fa-utensils',
            'core:i/emojicategoryobjects' => 'far fa-lightbulb',
            'core:i/emojicategorypeoplebody' => 'fas fa-restroom',
            'core:i/emojicategoryrecent' => 'far fa-clock',
            'core:i/emojicategorysmileysemotion' => 'far fa-smile',
            'core:i/emojicategorysymbols' => 'fas fa-heart',
            'core:i/emojicategorytravelplaces' => 'fas fa-plane',
            'core:i/edit' => 'fas fa-pencil-alt',
            'core:i/email' => 'fas fa-envelope',
            'core:i/empty' => 'fa-fw',
            'core:i/enrolmentsuspended' => 'fas fa-pause',
            'core:i/enrolusers' => 'fas fa-user-plus',
            'core:i/excluded' => 'fas fa-circle-minus',
            'core:i/expired' => 'fas fa-exclamation text-warning',
            'core:i/export' => 'fas fa-download',
            'core:i/externallink' => 'fas fa-external-link',
            'core:i/files' => 'far fa-copy',
            'core:i/filter' => 'fas fa-filter',
            'core:i/flagged' => 'fas fa-flag',
            'core:i/folder' => 'fas fa-folder',
            'core:i/grade_correct' => 'fas fa-check text-success',
            'core:i/grade_incorrect' => 'fas fa-times text-danger',
            'core:i/grade_partiallycorrect' => 'fas fa-check-square',
            'core:i/grades' => 'fas fa-table',
            'core:i/grading' => 'fas fa-wand-magic-sparkles',
            'core:i/gradingnotifications' => 'far fa-bell',
            'core:i/groupevent' => 'fas fa-users',
            'core:i/groupn' => 'fas fa-user',
            'core:i/group' => 'fas fa-users',
            'core:i/groups' => 'fas fa-user-circle',
            'core:i/groupv' => 'far fa-user-circle',
            'core:i/home' => 'fas fa-home',
            'core:i/hide' => 'fas fa-eye',
            'core:i/hierarchylock' => 'fas fa-lock',
            'core:i/import' => 'fas fa-level-up-alt',
            'core:i/incorrect' => 'fas fa-exclamation',
            'core:i/info' => 'fas fa-info',
            'core:i/invalid' => 'fas fa-times text-danger',
            'core:i/item' => 'fas fa-circle',
            'core:i/language' => 'fas fa-language',
            'core:i/link' => 'fas fa-link',
            'core:i/loading' => 'fas fa-circle-notch fa-spin',
            'core:i/loading_small' => 'fas fa-circle-notch fa-spin',
            'core:i/location' => 'fas fa-map-marker-alt',
            'core:i/lock' => 'fas fa-lock',
            'core:i/log' => 'fas fa-list-alt',
            'core:i/mahara_host' => 'fas fa-id-badge',
            'core:i/manual_item' => 'far fa-square',
            'core:i/marked' => 'fas fa-circle',
            'core:i/marker' => 'far fa-circle',
            'core:i/mean' => 'fas fa-calculator',
            'core:i/menu' => 'fas fa-ellipsis-v',
            'core:i/menubars' => 'fas fa-bars',
            'core:i/messagecontentaudio' => 'fas fa-headphones',
            'core:i/messagecontentimage' => 'far fa-image',
            'core:i/messagecontentmultimediageneral' => 'fas fa-video',
            'core:i/messagecontentvideo' => 'fas fa-film',
            'core:i/mnethost' => 'fas fa-external-link-alt',
            'core:i/moodle_host' => 'fas fa-graduation-cap',
            'core:i/moremenu' => 'fas fa-ellipsis-h',
            'core:i/move_2d' => 'fas fa-arrows-alt',
            'core:i/muted' => 'fas fa-microphone-slash',
            'core:i/navigationitem' => 'far fa-compass',
            'core:i/ne_red_mark' => 'fas fa-times',
            'core:i/new' => 'fas fa-bolt',
            'core:i/news' => 'far fa-newspaper',
            'core:i/next' => 'fas fa-chevron-right',
            'core:i/nosubcat' => 'far fa-plus-square',
            'core:i/notifications' => 'fas fa-bell',
            'core:i/open' => 'fas fa-folder-open',
            'core:i/otherevent' => 'fas fa-calendar-days',
            'core:i/outcomes' => 'fas fa-tasks',
            'core:i/overriden_grade' => 'fas fa-pencil',
            'core:i/payment' => 'far fa-money-bill-alt',
            'core:i/permissionlock' => 'fas fa-lock',
            'core:i/permissions' => 'fas fa-pen-square',
            'core:i/persona_sign_in_black' => 'fas fa-male',
            'core:i/portfolio' => 'fas fa-id-badge',
            'core:i/preview' => 'fas fa-search-plus',
            'core:i/previous' => 'fas fa-chevron-left',
            'core:i/privatefiles' => 'far fa-file',
            'core:i/progressbar' => 'fas fa-spinner fa-spin',
            'core:i/publish' => 'fas fa-share',
            'core:i/questions' => 'fas fa-question',
            'core:i/reload' => 'fas fa-sync',
            'core:i/report' => 'fas fa-chart-area',
            'core:i/repository' => 'far fa-hdd',
            'core:i/restore' => 'fas fa-level-up-alt',
            'core:i/return' => 'fas fa-arrow-left',
            'core:i/risk_config' => 'fas fa-exclamation text-muted',
            'core:i/risk_managetrust' => 'fas fa-exclamation-triangle text-warning',
            'core:i/risk_personal' => 'fas fa-exclamation-circle text-info',
            'core:i/risk_spam' => 'fas fa-exclamation text-primary',
            'core:i/risk_xss' => 'fas fa-exclamation-triangle text-danger',
            'core:i/role' => 'fas fa-user-md',
            'core:i/rss' => 'fas fa-rss',
            'core:i/rsssitelogo' => 'fas fa-graduation-cap',
            'core:i/scales' => 'fas fa-balance-scale',
            'core:i/scheduled' => 'far fa-calendar-check',
            'core:i/search' => 'fas fa-search',
            'core:i/section' => 'far fa-folder-open',
            'core:i/sendmessage' => 'fas fa-paper-plane',
            'core:i/settings' => 'fas fa-cog',
            'core:i/show' => 'fas fa-eye-slash',
            'core:i/siteevent' => 'fas fa-globe',
            'core:i/star' => 'fas fa-star',
            'core:i/star-o' => 'far fa-star',
            'core:i/star-rating' => 'fas fa-star',
            'core:i/stats' => 'fas fa-chart-line',
            'core:i/switch' => 'fas fa-exchange-alt',
            'core:i/switchrole' => 'fas fa-user-secret',
            'core:i/trash' => 'fas fa-trash-alt',
            'core:i/twoway' => 'fas fa-arrows-alt-h',
            'core:i/unchecked' => 'far fa-square',
            'core:i/uncheckedcircle' => 'far fa-circle',
            'core:i/unflagged' => 'far fa-flag',
            'core:i/unlock' => 'fas fa-unlock',
            'core:i/up' => 'fas fa-arrow-up',
            'core:i/userevent' => 'fas fa-user',
            'core:i/upload' => 'fas fa-upload',
            'core:i/user' => 'fas fa-user',
            'core:i/users' => 'fas fa-users',
            'core:i/valid' => 'fas fa-check text-success',
            'core:i/warning' => 'fas fa-exclamation text-warning',
            'core:i/window_close' => 'fas fa-window-close',
            'core:i/withsubcat' => 'fas fa-plus-square',
            'core:m/USD' => 'fas fa-usd',
            'core:t/addcontact' => 'fas fa-address-card',
            'core:t/add' => 'fas fa-plus',
            'core:t/approve' => 'fas fa-thumbs-up',
            'core:t/assignroles' => 'fas fa-user-circle',
            'core:t/award' => 'fas fa-trophy',
            'core:t/backpack' => 'fas fa-shopping-bag',
            'core:t/backup' => 'fas fa-arrow-circle-down',
            'core:t/block' => 'fas fa-ban',
            'core:t/block_to_dock_rtl' => 'fas fa-chevron-right',
            'core:t/block_to_dock' => 'fas fa-chevron-left',
            'core:t/blocks_drawer_rtl' => 'fas fa-chevron-right',
            'core:t/blocks_drawer' => 'fas fa-chevron-left',
            // Todo: Change to better icon once we have stacked icon support or more icons.
            'core:t/calc_off' => 'fas fa-calculator',
            'core:t/calc' => 'fas fa-calculator',
            'core:t/check' => 'fas fa-check',
            'core:t/clipboard' => 'fas fa-clipboard',
            'core:t/cohort' => 'fas fa-users',
            'core:t/collapsed_empty_rtl' => 'far fa-plus-square',
            'core:t/collapsed_empty' => 'far fa-plus-square',
            'core:t/collapsed_rtl' => 'fas fa-plus-square',
            'core:t/collapsed' => 'fas fa-plus-square',
            'core:t/collapsedcaret' => 'fas fa-caret-right',
            'core:t/collapsedchevron' => 'fas fa-chevron-right',
            'core:t/collapsedchevron_rtl' => 'fas fa-chevron-left',
            'core:t/completion_complete' => 'fas fa-circle',
            'core:t/completion_fail' => 'fas fa-xmark',
            'core:t/completion_incomplete' => 'fas fa-circle-dot',
            'core:t/contextmenu' => 'fas fa-cog',
            'core:t/copy' => 'fas fa-copy',
            'core:t/delete' => 'fas fa-trash',
            'core:t/dockclose' => 'fas fa-window-close',
            'core:t/dock_to_block_rtl' => 'fas fa-chevron-right',
            'core:t/dock_to_block' => 'fas fa-chevron-left',
            'core:t/download' => 'fas fa-download',
            'core:t/down' => 'fas fa-arrow-down',
            'core:t/downlong' => 'fas fa-long-arrow-alt-down',
            'core:t/dropdown' => 'fas fa-cog',
            'core:t/editinline' => 'fas fa-pencil-alt',
            'core:t/edit_menu' => 'fas fa-cog',
            'core:t/editstring' => 'fas fa-pencil-alt',
            'core:t/edit' => 'fas fa-cog',
            'core:t/emailno' => 'fas fa-ban',
            'core:t/email' => 'far fa-envelope',
            'core:t/emptystar' => 'far fa-star',
            'core:t/enrolusers' => 'fas fa-user-plus',
            'core:t/expanded' => 'fas fa-caret-down',
            'core:t/expandedchevron' => 'fas fa-chevron-down',
            'core:t/go' => 'fas fa-play',
            'core:t/grades' => 'fas fa-table',
            'core:t/groupn' => 'fas fa-user',
            'core:t/groups' => 'fas fa-user-circle',
            'core:t/groupv' => 'far fa-user-circle',
            'core:t/hide' => 'fas fa-eye',
            'core:t/index_drawer' => 'fas fa-list',
            'core:t/left' => 'fas fa-arrow-left',
            'core:t/less' => 'fas fa-caret-up',
            'core:t/life-ring' => 'far fa-life-ring',
            'core:t/locked' => 'fas fa-lock',
            'core:t/lock' => 'fas fa-unlock',
            'core:t/locktime' => 'fas fa-lock',
            'core:t/markasread' => 'fas fa-check',
            'core:t/messages' => 'fas fa-comments',
            'core:t/message' => 'fas fa-comment',
            'core:t/more' => 'fas fa-caret-down',
            'core:t/move' => 'fas fa-arrows-alt-v',
            'core:t/online' => 'fas fa-circle',
            'core:t/passwordunmask-edit' => 'fas fa-pencil-alt',
            'core:t/passwordunmask-reveal' => 'fas fa-eye',
            'core:t/play' => 'fas fa-play',
            'core:t/portfolioadd' => 'fas fa-plus',
            'core:t/preferences' => 'fas fa-wrench',
            'core:t/preview' => 'fas fa-search-plus',
            'core:t/print' => 'fas fa-print',
            'core:t/removecontact' => 'fas fa-user-times',
            'core:t/reload' => 'fas fa-sync-alt',
            'core:t/reset' => 'fas fa-redo',
            'core:t/restore' => 'fas fa-arrow-circle-up',
            'core:t/right' => 'fas fa-arrow-right',
            'core:t/sendmessage' => 'fas fa-paper-plane',
            'core:t/show' => 'fas fa-eye-slash',
            'core:t/sort_by' => 'fas fa-sort-amount-down-alt',
            'core:t/sort_asc' => 'fas fa-sort-up',
            'core:t/sort_desc' => 'fas fa-sort-down',
            'core:t/sort' => 'fas fa-sort',
            'core:t/stealth' => 'fas fa-eye-low-vision',
            'core:t/stop' => 'fas fa-stop',
            // Note: Does not work with blocks due to M.util.init_block_hider using M.util.image_url.  See: MDL-58848.
            'core:t/switch_minus' => 'fas fa-minus',
            'core:t/switch_plus' => 'fas fa-plus',
            'core:t/switch_whole' => 'far fa-square',
            'core:t/tags' => 'fas fa-tags',
            'core:t/unblock' => 'fas fa-commenting-alt',
            'core:t/unlocked' => 'fas fa-unlock-alt',
            'core:t/unlock' => 'fas fa-lock',
            'core:t/up' => 'fas fa-arrow-up',
            'core:t/uplong' => 'fas fa-long-arrow-alt-up',
            'core:t/user' => 'fas fa-user',
            'core:t/viewdetails' => 'fas fa-list',
        ];
    }

    /**
     * Overridable function to get a mapping of all icons.
     * Default is to do no mapping.
     *
     * @return array the map.
     */
    public function get_icon_name_map() {
        if (empty($this->fav)) {
            return parent::get_icon_name_map();
        } else {
            if ($this->map === []) {
                $cache = \cache::make('theme_adaptable', 'adaptablefontawesomeiconmapping');

                $this->map = $cache->get('mapping' . $this->fav);
                $getmethod = 'get_fa6_from_fa4'; // Only v6 now.

                if (empty($this->map)) {
                    $this->map = $this->get_core_icon_map();
                    $callback = 'get_fontawesome_icon_map';

                    if ($pluginsfunction = get_plugins_with_function($callback)) {
                        $toolbox = \theme_adaptable\toolbox::get_instance();
                        foreach ($pluginsfunction as $plugintype => $plugins) {
                            foreach ($plugins as $pluginsubtype => $pluginfunction) {
                                $pluginmap = $pluginfunction();
                                // Convert map from FA 4 to 6.
                                foreach ($pluginmap as $micon => $faicon) {
                                    $pluginmap[$micon] = $toolbox->{$getmethod}($faicon, true);
                                }
                                $this->map += $pluginmap;
                            }
                        }
                    }
                    $cache->set('mapping' . $this->fav, $this->map);
                }
            }
        }

        return $this->map;
    }

    /**
     * Get the AMD JS code name.
     *
     * @return string the name.
     */
    public function get_amd_name() {
        if (empty($this->fav)) {
            return parent::get_amd_name();
        }
        return 'theme_adaptable/icon_system_fontawesome';
    }

    /**
     * Get the AMD JS code name.
     *
     * @param renderer_base $output The output object.
     * @param pix_icon $icon The pix_icon object.
     *
     * @return string the rendered icon markup.
     */
    public function render_pix_icon(\renderer_base $output, pix_icon $icon) {
        $subtype = '\core\output\pix_icon_fontawesome';
        $subpix = new $subtype($icon);
        $data = $subpix->export_for_template($output);

        if (!$subpix->is_mapped()) {
            $data['unmappedIcon'] = $icon->export_for_template($output);
        } else if (empty($this->fav)) {
            $data['key'] = 'fa ' . $data['key'];
        }

        // MDL-62680.
        if (isset($icon->attributes['aria-hidden'])) {
            $data['aria-hidden'] = $icon->attributes['aria-hidden'];
        }

        return $output->render_from_template('theme_adaptable/pix_icon_fontawesome', $data);
    }
}
