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
 * Settings
 *
 * This file contains settings used by tool_mobile
 *
 * @package    tool_mobile
 * @copyright  2016 Juan Leyva
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_admin\local\settings\autocomplete;
use tool_mobile\api;


if ($hassiteconfig || has_capability('moodle/site:configview', context_system::instance())) {
    // We should wait to the installation to finish since we depend on some configuration values that are set once
    // the admin user profile is configured.
    if ($hassiteconfig && !during_initial_install()) {
        $enablemobiledocurl = new moodle_url(get_docs_url('Enable_mobile_web_services'));
        $enablemobiledoclink = html_writer::link($enablemobiledocurl, new lang_string('documentation'));
        $default = is_https() ? 1 : 0;
        $optionalsubsystems = $ADMIN->locate('optionalsubsystems');
        $optionalsubsystems->add(new admin_setting_enablemobileservice(
            'enablemobilewebservice',
            new lang_string('enablemobilewebservice', 'admin'),
            new lang_string('configenablemobilewebservice', 'admin', $enablemobiledoclink),
            $default
        ));
    }

    // Getting information to prepare settings pages.
    $ispremiumplan = false;
    $subscriptiondata = api::get_subscription_information(true);
    if (is_array($subscriptiondata) && !empty($subscriptiondata['subscription']['plan'])) {
        $plan = \core_text::strtolower(trim($subscriptiondata['subscription']['plan']));
        $ispremiumplan = ($plan === 'premium' || $plan === 'bma');
    }
    if (!$ispremiumplan) {
        $upgradeplanname = new lang_string('enhanced', 'tool_mobile');
        if (is_array($subscriptiondata) && !empty($subscriptiondata['availableplans'])) {
            foreach ($subscriptiondata['availableplans'] as $plan) {
                if ($plan['plan'] != 'premium') {
                    continue;
                }
                $upgradeplanname = $plan['name'];
            }
        }
        $planname = new lang_string('currentusage', 'tool_mobile');
        if (is_array($subscriptiondata) && !empty($subscriptiondata['subscription']['name'])) {
            $planname = $subscriptiondata['subscription']['name'];
        }
        $appsportalurl = (new \moodle_url(\tool_mobile\api::MOODLE_APPS_PORTAL_URL))->out(true);
        $premiumfeaturesurl = (new moodle_url("/admin/settings.php", ['section' => 'premiumfeatures']))->out(true);
    }
    // Setting pages group.
    $ismobilewsdisabled = empty($CFG->enablemobilewebservice);
    $ADMIN->add(
        'root',
        new admin_category('mobileapp', new lang_string('mobileapp', 'tool_mobile'), $ismobilewsdisabled),
        'development'
    );

    // Subscription information page.
    $mobileappsubscriptionstr = get_string('mobileappsubscription', 'tool_mobile');
    if (!$ispremiumplan) {
        $mobileappsubscriptionstr .= ' ' . get_string('upgradeyourplan', 'tool_mobile');
    }

    $ADMIN->add(
        'mobileapp',
        new admin_externalpage(
            'mobileappsubscription',
            $mobileappsubscriptionstr,
            "$CFG->wwwroot/$CFG->admin/tool/mobile/subscription.php",
            'moodle/site:configview',
            $ismobilewsdisabled
        )
    );

    // Premium features settings page.
    $premiumfeaturestitle = get_string('premiumfeatures', 'tool_mobile');
    $shownewsettingsuntil = null;
    if (!$ismobilewsdisabled) {
        $shownewsettingsuntil = get_config('tool_mobile', 'shownewsettings');
        if (empty($shownewsettingsuntil)) {
            $shownewsettingsuntil = time() + (180 * DAYSECS);
            set_config('shownewsettings', $shownewsettingsuntil, 'tool_mobile');
        }
        if (!empty($shownewsettingsuntil) && time() < $shownewsettingsuntil) {
            $premiumfeaturestitle .= ' ' . get_string('new', 'tool_mobile');
        }
    }
    $temp = new admin_settingpage(
        'premiumfeatures',
        $premiumfeaturestitle,
        'moodle/site:config',
        $ismobilewsdisabled
    );

    if (!$ispremiumplan) {
        // Show notice about features limited in the current plan.
        $templateheadersettings = [
            'title' => new lang_string('upgraderemovelimits', 'tool_mobile', $upgradeplanname),
            'icon' => '🚀',
            'message' => clean_text(get_string('upgradeplanlimits', 'tool_mobile', $planname)),
            'buttonstr' => new lang_string('learnmore', 'tool_mobile'),
            'buttonurl' => (new \moodle_url("/admin/tool/mobile/subscription.php"))->out(true),
        ];
        $lastupdated = get_config('tool_mobile', 'subscriptioninfoupdated');
        if (!$lastupdated || time() - $lastupdated > 10 * DAYSECS) {
            // Add cache reload option if the information is older than 10 days.
            $reloadcachetext = new lang_string('planneverchecked', 'tool_mobile');
            if ($lastupdated) {
                $lastupdatedstr = userdate($lastupdated, get_string('strftimedatemonthabbr', 'langconfig'));
                $reloadcachetext = new lang_string('planlastchecked', 'tool_mobile', $lastupdatedstr);
            }
            $templateheadersettings['reloadcache'] = [
                'url' => (new \moodle_url("/admin/tool/mobile/subscription.php", ['returnto' => $premiumfeaturesurl]))->out(true),
                'text' => $reloadcachetext,
            ];
        }

        $temp->add(new admin_setting_heading(
            'tool_mobile/moodleappsportalfeatures',
            '',
            $OUTPUT->render_from_template('tool_mobile/settings_alert', $templateheadersettings)
        ));
    }

    $temp->add(new admin_setting_heading(
        'tool_mobile/authentication',
        new lang_string('authentication'),
        ''
    ));

    if (!$ispremiumplan) {
        $featureslimited = [];
        if (is_array($subscriptiondata) && !empty($subscriptiondata['subscription']['features'])) {
            foreach ($subscriptiondata['subscription']['features'] as $feature) {
                if (isset($feature['limit']) && $feature['limit']) {
                    switch ($feature['name']) {
                        case 'customlanguagestrings':
                            $featureslimited['customlangstrings'] = $feature['limit'];
                            break;
                        default:
                            $featureslimited[$feature['name']] = $feature['limit'];
                            break;
                    }
                }
            }
        }
        $featureparams = [
            'url' => $appsportalurl,
            'plan' => $planname,
        ];

        $templatesubscribe = [
            'insettings' => true,
            'icon' => [
                'icon' => 'i/warning',
                'component' => 'core',
            ],
        ];
    }

    $options = [
        tool_mobile\api::QR_CODE_DISABLED => new lang_string('qrcodedisabled', 'tool_mobile'),
        tool_mobile\api::QR_CODE_URL => new lang_string('qrcodetypeurl', 'tool_mobile'),
    ];

    if (is_https()) {   // Allow QR login for https sites.
        $options[tool_mobile\api::QR_CODE_LOGIN] = '⚡ ' . get_string('qrcodetypelogin', 'tool_mobile')
            . ' (' . get_string('premiumfeatureonly', 'tool_mobile') . ')';
    }

    $temp->add(new admin_setting_configselect(
        'tool_mobile/qrcodetype',
        new lang_string('qrcodetype', 'tool_mobile'),
        new lang_string('qrcodetype_desc', 'tool_mobile'),
        tool_mobile\api::QR_CODE_URL,
        $options
    ));

    $temp->add(new admin_setting_configduration(
        'tool_mobile/qrkeyttl',
        new lang_string('qrkeyttl', 'tool_mobile'),
        new lang_string('qrkeyttl_desc', 'tool_mobile'),
        tool_mobile\api::LOGIN_QR_KEY_TTL,
        MINSECS
    ));
    $temp->hide_if('tool_mobile/qrkeyttl', 'tool_mobile/qrcodetype', 'neq', tool_mobile\api::QR_CODE_LOGIN);

    $temp->add(new admin_setting_configcheckbox(
        'tool_mobile/qrsameipcheck',
        new lang_string('qrsameipcheck', 'tool_mobile'),
        new lang_string('qrsameipcheck_desc', 'tool_mobile'),
        1
    ));
    $temp->hide_if('tool_mobile/qrsameipcheck', 'tool_mobile/qrcodetype', 'neq', tool_mobile\api::QR_CODE_LOGIN);

    if (!$ispremiumplan && get_config('tool_mobile', 'qrcodetype') == tool_mobile\api::QR_CODE_LOGIN) {
        $featureparams['feature'] = get_string('qrcodetypelogin', 'tool_mobile');
        $templatesubscribe['message'] = clean_text(get_string('qronlypremium', 'tool_mobile', $featureparams));

        $temp->add(new admin_setting_heading(
            'tool_mobile/qronlypremium',
            '',
            $OUTPUT->render_from_template('tool_mobile/subscribe_alert', $templatesubscribe)
        ));
    }

    $temp->add(new admin_setting_heading(
        'tool_mobile/branding',
        new lang_string('branding', 'tool_mobile'),
        ''
    ));

    $temp->add(new admin_setting_configcheckbox(
        'tool_mobile/showlogoinappheader',
        new lang_string('showlogoinappheader', 'tool_mobile'),
        new lang_string('showlogoinappheader_desc', 'tool_mobile'),
        0
    ));

    $temp->add(new admin_setting_configtext(
        'mobilecssurl',
        new lang_string('mobilecssurl', 'tool_mobile'),
        new lang_string('configmobilecssurl', 'tool_mobile'),
        '',
        PARAM_URL
    ));

    $temp->add(new admin_setting_heading(
        'tool_mobile/customisation',
        new lang_string('customisation', 'tool_mobile'),
        ''
    ));

    $options = tool_mobile\api::get_features_list();
    $featurename = new lang_string('disabledfeatures', 'tool_mobile');
    $temp->add(new admin_setting_configmultiselect(
        'tool_mobile/disabledfeatures',
        $featurename,
        new lang_string('disabledfeatures_desc', 'tool_mobile'),
        [],
        $options
    ));
    if (!$ispremiumplan && isset($featureslimited['disabledfeatures'])) {
        $featureparams['limit'] = $featureslimited['disabledfeatures'];
        $featureparams['feature'] = strtolower($featurename);
        $templatesubscribe['message'] = clean_text(get_string('limiteddisabledfeature', 'tool_mobile', $featureparams));

        $temp->add(new admin_setting_heading(
            'tool_mobile/disabledfeaturessubscribe',
            '',
            $OUTPUT->render_from_template('tool_mobile/subscribe_alert', $templatesubscribe)
        ));
    }

    // Manage disabledfeatures with custommenuitems and customusermenuitems.
    $custommenuitemsstr = new lang_string('custommenuitems', 'tool_mobile');
    $customusermenuitemsstr = new lang_string('customusermenuitems', 'tool_mobile');
    $temp->add(new admin_setting_configtextarea(
        'tool_mobile/custommenuitems',
        $custommenuitemsstr,
        new lang_string('custommenuitems_desc', 'tool_mobile'),
        '',
        PARAM_RAW,
        '50',
        '10',
    ));
    if (!$ispremiumplan && isset($featureslimited['custommenuitems'])) {
        $featureparams['limit'] = $featureslimited['custommenuitems'];
        $featureparams['feature1'] = strtolower($custommenuitemsstr);
        $featureparams['feature2'] = strtolower($customusermenuitemsstr);
        $templatesubscribe['message'] = clean_text(get_string('limiteddisabledfeature_related', 'tool_mobile', $featureparams));

        $temp->add(new admin_setting_heading(
            'tool_mobile/custommenuitemssubscribe',
            '',
            $OUTPUT->render_from_template('tool_mobile/subscribe_alert', $templatesubscribe)
        ));
    }

    $temp->add(new admin_setting_configtextarea(
        'tool_mobile/customusermenuitems',
        $customusermenuitemsstr,
        new lang_string('customusermenuitems_desc', 'tool_mobile'),
        '',
        PARAM_RAW,
        '50',
        '10',
    ));
    if (!$ispremiumplan && isset($featureslimited['custommenuitems'])) {
        $featureparams['limit'] = $featureslimited['custommenuitems'];
        $featureparams['feature1'] = strtolower($customusermenuitemsstr);
        $featureparams['feature2'] = strtolower($custommenuitemsstr);
        $templatesubscribe['message'] = clean_text(get_string('limiteddisabledfeature_related', 'tool_mobile', $featureparams));

        $temp->add(new admin_setting_heading(
            'tool_mobile/customusermenuitemssubscribe',
            '',
            $OUTPUT->render_from_template('tool_mobile/subscribe_alert', $templatesubscribe)
        ));
    }

    $featurename = new lang_string('customlangstrings', 'tool_mobile');
    $temp->add(new admin_setting_configtextarea(
        'tool_mobile/customlangstrings',
        $featurename,
        new lang_string('customlangstrings_desc', 'tool_mobile'),
        '',
        PARAM_RAW,
        '50',
        '10'
    ));
    if (!$ispremiumplan && isset($featureslimited['customlangstrings'])) {
        $featureparams['limit'] = $featureslimited['customlangstrings'];
        $featureparams['feature'] = strtolower($featurename);
        $templatesubscribe['message'] = clean_text(get_string('limiteddisabledfeature', 'tool_mobile', $featureparams));

        $temp->add(new admin_setting_heading(
            'tool_mobile/customlangstringssubscribe',
            '',
            $OUTPUT->render_from_template('tool_mobile/subscribe_alert', $templatesubscribe)
        ));
    }

    $ADMIN->add('mobileapp', $temp);

    // Appearance features settings page.
    $temp = new admin_settingpage(
        'mobileappearance',
        new lang_string('mobileappearance', 'tool_mobile'),
        'moodle/site:config',
        $ismobilewsdisabled
    );

    if (!$ispremiumplan) {
        $strmovedparams = [
            'url' => $premiumfeaturesurl,
            'urlname' => get_string('premiumfeatures', 'tool_mobile'),
            'name' => $planname,
            'upgradename' => $upgradeplanname,
        ];
        $templateheadersettings = [
            'title' => new lang_string('lookingforcsscustomisation', 'tool_mobile'),
            'icon' => '💡',
            'message' => clean_text(get_string('movedcsstopremiumfeatures', 'tool_mobile', $strmovedparams)),
            'extraclasses' => 'alert-info',
        ];

        $temp->add(new admin_setting_heading(
            'tool_mobile/moodleappsportalfeaturesappearance',
            '',
            $OUTPUT->render_from_template('tool_mobile/settings_alert', $templateheadersettings)
        ));
    }

    // Reference to Branded Mobile App.
    if (empty($CFG->disableserviceads_branded)) {
        $temp->add(new admin_setting_description(
            'moodlebrandedappreference',
            new lang_string('moodlebrandedapp', 'admin'),
            new lang_string('moodlebrandedappreference', 'admin')
        ));
    }

    $temp->add(new admin_setting_heading(
        'tool_mobile/smartappbanners',
        new lang_string('smartappbanners', 'tool_mobile'),
        ''
    ));

    $temp->add(
        new admin_setting_configcheckbox(
            'tool_mobile/enablesmartappbanners',
            new lang_string('enablesmartappbanners', 'tool_mobile'),
            new lang_string('enablesmartappbanners_desc', 'tool_mobile'),
            0
        )
    );

    $temp->add(new admin_setting_configtext(
        'tool_mobile/iosappid',
        new lang_string('iosappid', 'tool_mobile'),
        new lang_string('iosappid_desc', 'tool_mobile'),
        tool_mobile\api::DEFAULT_IOS_APP_ID,
        PARAM_ALPHANUM
    ));

    $temp->add(new admin_setting_configtext(
        'tool_mobile/androidappid',
        new lang_string('androidappid', 'tool_mobile'),
        new lang_string('androidappid_desc', 'tool_mobile'),
        tool_mobile\api::DEFAULT_ANDROID_APP_ID,
        PARAM_NOTAGS
    ));

    $temp->add(new admin_setting_configtext(
        'tool_mobile/setuplink',
        new lang_string('setuplink', 'tool_mobile'),
        new lang_string('setuplink_desc', 'tool_mobile'),
        'https://download.moodle.org/mobile',
        PARAM_URL
    ));

    $ADMIN->add('mobileapp', $temp);

    // Authentication features settings page.
    $temp = new admin_settingpage(
        'mobileauthentication',
        new lang_string('mobileauthentication', 'tool_mobile'),
        'moodle/site:config',
        $ismobilewsdisabled
    );

    if (!$ispremiumplan) {
        $templateheadersettings = [
            'title' => new lang_string('lookingforqrcodelogin', 'tool_mobile'),
            'icon' => '🎯',
            'message' => clean_text(get_string('movedqrtopremiumfeatures', 'tool_mobile', $strmovedparams)),
            'extraclasses' => 'alert-info',
        ];
        $featuresnotice = $OUTPUT->render_from_template('tool_mobile/settings_alert', $templateheadersettings);

        $temp->add(new admin_setting_heading(
            'tool_mobile/moodleappsportalfeaturesauthentication',
            '',
            $featuresnotice
        ));
    }

    $options = [
        tool_mobile\api::LOGIN_VIA_APP => new lang_string('loginintheapp', 'tool_mobile'),
        tool_mobile\api::LOGIN_VIA_BROWSER => new lang_string('logininthebrowser', 'tool_mobile'),
        tool_mobile\api::LOGIN_VIA_EMBEDDED_BROWSER => new lang_string('loginintheembeddedbrowser', 'tool_mobile'),
    ];
    $temp->add(new admin_setting_configselect(
        'tool_mobile/typeoflogin',
        new lang_string('typeoflogin', 'tool_mobile'),
        new lang_string('typeoflogin_desc', 'tool_mobile'),
        1,
        $options
    ));

    $temp->add(new admin_setting_configtext(
        'tool_mobile/forcedurlscheme',
        new lang_string('forcedurlscheme_key', 'tool_mobile'),
        new lang_string('forcedurlscheme', 'tool_mobile'),
        'moodlemobile',
        PARAM_NOTAGS
    ));

    $temp->add(new admin_setting_configcheckbox(
        'tool_mobile/forcelogout',
        new lang_string('forcelogout', 'tool_mobile'),
        new lang_string('forcelogout_desc', 'tool_mobile'),
        0
    ));

    $options = [
        tool_mobile\api::AUTOLOGOUT_DISABLED => new lang_string('never'),
        tool_mobile\api::AUTOLOGOUT_INMEDIATE => new lang_string('autologoutinmediate', 'tool_mobile'),
        tool_mobile\api::AUTOLOGOUT_CUSTOM => new lang_string('autologoutcustom', 'tool_mobile'),
    ];
    $temp->add(new admin_setting_configselect(
        'tool_mobile/autologout',
        new lang_string('autologout', 'tool_mobile'),
        new lang_string('autologout_desc', 'tool_mobile'),
        0,
        $options
    ));

    $temp->add(new admin_setting_configduration(
        'tool_mobile/autologouttime',
        new lang_string('autologouttime', 'tool_mobile'),
        '',
        DAYSECS
    ));
    $temp->hide_if('tool_mobile/autologouttime', 'tool_mobile/autologout', 'neq', tool_mobile\api::AUTOLOGOUT_CUSTOM);

    $temp->add(new admin_setting_configtext(
        'tool_mobile/minimumversion',
        new lang_string('minimumversion_key', 'tool_mobile'),
        new lang_string('minimumversion', 'tool_mobile'),
        '',
        PARAM_NOTAGS
    ));

    $options = [
        60 => new lang_string('numminutes', '', 1),
        180 => new lang_string('numminutes', '', 3),
        360 => new lang_string('numminutes', '', 6),
        900 => new lang_string('numminutes', '', 15),
        1800 => new lang_string('numminutes', '', 30),
        3600 => new lang_string('numminutes', '', 60),
    ];
    $temp->add(new admin_setting_configselect(
        'tool_mobile/autologinmintimebetweenreq',
        new lang_string('autologinmintimebetweenreq', 'tool_mobile'),
        new lang_string('autologinmintimebetweenreq_desc', 'tool_mobile'),
        360,
        $options
    ));

    $temp->add(new admin_setting_configcheckbox(
        'tool_mobile/enabledeeplinkautologin',
        new lang_string('enabledeeplinkautologin', 'tool_mobile'),
        new lang_string('enabledeeplinkautologin_desc', 'tool_mobile'),
        0
    ));

    $ADMIN->add('mobileapp', $temp);

    // Features settings page.
    $temp = new admin_settingpage(
        'mobilefeatures',
        new lang_string('mobilefeatures', 'tool_mobile'),
        'moodle/site:config',
        $ismobilewsdisabled
    );

    if (!$ispremiumplan) {
        $templateheadersettings = [
            'title' => new lang_string('lookingforcustomisationfeatures', 'tool_mobile'),
            'icon' => '🎯',
            'message' => clean_text(get_string('moveddisabledtopremiumfeatures', 'tool_mobile', $strmovedparams)),
            'extraclasses' => 'alert-info',
        ];
        $featuresnotice = $OUTPUT->render_from_template('tool_mobile/settings_alert', $templateheadersettings);

        $temp->add(new admin_setting_heading(
            'tool_mobile/moodleappsportalfeaturesfeatures',
            '',
            $featuresnotice
        ));
    }

    $temp->add(new admin_setting_configtext(
        'tool_mobile/apppolicy',
        new lang_string('apppolicy', 'tool_mobile'),
        new lang_string('apppolicy_help', 'tool_mobile'),
        '',
        PARAM_URL
    ));

    // File type exclusionlist.
    $choices = [];
    foreach (core_filetypes::get_types() as $key => $info) {
        $text = '.' . $key;
        if (!empty($info['type'])) {
            $text .= ' (' . $info['type'] . ')';
        }
        $choices[$key] = $text;
    }

    $attributes = [
        'manageurl' => new \moodle_url('/admin/tool/filetypes/index.php'),
        'managetext' => new lang_string('managefiletypes', 'tool_mobile'),
        'multiple' => true,
        'delimiter' => ',',
        'placeholder' => new lang_string('filetypeexclusionlistplaceholder', 'tool_mobile'),
    ];
    $temp->add(new autocomplete(
        'tool_mobile/filetypeexclusionlist',
        new lang_string('filetypeexclusionlist', 'tool_mobile'),
        new lang_string('filetypeexclusionlist_desc', 'tool_mobile'),
        [],
        $choices,
        $attributes
    ));

    $temp->add(
        new admin_setting_configtextarea(
            'tool_mobile/scriptallowlist',
            new lang_string('scriptallowlist', 'tool_mobile'),
            new lang_string('scriptallowlist_desc', 'tool_mobile'),
            '',
            PARAM_RAW_TRIMMED,
            '50',
            '10',
        )
    );

    $ADMIN->add('mobileapp', $temp);
}
