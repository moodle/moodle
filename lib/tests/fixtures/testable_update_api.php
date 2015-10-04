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
 * @package     core
 * @subpackage  fixtures
 * @category    test
 * @copyright   2015 David Mudrak <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\update;

defined('MOODLE_INTERNAL') || die();

/**
 * Testable variant of \core\update\api class.
 *
 * Provides access to some protected methods we want to explicitly test and
 * bypass the actual cURL calls by providing fake responses.
 *
 * @copyright 2015 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_api extends api {

    /**
     * Provides access to the parent protected method.
     *
     * @param int $branch
     * @return string
     */
    public function convert_branch_numbering_format($branch) {
        return parent::convert_branch_numbering_format($branch);
    }

    /**
     * Returns fake URL of the pluginfo.php API end-point.
     *
     * @return string
     */
    protected function get_serviceurl_pluginfo() {
        return 'http://testab.le/api/pluginfo.php';
    }

    /**
     * Mimics the call to the given end-point service with the given parameters.
     *
     * This simulates a hypothetical plugins directory with a single plugin
     * 'foo_bar' available (with a single release).
     *
     * @param string $serviceurl
     * @param array $params
     * @return stdClass|bool
     */
    protected function call_service($serviceurl, array $params=array()) {

        $response = (object)array(
            'data' => null,
            'info' => null,
            'status' => null,
        );

        $foobarinfo = (object)array(
            'status' => 'OK',
            'apiver' => '1.3',
            'pluginfo' => (object)array(
                'id' => 42,
                'name' => 'Foo bar',
                'component' => 'foo_bar',
                'source' => '',
                'doc' => '',
                'bugs' => '',
                'discussion' => '',
                'version' => false,
            ),
        );

        $version2015093000info = (object)array(
            'id' => '6765',
            'version' => '2015093000',
            'release' => '',
            'maturity' => '50',
            'downloadurl' => 'http://mood.le/plugins/foo_bar/2015093000.zip',
            'downloadmd5' => 'd41d8cd98f00b204e9800998ecf8427e',
            'vcssystem' => '',
            'vcssystemother' => '',
            'vcsrepositoryurl' => '',
            'vcsbranch' => '',
            'vcstag' => '',
            'supportedmoodles' => array(
                (object)array(
                    'version' => '2015041700',
                    'release' => '2.9'
                ),
            )
        );

        if ($serviceurl === 'http://testab.le/api/pluginfo.php') {
            if (strpos($params['plugin'], 'foo_bar@') === 0) {
                $response->data = $foobarinfo;
                $response->info = array(
                    'http_code' => 200,
                );
                $response->status = '200 OK';

                if (substr($params['plugin'], -11) === '@2015093000') {
                    $response->data->pluginfo->version = $version2015093000info;
                }

            } else if ($params['plugin'] === 'foo_bar' and isset($params['branch']) and isset($params['minversion'])) {
                $response->data = $foobarinfo;
                $response->info = array(
                    'http_code' => 200,
                );
                $response->status = '200 OK';

                if ($params['minversion'] <= 2015093000) {
                    $response->data->pluginfo->version = $version2015093000info;
                }

            } else {
                $response->info = array(
                    'http_code' => 404,
                );
                $response->status = '404 Not Found (unknown plugin)';
            }

            return $response;

        } else {
            return 'This should not happen';
        }
    }
}
