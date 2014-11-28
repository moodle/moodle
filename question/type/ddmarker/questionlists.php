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
 * These classes handle transforming arrays of records into a linked tree of contexts, categories and questions.
 *
 * @package   qtype_ddmarker
 * @copyright 2012 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * These classes handle transforming arrays of records into a linked tree of contexts, categories and questions.
 *
 * @copyright 2012 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_ddmarker_list_item implements renderable {

    /**
     * @var count of questions contained in this item and sub items.
     */
    protected $qcount = 0;
    /**
     * @var children array of pointers to list_items either category_list_items or context_list_items
     */
    protected $children = array();

    protected $stringidentifier;
    protected $link;
    protected $record;
    protected $list;
    protected $parentlist;
    protected $listtype = null;

    public function __construct($stringidentifier, $link, $record, $list, $parentlist = null) {
        $this->stringidentifier = $stringidentifier;
        $this->link = $link;
        $this->record = $record;
        $this->list = $list;
        $this->parentlist = $parentlist;
    }

    public function add_child($child) {
        $this->children[] = $child;
        // Class array_unique relies on __toString() returning a unique string
        // to determine if objects in array are the same or not.
        $this->children = array_unique($this->children);
    }

    abstract protected function parent_node ();


    public function item_name() {
        return $this->record->name;
    }

    public function id_param_name() {
        return $this->listtype.'id';
    }
    public function get_id() {
        return $this->record->id;
    }
    public function get_q_count() {
        return $this->qcount;
    }
    public function get_string_identifier() {
        return $this->stringidentifier;
    }
    public function get_linked() {
        return $this->link;
    }
    public function get_list_type() {
        return $this->listtype;
    }
    public function get_children() {
        return $this->children;
    }
    public function leaf_to_root($qcount) {
        $this->qcount += $qcount;
        $parent = $this->parent_node();
        if ($parent !== null) {
            $parent->add_child($this);
            $parent->leaf_to_root($qcount);
        }
    }

    public function process($renderer) {
        echo '<li>';
        echo $renderer->item($this);
        $this->process_children($renderer);
        echo '</li>';
        flush();
    }

    protected function process_children($renderer) {
        echo '<ul>';
        foreach ($this->children as $child) {
            $child->process($renderer);
        }
        echo '</ul>';
    }

    public function question_ids() {
        return $this->child_question_ids();
    }

    protected function child_question_ids() {
        $ids = array();
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->question_ids());
        }
        return $ids;
    }


    public function __toString() {
        return get_class($this).' '.$this->record->id;
    }

    /**
     * @return the course id in which this item is contained or the id of the front page course
     */
    public function course_context_id() {
        return $this->parent_node()->course_context_id();
    }

}
class qtype_ddmarker_category_list_item extends qtype_ddmarker_list_item {
    protected $listtype = 'category';

    public function parent_node() {
        if ($this->record->parent == 0) {
            return $this->parentlist->get_instance($this->record->contextid);
        } else {
            return $this->list->get_instance($this->record->parent);
        }
    }
}
class qtype_ddmarker_question_list_item extends qtype_ddmarker_list_item {
    protected $listtype = 'question';

    public function parent_node() {
        return $this->parentlist->get_instance($this->record->category);
    }

    public function question_ids() {
        return array($this->record->id);
    }
}
class qtype_ddmarker_context_list_item extends qtype_ddmarker_list_item {
    protected $listtype = 'context';
    public function parent_node() {
        $pathids = explode('/', $this->record->path);
        if (count($pathids) >= 3) {
            return $this->list->get_instance($pathids[count($pathids) - 2]);
        } else {
            return null;
        }
    }

    public function item_name() {
        return context_helper::preload_from_record($this->record)->get_context_name();
    }

    public function course_context_id() {
        if ((int)$this->record->contextlevel === CONTEXT_COURSE) {
            return $this->record->id;
        } else {
            return parent::course_context_id();
        }
    }
}
/**
 * Describes a nested list of listitems. This class and sub classes contain the functionality to build the nested list.
 **/
abstract class qtype_ddmarker_list {
    protected $records = array();
    protected $instances = array();
    abstract protected function new_list_item($stringidentifier, $link, $record);
    protected function make_list_item_instances_from_records($stringidentifier, $link) {
        if (!empty($this->records)) {
            foreach ($this->records as $id => $record) {
                $this->instances[$id] = $this->new_list_item($stringidentifier, $link, $record);
            }
        }
    }
    public function get_instance($id) {
        return $this->instances[$id];
    }

    public function leaf_node ($id, $qcount) {
        $instance = $this->get_instance($id);
        $instance->leaf_to_root($qcount);
    }

}

class qtype_ddmarker_context_list extends qtype_ddmarker_list {

    protected function new_list_item($stringidentifier, $link, $record) {
        return new qtype_ddmarker_context_list_item($stringidentifier, $link, $record, $this);
    }

    public function __construct($stringidentifier, $link, $contextids) {
        global $DB;
        $this->records = array();
        foreach ($contextids as $contextid) {
            if (!isset($this->records[$contextid])) {
                $this->records[$contextid] = context::instance_by_id($contextid);
            }
            $parents = $this->records[$contextid]->get_parent_contexts();
            foreach ($parents as $parentcontext) {
                if (!isset($this->records[$parentcontext->id])) {
                    $this->records[$parentcontext->id] = $parentcontext;
                }
            }
        }
        $this->make_list_item_instances_from_records($stringidentifier, $link);
    }

    public function render($roottorender = null) {
        if ($roottorender === null) {
            $roottorender = $this->root_node();
        }
        $rootitem = html_writer::tag('li', $roottorender->render());
        return html_writer::tag('ul', $rootitem);
    }

    public function root_node () {
        return $this->get_instance(context_system::instance()->id);
    }
}


class qtype_ddmarker_category_list extends qtype_ddmarker_list {
    protected $contextlist;
    protected function new_list_item($stringidentifier, $link, $record) {
        return new qtype_ddmarker_category_list_item($stringidentifier, $link, $record, $this, $this->contextlist);
    }
    public function __construct($stringidentifier, $link, $contextids, $contextlist) {
        global $DB;
        $this->contextlist = $contextlist;
        // Probably most efficient way to reconstruct question category tree is
        // to load all q cats in relevant contexts.
        list($sql, $params) = $DB->get_in_or_equal($contextids);
        $this->records = $DB->get_records_select('question_categories', "contextid ".$sql, $params);
        $this->make_list_item_instances_from_records($stringidentifier, $link);
    }
}

class qtype_ddmarker_question_list extends qtype_ddmarker_list {
    protected $categorylist;
    protected function new_list_item($stringidentifier, $link, $record) {
        return new qtype_ddmarker_question_list_item($stringidentifier, $link, $record, $this, $this->categorylist);
    }
    public function __construct($stringidentifier, $link, $questions, $categorylist) {
        global $DB;
        $this->categorylist = $categorylist;
        $this->records = $questions;
        $this->make_list_item_instances_from_records($stringidentifier, $link);
    }
    public function prepare_for_processing($top) {
    }
}