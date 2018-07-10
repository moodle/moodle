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

class iomad_commerce {

    public static function update_company($company, $oldcompany) {

        $call = 'updateCompany';
        $payload = array('origname' => $oldcompany->name,
                         'newname' => $company->name);
        $response = self::docall($call, $payload);
    }

    public static function update_user($user) {

        $call = 'updateUser';
        if (empty($user->company)) {
            $user->company = 'Registered';
        }
        $payload = array('username' => $user->username,
                         'firstname' => $user->firstname,
                         'lastname' => $user->lastname,
                         'email' => $user->email,
                         'company' => $user->company,
                         'password' => $user->password,
                         'address' => $user->address,
                         'city' => $user->city,
                         'country' => $user->country,
                         'manager' => $user->manager);

        $response = self::docall($call, $payload);
    }

    public static function assign_user($user, $companyname) {

        $call = 'updateUser';
        if (empty($user->manager)) {
            $user->manager = 'no';
        }
        $payload = array('username' => $user->username,
                         'firstname' => $user->firstname,
                         'lastname' => $user->lastname,
                         'email' => $user->email,
                         'company' => $companyname,
                         'password' => $user->password,
                         'address' => $user->address,
                         'city' => $user->city,
                         'country' => $user->country,
                         'manager' => $user->manager);
        $response = self::docall($call, $payload);
    }

    public static function delete_user($username) {

        $call = 'deleteUser';
        $payload = array('username' => $username);
        self::docall($call, $payload);
    }

    private static function docall($call, $payload) {
        global $CFG;

        $wsdlurl = $CFG->commerce_externalshop_url . '/wp-content/plugins/wpiomadsoap/wsdl/wpiomadsoap.wsdl';
        $soapserverurl = $CFG->commerce_externalshop_url . '/?api=soap&version=v1';

        $client = new SoapClient($wsdlurl, array('cache_wsdl' => WSDL_CACHE_NONE, 'trace' => 1));
        try {
            $client->__setLocation($soapserverurl);
            $response = $client->__soapCall($call, $payload);
            return $response;
        } catch (SoapFault $e) {
            return $e->getMessage();
        }
        return $response;
    }
}