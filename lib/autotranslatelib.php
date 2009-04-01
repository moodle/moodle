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
 * Prototype automatic translation system for Moodle.
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package autotranslate
 */

/**
 * Machine-translate some content into the configured language.
 * @param string $content some (HTML) content.
 * @return string the content. translated to the configured language.
 */
function auto_translate_content($content) {
    global $CFG;
    if (empty($CFG->autotranslatetolang)) {
        if (empty($CFG->rolesactive)) {
            $CFG->autotranslatetolang = 'null';
        } else {
            $langs = array_keys(auto_translate_target_languages());
            array_shift($langs);
            $lang = $langs[mt_rand(0, count($langs) - 1)];
            set_config('autotranslatetolang', $lang);
        }
    }
    $translator = translator_factory::instance()->get_translator($CFG->autotranslatetolang);
    return $translator->translate_content($content);
}

function is_untranslatable_string($identifier, $module) {
    global $CFG;
    return $module == 'autotranslate' || $module == 'langconfig' || empty($CFG->rolesactive);
}

/**
 * Get a list of languages we know how to automatically translate into.
 * @return array language code => human readable name.
 */
function auto_translate_target_languages() {
    static $list = null;
    if (!is_null($list)) {
        return $list;
    }
    $codes = array('null', 'cs_ps', 'en_nz_pl', 'en_uk_pl');
    $list = array();
    foreach ($codes as $code) {
        $name = get_string('targetlang_' . $code, 'autotranslate');
        if (substr($name, 0, 2) != '[[') {
            $name = base64_decode($name);
        }
        $list[$code] = $name;
    }
    return $list;
}

/**
 * Singleton class that gets the right auto_translator for a target language.
 */
class translator_factory {
    private static $instance = null;
    private $translators = array();

    protected function __constructor() {
    }

    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new translator_factory();
        }
        return self::$instance;
    }

    public static function get_translator($lang) {
        if (empty($lang)) {
            $lang = 'null';
        }
        if (isset($translators[$lang])) {
            return $translators[$lang];
        }
        $classname = $lang . '_auto_translator';
        if (strpos(print_backtrace(debug_backtrace(), true), 'database') !== false ||
                strpos(print_backtrace(debug_backtrace(), true), 'print_error') !== false) {
            $classname = 'null_auto_translator';
        }
        if (!class_exists($classname)) {
            throw new moodle_exception();
        }
        $translators[$lang] = new $classname;
        return $translators[$lang];
    }
}

interface auto_translator {
    public function translate_content($content);
}

class null_auto_translator implements auto_translator {
    public function translate_content($content) {
        return $content;
    }
}

abstract class word_by_word_translator implements auto_translator {
    public function translate_content($content) {
        $parsedcontent = $this->split_text_and_tags($content);
        foreach ($parsedcontent as $key => $item) {
            if ($item->type == 'text') {
                $parsedcontent[$key]->content = $this->translate_text($item->content);
            }
        }
        return $this->join_content($parsedcontent);
    }

    protected function split_text_and_tags($content) {
        $bits = preg_split('/((?:<[^#%*>][^>]*>|&\w+;|&#\d+;|&#[xX][0-9a-fA-F]+;)+)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        $parsedcontent = array();
        foreach ($bits as $index => $bit) {
            $item = new stdClass;
            $item->content = $bit;
            if ($index % 2) {
                $item->type = 'tag';
            } else {
                $item->type = 'text';
            }
            $parsedcontent[] = $item;
        }
        return $parsedcontent;
    }

    protected function translate_text($text) {
        $wordsandbreaks = preg_split('/\b/', $text);
        foreach ($wordsandbreaks as $index => $word) {
            if (preg_match('/\w+/', $word)) {
                $wordsandbreaks[$index] = $this->translate_word($word);
            }
        }
        return implode('', $wordsandbreaks);
    }

    protected function join_content($content) {
        $out = '';
        foreach ($content as $item) {
            $out .= $item->content;
        }
        return $out;
    }

    abstract protected function translate_word($word);
}

class reverse_auto_translator extends word_by_word_translator {
    protected function translate_word($word) {
        return strrev($word);
    }
}

class cs_ps_auto_translator extends word_by_word_translator {
    protected function translate_word($word) {
        $len = strlen($word);
        if ($len == 0) {
            return '';
        }
        $newword = chr(71);
        if ($len >= 2) {
            $end = round(($len - 2) / 5);
            $newword .= str_repeat(chr(114), $len - $end - 1);
            $newword .= str_repeat(chr(33), $end);
        }
        return $newword;
    }
}

class en_nz_pl_auto_translator extends word_by_word_translator {
    private $library = null;
    private $librarylen;
    private function ensure_library_loaded() {
        if (is_null($this->library)) {
            $this->library = unserialize(base64_decode(
                'YTo5OntpOjA7czozOiJjYXQiO2k6MTtzOjQ6InBvbnkiO2k6MjtzOjQ6InJh' .
                'Z2UiO2k6MztzOjU6Im5pbmphIjtpOjQ7czo1OiJhbmdyeSI7aTo1O3M6Njoi' .
                'ZmllcmNlIjtpOjY7czo2OiJjb2ZmZWUiO2k6NztzOjc6ImNhZmZpbmUiO2k6' .
                'ODtzOjY6Im1haGFyYSI7fQ=='));
            $this->librarylen = count($this->library);
        }
    }
    public function translate_word($word) {
        $len = strlen($word);
        if ($len == 0) {
            return '';
        }
        $this->ensure_library_loaded();
        return $this->library[($len - 1) % $this->librarylen];
    }
}

class en_uk_pl_auto_translator extends word_by_word_translator {
    private $library = null;
    private $librarylen;
    private function ensure_library_loaded() {
        if (is_null($this->library)) {
            $this->library = unserialize(base64_decode(
                'YTo5OntpOjA7czo0OiJjb29sIjtpOjE7czoyOiJ0aCI7aToyO3M6NToiY3Jh' .
                'enkiO2k6MztzOjc6ImJhc3Nvb24iO2k6NDtzOjEzOiJjb250cmFiYXNzb29u' .
                'IjtpOjU7czoxMzoiZ28gKHRoZSBnYW1lKSI7aTo2O3M6ODoicXVpei1tYW4i' .
                'O2k6NztzOjM6Im1hZCI7aTo4O3M6NDoicnRmbSI7fQ=='));
            $this->librarylen = count($this->library);
        }
    }
    public function translate_word($word) {
        $len = strlen($word);
        if ($len == 0) {
            return '';
        }
        $this->ensure_library_loaded();
        return $this->library[($len - 1) % $this->librarylen];
    }
}
