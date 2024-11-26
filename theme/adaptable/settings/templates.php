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
 * Template settings.
 *
 * @package    theme_adaptable
 * @copyright  2020 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

// Templates.
if ($ADMIN->fulltree) {
    $page = new \theme_adaptable\admin_settingspage('theme_adaptable_templates',
        get_string('templatessettings', 'theme_adaptable'));

    $page->add(new admin_setting_heading(
        'theme_adaptable_templates_heading',
        get_string('templatesheading', 'theme_adaptable'),
        format_text(get_string('templatesheadingdesc', 'theme_adaptable'), FORMAT_MARKDOWN)
    ));

    static $templates = [
        'mod_forum/forum_post_email_htmlemail' => 'mod_forum/forum_post_email_htmlemail',
        'mod_forum/forum_post_email_htmlemail_body' => 'mod_forum/forum_post_email_htmlemail_body',
        'mod_forum/forum_post_email_textemail' => 'mod_forum/forum_post_email_textemail',
        'mod_forum/forum_post_emaildigestbasic_htmlemail' => 'mod_forum/forum_post_emaildigestbasic_htmlemail',
        'mod_forum/forum_post_emaildigestbasic_textemail' => 'mod_forum/forum_post_emaildigestbasic_textemail',
        'mod_forum/forum_post_emaildigestfull_htmlemail' => 'mod_forum/forum_post_emaildigestfull_htmlemail',
        'mod_forum/forum_post_emaildigestfull_textemail' => 'mod_forum/forum_post_emaildigestfull_textemail',
    ];
    $name = 'theme_adaptable/templatessel';
    $title = get_string('templatessel', 'theme_adaptable');
    $description = get_string('templatesseldesc', 'theme_adaptable');
    $default = [];
    $setting = new admin_setting_configmultiselect($name, $title, $description, $default, $templates);
    $page->add($setting);

    $asettings->add($page);

    $overridetemplates = get_config('theme_adaptable', 'templatessel');
    if ($overridetemplates) {
        if (file_exists("{$CFG->dirroot}/theme/adaptable/settings/adaptable_admin_setting_configtemplate.php")) {
            require_once($CFG->dirroot . '/theme/adaptable/settings/adaptable_admin_setting_configtemplate.php');
        } else if (
            !empty($CFG->themedir) &&
            file_exists("{$CFG->themedir}/adaptable/settings/adaptable_admin_setting_configtemplate.php")
        ) {
            require_once($CFG->themedir . '/adaptable/settings/adaptable_admin_setting_configtemplate.php');
        }

        $overridetemplates = explode(',', $overridetemplates);
        foreach ($overridetemplates as $overridetemplate) {
            $overridetemplatesetting = str_replace('/', '_', $overridetemplate);
            $temppage = new admin_settingpage(
                'theme_adaptable_templates_' . $overridetemplatesetting,
                get_string('overridetemplate', 'theme_adaptable', $overridetemplate)
            );

            $name = 'theme_adaptable/activatetemplateoverride_' . $overridetemplatesetting;
            $title = get_string('activatetemplateoverride', 'theme_adaptable', $overridetemplate);
            $description = get_string(
                'activatetemplateoverridedesc',
                'theme_adaptable',
                ['template' => $overridetemplate, 'setting' => $overridetemplatesetting]
            );
            $setting = new admin_setting_configcheckbox($name, $title, $description, false);
            $temppage->add($setting);

            $name = 'theme_adaptable/overriddentemplate_' . $overridetemplatesetting;
            $title = get_string('overriddentemplate', 'theme_adaptable', $overridetemplate);
            $description = get_string('overriddentemplatedesc', 'theme_adaptable', $overridetemplate);
            $default = '';
            $setting = new adaptable_admin_setting_configtemplate($name, $title, $description, $default, $overridetemplate);
            $temppage->add($setting);

            $asettings->add($temppage);
        }
    }
}
