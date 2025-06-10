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
 * Steps definitions related to plagiarism_turnitin.
 *
 * @copyright 2018 Turnitin
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');
require_once(__DIR__ . '/../../vendor/autoload.php');

use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Mink\Exception\ExpectationException as ExpectationException;
use Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;
use Integrations\PhpSdk\TiiMembership;
use Integrations\PhpSdk\TurnitinAPI;

class behat_plagiarism_turnitin extends behat_base {

    /**
     * @Given I switch to iframe with locator :locator
     * @param String $locator
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function i_switch_to_iframe_with_locator($locator) {
        $iframe = $this->getSession()->getPage()->find("css", $locator);
        $iframename = $iframe->getAttribute("name");
        if ($iframename == "") {
            echo "\n\niFrame has no name. Let's name it.\n\n";
            $javascript = "(function(){
            var iframes = document.getElementsByTagName('iframe');
                for (var i = 0; i < iframes.length; i++) {
                    if (!iframes[i].name) {
                        iframes[i].name = 'iframe_number_' + (i + 1) ;
                    }
                }
            })()";
            $this->getSession()->executeScript($javascript);
            $iframe = $this->getSession()->getPage()->find("css", $locator);
            $iframename = $iframe->getAttribute("name");
            echo "\n\niFrame has new name:  " . $iframename . "\n\n";
        } else {
            echo "\n\niFrame already has a name: " . $iframename . "\n\n";
        }

        $this->getSession()->getDriver()->switchToIFrame($iframename);
    }

    /**
     * @Given I configure Turnitin URL
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function i_configure_turnitin_url() {
        $apiurl = getenv('TII_APIBASEURL');
        $javascript = "
            var option = document.createElement('option');
            option.setAttribute('value', '${apiurl}');
            var apiurl = document.createTextNode('${apiurl}');
            var select = document.querySelector('#id_plagiarism_turnitin_apiurl');
            option.appendChild(apiurl);
            select.appendChild(option);
        ";
        $this->getSession()->executeScript($javascript);
        $this->getSession()->getPage()->find("css", "#id_plagiarism_turnitin_apiurl")->selectOption($apiurl);
    }

    /**
     * @Given I configure Turnitin credentials
     */
    public function i_configure_turnitin_credentials() {
        $account = getenv('TII_ACCOUNT');
        $secret = getenv('TII_SECRET');

        $this->getSession()->getPage()->find("css", "#id_plagiarism_turnitin_accountid")->setValue($account);

        $this->getSession()->getPage()->find('css', '[title="Edit password"]')->click();
        $this->getSession()->getPage()->find("css", "#id_plagiarism_turnitin_secretkey")->setValue($secret);
    }

    /**
     * @Given I create a unique user with username :username
     * @param $username
     */
    public function i_create_a_unique_user($username) {
        $generator = testing_util::get_data_generator();
        $generator->create_user(array(
            'email' => uniqid($username, true) . '@example.com',
            'username' => $username,
            'password' => $username,
            'firstname' => $username,
            'lastname' => $username
        ));
    }

    /**
     * Makes sure user can see the exact number of text instances on the page.
     *
     * @Then /^I should see "(?P<textcount_number>\d+)" instances of "(?P<text_string>(?:[^"]|\\")*)" on the page$/
     * @throws ExpectationException Thrown by behat_base::find
     * @param int $textcount
     * @param string $text
     */
    public function i_should_see_textcount_instances_of_text_on_the_page($textcount, $text) {
        // Looking for all the matching nodes without any other descendant matching the
        // same xpath (we are using contains(., ....).
        $xpathliteral = behat_context_helper::escape($text);
        $xpath = "/descendant-or-self::*[contains(., $xpathliteral)]" .
            "[count(descendant::*[contains(., $xpathliteral)]) = 0]";

        try {
            $elements = $this->find_all('xpath', $xpath);
        } catch (ElementNotFoundException $e) {
            throw new ExpectationException('"' . $text . '" text was not found in the page', $this->getSession());
        }

        if (count($elements) != $textcount) {
            throw new ExpectationException('Found '.count($elements).' instances of the text '. $text.'. Expected '.$textcount,
                $this->getSession());
        }
    }

    /**
     * Poll 12 times over 2 minutes for an originality report. This should be enough time for the vast majority of cases.
     *
     * @Given /^I obtain an originality report for "(?P<student>(?:[^"]|\\")*)" on "(?P<modtype>(?:[^"]|\\")*)" "(?P<modname>(?:[^"]|\\")*)" on course "(?P<coursename>(?:[^"]|\\")*)"$/
     * @param string $student
     * @param string $modtype
     * @param string $modname
     * @param string $coursename
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function i_obtain_an_originality_report_for_student_on_modtype_assignmentname_on_course_coursename($student, $modtype, $modname, $coursename) {
        $reportfound = false;
        $count = 1;
        while (!$reportfound) {
            $this->execute('behat_general::i_run_the_scheduled_task', "\plagiarism_turnitin\\task\update_reports");
            $this->execute('behat_general::i_wait_seconds', 1);
            $this->execute('behat_navigation::i_am_on_course_homepage', $coursename);
            $this->execute('behat_general::click_link', $modname);

            switch($modtype) {
                case "assignment":
                    $this->execute('behat_navigation::i_navigate_to_in_current_page_administration', "View all submissions");
                    break;
                case "forum":
                    $this->execute('behat_general::click_link', "Forum post 1");
                    break;
                case "workshop":
                    $this->execute('behat_general::click_link', "Submission1");
                    break;
            }

            try {
                switch($modtype) {
                    case "assignment":
                        $this->execute('behat_general::row_column_of_table_should_contain', array($student, "File submissions", "generaltable", "%"));
                        break;
                    case "forum":
                    case "workshop":
                        $this->execute('behat_general::assert_element_contains_text', array("%", "div.origreport_score", "css_element"));
                        break;
                }
                break;
            } catch (Exception $e) {
                if ($count >= 12) {
                    throw new ElementNotFoundException($this->getSession());
                }
                $count++;
            }
        }
    }

    /**
     * @Given I accept the Turnitin EULA if necessary
     */
    public function i_accept_the_turnitin_eula_if_necessary() {
        try {
            $this->getSession()->getPage()->find("css", ".pp_turnitin_eula_link");

            $this->execute('behat_general::i_click_on', array(".pp_turnitin_eula_link", "css_element"));
            $this->execute('behat_general::wait_until_exists', array(".iframe-ltilaunch-eula", "css_element"));
            $this->i_switch_to_iframe_with_locator(".iframe-ltilaunch-eula");
            $this->execute('behat_general::i_click_on', array(".agree-button", "css_element"));
        } catch (Exception $e) {
            // EULA not found - so skip it.
        }
    }

    /**
     * @Given I accept the Turnitin EULA from the EV if necessary
     */
    public function i_accept_the_turnitin_eula_from_the_ev_if_necessary() {
        try {
            $this->getSession()->getPage()->find("css", ".agree-button");

            $this->execute('behat_general::i_click_on', array(".agree-button", "css_element"));
        } catch (Exception $e) {
            // EULA not found - so skip it.
        }
    }

    /**
     * Generic clicking action. Click on the element of the specified type.
     *
     * @When /^I click save changes button "(?P<element_string>(?:[^"]|\\")*)" "(?P<selector_string>[^"]*)"$/
     * @param string $selector type of element like css or xpath
     * @param string $locator to identify element
     */
    public function click_save_changes_button($selector, $locator, $exception = false, $node = false, $timeout = false) {
        try {
            $items = $this->find_all($selector, $locator, $exception, $node, $timeout);
            foreach($items as $element){

                if ($element->isVisible()) {
                    echo "Element is visible ";
                    $element->click();
                }
            }

        } catch (Exception $e) {
            throw new ElementNotFoundException($this->getSession());
        }
    }

    /**
     * @Given /^the following users will be created if they do not already exist:$/
     * @param TableNode $data
     * @throws Exception
     */
    public function the_following_users_will_be_created_if_they_do_not_already_exist(TableNode $data) {
        $newdata = array();
        $rowNum = 0;
        foreach ($data->getRows() as $row) {
            if (!$rowNum == 0) { // not header row
                $row[3] = str_replace('$account', getenv('TII_ACCOUNT'), $row[3]);
            }
            $rowNum++;
            $newdata[] = $row;
        }
        $tablenode = new TableNode($newdata);
        $this->execute('behat_data_generators::the_following_entities_exist', array('users', $tablenode));
    }

    /**
     * @Given /^I unenroll the user account "(?P<student>(?:[^"]|\\")*)" with the role "(?P<role>(?:[^"]|\\")*)" from the class in Turnitin$/
     * @throws Exception
     */
    public function i_unenroll_the_user_account_with_the_role_from_the_class_in_turnitin($student, $role) {
        global $DB;

        $course = $DB->get_record("course", array("fullname" => "Turnitin Behat EULA Test Course"), 'id', MUST_EXIST);
        $tiicourse = $DB->get_record('plagiarism_turnitin_courses', array("courseid" => $course->id), 'turnitin_cid', MUST_EXIST);

        // Get the user.
        $user = $DB->get_record("user", array("username" => $student), 'id', MUST_EXIST);
        $tiiuser = $DB->get_record('plagiarism_turnitin_users', array("userid" => $user->id), 'turnitin_uid', MUST_EXIST);

        $turnitincall = $this->behat_initialise_api(getenv('TII_ACCOUNT'), getenv('TII_SECRET'), getenv('TII_APIBASEURL'));

        // Find the membership IDs for this user/class/role and delete them.
        $membership = new TiiMembership();
        $membership->setClassId($tiicourse->turnitin_cid);
        $membership->setUserId($tiiuser->turnitin_uid);
        $membership->setRole($role);

        $response = $turnitincall->findMemberships($membership);
        $findmembership = $response->getMembership();
        $membershipids = $findmembership->getMembershipIds();

        try {
            foreach ($membershipids as $membershipid) {
                $membership->setMembershipId($membershipid);
                $turnitincall->deleteMembership($membership);
            }
        } catch (Exception $e) {
            // ignore exception.
        }
    }

    /**
     * Initialise the API object for a behat call.
     *
     * @return object \APITurnitin
     */
    public function behat_initialise_api( ) {
        global $CFG;

        $api = new TurnitinAPI(getenv('TII_ACCOUNT'), getenv('TII_APIBASEURL'), getenv('TII_SECRET'), 12);

        // Use Moodle's proxy settings if specified.
        if (!empty($CFG->proxyhost)) {
            $api->setProxyHost($CFG->proxyhost);
        }

        if (!empty($CFG->proxyport)) {
            $api->setProxyPort($CFG->proxyport);
        }

        if (!empty($CFG->proxyuser)) {
            $api->setProxyUser($CFG->proxyuser);
        }

        if (!empty($CFG->proxypassword)) {
            $api->setProxyPassword($CFG->proxypassword);
        }

        if (!empty($CFG->proxytype)) {
            $api->setProxyType($CFG->proxytype);
        }

        if (!empty($CFG->proxybypass)) {
            $api->setProxyBypass($CFG->proxybypass);
        }

        $api->setIntegrationVersion($CFG->version);
        $api->setPluginVersion(get_config('plagiarism_turnitin', 'version'));

        if (is_readable("$CFG->dataroot/moodleorgca.crt")) {
            $certificate = realpath("$CFG->dataroot/moodleorgca.crt");
            $api->setSSLCertificate($certificate);
        }

        return $api;
    }
}
