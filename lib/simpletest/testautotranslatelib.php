<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Tests for autotranslatelib.php.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package autotranslate
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/autotranslatelib.php');

class test_null_auto_translator extends UnitTestCase {
    public function test_translate_content() {
        $translator = new null_auto_translator;
        $some_content = 'some content';
        $this->assertEqual($translator->translate_content($some_content), $some_content);
    }
}

class testable_word_by_word_translator extends word_by_word_translator {
    public function split_text_and_tags($content) {
        return parent::split_text_and_tags($content);
    }
    public function translate_text($text) {
        return parent::translate_text($text);
    }
    public function join_content($content) {
        return parent::join_content($content);
    }
    public function translate_word($word) {
        return 'word';
    }
}

class test_word_by_word_translator extends UnitTestCase {
    private $wwt;

    public function setUp() {
        $this->wwt = new testable_word_by_word_translator();
    }

    public function test_split_text_and_tags_simple() {
        $parsedcontent = $this->wwt->split_text_and_tags('Some text.');
        $expected = array(
            (object) array('content' => 'Some text.', 'type' => 'text'),
        );
        $this->assertEqual($expected, $parsedcontent);
    }

    public function test_split_text_and_tags_entity_uc() {
        $parsedcontent = $this->wwt->split_text_and_tags('Hi&#XAa0;world!');
        $expected = array(
            (object) array('content' => 'Hi', 'type' => 'text'),
            (object) array('content' => '&#XAa0;', 'type' => 'tag'),
            (object) array('content' => 'world!', 'type' => 'text'),
        );
        $this->assertEqual($expected, $parsedcontent);
    }

    public function test_split_text_and_tags_complex_html() {
        $parsedcontent = $this->wwt->split_text_and_tags('<div class="frog">This &amp; <b>that</b></span>&#xa0;');
        $expected = array(
            (object) array('content' => '', 'type' => 'text'),
            (object) array('content' => '<div class="frog">', 'type' => 'tag'),
            (object) array('content' => 'This ', 'type' => 'text'),
            (object) array('content' => '&amp;', 'type' => 'tag'),
            (object) array('content' => ' ', 'type' => 'text'),
            (object) array('content' => '<b>', 'type' => 'tag'),
            (object) array('content' => 'that', 'type' => 'text'),
            (object) array('content' => '</b></span>&#xa0;', 'type' => 'tag'),
            (object) array('content' => '', 'type' => 'text'),
        );
        $this->assertEqual($expected, $parsedcontent);
    }

    public function test_translate_text() {
        $this->assertEqual('word *word* word word (word) word!',
                $this->wwt->translate_text('This *is* some text (rough) content!'));
    }

    public function test_translate_text_empty() {
        $this->assertEqual('', $this->wwt->translate_text(''));
    }

    public function test_join_content() {
        $this->assertEqual('Test <->', $this->wwt->join_content(array(
            (object) array('content' => 'Tes'),
            (object) array('content' => 't <'),
            (object) array('content' => '->'),
        )));
    }
}

class test_reverse_auto_translator extends UnitTestCase {
    private $translator;

    public function setUp() {
        $this->translator = new reverse_auto_translator();
    }

    public function test_translate_content() {
        $this->assertEqual('<div class="frog">sihT &amp; <b>taht</b></span>&#xa0;',
                $this->translator->translate_content('<div class="frog">This &amp; <b>that</b></span>&#xa0;'));
    }
}
