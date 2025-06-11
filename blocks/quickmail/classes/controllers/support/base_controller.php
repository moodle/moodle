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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_quickmail\controllers\support;

defined('MOODLE_INTERNAL') || die();

use block_quickmail\controllers\support\controller_request;
use block_quickmail\controllers\support\controller_form;
use block_quickmail\controllers\support\controller_form_component;
use block_quickmail\controllers\support\controller_session;
use moodle_url;

class base_controller {

    public static $formspath = 'block_quickmail\controllers\forms';
    public static $views = [];
    public static $actions = [];

    public $context;
    public $props;
    public $session;
    public $form_errors;

    public function __construct(&$page, $data = []) {
        $this->set_props($data);
        $this->session = new controller_session($this->get_store_key());
        $this->form_errors = [];
    }

    // Controller Instantiation.
    /**
     * Handles a request to the static controller implementation
     *
     * @param  object  &$page   the PAGE global, for manipulation of nav, etc.
     * @param  array   $data    optional, additional data to be included in controller
     * @param  string  $action  optional, explicit controller action
     * @return mixed
     */
    public static function handle(&$page, $data = [], $action = '') {
        $controller = new static($page, $data);

        // Persist any session data to the next request.
        $controller->session->reflash();

        $request = controller_request::make();

        // If no view name is present in the request, we can assume that this is a fresh entrance to the controller.
        if (!$request->view_name) {
            // Clear any session data for this controller.
            $controller->session->clear();

            // Set the view to the controller's default.
            $viewname = self::get_default_view();

            // Otherwise, use the requested view.
        } else {
            $viewname = $request->view_name;
        }

        // If action is relevant to controller.
        if (in_array($action, static::$actions)) {
            return $controller->call_action($action, $request);
        }

        // Determine which view we are calling.
        // If view name is empty, set to first view.
        $viewname = $request->view_name ?: self::get_default_view();

        // Call the view.
        return $controller->call_view($viewname, $request);
    }

    // View Method Directives.
    /**
     * Calls the given "view_name" which should be a controller method
     *
     * @param  controller_request  $request
     * @param  string  $viewname
     * @return mixed
     */
    public function view(controller_request $request, $viewname) {
        return $this->$viewname($request);
    }

    /**
     * Calls the given post_{view_name}_{action} which should be a controller method
     *
     * @param  controller_request  $request
     * @param  string  $viewname
     * @param  string  $action   back|next
     * @param  array   $overrideinputs       additional params to be included in the request input
     *                                        (useful for handling moodle-form-specific inputs)
     * @return mixed
     */
    public function post(controller_request $request, $viewname, $action, $overrideinputs = []) {
        foreach ($overrideinputs as $key => $value) {
            $request->input->$key = $value;
        }

        return $this->{ 'post_' . $viewname . '_' . $action }($request);
    }

    /**
     * Calls the given "action" method on the static controller implementation
     *
     * Additionally renders the page header and footer
     *
     * @param  string              $actionname
     * @param  controller_request  $request
     * @return mixed
     */
    private function call_action($actionname, controller_request $request) {
        $actionname = 'action_' . $actionname;

        if (!method_exists($this, $actionname)) {
            throw new \Exception('controller action "' . $actionname . '"does not exist!');
        }

        global $OUTPUT;
        $displayheaderfooter = self::display_header_footer($actionname, $request);

        if ($displayheaderfooter) {
            echo $OUTPUT->header();
        }

        call_user_func([$this, $actionname], $request);

        if ($displayheaderfooter) {
            echo $OUTPUT->footer();
        }
    }

    /**
     * Calls the given "view name" method on the static controller implementation which should subsequently render the view
     *
     * Additionally renders the page header and footer
     *
     * @param  string              $viewname
     * @param  controller_request  $request
     * @return string
     */
    private function call_view($viewname, controller_request $request) {
        if (!method_exists($this, $viewname)) {
            throw new \Exception('controller view "' . $viewname . '"does not exist!');
        }

        global $OUTPUT;
        $displayheaderfooter = self::display_header_footer('view', $request);

        if ($displayheaderfooter) {
            echo $OUTPUT->header();
        }

        call_user_func([$this, $viewname], $request);

        if ($displayheaderfooter) {
            echo $OUTPUT->footer();
        }
    }

    /**
     *  Returns true/false to display header and footer.
     *
     * @param  string              $actionorview
     * @param  controller_request  $request
     * @return boolean
     */
    private function display_header_footer($actionorview, controller_request $request) {
        $result = true;
        if ($actionorview === 'view') {
            $hideviews = ['delete', 'update', 'save', 'cancelbutton'];
            foreach ($hideviews as $view) {
                if (isset($request->input->{$view})) {
                    $result = false;
                }
            }
        } else if (strpos($actionorview, "action_") === 0) {
            $hideactions = ['action_delete', 'action_resend', 'action_duplicate'];
            if (in_array($actionorview, $hideactions)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Returns the default view name of the static controller implementation
     *
     * Note: this is the first view name in the controller implementation's list array
     *
     * @return string
     */
    public static function get_default_view() {
        return key(static::$views);
    }

    // Cancel Method Directive.
    /**
     * Calls the given "view_name" which should be a controller method
     *
     * @return mixed
     */
    public function cancel() {
        $this->session->clear();

        // Set the view to the controller's default.
        $viewname = self::get_default_view();

        $request = controller_request::make();

        return $this->$viewname($request);
    }

    // View Data.
    public function view_keys() {
        return array_keys(static::$views);
    }

    public function view_data_keys($view) {
        return static::$views[$view];
    }

    // Rendering.
    /**
     * Returns rendered HTML for the given form as a component
     *
     * @param  controller_form  $form
     * @param  array            $params   optional, any additional data to be passed to the renderer
     * @return string
     */
    public function render_form(controller_form $form, $params = []) {
        global $PAGE;

        $renderer = $PAGE->get_renderer('block_quickmail');

        $rendered = $renderer->controller_form_component(new controller_form_component($form, $params));

        $this->render_form_error_notification();

        echo $rendered;
    }

    /**
     * Returns rendered HTML for the given component
     *
     * @param  string    $componentname
     * @param  array     $params             optional, any additional data to be passed to the renderer
     * @return string
     */
    public function render_component($componentname, $params = []) {
        global $PAGE;

        $renderer = $PAGE->get_renderer('block_quickmail');

        $rendered = $renderer->controller_component_template($componentname, $params);

        echo $rendered;
    }

    /**
     * Renders a moodle error notification for any form errors
     *
     * @return string
     */
    public function render_form_error_notification() {
        if ($this->form_errors) {
            $html = '<ul style="margin-bottom: 0px;">';

            foreach ($this->form_errors as $error) {
                $html .= '<li>' . $error . '</li>';
            }

            $html .= '</ul>';

            \core\notification::error($html);
        }
    }

    // Form Instantiation.
    /**
     * Instantiates and return a controller_form instance of the given name
     *
     * Note: this will automatically include the current session input data as a "_customdata" prop on the form with key "stored"
     *
     * @param  string  $name            a form class name path (\controllers\forms = base path)
     * @param  array   $data            any additional data to be passed to the form
     * @param  string  $targetaction   optional, action directive to include on form target URL
     * @return controller_form
     */
    public function make_form($name, $data = [], $targetaction = '') {
        $class = implode('\\', [self::$formspath, $name]);

        $targetparams = in_array($targetaction, static::$actions)
            ? array_merge(['action' => $targetaction], $this->get_form_url_params())
            : $this->get_form_url_params();

        $querystring = ! empty($targetparams)
            ? '?' . http_build_query($targetparams, '', '&')
            : '';

        return new $class($querystring, $this->get_form_custom_data($name, $data), 'post', '', null, true, null);
    }

    /**
     * Returns the target url for controller_form's including any optional parameters set in the static controller implementation
     *
     * @return string
     */
    private function get_form_url() {
        global $CFG;

        $moodleurl = new moodle_url(static::$baseuri, $this->get_form_url_params());

        return $moodleurl->out();
    }

    /**
     * Returns an array of custom data to be passed to a controller_form, prepending the appropriate "view_form_name"
     *
     * @param  string  $name  a form class name path (\controllers\forms = base path)
     * @param  array   $data  any additional data to be passed to the form
     * @return array
     */
    private function get_form_custom_data($name, $data = []) {
        // Merge in the current session input data.
        return array_merge($data, [
            'view_form_name' => $this->get_form_view_name_from_path($name),
            'stored' => $this->session->get_data()
        ]);
    }

    /**
     * Returns the "view_form_name" short name from the given path
     *
     * @param  string  $path
     * @return string
     */
    private function get_form_view_name_from_path($path) {
        $parts = explode('\\', $path);

        return end($parts);
    }

    /**
     * Returns default form url params
     *
     * This method should be included on the static controller implementation if any custom query strings
     * are necessary (ex: courseid)
     *
     * @return array
     */
    public function get_form_url_params() {
        return [];
    }

    // Session Input.
    /**
     * Stores the given input array's specified keys in the session input
     *
     * @param  array  $input
     * @param  array  $keeps       key names to keep, others will be ignored
     * @param  array  $overrides   optional keyed array of params to override any input given
     * @return void
     */
    public function store($input, $keeps = [], $overrides = []) {
        // Filter out any unwanted params from input.
        $data = \block_quickmail_plugin::array_filter_key((array) $input, function ($k) use ($keeps) {
            return in_array($k, $keeps);
        });

        // Fill any wanted data keys that do not exist in the filtered params with a default.
        foreach ($keeps as $k) {
            if (!in_array($k, array_keys($data))) {
                $data[$k] = '';
            }
        }

        $data = array_merge($data, $overrides);

        $this->session->add_data($data);
    }

    /**
     * Returns this controller's session input for a given key
     *
     * @param  string  $key  optional, if null, will return an array of all data
     * @return mixed
     */
    public function stored($key = null) {
        return $this->session->get_data($key);
    }

    /**
     * Reports whether or not any of the given request input data is different for the given keys
     *
     * @param  stdClass  $requestinput
     * @param  array     $keys             keys to check for change
     * @return bool
     */
    public function stored_has_changed($requestinput, $keys = []) {
        foreach ($keys as $key) {
            if ($this->session->has_data($key)) {
                if ($requestinput->$key !== $this->stored($key)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Removes all data from the current session that is associated with views after the given view
     *
     * @param  string  $view
     * @return void
     */
    public function clear_store_after_view($view) {
        $reset = false;

        // Iterate through this controller's view keys.
        foreach ($this->view_keys() as $viewkey) {
            // If this is the key, flag to remove all data from this point on.
            if ($viewkey == $view) {
                $reset = true;
                continue;
            }

            // If resetting, remove all values for each data key.
            if ($reset) {
                foreach ($this->view_data_keys($viewkey) as $viewdatakey) {
                    $this->session->forget_data($viewdatakey);
                }
            }
        }
    }

    // Helpers.
    /**
     * Sets the controllers properties upon instantiation
     *
     * @param array $payload
     */
    private function set_props($payload = []) {
        $this->context = null;
        $this->props = (object)[];

        foreach ($payload as $key => $value) {
            switch ($key) {
                case 'context':
                    $this->context = $value;
                    break;

                default:
                    $this->props->$key = $value;
                    break;
            }
        }
    }

    /**
     * Returns the static controller implementation's "session key"
     *
     * Note: this is the controller's class name
     *
     * @return string
     */
    private function get_store_key() {
        $parts = explode('\\', get_called_class());

        return end($parts);
    }

    /**
     * Returns the static controller's short name
     *
     * @return string
     */
    public static function get_controller_short_name() {
        return str_replace('_controller', '', explode('\\', static::class)[2]);
    }

    public function dd($thing) {
        var_dump($thing);die;
    }

}
