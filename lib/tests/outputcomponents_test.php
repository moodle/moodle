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

namespace core;

use block_contents;
use custom_menu;
use custom_menu_item;
use paging_bar;
use renderer_base;
use single_button;
use single_select;
use theme_config;
use url_select;
use core\output\user_picture;

/**
 * Unit tests for lib/outputcomponents.php.
 *
 * @package   core
 * @category  test
 * @copyright 2011 David Mudrak <david@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class outputcomponents_test extends \advanced_testcase {
    /**
     * Tests user_picture::fields.
     *
     * @deprecated since Moodle 3.11 MDL-45242
     */
    public function test_fields_aliasing(): void {
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
        $this->assertEquals(count($returned), count($fields) + 1); // Only one extra field added.

        foreach ($fields as $field) {
            if ($field === 'id') {
                $expected = "id AS aliasedid";
            } else {
                $expected = "$field AS prefix$field";
            }
            $this->assertContains($expected, $returned, "Expected pattern '$expected' not returned");
        }
        $this->assertContains("custom1 AS prefixcustom1", $returned, "Expected pattern 'custom1 AS prefixcustom1' not returned");

        // Deprecation warnings for user_picture::fields.
        $this->assertDebuggingCalledCount(2);
    }

    /**
     * Tests user_picture::unalias.
     */
    public function test_fields_unaliasing(): void {
        $fields = implode(',', \core_user\fields::get_picture_fields());
        $fields = array_map('trim', explode(',', $fields));

        $fakerecord = new \stdClass();
        $fakerecord->aliasedid = 42;
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $fakerecord->{'prefix'.$field} = "Value of $field";
            }
        }
        $fakerecord->prefixcustom1 = 'Value of custom1';

        $returned = user_picture::unalias($fakerecord, array('custom1'), 'aliasedid', 'prefix');

        $this->assertEquals(42, $returned->id);
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $this->assertSame("Value of $field", $returned->{$field});
            }
        }
        $this->assertSame('Value of custom1', $returned->custom1);
    }

    /**
     * Tests user_picture::unalias with null values.
     */
    public function test_fields_unaliasing_null(): void {
        $fields = implode(',', \core_user\fields::get_picture_fields());
        $fields = array_map('trim', explode(',', $fields));

        $fakerecord = new \stdClass();
        $fakerecord->aliasedid = 42;
        foreach ($fields as $field) {
            if ($field !== 'id') {
                $fakerecord->{'prefix'.$field} = "Value of $field";
            }
        }
        $fakerecord->prefixcustom1 = 'Value of custom1';
        $fakerecord->prefiximagealt = null;

        $returned = user_picture::unalias($fakerecord, array('custom1'), 'aliasedid', 'prefix');

        $this->assertEquals(42, $returned->id);
        $this->assertNull($returned->imagealt);
        foreach ($fields as $field) {
            if ($field !== 'id' and $field !== 'imagealt') {
                $this->assertSame("Value of $field", $returned->{$field});
            }
        }
        $this->assertSame('Value of custom1', $returned->custom1);
    }

    public function test_get_url(): void {
        global $DB, $CFG, $USER;

        $this->resetAfterTest();

        // Verify new install contains expected defaults.
        $this->assertSame(theme_config::DEFAULT_THEME, $CFG->theme);
        $this->assertEquals(1, $CFG->slasharguments);
        $this->assertEquals(1, $CFG->themerev);
        $this->assertEquals(0, $CFG->themedesignermode);
        $this->assertSame('https://www.example.com/moodle', $CFG->wwwroot);
        $this->assertEquals(0, $CFG->enablegravatar);
        $this->assertSame('mm', $CFG->gravatardefaulturl);

        // Create some users.
        $page = new \moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(\context_system::instance());
        $renderer = $page->get_renderer('core');

        $user1 = $this->getDataGenerator()->create_user(array('picture'=>11, 'email'=>'user1@example.com'));
        $context1 = \context_user::instance($user1->id);
        $user2 = $this->getDataGenerator()->create_user(array('picture'=>0, 'email'=>'user2@example.com'));
        $context2 = \context_user::instance($user2->id);

        // User 3 is deleted.
        $user3 = $this->getDataGenerator()->create_user(array('picture'=>1, 'deleted'=>1, 'email'=>'user3@example.com'));
        $this->assertNotEmpty(\context_user::instance($user3->id));
        $this->assertEquals(0, $user3->picture);
        $this->assertNotEquals('user3@example.com', $user3->email);

        // User 4 is incorrectly deleted with its context deleted as well (testing legacy code).
        $user4 = $this->getDataGenerator()->create_user(['picture' => 1, 'deleted' => 1, 'email' => 'user4@example.com']);
        \context_helper::delete_instance(CONTEXT_USER, $user4->id);
        $this->assertEquals(0, $user4->picture);
        $this->assertNotEquals('user4@example.com', $user4->email);

        // Try legacy picture == 1.
        $user1->picture = 1;
        $up1 = new user_picture($user1);
        $this->assertSame($CFG->wwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/boost/f2?rev=1', $up1->get_url($page, $renderer)->out(false));
        $user1->picture = 11;

        // Try valid user with picture when user context is not cached - 1 query expected.
        \context_helper::reset_caches();
        $reads = $DB->perf_get_reads();
        $up1 = new user_picture($user1);
        $this->assertEquals($reads, $DB->perf_get_reads());
        $this->assertSame($CFG->wwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/boost/f2?rev=11', $up1->get_url($page, $renderer)->out(false));
        $this->assertEquals($reads+1, $DB->perf_get_reads());

        // Try valid user with contextid hint - no queries expected.
        $user1->contextid = $context1->id;
        \context_helper::reset_caches();
        $reads = $DB->perf_get_reads();
        $up1 = new user_picture($user1);
        $this->assertEquals($reads, $DB->perf_get_reads());
        $this->assertSame($CFG->wwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/boost/f2?rev=11', $up1->get_url($page, $renderer)->out(false));
        $this->assertEquals($reads, $DB->perf_get_reads());

        // Try valid user without image - no queries expected.
        \context_helper::reset_caches();
        $reads = $DB->perf_get_reads();
        $up2 = new user_picture($user2);
        $this->assertEquals($reads, $DB->perf_get_reads());
        $this->assertSame($CFG->wwwroot.'/theme/image.php/boost/core/1/u/f2', $up2->get_url($page, $renderer)->out(false));
        $this->assertEquals($reads, $DB->perf_get_reads());

        // Try guessing of deleted users - no queries expected.
        unset($user3->deleted);
        \context_helper::reset_caches();
        $reads = $DB->perf_get_reads();
        $up3 = new user_picture($user3);
        $this->assertEquals($reads, $DB->perf_get_reads());
        $this->assertSame($CFG->wwwroot.'/theme/image.php/boost/core/1/u/f2', $up3->get_url($page, $renderer)->out(false));
        $this->assertEquals($reads, $DB->perf_get_reads());

        // Try incorrectly deleted users (with valid email and picture flag, but user context removed) - some DB reads expected.
        unset($user4->deleted);
        $user4->email = 'user4@example.com';
        $user4->picture = 1;
        $reads = $DB->perf_get_reads();
        $up4 = new user_picture($user4);
        $this->assertEquals($reads, $DB->perf_get_reads());
        $this->assertSame($CFG->wwwroot.'/theme/image.php/boost/core/1/u/f2', $up4->get_url($page, $renderer)->out(false));
        $this->assertGreaterThan($reads, $DB->perf_get_reads());

        // Test gravatar.
        set_config('enablegravatar', 1);

        // Deleted user can not have gravatar.
        $user3->email = 'deleted';
        $user3->picture = 0;
        $up3 = new user_picture($user3);
        $this->assertSame($CFG->wwwroot.'/theme/image.php/boost/core/1/u/f2', $up3->get_url($page, $renderer)->out(false));
        $user4->email = 'deleted';
        $user4->picture = 0;
        $up4 = new user_picture($user4);
        $this->assertSame($CFG->wwwroot.'/theme/image.php/boost/core/1/u/f2', $up4->get_url($page, $renderer)->out(false));

        // Http version.
        $CFG->wwwroot = str_replace('https:', 'http:', $CFG->wwwroot);

        // Verify defaults to misteryman (mm).
        $up2 = new user_picture($user2);
        $this->assertSame('http://www.gravatar.com/avatar/ab53a2911ddf9b4817ac01ddcd3d975f?s=35&d=mm', $up2->get_url($page, $renderer)->out(false));

        // Without gravatardefaulturl, verify we pick own file.
        set_config('gravatardefaulturl', '');
        $up2 = new user_picture($user2);
        $this->assertSame('http://www.gravatar.com/avatar/ab53a2911ddf9b4817ac01ddcd3d975f?s=35&d=http%3A%2F%2Fwww.example.com%2Fmoodle%2Fpix%2Fu%2Ff2.png', $up2->get_url($page, $renderer)->out(false));
        // Uploaded image takes precedence before gravatar.
        $up1 = new user_picture($user1);
        $this->assertSame($CFG->wwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/boost/f2?rev=11', $up1->get_url($page, $renderer)->out(false));

        // Uploaded image with token-based access for current user.
        $up1 = new user_picture($user1);
        $up1->includetoken = true;
        $token = get_user_key('core_files', $USER->id);
        $this->assertSame($CFG->wwwroot.'/tokenpluginfile.php/'.$token.'/'.$context1->id.'/user/icon/boost/f2?rev=11', $up1->get_url($page, $renderer)->out(false));

        // Uploaded image with token-based access for other user.
        $up1 = new user_picture($user1);
        $up1->includetoken = $user2->id;
        $token = get_user_key('core_files', $user2->id);
        $this->assertSame($CFG->wwwroot.'/tokenpluginfile.php/'.$token.'/'.$context1->id.'/user/icon/boost/f2?rev=11', $up1->get_url($page, $renderer)->out(false));

        // Https version.
        $CFG->wwwroot = str_replace('http:', 'https:', $CFG->wwwroot);

        $up1 = new user_picture($user1);
        $this->assertSame($CFG->wwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/boost/f2?rev=11', $up1->get_url($page, $renderer)->out(false));

        $up2 = new user_picture($user2);
        $this->assertSame('https://secure.gravatar.com/avatar/ab53a2911ddf9b4817ac01ddcd3d975f?s=35&d=https%3A%2F%2Fwww.example.com%2Fmoodle%2Fpix%2Fu%2Ff2.png', $up2->get_url($page, $renderer)->out(false));

        $up3 = new user_picture($user3);
        $this->assertSame($CFG->wwwroot.'/theme/image.php/boost/core/1/u/f2', $up3->get_url($page, $renderer)->out(false));

        $up4 = new user_picture($user4);
        $this->assertSame($CFG->wwwroot.'/theme/image.php/boost/core/1/u/f2', $up4->get_url($page, $renderer)->out(false));

        // TODO MDL-44792 Rewrite those tests to use a fixture.
        // Now test gravatar with one theme having own images (afterburner).
        // $this->assertFileExists("$CFG->dirroot/theme/afterburner/config.php");
        // set_config('theme', 'afterburner');
        // $page = new \moodle_page();
        // $page->set_url('/user/profile.php');
        // $page->set_context(\context_system::instance());
        // $renderer = $page->get_renderer('core');

        // $up2 = new user_picture($user2);
        // $this->assertEquals('http://www.gravatar.com/avatar/ab53a2911ddf9b4817ac01ddcd3d975f?s=35&d=http%3A%2F%2Fwww.example.com%2Fmoodle%2Ftheme%2Fafterburner%2Fpix_core%2Fu%2Ff2.png', $up2->get_url($page, $renderer)->out(false));

        // $up2 = new user_picture($user2);
        // $this->assertSame('https://secure.gravatar.com/avatar/ab53a2911ddf9b4817ac01ddcd3d975f?s=35&d=https%3A%2F%2Fwww.example.com%2Fmoodle%2Ftheme%2Fafterburner%2Fpix_core%2Fu%2Ff2.png', $up2->get_url($page, $renderer)->out(false));
        // End of gravatar tests.

        // Test themed images.
        // set_config('enablegravatar', 0);
        // $this->assertFileExists("$CFG->dirroot/theme/formal_white/config.php"); // Use any other theme.
        // set_config('theme', 'formal_white');
        // $page = new \moodle_page();
        // $page->set_url('/user/profile.php');
        // $page->set_context(\context_system::instance());
        // $renderer = $page->get_renderer('core');

        // $up1 = new user_picture($user1);
        // $this->assertSame($CFG->wwwroot.'/pluginfile.php/'.$context1->id.'/user/icon/formal_white/f2?rev=11', $up1->get_url($page, $renderer)->out(false));

        // $up2 = new user_picture($user2);
        // $this->assertSame($CFG->wwwroot.'/theme/image.php/formal_white/core/1/u/f2', $up2->get_url($page, $renderer)->out(false));

        // Test non-slashargument images.
        set_config('theme', 'classic');
        $CFG->wwwroot = str_replace('https:', 'http:', $CFG->wwwroot);
        $CFG->slasharguments = 0;
        $page = new \moodle_page();
        $page->set_url('/user/profile.php');
        $page->set_context(\context_system::instance());
        $renderer = $page->get_renderer('core');

        $up3 = new user_picture($user3);
        $this->assertSame($CFG->wwwroot.'/theme/image.php?theme=classic&component=core&rev=1&image=u%2Ff2', $up3->get_url($page, $renderer)->out(false));
    }

    public function test_empty_menu(): void {
        $emptymenu = new custom_menu();
        $this->assertInstanceOf(custom_menu::class, $emptymenu);
        $this->assertFalse($emptymenu->has_children());
    }

    public function test_basic_syntax(): void {
        $definition = <<<EOF
Moodle community|http://moodle.org
-Moodle free support|http://moodle.org/support
-Moodle development|http://moodle.org/development
--Moodle Tracker|http://tracker.moodle.org
--Moodle Docs|http://docs.moodle.org
-Moodle News|http://moodle.org/news
Moodle company||Moodle trust pty
-Hosting|http://moodle.com/hosting|Commercial hosting
-Support|http://moodle.com/support|Commercial support
EOF;

        $menu = new custom_menu($definition);
        $this->assertInstanceOf('custom_menu', $menu);
        $this->assertTrue($menu->has_children());
        $firstlevel = $menu->get_children();
        $this->assertTrue(is_array($firstlevel));
        $this->assertCount(2, $firstlevel);

        $item = array_shift($firstlevel);
        $this->assertInstanceOf('custom_menu_item', $item);
        $this->assertTrue($item->has_children());
        $this->assertCount(3, $item->get_children());
        $this->assertEquals('Moodle community', $item->get_text());
        $itemurl = $item->get_url();
        $this->assertTrue($itemurl instanceof \moodle_url);
        $this->assertEquals('http://moodle.org', $itemurl->out());
        $this->assertNull($item->get_title()); // Implicit title.

        /** @var custom_menu_item $item */
        $item = array_shift($firstlevel);
        $this->assertTrue($item->has_children());
        $this->assertCount(2, $item->get_children());
        $this->assertSame('Moodle company', $item->get_text());
        $this->assertNull($item->get_url());
        $this->assertSame('Moodle trust pty', $item->get_title());

        $children = $item->get_children();
        $subitem = array_shift($children);
        $this->assertFalse($subitem->has_children());
        $this->assertSame('Hosting', $subitem->get_text());
        $this->assertSame('Commercial hosting', $subitem->get_title());
    }

    public function test_custommenu_mulitlang(): void {
        $definition = <<<EOF
Start|http://school.info
Info
-English|http://school.info/en|Information in English|en
--Nested under English
--I will be lost|||de
-Deutsch|http://school.info/de|Informationen in deutscher Sprache|de,de_du,de_kids
--Nested under Deutsch
--I will be lost|||en
kontaktieren Sie uns|contactus.php||de
Contact us|contactus.php||en
EOF;
        $definitionen = <<<EOF
Start|http://school.info
Info
-English|http://school.info/en|Information in English|en
--Nested under English
Contact us|contactus.php||en
EOF;
        $definitionde = <<<EOF
Start|http://school.info
Info
-Deutsch|http://school.info/de|Informationen in deutscher Sprache|de,de_du,de_kids
--Nested under Deutsch
kontaktieren Sie uns|contactus.php||de
EOF;

        $definitiondedu = <<<EOF
Start|http://school.info
Info
-Deutsch|http://school.info/de|Informationen in deutscher Sprache|de,de_du,de_kids
--Nested under Deutsch
EOF;

        $parsed = $this->custommenu_out(new custom_menu($definition));
        $parseden = $this->custommenu_out(new custom_menu($definition, 'en'));
        $parsedde = $this->custommenu_out(new custom_menu($definition, 'de'));
        $parseddedu = $this->custommenu_out(new custom_menu($definition, 'de_du'));

        $actualen = $this->custommenu_out(new custom_menu($definitionen, 'en'));
        $actualde = $this->custommenu_out(new custom_menu($definitionde, 'de'));
        $actualdedu = $this->custommenu_out(new custom_menu($definitiondedu, 'de_du'));

        $this->assertSame($actualen, $parseden, 'The parsed English menu does not match the expected English menu');
        $this->assertSame($actualde, $parsedde, 'The parsed German menu does not match the expected German menu');
        $this->assertSame($actualdedu, $parseddedu, 'The parsed German [Du] menu does not match the expected German [Du] menu');

        $this->assertNotSame($parsed, $parsedde, 'The menu without language is the same as the German menu. They should differ!');
        $this->assertNotSame($parsed, $parseden, 'The menu without language is the same as the English menu. They should differ!');
        $this->assertNotSame($parsed, $parseddedu, 'The menu without language is the same as the German [Du] menu. They should differ!');
        $this->assertNotSame($parseden, $parsedde, 'The English menu is the same as the German menu. They should differ!');
        $this->assertNotSame($parseden, $parseddedu, 'The English menu is the same as the German [Du] menu. They should differ!');
        $this->assertNotSame($parseddedu, $parsedde, 'The German [Du] menu is the same as the German menu. They should differ!');
    }

    /**
     * Support function that takes a custom_menu_item and converts it to a string.
     *
     * @param custom_menu_item $item
     * @param int $depth
     * @return string
     */
    protected function custommenu_out(custom_menu_item $item, $depth = 0) {
        $str = str_repeat('-', $depth);
        $str .= $item->get_text();
        $str .= '|' . $item->get_url();
        $str .= '|' . $item->get_title();
        if ($item->has_children()) {
            $str .= '|' . count($item->get_children());
            foreach ($item->get_children() as $child) {
                $str .= "\n" . $this->custommenu_out($child, $depth + 1);
            }
        }
        return $str;
    }

    public function test_prepare(): void {
        $expecteda = array('<span class="current-page">1</span>',
            '<a href="index.php?page=1">2</a>',
            '<a href="index.php?page=2">3</a>',
            '<a href="index.php?page=3">4</a>',
            '<a href="index.php?page=4">5</a>',
            '<a href="index.php?page=5">6</a>',
            '<a href="index.php?page=6">7</a>',
            '<a href="index.php?page=7">8</a>',
        );
        $expectedb = array('<a href="page?page=3">4</a>',
            '<a href="page?page=4">5</a>',
            '<span class="current-page">6</span>',
            '<a href="page?page=6">7</a>',
            '<a href="page?page=7">8</a>',
        );

        $mpage = new \moodle_page();
        $rbase = new renderer_base($mpage, "/");
        $pbara = new paging_bar(40, 0, 5, 'index.php');
        $pbara->prepare($rbase, $mpage, "/");
        $pbarb = new paging_bar(100, 5, 5, 'page');
        $pbarb->maxdisplay = 5;
        $pbarb->prepare($rbase, $mpage, "/");

        $this->assertEquals($expecteda, $pbara->pagelinks);
        $this->assertEquals($expectedb, $pbarb->pagelinks);
    }

    public function test_pix_icon(): void {
        $this->resetAfterTest();

        $page = new \moodle_page();

        set_config('theme', 'boost');
        // Need to reset after changing theme.
        $page->reset_theme_and_output();
        $renderer = $page->get_renderer('core');

        $reason = 'An icon with no alt text is hidden from screenreaders.';
        $this->assertStringContainsString('aria-hidden="true"', $renderer->pix_icon('t/print', ''), $reason);

        $reason = 'An icon with alt text is not hidden from screenreaders.';
        $this->assertStringNotContainsString('aria-hidden="true"', $renderer->pix_icon('t/print', 'Print'), $reason);

        // Test another theme with a different icon system.
        set_config('theme', 'classic');
        // Need to reset after changing theme.
        $page->reset_theme_and_output();
        $renderer = $page->get_renderer('core');

        $reason = 'An icon with no alt text is hidden from screenreaders.';
        $this->assertStringContainsString('aria-hidden="true"', $renderer->pix_icon('t/print', ''), $reason);

        $reason = 'An icon with alt text is not hidden from screenreaders.';
        $this->assertStringNotContainsString('aria-hidden="true"', $renderer->pix_icon('t/print', 'Print'), $reason);
    }

    /**
     * Test for checking the template context data for the single_select element.
     */
    public function test_single_select(): void {
        global $PAGE;

        $fakename = 'fakename';
        $fakeclass = 'fakeclass';
        $faketitle = 'faketitle';
        $fakedisabled = true;
        $fakefor = 'fakefor';

        $someid = 'someid';
        $realname = 'realname';
        $realclass = 'realclass';
        $realtitle = 'realtitle';
        $realdisabled = false;
        $reallabel = 'Some cool label';
        $labelclass = 'somelabelclass';
        $labelstyle = 'font-weight: bold';

        $dataaction = 'actiondata';
        $dataother = 'otherdata';

        $attributes = [
            'id' => $someid,
            'class' => $fakeclass,
            'title' => $faketitle,
            'disabled' => $fakedisabled,
            'name' => $fakename,
            'data-action' => $dataaction,
            'data-other' => $dataother,
        ];
        $labelattributes = [
            'for' => $fakefor,
            'class' => $labelclass,
            'style' => $labelstyle
        ];

        $options = [ "Option A", "Option B", "Option C" ];
        $nothing = ['' => 'choosedots'];

        $url = new \moodle_url('/');

        $singleselect = new single_select($url, $realname, $options, null, $nothing, 'someformid');
        $singleselect->class = $realclass;
        $singleselect->tooltip = $realtitle;
        $singleselect->disabled = $realdisabled;
        $singleselect->attributes = $attributes;
        $singleselect->label = $reallabel;
        $singleselect->labelattributes = $labelattributes;

        $renderer = $PAGE->get_renderer('core');
        $data = $singleselect->export_for_template($renderer);

        $this->assertEquals($realtitle, $data->title);
        $this->assertEquals($singleselect->class, $data->classes);
        $this->assertEquals($realname, $data->name);
        $this->assertEquals($reallabel, $data->label);
        $this->assertEquals($realdisabled, $data->disabled);
        $this->assertEquals($someid, $data->id);

        // Validate attributes array.
        // The following should not be included: id, class, name, disabled.
        $this->assertFalse(in_array(['name' => 'id', 'value' => $someid], $data->attributes));
        $this->assertFalse(in_array(['name' => 'class', 'value' => $fakeclass], $data->attributes));
        $this->assertFalse(in_array(['name' => 'name', 'value' => $fakeclass], $data->attributes));
        $this->assertFalse(in_array(['name' => 'disabled', 'value' => $fakedisabled], $data->attributes));
        // The rest should be fine.
        $this->assertTrue(in_array(['name' => 'data-action', 'value' => $dataaction], $data->attributes));
        $this->assertTrue(in_array(['name' => 'data-other', 'value' => $dataother], $data->attributes));

        // Validate label attributes.
        // The for attribute should not be included.
        $this->assertFalse(in_array(['name' => 'for', 'value' => $someid], $data->labelattributes));
        // The rest should be fine.
        $this->assertTrue(in_array(['name' => 'class', 'value' => $labelclass], $data->labelattributes));
        $this->assertTrue(in_array(['name' => 'style', 'value' => $labelstyle], $data->labelattributes));
    }
    /**
     * Test for checking the template context data for the single_select element.
     * @covers \single_button
     */
    public function test_single_button(): void {
        global $PAGE;
        $url = new \moodle_url('/');
        $realname = 'realname';
        $attributes = [
            'data-dummy' => 'dummy',
        ];
        $singlebutton = new single_button($url, $realname, 'post', single_button::BUTTON_SECONDARY, $attributes);
        $renderer = $PAGE->get_renderer('core');
        $data = $singlebutton->export_for_template($renderer);

        $this->assertEquals($realname, $data->label);
        $this->assertEquals('post', $data->method);
        $this->assertEquals('singlebutton', $data->classes);
        $this->assertEquals('secondary', $data->type);
        $this->assertEquals($attributes['data-dummy'], $data->attributes[0]['value']);

        $singlebutton = new single_button($url, $realname, 'post', single_button::BUTTON_PRIMARY, $attributes);
        $renderer = $PAGE->get_renderer('core');
        $data = $singlebutton->export_for_template($renderer);

        $this->assertEquals($realname, $data->label);
        $this->assertEquals('post', $data->method);
        $this->assertEquals('singlebutton', $data->classes);
        $this->assertEquals('primary', $data->type);
        $this->assertEquals($attributes['data-dummy'], $data->attributes[0]['value']);
    }

    /**
     * Test for checking the template context data for the single_select element legacy API.
     * @covers \single_button
     */
    public function test_single_button_deprecated(): void {
        global $PAGE;
        $url = new \moodle_url('/');
        $realname = 'realname';
        $attributes = [
            'data-dummy' => 'dummy',
        ];

        // Test that when we use a true boolean value for the 4th parameter this is set as primary type.
        $singlebutton = new single_button($url, $realname, 'post', single_button::BUTTON_PRIMARY, $attributes);
        $renderer = $PAGE->get_renderer('core');
        $data = $singlebutton->export_for_template($renderer);
        $this->assertEquals($realname, $data->label);
        $this->assertEquals('post', $data->method);
        $this->assertEquals('singlebutton', $data->classes);
        $this->assertEquals('primary', $data->type);
        $this->assertEquals($attributes['data-dummy'], $data->attributes[0]['value']);

        // Test that when we use a false boolean value for the 4th parameter this is set as secondary type.
        $singlebutton = new single_button($url, $realname, 'post', false, $attributes);
        $this->assertDebuggingCalled();
        $renderer = $PAGE->get_renderer('core');
        $data = $singlebutton->export_for_template($renderer);
        $this->assertEquals($realname, $data->label);
        $this->assertEquals('post', $data->method);
        $this->assertEquals('singlebutton', $data->classes);
        $this->assertEquals('secondary', $data->type);
        $this->assertEquals($attributes['data-dummy'], $data->attributes[0]['value']);

        // Test that when we set the primary value, then this is reflected in the type.
        $singlebutton->primary = false;
        $this->assertDebuggingCalled();
        $this->assertEquals(single_button::BUTTON_SECONDARY, $singlebutton->type);
        $singlebutton->primary = true;
        $this->assertDebuggingCalled();
        $this->assertEquals(single_button::BUTTON_PRIMARY, $singlebutton->type);
        // Then set the type directly.

        $singlebutton->type = single_button::BUTTON_DANGER;
        $data = $singlebutton->export_for_template($renderer);
        $this->assertEquals('danger', $data->type);

    }

    /**
     * Test for checking the template context data for the url_select element.
     */
    public function test_url_select(): void {
        global $PAGE;

        $fakename = 'fakename';
        $fakeclass = 'fakeclass';
        $faketitle = 'faketitle';
        $fakedisabled = true;
        $fakefor = 'fakefor';

        $someid = 'someid';
        $realclass = 'realclass';
        $realtitle = 'realtitle';
        $realdisabled = false;
        $reallabel = 'Some cool label';
        $labelclass = 'somelabelclass';
        $labelstyle = 'font-weight: bold';

        $dataaction = 'actiondata';
        $dataother = 'otherdata';

        $attributes = [
            'id' => $someid,
            'class' => $fakeclass,
            'title' => $faketitle,
            'disabled' => $fakedisabled,
            'name' => $fakename,
            'data-action' => $dataaction,
            'data-other' => $dataother,
        ];
        $labelattributes = [
            'for' => $fakefor,
            'class' => $labelclass,
            'style' => $labelstyle
        ];

        $url1 = new \moodle_url("/#a");
        $url2 = new \moodle_url("/#b");
        $url3 = new \moodle_url("/#c");

        $urls = [
            $url1->out() => 'A',
            $url2->out() => 'B',
            $url3->out() => 'C',
        ];
        $nothing = ['' => 'choosedots'];

        $urlselect = new url_select($urls, null, $nothing, 'someformid');
        $urlselect->class = $realclass;
        $urlselect->tooltip = $realtitle;
        $urlselect->disabled = $realdisabled;
        $urlselect->attributes = $attributes;
        $urlselect->label = $reallabel;
        $urlselect->labelattributes = $labelattributes;

        $renderer = $PAGE->get_renderer('core');
        $data = $urlselect->export_for_template($renderer);

        $this->assertEquals($realtitle, $data->title);
        $this->assertEquals($urlselect->class, $data->classes);
        $this->assertEquals($reallabel, $data->label);
        $this->assertEquals($realdisabled, $data->disabled);
        $this->assertEquals($someid, $data->id);

        // Validate attributes array.
        // The following should not be included: id, class, name, disabled.
        $this->assertFalse(in_array(['name' => 'id', 'value' => $someid], $data->attributes));
        $this->assertFalse(in_array(['name' => 'class', 'value' => $fakeclass], $data->attributes));
        $this->assertFalse(in_array(['name' => 'name', 'value' => $fakeclass], $data->attributes));
        $this->assertFalse(in_array(['name' => 'disabled', 'value' => $fakedisabled], $data->attributes));
        // The rest should be fine.
        $this->assertTrue(in_array(['name' => 'data-action', 'value' => $dataaction], $data->attributes));
        $this->assertTrue(in_array(['name' => 'data-other', 'value' => $dataother], $data->attributes));

        // Validate label attributes.
        // The for attribute should not be included.
        $this->assertFalse(in_array(['name' => 'for', 'value' => $someid], $data->labelattributes));
        // The rest should be fine.
        $this->assertTrue(in_array(['name' => 'class', 'value' => $labelclass], $data->labelattributes));
        $this->assertTrue(in_array(['name' => 'style', 'value' => $labelstyle], $data->labelattributes));
    }

    /**
     * Test for checking the template context data for the url_select element.
     * @covers \url_select::disable_option
     * @covers \url_select::enable_option
     */
    public function test_url_select_disabled_options(): void {
        global $PAGE;
        $url1 = new \moodle_url("/#a");
        $url2 = new \moodle_url("/#b");
        $url3 = new \moodle_url("/#c");

        $urls = [
            $url1->out() => 'A',
            $url2->out() => 'B',
            $url3->out() => 'C',
        ];
        $urlselect = new url_select($urls,
            null,
            null,
            'someformid',
            null);
        $renderer = $PAGE->get_renderer('core');
        $urlselect->set_option_disabled($url2->out(), true);
        $data = $urlselect->export_for_template($renderer);
        $this->assertFalse($data->options[0]['disabled']);
        $this->assertTrue($data->options[1]['disabled']);
        $urlselect->set_option_disabled($url2->out(), false);
        $data = $urlselect->export_for_template($renderer);
        $this->assertFalse($data->options[0]['disabled']);
        $this->assertFalse($data->options[1]['disabled']);
    }

    /**
     * Data provider for test_block_contents_is_fake().
     *
     * @return array
     */
    public static function block_contents_is_fake_provider(): array {
        return [
            'Null' => [null, false],
            'Not set' => [false, false],
            'Fake' => ['_fake', true],
            'Real block' => ['activity_modules', false],
        ];
    }

    /**
     * Test block_contents is_fake() method.
     *
     * @dataProvider block_contents_is_fake_provider
     * @param mixed $value Value for the data-block attribute
     * @param boolean $expected The expected result
     */
    public function test_block_contents_is_fake($value, $expected): void {
        $bc = new block_contents(array());
        if ($value !== false) {
            $bc->attributes['data-block'] = $value;
        }
        $this->assertEquals($expected, $bc->is_fake());
    }
}
