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

require_once(dirname(__FILE__) . '/../../../user/profile/lib.php');
require_once(dirname(__FILE__) . '/../../../local/iomad/lib/company.php');

class EmailVars {
    // Objects the vars refer to.
    protected $user = null;
    protected $course = null;
    protected $site = null;
    protected $company = null;
    protected $invoice = null;
    protected $classroom = null;
    protected $license = null;
    protected $url = null;
    protected $sender = null;
    protected $approveuser = null;
    protected $nugget = null;
    protected $event = null;

    protected $blank = "[blank]";

    /**
     * Constructor
     *
     * Sets up and retrieves the API objects
     *
     **/
    public function __construct($company, $user, $course, $invoice, $classroom, $license, $sender, $approveuser, $nugget, $event) {
        $this->company =& $company;
        $this->user =& $user;
        $this->invoice =& $invoice;
        $this->classroom =& $classroom;
        $this->license =& $license;
        $this->sender =& $sender;
        $this->approveuser =& $approveuser;
        $this->nugget =& $nugget;
        $this->event =& $event;

        if (!isset($this->company)) {
            if (isset($user->id)) {
                $this->company = company::by_userid($user->id);
            }
        }

        $this->course =& $course;
        if (!empty($course->id)) {
            $this->course->url = new moodle_url($this->company->get_wwwroot() .'/course/view.php', array('id' => $this->course->id));
        }
        if (!empty($user->id)) {
            $this->url = new moodle_url($this->company->get_wwwroot() .'/user/profile.php', array('id' => $this->user->id));
        }
        $this->site = get_site();
    }

    /**
     * Check whether it is ok to call certain methods of this class as a substitution var
     *
     * Parameters - $methodname = text;
     *
     * Returns text.
     *
     **/
    private static function ok2call($methodname) {
        return ($methodname != "vars" && $methodname != "__construct" && $methodname != "__get" && $methodname != "ok2call");
    }

    /**
     * Set up all the methods that can be called and used for substitution var in email templates.
     *
     * Returns array();
     *
     **/
    public static function vars() {
        $reflection = new ReflectionClass("EmailVars");
        $amethods = $reflection->getMethods();

        // These fields refer to the objects declared at the top of this class. User_ -> $this->user, etc.
        $result = array(
            // User fields.
                        'User_FirstName', 'User_LastName', 'User_Email', 'User_Username', 'User_Newpassword',
                        'User_ICQ', 'User_Skype', 'User_Yahoo', 'User_AIM', 'User_MSN', 'User_Phone1', 'User_Phone2',
                        'User_Institution', 'User_Department',
                        'User_Address', 'User_City', 'User_Country',
            // Approvals stuffs.
                        'Approveuser_FirstName', 'Approveuser_LastName',
            // Course fields .
                        'Course_FullName', 'Course_ShortName', 'CourseURL', 'Course_ReportText',
            // ClassRoom fields.
                        'Classroom_Name', 'Classroom_Address', 'Classroom_Postcode', 'Classroom_City',
                        'Classroom_Country', 'Classroom_Capacity', 'Classroom_Summary', 'Classroom_Time',
            // Site fields.
                        'Site_FullName', 'Site_ShortName', 'Site_Summary',
            // Company fields.
                        'Company_Name', 'Company_City', 'Company_ShortName', 'Company_Country',
            // Invoice fields.
                        'Invoice_Itemized', 'Invoice_FirstName', 'Invoice_LastName', 'Invoice_Company', 'Invoice_Reference',
            // License fields.
                        'License_Length', 'License_Valid',
            // Sender information fields .
                        'Sender_FirstName', 'Sender_LastName', 'Sender_Email',
            // Activity information fields .
                        'Activity_Name', 'Activity_Duedate',
            // Miscellaneouss fields.
                        'LinkURL', 'SiteURL', 'Event_Name',
            // Microlearning fields.
                        'Nugget_Name', 'Nugget_URL'
        );

        // Add all methods of this class that are ok2call to the $result array as well.
        // This means you can add extra methods to this class to cope with values that don't fit in objects mentioned above.
        // Or to create methods with specific formatting of the values (just don't give those methods names starting with
        // 'User_', 'Course_', etc).
        foreach ($amethods as $method) {
            if (self::ok2call($method->name)) {
                $result[] = $method->name;
            }
        }

        return $result;
    }

    /**
     * trap calls to non-existent methods of this class, that can then be routed to the appropriate objects.
     *
     **/
    public function __get($name) {
        if (isset($name)) {
            if (array_key_exists($name, $this)) {
                return $this[$name];
            }

            preg_match('/^(.*)_(.*)$/', $name, $matches);
            if (isset($matches[1])) {
                $object = strtolower($matches[1]);
                $property = strtolower($matches[2]);

                if (isset($this->$object->$property)) {
                    return $this->$object->$property;
                } else if (method_exists($this->$object, '__get')) {
                    return $this->$object->__get($property);
                } else if (method_exists($this->$object, 'get')) {
                    return $this->$object->get($property);
                } else {
                    return $this->blank;
                }
            } else if (self::ok2call($name)) {
                return $this->$name();
            }
        }
    }

    /**
     * Provide the CourseURL method for templates.
     *
     * returns text;
     *
     **/
    function CourseURL() {
        global $CFG;

        if (empty($CFG->allowthemechangeonurl)) {
            return $this->course->url;
        } else {
            // Get the company theme.
            if (method_exists($this->company,'get_theme')) {
                $theme = $this->company->get_theme();
                return new moodle_url($this->course->url, array('theme' => $theme));
            } else {
                return new moodle_url($this->course->url);
            }
        }
    }

    /**
     * Provide the SiteURL method for templates.
     *
     * returns text;
     *
     **/
    function SiteURL() {
        global $CFG;

        // Get the company wwwroot.
        $wwwroot = $this->company->get_wwwroot();

        // Can we add the theme to the URL too?
        if (empty($CFG->allowthemechangeonurl)) {
            return $wwwroot;
        } else {
            // Get the company theme.
            if (method_exists($this->company,'get_theme')) {
                $theme = $this->company->get_theme();
                return new moodle_url($wwwroot, array('theme' => $theme));
            } else {
                return new moodle_url($wwwroot);
            }
        }
    }

    /**
     * Provide the LinkURL method for templates.
     *
     * returns text;
     *
     **/
    function LinkURL() {
        global $CFG;

        if (empty($CFG->allowthemechangeonurl)) {
            return $this->url;
        } else {
            // Get the company theme.
            if (method_exists($this->company,'get_theme')) {
                $theme = $this->company->get_theme();
                return new moodle_url($this->url, array('theme' => $theme));
            } else {
                return new moodle_url($this->url);
            }
        }
    }
}
