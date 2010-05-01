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
 * XML-RPC client class
 */
class webservice_xmlrpc_client {
    /**
     * Execute client WS request with token authentication
     * @param string $entrypointurl
     * @param string $token
     * @param string $functionname
     * @param array $params
     * @return mixed
     */
    public function call($entrypointurl, $token, $functionname, $params) {
        global $DB, $CFG;

        //zend expects 0 based array with numeric indexes
        $params = array_values($params);

        //traditional Zend xmlrpc client call (integrating the token into the URL)
        require_once 'Zend/XmlRpc/Client.php';
        $serverurl = $entrypointurl."?wstoken=".$token;
        $client = new Zend_XmlRpc_Client($serverurl);
        $result = $client->call($functionname, $params);

        return $result;
    }
}