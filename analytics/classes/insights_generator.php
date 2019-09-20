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
 * Insights generator.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/messagelib.php');

/**
 * Insights generator.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class insights_generator {

    /**
     * @var int
     */
    private $modelid;

    /**
     * @var \core_analytics\local\target\base
     */
    private $target;

    /**
     * @var int[]
     */
    private $contextcourseids;

    /**
     * Constructor.
     *
     * @param int $modelid
     * @param \core_analytics\local\target\base $target
     */
    public function __construct(int $modelid, \core_analytics\local\target\base $target) {
        $this->modelid = $modelid;
        $this->target = $target;
    }

    /**
     * Generates insight notifications.
     *
     * @param array                         $samplecontexts    The contexts these predictions belong to
     * @param \core_analytics\prediction[]  $predictions       The prediction records
     * @return  null
     */
    public function generate($samplecontexts, $predictions) {

        $analyserclass = $this->target->get_analyser_class();

        // We will need to restore it later.
        $actuallanguage = current_language();

        if ($analyserclass::one_sample_per_analysable()) {

            // Iterate through the predictions and the users in each prediction (likely to be just one).
            foreach ($predictions as $prediction) {

                $context = $samplecontexts[$prediction->get_prediction_data()->contextid];

                $users = $this->target->get_insights_users($context);
                foreach ($users as $user) {

                    $this->set_notification_language($user);
                    list($insighturl, $fullmessage, $fullmessagehtml) = $this->prediction_info($prediction, $context, $user);
                    $this->notification($context, $user, $insighturl, $fullmessage, $fullmessagehtml);
                }
            }

        } else {

            // Iterate through the context and the users in each context.
            foreach ($samplecontexts as $context) {

                // Weird to pass both the context and the contextname to a method right, but this way we don't add unnecessary
                // db reads calling get_context_name() multiple times.
                $contextname = $context->get_context_name(false);

                $users = $this->target->get_insights_users($context);
                foreach ($users as $user) {

                    $this->set_notification_language($user);

                    $insighturl = $this->target->get_insight_context_url($this->modelid, $context);

                    list($fullmessage, $fullmessagehtml) = $this->target->get_insight_body($context, $contextname, $user,
                        $insighturl);

                    $this->notification($context, $user, $insighturl, $fullmessage, $fullmessagehtml);
                }
            }
        }

        force_current_language($actuallanguage);
    }

    /**
     * Generates a insight notification for the user.
     *
     * @param  \context    $context
     * @param  \stdClass   $user
     * @param  \moodle_url $insighturl    The insight URL
     * @param  string      $fullmessage
     * @param  string      $fullmessagehtml
     * @return null
     */
    private function notification(\context $context, \stdClass $user, \moodle_url $insighturl, string $fullmessage, string $fullmessagehtml) {

        $message = new \core\message\message();
        $message->component = 'moodle';
        $message->name = 'insights';

        $message->userfrom = \core_user::get_support_user();
        $message->userto = $user;

        $message->subject = $this->target->get_insight_subject($this->modelid, $context);

        // Same than the subject.
        $message->contexturlname = $message->subject;
        $message->courseid = $this->get_context_courseid($context);

        $message->fullmessage = $fullmessage;
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessagehtml = $fullmessagehtml;
        $message->smallmessage = $fullmessage;
        $message->contexturl = $insighturl->out(false);

        message_send($message);
    }

    /**
     * Returns the course context of the provided context reading an internal cache first.
     *
     * @param  \context $context
     * @return int
     */
    private function get_context_courseid(\context $context) {

        if (empty($this->contextcourseids[$context->id])) {

            $coursecontext = $context->get_course_context(false);
            if (!$coursecontext) {
                // Default to the frontpage course context.
                $coursecontext = \context_course::instance(SITEID);
            }
            $this->contextcourseids[$context->id] = $coursecontext->instanceid;
        }

        return $this->contextcourseids[$context->id];
    }

    /**
     * Extracts info from the prediction for display purposes.
     *
     * @param  \core_analytics\prediction   $prediction
     * @param  \context                     $context
     * @param  \stdClass                    $user
     * @return array Three items array with formats [\moodle_url, string, string]
     */
    private function prediction_info(\core_analytics\prediction $prediction, \context $context, \stdClass $user) {
        global $OUTPUT;

        // The prediction actions get passed to the target so that it can show them in its preferred way.
        $predictionactions = $this->target->prediction_actions($prediction, true, true);
        $predictioninfo = $this->target->get_insight_body_for_prediction($context, $user, $prediction, $predictionactions);

        // For FORMAT_PLAIN.
        $fullmessageplaintext = '';
        if (!empty($predictioninfo[FORMAT_PLAIN])) {
            $fullmessageplaintext .= $predictioninfo[FORMAT_PLAIN];
        }

        $insighturl = $predictioninfo['url'] ?? null;

        // For FORMAT_HTML.
        $messageactions  = [];
        foreach ($predictionactions as $action) {
            $actionurl = $action->get_url();
            if (!$actionurl->get_param('forwardurl')) {

                $params = ['actionvisiblename' => $action->get_text(), 'target' => '_blank'];
                $actiondoneurl = new \moodle_url('/report/insights/done.php', $params);
                // Set the forward url to the 'done' script.
                $actionurl->param('forwardurl', $actiondoneurl->out(false));
            }

            if (empty($insighturl)) {
                // We use the primary action url as insight url so we log that the user followed the provided link.
                $insighturl = $action->get_url();
            }

            $actiondata = (object)['url' => $action->get_url()->out(false), 'text' => $action->get_text()];

            // Basic message for people who still lives in the 90s.
            $fullmessageplaintext .= get_string('insightinfomessageaction', 'analytics', $actiondata) . PHP_EOL;

            // We now process the HTML version actions, with a special treatment for useful/notuseful.
            if ($action->get_action_name() === 'fixed') {
                $usefulurl = $actiondata->url;
            } else if ($action->get_action_name() === 'notuseful') {
                $notusefulurl = $actiondata->url;
            } else {
                $messageactions[] = $actiondata;
            }
        }

        // Extra condition because we don't want to show the yes/no unless we have urls for both of them.
        if (!empty($usefulurl) && !empty($notusefulurl)) {
            $usefulbuttons = ['usefulurl' => $usefulurl, 'notusefulurl' => $notusefulurl];
        }

        $contextinfo = [
            'usefulbuttons' => $usefulbuttons,
            'actions' => $messageactions,
            'body' => $predictioninfo[FORMAT_HTML] ?? ''
        ];
        $fullmessagehtml = $OUTPUT->render_from_template('core_analytics/insight_info_message_prediction', $contextinfo);
        return [$insighturl, $fullmessageplaintext, $fullmessagehtml];
    }

    /**
     * Sets the session language to the language used by the notification receiver.
     *
     * @param  \stdClass $user The user who will receive the message
     * @return null
     */
    private function set_notification_language($user) {
        global $CFG;

        // Copied from current_language().
        if (!empty($user->lang)) {
            $lang = $user->lang;
        } else if (isset($CFG->lang)) {
            $lang = $CFG->lang;
        } else {
            $lang = 'en';
        }
        force_current_language($lang);
    }
}
