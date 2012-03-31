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

defined('MOODLE_INTERNAL') || die();

/**
 * These classes handle transforming arrays of records into a linked tree of contexts, categories and questions.
 *
 * @package    qtype
 * @subpackage ddmarker
 * @copyright  2012 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_ddmarker_list_item {
    /**
     * @var count of questions contained in this item and sub items.
     */
    protected $qcount = 0;
    /**
     * @var children array of pointers to list_items either category_list_items or context_list_items
     */
    protected $children = array();

    protected $record;


    public function add_child($child) {
        $this->children[] = $child;
        //array_unique relies on __toString() returning a unique string to determine if objects in array
        //are the same or not
        $this->children = array_unique($this->children);
    }

    abstract protected function parent_node ();

    abstract public function render($stringidentifier, $link);

    public function leaf_to_root($qcount) {
        $this->qcount += $qcount;
        $parent = $this->parent_node();
        if ($parent !== null) {
            $parent->add_child($this);
            $parent->leaf_to_root($qcount);
        }
    }

    public function process($progresstrace = null, $depth = 0) {
        if (null === $progresstrace) {
            $progresstrace = new html_list_progress_trace();
        }
        $progresstrace->output((string)$this, $depth);
        $this->process_children($progresstrace, $depth);
    }

    protected function process_children($progresstrace, $depth) {
        $children = array();
        foreach ($this->children as $child) {
            $child->process($progresstrace, $depth + 1);
        }
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

    protected function render_children($stringidentifier, $link) {
        $children = array();
        foreach ($this->children as $child) {
            $children[] = $child->render($stringidentifier, $link);
        }
        return html_writer::alist($children);
    }

    public function __toString() {
        return get_class($this).' '.$this->record->id;
    }

}
class qtype_ddmarker_category_list_item extends qtype_ddmarker_list_item {

    public function __construct($record, $list, $parentlist) {
        $this->record = $record;
        $this->list = $list;
        $this->parentlist = $parentlist;
    }

    public function parent_node() {
        if ($this->record->parent == 0) {
            return $this->parentlist->get_instance($this->record->contextid);
        } else {
            return $this->list->get_instance($this->record->parent);
        }
    }

    public function render ($stringidentifier, $link) {
        global $PAGE;
        $a = new stdClass();
        $a->qcount = $this->qcount;
        $a->name = $this->record->name;
        $thisitem = get_string($stringidentifier.'category', 'qtype_ddmarker', $a);
        if ($link) {
            $actionurl = new moodle_url($PAGE->url, array('categoryid'=> $this->record->id));
            $thisitem = html_writer::tag('a', $thisitem, array('href' => $actionurl));
        }

        return $thisitem.$this->render_children($stringidentifier, $link);
    }
}
class qtype_ddmarker_question_list_item extends qtype_ddmarker_list_item {

    public function __construct($record, $list, $parentlist) {
        $this->record = $record;
        $this->list = $list;
        $this->parentlist = $parentlist;
    }

    public function parent_node() {
        return $this->parentlist->get_instance($this->record->category);
    }

    public function render ($stringidentifier, $link) {
        global $PAGE;
        $a = new stdClass();
        $a->name = $this->record->name;
        $thisitem = get_string('listitemquestion', 'qtype_ddmarker', $a);
        return $thisitem;
    }
    public function question_ids() {
        return array($this->record->id);
    }
}
class qtype_ddmarker_context_list_item extends qtype_ddmarker_list_item {

    protected $list;
    protected $parentlist = null;

    public function __construct($record, $list) {
        $this->record = $record;
        $this->list = $list;
    }

    public function parent_node() {
        $pathids = explode('/', $this->record->path);
        if (count($pathids) >= 3) {
            return $this->list->get_instance($pathids[count($pathids)-2]);
        } else {
            return null;
        }
    }

    public function render ($stringidentifier, $link) {
        global $PAGE;
        $a = new stdClass();
        $a->qcount = $this->qcount;
        $a->name = print_context_name($this->record);
        $thisitem = get_string($stringidentifier.'context', 'qtype_ddmarker', $a);
        if ($link) {
            $actionurl = new moodle_url($PAGE->url, array('contextid'=> $this->record->id));
            $thisitem = html_writer::tag('a', $thisitem, array('href' => $actionurl));
        }
        return $thisitem.$this->render_children($stringidentifier, $link);
    }
}
/**
 * Describes a nested list of listitems. This class and sub classes contain the functionality to build the nested list.
 **/
abstract class qtype_ddmarker_list {
    protected $records = array();
    protected $instances = array();
    abstract protected function new_list_item($record);
    protected function make_list_item_instances_from_records ($contextids = null) {
        if (!empty($this->records)) {
            foreach ($this->records as $id => $record) {
                $this->instances[$id] = $this->new_list_item($record);
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

    protected function new_list_item($record) {
        return new qtype_ddmarker_context_list_item($record, $this);
    }

    public function __construct($contextids) {
        global $DB;
        $this->records = array();
        foreach ($contextids as $contextid) {
            if (!isset($this->records[$contextid])) {
                $this->records[$contextid] = get_context_instance_by_id($contextid, MUST_EXIST);
            }
            $parents = get_parent_contexts($this->records[$contextid]);
            foreach ($parents as $parentcontextid) {
                if (!isset($this->records[$parentcontextid])) {
                    $this->records[$parentcontextid] =
                                        get_context_instance_by_id($parentcontextid, MUST_EXIST);
                }
            }
        }
        parent::make_list_item_instances_from_records ($contextids);
    }
    public function render($stringidentifier, $link, $roottorender = null) {
        if ($roottorender === null) {
            $roottorender = $this->root_node();
        }
        $rootitem = html_writer::tag('li', $roottorender->render($stringidentifier, $link));
        return html_writer::tag('ul', $rootitem);
    }
    public function root_node () {
        return $this->get_instance(get_context_instance(CONTEXT_SYSTEM)->id);
    }
}



class qtype_ddmarker_category_list extends qtype_ddmarker_list {
    protected $contextlist;
    protected function new_list_item($record) {
        return new qtype_ddmarker_category_list_item($record, $this, $this->contextlist);
    }
    public function __construct($contextids, $contextlist) {
        global $DB;
        $this->contextlist = $contextlist;
        //probably most efficient way to reconstruct question category tree is to load all q cats in relevant contexts
        list($sql, $params) = $DB->get_in_or_equal($contextids);
        $this->records = $DB->get_records_select('question_categories', "contextid ".$sql, $params);
        parent::make_list_item_instances_from_records ($contextids);
    }
}

class qtype_ddmarker_question_list extends qtype_ddmarker_list {
    protected $categorylist;
    protected function new_list_item($record) {
        return new qtype_ddmarker_question_list_item($record, $this, $this->categorylist);
    }
    public function __construct($questions, $categorylist) {
        global $DB;
        $this->categorylist = $categorylist;
        $this->records = $questions;
        parent::make_list_item_instances_from_records ();
    }
    public function prepare_for_processing($top) {
    }
}