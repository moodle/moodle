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
 * Unit tests for lib/outputcomponents.php.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2011 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/outputcomponents.php');


/**
 * Unit tests for the user_picture class
 */
class user_picture_testcase extends advanced_testcase {

    public function test_fields_aliasing() {
        $fields = user_picture::fields();
        $fields = array_map('trim', explode(',', $fields));
        $this->assertTrue(in_array('id', $fields));

        $aliased = array();
        foreach ($fields as $field) {
            if ($field === 'id') {
                $aliased['id'] = 'aliasedid';
            } else {
                $aliased[$field] = 'prefix'.$field;
            }
        }

        $returned = user_picture::fields('', array('custom1', 'id'), 'aliasedid', 'prefix');
        $returned = array_map('trim', explode(',', $returned));
        $this->assertEquals(count($returned), count($fields) + 1); // only one extra field added

        foreach ($fields as $field) {
            if ($field === 'id') {
                $expected = "id AS aliasedid";
            } else {
                $expected = "$field AS prefix$field";
            }
            $this->assertTrue(in_array($expected, $returned), "Expected pattern '$expected' not returned");
        }
        $this->assertTrue(in_array("custom1 AS prefixcustom1", $returned), "Expected pattern 'custom1 AS prefixcustom1' not returned");
    }

    public function test_fields_unaliasing() {
        $fields = user_picture::fields();
        $fields = array_map('trim', explode(',', $fields));

        $fakerecord = new stdClass();
        $fakerecord->aliasedid = 42;
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $fakerecord->{'prefix'.$field} = "Value of $field";
            }
        }
        $fakerecord->prefixcustom1 = 'Value of custom1';

        $returned = user_picture::unalias($fakerecord, array('custom1'), 'aliasedid', 'prefix');

        $this->assertEquals($returned->id, 42);
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $this->assertEquals($returned->{$field}, "Value of $field");
            }
        }
        $this->assertEquals($returned->custom1, 'Value of custom1');
    }

    public function test_fields_unaliasing_null() {
        $fields = user_picture::fields();
        $fields = array_map('trim', explode(',', $fields));

        $fakerecord = new stdClass();
        $fakerecord->aliasedid = 42;
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $fakerecord->{'prefix'.$field} = "Value of $field";
            }
        }
        $fakerecord->prefixcustom1 = 'Value of custom1';
        $fakerecord->prefiximagealt = null;

        $returned = user_picture::unalias($fakerecord, array('custom1'), 'aliasedid', 'prefix');

        $this->assertEquals($returned->id, 42);
        $this->assertEquals($returned->imagealt, null);
        foreach ($fields as $field) {
            if ($field !== 'id' and $field !== 'imagealt') {
                $this->assertEquals($returned->{$field}, "Value of $field");
            }
        }
        $this->assertEquals($returned->custom1, 'Value of custom1');
    }

    public function test_get_url() {
        global $DB, $CFG;

        $this->resetAfterTest();

        // Force SVG on so that we have predictable URL's.
        $CFG->svgicons = true;

        // verify new install contains expected defaults
        $this->assertEquals('standard', $CFG->theme);
        $this->assertEquals(1, $CFG->slasharguments);
        $this->assertEquals(1, $CFG->themerev);
        $this->assertEquals(0, $CFG->themedesignermode);
        $this->assertEquals('http://www.example.com/moodle', $CFG->wwwroot);
        $this->assertEquals($CFG->wwwroot, $CFG->httpswwwroot);
        $this->assertEquals(0, $CFG->enablegravatar);
        $this->assertEquals('mm', $CFG->gravatardefaulturl);

        // create some users
        $page = new moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(context_system::instance());
        $renderer = $page->get_renderer('core');

        $user1 = $this->getDataGenerator()->create_user(array('picture'=>11, 'email'=>'user1@example.com'));
        $context1 = context_user::instance($user1->id);
        $user2 = $this->getDataGenerator()->create_user(array('picture'=>0, 'email'=>'user2@example.com'));
        $context2 = context_user::instance($user2->id);

        $user3 = $this->getDataGenerator()->create_user(array('picture'=>1, 'deleted'=>1, 'email'=>'user3@example.com'));
        $context3 = context_user::instance($user3->id, IGNORE_MISSING);
        $this->assertEquals($user3->picture, 0);
        $this->assertNotEquals($user3->email, 'user3@example.com');
        $this->assertFalse($context3);

        // try legacy picture == 1
        $user1->picture = 1;
        $up1 = new user_picture($user1);
        $this->assertEquals($CFG->wwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/standard/f2?rev=1', $up1->get_url($page, $renderer)->out(false));
        $user1->picture = 11;

        // try valid user with picture when user context is not cached - 1 query expected
        context_helper::reset_caches();
        $reads = $DB->perf_get_reads();
        $up1 = new user_picture($user1);
        $this->assertEquals($reads, $DB->perf_get_reads());
        $this->assertEquals($CFG->wwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/standard/f2?rev=11', $up1->get_url($page, $renderer)->out(false));
        $this->assertEquals($reads+1, $DB->perf_get_reads());

        // try valid user with contextid hint - no queries expected
        $user1->contextid = $context1->id;
        context_helper::reset_caches();
        $reads = $DB->perf_get_reads();
        $up1 = new user_picture($user1);
        $this->assertEquals($reads, $DB->perf_get_reads());
        $this->assertEquals($CFG->wwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/standard/f2?rev=11', $up1->get_url($page, $renderer)->out(false));
        $this->assertEquals($reads, $DB->perf_get_reads());

        // try valid user without image - no queries expected
        context_helper::reset_caches();
        $reads = $DB->perf_get_reads();
        $up2 = new user_picture($user2);
        $this->assertEquals($reads, $DB->perf_get_reads());
        $this->assertEquals($CFG->wwwroot.'/theme/image.php/standard/core/1/u/f2', $up2->get_url($page, $renderer)->out(false));
        $this->assertEquals($reads, $DB->perf_get_reads());

        // try guessing of deleted users - no queries expected
        unset($user3->deleted);
        context_helper::reset_caches();
        $reads = $DB->perf_get_reads();
        $up3 = new user_picture($user3);
        $this->assertEquals($reads, $DB->perf_get_reads());
        $this->assertEquals($CFG->wwwroot.'/theme/image.php/standard/core/1/u/f2', $up3->get_url($page, $renderer)->out(false));
        $this->assertEquals($reads, $DB->perf_get_reads());

        // try incorrectly deleted users (with valid email and pciture flag) - some DB reads expected
        $user3->email = 'user3@example.com';
        $user3->picture = 1;
        $reads = $DB->perf_get_reads();
        $up3 = new user_picture($user3);
        $this->assertEquals($reads, $DB->perf_get_reads());
        $this->assertEquals($CFG->wwwroot.'/theme/image.php/standard/core/1/u/f2', $up3->get_url($page, $renderer)->out(false));
        $this->assertTrue($reads < $DB->perf_get_reads());


        // test gravatar
        set_config('enablegravatar', 1);

        // deleted user can not have gravatar
        $user3->email = 'deleted';
        $user3->picture = 0;
        $up3 = new user_picture($user3);
        $this->assertEquals($CFG->wwwroot.'/theme/image.php/standard/core/1/u/f2', $up3->get_url($page, $renderer)->out(false));

        // verify defaults to misteryman (mm)
        $up2 = new user_picture($user2);
        $this->assertEquals('http://www.gravatar.com/avatar/ab53a2911ddf9b4817ac01ddcd3d975f?s=35&d=mm', $up2->get_url($page, $renderer)->out(false));

        // without gravatardefaulturl, verify we pick own file
        set_config('gravatardefaulturl', '');
        $up2 = new user_picture($user2);
        $this->assertEquals('http://www.gravatar.com/avatar/ab53a2911ddf9b4817ac01ddcd3d975f?s=35&d=http%3A%2F%2Fwww.example.com%2Fmoodle%2Fpix%2Fu%2Ff2.png', $up2->get_url($page, $renderer)->out(false));
        // uploaded image takes precedence before gravatar
        $up1 = new user_picture($user1);
        $this->assertEquals($CFG->wwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/standard/f2?rev=11', $up1->get_url($page, $renderer)->out(false));

        // https version
        $CFG->httpswwwroot = str_replace('http:', 'https:', $CFG->wwwroot);

        $up1 = new user_picture($user1);
        $this->assertEquals($CFG->httpswwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/standard/f2?rev=11', $up1->get_url($page, $renderer)->out(false));

        $up3 = new user_picture($user3);
        $this->assertEquals($CFG->httpswwwroot.'/theme/image.php/standard/core/1/u/f2', $up3->get_url($page, $renderer)->out(false));

        $up2 = new user_picture($user2);
        $this->assertEquals('https://secure.gravatar.com/avatar/ab53a2911ddf9b4817ac01ddcd3d975f?s=35&d=https%3A%2F%2Fwww.example.com%2Fmoodle%2Fpix%2Fu%2Ff2.png', $up2->get_url($page, $renderer)->out(false));

        // now test gravatar with one theme having own images (afterburner)
        $CFG->httpswwwroot = $CFG->wwwroot;
        $this->assertTrue(file_exists("$CFG->dirroot/theme/afterburner/config.php"));
        set_config('theme', 'afterburner');
        $page = new moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(context_system::instance());
        $renderer = $page->get_renderer('core');

        $up2 = new user_picture($user2);
        $this->assertEquals('http://www.gravatar.com/avatar/ab53a2911ddf9b4817ac01ddcd3d975f?s=35&d=http%3A%2F%2Fwww.example.com%2Fmoodle%2Ftheme%2Fafterburner%2Fpix_core%2Fu%2Ff2.png', $up2->get_url($page, $renderer)->out(false));

        // https version
        $CFG->httpswwwroot = str_replace('http:', 'https:', $CFG->wwwroot);

        $up2 = new user_picture($user2);
        $this->assertEquals('https://secure.gravatar.com/avatar/ab53a2911ddf9b4817ac01ddcd3d975f?s=35&d=https%3A%2F%2Fwww.example.com%2Fmoodle%2Ftheme%2Fafterburner%2Fpix_core%2Fu%2Ff2.png', $up2->get_url($page, $renderer)->out(false));
        // end of gravatar tests

        // test themed images
        set_config('enablegravatar', 0);
        $this->assertTrue(file_exists("$CFG->dirroot/theme/formal_white/config.php")); // use any other theme
        set_config('theme', 'formal_white');
        $CFG->httpswwwroot = $CFG->wwwroot;
        $page = new moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(context_system::instance());
        $renderer = $page->get_renderer('core');

        $up1 = new user_picture($user1);
        $this->assertEquals($CFG->wwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/formal_white/f2?rev=11', $up1->get_url($page, $renderer)->out(false));

        $up2 = new user_picture($user2);
        $this->assertEquals($CFG->wwwroot.'/theme/image.php/formal_white/core/1/u/f2', $up2->get_url($page, $renderer)->out(false));

        // test non-slashargument images
        set_config('theme', 'standard');
        $CFG->httpswwwroot = $CFG->wwwroot;
        $CFG->slasharguments = 0;
        $page = new moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(context_system::instance());
        $renderer = $page->get_renderer('core');

        $up3 = new user_picture($user3);
        $this->assertEquals($CFG->wwwroot.'/theme/image.php?theme=standard&component=core&rev=1&image=u%2Ff2', $up3->get_url($page, $renderer)->out(false));
    }
}


/**
 * Unit tests for the custom_menu class
 */
class custom_menu_testcase extends basic_testcase {

    public function test_empty_menu() {
        $emptymenu = new custom_menu();
        $this->assertTrue($emptymenu instanceof custom_menu);
        $this->assertFalse($emptymenu->has_children());
    }

    public function test_basic_syntax() {
        $definition = <<<EOF
Moodle community|http://moodle.org
-Moodle free support|http://moodle.org/support
-Moodle development|http://moodle.org/development
--Moodle Tracker|http://tracker.moodle.org
--Moodle Docs|http://docs.moodle.org
-Moodle News|http://moodle.org/news
Moodle company
-Hosting|http://moodle.com/hosting|Commercial hosting
-Support|http://moodle.com/support|Commercial support
EOF;

        $menu = new custom_menu($definition);
        $this->assertTrue($menu instanceof custom_menu);
        $this->assertTrue($menu->has_children());
        $firstlevel = $menu->get_children();
        $this->assertTrue(is_array($firstlevel));
        $this->assertEquals(2, count($firstlevel));

        $item = array_shift($firstlevel);
        $this->assertTrue($item instanceof custom_menu_item);
        $this->assertTrue($item->has_children());
        $this->assertEquals(3, count($item->get_children()));
        $this->assertEquals('Moodle community', $item->get_text());
        $itemurl = $item->get_url();
        $this->assertTrue($itemurl instanceof moodle_url);
        $this->assertEquals('http://moodle.org', $itemurl->out());
        $this->assertEquals($item->get_text(), $item->get_title()); // implicit title

        $item = array_shift($firstlevel);
        $this->assertTrue($item->has_children());
        $this->assertEquals(2, count($item->get_children()));
        $this->assertEquals('Moodle company', $item->get_text());
        $this->assertTrue(is_null($item->get_url()));

        $children = $item->get_children();
        $subitem = array_shift($children);
        $this->assertFalse($subitem->has_children());
        $this->assertEquals('Hosting', $subitem->get_text());
        $this->assertEquals('Commercial hosting', $subitem->get_title());
    }

    public function test_multilang_support() {
        $definition = <<<EOF
Start|http://school.info
Info
-English|http://school.info/en|Information in English|en
-Deutsch|http://school.info/de|Informationen in deutscher Sprache|de,de_du,de_kids
EOF;

        // the menu without multilang support
        $menu = new custom_menu($definition);
        $this->assertTrue($menu->has_children());
        $this->assertEquals(2, count($menu->get_children()));

        $children = $menu->get_children();
        $infomenu = array_pop($children);
        $this->assertTrue($infomenu->has_children());
        $children = $infomenu->get_children();
        $this->assertEquals(2, count($children));

        $children = $infomenu->get_children();
        $langspecinfo = array_shift($children);
        $this->assertEquals('Information in English', $langspecinfo->get_title());

        // same menu for English language selected
        $menu = new custom_menu($definition, 'en');
        $this->assertTrue($menu->has_children());
        $this->assertEquals(2, count($menu->get_children()));

        $children = $menu->get_children();
        $infomenu = array_pop($children);
        $this->assertTrue($infomenu->has_children());
        $this->assertEquals(1, count($infomenu->get_children()));

        $children = $infomenu->get_children();
        $langspecinfo = array_shift($children);
        $this->assertEquals('Information in English', $langspecinfo->get_title());

        // same menu for German (de_du) language selected
        $menu = new custom_menu($definition, 'de_du');
        $this->assertTrue($menu->has_children());
        $this->assertEquals(2, count($menu->get_children()));

        $children = $menu->get_children();
        $infomenu = array_pop($children);
        $this->assertTrue($infomenu->has_children());
        $this->assertEquals(1, count($infomenu->get_children()));

        $children = $infomenu->get_children();
        $langspecinfo = array_shift($children);
        $this->assertEquals('Informationen in deutscher Sprache', $langspecinfo->get_title());

        // same menu for Czech language selected
        $menu = new custom_menu($definition, 'cs');
        $this->assertTrue($menu->has_children());
        $this->assertEquals(2, count($menu->get_children()));

        $children = $infomenu->get_children();
        $infomenu = array_pop( $children);
        $this->assertFalse($infomenu->has_children());
    }
}
