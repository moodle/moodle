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
     * Exports the data.
     *
     * @param \renderer_base $output
     * @return array with the subscription information
     */
    public function export_for_template(\renderer_base $output): array {
        global $CFG;

        $ms = get_config('tool_mobile');    // Get mobile settings.

        $data = $this->subscriptiondata;
        $data['appsportalurl'] = \tool_mobile\api::MOODLE_APPS_PORTAL_URL;

        // First prepare messages that may come from the WS.
        if (!empty($data['messages'])) {
            foreach ($data['messages'] as $msg) {
                $data['messages' . $msg['type']][] = ['message' => $msg['message']];
            }
        }
        unset($data['messages']);

        // Now prepare statistics information.
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
                        'message' => get_string('notificationslimitreached', 'tool_mobile', $data['appsportalurl'])
                    ];
                }
            }
        }

        // Review features.
        foreach ($data['subscription']['features'] as &$feature) {

            // Check the type of features, if it is a limitation or functionality feature.
            if (array_key_exists('limit', $feature)) {

                if (empty($feature['limit'])) {   // Unlimited, no need to calculate current values.
                    $feature['humanstatus'] = get_string('unlimited');
                    $feature['showbar'] = 0;
                    continue;
                }

                switch ($feature['name']) {
                    // Check active devices.
                    case 'pushnotificationsdevices':
                        if (isset($data['notifications']['currentactivedevices'])) {
                            $feature['status'] = $data['notifications']['currentactivedevices'];
                        }
                        break;
                    // Check menu items.
                    case 'custommenuitems':
                        $custommenuitems = [];
                        $els = rtrim($ms->custommenuitems, "\n");
                        if (!empty($els)) {
                            $custommenuitems = explode("\n", $els);
                            // Get unique custom menu urls.
                            $custommenuitems = array_flip(
                                array_map(function($val) {
                                    return explode('|', $val)[1];
                                }, $custommenuitems)
                            );
                        }
                        $feature['status'] = count($custommenuitems);
                        break;
                    // Check language strings.
                    case 'customlanguagestrings':
                        $langstrings = [];
                        $els = rtrim($ms->customlangstrings, "\n");
                        if (!empty($els)) {
                            $langstrings = explode("\n", $els);
                            // Get unique language string ids.
                            $langstrings = array_flip(
                                array_map(function($val) {
                                    return explode('|', $val)[0];
                                }, $langstrings)
                            );
                        }
                        $feature['status'] = count($langstrings);
                        break;
                    // Check disabled features strings.
                    case 'disabledfeatures':
                        $feature['status'] = empty($ms->disabledfeatures) ? 0 : count(explode(',', $ms->disabledfeatures));
                        break;
                }

                $feature['humanstatus'] = '?/' . $feature['limit'];
                // Check if we should display the bar and how.
                if (isset($feature['status']) && is_int($feature['status'])) {
                    $feature['humanstatus'] = $feature['status'] . '/' . $feature['limit'];
                    $feature['showbar'] = 1;

                    if ($feature['status'] == $feature['limit']) {
                        $feature['barclass'] = 'bg-warning';
                    }

                    if ($feature['status'] > $feature['limit']) {
                        $feature['barclass'] = 'bg-danger';
                        $feature['humanstatus'] .= ' - ' . get_string('subscriptionlimitsurpassed', 'tool_mobile');
                    }
                }

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

        return $data;
    }
}
