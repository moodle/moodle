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
 * Category header settings
 *
 * @package    theme_adaptable
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Category headers heading.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage(
        'theme_adaptable_categoryheaders',
        get_string('categoryheaderssettings', 'theme_adaptable'),
        true
    );

    $page->add(new admin_setting_heading(
        'theme_adaptable_categoryheaders',
        get_string('categoryheaderssettingsheading', 'theme_adaptable'),
        format_text(get_string('categoryheaderssettingsdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    // Category headers to use.
    $coursecatsoptions = \theme_adaptable\toolbox::get_top_level_categories();
    $name = 'theme_adaptable/categoryhavecustomheader';
    $title = get_string('categoryhavecustomheader', 'theme_adaptable');
    $description = get_string('categoryhavecustomheaderdesc', 'theme_adaptable');
    $default = [];
    $setting = new admin_setting_configmultiselect($name, $title, $description, $default, $coursecatsoptions);
    $page->add($setting);

    $tohavecustomheader = get_config('theme_adaptable', 'categoryhavecustomheader');
    if (!empty($tohavecustomheader)) {
        $customheaderids = explode(',', $tohavecustomheader);
        $topcats = \theme_adaptable\toolbox::get_top_categories_with_children();
        foreach ($customheaderids as $customheaderid) {
            $catinfo = $topcats[$customheaderid];
            if (empty($catinfo['children'])) {
                $headdesc = get_string(
                    'categoryheaderheaderdesc',
                    'theme_adaptable',
                    ['id' => $customheaderid, 'name' => $catinfo['name']]
                );
            } else {
                $childrentext = '';
                $first = true;
                foreach ($catinfo['children'] as $catchildid => $catchild) {
                    if ($first) {
                        $first = false;
                    } else {
                        $childrentext .= ', ';
                    }
                    $childrentext .= $catchild . '(' . $catchildid . ')';
                }
                $headdesc = get_string(
                    'categoryheaderheaderdescchildren',
                    'theme_adaptable',
                    ['id' => $customheaderid, 'name' => $catinfo['name'], 'children' => $childrentext]
                );
            }
            $page->add(new admin_setting_heading(
                'theme_adaptable_categoryheader' . $customheaderid,
                get_string(
                    'categoryheaderheader',
                    'theme_adaptable',
                    ['id' => $customheaderid, 'name' => $catinfo['name']]
                ),
                format_text($headdesc, FORMAT_MARKDOWN)
            ));

            // Background image.
            $name = 'theme_adaptable/categoryheaderbgimage' . $customheaderid;
            $title = get_string('categoryheaderbgimage', 'theme_adaptable', ['id' => $customheaderid,
                'name' => $catinfo['name'], ]);
            if (empty($catinfo['children'])) {
                $description = get_string('categoryheaderbgimagedesc', 'theme_adaptable', ['id' => $customheaderid,
                    'name' => $catinfo['name'], ]);
            } else {
                $description = get_string(
                    'categoryheaderbgimagedescchildren',
                    'theme_adaptable',
                    ['id' => $customheaderid, 'name' => $catinfo['name'], 'children' => $childrentext]
                );
            }
            $setting = new \theme_adaptable\admin_setting_configstoredfiles(
                $name, $title, $description, 'categoryheaderbgimage' . $customheaderid,
                ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
            );
            $page->add($setting);

            // Background image text colour.
            $name = 'theme_adaptable/categoryheaderbgimagetextcolour' . $customheaderid;
            $title = get_string('categoryheaderbgimagetextcolour', 'theme_adaptable', ['id' => $customheaderid,
                'name' => $catinfo['name'], ]);
            $previewconfig = null;
            if (empty($catinfo['children'])) {
                $description = get_string('categoryheaderbgimagetextcolourdesc', 'theme_adaptable', ['id' => $customheaderid,
                    'name' => $catinfo['name'], ]);
            } else {
                $description = get_string(
                    'categoryheaderbgimagetextcolourdescchildren',
                    'theme_adaptable',
                    ['id' => $customheaderid, 'name' => $catinfo['name'], 'children' => $childrentext]
                );
            }
            $setting = new admin_setting_configcolourpicker($name, $title, $description, '#ffffff', $previewconfig);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);

            // Logo.
            $name = 'theme_adaptable/categoryheaderlogo' . $customheaderid;
            $title = get_string('categoryheaderlogo', 'theme_adaptable', ['id' => $customheaderid,
                'name' => $catinfo['name'], ]);
            if (empty($catinfo['children'])) {
                $description = get_string('categoryheaderlogodesc', 'theme_adaptable', ['id' => $customheaderid,
                    'name' => $catinfo['name'], ]);
            } else {
                $description = get_string('categoryheaderlogodescchildren', 'theme_adaptable', ['id' => $customheaderid,
                    'name' => $catinfo['name'], 'children' => $childrentext, ]);
            }
            $setting = new \theme_adaptable\admin_setting_configstoredfiles(
                $name, $title, $description, 'categoryheaderlogo' . $customheaderid,
                ['accepted_types' => '*.jpg,*.jpeg,*.png', 'maxfiles' => 1]
            );
            $page->add($setting);

            // Custom title.
            $name = 'theme_adaptable/categoryheadercustomtitle' . $customheaderid;
            $title = get_string('categoryheadercustomtitle', 'theme_adaptable', ['id' => $customheaderid,
                'name' => $catinfo['name'], ]);
            if (empty($catinfo['children'])) {
                $description = get_string('categoryheadercustomtitledesc', 'theme_adaptable', ['id' => $customheaderid,
                    'name' => $catinfo['name'], ]);
            } else {
                $description = get_string(
                    'categoryheadercustomtitledescchildren',
                    'theme_adaptable',
                    ['id' => $customheaderid, 'name' => $catinfo['name'],
                    'children' => $childrentext, ]
                );
            }
            $default = '';
            $setting = new admin_setting_configtext($name, $title, $description, $default);
            $page->add($setting);

            // Custom CSS.
            $name = 'theme_adaptable/categoryheadercustomcss' . $customheaderid;
            $title = get_string('categoryheadercustomcss', 'theme_adaptable', ['id' => $customheaderid,
                'name' => $catinfo['name'], ]);
            if (empty($catinfo['children'])) {
                $description = get_string(
                    'categoryheadercustomcssdesc',
                    'theme_adaptable',
                    ['id' => $customheaderid, 'name' => $catinfo['name']]
                );
            } else {
                $description = get_string(
                    'categoryheadercustomcssdescchildren',
                    'theme_adaptable',
                    ['id' => $customheaderid, 'name' => $catinfo['name'], 'children' => $childrentext]
                );
            }
            $default = '';
            $setting = new admin_setting_configtextarea($name, $title, $description, $default);
            $setting->set_updatedcallback('theme_reset_all_caches');
            $page->add($setting);
        }
    }

    $asettings->add($page);
}
