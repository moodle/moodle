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

namespace mod_forum\output;

use advanced_testcase;
use moodle_url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Tests for the subscription_actionbar renderable.
 *
 * @package    mod_forum
 * @copyright  2026 Rajneel Totaram <rajneel.totaram@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[CoversClass(subscription_actionbar::class)]
final class subscription_actionbar_test extends advanced_testcase {
    #[\Override]
    public static function setUpBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/forum/lib.php');
        parent::setUpBeforeClass();
    }

    /**
     * Create a forum and export the action bar for it.
     *
     * @param int $subscriptionmode The forum subscription mode.
     * @param int $edit Whether the manage subscribers view is shown.
     * @return array The forum and the exported template data.
     */
    private function export_actionbar(int $subscriptionmode, int $edit): array {
        global $PAGE;

        $course = $this->getDataGenerator()->create_course();
        $forum = $this->getDataGenerator()->create_module('forum', [
            'course' => $course->id,
            'forcesubscribe' => $subscriptionmode,
        ]);

        $url = new moodle_url('/mod/forum/subscribers.php', ['id' => $forum->cmid]);
        $actionbar = new subscription_actionbar($forum->cmid, $url, $forum, $edit);
        return [$forum, $actionbar->export_for_template($PAGE->get_renderer('mod_forum'))];
    }

    /**
     * Data provider for the view and manage subscribers selector tests.
     *
     * @return array
     */
    public static function view_manage_provider(): array {
        return [
            'View subscribers' => [0, 'off', 'forum:viewsubscribers'],
            'Manage subscribers' => [1, 'on', 'managesubscriptionson'],
        ];
    }

    /**
     * Both the select_menu data and the deprecated url_select data for the view and manage
     * subscribers selector are exported, with the current page selected.
     *
     * @param int $edit Whether the manage subscribers view is shown.
     * @param string $selectededit The edit parameter of the selected option URL.
     * @param string $selectedstring The string identifier of the selected option name.
     */
    #[DataProvider('view_manage_provider')]
    public function test_export_for_template_view_manage(int $edit, string $selectededit, string $selectedstring): void {
        $this->resetAfterTest();

        [$forum, $data] = $this->export_actionbar(FORUM_CHOOSESUBSCRIBE, $edit);

        $viewurl = (new moodle_url('/mod/forum/subscribers.php', ['id' => $forum->cmid, 'edit' => 'off']))->out(false);
        $manageurl = (new moodle_url('/mod/forum/subscribers.php', ['id' => $forum->cmid, 'edit' => 'on']))->out(false);
        $selectedurl = $selectededit === 'off' ? $viewurl : $manageurl;
        $expectedoptions = [
            $viewurl => get_string('forum:viewsubscribers', 'forum'),
            $manageurl => get_string('managesubscriptionson', 'forum'),
        ];

        // The select_menu data used by the core template.
        $this->assertArrayHasKey('viewandmanageselectmenu', $data);
        $menu = $data['viewandmanageselectmenu'];
        $this->assertEquals('selectviewandmanagesubscribers', $menu->name);
        $this->assertEquals(get_string('subscribers', 'forum'), $menu->label);
        $this->assertEquals($selectedurl, $menu->value);
        $this->assertEquals(get_string($selectedstring, 'forum'), $menu->selectedoption);
        $this->assertEquals($expectedoptions, array_column($menu->options, 'name', 'value'));
        $this->assertEquals([$selectedurl], array_column(array_filter($menu->options, fn($o) => $o['selected']), 'value'));

        // The deprecated url_select data.
        $this->assertArrayHasKey('viewandmanageselect', $data);
        $legacy = $data['viewandmanageselect'];
        $this->assertEquals('selectviewandmanagesubscribers', $legacy->formid);
        $this->assertEquals(get_string('subscribers', 'forum'), $legacy->label);
        $this->assertEquals((new moodle_url('/course/jumpto.php'))->out(false), $legacy->action);
        // The url_select options are relative to wwwroot, as expected by course/jumpto.php.
        $expectedlocaloptions = array_combine(
            array_map(fn($url) => (new moodle_url($url))->out_as_local_url(false), array_keys($expectedoptions)),
            $expectedoptions,
        );
        $selectedlocalurl = (new moodle_url($selectedurl))->out_as_local_url(false);
        $this->assertEquals($expectedlocaloptions, array_column($legacy->options, 'name', 'value'));
        $this->assertEquals([$selectedlocalurl], array_column(array_filter($legacy->options, fn($o) => $o['selected']), 'value'));
    }

    /**
     * The subscription mode selector is only exported on the view subscribers page.
     *
     * @param int $edit Whether the manage subscribers view is shown.
     */
    #[DataProvider('view_manage_provider')]
    public function test_export_for_template_subscription_options(int $edit): void {
        $this->resetAfterTest();

        [, $data] = $this->export_actionbar(FORUM_CHOOSESUBSCRIBE, $edit);

        if ($edit) {
            $this->assertArrayNotHasKey('subscriptionoptions', $data);
        } else {
            $this->assertArrayHasKey('subscriptionoptions', $data);
            $this->assertEquals('selectsubscriptionoptions', $data['subscriptionoptions']->formid);
        }
    }

    /**
     * Neither view and manage subscribers key is exported when the forum uses forced subscription.
     *
     * @param int $edit Whether the manage subscribers view is shown.
     */
    #[DataProvider('view_manage_provider')]
    public function test_export_for_template_omits_view_manage_when_forced(int $edit): void {
        $this->resetAfterTest();

        [, $data] = $this->export_actionbar(FORUM_FORCESUBSCRIBE, $edit);

        $this->assertArrayNotHasKey('viewandmanageselectmenu', $data);
        $this->assertArrayNotHasKey('viewandmanageselect', $data);
        $this->assertArrayHasKey('subscriptionoptions', $data);
    }

    /**
     * The core template only renders the select_menu, and rendering the deprecated url_select
     * data alongside it (as an override might) does not produce duplicate ids.
     */
    public function test_render_has_unique_ids(): void {
        global $PAGE;

        $this->resetAfterTest();

        [, $data] = $this->export_actionbar(FORUM_CHOOSESUBSCRIBE, 0);
        $output = $PAGE->get_renderer('mod_forum');

        // The core template renders the tertiary navigation selector, not the url_select.
        $html = $output->render_from_template('mod_forum/forum_subscription_action', $data);
        $this->assertStringContainsString('tertiary-navigation-selector', $html);
        $this->assertStringNotContainsString('id="selectviewandmanagesubscribers"', $html);

        // An override rendering the deprecated data too.
        $html .= $output->render_from_template('core/url_select', $data['viewandmanageselect']);
        $this->assertStringContainsString('id="selectviewandmanagesubscribers"', $html);

        $doc = new \DOMDocument();
        $doc->loadHTML('<!DOCTYPE html><html><body>' . $html . '</body></html>', LIBXML_NOERROR);
        $ids = [];
        foreach ((new \DOMXPath($doc))->query('//*[@id]') as $element) {
            $ids[] = $element->getAttribute('id');
        }
        $this->assertNotEmpty($ids);
        $this->assertEquals($ids, array_unique($ids));
    }
}
