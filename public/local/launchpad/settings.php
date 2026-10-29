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
 * Admin settings for local_launchpad.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_launchpad', get_string('pluginname', 'local_launchpad'));

    $settings->add(new admin_setting_heading(
        'local_launchpad/signupheading',
        get_string('signupheading', 'local_launchpad'),
        get_string('signupheading_desc', 'local_launchpad')
    ));

    $settings->add(new admin_setting_heading(
        'local_launchpad/mentorheading',
        get_string('mentorheading', 'local_launchpad'),
        get_string('mentorheading_desc', 'local_launchpad')
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentorformurl',
        get_string('mentorformurl', 'local_launchpad'),
        get_string('mentorformurl_desc', 'local_launchpad'),
        '',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentoreyebrow',
        get_string('mentoreyebrow', 'local_launchpad'),
        get_string('mentoreyebrow_desc', 'local_launchpad'),
        'Join the review team',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentortitle',
        get_string('mentortitle', 'local_launchpad'),
        get_string('mentortitle_desc', 'local_launchpad'),
        'Want to be a mentor?',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtextarea(
        'local_launchpad/mentorintro',
        get_string('mentorintro', 'local_launchpad'),
        get_string('mentorintro_desc', 'local_launchpad'),
        'Apply on the form, we review fit, then we contact you for onboarding. Same 70/100 bar for employer and fellowship cohorts.',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentorchip',
        get_string('mentorchip', 'local_launchpad'),
        get_string('mentorchip_desc', 'local_launchpad'),
        'Application',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentorcardtitle',
        get_string('mentorcardtitle', 'local_launchpad'),
        get_string('mentorcardtitle_desc', 'local_launchpad'),
        'How mentoring works here',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtextarea(
        'local_launchpad/mentorcardbody',
        get_string('mentorcardbody', 'local_launchpad'),
        get_string('mentorcardbody_desc', 'local_launchpad'),
        'Mentors score evidence against published rubrics, write change notes learners can apply, and hold the same 70/100 bar for employer and fellowship cohorts.',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentorstep1title',
        get_string('mentorstep1title', 'local_launchpad'),
        get_string('mentorstep_desc', 'local_launchpad'),
        'Apply on the Google Form',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentorstep1body',
        get_string('mentorstep1body', 'local_launchpad'),
        get_string('mentorstep_desc', 'local_launchpad'),
        'Tell us your background, tracks you can review, and availability.',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentorstep2title',
        get_string('mentorstep2title', 'local_launchpad'),
        get_string('mentorstep_desc', 'local_launchpad'),
        'We review your details',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentorstep2body',
        get_string('mentorstep2body', 'local_launchpad'),
        get_string('mentorstep_desc', 'local_launchpad'),
        'Our team reads your submission and checks fit for open mentor seats.',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentorstep3title',
        get_string('mentorstep3title', 'local_launchpad'),
        get_string('mentorstep_desc', 'local_launchpad'),
        'We contact you',
        PARAM_TEXT
    ));
    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentorstep3body',
        get_string('mentorstep3body', 'local_launchpad'),
        get_string('mentorstep_desc', 'local_launchpad'),
        'Next steps: intro call, rubric walkthrough, then access to review in Learn.',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentorctalabel',
        get_string('mentorctalabel', 'local_launchpad'),
        get_string('mentorctalabel_desc', 'local_launchpad'),
        'Apply to be a mentor',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/mentormedialab',
        get_string('mentormedialab', 'local_launchpad'),
        get_string('mentormedialab_desc', 'local_launchpad'),
        'Mentors',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_heading(
        'local_launchpad/footerheading',
        get_string('footerheading', 'local_launchpad'),
        get_string('footerheading_desc', 'local_launchpad')
    ));

    $settings->add(new admin_setting_configtextarea(
        'local_launchpad/footerbrand',
        get_string('footerbrand', 'local_launchpad'),
        get_string('footerbrand_desc', 'local_launchpad'),
        'Employer and fellowship portals on one Moodle account. Assigned courses, mentor review, and proof of work.',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/footercompany',
        get_string('footercompany', 'local_launchpad'),
        get_string('footercompany_desc', 'local_launchpad'),
        'ToBa Tech Solutions · ToBa Learn',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/footerlegal',
        get_string('footerlegal', 'local_launchpad'),
        get_string('footerlegal_desc', 'local_launchpad'),
        'Structured learning for employers and fellows, not a paid internship.',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/footerctanote',
        get_string('footerctanote', 'local_launchpad'),
        get_string('footerctanote_desc', 'local_launchpad'),
        'Your enrolment decides which portal opens.',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/footeremail',
        get_string('footeremail', 'local_launchpad'),
        get_string('footeremail_desc', 'local_launchpad'),
        '',
        PARAM_EMAIL
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/footerphone',
        get_string('footerphone', 'local_launchpad'),
        get_string('footerphone_desc', 'local_launchpad'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/footerprivacyurl',
        get_string('footerprivacyurl', 'local_launchpad'),
        get_string('footerprivacyurl_desc', 'local_launchpad'),
        '',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/footertermsurl',
        get_string('footertermsurl', 'local_launchpad'),
        get_string('footertermsurl_desc', 'local_launchpad'),
        '',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'local_launchpad/footercontacturl',
        get_string('footercontacturl', 'local_launchpad'),
        get_string('footercontacturl_desc', 'local_launchpad'),
        '',
        PARAM_URL
    ));

    $ADMIN->add('localplugins', $settings);
}
