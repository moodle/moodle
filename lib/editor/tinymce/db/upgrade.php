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
 * TinyMCE editor integration upgrade.
 *
 * @package    editor_tinymce
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_editor_tinymce_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();


    if ($oldversion < 2012083100) {
        // Reset redesigned editor toolbar setting.
        unset_config('customtoolbar', 'editor_tinymce');
        upgrade_plugin_savepoint(true, 2012083100, 'editor', 'tinymce');
    }


    // Moodle v2.4.0 release upgrade line
    // Put any upgrade step following this


    // Moodle v2.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2013061400) {
        // Reset redesigned editor toolbar setting.
        $oldorder = "fontselect,fontsizeselect,formatselect,|,undo,redo,|,search,replace,|,fullscreen

bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,|,cleanup,removeformat,pastetext,pasteword,|,forecolor,backcolor,|,ltr,rtl

bullist,numlist,outdent,indent,|,link,unlink,|,image,nonbreaking,charmap,table,|,code";

        $neworder = "formatselect,bold,italic,|,bullist,numlist,|,link,unlink,|,image

undo,redo,|,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,|,outdent,indent,|,forecolor,backcolor,|,ltr,rtl,|,nonbreaking,charmap,table

fontselect,fontsizeselect,code,search,replace,|,cleanup,removeformat,pastetext,pasteword,|,fullscreen";
        $currentorder = get_config('editor_tinymce', 'customtoolbar');
        if ($currentorder == $oldorder) {
            unset_config('customtoolbar', 'editor_tinymce');
            set_config('customtoolbar', $neworder, 'editor_tinymce');
        }
        upgrade_plugin_savepoint(true, 2013061400, 'editor', 'tinymce');
    }

    if ($oldversion < 2013070500) {
        // Insert wrap plugin to nicely wrap the toolbars on small screens.
        $oldorder = "formatselect,bold,italic,|,bullist,numlist,|,link,unlink,|,image

undo,redo,|,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,|,outdent,indent,|,forecolor,backcolor,|,ltr,rtl,|,nonbreaking,charmap,table

fontselect,fontsizeselect,code,search,replace,|,cleanup,removeformat,pastetext,pasteword,|,fullscreen";

        $neworder = "formatselect,bold,italic,wrap,bullist,numlist,|,link,unlink,|,image

undo,redo,|,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,wrap,outdent,indent,|,forecolor,backcolor,|,ltr,rtl,|,nonbreaking,charmap,table

fontselect,fontsizeselect,wrap,code,search,replace,|,cleanup,removeformat,pastetext,pasteword,|,fullscreen";
        $currentorder = get_config('editor_tinymce', 'customtoolbar');
        if ($currentorder == $oldorder) {
            unset_config('customtoolbar', 'editor_tinymce');
            set_config('customtoolbar', $neworder, 'editor_tinymce');
        } else {
            // Simple auto conversion algorithm.
            $toolbars = explode("\n", $oldorder);
            $newtoolbars = array();
            foreach ($toolbars as $toolbar) {
                $sepcount = substr_count($toolbar, '|');

                if ($sepcount > 0) {
                    // We assume the middle separator (rounding down).
                    $divisionindex = round($sepcount / 2, 0, PHP_ROUND_HALF_DOWN);

                    $buttons = explode(',', $toolbar);
                    $index = 0;
                    foreach ($buttons as $key => $button) {
                        if ($button === "|") {
                            if ($index == $divisionindex) {
                                $buttons[$key] = 'wrap';
                                break;
                            } else {
                                $index += 1;
                            }
                        }
                    }
                    $toolbar = implode(',', $buttons);
                }
                array_push($newtoolbars, $toolbar);
            }
            $neworder = implode("\n", $newtoolbars);

            // Set the new config.
            unset_config('customtoolbar', 'editor_tinymce');
            set_config('customtoolbar', $neworder, 'editor_tinymce');
        }
        upgrade_plugin_savepoint(true, 2013070500, 'editor', 'tinymce');
    }

    if ($oldversion < 2013102900) {
        // Reset redesigned editor toolbar setting.
        $currentorder = get_config('editor_tinymce', 'customtoolbar');
        // Start with a wrap.
        $neworder = "wrap,". $currentorder;
        // Replace all separators with wraps to allow for proper display of groups.
        $neworder = preg_replace('/\|\|*/', "wrap", $neworder);
        // Insert a wrap between the format selector and the bold button.
        $neworder = str_replace("formatselect,bold", "formatselect,wrap,bold", $neworder);
        set_config('customtoolbar', $neworder, 'editor_tinymce');
        upgrade_plugin_savepoint(true, 2013102900, 'editor', 'tinymce');
    }

    if ($oldversion < 2013110600) {
        // Reset redesigned editor toolbar setting.
        $currentorder = get_config('editor_tinymce', 'customtoolbar');
        $olddefaultorder = "wrap,formatselect,wrap,bold,italic,wrap,bullist,numlist,wrap,link,unlink,wrap,image

undo,redo,wrap,underline,strikethrough,sub,sup,wrap,justifyleft,justifycenter,justifyright,wrap,outdent,indent,wrap,forecolor,backcolor,wrap,ltr,rtl,wrap,nonbreaking,charmap,table

fontselect,fontsizeselect,wrap,code,search,replace,wrap,cleanup,removeformat,pastetext,pasteword,wrap,fullscreen";
        $neworder = "wrap,formatselect,wrap,bold,italic,wrap,bullist,numlist,wrap,link,unlink,wrap,image

undo,redo,wrap,underline,strikethrough,sub,sup,wrap,justifyleft,justifycenter,justifyright,wrap,outdent,indent,wrap,forecolor,backcolor,wrap,ltr,rtl

fontselect,fontsizeselect,wrap,code,search,replace,wrap,nonbreaking,charmap,table,wrap,cleanup,removeformat,pastetext,pasteword,wrap,fullscreen";
        if ($currentorder == $olddefaultorder) {
            set_config('customtoolbar', $neworder, 'editor_tinymce');
        }

        upgrade_plugin_savepoint(true, 2013110600, 'editor', 'tinymce');
    }

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.


    // Moodle v2.7.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2014062900) {
        // We only want to delete DragMath from the customtoolbar setting if the directory no longer exists. If
        // the directory is present then it means it has been restored, so do not remove any settings.
        if (!check_dir_exists($CFG->libdir . '/editor/tinymce/plugins/dragmath', false)) {
            // Remove the DragMath plugin from the 'customtoolbar' setting (if it exists) as it has been removed.
            $currentorder = get_config('editor_tinymce', 'customtoolbar');
            $newtoolbarrows = array();
            $currenttoolbarrows = explode("\n", $currentorder);
            foreach ($currenttoolbarrows as $currenttoolbarrow) {
                $currenttoolbarrow = implode(',', array_diff(str_getcsv($currenttoolbarrow), array('dragmath')));
                $newtoolbarrows[] = $currenttoolbarrow;
            }
            $neworder = implode("\n", $newtoolbarrows);
            unset_config('customtoolbar', 'editor_tinymce');
            set_config('customtoolbar', $neworder, 'editor_tinymce');
        }

        upgrade_plugin_savepoint(true, 2014062900, 'editor', 'tinymce');
    }

    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
