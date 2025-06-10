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

defined('MOODLE_INTERNAL') || die();

abstract class lsu_provider_error_handlers {
    private static function reprocess_source($provider, $source, $semesterid) {
        $semester = ues_semester::get(array('id' => $semesterid));

        $provider->process_data_source($source, $semester);
    }

    public static function reprocess_student_data($enrol, $params) {
        $provider = $enrol->provider();
        $source = $provider->student_data_source();

        self::reprocess_source($provider, $source, $params['semesterid']);
    }

    public static function reprocess_anonymous_numbers($enrol, $params) {
        $provider = $enrol->provider();
        $source = $provider->anonymous_source();

        self::reprocess_source($provider, $source, $params['semesterid']);
    }

    public static function reprocess_degree_candidates($enrol, $params) {
        $provider = $enrol->provider();
        $source = $provider->degree_source();

        self::reprocess_source($provider, $source, $params['semesterid']);
    }

    public static function reprocess_sports_information($enrol, $params) {
        $provider = $enrol->provider();
        $source = $provider->sports_source();

        self::reprocess_source($provider, $source, $params['semesterid']);
    }
}
