<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_admin\output;

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * The "From Moodle" CTA cards renderable, shown at the bottom of the admin notifications page.
 *
 * @package    core_admin
 * @copyright  2026 Matt Porritt <matt.porritt@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notification_ctas implements renderable, templatable {
    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $CFG;

        if (defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING) {
            // The $CFG->disablenotificationctas setting is config.php-only and cannot be set directly
            // from Behat, so allow Behat to simulate it via a JSON-encoded config value instead,
            // mirroring the approach used for $CFG->showcampaigncontent before it was removed.
            $behatvalue = get_config('core', 'disablenotificationctas');
            $disabled = $behatvalue ? json_decode($behatvalue, true) : [];
        } else {
            $disabled = isset($CFG->disablenotificationctas) ? (array) $CFG->disablenotificationctas : [];
        }

        $ctas = [];
        foreach ($this->get_cta_definitions() as $key => $definition) {
            if (in_array($key, $disabled, true)) {
                continue;
            }

            // The feedback CTA promotes enabling site feedback, so once it is already
            // enabled there is nothing left for the card to prompt the administrator to do.
            if ($key === 'feedback' && !empty($CFG->enableuserfeedback)) {
                continue;
            }

            $cta = new stdClass();
            $cta->key = $key;
            $cta->logourl = $output->image_url($definition['logo'], 'core')->out(false);
            $cta->logoheight = $definition['logoheight'];
            $cta->logopill = $definition['logopill'];
            $cta->title = $definition['title'];
            $cta->body = $definition['body'];
            $cta->ticks = array_map(fn($tick) => (object) ['text' => $tick], $definition['ticks']);
            $cta->cta = $definition['cta'];
            $cta->caption = $definition['caption'];
            $cta->href = $definition['internal']
                ? $definition['url']
                : $this->build_tracked_cta_url($definition['url'], $key)->out(false);
            $cta->newwindow = !$definition['internal'];
            $ctas[] = $cta;
        }

        $data = new stdClass();
        $data->heading = get_string('notificationctafromhqheading', 'admin');
        $data->subtitle = get_string('notificationctafromhqsubtitle', 'admin');
        $data->ctas = $ctas;
        $data->hasctas = !empty($ctas);

        return $data;
    }

    /**
     * Return the definitions of the four "From Moodle" CTA cards.
     *
     * Card copy is final content agreed in MDL-89290 and must not be treated as placeholder text.
     *
     * Each card's brand colours are not here: they are keyed off the card's 'data-cta-key' attribute in
     * theme/boost/scss/moodle/admin.scss. Colours emitted from here would land in a style attribute, which
     * no stylesheet rule can override, so the dark colour mode could not correct them.
     *
     * @return array Card definitions keyed by CTA key.
     */
    protected function get_cta_definitions(): array {
        return [
            'marketplace' => [
                'logo' => 'notification_cta_marketplace',
                'logoheight' => 26,
                'logopill' => false,
                'title' => get_string('notificationctamarketplacetitle', 'admin'),
                'body' => get_string('notificationctamarketplacebody', 'admin'),
                'ticks' => [
                    get_string('notificationctamarketplacetick1', 'admin'),
                    get_string('notificationctamarketplacetick2', 'admin'),
                    get_string('notificationctamarketplacetick3', 'admin'),
                ],
                'cta' => get_string('notificationctamarketplacecta', 'admin'),
                'caption' => get_string('notificationctamarketplacecaption', 'admin'),
                'url' => 'https://marketplace.moodle.com/',
                'internal' => false,
            ],
            'moodlecloud' => [
                'logo' => 'notification_cta_moodlecloud',
                'logoheight' => 30,
                'logopill' => false,
                'title' => get_string('notificationctamoodlecloudtitle', 'admin'),
                'body' => get_string('notificationctamoodlecloudbody', 'admin'),
                'ticks' => [
                    get_string('notificationctamoodlecloudtick1', 'admin'),
                    get_string('notificationctamoodlecloudtick2', 'admin'),
                    get_string('notificationctamoodlecloudtick3', 'admin'),
                ],
                'cta' => get_string('notificationctamoodlecloudcta', 'admin'),
                'caption' => get_string('notificationctamoodlecloudcaption', 'admin'),
                'url' => 'https://www.moodlecloud.com/standard-plans/',
                'internal' => false,
            ],
            'partners' => [
                'logo' => 'notification_cta_partners',
                'logoheight' => 46,
                'logopill' => false,
                'title' => get_string('notificationctapartnerstitle', 'admin'),
                'body' => get_string('notificationctapartnersbody', 'admin'),
                'ticks' => [
                    get_string('notificationctapartnerstick1', 'admin'),
                    get_string('notificationctapartnerstick2', 'admin'),
                    get_string('notificationctapartnerstick3', 'admin'),
                ],
                'cta' => get_string('notificationctapartnerscta', 'admin'),
                'caption' => get_string('notificationctapartnerscaption', 'admin'),
                'url' => 'https://moodle.com/services/certified-service-providers/',
                'internal' => false,
            ],
            'feedback' => [
                'logo' => 'moodlelogo',
                'logoheight' => 28,
                'logopill' => true,
                'title' => get_string('notificationctafeedbacktitle', 'admin'),
                'body' => get_string('notificationctafeedbackbody', 'admin'),
                'ticks' => [
                    get_string('notificationctafeedbacktick1', 'admin'),
                    get_string('notificationctafeedbacktick2', 'admin'),
                    get_string('notificationctafeedbacktick3', 'admin'),
                ],
                'cta' => get_string('notificationctafeedbackcta', 'admin'),
                'caption' => get_string('notificationctafeedbackcaption', 'admin'),
                'url' => (new moodle_url('/admin/settings.php', ['section' => 'userfeedback']))->out(false),
                'internal' => true,
            ],
        ];
    }

    /**
     * Append the site-identification param to a CTA URL, following the same pattern already
     * used and tested for the "Browse new plugins" link
     * (see tool_installaddon_installer::get_external_service_url()), so the marketplace can
     * match on site URL instead of relying on a new registration-based lookup.
     *
     * A minimal utm_source/utm_campaign pair is also kept for on-site analytics; utm_medium,
     * version and a separate site-identifier hash are no longer sent, since the site param
     * already carries the site URL and Moodle version.
     *
     * @param string $url The base CTA URL.
     * @param string $ctakey The CTA key, used as the utm_campaign value.
     * @return moodle_url
     */
    protected function build_tracked_cta_url(string $url, string $ctakey): moodle_url {
        global $SITE, $CFG;

        $site = base64_encode(json_encode([
            'fullname' => strip_tags($SITE->fullname),
            'url' => $CFG->wwwroot,
            'majorversion' => moodle_major_version(),
        ]));

        return new moodle_url($url, [
            'site' => $site,
            'utm_source' => 'moodle_admin',
            'utm_campaign' => $ctakey,
        ]);
    }
}
