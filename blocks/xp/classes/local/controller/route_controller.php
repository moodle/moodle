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
 * Route controller.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\controller;

use coding_exception;
use block_xp\local\routing\url;
use html_writer;

/**
 * Route controller class.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class route_controller implements controller {

    /** @var \block_xp\local\request The request. */
    protected $request;
    /** @var url The page URL, not relative to the router. */
    protected $pageurl;
    /** @var \renderer_base A renderer. */
    protected $renderer;
    /** @var \block_xp\local\routing\url_resolver URL resolver. */
    protected $urlresolver;

    /** @var array The combined request and optional parameters. */
    private $params;
    /** @var array The optional parameters. */
    private $optionalparams;

    /**
     * Define the page url.
     *
     * @return void
     */
    private function define_pageurl() {
        $paramsdef = array_reduce($this->define_optional_params(), function($carry, $item) {
            if (isset($item[3]) && !$item[3]) {
                // Do not return parameters which must not be in the URL.
                return $carry;
            }
            $carry[$item[0]] = $item[1];
            return $carry;
        }, []);

        $params = [];
        foreach ($this->optionalparams as $param => $value) {
            // Skip the ones which didn't make the cut.
            if (!array_key_exists($param, $paramsdef)) {
                continue;
            }
            // Skip arguments whose defaults values are the same.
            if ($paramsdef[$param] == $value) {
                continue;
            }
            // Finally, accept the parameter.
            $params[$param] = $value;
        }

        $this->pageurl = new url($this->request->get_url());
        $this->pageurl->params($params);
    }

    /**
     * Authentication.
     *
     * @return void
     */
    abstract protected function require_login();

    /**
     * Post authentication.
     *
     * Use this to initialise objects which you'll need throughout the request.
     *
     * @return void
     */
    protected function post_login() {
        $this->urlresolver = \block_xp\di::get('url_resolver');
    }

    /**
     * Add here all permissions checks related to accessing the page.
     *
     * @throws \moodle_exception When the conditions are not met.
     * @return void
     */
    abstract protected function permissions_checks();

    /**
     * Moodle page specifics.
     *
     * @return void
     */
    protected function page_setup() {
    }

    /**
     * Collect the parameters.
     *
     * @return void
     */
    private function collect_params() {
        $this->collect_optional_params();
        $this->params = $this->request->get_route()->get_params() + $this->optionalparams;
    }

    /**
     * The optional params expected.
     *
     * Using this format:
     * [
     *     ['paramname', $defaultvalue, PARAM_TYPE],
     *     ['paramname2', $defaultvalue, PARAM_TYPE, $includeinurl],
     *     ...
     * ]
     *
     * The parameter $includeinurl is optional and defaults to true. When false,
     * the value will not be added to the page URL, you can consider it as being
     * dismissed when the user navigated to another page. Make sure to pass it
     * around when you need it. It's useful for values such as 'confirm' which
     * you would want to automatically remove from the page URL.
     *
     * @return array
     */
    protected function define_optional_params() {
        return [];
    }

    /**
     * Read and compute the optional params.
     *
     * This should only be used once, to read the parameters, refer to {@see self::get_param}.
     *
     * @return array
     */
    private function collect_optional_params() {
        $this->optionalparams = array_reduce($this->define_optional_params(), function($carry, $data) {
            $carry[$data[0]] = optional_param($data[0], $data[1], $data[2]);
            return $carry;
        }, []);
    }

    /**
     * Read one of the parameters.
     *
     * This includes request, and GET/POST parameters.
     *
     * @param string $name The parameter name.
     * @return mixed
     */
    final protected function get_param($name) {
        if (!array_key_exists($name, $this->params)) {
            throw new \coding_exception('Unknown parameter: ' . $name);
        }
        return $this->params[$name];
    }

    /**
     * Get the renderer.
     *
     * @return \renderer_base
     */
    protected function get_renderer() {
        if (!$this->renderer) {
            $this->renderer = \block_xp\di::get('renderer');
        }
        return $this->renderer;
    }

    /**
     * Handle the request.
     *
     * @param \block_xp\local\routing\request $request The request.
     * @return void
     */
    final public function handle(\block_xp\local\routing\request $request) {
        if (!$request instanceof \block_xp\local\routing\routed_request) {
            throw new coding_exception('Routed request must be used here...');
        }
        $this->request = $request;
        $this->collect_params();
        $this->define_pageurl();
        $this->require_login();
        $this->post_login();
        $this->permissions_checks();
        $this->page_setup();
        $this->pre_content();
        $this->start();
        $this->content();
        $this->end();
    }

    /**
     * What needs to be done prior to any output.
     *
     * This is the place you want to initiate redirections from.
     *
     * @return void
     */
    protected function pre_content() {
    }

    /**
     * Start the output.
     *
     * @return void
     */
    final protected function start() {
        echo $this->get_renderer()->header();
        echo html_writer::start_div('block_xp');
    }

    /**
     * Echo the content.
     *
     * @return void
     */
    abstract protected function content();

    /**
     * Finalise the output.
     *
     * @return void
     */
    final protected function end() {
        echo html_writer::end_div();
        echo $this->get_renderer()->footer();
    }

    /**
     * Helper method to redirect.
     *
     * @param url $url The URL to go to.
     * @param string $message The redirect message.
     * @return void
     */
    final protected function redirect(url $url = null, $message = '') {
        if ($url === null) {
            $url = $this->pageurl;
        }
        redirect($url, $message);
    }
}
