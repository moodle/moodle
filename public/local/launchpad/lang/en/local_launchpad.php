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
 * Language strings for local_launchpad.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'ToBa Learn LaunchPad';
$string['privacy:metadata'] = 'The LaunchPad plugin stores a portal preference for users who self-register for Fellowship.';
$string['privacy:metadata:preference:toba_portal'] = 'Records that the account was created via Fellowship self-registration.';
$string['pagetitle'] = 'ToBa Learn · LaunchPad Fellowship';
$string['mentorformurl'] = 'Become a mentor · Google Form URL';
$string['mentorformurl_desc'] = 'Public Google Form link for mentor applications. Applicants are redirected here from the landing page. After they submit, your team contacts them for the next steps.';

/* Fellowship self-registration. */
$string['signupheading'] = 'Fellowship self-registration';
$string['signupheading_desc'] = 'Fellows can create a Moodle account from the LaunchPad and login Fellowship tab. This requires Email-based self-registration (Site administration → Plugins → Authentication → Manage authentication → Self registration = Email-based self-registration). Install/upgrade enables it only when Self registration is currently empty. After email confirmation, programme admins still assign courses — there is no auto-enrolment.';
$string['signupnotice'] = 'Fellowship registration — confirm your email, then your programme assigns courses. There is no open marketplace.';
$string['signupportalinvalid'] = 'Self-registration is only available for the Fellowship portal.';
$string['registerfellowship'] = 'Register for Fellowship';
$string['signinfellowship'] = 'Sign in to Fellowship';
$string['createfellowshipaccount'] = 'Create Fellowship account';
$string['signupclosednote'] = 'Fellowship self-registration is closed. Ask your programme admin for an account, or sign in if you already have one.';
$string['signupconfirmnote'] = 'Confirm your email, then your programme assigns courses. No open marketplace.';
$string['signuppagetitle'] = 'Create your Fellowship account';
$string['signuppagelead'] = 'Use the same details Moodle requires for a new account. After you confirm your email, your programme admin assigns courses.';

/* Become a mentor section. */
$string['mentorheading'] = 'Become a mentor section';
$string['mentorheading_desc'] = 'Manage the public “Want to be a mentor?” section: section copy, application card, three steps, and the apply button. Leave a field blank to keep its default.';
$string['mentoreyebrow'] = 'Section eyebrow';
$string['mentoreyebrow_desc'] = 'Small label above the section title (for example: Join the review team).';
$string['mentortitle'] = 'Section title';
$string['mentortitle_desc'] = 'Main heading for the mentor section.';
$string['mentorintro'] = 'Section intro';
$string['mentorintro_desc'] = 'Short paragraph beside the section title.';
$string['mentorchip'] = 'Card chip';
$string['mentorchip_desc'] = 'Small pill on the application card (for example: Application).';
$string['mentorcardtitle'] = 'Card title';
$string['mentorcardtitle_desc'] = 'Heading inside the application card.';
$string['mentorcardbody'] = 'Card description';
$string['mentorcardbody_desc'] = 'Paragraph under the card title explaining how mentoring works.';
$string['mentorstep1title'] = 'Step 1 title';
$string['mentorstep1body'] = 'Step 1 detail';
$string['mentorstep2title'] = 'Step 2 title';
$string['mentorstep2body'] = 'Step 2 detail';
$string['mentorstep3title'] = 'Step 3 title';
$string['mentorstep3body'] = 'Step 3 detail';
$string['mentorstep_desc'] = 'Shown in the numbered application steps.';
$string['mentorctalabel'] = 'Apply button label';
$string['mentorctalabel_desc'] = 'Text on the primary apply button.';
$string['mentormedialab'] = 'Image badge label';
$string['mentormedialab_desc'] = 'Label on the mentor image card (for example: Mentors).';

/* Footer content (Site administration → Plugins → ToBa Learn LaunchPad). */
$string['footerheading'] = 'Landing page footer';
$string['footerheading_desc'] = 'Manage the public LaunchPad footer copy, legal line, and optional contact links. Leave optional fields blank to hide them.';
$string['footerbrand'] = 'Brand tagline';
$string['footerbrand_desc'] = 'Short paragraph under the logo. Keep it to one or two sentences.';
$string['footercompany'] = 'Company / product name';
$string['footercompany_desc'] = 'Shown in the copyright line (for example: ToBa Tech Solutions · ToBa Learn).';
$string['footerlegal'] = 'Legal / disclaimer line';
$string['footerlegal_desc'] = 'Secondary line in the footer bar (for example: not a paid internship).';
$string['footerctanote'] = 'CTA helper text';
$string['footerctanote_desc'] = 'Small note under the Sign in / Access portal button.';
$string['footeremail'] = 'Contact email';
$string['footeremail_desc'] = 'Optional. Shown as a mailto link in the Contact column. Leave blank to hide.';
$string['footerphone'] = 'Contact phone';
$string['footerphone_desc'] = 'Optional. Leave blank to hide.';
$string['footerprivacyurl'] = 'Privacy policy URL';
$string['footerprivacyurl_desc'] = 'Optional public URL. Leave blank to hide the Privacy link.';
$string['footertermsurl'] = 'Terms of use URL';
$string['footertermsurl_desc'] = 'Optional public URL. Leave blank to hide the Terms link.';
$string['footercontacturl'] = 'Contact / support page URL';
$string['footercontacturl_desc'] = 'Optional public URL (support form or contact page). Leave blank to hide.';

/* Employee area (#13 provisioning). */
$string['emptystate_no_course'] = 'You do not have a LaunchPad course yet. When your place is confirmed, your programme admin will enrol you and your track will appear here.';
$string['employee_area_title'] = 'Employee learning';
$string['employee_area_heading'] = 'Employee area';
$string['employee_area_body'] = 'Your employee access is active. Programme content will appear here when your organisation assigns it. This area does not enrol you in the Fellowship or grant mentor or admin rights.';
$string['employee_access_denied'] = 'You do not have employee access. Ask your programme administrator if you expected to see this page.';
$string['employee_cohort_name'] = 'ToBa Learn Employees';
