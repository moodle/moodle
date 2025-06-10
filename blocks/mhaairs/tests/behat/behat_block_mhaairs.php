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
 * Steps definitions.
 *
 * @package    block_mhaairs
 * @category   tests
 * @copyright  2015 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

use Behat\Behat\Context\Step\Given as Given;
use Behat\Gherkin\Node\TableNode as TableNode;
use Behat\Gherkin\Node\PyStringNode as PyStringNode;
use Moodle\BehatExtension\Exception\SkippedException as SkippedException;

/**
 * Mhaairs block steps definitions.
 *
 * @package    block_mhaairs
 * @category   tests
 * @copyright  2015 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_block_mhaairs extends behat_base {

    /**
     * Sets the customer number and shared secret from bht config in the settings form.
     *
     * @Given /^I set the mhaairs customer number and shared secret$/
     */
    public function set_the_mhaairs_customer_number_and_shared_secret() {
        $customernumber = get_config(null, 'behat_mhaairs_customer_number');
        $sharedsecret = get_config(null, 'behat_mhaairs_shared_secret');

        // Skip if customer number or shared secret are not set.
        if (!$customernumber or !$sharedsecret) {
            throw new SkippedException;
        }

        $data = array(
            'Customer Number' => $customernumber,
            'Shared Secret' => $sharedsecret,
        );
        $table = new TableNode($data);
        $step = 'behat_forms::i_set_the_following_fields_to_these_values';
        $this->own_execute($step, array($table));
    }

    /**
     * Sets the customer number and shared secret from bht config.
     *
     * @Given /^the mhaairs customer number and shared secret are set$/
     */
    public function the_mhaairs_customer_number_and_shared_secret_are_set() {
        $customernumber = get_config(null, 'behat_mhaairs_customer_number');
        $sharedsecret = get_config(null, 'behat_mhaairs_shared_secret');

        // Skip if customer number or shared secret are not set.
        if (!$customernumber or !$sharedsecret) {
            throw new SkippedException;
        }

        set_config('block_mhaairs_customer_number', $customernumber);
        set_config('block_mhaairs_shared_secret', $sharedsecret);
    }

    /**
     * Enables existing web services.
     *
     * @Given /^the following web services are enabled:$/
     * @param TableNode $data
     */
    public function the_following_web_services_are_enabled(TableNode $data) {
        global $DB;

        foreach ($data->getHash() as $datahash) {

            $params = array();
            $params['component'] = $datahash['component'];
            if (!empty($datahash['shortname'])) {
                $params['shortname'] = $datahash['shortname'];
            } else if (!empty($datahash['name'])) {
                $params['name'] = $datahash['name'];
            } else {
                continue;
            }

            $DB->set_field('external_services', 'enabled', 1, $params);
        }
    }

    /**
     * Opens mhaairs help page and verify that it is not broken.
     *
     * @Given /^the mhaairs help page "(?P<name_string>(?:[^"]|\\")*)" is not broken$/
     */
    public function the_mhaairs_help_page_is_not_broken($name) {
        $windowname = get_config(null, 'behat_mhaairs_'. $name. '_window_name');
        $windowcontent = get_config(null, 'behat_mhaairs_'. $name. '_window_content');

        // Skip if window name and content are not set.
        if (!$windowname or !$windowcontent) {
            throw new SkippedException;
        }

        $linklabel = get_string($name. 'label', 'block_mhaairs');

        // Follow the help link.
        $step = 'behat_general::click_link';
        $this->own_execute($step, array($linklabel));

        // Switch to the window.
        $step = 'behat_general::switch_to_window';
        $this->own_execute($step, array($windowname));

        // Verify page by content.
        $step = 'behat_general::assert_page_contains_text';
        $this->own_execute($step, array($windowcontent));

        // Switch to the main window.
        $step = 'behat_general::switch_to_the_main_window';
        $this->own_execute($step, array());
    }

    /**
     * Creates tokens.
     *
     * @Given /^the following tokens exist:$/
     * @param TableNode $data
     */
    public function the_following_tokens_exist(TableNode $data) {
        global $DB, $CFG;

        foreach ($data->getHash() as $datahash) {

            $service = $this->get_service_id($datahash['service']);
            $userid = $this->get_user_id($datahash['user']);
            $validuntil = !empty($datahash['validuntil']) ? $datahash['validuntil'] : '';
            $iprestriction = !empty($datahash['iprestriction']) ? $datahash['iprestriction'] : '';

            require_once("$CFG->dirroot/webservice/lib.php");
            $webservicemanager = new webservice();

            // Check the the user is allowed for the service.
            $selectedservice = $webservicemanager->get_external_service_by_id($service);
            if ($selectedservice->restrictedusers) {
                $restricteduser = $webservicemanager->get_ws_authorised_user($service, $userid);
                if (empty($restricteduser)) {
                    throw new moodle_exception('usernotallowed', 'webservice');
                }
            }

            // Check if the user is deleted. unconfirmed, suspended or guest.
            $user = $DB->get_record('user', array('id' => $userid));
            if ($user->id == $CFG->siteguest or $user->deleted or !$user->confirmed or $user->suspended) {
                throw new moodle_exception('forbiddenwsuser', 'webservice');
            }

            external_generate_token(
                EXTERNAL_TOKEN_PERMANENT,
                $service,
                $userid,
                context_system::instance(),
                $validuntil,
                $iprestriction
            );
        }
    }

    /**
     * Sets the token field to the token of the specified user.
     * Starts in the web service test client form after the function
     * has been selected.
     *
     * @Given /^I set the token field to "(?P<username_string>(?:[^"]|\\")*)" token for "(?P<service_string>(?:[^"]|\\")*)" service$/
     *
     * @param string $username
     * @param string $service
     */
    public function i_set_the_token_field_to_token_for_service($username, $service) {
        global $DB;

        $params = array(
            'userid' => $this->get_user_id($username),
            'externalserviceid' => $this->get_service_id($service),
        );
        $token = $DB->get_field('external_tokens', 'token', $params);
        $step = 'behat_forms::i_set_the_field_to';
        $this->own_execute($step, array('token', $token));
    }

    /**
     * Gets the user id from it's username.
     * @throws Exception
     * @param string $username
     * @return int
     */
    protected function get_user_id($username) {
        global $DB;

        if (empty($username)) {
            return 0;
        }

        if (!$id = $DB->get_field('user', 'id', array('username' => $username))) {
            throw new Exception('The specified user with username "' . $username . '" does not exist');
        }
        return $id;
    }

    /**
     * Gets the service id from it's shortname.
     * @throws Exception
     * @param string $shortname
     * @return int
     */
    protected function get_service_id($shortname) {
        global $DB;

        if (empty($shortname)) {
            return 0;
        }

        if (!$id = $DB->get_field('external_services', 'id', array('shortname' => $shortname))) {
            throw new Exception('The specified service with shortname "' . $shortname . '" does not exist');
        }
        return $id;
    }

    /**
     * Helper function to execute api in a given context.
     * Backwards compatibility for pre 3.0 versions.
     * NOTE: Wait for pending js and look for exception should be called,
     * to avoid unexpected results. But they are not implemented here, so
     * pre 3.0 use might encounter issues.
     *
     * @param string $contextapi context in which api is defined.
     * @param array $params list of params to pass.
     * @throws Exception
     */
    protected function own_execute($contextapi, $params = array()) {
        if (method_exists($this, 'execute')) {
            $this->execute($contextapi, $params);
            return;
        }

        if (!is_array($params)) {
            $params = array($params);
        }

        // Get required context and execute the api.
        $contextapi = explode("::", $contextapi);
        $context = behat_context_helper::get($contextapi[0]);
        call_user_func_array(array($context, $contextapi[1]), $params);
    }
}
