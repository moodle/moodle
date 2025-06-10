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

ini_set('default_socket_timeout', 300);

interface semester_codes {
    const FALL = '1S';
    const FALL1 = '1L';
    const FALL2 = '1P';
    const SPRING = '2S';
    const SPRING1 = '2D';
    const SPRING2 = '2L';
    const SUMMER = '3S';
    const SUMMER1 = '3D';
    const SUMMER2 = '1D';
    const WINTER_INT = '1T';
    const SPRING_INT = '2T';
    const SUMMER_INT = '3T';
}

interface institution_codes {
    const LSU_SEM = 'CLSB';
    const ONLINE_SEM = 'CLSB';
    const LAW_SEM = 'LAWB';

    const LSU_FINAL = 'CLSE';
    const ONLINE_FINAL = 'CLSE';
    const LAW_FINAL = 'LAWE';

    const LSU_CAMPUS = '01';
    const ONLINE_CAMPUS = '01';
    const LAW_CAMPUS = '08';

    const LSU_INST = '1590';
    const ONLINE_INST = '1590';
    const LAW_INST = '1595';
}

abstract class lsu_source implements institution_codes, semester_codes {

    // An LSU source requires these.
    public $serviceId;
    public $username;
    public $password;
    public $wsdl;

    public function __construct($username, $password, $wsdl, $serviceId) {
        $this->username  = $username;
        $this->password  = $password;
        $this->wsdl      = $wsdl;
        $this->serviceId = $serviceId;
    }

    protected function build_parameters(array $params) {
        return array (
            'widget1' => $this->username,
            'widget2' => $this->password,
            'serviceId' => $this->serviceId,
            'parameters' => $params
        );
    }

    protected function escape_illegals($response) {
        $convertables = array(
            '/s?&s?/' => ' &amp; ',
        );
        foreach ($convertables as $pattern => $replaced) {
            $response = preg_replace($pattern, $replaced, $response);
        }
        return $response;
    }

    protected function clean_response($response) {
        $clean = $this->escape_illegals($response);

        $contents = $clean;
        return $contents;
    }

    public function invoke($params) {
        $client = new SoapClient($this->wsdl, array('connection_timeout' => 3600));

        $invoke_params = $this->build_parameters($params);

        $response = $client->invoke($invoke_params)->invokeReturn;

        return new SimpleXmlElement($this->clean_response($response));
    }

    public function parse_date($date) {
        $parts = explode('-', $date);
        return mktime(0, 0, 0, $parts[1], $parts[2], $parts[0]);
    }

    public function parse_name($fullname) {
        list($lastname, $fm) = explode(',', $fullname);
        $other = explode(' ', trim($fm));

        $first = $other[0];

        if (strlen($first) == 1) {
            $first = $first . ' ' . $other[1];
        }

        return array($first, $lastname);
    }

    public function encode_semester($semester_year, $semester_name) {

        $partial = function ($year, $name) {
            return sprintf('%d%s', $year, $name);
        };

        switch ($semester_name) {
            case 'Fall':
                return $partial($semester_year + 1, self::FALL);
            case 'First Fall':
                return $partial($semester_year + 1, self::FALL1);
            case 'Second Fall':
                return $partial($semester_year + 1, self::FALL2);
            case 'WinterInt':
                return $partial($semester_year + 1, self::WINTER_INT);
            case 'Summer':
                return $partial($semester_year, self::SUMMER);
            case 'First Summer':
                return $partial($semester_year, self::SUMMER1);
            case 'Second Summer':
                return $partial($semester_year + 1, self::SUMMER2);
            case 'Spring':
                return $partial($semester_year, self::SPRING);
            case 'First Spring':
                return $partial($semester_year, self::SPRING1);
            case 'Second Spring':
                return $partial($semester_year, self::SPRING2);
            case 'SummerInt':
                return $partial($semester_year, self::SUMMER_INT);
            case 'SpringInt':
                return $partial($semester_year, self::SPRING_INT);
        }
    }
}

abstract class lsu_teacher_format extends lsu_source {
    public function format_teacher($xml_teacher) {
        $primary_flag = trim($xml_teacher->PRIMARY_INSTRUCTOR);

        list($first, $last) = $this->parse_name($xml_teacher->INDIV_NAME);

        $teacher = new stdClass;

        $teacher->idnumber = (string) $xml_teacher->LSU_ID;
        $teacher->primary_flag = (string) $primary_flag == 'Y' ? 1 : 0;

        $teacher->firstname = $first;
        $teacher->lastname = $last;
        $teacher->username = (string) $xml_teacher->PRIMARY_ACCESS_ID;

        return $teacher;
    }
}

abstract class lsu_student_format extends lsu_source {
    const AUDIT = 'AU';

    public function format_student($xml_student) {
        $student = new stdClass;

        $student->idnumber = (string) $xml_student->LSU_ID;
        $student->credit_hours = (string) $xml_student->CREDIT_HRS;

        if (trim((string) $xml_student->GRADING_CODE) == self::AUDIT) {
            $student->student_audit = 1;
        }

        list($first, $last) = $this->parse_name($xml_student->INDIV_NAME);

        $student->username = (string) $xml_student->PRIMARY_ACCESS_ID;
        $student->firstname = $first;
        $student->lastname = $last;
        $student->user_ferpa = trim((string)$xml_student->WITHHOLD_DIR_FLG) == 'P' ? 1 : 0;

        return $student;
    }
}
