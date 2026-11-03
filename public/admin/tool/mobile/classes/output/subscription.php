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
 * Subscription page.
 *
 * @package   tool_mobile
 * @copyright 2020 Moodle Pty Ltd
 * @author    <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_mobile\output;

use tool_mobile\api;

/**
 * Subscription page.
 *
 * @package   tool_mobile
 * @copyright 2020 Moodle Pty Ltd
 * @author    <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class subscription implements \renderable, \templatable {

    /**
     * Subscription data.
     *
     * @var array subscription data
     */
    protected $subscriptiondata;

    /**
     * Constructor for the class, sets the subscription data.
     *
     * @param array $subscriptiondata subscription data
     * @return void
     */
    public function __construct(array $subscriptiondata) {
        $this->subscriptiondata = $subscriptiondata;
    }

    /**
     * Get the lines from a multiline setting, splitting on any line ending as the app does.
     *
     * @param string $config raw setting value
     * @return string[] lines, not trimmed
     */
    private static function get_lines(string $config): array {
        return preg_split('/\r\n|\r|\n/', $config);
    }

    /**
     * Normalise a language code so that "pt_br" and "pt-br" are considered the same language.
     *
     * @param string $lang language code
     * @return string normalised language code
     */
    private static function normalise_lang(string $lang): string {
        return str_replace('_', '-', $lang);
    }

    /**
     * Count the custom menu items as the app does, to be compared with the subscription limit.
     *
     * Each line follows the "label|url|type|lang" format and is ignored unless label, url and type are not empty.
     * Lines with the same url and type (case insensitive) are translations of the same item.
     *
     * An item with at least one label not restricted to a language with "<lang>_only" is shown in every language.
     * Otherwise, it is only shown in the languages of its labels. The count is the number of items shown in every
     * language plus the highest number of language restricted items shown in a single language.
     *
     * @param string $menuitemsconfig raw setting value (one menu item per line)
     * @return int number of custom menu items
     */
    private static function count_custom_menu_items(string $menuitemsconfig): int {
        // Labels languages of each item, indexed by item identity.
        $items = [];
        foreach (self::get_lines($menuitemsconfig) as $line) {
            [$label, $url, $type, $lang] = array_pad(array_map('trim', explode('|', $line)), 4, '');
            if ($label === '' || $url === '' || $type === '') {
                continue;
            }
            $items[$url . '|' . \core_text::strtolower($type)][] = \core_text::strtolower($lang);
        }

        $generic = 0;
        $langonly = [];
        foreach ($items as $langs) {
            $onlylangs = [];
            foreach ($langs as $lang) {
                if (!str_ends_with($lang, '_only')) {
                    // Shown in every language.
                    $onlylangs = null;
                    break;
                }
                $onlylangs[self::normalise_lang(substr($lang, 0, -strlen('_only')))] = true;
            }

            if ($onlylangs === null) {
                $generic++;
                continue;
            }
            foreach (array_keys($onlylangs) as $lang) {
                $langonly[$lang] = ($langonly[$lang] ?? 0) + 1;
            }
        }

        return $generic + ($langonly ? max($langonly) : 0);
    }

    /**
     * Count the custom language strings as the app does, to be compared with the subscription limit.
     *
     * Each line follows the "id|string|lang" format and is ignored if it has less than 3 values. Duplicates are not
     * removed. The limit applies per language, so the count is the highest number of strings in a single language.
     *
     * @param string $customlangstrings raw setting value (one language string per line)
     * @return int number of custom language strings
     */
    private static function count_custom_lang_strings(string $customlangstrings): int {
        $counts = [];
        foreach (self::get_lines($customlangstrings) as $line) {
            $values = explode('|', $line);
            if (count($values) < 3) {
                continue;
            }
            $lang = self::normalise_lang(trim($values[2]));
            $counts[$lang] = ($counts[$lang] ?? 0) + 1;
        }

        return $counts ? max($counts) : 0;
    }

    /**
     * Exports the data.
     *
     * @param \core\output\renderer_base $output
     * @return array with the subscription information
     */
    public function export_for_template(\core\output\renderer_base $output): array {
        global $CFG, $USER;

        $ms = get_config('tool_mobile');    // Get mobile settings.

        $data = $this->subscriptiondata;
        // Check subscription information to prepare the page data.
        if (empty($data) || empty($data['subscription']) || !isset($data['availableplans'])) {
            // Prepare data for no subscription information case, with a message and a link to the apps portal.
            $datanosub['appsportalurl'] = \tool_mobile\api::MOODLE_APPS_PORTAL_URL;
            $datanosub['messagenosubscriptioninfo'][] = [
                'message' => get_string('nosubsblocked', 'tool_mobile', $datanosub['appsportalurl']),
                'title' => get_string('nosubswhyhappen', 'tool_mobile'),
                'headinglevel' => 4,
                'extraclasses' => 'mt-4 mb-0',
            ];
            return $datanosub;
        }

        $livedataurl = \tool_mobile\api::MOODLE_APPS_PORTAL_URL;
        $data['messagelivedata'] = get_string('showinglivedata', 'tool_mobile', $livedataurl);

        $lastupdate = get_config('tool_mobile', 'subscriptioninfoupdated');
        if (empty($lastupdate) || time() - $lastupdate > 10 * DAYSECS) {
            if (empty($lastupdate)) {
                $data['messageoldcache'] = get_string('planneverchecked', 'tool_mobile');
            } else {
                $diff = time() - $lastupdate;
                $oldcachetime['since'] = (int) floor($diff / DAYSECS);
                $oldcachetime['date'] = userdate($lastupdate, get_string('strftimedatetime', 'langconfig'));
                $data['messageoldcache'] = get_string('showingcacheddata', 'tool_mobile', $oldcachetime);
            }
        }

        $data['appsportalurl'] = \tool_mobile\api::MOODLE_APPS_PORTAL_URL;
        $params = [
            'sitesecret' => md5(\core\hub\registration::get_secret()),
            'siteurl' => $CFG->wwwroot,
            'origin' => 'moodlelms',
        ];
        $unregisteredurl = new \moodle_url(
            \tool_mobile\api::MOODLE_APPS_PORTAL_URL . '/local/apps/signup_site.php',
            $params,
        );

        $params['plan'] = 'premium';
        $params['email'] = md5($USER->email);
        $appsportalupgradeurl = new \moodle_url(
            \tool_mobile\api::MOODLE_APPS_PORTAL_URL . '/local/apps/signup_site.php',
            $params,
        );
        $data['appsportalupgradeurl'] = $appsportalupgradeurl->out(false);

        // First prepare messages that may come from the WS.
        if (!empty($data['messages'])) {
            foreach ($data['messages'] as $msg) {
                switch ($msg['code'] ?? '') {
                    case "deprecatedplan":
                        $data['messagedeprecation'][] = [
                            'message' => $msg['message'],
                            'titleicon' => [
                                'icon' => 'i/warning',
                                'component' => 'core',
                            ],
                            'title' => get_string('plancontinues', 'tool_mobile', $data['subscription']['name']),
                        ];
                        break;
                    case "missingcredentials":
                        $data['messagemissingcredentials'][] = [
                            'message' => get_string('statisticstemporarilyunavailable', 'tool_mobile'),
                            'extraclasses' => 'mt-4',
                        ];
                        break;

                    case "unregisteredsite":
                        $data['messageunregisteredsite'][] = [
                            'message' => $msg['message'],
                            'titleicon' => [
                                'icon' => 'i/warning',
                                'component' => 'core',
                            ],
                            'buttonstr' => get_string('register', 'tool_mobile'),
                            'buttonurl' => $unregisteredurl->out(false),
                            'extrabuttonclasses' => 'text-dark',
                        ];
                        break;

                    default:
                        $data['messages' . $msg['type']][] = ['message' => $msg['message']];
                        break;
                }
            }
        }
        unset($data['messages']);

        // Derive plan flags for template logic-less checks.
        $currentplan = api::get_normalized_plan($data, false);
        $data['subscription']['isfree'] = ($currentplan === 'free');
        $data['subscription']['ispremium'] = api::is_premium_or_bma_plan($data, false);
        if (!$data['subscription']['ispremium']) {
            if (api::has_matomo_additional_html()) {
                $data['morecurrentsetup']['matomoinapp'] = 1;
            }
            if (!empty(get_config('core_admin', 'logo')) || !empty(get_config('core_admin', 'logocompact'))) {
                $data['morecurrentsetup']['logoinapp'] = 1;
            }
        }

        if (!empty($data['subscription']['expiretime'])) {
            $expiretimeshort = userdate(
                (int)$data['subscription']['expiretime'],
                get_string('strftimedatetimeshort', 'langconfig'),
            );
            $data['subscription']['planexpiresontext'] = get_string('planexpireson', 'tool_mobile', $expiretimeshort);
        }

        if ($data['subscription']['ispremium']) {
            $data['messagefullaccess'][] = [
                'titleicon' => [
                    'icon' => 'i/checkedcircle',
                    'component' => 'core',
                ],
                'title' => get_string('fullaccessfeatures', 'tool_mobile', $data['subscription']['name']),
            ];
        }

        // Review availableplans.
        foreach ($data['availableplans'] as $plan) {
            if ($plan['plan'] != 'premium') {
                continue;
            }
            $data['tocompareplan'] = $plan;
        }

        // Now prepare statistics information.
        if (!isset($data['messagemissingcredentials'])) {
            if (isset($data['statistics']['notifications'])) {
                $data['notifications'] = $data['statistics']['notifications'];
                unset($data['statistics']['notifications']);

                // Find current month data.
                $data['notifications']['currentactivedevices'] = 0;

                if (isset($data['notifications']['monthly'][0])) {
                    $currentmonth = $data['notifications']['monthly'][0];
                    $data['notifications']['currentactivedevices'] = $currentmonth['activedevices'];
                    if (!empty($currentmonth['limitreachedtime'])) {
                        $data['notifications']['limitreachedtime'] = $currentmonth['limitreachedtime'];
                        $data['notifications']['ignorednotificationswarning'] = [
                            'message' => get_string('notificationslimitreached', 'tool_mobile'),
                            'icon' => [
                                'icon' => 'i/risk_dataloss',
                                'component' => 'core',
                            ],
                            'buttonstr' => get_string('upgradetosubscription', 'tool_mobile', $data['tocompareplan']['name']),
                            'buttonurl' => $appsportalupgradeurl->out(false),
                            'extraclasses' => 'alert-danger',
                            'extrabuttonclasses' => 'bg-danger text-light',
                        ];
                    }
                    $monthformat = get_string('strftimemonth');
                    foreach ($data['notifications']['monthly'] as $key => $month) {
                        if (!empty($month['year']) && !empty($month['month'])) {
                            $timestamp = make_timestamp((int)$month['year'], (int)$month['month'], 1);
                            $data['notifications']['monthly'][$key]['monthstr'] = userdate($timestamp, $monthformat);
                        }
                    }
                }
                $data['messagesnotificationsseemore'] = get_string('notificationsseemore', 'tool_mobile', $data['appsportalurl']);
            } else {
                $urlmessagesetting = new \moodle_url('/admin/settings.php', ['section' => 'messagesettingairnotifier']);
                $data['messagesnonotifications'][] = [
                    'icon' => [
                        'icon' => 'i/warning',
                        'component' => 'core',
                    ],
                    'message' => get_string('notificationsmissingwarning', 'tool_mobile', $urlmessagesetting),
                    'extraclasses' => 'mt-4',
                ];
            }
        }
        $subscribedfeatures = [];
        // Review features.
        foreach ($data['subscription']['features'] as $featureindex => &$feature) {
            $featurenameforid = clean_param((string)($feature['name'] ?? 'feature'), PARAM_ALPHANUMEXT);
            $feature['progresslabelid'] = 'tool-mobile-feature-' . $featureindex . '-' . $featurenameforid . '-label';
            // Check the type of features, if it is a limitation or functionality feature.
            if (array_key_exists('limit', $feature)) {
                if (empty($feature['limit'])) {   // Unlimited, no need to calculate current values.
                    $feature['humanstatus'] = get_string('unlimited');
                    $feature['showbar'] = 0;
                    $feature['limit'] = get_string('unlimited');
                }

                switch ($feature['name']) {
                    // Check active devices.
                    case 'pushnotificationsdevices':
                        $feature['status'] = 0;
                        if (isset($data['notifications']['currentactivedevices'])) {
                            $feature['status'] = $data['notifications']['currentactivedevices'];
                        }
                        $feature['showstatus'] = 1;
                        break;
                    // Check menu items.
                    case 'custommenuitems':
                        $feature['status'] = self::count_custom_menu_items($ms->custommenuitems) +
                            self::count_custom_menu_items($ms->customusermenuitems);
                        $feature['showstatus'] = 1;
                        break;
                    // Check language strings.
                    case 'customlanguagestrings':
                        $feature['status'] = self::count_custom_lang_strings($ms->customlangstrings);
                        $feature['showstatus'] = 1;
                        break;
                    // Check disabled features strings.
                    case 'disabledfeatures':
                        $feature['status'] = empty($ms->disabledfeatures) ? 0 : count(explode(',', $ms->disabledfeatures));
                        $feature['showstatus'] = 1;
                        break;
                }

                $feature['humanstatus'] = '?/' . $feature['limit'];
                // Check if we should display the bar and how.
                if (isset($feature['status'])) {
                    $feature['humanstatus'] = (string)$feature['status'];
                    if (is_int($feature['status']) && is_int($feature['limit'])) {
                        $feature['showbar'] = 1;
                        // Show currentussage with or without currentusage.
                        if (!isset($data['subscription']['currentusage'])) {
                            $data['subscription']['currentusage'] = [];
                        }
                        $feature['humanstatus'] = $feature['status'] . ' / ' . $feature['limit'];
                        $feature['percentage'] = round(($feature['status'] / $feature['limit']) * 100);
                        if ($feature['percentage'] > 100) {
                            $feature['percentage'] = 100;
                        }
                        $feature['barclass'] = 'bg-success';

                        if ($feature['status'] >= ($feature['limit'] / 2)) {
                            $feature['barclass'] = 'bg-warning';
                        }
                        if ($feature['status'] >= $feature['limit']) {
                            $feature['barclass'] = 'bg-danger';
                            if ($feature['status'] > $feature['limit']) {
                                $feature['humanstatus'] .= ' - ' . get_string('subscriptionlimitsurpassed', 'tool_mobile');
                            }
                        }
                    } else {
                        $feature['showbar'] = 0;
                        if ($feature['status'] >= $feature['limit']) {
                            $feature['barclass'] = 'bg-danger';
                            if ($feature['status'] > $feature['limit']) {
                                $feature['humanstatus'] .= ' - ' . get_string('subscriptionlimitsurpassed', 'tool_mobile');
                            }
                        }
                    }
                }
                $subscribedfeatures[$feature['name']]['subscribedlimitstr'] = $feature['limitstr'];
                $subscribedfeatures[$feature['name']]['subscribedenabled'] = $feature['enabled'];
                $subscribedfeatures[$feature['name']]['subscribeddecoration'] =
                    $feature['enabled'] ? 'text-primary' : 'text-danger';
                $subscribedfeatures[$feature['name']]['subscribedlimitstr'] = $feature['limitstr'];
                $subscribedfeatures[$feature['name']]['subscribedenabled'] = $feature['enabled'];
                $subscribedfeatures[$feature['name']]['subscribeddecoration'] =
                    $feature['enabled'] ? 'text-primary' : 'text-danger';
            } else {
                $feature['humanstatus'] = empty($feature['enabled']) ? get_string('notincluded') : get_string('included');

                if (empty($feature['enabled'])) {
                    switch ($feature['name']) {
                        // Check remote themes.
                        case 'remotethemes':
                            if (!empty($CFG->mobilecssurl)) {
                                $feature['message'] = [
                                    'type' => 'danger', 'message' => get_string('subscriptionfeaturenotapplied', 'tool_mobile')];
                            }
                            break;
                        // Check site logo.
                        case 'sitelogo':
                            if ($output->get_logo_url() || $output->get_compact_logo_url()) {
                                $feature['message'] = [
                                    'type' => 'danger', 'message' => get_string('subscriptionfeaturenotapplied', 'tool_mobile')];
                            }
                            break;
                        // Check QR automatic login.
                        case 'qrautomaticlogin':
                            if ($ms->qrcodetype == \tool_mobile\api::QR_CODE_LOGIN) {
                                $feature['message'] = [
                                    'type' => 'danger', 'message' => get_string('subscriptionfeaturenotapplied', 'tool_mobile')];
                            }
                            break;
                    }
                }
                $subscribedfeatures[$feature['name']]['subscribedhumanstatus'] = $feature['humanstatus'];
                $subscribedfeatures[$feature['name']]['subscribedenabled'] = $feature['enabled'] ? 'fa-check' : 'fa-times';
                $subscribedfeatures[$feature['name']]['subscribeddecoration'] =
                    $feature['enabled'] ? 'text-primary' : 'text-danger';
                $subscribedfeatures[$feature['name']]['subscribedhumanstatus'] = $feature['humanstatus'];
                $subscribedfeatures[$feature['name']]['subscribedenabled'] = $feature['enabled'] ? 'fa-check' : 'fa-times';
                $subscribedfeatures[$feature['name']]['subscribeddecoration'] =
                    $feature['enabled'] ? 'text-primary' : 'text-danger';
            }
        }

        usort($data['subscription']['features'],
            function (array $featurea, array $featureb) {
                $isfeaturea = !array_key_exists('limit', $featurea);
                $isfeatureb = !array_key_exists('limit', $featureb);

                if (!$isfeaturea && $isfeatureb) {
                    return 1;
                }
                return 0;
            }
        );

        if (!$data['availableplans']) {
            return $data;
        }
        // Review availableplans features and filter to use premium.
        foreach ($data['tocompareplan']['features'] as &$feature) {
            if (array_key_exists('limit', $feature) && empty($feature['limit'])) {
                $feature['limit'] = 'unlimited';
            }
            $feature['humanstatus'] = $feature['enabled'] ? get_string('included') : get_string('notincluded');
            if (isset($subscribedfeatures[$feature['name']])) {
                $feature = array_merge($feature, $subscribedfeatures[$feature['name']]);
            }
        }
        if (!empty($data['tocompareplan'])) {
            usort(
                $data['tocompareplan']['features'],
                function (array $featurea, array $featureb) {
                    $isfeaturea = !array_key_exists('limit', $featurea);
                    $isfeatureb = !array_key_exists('limit', $featureb);

                    if (!$isfeaturea && $isfeatureb) {
                        return 1;
                    }
                    return 0;
                }
            );
        }
        unset($data['availableplans']);
        return $data;
    }
}
