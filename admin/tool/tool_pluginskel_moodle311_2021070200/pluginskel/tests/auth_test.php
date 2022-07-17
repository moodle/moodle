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
 * File containing tests for generating an authentication plugin.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use Monolog\Logger;
use Monolog\Handler\NullHandler;
use tool_pluginskel\local\util\manager;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/setuplib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/pluginskel/vendor/autoload.php');
require_once(__DIR__.'/../locallib.php');

/**
 * Auth test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_auth_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'auth_test',
        'name'      => 'Auth test',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'auth_features' => array(
            'config_ui' => true,
            'description' => 'Auth plugin description',
            'can_edit_profile' => false,
            'is_internal' => true,
            'prevent_local_passwords' => false,
            'is_synchronised_with_external' => false,
            'can_reset_password' => false,
            'can_signup' => false,
            'can_confirm' => true,
            'can_be_manually_set' => false,
        )
    );

    /**
     * Tests creating the auth.php file.
     */
    public function test_auth_auth_php() {
        $logger = new Logger('authtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('auth.php', $files);
        $authfile = $files['auth.php'];

        list($type, $authname) = \core_component::normalize_component($recipe['component']);
        $description = 'Authentication class for '.$authname.' is defined here.';
        $this->assertStringContainsString($description, $authfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        $this->assertStringContainsString($moodleinternal, $authfile);

        $classdefinition = 'class auth_plugin_'.$authname.' extends auth_plugin_base';
        $this->assertStringContainsString($classdefinition, $authfile);

        $userlogin = 'public function user_login($username, $password)';
        $this->assertStringContainsString($userlogin, $authfile);

        $recipefeatures = array(
            'can_edit_profile',
            'prevent_local_passwords',
            'is_synchronised_with_external',
            'can_reset_password',
            'can_signup',
            'can_confirm',
            'can_be_manually_set',
            'is_internal'
        );

        foreach ($recipefeatures as $functionname) {
            $function = '/public function '.$functionname.'\(\) {';
            $returnvalue = $recipe['auth_features'][$functionname] == true ? 'true' : 'false';
            $function .= '\s+return '.$returnvalue.';/';
            $this->assertRegExp($function, $authfile);
        }

        $canchangepassword = 'public function can_change_password()';
        $this->assertStringNotContainsString($canchangepassword, $authfile);

        $configform = 'public function config_form($config, $err, $userfields)';
        $this->assertStringContainsString($configform, $authfile);

        $processconfig = 'public function process_config($config)';
        $this->assertStringContainsString($processconfig, $authfile);
    }

    /**
     * Tests creating the 'auth_description' lang string.
     */
    public function test_auth_lang_strings() {
        $logger = new Logger('authtest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('lang/en/'.$recipe['component'].'.php', $files);
        $langfile = $files['lang/en/'.$recipe['component'].'.php'];

        $descriptionstring = "\$string['auth_description'] = '".$recipe['auth_features']['description']."';";
        $this->assertStringContainsString($descriptionstring, $langfile);
    }
}
