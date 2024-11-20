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
 * Send statements to the LRS.
 *
 * @package   logstore_xapi
 * @copyright Jerret Fowler <jerrett.fowler@gmail.com>
 *            Ryan Smith <https://www.linkedin.com/in/ryan-smith-uk/>
 *            David Pesce <david.pesce@exputo.com>
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace src\loader\lrs;

use src\loader\utils as utils;

/**
 * Send statements to an LRS.
 *
 * @param array $config An array of configuration settings.
 * @param array $events An array of events.
 * @return array
 */
function load(array $config, array $events) {
    $sendhttpstatements = function (array $config, array $statements) {
        $endpoint = $config['lrs_endpoint'];
        $username = $config['lrs_username'];
        $password = $config['lrs_password'];

        $url = utils\correct_endpoint($endpoint).'/statements';
        $auth = base64_encode($username.':'.$password);
        $postdata = json_encode($statements);

        if ($postdata === false) {
            throw new \Exception('JSON encode error: '.json_last_error_msg());
        }

        $request = curl_init();
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($request, CURLOPT_HTTPHEADER, [
            'Authorization: Basic '.$auth,
            'X-Experience-API-Version: 1.0.0',
            'Content-Type: application/json',
        ]);

        $responsetext = curl_exec($request);
        $responsecode = curl_getinfo($request, CURLINFO_RESPONSE_CODE);
        curl_close($request);

        if ($responsecode !== 200) {
            throw new \Exception($responsetext, $responsecode);
        }
    };
    return utils\load_in_batches($config, $events, $sendhttpstatements);
}
