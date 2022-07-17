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
 * File containing tests for generating an enrol plugin type.
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

/**
 * Enrol test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_enrol_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'enrol_test',
        'name'      => 'Enrol test',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'features'  => array(
            'settings' => true,
        ),
        'enrol_features' => array(
            'allow_enrol' => false,
            'allow_unenrol' => false,
            'allow_unenrol_user' => true,
            'allow_manage' => true,
        ),
        'capabilities' => array(
            array(
                'name' => 'enrol',
                'title' => 'Enrol user',
                'captype' => 'write',
                'contextlevel' => 'CONTEXT_COURSE',
                'archetypes' => array(
                    array(
                        'role' => 'manager',
                        'permission' => 'CAP_ALLOW'
                    ),
                    array(
                        'role' => 'editingteacher',
                        'permission' => 'CAP_ALLOW'
                    )
                )
            ),
            array(
                'name' => 'unenrol',
                'title' => 'Unenrol user',
                'captype' => 'write',
                'contextlevel' => 'CONTEXT_COURSE',
                'archetypes' => array(
                    array(
                        'role' => 'manager',
                        'permission' => 'CAP_ALLOW'
                    ),
                    array(
                        'role' => 'editingteacher',
                        'permission' => 'CAP_ALLOW'
                    )
                )
            ),
            array(
                'name' => 'manage',
                'title' => 'Manage users',
                'captype' => 'write',
                'contextlevel' => 'CONTEXT_COURSE',
                'archetypes' => array(
                    array(
                        'role' => 'manager',
                        'permission' => 'CAP_ALLOW'
                    ),
                    array(
                        'role' => 'editingteacher',
                        'permission' => 'CAP_ALLOW'
                    )
                )
            )
        )
    );

    /**
     * Tests creating the lib.php file.
     */
    public function test_enrol_lib_php() {
        $logger = new Logger('enroltest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();

        $this->assertArrayHasKey('lib.php', $files);
        $libfile = $files['lib.php'];

        list($type, $enrolname) = \core_component::normalize_component($recipe['component']);
        $description = 'The enrol plugin '.$enrolname.' is defined here.';
        $this->assertStringContainsString($description, $libfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die()";
        $this->assertStringContainsString($moodleinternal, $libfile);

        $classdefinition = 'class '.$recipe['component'].'_plugin extends enrol_plugin';
        $this->assertStringContainsString($classdefinition, $libfile);

        $returnvalue = $recipe['enrol_features']['allow_enrol'] == true ? 'true' : 'false';
        $allowenrol = '/public function allow_enrol\(\$instance\) {\s+return '.$returnvalue.';/';
        $this->assertRegExp($allowenrol, $libfile);

        $returnvalue = $recipe['enrol_features']['allow_unenrol'] == true ? 'true' : 'false';
        $allowunenrol = '/public function allow_unenrol\(\$instance\) {\s+return '.$returnvalue.';/';
        $this->assertRegExp($allowunenrol, $libfile);

        $returnvalue = $recipe['enrol_features']['allow_manage'] == true ? 'true' : 'false';
        $allowmanage = '/public function allow_manage\(\$instance\) {\s+return '.$returnvalue.';/';
        $this->assertRegExp($allowmanage, $libfile);

        $returnvalue = $recipe['enrol_features']['allow_unenrol_user'] == true ? 'true' : 'false';
        $allowunenroluser = '/public function allow_unenrol_user\(\$instance\, \$ue\) {\s+return '.$returnvalue.';/';
        $this->assertRegExp($allowunenroluser, $libfile);
    }
}
