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
 * @package moodlecore
 * @subpackage backup-helper
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Helper class that will perform all the necessary decoding tasks on restore
 *
 * This class will register all the restore_decode_content and
 * restore_decode_rule instances defined by the restore tasks
 * in order to perform the complete decoding of links in the
 * final task of the restore_plan execution.
 *
 * By visiting each content provider will apply all the defined rules
 *
 * TODO: Complete phpdocs
 */
class restore_decode_processor {

    protected $contents;  // Array of restore_decode_content providers
    protected $rules;     // Array of restore_decode_rule workers
    protected $restoreid;   // The unique restoreid we are executing
    protected $sourcewwwroot; // The original wwwroot of the backup file
    protected $targetwwwroot; // The target wwwroot of the restore operation

    public function __construct($restoreid, $sourcewwwroot, $targetwwwroot) {
        $this->restoreid = $restoreid;
        $this->sourcewwwroot = $sourcewwwroot;
        $this->targetwwwroot = $targetwwwroot;

        $this->contents = array();
        $this->rules    = array();
    }

    public function add_content($content) {
        if (!$content instanceof restore_decode_content) {
            throw new restore_decode_processor_exception('incorrect_restore_decode_content', get_class($content));
        }
        $content->set_restoreid($this->restoreid);
        $this->contents[] = $content;
    }

    public function add_rule($rule) {
        if (!$rule instanceof restore_decode_rule) {
            throw new restore_decode_processor_exception('incorrect_restore_decode_rule', get_class($rule));
        }
        $rule->set_restoreid($this->restoreid);
        $rule->set_wwwroots($this->sourcewwwroot, $this->targetwwwroot);
        $this->rules[] = $rule;
    }

    /**
     * Visit all the restore_decode_content providers
     * that will cause decode_content() to be called
     * for each content
     */
    public function execute() {
        // Iterate over all contents, visiting them
        /** @var restore_decode_content $content */
        foreach ($this->contents as $content) {
            $content->process($this);
        }
    }

    /**
     * Receive content from restore_decode_content objects
     * and apply all the restore_decode_rules to them
     */
    public function decode_content($content) {
        if (!$content = $this->precheck_content($content)) { // Perform some prechecks
            return false;
        }
        // Iterate over all rules, chaining results
        foreach ($this->rules as $rule) {
            $content = $rule->decode($content);
        }
        return $content;
    }

    /**
     * Adds all the course/section/activity/block contents and rules
     */
    public static function register_link_decoders($processor) {
        $tasks = array(); // To get the list of tasks having decoders

        // Add the course task
        $tasks[] = 'restore_course_task';

        // Add the section task
        $tasks[] = 'restore_section_task';

        // Add the module tasks
        $mods = core_component::get_plugin_list('mod');
        foreach ($mods as $mod => $moddir) {
            if (class_exists('restore_' . $mod . '_activity_task')) {
                $tasks[] = 'restore_' . $mod . '_activity_task';
            }
        }

        // Add the default block task
        $tasks[] = 'restore_default_block_task';

        // Add the custom block tasks
        $blocks = core_component::get_plugin_list('block');
        foreach ($blocks as $block => $blockdir) {
            if (class_exists('restore_' . $block . '_block_task')) {
                $tasks[] = 'restore_' . $block . '_block_task';
            }
        }

        // We have all the tasks registered, let's iterate over them, getting
        // contents and rules and adding them to the processor
        foreach ($tasks as $classname) {
            // Get restore_decode_content array and add to processor
            $contents = call_user_func(array($classname, 'define_decode_contents'));
            if (!is_array($contents)) {
                throw new restore_decode_processor_exception('define_decode_contents_not_array', $classname);
            }
            foreach ($contents as $content) {
                $processor->add_content($content);
            }
            // Get restore_decode_rule array and add to processor
            $rules = call_user_func(array($classname, 'define_decode_rules'));
            if (!is_array($rules)) {
                throw new restore_decode_processor_exception('define_decode_rules_not_array', $classname);
            }
            foreach ($rules as $rule) {
                $processor->add_rule($rule);
            }
        }

        // Now process all the plugins contents (note plugins don't have support for rules)
        // TODO: Add other plugin types (course formats, local...) here if we add them to backup/restore
        $plugins = array('qtype');
        foreach ($plugins as $plugin) {
            $contents = restore_plugin::get_restore_decode_contents($plugin);
            if (!is_array($contents)) {
                throw new restore_decode_processor_exception('get_restore_decode_contents_not_array', $plugin);
            }
            foreach ($contents as $content) {
                $processor->add_content($content);
            }
        }
    }

// Protected API starts here

    /**
     * Perform some general checks in content. Returning false rules processing is skipped
     */
    protected function precheck_content($content) {
        // Look for $@ in content (all interlinks contain that)
        return (strpos($content, '$@') === false) ? false : $content;
    }
}

/*
 * Exception class used by all the @restore_decode_content stuff
 */
class restore_decode_processor_exception extends backup_exception {

    public function __construct($errorcode, $a=NULL, $debuginfo=null) {
        return parent::__construct($errorcode, $a, $debuginfo);
    }
}
