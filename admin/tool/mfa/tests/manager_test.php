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

namespace tool_mfa;
use tool_mfa\tool_mfa_trait;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/tool_mfa_trait.php');

/**
 * Tests for MFA manager class.
 *
 * @package     tool_mfa
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager_test extends \advanced_testcase {

    use tool_mfa_trait;

    /**
     * Tests getting the factor total weight
     *
     * @covers ::get_total_weight
     * @covers ::setup_user_factor
     */
    public function test_get_total_weight() {
        $this->resetAfterTest(true);

        // Create and login a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // First get weight with no active factors.
        $this->assertEquals(0, \tool_mfa\manager::get_total_weight());

        // Now setup a couple of input based factors.
        $this->set_factor_state('totp', 1, 100);

        $this->set_factor_state('email', 1, 100);

        // Check weight is still 0 with no passes.
        $this->assertEquals(0, \tool_mfa\manager::get_total_weight());

        // Manually pass 1 .
        $factor = \tool_mfa\plugininfo\factor::get_factor('totp');
        $totpdata = [
            'secret' => 'fakekey',
            'devicename' => 'fakedevice',
        ];
        $this->assertNotEmpty($factor->setup_user_factor((object) $totpdata));
        $factor->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
        $this->assertEquals(100, \tool_mfa\manager::get_total_weight());

        // Now both.
        $factor2 = \tool_mfa\plugininfo\factor::get_factor('email');
        $factor2->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
        $this->assertEquals(200, \tool_mfa\manager::get_total_weight());

        // Now setup a no input factor, and check that weight is automatically added without input.
        $this->set_factor_state('auth', 1, 100);
        set_config('goodauth', 'manual', 'factor_auth');

        $this->assertEquals(300, \tool_mfa\manager::get_total_weight());
    }

    /**
     * Tests getting the factor status
     *
     * @covers ::get_status
     */
    public function test_get_status() {
        $this->resetAfterTest(true);

        // Create and login a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Check for fail status with no factors.
        $this->assertEquals(\tool_mfa\plugininfo\factor::STATE_FAIL, \tool_mfa\manager::get_status());

        // Now add a no input factor.
        $this->set_factor_state('auth', 1, 100);
        set_config('goodauth', 'manual', 'factor_auth');

        // Check state is now passing.
        $this->assertEquals(\tool_mfa\plugininfo\factor::STATE_PASS, \tool_mfa\manager::get_status());

        // Now add a failure state factor, and ensure that fail takes precedent.
        $this->set_factor_state('email', 1, 100);
        $factoremail = \tool_mfa\plugininfo\factor::get_factor('email');
        $factoremail->set_state(\tool_mfa\plugininfo\factor::STATE_FAIL);

        $this->assertEquals(\tool_mfa\plugininfo\factor::STATE_FAIL, \tool_mfa\manager::get_status());

        // Remove no input factor, and remove fail state by logging in/out. Simulates no data entered yet.
        $this->setUser(null);
        $this->setUser($user);
        $this->set_factor_state('auth', 0, 100);
        $factoremail->set_state(\tool_mfa\plugininfo\factor::STATE_UNKNOWN);

        $this->assertEquals(\tool_mfa\plugininfo\factor::STATE_NEUTRAL, \tool_mfa\manager::get_status());
    }

    /**
     * Tests checking if passed enough factors
     *
     * @covers ::passed_enough_factors
     */
    public function test_passed_enough_factors() {
        $this->resetAfterTest(true);

        // Create and login a user.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Check when no factors are setup.
        $this->assertEquals(false, \tool_mfa\manager::passed_enough_factors());

        // Setup a no input factor.
        $this->set_factor_state('auth', 1, 100);
        set_config('goodauth', 'manual', 'factor_auth');

        // Check that is enough to pass.
        $this->assertEquals(true, \tool_mfa\manager::passed_enough_factors());

        // Lower the weight of the factor.
        $this->set_factor_state('auth', 1, 75);
        $this->assertEquals(false, \tool_mfa\manager::passed_enough_factors());

        // Add another factor to get enough weight to pass, but dont set pass state yet.
        $this->set_factor_state('email', 1, 100);
        $factoremail = \tool_mfa\plugininfo\factor::get_factor('email');
        $this->assertEquals(false, \tool_mfa\manager::passed_enough_factors());

        // Now pass the factor and check weight.
        $factoremail->set_state(\tool_mfa\plugininfo\factor::STATE_PASS);
        $this->assertEquals(true, \tool_mfa\manager::passed_enough_factors());
    }

    /**
     * The data provider for whether urls should be redirected or not
     *
     * @return  array
     */
    public static function should_redirect_urls_provider() {
        $badurl1 = new \moodle_url('/');
        $badparam1 = $badurl1->out();
        $badurl2 = new \moodle_url('admin/tool/mfa/auth.php');
        $badparam2 = $badurl2->out();
        return [
            ['/', 'http://test.server', true],
            ['/admin/tool/mfa/action.php', 'http://test.server', true],
            ['/admin/tool/mfa/factor/totp/settings.php', 'http://test.server', true],
            ['/', 'http://test.server', true, ['url' => $badparam1]],
            ['/', 'http://test.server', true, ['url' => $badparam2]],
            ['/admin/tool/mfa/auth.php', 'http://test.server', false],
            ['/admin/tool/mfa/auth.php', 'http://test.server/parent/directory', false],
            ['/admin/tool/mfa/action.php', 'http://test.server/parent/directory', true],
            ['/', 'http://test.server/parent/directory', true, ['url' => $badparam1]],
            ['/', 'http://test.server/parent/directory', true, ['url' => $badparam2]],
        ];
    }

    /**
     * Tests whether it should require mfa
     *
     * @covers ::should_require_mfa
     * @param string $urlstring
     * @param string $webroot
     * @param bool $status
     * @param array|null $params
     * @dataProvider should_redirect_urls_provider
     */
    public function test_should_require_mfa_urls($urlstring, $webroot, $status, $params = null) {
        $this->resetAfterTest(true);
        global $CFG;
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $CFG->wwwroot = $webroot;
        $url = new \moodle_url($urlstring, $params);
        $this->assertEquals($status, \tool_mfa\manager::should_require_mfa($url, false));
    }

    /**
     * Tests whether it should require the mfa checks
     *
     * @covers ::should_require_mfa
     */
    public function test_should_require_mfa_checks() {
        // Setup test and user.
        global $CFG;
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();

        $badurl = new \moodle_url('/');

        // Upgrade checks.
        $this->setAdminUser();
        // Mark the site as upgraded so it will not fail when running the unittest as a whole.
        $CFG->allversionshash = \core_component::get_all_versions_hash();
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $oldhash = $CFG->allversionshash;
        $CFG->allversionshash = 'abc';
        $this->assertEquals(\tool_mfa\manager::NO_REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $CFG->allversionshash = $oldhash;
        $upgradesettings = new \moodle_url('/admin/upgradesettings.php');
        $this->assertEquals(\tool_mfa\manager::NO_REDIRECT, \tool_mfa\manager::should_require_mfa($upgradesettings, false));
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));

        // Admin not setup.
        $this->setUser($user);
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $CFG->adminsetuppending = 1;
        $this->assertEquals(\tool_mfa\manager::NO_REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $CFG->adminsetuppending = 0;

        // Check prevent_redirect.
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $this->assertEquals(\tool_mfa\manager::NO_REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, true));

        // User not setup properly.
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $notsetup = clone($user);
        unset($notsetup->firstname);
        $this->setUser($notsetup);
        $this->assertEquals(\tool_mfa\manager::NO_REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $this->setUser($user);

        // Enrolment.
        $enrolurl = new \moodle_url('/enrol/index.php');
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $this->assertEquals(\tool_mfa\manager::NO_REDIRECT, \tool_mfa\manager::should_require_mfa($enrolurl, false));

        // Guest User.
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $this->setGuestUser();
        $this->assertEquals(\tool_mfa\manager::NO_REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $this->setUser($user);

        // Forced password changes.
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        set_user_preference('auth_forcepasswordchange', true);
        $this->assertEquals(\tool_mfa\manager::NO_REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        set_user_preference('auth_forcepasswordchange', false);

        // Login as check.
        $user2 = $this->getDataGenerator()->create_user();
        $syscontext = \context_system::instance();
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $this->setAdminUser();
        \core\session\manager::loginas($user2->id, $syscontext, false);
        $this->assertEquals(\tool_mfa\manager::NO_REDIRECT, \tool_mfa\manager::should_require_mfa($badurl, false));
        $this->setUser($user);
    }

    /**
     * Tests should require the mfa redirection loop
     *
     * @covers ::should_require_mfa
     */
    public function test_should_require_mfa_redirection_loop() {
        // Setup test and user.
        global $CFG, $SESSION;
        $CFG->wwwroot = 'http://phpunit.test';
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Set first referer url.
        $_SERVER['HTTP_REFERER'] = 'http://phpunit.test';
        $url = new \moodle_url('/');

        // Test you get three redirs then exception.
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        // Set count to threshold.
        $SESSION->mfa_redir_count = 5;
        $this->assertEquals(\tool_mfa\manager::REDIRECT_EXCEPTION, \tool_mfa\manager::should_require_mfa($url, false));
        // Reset session vars.
        unset($SESSION->mfa_redir_referer);
        unset($SESSION->mfa_redir_count);

        // Test 4 different redir urls.
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        $_SERVER['HTTP_REFERER'] = 'http://phpunit.test/2';
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        $_SERVER['HTTP_REFERER'] = 'http://phpunit3.test/3';
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        $_SERVER['HTTP_REFERER'] = 'http://phpunit4.test/4';
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        // Reset session vars.
        unset($SESSION->mfa_redir_referer);
        unset($SESSION->mfa_redir_count);

        // Test 6 then jump to new referer (5 + 1 to set the first time).
        $_SERVER['HTTP_REFERER'] = 'http://phpunit.test';
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));

        $_SERVER['HTTP_REFERER'] = 'http://phpunit.test/2';
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
        // Now test that going back to original URL doesnt cause exception.
        $_SERVER['HTTP_REFERER'] = 'http://phpunit.test';
        $this->assertEquals(\tool_mfa\manager::REDIRECT, \tool_mfa\manager::should_require_mfa($url, false));
    }

    /**
     * Tests checking for possible setup factor
     *
     * @covers ::possible_factor_setup
     * @covers ::setup_user_factor
     */
    public function test_possible_factor_setup() {
        // Setup test and user.
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Test for totp is able to be setup.
        set_config('enabled', 1, 'factor_totp');
        $this->assertTrue(\tool_mfa\manager::possible_factor_setup());
        set_config('enabled', 0, 'factor_totp');

        // Test TOTP is already setup and can be managed.
        $totp = \tool_mfa\plugininfo\factor::get_factor('totp');
        set_config('enabled', 1, 'factor_totp');
        $totpdata = [
            'secret' => 'fakekey',
            'devicename' => 'fakedevice',
        ];
        $this->assertNotEmpty($totp->setup_user_factor((object) $totpdata));
        $this->assertTrue(\tool_mfa\manager::possible_factor_setup());
        set_config('enabled', 0, 'factor_totp');

        // Test no factors can be setup.
        set_config('enabled', 1, 'factor_email');
        set_config('enabled', 1, 'factor_admin');
        $this->assertFalse(\tool_mfa\manager::possible_factor_setup());
        set_config('enabled', 0, 'factor_email');
        set_config('enabled', 0, 'factor_admin');
    }

    /**
     * Tests checking if a factor is ready
     *
     * @covers ::is_ready
     */
    public function test_is_ready() {
        // Setup test and user.
        global $CFG;
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        set_config('enabled', 1, 'factor_nosetup');
        set_config('enabled', 1, 'tool_mfa');

        // Capability Check.
        $this->assertTrue(\tool_mfa\manager::is_ready());
        // Swap to role without capability.
        $this->setGuestUser();
        $this->assertFalse(\tool_mfa\manager::is_ready());
        $this->setUser($user);

        // Enabled check.
        $this->assertTrue(\tool_mfa\manager::is_ready());
        set_config('enabled', 0, 'tool_mfa');
        $this->assertFalse(\tool_mfa\manager::is_ready());
        set_config('enabled', 1, 'tool_mfa');

        // Upgrade check.
        $this->assertTrue(\tool_mfa\manager::is_ready());
        $CFG->upgraderunning = true;
        $this->assertFalse(\tool_mfa\manager::is_ready());
        unset($CFG->upgraderunning);

        // No factors check.
        $this->assertTrue(\tool_mfa\manager::is_ready());
        set_config('enabled', 0, 'factor_nosetup');
        $this->assertFalse(\tool_mfa\manager::is_ready());
        set_config('enabled', 1, 'factor_nosetup');
    }

    /**
     * Tests core hooks
     *
     * @covers ::mfa_config_hook_test
     * @covers ::mfa_login_hook_test
     */
    public function test_core_hooks() {
        // Setup test and user.
        global $CFG, $SESSION;
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Require login to fire hooks. Config we get for free.
        require_login();

        $this->assertTrue($CFG->mfa_config_hook_test);
        $this->assertTrue($SESSION->mfa_login_hook_test);
    }

    /**
     * Tests circular redirect auth
     *
     * @covers ::should_require_mfa
     */
    public function test_circular_redirect_auth() {
        // Setup test and user.
        $this->resetAfterTest(true);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Spoof the referrer for the redirect check.
        $_SERVER['HTTP_REFERER'] = '/admin/tool/mfa/auth.php';
        $baseurl = new \moodle_url('/my/naughty/page.php');

        // After a single check, we should redirect.
        $this->assertEquals(\tool_mfa\manager::REDIRECT,
            \tool_mfa\manager::should_require_mfa($baseurl, false));

        // Now hammer it up to the threshold to emulate a repeated force browse from auth.php.
        for ($i = 0; $i < \tool_mfa\manager::REDIR_LOOP_THRESHOLD; $i++) {
            \tool_mfa\manager::should_require_mfa($baseurl, false);
        }

        // Now finally confirm that a 6th access attempt (after loop safety trigger) still redirects.
        $this->assertEquals(\tool_mfa\manager::REDIRECT,
            \tool_mfa\manager::should_require_mfa($baseurl, false));
    }
}
