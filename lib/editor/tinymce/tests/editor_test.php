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
 * TinyMCE tests.
 *
 * @package   editor_tinymce
 * @category  phpunit
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * TinyMCE tests.
 *
 * @package   editor_tinymce
 * @category  phpunit
 * @copyright 2012 Petr Skoda {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_tinymce_testcase extends advanced_testcase {

    public function test_autoloading() {
        // Note: This test core_frankestyle calssloader.
        $this->assertTrue(class_exists('editor_tinymce_plugin'));
        $this->assertFalse(class_exists('editor_tinymce_plugin_xx_yy'));
        $this->assertFalse(class_exists('\editor_tinymce\plugin'));
    }

    public function test_toolbar_parsing() {
        global $CFG;
        require_once("$CFG->dirroot/lib/editorlib.php");
        require_once("$CFG->dirroot/lib/editor/tinymce/lib.php");

        $result = tinymce_texteditor::parse_toolbar_setting("bold,italic\npreview");
        $this->assertSame(array('bold,italic', 'preview'), $result);

        $result = tinymce_texteditor::parse_toolbar_setting("| bold,|italic*blink\rpreview\n\n| \n paste STYLE | ");
        $this->assertSame(array('bold,|,italic,blink', 'preview', 'paste,style'), $result);

        $result = tinymce_texteditor::parse_toolbar_setting("| \n\n| \n \r");
        $this->assertSame(array(), $result);

        $result = tinymce_texteditor::parse_toolbar_setting("one\ntwo\n\nthree\nfour\nfive\nsix\nseven\neight\nnine\nten");
        $this->assertSame(array('one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten'), $result);
    }

    public function test_add_button() {
        global $CFG;
        $plugin = new tinymce_testplugin(__DIR__);
        $config = get_config('editor_tinymce');
        $params = array(
            'moodle_config' => $config,
            'entity_encoding' => "raw",
            'plugins' => 'lists,table,style,layer,advhr,advlink,emotions,inlinepopups,' .
                'searchreplace,paste,directionality,fullscreen,nonbreaking,contextmenu,' .
                'insertdatetime,save,iespell,preview,print,noneditable,visualchars,' .
                'xhtmlxtras,template,pagebreak',
            'gecko_spellcheck' => true,
            'theme_advanced_font_sizes' => "1,2,3,4,5,6,7",
            'moodle_plugin_base' => "$CFG->wwwroot/lib/editor/tinymce/plugins/",
            'theme_advanced_font_sizes' => "1,2,3,4,5,6,7",
            'theme_advanced_layout_manager' => "SimpleLayout",
            'theme_advanced_buttons1' => 'one,two,|,three,four',
            'theme_advanced_buttons2' => 'five,six',
            'theme_advanced_buttons3' => 'seven,eight,|',
            'theme_advanced_buttons4' => '|,nine',
            'theme_advanced_buttons5' => 'ten,eleven,twelve',
            'theme_advanced_buttons6' => 'thirteen,fourteen',
            'theme_advanced_buttons7' => 'fiveteen',
            'theme_advanced_buttons' => 'zero', // this is a fake entry, it is not a button row.
            'theme_something' => 123,
        );

        // Count number of rows.
        $this->assertSame(7, $plugin->test_count_button_rows($params));

        // Find button - first button in a row.
        $this->assertSame(1, $plugin->test_find_button($params, 'one'));
        // Find button - last button in a row.
        $this->assertSame(4, $plugin->test_find_button($params, 'nine'));
        // Find button - middle button in a row.
        $this->assertSame(5, $plugin->test_find_button($params, 'eleven'));
        // Find button - the only button in a row.
        $this->assertSame(7, $plugin->test_find_button($params, 'fiveteen'));
        // Find button - button not present.
        $this->assertSame(false, $plugin->test_find_button($params, 'sixteen'));
        // Find button - button not present.
        $this->assertSame(false, $plugin->test_find_button($params, 'zero'));

        // Adding button in the beginning of the row.
        $this->assertTrue($plugin->test_add_button_before($params, 1, 'new1', '', true));
        $this->assertSame('new1,one,two,|,three,four', $params['theme_advanced_buttons1']);
        // Adding button that already exists (nothing changes).
        $this->assertTrue($plugin->test_add_button_before($params, 1, 'new1', '', true));
        $this->assertSame('new1,one,two,|,three,four', $params['theme_advanced_buttons1']);
        // Adding button before existing button.
        $this->assertTrue($plugin->test_add_button_before($params, 1, 'new2', 'two', true));
        $this->assertSame('new1,one,new2,two,|,three,four', $params['theme_advanced_buttons1']);
        // Adding button before another button that does not exist ($alwaysadd = false).
        $this->assertTrue($plugin->test_add_button_before($params, 4, 'new3', 'fiveteen', true));
        $this->assertSame('new3,|,nine', $params['theme_advanced_buttons4']);
        // Adding button before another button that does not exist ($alwaysadd = false).
        $this->assertFalse($plugin->test_add_button_before($params, 4, 'new4', 'fiveteen', false));
        $this->assertSame('new3,|,nine', $params['theme_advanced_buttons4']);
        // Adding button into non-existing 0 row.
        $this->assertTrue($plugin->test_add_button_before($params, 0, 'new9'));
        $this->assertSame('new9,new1,one,new2,two,|,three,four', $params['theme_advanced_buttons1']);
        $this->assertFalse(isset($params['theme_advanced_buttons0']));
        // Adding button into non-existing 9 row.
        $this->assertTrue($plugin->test_add_button_before($params, 9, 'new10'));
        $this->assertSame('new10,fiveteen', $params['theme_advanced_buttons7']);
        $this->assertFalse(isset($params['theme_advanced_buttons9']));

        // Adding button in the end of the row.
        $this->assertTrue($plugin->test_add_button_after($params, 5, 'new5', '', true));
        $this->assertSame('ten,eleven,twelve,new5', $params['theme_advanced_buttons5']);
        // Adding button that already exists.
        $this->assertTrue($plugin->test_add_button_after($params, 5, 'new5', '', true));
        $this->assertSame('ten,eleven,twelve,new5', $params['theme_advanced_buttons5']);
        // Adding button after the existing button.
        $this->assertTrue($plugin->test_add_button_after($params, 6, 'new6', 'thirteen', true));
        $this->assertSame('thirteen,new6,fourteen', $params['theme_advanced_buttons6']);
        // Adding button after another button that does not exist ($alwaysadd = true).
        $this->assertTrue($plugin->test_add_button_after($params, 6, 'new7', 'fiveteen', true));
        $this->assertSame('thirteen,new6,fourteen,new7', $params['theme_advanced_buttons6']);
        // Adding button after another button that does not exist ($alwaysadd = false).
        $this->assertFalse($plugin->test_add_button_after($params, 6, 'new8', 'fiveteen', false));
        $this->assertSame('thirteen,new6,fourteen,new7', $params['theme_advanced_buttons6']);
        // Adding button into non-existing 0 row.
        $this->assertTrue($plugin->test_add_button_after($params, 0, 'new11'));
        $this->assertSame('new9,new1,one,new2,two,|,three,four,new11', $params['theme_advanced_buttons1']);
        $this->assertFalse(isset($params['theme_advanced_buttons0']));
        // Adding button into non-existing 9 row.
        $this->assertTrue($plugin->test_add_button_after($params, 9, 'new12'));
        $this->assertSame('new10,fiveteen,new12', $params['theme_advanced_buttons7']);
        $this->assertFalse(isset($params['theme_advanced_buttons9']));
    }
}

/**
 * Pseudo plugin class for testing editor_tinymce_plugin protected methods
 *
 * @package   editor_tinymce
 * @category  phpunit
 * @copyright 2013 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tinymce_testplugin extends editor_tinymce_plugin {
    protected function update_init_params(array &$params, context $context, array $options = null) {
        // Empty function just to make a class not abstract.
    }

    public function test_count_button_rows(array &$params) {
        return parent::count_button_rows($params);
    }

    public function test_find_button(array &$params, $button) {
        return parent::find_button($params, $button);
    }

    public function test_add_button_after(array &$params, $row, $button, $after = '', $alwaysadd = true) {
        return parent::add_button_after($params, $row, $button, $after, $alwaysadd);
    }

    public function test_add_button_before(array &$params, $row, $button, $before = '', $alwaysadd = true) {
        return parent::add_button_before($params, $row, $button, $before, $alwaysadd);
    }
}