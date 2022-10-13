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
 * File containing tests for the 'message_providers' feature.
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
 * Message_providers test class.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei alexandru.elisei@gmail.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_pluginskel_message_providers_testcase extends advanced_testcase {

    /** @var string[] The test recipe. */
    protected static $recipe = array(
        'component' => 'local_messageproviderstest',
        'name'      => 'Message_providers test',
        'copyright' => '2016 Alexandru Elisei <alexandru.elisei@gmail.com>',
        'message_providers' => array(
            array(
                'name' => 'submission',
                'title' => 'Submission title',
                'capability' => 'mod/quiz:emailnotifysubmission'
            ),
        )
    );

    /**
     * Test creating the message providers.
     */
    public function test_message_providers() {
        $logger = new Logger('messageproviderstest');
        $logger->pushHandler(new NullHandler());
        $manager = manager::instance($logger);

        $recipe = self::$recipe;
        $manager->load_recipe($recipe);
        $manager->make();

        $files = $manager->get_files_content();
        $this->assertArrayHasKey('db/messages.php', $files);
        $messagesfile = $files['db/messages.php'];

        // Verify the boilerplate.
        $description = 'Plugin message providers are defined here.';
        $this->assertStringContainsString($description, $messagesfile);

        $moodleinternal = "defined('MOODLE_INTERNAL') || die();";
        $this->assertStringContainsString($moodleinternal, $messagesfile);

        // Verify if the message provider has been generated correctly.
        $messageprovider = $recipe['message_providers'][0]['name'];
        $this->assertStringContainsString("'".$messageprovider."' => array(", $messagesfile);

        $capability = $recipe['message_providers'][0]['capability'];
        $this->assertStringContainsString("'capability' => '".$capability."'", $messagesfile);

        // Verify if the title string has been generated.
        $this->assertArrayHasKey('lang/en/'.$recipe['component'].'.php', $files);
        $langfile = $files['lang/en/'.$recipe['component'].'.php'];

        $langstring = "\$string['messageprovider:".$messageprovider."'] = '".$recipe['message_providers'][0]['title']."';";
        $this->assertStringContainsString($langstring, $langfile);
    }
}
