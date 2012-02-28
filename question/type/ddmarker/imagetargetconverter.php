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
 * This page lets admins check the environment requirements for this question type.
 *
 * @package    qtype
 * @subpackage pmatch
 * @copyright  2012 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

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
        $this->children = array_unique($this->children);
    }

    abstract protected function parent_node ();

    abstract public function render ();

    public function leaf_to_root($qcount) {
        $this->qcount += $qcount;
        $parent = $this->parent_node();
        if ($parent !== null) {
            $parent->add_child($this);
            $parent->leaf_to_root($qcount);
        }
    }

    protected function render_children() {
        $children = array();
        foreach ($this->children as $child) {
            $children[] = $child->render();
        }
        return html_writer::alist($children);
    }

    public function __toString() {
        return get_class($this).' '.$this->record->id;
    }

}
class qtype_ddmarker_category_list_item extends qtype_ddmarker_list_item {

    function __construct($record, $list, $parentlist) {
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

    public function render () {
        $a = new stdClass();
        $a->qcount = $this->qcount;
        $a->name = $this->record->name;
        return get_string('categorylistitem', 'qtype_ddmarker', $a).$this->render_children();
    }
}
class qtype_ddmarker_context_list_item extends qtype_ddmarker_list_item {

    protected $list;
    protected $parentlist = null;

    function __construct($record, $list) {
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

    public function render () {
        $a = new stdClass();
        $a->qcount = $this->qcount;
        $a->name = $this->record->get_context_name();
        return get_string('contextlistitem', 'qtype_ddmarker', $a).$this->render_children();
    }
}
/**
 * Describes a nested list of listitems. This class and sub classes contain the functionality to build the nested list.
 **/
abstract class qtype_ddmarker_list {
    abstract protected function new_list_item($record);
    protected function make_list_item_instances_from_records ($contextids) {
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
    protected $records = array();
    protected $instances = array();

    protected function new_list_item($record) {
        return new qtype_ddmarker_context_list_item($record, $this);
    }

    public function __construct($contextids) {
        global $DB;
        $this->records = array();
        foreach ($contextids as $contextid) {
            if (!isset($records[$contextid])) {
                $this->records[$contextid] = context::instance_by_id($contextid);
            }
            $this->records += $this->records[$contextid]->get_parent_contexts();
        }
        parent::make_list_item_instances_from_records ($contextids);
    }
    public function render() {
        $rootitem = html_writer::tag('li', $this->root_node()->render());
        return html_writer::tag('ul', $rootitem);
    }
    public function root_node () {
        return $this->get_instance(get_context_instance(CONTEXT_SYSTEM)->id);
    }
}

class qtype_ddmarker_category_list extends qtype_ddmarker_list {
    protected $records = array();
    protected $instances = array();
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
// Check the user is logged in.
require_login();
$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/question:config', $context);

admin_externalpage_setup('qtypeddmarkerfromimagetarget');

// Header.
echo $OUTPUT->header();
echo $OUTPUT->heading_with_help(get_string('imagetargetconverter', 'qtype_ddmarker'), '', 'qtype_ddmarker');

$catswithquestions = $DB->get_records_sql('SELECT cat.id, cat.contextid, COUNT(1) AS qcount '.
                                            'FROM {question_categories} cat, {question} q '.
                                            'WHERE q.category =  cat.id '.
                                            'GROUP BY cat.id, cat.contextid');
//print_object($catswithquestions);

$contextids = array();
foreach ($catswithquestions as $catwithquestions) {
    $contextids[] = $catwithquestions->contextid;
}

$contexts = new qtype_ddmarker_context_list($contextids);
$categories = new qtype_ddmarker_category_list($contextids, $contexts);

foreach ($catswithquestions as $catwithquestions) {
    $categories->leaf_node($catwithquestions->id, $catwithquestions->qcount);
}
//print_object($contexts->root_node());

echo $contexts->render();


// Footer.
echo $OUTPUT->footer();
