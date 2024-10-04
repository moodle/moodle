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

namespace core\route\controller;

use core\form\error_feedback;
use core\router;
use core\router\route;
use core\router\schema\parameters\query_parameter;
use core\router\util;
use core\url;
use core_user;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Page Not Found Controller.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_not_found_controller {
    use \core\router\route_controller;

    /**
     * Constructor for the page not found handler.
     *
     * @param \core\router $router The router.
     */
    public function __construct(
        /** @var router The routing engine */
        private router $router,
    ) {
    }

    /**
     * Administer a course.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    #[route(
        path: '/error',
        method: ['GET', 'POST'],
        queryparams: [
            new query_parameter(
                name: 'code',
                type: \core\param::INT,
                default: 404,
            ),
        ],
    )]
    public function page_not_found_handler(
        ServerRequestInterface $request,
    ): ResponseInterface {
        global $CFG, $PAGE, $OUTPUT, $ME;

        $context = \core\context\system::instance();
        $title = get_string('pagenotexisttitle', 'error');
        $PAGE->set_url('/error');
        $PAGE->set_context($context);
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        $PAGE->navbar->add($title);

        // This allows the webserver to dictate wether the http status should remain
        // what it would have been, or force it to be a 404. Under other conditions
        // it could most often be a 403, 405 or a 50x error.
        $code = $request->getQueryParams()['code'] ?? 404;
        $response = $this->router->get_app()->getResponseFactory()->createResponse($code);

        $mform = $this->get_message_form($response);
        if ($mform) {
            if ($this->process_message_form($mform)) {
                // The form was submitted. Redirect to the home page.
                return util::redirect(
                    $response,
                    new url('/'),
                );
            }

            // Form not submitted. We need to set the referer and request path because the URI may be different on submission.
            $mform->set_data([
                'referer' => $request->getHeaderLine('Referer'),
                'requested' => $request->getUri()->getPath(),
            ]);
        }

        $response->getBody()->write($OUTPUT->header());
        $response->getBody()->write($OUTPUT->notification(get_string('pagenotexist', 'error', s($ME)), 'error'));
        $response->getBody()->write($OUTPUT->supportemail(['class' => 'text-center d-block mb-3 fw-bold']));

        if ($mform) {
            $response->getBody()->write(\html_writer::tag('h4', get_string('sendmessage', 'error')));
            $response->getBody()->write($mform->render());
        } else {
            $response->getBody()->write($OUTPUT->continue_button($CFG->wwwroot));
        }

        $response->getBody()->write($OUTPUT->footer());

        return $response;
    }

    /**
     * Get the message form, or null if it should not be displayed.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return error_feedback|null
     */
    protected function get_message_form(
        ResponseInterface $response,
    ): ?\moodleform {
        $canmessage = has_capability('moodle/site:senderrormessage', \core\context\system::instance());
        $supportuser = core_user::get_support_user();

        // We can only message support if both the user has the capability
        // and the support user is a real user.
        $canmessage = $canmessage && core_user::is_real_user($supportuser->id);

        if (!$canmessage) {
            return null;
        }

        return new error_feedback(util::get_path_for_callable([self::class, 'page_not_found_handler'], [], [])->out());
    }

    /**
     * Process the message form.
     *
     * If the form was submitted, send the message and return a redirect response.
     *
     * @param error_feedback $mform
     * @return bool
     */
    protected function process_message_form(
        error_feedback $mform,
    ): bool {
        global $CFG, $USER;
        if ($data = $mform->get_data()) {
            // Send the message and redirect.
            $message = new \core\message\message();
            $message->courseid         = SITEID;
            $message->component        = 'moodle';
            $message->name             = 'errors';
            $message->userfrom          = $USER;
            $message->userto            = core_user::get_support_user();
            $message->subject           = 'Error: ' . $data->referer . ' -> ' . $data->requested;
            $message->fullmessage       = $data->text;
            $message->fullmessageformat = FORMAT_PLAIN;
            $message->fullmessagehtml   = '';
            $message->smallmessage      = '';
            $message->contexturl = $data->requested;
            message_send($message);

            \core\notification::success(get_string('sendmessagesent', 'error', $data->requested));

            return true;
        }

        return false;
    }
}
