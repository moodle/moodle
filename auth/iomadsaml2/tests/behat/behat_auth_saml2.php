<?php
// This file is part of Moodle
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
 * Behat tests for auth_iomadsaml2
 *
 * @package     auth_iomadsaml2
 * @author      Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright   2018 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

use auth_iomadsaml2\admin\iomadsaml2_settings;
use auth_iomadsaml2\task\metadata_refresh;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Exception\ExpectationException;
use Behat\Gherkin\Node\TableNode;

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Behat tests for auth_iomadsaml2
 *
 * @package     auth_iomadsaml2
 * @author      Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright   2018 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_auth_iomadsaml2 extends behat_base {
    /**
     * Confirms the Authentication plugin is enabled
     *
     * @param bool $enabled
     * @Given /^the authentication plugin iomadsaml2 is (disabled|enabled) +\# auth_iomadsaml2$/
     */
    public function the_authentication_plugin_is_enabled_auth_saml($enabled = true) {
        // If using IOMAD SAML2 functionality, ensure all sessions are reset.
        $this->reset_moodle_session();

        if (($enabled == 'disabled') || ($enabled === false)) {
            set_config('auth', '');
        } else {
            set_config('auth', 'iomadsaml2');
            $this->initialise_iomadsaml2();
        }

        \core\session\manager::gc(); // Remove stale sessions.
        core_plugin_manager::reset_caches();
    }

    /**
     * Goes to the login/self test page
     *
     * @param string $page
     * @Given /^I go to the (login|self-test) page +\# auth_iomadsaml2$/
     */
    public function i_go_to_the_login_page_auth_saml($page) {
        switch ($page){
            case 'login':
                $page = '/login/index.php';
                break;
            case 'self-test':
                $page = '/auth/iomadsaml2/test.php';
                break;
        }
        $this->getSession()->visit($this->locate_path($page));
    }

    /**
     * Go to the auth_iomadsaml2 login page.
     *
     * @param string $parameters
     * @When /^I go to the login page with "([^"]*)" +\# auth_iomadsaml2$/
     */
    public function i_go_to_the_login_page_with_auth_saml($parameters) {
        $this->getSession()->visit($this->locate_path("login/index.php?{$parameters}"));
    }

    /**
     * Log in as admin.
     *
     * @Given /^I am an administrator +\# auth_iomadsaml2$/
     */
    public function im_an_administrator_auth_saml() {
        return $this->execute('behat_auth::i_log_in_as', ['admin']);
    }

    /**
     * Go to the iomadsaml2 settings page.
     *
     * @Given /^I am on the iomadsaml2 settings page +\# auth_iomadsaml2$/
     * @Then /^I go to the iomadsaml2 settings page (?:again) +\# auth_iomadsaml2$/
     */
    public function i_go_to_the_samlsettings_page_auth_saml() {
        $this->getSession()->visit($this->locate_path('/admin/settings.php?section=authsettingiomadsaml2'));
    }

    /**
     * Change the setting to auth_saml
     *
     * @param string $field
     * @param string $value
     * @When /^I change the setting "([^"]*)" to "([^"]*)" +\# auth_iomadsaml2$/
     */
    public function i_change_the_setting_to_auth_saml($field, $value) {
        $this->execute('behat_forms::i_set_the_field_to', [$field, $value]);
    }

    /**
     * The setting should be auth_saml
     *
     * @param string $field
     * @param string $expectedvalue
     * @Given /^the setting "([^"]*)" should be "([^"]*)" +\# auth_iomadsaml2$/
     */
    public function the_setting_should_be_auth_saml($field, $expectedvalue) {
        $this->execute('behat_forms::the_field_matches_value', [$field, $expectedvalue]);
    }

    /**
     * Apply defaults
     */
    private function apply_defaults() {
        global $CFG;

        require_once($CFG->dirroot . '/auth/iomadsaml2/auth.php');

        // All integration test are over HTTP.
        set_config('cookiesecure', false);

        /** @var auth_plugin_iomadsaml2 $auth */
        $auth = get_auth_plugin('iomadsaml2');

        $defaults = array_merge($auth->defaults, [
            'autocreate'          => 1,
            'field_map_idnumber'  => 'uid',
            'field_map_email'     => 'email',
            'field_map_firstname' => 'firstname',
            'field_map_lastname'  => 'surname',
            'field_map_lang'      => 'lang',
        ]);

        foreach (['email', 'firstname', 'lastname', 'lang'] as $field) {
            $defaults["field_lock_{$field}"] = 'unlocked';
            $defaults["field_updatelocal_{$field}"] = 'oncreate';
        }

        foreach ($defaults as $key => $value) {
            set_config($key, $value, 'auth_iomadsaml2');
        }
    }

    /**
     * Initialise iomadsaml2
     */
    private function initialise_iomadsaml2() {
        $this->apply_defaults();
        require(__DIR__ . '/../../setup.php');
    }

    /**
     * Saml setting is set to auth_saml
     *
     * @param string $setting
     * @param string $value
     * @Given /^the iomadsaml2 setting "([^"]*)" is set to "([^"]*)" +\# auth_iomadsaml2$/
     */
    public function the_saml_setting_is_set_to_auth_saml($setting, $value) {
        $map = [];

        if ($setting == 'Dual Login') {
            $setting = 'duallogin';
            $map = [
                'no'      => iomadsaml2_settings::OPTION_DUAL_LOGIN_NO,
                'yes'     => iomadsaml2_settings::OPTION_DUAL_LOGIN_YES,
                'passive' => iomadsaml2_settings::OPTION_DUAL_LOGIN_PASSIVE,
            ];
        }

        if ($setting == 'Group rules') {
            $setting = 'grouprules';
        }

        if ($setting == 'Account blocking response type') {
            $setting = 'flagresponsetype';
            $map = [
                'display custom message'   => iomadsaml2_settings::OPTION_FLAGGED_LOGIN_MESSAGE,
                'redirect to external url' => iomadsaml2_settings::OPTION_FLAGGED_LOGIN_REDIRECT,
            ];
        }

        if ($setting == 'Redirect URL') {
            $setting = 'flagredirecturl';
        }

        if ($setting == 'Response message') {
            $setting = 'flagmessage';
        }

        $lowervalue = strtolower($value);
        $value = array_key_exists($lowervalue, $map) ? $map[$lowervalue] : $value;
        set_config($setting, $value, 'auth_iomadsaml2');
    }

    /**
     * Configures auth_iomadsaml2 to use the mock SAML IdP in tests/fixtures/mockidp.
     *
     * Also initialises certificates (if not done yet) and turns off secure cookies, in case you
     * are running Behat over http.
     *
     * @Given /^the mock SAML IdP is configured +\# auth_iomadsaml2$/
     */
    public function the_mock_saml_idp_is_configured() {
        global $CFG;
        $cert = file_get_contents(__DIR__ . '/../fixtures/mockidp/mock.crt');
        $cert = preg_replace('~(-----(BEGIN|END) CERTIFICATE-----)|\n~', '', $cert);
        $baseurl = $CFG->wwwroot . '/auth/iomadsaml2/tests/fixtures/mockidp';

        $metadata = <<<EOF
<md:EntityDescriptor entityID="{$baseurl}/idpmetadata.php" xmlns:md="urn:oasis:names:tc:SAML:2.0:metadata">
    <md:IDPSSODescriptor protocolSupportEnumeration="urn:oasis:names:tc:SAML:2.0:protocol" WantAuthnRequestsSigned="false">
        <md:KeyDescriptor>
            <KeyInfo xmlns="http://www.w3.org/2000/09/xmldsig#">
                <X509Data><X509Certificate>{$cert}</X509Certificate></X509Data>
            </KeyInfo>
        </md:KeyDescriptor>
        <md:SingleLogoutService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect"
            Location="{$baseurl}/slo.php" />
        <md:NameIDFormat>urn:oasis:names:tc:SAML:2.0:nameid-format:persistent</md:NameIDFormat>
        <md:SingleSignOnService Binding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect"
            Location="{$baseurl}/sso.php" />
    </md:IDPSSODescriptor>
</md:EntityDescriptor>
EOF;

        // Update the config setting using the same method used in the UI.
        $idpmetadata = new \auth_iomadsaml2\admin\setting_idpmetadata();
        $idpmetadata->set_updatedcallback('auth_iomadsaml2_update_idp_metadata');
        $idpmetadata->write_setting($metadata);

        // Allow insecure cookies for Behat testing.
        set_config('cookiesecure', '0');

        // Turn auth_iomadsaml2 debugging on, required for self-test feature.
        set_config('debug', '1', 'auth_iomadsaml2');

        $auth = get_auth_plugin('iomadsaml2');
        if (!$auth->is_configured()) {
            require_once(__DIR__ . '/../../setuplib.php');
            create_certificates($auth);
        }
    }

    /**
     * Confirms a user's login from the IdP, and returns information back to Moodle.
     *
     * This step must be used while at the mock IdP 'login' screen.
     *
     * @param mixed $passive
     * @param TableNode $data Table of attributes
     * @When /^the mock SAML IdP allows ((?:passive )?)login with the following attributes: +\# auth_iomadsaml2$/
     */
    public function the_mock_saml_idp_allows_login_with_the_following_attributes($passive, TableNode $data) {
        // Check the correct page is current.
        $this->find('xpath', '//h1[normalize-space(.)="Mock IdP login"]',
                new ExpectationException('Not on the IdP login page.', $this->getSession()));

        // Find out if it's in passive mode.
        $pagepassive = $this->getSession()->getDriver()->find('//h2[normalize-space(.)="Passive mode"]');
        if ($passive && !$pagepassive) {
            throw new ExpectationException('Expected passive mode, but not passive.', $this->getSession());
        } else if (!$passive && $pagepassive) {
            throw new ExpectationException('Expected not passive mode, but passive.', $this->getSession());
        }

        // Work out the JSON data.
        $out = new \stdClass();
        foreach ($data->getRowsHash() as $key => $value) {
            $out->{$key} = $value;
        }
        $json = json_encode($out);

        // Set the field and press the submit button.
        $this->getSession()->getDriver()->setValue('//textarea', $json);
        $this->getSession()->getDriver()->click('//button[@id="login"]');
    }

    /**
     * After a passive login attempt, when the IdP confirms that the user is not logged in.
     *
     * @Given /^the mock SAML IdP does not allow passive login +\# auth_iomadsaml2$/
     */
    public function the_mock_saml_idp_does_not_allow_passive_login() {
        // Check the correct page is current.
        $this->find('xpath', '//h1[normalize-space(.)="Mock IdP login"]',
                new ExpectationException('Not on the IdP login page.', $this->getSession()));

        $this->find('xpath', '//h2[normalize-space(.)="Passive mode"]',
                new ExpectationException('Expected passive mode, but not passive.', $this->getSession()));

        // Press the no-login button.
        $this->getSession()->getDriver()->click('//button[@id="nologin"]');
    }

    /**
     * Confirms logout from the IdP.
     *
     * This step must be used while at the mock IdP 'logout' screen.
     *
     * @When /^the mock SAML IdP confirms logout +\# auth_iomadsaml2$/
     */
    public function the_mock_saml_idp_confirms_logout() {
        // Check the correct page is current.
        $this->find('xpath', '//h1[normalize-space(.)="Mock IdP logout"]',
                new ExpectationException('Not on the IdP logout page.', $this->getSession()));

        // Press the submit button.
        $this->getSession()->getDriver()->click('//button');
    }

    /**
     * Sets a cookie (for use testing the autologin based on cookie).
     *
     * @param string $cookiename
     * @param array $value
     * @When /^the cookie "([^"]+)" is set to "([^"]+)" +\# auth_iomadsaml2$/
     */
    public function the_cookie_is_set_to($cookiename, $value) {
        $this->getSession()->getDriver()->executeScript('document.cookie = "' .
                addslashes_js($cookiename) . '=' . addslashes_js($value) . '";');
    }

    /**
     * Clears a cookie (for use testing the autologin based on cookie).
     *
     * @param string $cookiename
     * @When /^the cookie "([^"]+)" is removed +\# auth_iomadsaml2$/
     */
    public function the_cookie_is_removed($cookiename) {
        $this->getSession()->getDriver()->executeScript('document.cookie = "' .
                addslashes_js($cookiename) . '=; expires=Thu, 01 Jan 1970 00:00:00 GMT";');
    }
    /**
     * Visist iomadsaml2 login page.
     */
    private function visit_iomadsaml2_login_page() {
        $this->getSession()->visit($this->locate_path('http://simplesamlphp.test:8001/module.php/core/authenticate.php'));
    }

    /**
     * Reset iomadsaml2 session.
     */
    private function reset_iomadsaml2_session() {
        $this->visit_iomadsaml2_login_page();
        $this->getSession()->reset();
    }

    /**
     * Reset moodle session.
     */
    private function reset_moodle_session() {
        $this->i_go_to_the_login_page_with_auth_saml('saml=off');
        $this->getSession()->reset();
    }

    /**
     * Execute.
     *
     * @param string $contextapi context in which api is defined.
     * @param array $params list of params to pass.
     */
    protected function execute($contextapi, $params = []) {
        global $CFG;

        // We allow usage of depricated behat steps for now.
        $CFG->behat_usedeprecated = true;

        // If newer Moodle, use the correct version.
        if ($CFG->branch >= 29) {
            return parent::execute($contextapi, $params);
        }

        // Backported for Moodle 27 and 28.
        list($class, $method) = explode("::", $contextapi);
        $object = behat_context_helper::get($class);
        $object->setMinkParameter('base_url', $CFG->wwwroot);
        return call_user_func_array([$object, $method], $params);
    }
}
