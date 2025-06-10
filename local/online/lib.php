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

interface online_semester_codes {
    const FALL1 = '1L';
    const FALL2 = '1P';
    const SPRING1 = '2D';
    const SPRING2 = '2L';
    const SUMMER1 = '3D';
    const SUMMER2 = '1D';
}

interface online_institution_codes {
    const ONLINE_SEM = 'CLSB';
    const LAW_SEM = 'LAWB';

    const ONLINE_FINAL = 'CLSE';
    const LAW_FINAL = 'LAWE';

    const ONLINE_CAMPUS = '01';
    const LAW_CAMPUS = '08';

    const ONLINE_INST = '1590';
    const LAW_INST = '1595';
}

abstract class online_source implements online_institution_codes, online_semester_codes {
    // An ONLINE source requires these.
    public $serviceId;
    public $username;
    public $password;
    public $wsdl;

    public function __construct($username, $password, $wsdl, $serviceId) {
        $this->username = $username;
        $this->password = $password;
        $this->wsdl = $wsdl;
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

        $invokeparams = $this->build_parameters($params);

        $response = $client->invoke($invokeparams)->invokeReturn;

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

    public function encode_semester($semesteryear, $semestername) {

        $partial = function ($year, $name) {
            return sprintf('%d%s', $year, $name);
        };

        switch ($semestername) {
            case 'First Fall':
                return $partial($semesteryear + 1, self::FALL1);
            case 'Second Fall':
                return $partial($semesteryear + 1, self::FALL2);
            case 'First Spring':
                return $partial($semesteryear, self::SPRING1);
            case 'Second Spring':
                return $partial($semesteryear, self::SPRING2);
            case 'First Summer':
                return $partial($semesteryear, self::SUMMER1);
            case 'Second Summer':
                return $partial($semesteryear + 1, self::SUMMER2);
        }
    }
}

abstract class online_teacher_format extends online_source {
    public function format_teacher($xmlteacher) {
        $primaryflag = trim($xmlteacher->PRIMARY_INSTRUCTOR);

        list($first, $last) = $this->parse_name($xmlteacher->INDIV_NAME);

        $teacher = new stdClass;

        $teacher->idnumber = (string) $xmlteacher->LSU_ID;
        $teacher->primary_flag = (string) $primaryflag == 'Y' ? 1 : 0;

        $teacher->firstname = $first;
        $teacher->lastname = $last;
        $teacher->username = (string) $xmlteacher->PRIMARY_ACCESS_ID;

        return $teacher;
    }
}

abstract class online_student_format extends online_source {
    const AUDIT = 'AU';

    public function format_student($xmlstudent) {
        $student = new stdClass;

        $student->idnumber = (string) $xmlstudent->LSU_ID;
        $student->credit_hours = (string) $xmlstudent->CREDIT_HRS;

        if (trim((string) $xmlstudent->GRADING_CODE) == self::AUDIT) {
            $student->student_audit = 1;
        }

        list($first, $last) = $this->parse_name($xmlstudent->INDIV_NAME);

        $student->username = (string) $xmlstudent->PRIMARY_ACCESS_ID;
        $student->firstname = $first;
        $student->lastname = $last;
        $student->user_ferpa = trim((string)$xmlstudent->WITHHOLD_DIR_FLG) == 'P' ? 1 : 0;

        return $student;
    }
}