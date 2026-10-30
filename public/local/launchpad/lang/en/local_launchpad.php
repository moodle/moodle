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
$string['signupheading_desc'] = 'Fellows can create a Moodle account from the LaunchPad and login Fellowship tab. This requires Email-based self-registration (Site administration → Plugins → Authentication → Manage authentication → Self registration = Email-based self-registration). Install/upgrade enables it only when Self registration is currently empty. After email confirmation, programme admins still assign courses. There is no auto-enrolment.';
$string['signupnotice'] = 'Fellowship registration: confirm your email, then your programme assigns courses. There is no open marketplace.';
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
$string['mentor_area_title'] = 'Mentor home';
$string['mentor_area_heading'] = 'Mentor area';
$string['mentor_area_body'] = 'Your mentor access is active for the LaunchPad courses listed below. Dedicated review tools will appear here later. This area does not enrol you as a Fellow or grant admin rights.';
$string['mentor_area_courses'] = 'Your assigned LaunchPad courses';
$string['mentor_area_no_courses'] = 'No LaunchPad mentor courses are assigned to you yet. Ask your programme administrator if you expected to see one.';
$string['mentor_access_denied'] = 'You do not have mentor access. Mentors are assigned by staff on LaunchPad courses only.';

// Fellowship dashboard shell.
$string['fellowship_area_title'] = 'Fellowship · ToBa Learn';
$string['fellowship_area_heading'] = 'Fellowship';
$string['fellowship_role_label'] = 'Fellow · LaunchPad';
$string['fellowship_access_denied'] = 'You do not have Fellowship access. Fellows are enrolled as students on LaunchPad courses by programme staff.';
$string['fellowship_course_denied'] = 'That LaunchPad course is not available on your account.';
$string['fellowship_no_courses'] = 'You do not have a LaunchPad course yet. When your place is confirmed, your programme admin will enrol you and your track will appear here.';
$string['fellowship_no_activities'] = 'No visible activities in this course yet.';
$string['fellowship_welcome_named'] = 'Welcome, {$a}';
$string['fellowship_welcome_back'] = 'Welcome back, {$a}';
$string['fellowship_nav_aria'] = 'Fellowship navigation';
$string['fellowship_nav_learn'] = 'Learn';
$string['fellowship_nav_you'] = 'You';
$string['fellowship_nav_home'] = 'Home';
$string['fellowship_nav_track'] = 'My track';
$string['fellowship_nav_progress'] = 'Progress';
$string['fellowship_nav_achievements'] = 'Achievements';
$string['fellowship_nav_record'] = 'My record';
$string['fellowship_nav_feedback'] = 'Feedback';
$string['fellowship_nav_phases'] = 'Phase progress';
$string['fellowship_nav_completion'] = 'Completion';
$string['fellowship_nav_help'] = 'Help & FAQ';
$string['fellowship_nav_news'] = 'Announcements';
$string['fellowship_nav_calendar'] = 'Calendar';
$string['fellowship_nav_portfolio'] = 'Portfolio';
$string['fellowship_nav_account'] = 'Account';
$string['fellowship_nav_public'] = 'Public site';
$string['fellowship_nav_signout'] = 'Sign out';
$string['fellowship_news_intro'] = 'Posts from the news forum on your LaunchPad courses. Open Moodle for the full thread.';
$string['fellowship_news_open_moodle'] = 'Open news forum';
$string['fellowship_news_empty_title'] = 'No announcements yet';
$string['fellowship_news_empty_body'] = 'When your programme posts to the course news forum, those discussions appear here.';
$string['fellowship_calendar_intro'] = 'Upcoming events from your courses and personal calendar. Times use your Moodle timezone.';
$string['fellowship_calendar_open_moodle'] = 'Open Moodle calendar';
$string['fellowship_calendar_empty_title'] = 'Nothing scheduled soon';
$string['fellowship_calendar_empty_body'] = 'Course or personal events in the next 90 days will show here. Nothing is invented for demo.';
$string['fellowship_calendar_personal'] = 'Personal';
$string['fellowship_portfolio_intro'] = 'Assignment evidence from your track — submissions and mentor reviews. This is not a separate portfolio product yet.';
$string['fellowship_portfolio_has_feedback'] = 'Includes mentor comment';
$string['fellowship_portfolio_empty_title'] = 'No evidence yet';
$string['fellowship_portfolio_empty_body'] = 'Submit a track assignment in Moodle. Graded or pending submissions appear here as portfolio evidence.';
$string['fellowship_foundations_heading'] = 'Foundations activities';
$string['fellowship_foundations_empty'] = 'No Foundations activities are visible on your track yet.';
$string['fellowship_orbit_tip_news'] = 'Announcements come from each course <b>news forum</b>. Open a post to read the full thread in Moodle.';
$string['fellowship_orbit_tip_calendar'] = 'This list is your Moodle calendar for enrolled tracks — not a sample schedule.';
$string['fellowship_orbit_tip_portfolio'] = 'Portfolio here means assignment evidence already in Moodle. A dedicated case-study saver is not built yet.';
$string['fellowship_your_courses'] = 'Your courses';
$string['fellowship_open_moodle_course'] = 'Open in Moodle';
$string['fellowship_open_activity'] = 'Open';
$string['fellowship_next_activity'] = 'Continue learning';
$string['fellowship_view_progress'] = 'View progress';
$string['fellowship_all_tracked_done'] = 'Tracked activities in this course are complete. Open My track for the full list, or continue in Moodle.';
$string['fellowship_progress_intro'] = 'Progress below comes from Moodle activity completion when it is enabled. It is not XP, badges, or a grade.';
$string['fellowship_progress_from_completion'] = 'Based on Moodle activity completion for activities that track completion.';
$string['fellowship_progress_not_tracked'] = 'Activity completion is not enabled (or no activities track completion) for this course yet. Open the Moodle course for the authoritative view.';
$string['fellowship_progress_summary'] = '{$a->done} of {$a->total} tracked activities complete ({$a->percent}%).';
$string['fellowship_complete'] = 'Complete';
$string['fellowship_incomplete'] = 'To do';
$string['fellowship_not_tracked'] = 'Not tracked';
$string['fellowship_native_activities_note'] = 'Quizzes, assignments, and pages open in Moodle. Submit and grade there — this view does not replace those workflows.';
$string['fellowship_section_general'] = 'General';
$string['fellowship_section_numbered'] = 'Section {$a}';
$string['fellowship_help_intro'] = 'Straight answers about how the Fellowship works in Moodle.';
$string['fellowship_faq_internship_q'] = 'Is this a paid internship?';
$string['fellowship_faq_internship_a'] = 'No. LaunchPad is structured learning. No job, internship, placement, or income is promised.';
$string['fellowship_faq_track_q'] = 'Why do I only see certain courses?';
$string['fellowship_faq_track_a'] = 'Your programme team enrols you on LaunchPad courses. Other tracks stay unavailable until you are enrolled.';
$string['fellowship_faq_grade_q'] = 'Where do grades and feedback live?';
$string['fellowship_faq_grade_a'] = 'In the Moodle activities themselves (for example Assign feedback and the gradebook). This dashboard does not invent grades.';
$string['fellowship_faq_quiz_q'] = 'Can I take quizzes here?';
$string['fellowship_faq_quiz_a'] = 'Open the quiz link to use Moodle’s quiz. Attempts and scoring stay in Moodle.';
$string['fellowship_home_lead'] = 'Your assigned LaunchPad courses and the next activity to open in Moodle.';
$string['fellowship_stat_badges'] = 'Badges earned';
$string['fellowship_stat_required'] = 'Required done';
$string['fellowship_stat_gate'] = 'Current gate';
$string['fellowship_recent_badges'] = 'Recent badges';
$string['fellowship_recent_badges_empty'] = 'No badges earned yet.';
$string['fellowship_recent_badges_empty_hint'] = 'Finish lessons, quizzes, or assignments in Moodle — recent badges will show here newest first.';
$string['fellowship_gate_none'] = 'Not started';
$string['fellowship_gate_complete'] = 'Complete';
$string['fellowship_menu'] = 'Menu';
$string['fellowship_skip_to_content'] = 'Skip to content';
$string['fellowship_breadcrumb'] = 'Breadcrumb';
$string['fellowship_no_courses_heading'] = 'No LaunchPad course yet';
$string['fellowship_xp_total_label'] = 'Total XP';
$string['fellowship_xp_title'] = 'XP from your Moodle activity record (lessons, quizzes, submissions). Does not affect grades.';
$string['fellowship_notifications'] = 'Notifications';
$string['fellowship_notifications_unread'] = 'Notifications, {$a} unread';
$string['fellowship_your_journey'] = 'Your journey';
$string['fellowship_open_track'] = 'Open track';
$string['fellowship_journey_aria'] = 'Your journey through four gates';
$string['fellowship_achievements_eyebrow'] = 'Private to you';
$string['fellowship_achievements_lead'] = 'XP, levels and badges come only from work saved to your record. No rankings, no leaderboards — your progress isn’t a competition.';
$string['fellowship_level_path'] = 'Level path';
$string['fellowship_level_now'] = 'Current level:';
$string['fellowship_xp_to_level'] = 'to';
$string['fellowship_level_top'] = 'Top level reached.';
$string['fellowship_badges_heading'] = 'Badges';
$string['fellowship_badges_of'] = 'of';
$string['fellowship_badges_earned'] = 'earned';
$string['fellowship_xp_history'] = 'XP history';
$string['fellowship_xp_history_empty'] = 'Nothing yet. Finish a lesson, quiz or assignment in Moodle to earn your first XP.';
$string['fellowship_xp_provisional_note'] = 'XP and badges refresh from your Moodle completion, quiz and assignment records.';
$string['fellowship_xp_how'] = 'How XP works';
$string['fellowship_xp_how_note'] = 'Each listed action counts once from your Moodle record. XP never affects your grade. Portfolio XP stays unavailable until that feature ships.';
$string['fellowship_xp_when'] = 'When';
$string['fellowship_xp_what'] = 'What you did';
$string['fellowship_xp_col'] = 'XP';
$string['fellowship_xp_log_lesson'] = 'Finished {$a}';
$string['fellowship_xp_log_quiz'] = 'Submitted quiz attempt · {$a}';
$string['fellowship_xp_log_quiz_perfect'] = 'Full marks on {$a}';
$string['fellowship_xp_log_quiz_correct'] = 'Correct answers on {$a}';
$string['fellowship_xp_log_assign'] = 'Submitted assignment · {$a}';
$string['fellowship_xp_log_resubmit'] = 'Resubmitted after feedback · {$a}';
$string['fellowship_xp_log_gate'] = 'Passed Foundations gate';
$string['fellowship_badge_earned_on'] = 'Earned {$a}';
$string['fellowship_badge_earned_recent'] = 'Earned';
$string['fellowship_orbit_aria'] = 'Dot Dot';
$string['fellowship_orbit_dismiss'] = 'Dismiss tip';
$string['fellowship_orbit_hide'] = 'Hide Dot Dot';
$string['fellowship_orbit_show'] = 'Show Dot Dot';
$string['fellowship_orbit_tip_home'] = 'Your <b>next activity</b> is at the top. XP comes from work saved in Moodle.';
$string['fellowship_orbit_tip_track'] = 'Open activities in Moodle. Later phases unlock when the programme releases them.';
$string['fellowship_orbit_tip_progress'] = 'Progress here uses Moodle activity completion when it is enabled.';
$string['fellowship_orbit_tip_achievements'] = 'Levels and badges update from lessons, quizzes and submissions you complete.';
$string['fellowship_orbit_tip_help'] = 'Straight answers only. Contact the programme team if you are stuck.';
$string['fellowship_orbit_tip_feedback'] = 'Mentor comments and grades live on Moodle assignments. Open an item to revise or read the full review.';
$string['fellowship_orbit_tip_phases'] = 'Each phase passes only after a released review at 70/100 or above. Weights are fixed by the programme.';
$string['fellowship_orbit_tip_completion'] = 'Every gate must reach 70. The certificate stays locked until the programme turns issuance on.';
$string['fellowship_completion_gates'] = '{$a->met} of {$a->total} gates passed';
$string['fellowship_completion_gates_note'] = 'Plus Shared orientation ✓. Every gate must reach 70.';
$string['fellowship_completion_ring_label'] = 'gates passed';
$string['fellowship_completion_met'] = 'Requirements met';
$string['fellowship_completion_outstanding'] = 'Requirements outstanding';
$string['fellowship_completion_col_req'] = 'Requirement';
$string['fellowship_completion_col_weight'] = 'Weight';
$string['fellowship_completion_col_result'] = 'Result';
$string['fellowship_completion_col_status'] = 'Status';
$string['fellowship_completion_orientation'] = 'Shared orientation';
$string['fellowship_completion_orientation_done'] = 'Complete';
$string['fellowship_completion_weighted'] = 'Weighted result';
$string['fellowship_completion_weighted_pending'] = 'Pending';
$string['fellowship_completion_weighted_ok'] = 'On track';
$string['fellowship_completion_weighted_low'] = 'Below 70';
$string['fellowship_completion_calc_note'] = 'Calculated only when every phase has a released review. Practice quizzes carry no weight.';
$string['fellowship_completion_todo'] = 'Still to do';
$string['fellowship_completion_cert_heading'] = 'Certificate';
$string['fellowship_completion_cert_title'] = 'Completion certificate';
$string['fellowship_completion_cert_na'] = 'Not available yet';
$string['fellowship_completion_cert_body'] = '<b>Certificate issuance is not available</b> yet. Your results are your record until the programme confirms the tool and you meet the criteria.';
$string['fellowship_completion_cert_disclaimer'] = 'A certificate means the published criteria were met. It doesn’t imply a job, internship or placement.';
$string['fellowship_phases_eyebrow'] = 'My record';
$string['fellowship_phases_lead'] = 'Each phase passes only after a released review at 70/100 or above.';
$string['fellowship_phases_note'] = 'A missing or unreviewed grade stays pending. A high capstone mark can’t make up for a phase that hasn’t passed.';
$string['fellowship_phase_weight'] = 'Weight';
$string['fellowship_phase_hint_foundations'] = 'Foundations evidence';
$string['fellowship_phase_hint_submission'] = 'phase submission';
$string['fellowship_phase_hint_capstone'] = 'capstone and demo';
$string['fellowship_track_eyebrow'] = 'Track course';
$string['fellowship_track_lead_default'] = 'Work through the four gates on your assigned track. Activities open in Moodle. Later phases appear when the programme releases them.';
$string['fellowship_track_blurb_1'] = 'Evidence versus assumptions, problem framing and prioritisation.';
$string['fellowship_track_blurb_2'] = 'Brief, backlog, acceptance scenarios and prototype.';
$string['fellowship_track_blurb_3'] = 'A simulated stakeholder adds capacity and accessibility constraints.';
$string['fellowship_track_blurb_4'] = 'Defend the revised roadmap; submit prototype, change log and decision pack.';
$string['fellowship_track_not_released'] = '🔒 Not released yet';
$string['fellowship_track_phase_locked'] = 'Unlocks when the programme releases this phase. {$a}% of your result.';
$string['fellowship_track_capstone'] = 'Capstone';
$string['fellowship_track_capstone_body'] = 'Brief, backlog, acceptance scenarios, prototype, change log and an evidence-linked decision memo. Stakeholder and data are <b>simulated</b> and labelled.';
$string['fellowship_track_meta_prereq'] = 'Prerequisite';
$string['fellowship_track_meta_device'] = 'Device';
$string['fellowship_track_meta_tools'] = 'Tools';
$string['fellowship_track_meta_mentor'] = 'Mentor';
$string['fellowship_track_prerequisite'] = 'Shared orientation ✓';
$string['fellowship_track_device'] = 'Phone or laptop to read and submit; a laptop helps for the prototype.';
$string['fellowship_track_tools'] = 'No paid tools required.';
$string['fellowship_track_mentor_tba'] = 'Assigned when your programme team enrols a course teacher.';
$string['fellowship_track_how_completion'] = 'How completion works';
$string['fellowship_track_how_completion_lead'] = 'Each phase is reviewed and must reach 70/100.';
$string['fellowship_feedback_eyebrow'] = 'From your mentors';
$string['fellowship_feedback_lead'] = 'Reviews come from Moodle assignments: grades, written comments, and resubmit when an attempt is reopened. Nothing here invents feedback.';
$string['fellowship_feedback_no_assigns_title'] = 'No assignments yet';
$string['fellowship_feedback_no_assigns_body'] = 'When your track has assignments, submissions and mentor reviews will appear here.';
$string['fellowship_feedback_empty_title'] = 'No released feedback yet';
$string['fellowship_feedback_empty_submit'] = 'Submit your Foundations evidence (or another track assignment) to have it reviewed. Released mentor feedback appears here.';
$string['fellowship_feedback_empty_waiting'] = 'Your submission is waiting for review. Released feedback appears here when your mentor grades it — we would rather be honest than fast.';
$string['fellowship_feedback_cta_receipt'] = 'View submission';
$string['fellowship_feedback_status_none'] = 'Not submitted';
$string['fellowship_feedback_status_pending'] = 'Submitted — review pending';
$string['fellowship_feedback_status_reviewed'] = 'Reviewed';
$string['fellowship_feedback_status_changes'] = 'Changes requested';
$string['fellowship_feedback_summary_none'] = 'Open the assignment in Moodle to submit when you are ready.';
$string['fellowship_feedback_summary_pending'] = 'Your work is with your mentor. Released grades and comments appear here.';
$string['fellowship_feedback_summary_reviewed'] = 'Your mentor has released a review. Open Moodle for the full assignment page.';
$string['fellowship_feedback_summary_changes'] = 'Your mentor reopened this attempt. Revise and resubmit in Moodle.';
$string['fellowship_feedback_attempt'] = 'Attempt';
$string['fellowship_feedback_released'] = 'Released';
$string['fellowship_feedback_cta_open'] = 'Open assignment';
$string['fellowship_feedback_cta_view'] = 'Open in Moodle';
$string['fellowship_feedback_cta_revise'] = 'Revise and resubmit';
$string['fellowship_feedback_native_note'] = 'Submit, upload files, and read full feedback history in Moodle. This list updates from Moodle each time you open Feedback — it is not a static demo.';
$string['fellowship_feedback_section_released'] = 'Released reviews';
$string['fellowship_feedback_section_open'] = 'Submissions & open assignments';
