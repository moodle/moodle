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
 * Rest API base class mapping rest api methods to endpoints with http methods, args and post body.
 *
 * @package    core
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\oauth2;

use curl;
use coding_exception;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

/**
 * Rest API base class mapping rest api methods to endpoints with http methods, args and post body.
 *
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class rest {

    /** @var curl $curl */
    private $curl;

    /**
     * Constructor.
     *
     * @param curl $curl
     */
    public function __construct(curl $curl) {
        $this->curl = $curl;
    }

    /**
     * Abstract function to define the functions of the rest API.
     *
     * @return array Example:
     *  [ 'listFiles' => [ 'method' => 'get', 'args' => [ 'folder' => PARAM_STRING ], 'response'  => 'json' ] ]
     */
    public abstract function get_api_functions();

    /**
     * Call a function from the Api with a set of arguments and optional data.
     *
     * @param string $functionname
     * @param array $functionargs
     */
    public function call($functionname, $functionargs) {
        $functions = $this->get_api_functions();
        $supportedmethods = [ 'get', 'put', 'post', 'patch', 'head', 'delete' ];
        if (empty($functions[$functionname])) {
            throw new coding_exception('unsupported api functionname: ' . $functionname);
        }

        $method = $functions[$functionname]['method'];
        $endpoint = $functions[$functionname]['endpoint'];
        $responsetype = $functions[$functionname]['response'];
        if (!in_array($method, $supportedmethods)) {
            throw new coding_exception('unsupported api method: ' . $method);
        }

        $args = $functions[$functionname]['args'];
        $callargs = [];
        foreach ($args as $argname => $argtype) {
            if (isset($functionargs[$argname])) {
                $callargs[$argname] = clean_param($functionargs[$argname], $argtype);
            }
        }

        $response = $this->curl->$method($endpoint, $callargs);

        if ($this->curl->errno == 0) {
            if ($responsetype == 'json') {
                $json = json_decode($response);

                if (!empty($json->error)) {
                    throw new rest_exception($json->error->message, $json->error->code);
                }
                return $json;
            }
            return $response;
        } else {
            throw new rest_exception($this->curl->error, $this->curl->errno);
        }
    }
}
