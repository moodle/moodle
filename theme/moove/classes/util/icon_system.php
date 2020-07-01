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
 * Custom moove icon system
 *
 * @package    theme_moove
 * @copyright  2017 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\util;

use core\output\icon_system_font;
use renderer_base;
use pix_icon;

defined('MOODLE_INTERNAL') || die();

/**
 * Class allowing different systems for mapping and rendering icons.
 *
 * @package    theme_moove
 * @copyright  2017 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class icon_system extends icon_system_font {

    /**
     * @var array $map Cached map of moodle icon names to font awesome icon names.
     */
    private $map = [];

    /**
     * Get the icon mapping
     *
     * @return array
     */
    public function get_core_icon_map() {
        return [
            'core:docs' => 'slicon-info',
            'core:help' => 'slicon-question text-info',
            'core:req' => 'slicon-exclamation text-danger',
            'core:a/add_file' => 'fa-file-o',
            'core:a/create_folder' => 'fa-folder-o',
            'core:a/download_all' => 'fa-download',
            'core:a/help' => 'slicon-question text-info',
            'core:a/logout' => 'slicon-logout',
            'core:a/refresh' => 'fa-refresh',
            'core:a/search' => 'fa-search',
            'core:a/setting' => 'slicon-settings',
            'core:a/view_icon_active' => 'fa-th',
            'core:a/view_list_active' => 'slicon-list',
            'core:a/view_tree_active' => 'fa-folder',
            'core:b/bookmark-new' => 'fa-bookmark',
            'core:b/document-edit' => 'fa-pencil',
            'core:b/document-new' => 'fa-file-o',
            'core:b/document-properties' => 'fa-info',
            'core:b/edit-copy' => 'fa-files-o',
            'core:b/edit-delete' => 'slicon-trash',
            'core:e/abbr' => 'slicon-bubble',
            'core:e/absolute' => 'fa-crosshairs',
            'core:e/accessibility_checker' => 'fa-universal-access',
            'core:e/acronym' => 'slicon-bubble',
            'core:e/advance_hr' => 'fa-arrows-h',
            'core:e/align_center' => 'fa-align-center',
            'core:e/align_left' => 'fa-align-left',
            'core:e/align_right' => 'fa-align-right',
            'core:e/anchor' => 'fa-chain',
            'core:e/backward' => 'fa-undo',
            'core:e/bold' => 'fa-bold',
            'core:e/bullet_list' => 'fa-list-ul',
            'core:e/cancel' => 'fa-times',
            'core:e/cell_props' => 'fa-info',
            'core:e/cite' => 'fa-quote-right',
            'core:e/cleanup_messy_code' => 'fa-eraser',
            'core:e/clear_formatting' => 'fa-i-cursor',
            'core:e/copy' => 'fa-clone',
            'core:e/cut' => 'fa-scissors',
            'core:e/decrease_indent' => 'fa-outdent',
            'core:e/delete_col' => 'slicon-minus',
            'core:e/delete_row' => 'slicon-minus',
            'core:e/delete' => 'slicon-minus',
            'core:e/delete_table' => 'slicon-minus',
            'core:e/document_properties' => 'fa-info',
            'core:e/emoticons' => 'fa-smile-o',
            'core:e/find_replace' => 'fa-search-plus',
            'core:e/file-text' => 'fa-file-text',
            'core:e/forward' => 'fa-arrow-right',
            'core:e/fullpage' => 'fa-arrows-alt',
            'core:e/fullscreen' => 'fa-arrows-alt',
            'core:e/help' => 'slicon-question',
            'core:e/increase_indent' => 'fa-indent',
            'core:e/insert_col_after' => 'fa-columns',
            'core:e/insert_col_before' => 'fa-columns',
            'core:e/insert_date' => 'slicon-calendar',
            'core:e/insert_edit_image' => 'fa-picture-o',
            'core:e/insert_edit_link' => 'fa-link',
            'core:e/insert_edit_video' => 'fa-file-video-o',
            'core:e/insert_file' => 'slicon-doc',
            'core:e/insert_horizontal_ruler' => 'fa-arrows-h',
            'core:e/insert_nonbreaking_space' => 'fa-square-o',
            'core:e/insert_page_break' => 'fa-level-down',
            'core:e/insert_row_after' => 'slicon-plus',
            'core:e/insert_row_before' => 'slicon-plus',
            'core:e/insert' => 'slicon-plus',
            'core:e/insert_time' => 'fa-clock-o',
            'core:e/italic' => 'fa-italic',
            'core:e/justify' => 'fa-align-justify',
            'core:e/layers_over' => 'slicon-arrow-up-circle',
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
            'core:e/prevent_autolink' => 'slicon-exclamation',
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
            'core:e/spellcheck' => 'slicon-check',
            'core:e/split_cells' => 'fa-columns',
            'core:e/strikethrough' => 'fa-strikethrough',
            'core:e/styleparagraph' => 'fa-font',
            'core:e/styleprops' => 'fa-info',
            'core:e/subscript' => 'fa-subscript',
            'core:e/superscript' => 'fa-superscript',
            'core:e/table_props' => 'fa-table',
            'core:e/table' => 'fa-table',
            'core:e/template' => 'fa-sticky-note',
            'core:e/text_color_picker' => 'fa-paint-brush',
            'core:e/text_color' => 'fa-paint-brush',
            'core:e/text_highlight_picker' => 'fa-lightbulb-o',
            'core:e/text_highlight' => 'fa-lightbulb-o',
            'core:e/tick' => 'slicon-check',
            'core:e/toggle_blockquote' => 'fa-quote-left',
            'core:e/underline' => 'fa-underline',
            'core:e/undo' => 'fa-undo',
            'core:e/visual_aid' => 'fa-universal-access',
            'core:e/visual_blocks' => 'fa-audio-description',
            'theme:fp/add_file' => 'fa-file-o',
            'theme:fp/alias' => 'slicon-share',
            'theme:fp/alias_sm' => 'slicon-share',
            'theme:fp/check' => 'slicon-check',
            'theme:fp/create_folder' => 'fa-folder-o',
            'theme:fp/cross' => 'fa-remove',
            'theme:fp/download_all' => 'fa-download',
            'theme:fp/help' => 'slicon-question',
            'theme:fp/link' => 'fa-link',
            'theme:fp/link_sm' => 'fa-link',
            'theme:fp/logout' => 'slicon-logout',
            'theme:fp/path_folder' => 'fa-folder',
            'theme:fp/path_folder_rtl' => 'fa-folder',
            'theme:fp/refresh' => 'fa-refresh',
            'theme:fp/search' => 'fa-search',
            'theme:fp/setting' => 'slicon-settings',
            'theme:fp/view_icon_active' => 'fa-th',
            'theme:fp/view_list_active' => 'slicon-list',
            'theme:fp/view_tree_active' => 'fa-folder',
            'core:i/addblock' => 'fa-plus-square',
            'core:i/assignroles' => 'fa-user-plus',
            'core:i/backup' => 'fa-file-zip-o',
            'core:i/badge' => 'slicon-shield',
            'core:i/breadcrumbdivider' => 'fa-angle-right',
            'core:i/calc' => 'fa-calculator',
            'core:i/calendar' => 'slicon-calendar',
            'core:i/calendareventdescription' => 'fa-align-left',
            'core:i/calendareventtime' => 'fa-clock-o',
            'core:i/caution' => 'slicon-exclamation text-warning',
            'core:i/checked' => 'slicon-check',
            'core:i/checkedcircle' => 'fa-check-circle',
            'core:i/checkpermissions' => 'slicon-lock-open',
            'core:i/cohort' => 'slicon-people',
            'core:i/competencies' => 'slicon-check',
            'core:i/completion_self' => 'fa-user-o',
            'core:i/dashboard' => 'slicon-speedometer',
            'core:i/categoryevent' => 'fa-cubes',
            'core:i/course' => 'slicon-graduation',
            'core:i/courseevent' => 'fa-university',
            'core:i/customfield' => 'fa-hand-o-right',
            'core:i/db' => 'fa-database',
            'core:i/delete' => 'slicon-trash',
            'core:i/down' => 'fa-arrow-down',
            'core:i/dragdrop' => 'fa-arrows',
            'core:i/duration' => 'fa-clock-o',
            'core:i/emojicategoryactivities' => 'fa-futbol-o',
            'core:i/emojicategoryanimalsnature' => 'fa-leaf',
            'core:i/emojicategoryflags' => 'fa-flag',
            'core:i/emojicategoryfooddrink' => 'fa-cutlery',
            'core:i/emojicategoryobjects' => 'fa-lightbulb-o',
            'core:i/emojicategoryrecent' => 'fa-clock-o',
            'core:i/emojicategorysmileyspeople' => 'fa-smile-o',
            'core:i/emojicategorysymbols' => 'fa-heart',
            'core:i/emojicategorytravelplaces' => 'fa-plane',
            'core:i/edit' => 'slicon-pencil',
            'core:i/email' => 'fa-envelope',
            'core:i/empty' => 'slicon-options',
            'core:i/enrolmentsuspended' => 'fa-pause',
            'core:i/enrolusers' => 'fa-user-plus',
            'core:i/expired' => 'slicon-exclamation text-warning',
            'core:i/export' => 'fa-download',
            'core:i/files' => 'slicon-doc',
            'core:i/filter' => 'fa-filter',
            'core:i/flagged' => 'fa-flag',
            'core:i/folder' => 'fa-folder',
            'core:i/grade_correct' => 'fa-check text-success',
            'core:i/grade_incorrect' => 'fa-remove text-danger',
            'core:i/grade_partiallycorrect' => 'fa-check-square',
            'core:i/grades' => 'slicon-book-open',
            'core:i/grading' => 'fa-magic',
            'core:i/gradingnotifications' => 'fa-bell-o',
            'core:i/groupevent' => 'fa-group',
            'core:i/groupn' => 'slicon-user',
            'core:i/group' => 'slicon-people',
            'core:i/groups' => 'slicon-user-follow',
            'core:i/groupv' => 'fa-user-circle-o',
            'core:i/home' => 'slicon-home',
            'core:i/hide' => 'fa-eye',
            'core:i/hierarchylock' => 'fa-lock',
            'core:i/import' => 'slicon-arrow-up-circle',
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
            'core:i/menubars' => 'slicon-menu',
            'core:i/messagecontentaudio' => 'fa-headphones',
            'core:i/messagecontentimage' => 'fa-image',
            'core:i/messagecontentvideo' => 'fa-film',
            'core:i/messagecontentmultimediageneral' => 'fa-file-video-o',
            'core:i/mnethost' => 'fa-external-link',
            'core:i/moodle_host' => 'slicon-graduation',
            'core:i/moremenu' => 'fa-ellipsis-h',
            'core:i/move_2d' => 'fa-arrows',
            'core:i/muted' => 'fa-microphone-slash',
            'core:i/navigationitem' => 'slicon-options',
            'core:i/ne_red_mark' => 'fa-remove',
            'core:i/new' => 'fa-bolt',
            'core:i/news' => 'fa-newspaper-o',
            'core:i/next' => 'fa-chevron-right',
            'core:i/nosubcat' => 'fa-plus-square-o',
            'core:i/notifications' => 'fa-bell',
            'core:i/open' => 'fa-folder-open',
            'core:i/outcomes' => 'fa-tasks',
            'core:i/payment' => 'fa-money',
            'core:i/permissionlock' => 'fa-lock',
            'core:i/permissions' => 'fa-pencil-square-o',
            'core:i/persona_sign_in_black' => 'fa-male',
            'core:i/portfolio' => 'fa-id-badge',
            'core:i/preview' => 'fa-search-plus',
            'core:i/previous' => 'fa-chevron-left',
            'core:i/privatefiles' => 'slicon-doc',
            'core:i/progressbar' => 'fa-spinner fa-spin',
            'core:i/publish' => 'slicon-share',
            'core:i/questions' => 'fa-question',
            'core:i/reload' => 'fa-refresh',
            'core:i/report' => 'fa-area-chart',
            'core:i/repository' => 'fa-hdd-o',
            'core:i/restore' => 'slicon-arrow-up-circle',
            'core:i/return' => 'slicon-action-undo',
            'core:i/risk_config' => 'slicon-exclamation text-muted',
            'core:i/risk_managetrust' => 'slicon-exclamation-triangle text-warning',
            'core:i/risk_personal' => 'slicon-exclamation text-info',
            'core:i/risk_spam' => 'slicon-exclamation text-primary',
            'core:i/risk_xss' => 'fa-exclamation-triangle text-danger',
            'core:i/role' => 'fa-user-md',
            'core:i/rss' => 'fa-rss',
            'core:i/rsssitelogo' => 'slicon-graduation',
            'core:i/scales' => 'fa-balance-scale',
            'core:i/scheduled' => 'fa-calendar-check-o',
            'core:i/search' => 'fa-search',
            'core:i/section' => 'slicon-options',
            'core:i/sendmessage' => 'fa-paper-plane',
            'core:i/settings' => 'slicon-settings',
            'core:i/show' => 'fa-eye-slash',
            'core:i/siteevent' => 'fa-globe',
            'core:i/star' => 'fa-star',
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
            'core:i/up' => 'slicon-arrow-up',
            'core:i/userevent' => 'slicon-user',
            'core:i/user' => 'slicon-user',
            'core:i/users' => 'slicon-people',
            'core:i/valid' => 'fa-check text-success',
            'core:i/warning' => 'slicon-exclamation text-warning',
            'core:i/window_close' => 'fa-window-close',
            'core:i/withsubcat' => 'fa-plus-square',
            'core:m/USD' => 'fa-usd',
            'core:t/addcontact' => 'fa-address-card',
            'core:t/add' => 'slicon-plus',
            'core:t/approve' => 'fa-thumbs-up',
            'core:t/assignroles' => 'slicon-user-follow',
            'core:t/award' => 'fa-trophy',
            'core:t/backpack' => 'fa-shopping-bag',
            'core:t/backup' => 'fa-arrow-circle-down',
            'core:t/block' => 'fa-ban',
            'core:t/block_to_dock_rtl' => 'fa-chevron-right',
            'core:t/block_to_dock' => 'fa-chevron-left',
            'core:t/calc_off' => 'fa-calculator', // TODO: Change to better icon once we have stacked icon support or more icons.
            'core:t/calc' => 'fa-calculator',
            'core:t/check' => 'slicon-check',
            'core:t/cohort' => 'slicon-people',
            'core:t/collapsed_empty_rtl' => 'fa-plus-square-o',
            'core:t/collapsed_empty' => 'fa-plus-square-o',
            'core:t/collapsed_rtl' => 'fa-plus-square',
            'core:t/collapsed' => 'fa-plus-square',
            'core:t/collapsedcaret' => 'fa-caret-right',
            'core:t/contextmenu' => 'slicon-settings',
            'core:t/copy' => 'fa-copy',
            'core:t/delete' => 'slicon-trash',
            'core:t/dockclose' => 'fa-window-close',
            'core:t/dock_to_block_rtl' => 'fa-chevron-right',
            'core:t/dock_to_block' => 'fa-chevron-left',
            'core:t/download' => 'fa-download',
            'core:t/down' => 'fa-arrow-down',
            'core:t/downlong' => 'fa-long-arrow-down',
            'core:t/dropdown' => 'slicon-settings',
            'core:t/editinline' => 'slicon-pencil',
            'core:t/edit_menu' => 'slicon-settings',
            'core:t/editstring' => 'slicon-pencil',
            'core:t/edit' => 'slicon-settings',
            'core:t/emailno' => 'fa-ban',
            'core:t/email' => 'fa-envelope-o',
            'core:t/emptystar' => 'fa-star-o',
            'core:t/enrolusers' => 'fa-user-plus',
            'core:t/expanded' => 'fa-caret-down',
            'core:t/go' => 'fa-play',
            'core:t/grades' => 'slicon-book-open',
            'core:t/groupn' => 'slicon-user',
            'core:t/groups' => 'slicon-user-follow',
            'core:t/groupv' => 'fa-user-circle-o',
            'core:t/hide' => 'fa-eye',
            'core:t/left' => 'slicon-action-undo',
            'core:t/less' => 'fa-caret-up',
            'core:t/locked' => 'fa-lock',
            'core:t/lock' => 'fa-unlock',
            'core:t/locktime' => 'fa-lock',
            'core:t/markasread' => 'slicon-check',
            'core:t/messages' => 'slicon-bubbles',
            'core:t/message' => 'slicon-bubble',
            'core:t/more' => 'fa-caret-down',
            'core:t/move' => 'fa-arrows-v',
            'core:t/online' => 'fa-circle',
            'core:t/passwordunmask-edit' => 'slicon-pencil',
            'core:t/passwordunmask-reveal' => 'fa-eye',
            'core:t/portfolioadd' => 'slicon-plus',
            'core:t/preferences' => 'slicon-wrench',
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
            'core:t/switch_minus' => 'slicon-minus',
            'core:t/switch_plus' => 'slicon-plus',
            'core:t/switch_whole' => 'fa-square-o',
            'core:t/tags' => 'fa-tags',
            'core:t/unblock' => 'fa-commenting',
            'core:t/unlocked' => 'slicon-lock-open',
            'core:t/unlock' => 'fa-lock',
            'core:t/up' => 'slicon-arrow-up',
            'core:t/uplong' => 'fa-long-arrow-up',
            'core:t/user' => 'slicon-user',
            'core:t/viewdetails' => 'slicon-layers',
        ];
    }

    /**
     * Overridable function to get a mapping of all icons.
     * Default is to do no mapping.
     *
     * @return array
     */
    public function get_icon_name_map() {
        if ($this->map === []) {
            $cache = \cache::make('theme_moove', 'fontawesomemooveiconmapping');

            $this->map = $cache->get('mapping');

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
                $cache->set('mapping', $this->map);
            }

        }
        return $this->map;
    }

    /**
     * Get the AMD icon system name.
     *
     * @return string
     */
    public function get_amd_name() {
        return 'core/icon_system_fontawesome';
    }

    /**
     * Renders the pix icon using the icon system
     *
     * @param renderer_base $output
     * @param pix_icon $icon
     * @return mixed
     */
    public function render_pix_icon(renderer_base $output, pix_icon $icon) {
        $subtype = 'pix_icon_fontawesome';
        $subpix = new $subtype($icon);

        $data = $subpix->export_for_template($output);

        if (!$subpix->is_mapped()) {
            $data['unmappedIcon'] = $icon->export_for_template($output);
        }
        return $output->render_from_template('core/pix_icon_fontawesome', $data);
    }

}

