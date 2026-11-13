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

declare(strict_types=1);

namespace core_admin;

/**
 * Unit tests for the core_admin_renderer notifications page.
 *
 * @package     core_admin
 * @copyright   2026 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\core_admin_renderer::class)]
final class renderer_test extends \advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();
        global $CFG;
        require_once($CFG->libdir . '/adminlib.php');
    }

    /**
     * Clear the static registration cache so each test sees the current database state.
     *
     * \core\hub\registration caches the registration record in a static property that is not
     * reset between tests, so without this a record from an earlier test leaks into this one.
     */
    protected function setUp(): void {
        parent::setUp();

        $property = new \ReflectionProperty(\core\hub\registration::class, 'registration');
        $property->setValue(null, null);
    }

    /**
     * Get a core_admin_renderer instance.
     *
     * @return \core_admin_renderer
     */
    protected function get_renderer(): \core_admin_renderer {
        global $PAGE;
        return $PAGE->get_renderer('core', 'admin');
    }

    /**
     * Register the site locally so \core\hub\registration::is_registered() returns true.
     */
    private function register_site(): void {
        global $CFG, $DB;

        // Paused-reporting warnings only apply to publicly accessible sites. Pin the value rather than
        // letting site_is_public() resolve the test wwwroot, so these tests do not depend on host resolution.
        $CFG->site_is_public = true;
        $DB->insert_record('registration_hubs', [
            'token' => 'abc123',
            'hubname' => 'Moodle.org',
            'huburl' => HUB_MOODLEORGHUBURL,
            'confirmed' => 1,
            'secret' => 'secret123',
            'timemodified' => time(),
        ]);
        set_config('site_regupdateversion', max(array_keys(\core\hub\registration::CONFIRM_NEW_FIELDS)), 'hub');
    }

    /**
     * Call notifications_page() with all notification triggers disabled by default,
     * only overriding the values passed in $overrides.
     *
     * @param array $overrides
     * @return string
     */
    protected function render_notifications_page(array $overrides = []): string {
        $params = array_merge([
            'maturity' => MATURITY_STABLE,
            'insecuredataroot' => false,
            'errorsdisplayed' => false,
            'cronoverdue' => false,
            'dbproblems' => false,
            'maintenancemode' => false,
            'availableupdates' => null,
            'availableupdatesfetch' => null,
            'buggyiconvnomb' => false,
            'registered' => true,
            'cachewarnings' => [],
            'eventshandlers' => 0,
            'themedesignermode' => false,
            'devlibdir' => false,
            'mobileconfigured' => true,
            'overridetossl' => false,
            'invalidforgottenpasswordurl' => false,
            'croninfrequent' => false,
            'xmlrpcwarning' => '',
        ], $overrides);

        return $this->get_renderer()->notifications_page(...array_values($params));
    }

    /**
     * Notifications are grouped and ordered danger, then warning, then notice, with an accurate
     * severity count summary line, matching the MDL-89290 acceptance criteria.
     */
    public function test_notifications_grouped_and_ordered_by_severity(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->disableupdatenotifications = true;

        $output = $this->render_notifications_page([
            'devlibdir' => true,
            'cronoverdue' => true,
            'errorsdisplayed' => true,
        ]);

        // Summary line shows "1 critical · 2 warnings".
        $this->assertStringContainsString(
            get_string('notificationsummarycritical', 'admin', 1)
                . get_string('notificationsummaryseparator', 'admin')
                . get_string('notificationsummarywarningplural', 'admin', 2),
            $output
        );

        // The danger box appears before the warning boxes, which appear before the "From Moodle" CTAs.
        $dangerpos = strpos($output, 'alert-danger');
        $warningpos = strpos($output, 'alert-warning');
        $ctapos = strpos($output, get_string('notificationctafromhqheading', 'admin'));

        $this->assertNotFalse($dangerpos);
        $this->assertNotFalse($warningpos);
        $this->assertNotFalse($ctapos);
        $this->assertLessThan($warningpos, $dangerpos);
        $this->assertLessThan($ctapos, $warningpos);

        // No notice-level items were triggered in this scenario.
        $this->assertStringNotContainsString(get_string('notificationsummarynotice', 'admin', 1), $output);
    }

    /**
     * A severity group with zero items is omitted from the summary line entirely.
     */
    public function test_notifications_summary_omits_empty_groups(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->disableupdatenotifications = true;

        $output = $this->render_notifications_page(['cronoverdue' => true]);

        $this->assertStringContainsString(get_string('notificationsummarywarning', 'admin', 1), $output);
        $this->assertStringNotContainsString('critical', $output);
        $this->assertStringNotContainsString(get_string('notificationsummarynotice', 'admin', 1), $output);
    }

    /**
     * When there are no notifications at all (every check passes), the summary line and the
     * notification list are both omitted entirely, but the heading and "From Moodle" CTAs still
     * render correctly.
     */
    public function test_no_notifications_at_all_renders_correctly(): void {
        global $CFG;
        $this->resetAfterTest();
        $CFG->disableupdatenotifications = true;

        $output = $this->render_notifications_page();

        // No summary line is rendered at all: no severity counts, and no empty wrapper markup.
        // (The separator string itself is not checked for absence, since it is reused as plain
        // punctuation in some "From Moodle" CTA captions, unrelated to the summary line.)
        $this->assertStringNotContainsString('notification-summary', $output);

        // No notification alert boxes are rendered.
        $this->assertStringNotContainsString('alert-danger', $output);
        $this->assertStringNotContainsString('alert-warning', $output);
        $this->assertStringNotContainsString('alert-info', $output);

        // The heading and the "From Moodle" CTAs still render.
        $this->assertStringContainsString(get_string('notifications', 'admin'), $output);
        $this->assertStringContainsString(get_string('notificationctafromhqheading', 'admin'), $output);
    }

    /**
     * Cron warnings are not rendered when the cron check reports cron is running as expected.
     */
    public function test_cron_warning_not_rendered_when_cron_check_ok(): void {
        global $CFG;

        $this->resetAfterTest();

        $CFG->expectedcronfrequency = MINSECS;
        $CFG->disableupdatenotifications = true;

        // Last cron start is within the expected frequency plus the grace period.
        set_config('lastcronstart', time() - 90, 'tool_task');
        set_config('lastcroninterval', MINSECS, 'tool_task');

        $output = $this->render_notifications_page(['cronoverdue' => true, 'croninfrequent' => true]);

        $this->assertStringNotContainsString(get_string('cronok', 'tool_task'), $output);
        $this->assertStringNotContainsString('notification-summary', $output);
        $this->assertStringNotContainsString('alert-warning', $output);
    }

    /**
     * Cron infrequent warning is rendered when the cron check reports cron is not running frequently.
     */
    public function test_cron_infrequent_warning_rendered(): void {
        global $CFG;

        $this->resetAfterTest();

        $CFG->expectedcronfrequency = MINSECS;
        $CFG->disableupdatenotifications = true;

        set_config('lastcronstart', time() - 90, 'tool_task');
        set_config('lastcroninterval', 5 * MINSECS, 'tool_task');

        $output = $this->render_notifications_page(['croninfrequent' => true]);

        $this->assertStringContainsString(get_string('notificationsummarywarning', 'admin', 1), $output);
        $this->assertStringContainsString('alert-warning', $output);
        $this->assertStringContainsString(get_string('croninfrequent', 'admin', [
            'actual' => format_time(5 * MINSECS),
            'expected' => format_time(MINSECS),
        ]), $output);
    }

    /**
     * The available-updates check renders as a notice, per the MDL-89290 testing instructions.
     */
    public function test_available_updates_is_a_notice(): void {
        $this->resetAfterTest();

        $output = $this->render_notifications_page([
            'availableupdates' => ['core' => null],
            'availableupdatesfetch' => time() - (8 * DAYSECS),
        ]);

        $this->assertStringContainsString(get_string('notificationsummarynotice', 'admin', 1), $output);
        $this->assertStringNotContainsString('critical', $output);
        $this->assertStringNotContainsString(get_string('notificationsummarywarning', 'admin', 1), $output);
    }

    /**
     * The available-updates notice always has explanatory copy, even when there are no
     * updates and the last check wasn't recent - it must not render as a bare button.
     */
    public function test_available_updates_copy_when_none_available(): void {
        $fetch = time() - (8 * DAYSECS);
        $output = $this->render_notifications_page([
            'availableupdates' => ['core' => null],
            'availableupdatesfetch' => $fetch,
        ]);

        $this->assertStringContainsString(
            get_string('notificationctaupdateschecked', 'admin', format_time(time() - $fetch)),
            $output
        );
    }

    /**
     * The available-updates notice has explanatory copy even when a check has never run.
     */
    public function test_available_updates_copy_when_never_checked(): void {
        $output = $this->render_notifications_page([
            'availableupdates' => ['core' => null],
            'availableupdatesfetch' => null,
        ]);

        $this->assertStringContainsString(get_string('notificationctaupdatesnotchecked', 'admin'), $output);
    }

    /**
     * The available-updates notice keeps showing the last-checked text alongside the "check for
     * updates" button when an update is available, matching the pre-MDL-89290 behaviour - it must
     * not be suppressed just because an update is on offer.
     */
    public function test_available_updates_shows_last_checked_when_updates_available(): void {
        $fetch = time() - (8 * DAYSECS);
        $output = $this->render_notifications_page([
            'availableupdates' => ['core' => [new \core\update\info('core', ['version' => 2026080100])]],
            'availableupdatesfetch' => $fetch,
        ]);

        $this->assertStringContainsString(
            get_string('notificationctaupdateschecked', 'admin', format_time(time() - $fetch)),
            $output
        );
    }

    /**
     * When there is no update available, the explanatory text and the "check for updates"
     * button sit on a single line, like every other notification item (no <p> or extra
     * margin-top wrapper forcing it onto its own separate lines).
     */
    public function test_available_updates_renders_on_a_single_line(): void {
        $output = $this->render_notifications_page([
            'availableupdates' => ['core' => null],
            'availableupdatesfetch' => null,
        ]);

        $this->assertMatchesRegularExpression(
            '/' . preg_quote(get_string('notificationctaupdatesnotchecked', 'admin'), '/') . '&nbsp;<div class="singlebutton">/',
            $output
        );
    }

    /**
     * The legacy Partner/donation/marketplace-notice/feedback-notice content no longer appears.
     */
    public function test_legacy_promotional_content_removed(): void {
        $this->resetAfterTest();

        $output = $this->render_notifications_page();

        $this->assertStringNotContainsString('campaign-content', $output);
        $this->assertStringNotContainsString('services-support-content', $output);
    }

    /**
     * The "From Moodle" CTA grid is always rendered after the notification list.
     */
    public function test_from_moodle_ctas_rendered(): void {
        $this->resetAfterTest();

        $output = $this->render_notifications_page();

        $this->assertStringContainsString(get_string('notificationctafromhqheading', 'admin'), $output);
        $this->assertStringContainsString(get_string('notificationctamarketplacetitle', 'admin'), $output);
        $this->assertStringContainsString(get_string('notificationctamoodlecloudtitle', 'admin'), $output);
        $this->assertStringContainsString(get_string('notificationctapartnerstitle', 'admin'), $output);
        $this->assertStringContainsString(get_string('notificationctafeedbacktitle', 'admin'), $output);
    }

    /**
     * Each notification item is prefixed with a bold category label matching its severity,
     * for example "Security", "Critical", "Warning" or "Notice" (no icon, per MDL-89290 scope).
     */
    public function test_notification_items_have_bold_category_labels(): void {
        $this->resetAfterTest();

        $output = $this->render_notifications_page([
            'devlibdir' => true,
            'cronoverdue' => true,
            'availableupdates' => ['core' => null],
            'availableupdatesfetch' => time() - (8 * DAYSECS),
        ]);

        $securitylabel = \html_writer::tag('strong', get_string('notificationlabelsecurity', 'admin'), [
            'class' => 'notification-label-danger',
        ]);
        $warninglabel = \html_writer::tag('strong', get_string('notificationlabelwarning', 'admin'), [
            'class' => 'notification-label-warning',
        ]);
        $noticelabel = \html_writer::tag('strong', get_string('notificationlabelnotice', 'admin'), [
            'class' => 'notification-label-notice',
        ]);

        $this->assertStringContainsString($securitylabel, $output);
        $this->assertStringContainsString($warninglabel, $output);
        $this->assertStringContainsString($noticelabel, $output);
    }

    /**
     * The deprecated admin_notifications_page() still renders the page, emits a deprecation notice, and
     * ignores the three arguments that notifications_page() no longer takes.
     */
    public function test_admin_notifications_page_deprecated(): void {
        $this->resetAfterTest();

        $renderer = $this->get_renderer();
        $output = $renderer->admin_notifications_page(
            MATURITY_STABLE,
            false,
            false,
            false,
            false,
            false,
            null,
            null,
            false,
            true,
            [],
            0,
            false,
            false,
            true,
            false,
            false,
            false,
            true,
            true,
            true,
            ''
        );

        $this->assertDebuggingCalled();
        $this->assertStringContainsString(get_string('notifications', 'admin'), $output);
        // The removed arguments must not bring back any of the retired banners.
        $this->assertStringNotContainsString('campaign-content', $output);
        $this->assertStringNotContainsString('services-support-content', $output);
        // The replacement call to action cards are rendered instead.
        $this->assertStringContainsString('admin-notification-ctas', $output);
    }

    /**
     * A registered site that is reporting normally shows no registration warning.
     */
    public function test_warn_if_not_registered_reporting_normally(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->register_site();

        $this->assertSame('', $this->get_renderer()->warn_if_not_registered());
    }

    /**
     * A registered site with unconfirmed new registration fields shows no warning here.
     *
     * Every page rendering this warning for a registered site also calls
     * \core\hub\registration::registration_reminder(), which redirects the admin to the registration form
     * for that cause before this warning could render.
     */
    public function test_warn_if_not_registered_new_fields_pending(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->register_site();
        // Reset to before the first CONFIRM_NEW_FIELDS entry, so every new field is pending confirmation.
        set_config('site_regupdateversion', 0, 'hub');

        $this->assertSame(
            \core\hub\registration::REPORTING_PAUSED_NEW_FIELDS,
            \core\hub\registration::get_reporting_paused_reason(),
        );
        $this->assertSame('', $this->get_renderer()->warn_if_not_registered());
    }

    /**
     * A registered site with the registration cron task disabled shows a persistent warning.
     */
    public function test_warn_if_not_registered_task_disabled(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->register_site();

        $task = \core\task\manager::get_scheduled_task(\core\task\registration_cron_task::class);
        $task->set_disabled(true);
        \core\task\manager::configure_scheduled_task($task);

        $warning = $this->get_renderer()->warn_if_not_registered();
        $this->assertStringContainsString(get_string('registrationreportingpausedtaskdisabled', 'admin'), $warning);
        $this->assertStringContainsString('alert-warning', $warning);
        $this->assertStringNotContainsString('alert-danger', $warning);
    }

    /**
     * The task-disabled paused-reporting warning is grouped in the "warning" severity band on the
     * Notifications page, not "notice", so it appears alongside other warning-level items.
     */
    public function test_registration_warning_severity_task_disabled_is_warning(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();
        $this->register_site();
        $CFG->disableupdatenotifications = true;

        $task = \core\task\manager::get_scheduled_task(\core\task\registration_cron_task::class);
        $task->set_disabled(true);
        \core\task\manager::configure_scheduled_task($task);

        $output = $this->render_notifications_page();

        $this->assertStringContainsString(get_string('notificationsummarywarning', 'admin', 1), $output);
        $this->assertStringNotContainsString(get_string('notificationsummarynotice', 'admin', 1), $output);
    }

    /**
     * A site that is not publicly accessible is not warned about paused reporting.
     */
    public function test_warn_if_not_registered_task_disabled_not_public(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $this->register_site();
        $CFG->site_is_public = false;

        $task = \core\task\manager::get_scheduled_task(\core\task\registration_cron_task::class);
        $task->set_disabled(true);
        \core\task\manager::configure_scheduled_task($task);

        $this->assertSame('', $this->get_renderer()->warn_if_not_registered());
    }

    /**
     * An unregistered public site still shows the original registration warning.
     */
    public function test_warn_if_not_registered_unregistered(): void {
        global $CFG;

        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->site_is_public = true;

        $warning = $this->get_renderer()->warn_if_not_registered();
        $this->assertStringContainsString(get_string('registrationwarning', 'admin'), $warning);
    }

    /**
     * A user without moodle/site:config (for example a Manager, who has moodle/site:configview and can
     * reach /admin/search.php) must not see the task-disabled paused-reporting warning: only admins who
     * can act on it should see it.
     */
    public function test_warn_if_not_registered_task_disabled_no_capability(): void {
        global $DB;

        $this->resetAfterTest();
        $this->register_site();

        $manageruser = $this->getDataGenerator()->create_user();
        $managerrole = $DB->get_record('role', ['shortname' => 'manager']);
        role_assign($managerrole->id, $manageruser->id, \context_system::instance()->id);
        $this->setUser($manageruser);

        $task = \core\task\manager::get_scheduled_task(\core\task\registration_cron_task::class);
        $task->set_disabled(true);
        \core\task\manager::configure_scheduled_task($task);

        $this->assertSame('', $this->get_renderer()->warn_if_not_registered());
    }

    /**
     * A user without moodle/site:config (for example a Manager, who has moodle/site:configview) must not
     * have the registration warning bumped to "warning" severity on the Notifications page when the
     * registration cron task is disabled: registration_warning_severity() gates that severity bump on the
     * same capability, so it should still classify as the default "notice" for this user.
     */
    public function test_registration_warning_severity_task_disabled_no_capability(): void {
        global $CFG, $DB;

        $this->resetAfterTest();
        $this->register_site();
        $CFG->disableupdatenotifications = true;

        $manageruser = $this->getDataGenerator()->create_user();
        $managerrole = $DB->get_record('role', ['shortname' => 'manager']);
        role_assign($managerrole->id, $manageruser->id, \context_system::instance()->id);
        $this->setUser($manageruser);

        $task = \core\task\manager::get_scheduled_task(\core\task\registration_cron_task::class);
        $task->set_disabled(true);
        \core\task\manager::configure_scheduled_task($task);

        $output = $this->render_notifications_page();

        $this->assertStringNotContainsString(get_string('notificationsummarywarning', 'admin', 1), $output);
    }
}
