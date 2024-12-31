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
 * A lightweight mainly confirming installation works
 *
 * @package    tool_aiconnect
 * @copyright  2024 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_aiconnect;

/**
 * Basic setup and test run to confirm it installs
 *
 * @package tool_aiconnect
 */
class test_aiconnect extends \advanced_testcase {

    /**
     * The class with most of the functionality
     * @var $ai
     */
     public $ai;


    /**
     * Initialise everything
     *
     * @return void
     */
    public function setUp(): void {
        if (defined('TEST_LLM_APIKEY')) {
            set_config('apikey', TEST_LLM_APIKEY, 'tool_aiconnect');
            $this->ai = new ai\ai();
        } else {
            exit('Test will only run if TEST_LLM_APIKEY is defined in config.php');
        }
    }

     /**
      * Ask the LLM to do some maths
      * @return void
      */
    public function test_prompt_completion(): void {
        $this->resetAfterTest();
        if (!$this->ai) {
            $this->markTestSkipped();
        }
        $query = "What is 2 * 4?";
        $result = $this->ai->prompt_completion($query);
        $this->assertIsArray($result);
        $this->assertStringContainsString("8", $result['response']['choices'][0]['message']['content']);
    }
    /**
     * Confirm that an array of models are returned.
     * This may not work as expected with ollama
     * @return void
     */
    public function test_get_models(): void {
        $this->resetAfterTest();
        if (!$this->ai) {
            $this->markTestSkipped();
        }

        $result = $this->ai->get_models();
        $this->assertIsArray($result->models);
    }
}
