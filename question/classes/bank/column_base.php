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
 * Base class for representing a column in a {@link question_bank_view}.
 *
 * @package   core_question
 * @copyright 1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_question\bank;
defined('MOODLE_INTERNAL') || die();


/**
 * Base class for representing a column in a {@link question_bank_view}.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class column_base {
    /**
     * @var view $qbank the question bank view we are helping to render.
     */
    protected $qbank;

    /** @var bool determine whether the column is td or th. */
    protected $isheading = false;

    /**
     * Constructor.
     * @param view $qbank the question bank view we are helping to render.
     */
    public function __construct(view $qbank) {
        $this->qbank = $qbank;
        $this->init();
    }

    /**
     * A chance for subclasses to initialise themselves, for example to load lang strings,
     * without having to override the constructor.
     */
    protected function init() {
    }

    /**
     * Set the column as heading
     */
    public function set_as_heading() {
        $this->isheading = true;
    }

    public function is_extra_row() {
        return false;
    }

    /**
     * Output the column header cell.
     */
    public function display_header() {
        echo '<th class="header ' . $this->get_classes() . '" scope="col">';
        $sortable = $this->is_sortable();
        $name = get_class($this);
        $title = $this->get_title();
        $tip = $this->get_title_tip();
        if (is_array($sortable)) {
            if ($title) {
                echo '<div class="title">' . $title . '</div>';
            }
            $links = array();
            foreach ($sortable as $subsort => $details) {
                $links[] = $this->make_sort_link($name . '-' . $subsort,
                        $details['title'], isset($details['tip']) ? $details['tip'] : '', !empty($details['reverse']));
            }
            echo '<div class="sorters">' . implode(' / ', $links) . '</div>';
        } else if ($sortable) {
            echo $this->make_sort_link($name, $title, $tip);
        } else {
            if ($tip) {
                echo '<span title="' . $tip . '">';
            }
            echo $title;
            if ($tip) {
                echo '</span>';
            }
        }
        echo "</th>\n";
    }

    /**
     * Title for this column. Not used if is_sortable returns an array.
     */
    protected abstract function get_title();

    /**
     * @return string a fuller version of the name. Use this when get_title() returns
     * something very short, and you want a longer version as a tool tip.
     */
    protected function get_title_tip() {
        return '';
    }

    /**
     * Get a link that changes the sort order, and indicates the current sort state.
     * @param string $sort the column to sort on.
     * @param string $title the link text.
     * @param string $tip the link tool-tip text. If empty, defaults to title.
     * @param bool $defaultreverse whether the default sort order for this column is descending, rather than ascending.
     * @return string HTML fragment.
     */
    protected function make_sort_link($sort, $title, $tip, $defaultreverse = false) {
        $currentsort = $this->qbank->get_primary_sort_order($sort);
        $newsortreverse = $defaultreverse;
        if ($currentsort) {
            $newsortreverse = $currentsort > 0;
        }
        if (!$tip) {
            $tip = $title;
        }
        if ($newsortreverse) {
            $tip = get_string('sortbyxreverse', '', $tip);
        } else {
            $tip = get_string('sortbyx', '', $tip);
        }
        $link = '<a href="' . $this->qbank->new_sort_url($sort, $newsortreverse) . '" title="' . $tip . '">';
        $link .= $title;
        if ($currentsort) {
            $link .= $this->get_sort_icon($currentsort < 0);
        }
        $link .= '</a>';
        return $link;
    }

    /**
     * Get an icon representing the corrent sort state.
     * @param bool $reverse sort is descending, not ascending.
     * @return string HTML image tag.
     */
    protected function get_sort_icon($reverse) {
        global $OUTPUT;
        if ($reverse) {
            return $OUTPUT->pix_icon('t/sort_desc', get_string('desc'), '', array('class' => 'iconsort'));
        } else {
            return $OUTPUT->pix_icon('t/sort_asc', get_string('asc'), '', array('class' => 'iconsort'));
        }
    }

    /**
     * Output this column.
     * @param object $question the row from the $question table, augmented with extra information.
     * @param string $rowclasses CSS class names that should be applied to this row of output.
     */
    public function display($question, $rowclasses) {
        $this->display_start($question, $rowclasses);
        $this->display_content($question, $rowclasses);
        $this->display_end($question, $rowclasses);
    }

    /**
     * Output the opening column tag.  If it is set as heading, it will use <th> tag instead of <td>
     *
     * @param \stdClass $question
     * @param string $rowclasses
     */
    protected function display_start($question, $rowclasses) {
        $tag = 'td';
        $attr = array('class' => $this->get_classes());
        if ($this->isheading) {
            $tag = 'th';
            $attr['scope'] = 'row';
        }
        echo \html_writer::start_tag($tag, $attr);
    }

    /**
     * @return string the CSS classes to apply to every cell in this column.
     */
    protected function get_classes() {
        $classes = $this->get_extra_classes();
        $classes[] = $this->get_name();
        return implode(' ', $classes);
    }

    /**
     * Get the internal name for this column. Used as a CSS class name,
     * and to store information about the current sort. Must match PARAM_ALPHA.
     *
     * @return string column name.
     */
    public abstract function get_name();

    /**
     * @return array any extra class names you would like applied to every cell in this column.
     */
    public function get_extra_classes() {
        return array();
    }

    /**
     * Output the contents of this column.
     * @param object $question the row from the $question table, augmented with extra information.
     * @param string $rowclasses CSS class names that should be applied to this row of output.
     */
    protected abstract function display_content($question, $rowclasses);

    /**
     * Output the closing column tag
     *
     * @param object $question
     * @param string $rowclasses
     */
    protected function display_end($question, $rowclasses) {
        $tag = 'td';
        if ($this->isheading) {
            $tag = 'th';
        }
        echo \html_writer::end_tag($tag);
    }

    /**
     * Return an array 'table_alias' => 'JOIN clause' to bring in any data that
     * this column required.
     *
     * The return values for all the columns will be checked. It is OK if two
     * columns join in the same table with the same alias and identical JOIN clauses.
     * If to columns try to use the same alias with different joins, you get an error.
     * The only table included by default is the question table, which is aliased to 'q'.
     *
     * It is importnat that your join simply adds additional data (or NULLs) to the
     * existing rows of the query. It must not cause additional rows.
     *
     * @return array 'table_alias' => 'JOIN clause'
     */
    public function get_extra_joins() {
        return array();
    }

    /**
     * @return array fields required. use table alias 'q' for the question table, or one of the
     * ones from get_extra_joins. Every field requested must specify a table prefix.
     */
    public function get_required_fields() {
        return array();
    }

    /**
     * If this column needs extra data (e.g. tags) then load that here.
     *
     * The extra data should be added to the question object in the array.
     * Probably a good idea to check that another column has not already
     * loaded the data you want.
     *
     * @param \stdClass[] $questions the questions that will be displayed.
     */
    public function load_additional_data(array $questions) {
    }

    /**
     * Load the tags for each question.
     *
     * Helper that can be used from {@link load_additional_data()};
     *
     * @param array $questions
     */
    public function load_question_tags(array $questions) {
        $firstquestion = reset($questions);
        if (isset($firstquestion->tags)) {
            // Looks like tags are already loaded, so don't do it again.
            return;
        }

        // Load the tags.
        $tagdata = \core_tag_tag::get_items_tags('core_question', 'question',
                array_keys($questions));

        // Add them to the question objects.
        foreach ($tagdata as $questionid => $tags) {
            $questions[$questionid]->tags = $tags;
        }
    }

    /**
     * Can this column be sorted on? You can return either:
     *  + false for no (the default),
     *  + a field name, if sorting this column corresponds to sorting on that datbase field.
     *  + an array of subnames to sort on as follows
     *  return array(
     *      'firstname' => array('field' => 'uc.firstname', 'title' => get_string('firstname')),
     *      'lastname' => array('field' => 'uc.lastname', 'title' => get_string('lastname')),
     *  );
     * As well as field, and field, you can also add 'revers' => 1 if you want the default sort
     * order to be DESC.
     * @return mixed as above.
     */
    public function is_sortable() {
        return false;
    }

    /**
     * Helper method for building sort clauses.
     * @param bool $reverse whether the normal direction should be reversed.
     * @return string 'ASC' or 'DESC'
     */
    protected function sortorder($reverse) {
        if ($reverse) {
            return ' DESC';
        } else {
            return ' ASC';
        }
    }

    /**
     * @param bool $reverse Whether to sort in the reverse of the default sort order.
     * @param string $subsort if is_sortable returns an array of subnames, then this will be
     *      one of those. Otherwise will be empty.
     * @return string some SQL to go in the order by clause.
     */
    public function sort_expression($reverse, $subsort) {
        $sortable = $this->is_sortable();
        if (is_array($sortable)) {
            if (array_key_exists($subsort, $sortable)) {
                return $sortable[$subsort]['field'] . $this->sortorder($reverse);
            } else {
                throw new \coding_exception('Unexpected $subsort type: ' . $subsort);
            }
        } else if ($sortable) {
            return $sortable . $this->sortorder($reverse);
        } else {
            throw new \coding_exception('sort_expression called on a non-sortable column.');
        }
    }
}
