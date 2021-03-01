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
 * Google Docs repository data generator
 *
 * @package    repository_googledocs
 * @category   test
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \core\oauth2\issuer;
use \core\oauth2\endpoint;

/**
 * Google Docs repository data generator class
 *
 * @package    repository_googledocs
 * @category   test
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_googledocs_generator extends testing_repository_generator {

    /**
     * Fill in type record defaults.
     *
     * @param array $record
     * @return array
     */
    protected function prepare_type_record(array $record) {
        $record = parent::prepare_type_record($record);
        $issuerrecord = (object) [
            'name' => 'Google',
            'image' => 'https://accounts.google.com/favicon.ico',
            'baseurl' => 'https://accounts.google.com/',
            'loginparamsoffline' => 'access_type=offline&prompt=consent',
            'showonloginpage' => issuer::EVERYWHERE
        ];

        $issuer = new issuer(0, $issuerrecord);
        $issuer->create();

        $endpointrecord = (object) [
            'issuerid' => $issuer->get('id'),
            'name' => 'discovery_endpoint',
            'url' => 'https://accounts.google.com/.well-known/openid-configuration'
        ];
        $endpoint = new endpoint(0, $endpointrecord);
        $endpoint->create();

        if (!isset($record['issuerid'])) {
            $record['issuerid'] = $issuer->get('id');
        }
        if (!isset($record['defaultreturntype'])) {
            $record['defaultreturntype'] = FILE_INTERNAL;
        }
        if (!isset($record['supportedreturntypes'])) {
            $record['supportedreturntypes'] = 'both';
        }
        if (!isset($record['documentformat'])) {
            $record['documentformat'] = 'pdf';
        }
        if (!isset($record['presentationformat'])) {
            $record['presentationformat'] = 'pdf';
        }
        if (!isset($record['drawingformat'])) {
            $record['drawingformat'] = 'pdf';
        }
        if (!isset($record['spreadsheetformat'])) {
            $record['spreadsheetformat'] = 'pdf';
        }
        return $record;
    }

}
