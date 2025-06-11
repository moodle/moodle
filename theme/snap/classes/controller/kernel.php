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

namespace theme_snap\controller;

/**
 * Controller Kernel.
 *
 * Handles typical request lifecycle. *
 * Given an action, route it to a controller method,
 * execute controller method and handle any return
 * values.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class kernel {
    /**
     * @var router
     */
    public $router;

    /**
     * @param router $router
     */
    public function __construct(router $router) {
        $this->router = $router;
    }

    /**
     * Entry method for handling a action based request
     *
     * @param string $action The action to handle
     */
    public function handle($action) {
        $callback = $this->resolve_controller_callback($action);
        $this->execute_callback($callback);
    }

    /**
     * Given an action, find the controller and method responsible for
     * handling the action.
     *
     * In addition, send some extra variables to the controller
     * and initialize it.
     *
     * @param string $action
     * @return array
     */
    public function resolve_controller_callback($action) {
        // @codingStandardsIgnoreLine
        /** @var $controller controller_abstract */
        list($controller, $method) = $this->router->route_action($action);

        $controller->init($action);

        return array($controller, $method);
    }

    /**
     * Given a controller callback, execute the callback
     * and handle the return value or resulting output
     * buffer.
     *
     * Automatically wraps non-empty responses with
     * header/footer, etc.
     *
     * @param $callback
     * @throws \coding_exception
     */
    public function execute_callback($callback) {
        global $OUTPUT;

        ob_start();
        $response = call_user_func($callback);
        $buffer   = trim(ob_get_contents());
        ob_end_clean();

        if (!empty($response) && !empty($buffer)) {
            throw new \coding_exception('Mixed return output and buffer output');
        } else if (!empty($buffer)) {
            $response = $buffer;
        }
        if (!empty($response)) {
            if (!PHPUNIT_TEST) {
                echo $OUTPUT->header();
            }
            echo $response;
            if (!PHPUNIT_TEST) {
                echo $OUTPUT->footer();
            }
        }
    }
}
