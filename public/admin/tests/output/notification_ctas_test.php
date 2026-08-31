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

namespace core_admin\output;

/**
 * Unit tests for the "From Moodle" notification CTA cards.
 *
 * @package     core_admin
 * @copyright   2026 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\PHPUnit\Framework\Attributes\CoversClass(notification_ctas::class)]
final class notification_ctas_test extends \advanced_testcase {
    /**
     * All four cards are shown by default, in a fixed order, with tracked hrefs.
     */
    public function test_export_for_template_default(): void {
        global $PAGE;
        $this->resetAfterTest();

        $ctas = new notification_ctas();
        $data = $ctas->export_for_template($PAGE->get_renderer('core'));

        $this->assertTrue($data->hasctas);
        $this->assertCount(4, $data->ctas);
        $this->assertSame(
            ['marketplace', 'moodlecloud', 'partners', 'feedback'],
            array_map(fn($cta) => $cta->key, $data->ctas)
        );

        foreach ($data->ctas as $cta) {
            $this->assertNotEmpty($cta->title);
            $this->assertNotEmpty($cta->body);
            $this->assertCount(3, $cta->ticks);
            $this->assertNotEmpty($cta->cta);
            $this->assertNotEmpty($cta->caption);
            $this->assertIsInt($cta->logoheight);
            $this->assertGreaterThan(0, $cta->logoheight);
            $this->assertIsBool($cta->logopill);
        }
    }

    /**
     * The external marketplace-style CTAs (marketplace, moodlecloud, partners) carry a base64
     * "site" param following the same pattern already used and tested for the "Browse new
     * plugins" link (see tool_installaddon_installer::get_external_service_url()), so the
     * marketplace can match on site URL instead of a new registration-based lookup. A minimal
     * utm_source/utm_campaign pair is also kept for on-site analytics.
     */
    public function test_marketplace_ctas_carry_site_info_and_utm_params(): void {
        global $CFG, $SITE, $PAGE;
        $this->resetAfterTest();

        $ctas = new notification_ctas();
        $data = $ctas->export_for_template($PAGE->get_renderer('core'));
        $bykey = [];
        foreach ($data->ctas as $cta) {
            $bykey[$cta->key] = $cta;
        }

        foreach (['marketplace', 'moodlecloud', 'partners'] as $key) {
            $query = [];
            parse_str((string) parse_url($bykey[$key]->href, PHP_URL_QUERY), $query);

            $this->assertSame(['site', 'utm_source', 'utm_campaign'], array_keys($query));
            $this->assertSame('moodle_admin', $query['utm_source']);
            $this->assertSame($key, $query['utm_campaign']);

            $site = json_decode(base64_decode($query['site']), true);
            $this->assertSame(strip_tags($SITE->fullname), $site['fullname']);
            $this->assertSame($CFG->wwwroot, $site['url']);
            $this->assertSame(moodle_major_version(), $site['majorversion']);
        }

        $this->assertStringStartsWith('https://marketplace.moodle.com/', $bykey['marketplace']->href);
        $this->assertStringStartsWith('https://www.moodlecloud.com/standard-plans/', $bykey['moodlecloud']->href);
        $this->assertStringStartsWith(
            'https://moodle.com/services/certified-service-providers/',
            $bykey['partners']->href
        );
    }

    /**
     * The external cards (marketplace, moodlecloud, partners) open in a new window, since they
     * leave the site; the internal feedback card stays in the same window.
     */
    public function test_external_ctas_open_in_new_window(): void {
        global $PAGE;
        $this->resetAfterTest();

        $ctas = new notification_ctas();
        $data = $ctas->export_for_template($PAGE->get_renderer('core'));
        $bykey = [];
        foreach ($data->ctas as $cta) {
            $bykey[$cta->key] = $cta;
        }

        $this->assertTrue($bykey['marketplace']->newwindow);
        $this->assertTrue($bykey['moodlecloud']->newwindow);
        $this->assertTrue($bykey['partners']->newwindow);
        $this->assertFalse($bykey['feedback']->newwindow);
    }

    /**
     * The feedback card links straight to the internal admin settings page, with no marketplace
     * tracking params, since it never leaves the site.
     */
    public function test_feedback_cta_has_no_tracking_params(): void {
        global $PAGE;
        $this->resetAfterTest();

        $ctas = new notification_ctas();
        $data = $ctas->export_for_template($PAGE->get_renderer('core'));
        $bykey = [];
        foreach ($data->ctas as $cta) {
            $bykey[$cta->key] = $cta;
        }

        $query = [];
        parse_str((string) parse_url($bykey['feedback']->href, PHP_URL_QUERY), $query);
        $this->assertSame(['section'], array_keys($query));
        $this->assertStringEndsWith('/admin/settings.php', parse_url($bykey['feedback']->href, PHP_URL_PATH));
    }

    /**
     * No colour reaches the template.
     *
     * The card colours are keyed off data-cta-key in theme/boost/scss/moodle/admin.scss instead. A colour
     * exported from here would end up in a style attribute, which no stylesheet rule can override, so the
     * dark colour mode would have no way to correct it.
     */
    public function test_export_for_template_carries_no_colours(): void {
        global $PAGE;
        $this->resetAfterTest();

        $ctas = new notification_ctas();
        $data = $ctas->export_for_template($PAGE->get_renderer('core'));

        foreach ($data->ctas as $cta) {
            $values = get_object_vars($cta);
            $values['ticks'] = implode(' ', array_map(fn($tick) => $tick->text, $cta->ticks));

            foreach ($values as $name => $value) {
                $this->assertDoesNotMatchRegularExpression(
                    '/#[0-9a-f]{3,8}\b|\brgba?\(|\bhsla?\(/i',
                    (string) $value,
                    "The '{$name}' value of the '{$cta->key}' CTA looks like a colour."
                );
            }
        }
    }

    /**
     * Each card's logo size matches the reference design, and the Feedback card's orange
     * logo sits inside a white "pill" so it stays legible against its orange band.
     */
    public function test_logo_sizing_and_pill_match_reference_design(): void {
        global $PAGE;
        $this->resetAfterTest();

        $ctas = new notification_ctas();
        $data = $ctas->export_for_template($PAGE->get_renderer('core'));
        $bykey = [];
        foreach ($data->ctas as $cta) {
            $bykey[$cta->key] = $cta;
        }

        $this->assertSame(26, $bykey['marketplace']->logoheight);
        $this->assertSame(30, $bykey['moodlecloud']->logoheight);
        $this->assertSame(46, $bykey['partners']->logoheight);
        $this->assertSame(28, $bykey['feedback']->logoheight);

        $this->assertFalse($bykey['marketplace']->logopill);
        $this->assertFalse($bykey['moodlecloud']->logopill);
        $this->assertFalse($bykey['partners']->logopill);
        $this->assertTrue($bykey['feedback']->logopill);
    }

    /**
     * Keys listed in $CFG->disablenotificationctas hide only the matching card.
     */
    public function test_disablenotificationctas_hides_selected_cards(): void {
        global $CFG, $PAGE;
        $this->resetAfterTest();

        $CFG->disablenotificationctas = ['moodlecloud', 'partners'];

        $ctas = new notification_ctas();
        $data = $ctas->export_for_template($PAGE->get_renderer('core'));

        $this->assertTrue($data->hasctas);
        $this->assertSame(
            ['marketplace', 'feedback'],
            array_map(fn($cta) => $cta->key, $data->ctas)
        );
    }

    /**
     * Disabling all four CTA keys results in no cards being shown.
     */
    public function test_disablenotificationctas_can_hide_all_cards(): void {
        global $CFG, $PAGE;
        $this->resetAfterTest();

        $CFG->disablenotificationctas = ['marketplace', 'moodlecloud', 'partners', 'feedback'];

        $ctas = new notification_ctas();
        $data = $ctas->export_for_template($PAGE->get_renderer('core'));

        $this->assertFalse($data->hasctas);
        $this->assertCount(0, $data->ctas);
    }

    /**
     * The feedback card promotes enabling site feedback, so once $CFG->enableuserfeedback is
     * already on there is nothing left for the card to prompt the administrator to do, and it
     * must not be shown.
     */
    public function test_feedback_cta_hidden_when_enableuserfeedback_is_enabled(): void {
        global $CFG, $PAGE;
        $this->resetAfterTest();

        $CFG->enableuserfeedback = 1;

        $ctas = new notification_ctas();
        $data = $ctas->export_for_template($PAGE->get_renderer('core'));

        $this->assertSame(
            ['marketplace', 'moodlecloud', 'partners'],
            array_map(fn($cta) => $cta->key, $data->ctas)
        );
    }

    /**
     * $CFG->disablenotificationctas can still hide the feedback card outright, independently of
     * $CFG->enableuserfeedback.
     */
    public function test_disablenotificationctas_overrides_feedback_cta_regardless_of_enableuserfeedback(): void {
        global $CFG, $PAGE;
        $this->resetAfterTest();

        $CFG->enableuserfeedback = 0;
        $CFG->disablenotificationctas = ['feedback'];

        $ctas = new notification_ctas();
        $data = $ctas->export_for_template($PAGE->get_renderer('core'));

        $this->assertSame(
            ['marketplace', 'moodlecloud', 'partners'],
            array_map(fn($cta) => $cta->key, $data->ctas)
        );
    }
}
