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
 * Output helper to export actions for rendering.
 *
 * @package   report_insights
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_insights\output;

defined('MOODLE_INTERNAL') || die();

/**
 * Output helper to export actions for rendering.
 *
 * @package   report_insights
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class actions_exporter {

    /**
     * Add the prediction actions renderable.
     *
     * @param  \core_analytics\local\target\base $target
     * @param  \renderer_base                    $output
     * @param  \core_analytics\prediction        $prediction
     * @param  bool                              $includedetailsaction
     * @return \stdClass|false
     */
    public static function add_prediction_actions(\core_analytics\local\target\base $target, \renderer_base $output,
            \core_analytics\prediction $prediction, bool $includedetailsaction = false) {

        $actions = $target->prediction_actions($prediction, $includedetailsaction);
        if ($actions) {
            $actionsmenu = new \action_menu();

            // Add all actions defined by the target.
            foreach ($actions as $action) {
                $actionsmenu->add_primary_action($action->get_action_link());
            }

            return $actionsmenu->export_for_template($output);
        }

        return false;
    }

    /**
     * Add bulk actions renderables.
     *
     * Note that if you are planning to render the bulk actions, the provided predictions must share the same predicted value.
     *
     * @param  \core_analytics\local\target\base $target
     * @param  \renderer_base                    $output
     * @param  \core_analytics\prediction[]      $predictions   Bulk actions for this set of predictions.
     * @param  \context                          $context       The context of these predictions.
     * @return \stdClass[]|false
     */
    public static function add_bulk_actions(\core_analytics\local\target\base $target, \renderer_base $output, array $predictions,
            \context $context) {
        global $USER;

        $bulkactions = $target->bulk_actions($predictions);

        if ($context->contextlevel === CONTEXT_USER) {
            // Remove useful / notuseful if the current user is not part of the users who receive the insight (e.g. a site manager
            // who looks at the generated insights for a particular user).

            $insightusers = $target->get_insights_users($context);
            if (empty($insightusers[$USER->id])) {
                foreach ($bulkactions as $key => $action) {
                    if ($action->get_action_name() === 'useful' || $action->get_action_name() === 'notuseful') {
                        unset($bulkactions[$key]);
                    }
                }
            }

        }

        if (!$bulkactions) {
            return false;
        }

        $actionsmenu = [];

        // All the predictions share a common predicted value.
        $predictionvalue = reset($predictions)->get_prediction_data()->prediction;

        // Add all actions defined by the target.
        foreach ($bulkactions as $action) {
            $action->get_action_link()->set_attribute('data-togglegroup', 'insight-bulk-action-' . $predictionvalue);
            $actionsmenu[] = $action->get_action_link()->export_for_template($output);
        }

        if (empty($actionsmenu)) {
            return false;
        }

        return $actionsmenu;
    }

}

