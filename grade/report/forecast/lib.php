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
 * Definition of the grade_forecast_report class is defined
 *
 * @package    gradereport_forecast
 * @copyright  2016 Louisiana State University, Chad Mazilly, Robert Russo, Dave Elliott
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/grade/report/lib.php');
require_once($CFG->libdir.'/tablelib.php');

//showhiddenitems values
define("GRADE_REPORT_FORECAST_HIDE_HIDDEN", 0);
define("GRADE_REPORT_FORECAST_HIDE_UNTIL", 1);
define("GRADE_REPORT_FORECAST_SHOW_HIDDEN", 2);

/**
 * Class providing an API for the user report building and displaying.
 * @uses grade_report
 * @package gradereport_forecast
 */
class grade_report_forecast extends grade_report {

    /**
     * The user.
     * @var object $user
     */
    public $user;

    /**
     * A flexitable to hold the data.
     * @var object $table
     */
    public $table;

    /**
     * An array of table headers
     * @var array
     */
    public $tableheaders = array();

    /**
     * An array of table columns
     * @var array
     */
    public $tablecolumns = array();

    /**
     * An array containing rows of data for the table.
     * @var type
     */
    public $tabledata = array();

    /**
     * The grade tree structure
     * @var grade_tree
     */
    public $gtree;

    /**
     * Flat structure similar to grade tree
     */
    public $gseq;

    /**
     * Decimal points to use for values in the report, default 2
     * @var int
     */
    public $decimals = 2;

    /**
     * The number of decimal places to round range to, default 0
     * @var int
     */
    public $rangedecimals = 0;

    /**
     * Show letter grades in the report, default true
     * @var bool
     */
    public $showlettergrade = true;

    /**
     * Show grade percentages in the report, default true
     * @var bool
     */
    public $showgradepercentage = true;

    public $maxdepth;
    public $evenodd;

    public $canviewhidden;

    public $switch;

    /**
     * Show hidden items even when user does not have required cap
     */
    public $showhiddenitems;
    public $showtotalsifcontainhidden;

    public $baseurl;
    public $pbarurl;

    public $courseid;

    /*
     * The course's grade letters array
     *
     * bounday => letter
     */
    public $letters = [];

    /*
     * Formatted input grade data
     *
     * array: grades(array: grade_item_id => input value) | totalUngradedItemCount | inputGradeItemCount
     */
    public $inputData;

    /*
     * An array of grade_items and their "aggregate" array
     *
     * grade_item_id => grade | grademin | grademax | calculatedValue
     */
    public $itemAggregates;
    
    /*
     * The master course category's details
     *
     * array: grade_category | element
     */
    public $courseGradeData;

    /*
     * string: the missing (ungraded) grade item id
     */
    public $ungradedGradeItemKey;

    /*
     * An array of DOM element ids, grouped by course letters "boundary", with corresponding grade output string
     * 
     * string: must make element id => grade output
     */
    public $mustMakeArray;

    /*
     * Response array that will be converted to JSON
     *
     * array: showMustMake | mustMakeArray | cats | course
     */
    public $response;

    /**
     * The modinfo object to be used.
     *
     * @var course_modinfo
     */
    protected $modinfo = null;

    /**
     * View as user.
     *
     * When this is set to true, the visibility checks, and capability checks will be
     * applied to the user whose grades are being displayed. This is very useful when
     * a mentor/parent is viewing the report of their mentee because they need to have
     * access to the same information, but not more, not less.
     *
     * @var boolean
     */
    protected $viewasuser = false;

    /**
     * An array that collects the aggregationhints for every
     * grade_item. The hints contain grade, grademin, grademax
     * status, weight and parent.
     *
     * @var array
     */
    protected $aggregationhints = array();

    // BEGIN LSU To avoid "creation of dynamic property" warning
    public $enabledforstudents;
    // END LSU To avoid "creation of dynamic property" warning

    /**
     * Constructor. Sets local copies of user preferences and initialises grade_tree.
     * @param int $courseid
     * @param object $gpr grade plugin return tracking object
     * @param string $context
     * @param int $userid The id of the user
     * @param bool $viewasuser Set this to true when the current user is a mentor/parent of the targetted user.
     * @param array $inputData
     */
    public function __construct($courseid, $gpr, $context, $userid, $viewasuser = null, $inputData = []) {
        global $DB, $CFG;
        parent::__construct($courseid, $gpr, $context);

        $this->showhiddenitems = grade_get_setting($this->courseid, 'report_forecast_showhiddenitems', $CFG->grade_report_forecast_showhiddenitems);
        $this->showtotalsifcontainhidden = array($this->courseid => grade_get_setting($this->courseid, 'report_forecast_showtotalsifcontainhidden', $CFG->grade_report_forecast_showtotalsifcontainhidden));

        $this->showlettergrade = grade_get_setting($this->courseid, 'report_forecast_showlettergrade', !empty($CFG->grade_report_forecast_showlettergrade));

        $this->showgradepercentage = grade_get_setting($this->courseid, 'report_forecast_showgradepercentage', !empty($CFG->grade_report_forecast_showgradepercentage));

        $this->viewasuser = $viewasuser;

        // The default grade decimals is 2
        $defaultdecimals = 2;
        if (property_exists($CFG, 'grade_decimalpoints')) {
            $defaultdecimals = $CFG->grade_decimalpoints;
        }
        $this->decimals = grade_get_setting($this->courseid, 'decimalpoints', $defaultdecimals);

        // The default range decimals is 0
        $defaultrangedecimals = 0;
        if (property_exists($CFG, 'grade_report_forecast_rangedecimals')) {
            $defaultrangedecimals = $CFG->grade_report_forecast_rangedecimals;
        }
        $this->rangedecimals = grade_get_setting($this->courseid, 'report_forecast_rangedecimals', $defaultrangedecimals);

        if (property_exists($CFG, 'grade_report_forecast_enabledforstudents')) {
            $defaultenabledforstudents = $CFG->grade_report_forecast_enabledforstudents;
        }
        $this->enabledforstudents = grade_get_setting($this->courseid, 'report_forecast_rangedecimals', $defaultenabledforstudents);

        // hard set this to true as we need the gtree to be consistent
        $this->switch = true;

        // Grab the grade_tree for this course
        $this->gtree = new grade_tree($this->courseid, false, $this->switch, null, !$CFG->enableoutcomes);

        // Get the user (for full name).
        $this->user = $DB->get_record('user', array('id' => $userid));

        // What user are we viewing this as?
        $coursecontext = context_course::instance($this->courseid);
        if ($viewasuser) {
            $this->modinfo = new course_modinfo($this->course, $this->user->id);
            $this->canviewhidden = has_capability('moodle/grade:viewhidden', $coursecontext, $this->user->id);
        } else {
            $this->modinfo = $this->gtree->modinfo;
            $this->canviewhidden = has_capability('moodle/grade:viewhidden', $coursecontext);
        }

        // Determine the number of rows and indentation.
        $this->maxdepth = 1;
        $this->inject_rowspans($this->gtree->top_element);
        $this->maxdepth++; // Need to account for the lead column that spans all children.
        for ($i = 1; $i <= $this->maxdepth; $i++) {
            $this->evenodd[$i] = 0;
        }

        $this->tabledata = array();

        // base url for sorting by first/last name
        $this->baseurl = $CFG->wwwroot.'/grade/report?id='.$courseid.'&amp;userid='.$userid;
        $this->pbarurl = $this->baseurl;

        $this->courseid = $courseid;
        
        $this->letters = grade_get_letters($coursecontext);

        $this->inputData = $this->formatRawInputData($inputData);
        $this->itemAggregates = [];
        $this->courseGradeData = [];
        $this->ungradedGradeItemKey = '';
        $this->mustMakeArray = [];
        
        $this->response = $this->newResponse();

        // no groups on this report - rank is from all course users
        $this->setup_table();
    }

    /**
     * Returns a JSON array of this user's forecasted category and course grades for this course considering any input values
     * 
     * @return JSON array
     */
    public function getJsonResponse() {
        $showMustMake = $this->shouldShowMustMake();

        // add grades to response
        $this->addGradesToResponse($showMustMake);

        // add "must make" data to response
        if ($this->response['showMustMake'] = $showMustMake) {
            $this->response['ungradedGradeItemKey'] = $this->ungradedGradeItemKey;
            $this->response['mustMakeArray'] = $this->mustMakeArray;
        }

        return $this->formattedJsonResponse();
    }

    /**
     * Adds forecasted grade information to the response
     * 
     * @param  bool  $shouldCalculateMustMake  whether or not to calculate must make array
     * @return void
     */
    private function addGradesToResponse($shouldCalculateMustMake = false) {

        // include all transformed, aggregated sub-category grades
        $this->response['cats'] = $this->getTransformedCategoryGrades();

        // include the transformed master course grade
        if ( ! empty($this->courseGradeData)) {
            $this->response['course'] = $this->getTransformedCourseGrade();
        }

        if ($shouldCalculateMustMake) {
            $this->calculateMustMake();
        }
    }

    /**
     * Returns a formatted JSON array of the report's current "response"
     * 
     * @return JSON array
     */
    private function formattedJsonResponse() {
        return json_encode($this->response);
    }

    /**
     * Returns a default "response" array
     * 
     * @return array
     */
    private function newResponse() {
        return [
            'cats' => [],
            'course' => '',
            'showMustMake' => false,
            'mustMakeArray' => [],
        ];
    }

    /**
     * Returns an array of this course's "category" grade_items and their corresponding calculated aggregates
     *
     * By default, formats output as: grade_item_id => display_string
     *
     * If no transform, output as array: categoryItem|aggregate
     * 
     * @param  boolean $transform  whether or not to transform the output
     * @return array
     */
    private function getTransformedCategoryGrades($transform = true) {

        // get the inverted gtree "levels" array
        $levels = array_reverse($this->gtree->levels, true);

        $categoryGrades = [];

        // iterate through each of the course's reversed gtree "levels", aggregating "level"-level categories along the way
        // levelIndex = gtree "level" number index (0 as course level)
        // levelItems = gtree "level" array
        foreach ($levels as $levelIndex => $levelItems) {

            // iterate through the level items (which can be of type: item, categoryitem, category, courseitem)
            foreach ($levelItems as $key => $levelItem) {
                
                // act only on "category" level items, which can include courses and any sub-categories
                if ($levelItem['type'] == 'category') {
                    
                    // pluck the gtree element
                    $element = $this->getLevelItemElement($levelItem);

                    // extract the grade_category from this element
                    $category = $this->getElementObject($element);

                    // if this is a master level "course" category, cache its data for final aggregation
                    if ($this->isCourseCategory($category)) {
                        $this->courseGradeData = [
                            'category' => $category,
                            'element' => $element,
                        ];
                        continue;
                    }

                    // fetch the grade_item representation of this grade_category
                    // TODO: get grade_item from element children "categoryitem" ???
                    // TODO: cache this result and check for in the following processes
                    $categoryItem = $this->getGradeItemFromCategory($category);

                    if ($category->aggregateonlygraded == 1) {
                        $removeUngradedItems = true;
                    } else {
                        $removeUngradedItems = false;
                    }


                    // if this item has already been aggregated, move on
                    if ($this->itemIdAlreadyAggregated($categoryItem->id)) // <--- add "or needs to be updated" here???
                        return;

                    // get all grade_items (only) that will be considered in the category aggregation calculation
                    $categoryGradeItems = $this->getElementChildren($element, ['item', 'category'], true);

                    // get all grade values belonging to the given grade items, removing ungraded/uninput items from calculation
                    $categoryGradeValues = $this->getCategoryGradeItemValuesArray($category, $categoryGradeItems, $removeUngradedItems);

                    // get the aggregate of this category using the given grade items and values
                    $aggregate = $this->getCategoryGradeAggregate($category, $categoryGradeItems, $categoryGradeValues, true);

                    // add to output
                    $categoryGrades[] = ['categoryItem' => $categoryItem, 'aggregate' => $aggregate];

                    // store this value for future aggregations
                    $this->storeItemAggregate($categoryItem->id, $aggregate);
                }
            }
        }

        return ($transform) ? $this->transformCategoryGradesForResponse($categoryGrades) : $categoryGrades;
    }

    /**
     * Tranforms given category grade arrays to prep for response
     * 
     * @param  array  $categoryGrades  categoryItem|aggregate
     * @return array  category_grade_item_id => formatted total display
     */
    private function transformCategoryGradesForResponse($categoryGrades) {
        $output = [];

        foreach ($categoryGrades as $cg) {
            $output[$cg['categoryItem']->id] = $this->formatItemAggregateDisplay($cg['categoryItem'], $cg['aggregate']);
        }

        return $output;
    }

    /**
     * Returns the master course's transformed, aggregated total
     *
     * By default, formats as display string
     *
     * If no transform, output as array: courseItem|aggregate
     * 
     * @param  boolean $transform      whether or not to transform the output
     * @param  boolean $transformOnly  if transforming, show only this field
     * @return array
     */
    private function getTransformedCourseGrade($transform = true, $transformOnly = '') {
        $courseGrade = [];

        if ($this->courseGradeData['category']->aggregateonlygraded == 1) {
            $removeUngradedItems = true;
        } else {
            $removeUngradedItems = false;
        }

        // get the course's grade item
        $courseItem = $this->getGradeItemFromCategory($this->courseGradeData['category'], 'course');

        // get all grade_items (only) that will be considered in the course aggregation calculation
        $courseGradeItems = $this->getElementChildren($this->courseGradeData['element'], ['item', 'category'], true);

        // get all grade values belonging to the given grade items, setting ungraded/uninput items to zero
        $courseGradeValues = $this->getCategoryGradeItemValuesArray($this->courseGradeData['category'], $courseGradeItems, $removeUngradedItems);

        // get the aggregate of this course using the given grade items and values
        $aggregate = $this->getCategoryGradeAggregate($this->courseGradeData['category'], $courseGradeItems, $courseGradeValues, true);

        // add to output
        $courseGrade = ['courseItem' => $courseItem, 'aggregate' => $aggregate];

        return ($transform) ? $this->formatItemAggregateDisplay($courseGrade['courseItem'], $courseGrade['aggregate'], $transformOnly) : $courseGrade;
    }


    /**
     * Populates the "must make" array by iterating through a course's "letter" boundaries and
     * determining what missing grade is necessary to achieve each boundary
     * 
     * @return void
     */
    private function calculateMustMake() {

        // get the sole missing item
        $missingItem = grade_item::fetch(['id' => $this->ungradedGradeItemKey]);

        if ($missingItem) {
            // first, determine if the user will outright pass all boundaries with a zero
            // if ($this->calculateTotalWithUngradedValue(0) >= $this->courseGradeBoundaryHigh) {
            //     // if so, return all checks as results
            //     $this->mustMakeArray = $this->createMustMakeArray($this->getSymbolCheckMark());
            //     return;
            // }

            // // then, determine if the user will get the lowest boundary with a 100
            // if ($this->calculateTotalWithUngradedValue(100) < $this->courseGradeBoundaryLow) {
            //     // if so, return all fails as results
            //     $this->mustMakeArray = $this->createMustMakeArray($this->getSymbolXMark());
            //     return;
            // }

            // then, loop through all boundaries calculating for each the grade necessary to get that boundary
            // iterate through the boundaries, starting at the lowest
            $boundaries = array_reverse(array_keys($this->letters));

            foreach ($boundaries as $boundary) {
                // find the passing grade value for this item and this boundary and add to results
                $mustMakeArray[$this->getMustMakeLetterId($boundary)] = $this->getPassingGradeItemValue($boundary, $missingItem->grademin, $missingItem->grademax);
            }

            // set must make array
            $this->mustMakeArray = $mustMakeArray;
        }
    }

    /**
     * Returns the minimum passing grade value necessary (within given bounds) to achieve to given minimum grade value threshold,
     * or notification of an outright pass or fail (in the form of html symbols)

     * @param  int  $minimumGradeValueBoundary  minimum grade value for a letter
     * @param  int  $minValue   minimum grade value possible for this search attempt
     * @param  int  $maxValue   maximum grade value possible for this search attempt
     * @return string
     */
    private function getPassingGradeItemValue($minimumGradeValueBoundary, $minValue, $maxValue) {

        // first, determine if the user will outright meet the minimum with the minimum value possible
        if ($this->calculateTotalWithUngradedValue($minValue) >= $minimumGradeValueBoundary) {
            // return pass symbol
            return $this->getSymbolCheckMark();
        } elseif ($this->calculateTotalWithUngradedValue($maxValue) < $minimumGradeValueBoundary) {
            // return fail symbol
            return $this->getSymbolXMark();
        }

        // if not, try a binary search attempt and return result
        $left = $minValue;
        $right = $maxValue;

        while ($left <= $right) {
            $attempt = floor(($left + $right)/2);

            $calc = $this->calculateTotalWithUngradedValue($attempt);
            
            if ($calc == $minimumGradeValueBoundary) {
                // once we find a good result, decrement the attempt value until we hit the floor
                while ($this->calculateTotalWithUngradedValue($attempt - 1) >= $minimumGradeValueBoundary) {
                    $attempt--;
                }

                return $attempt;
            } elseif ($calc > $minimumGradeValueBoundary) {
                $right = $attempt - 1;
            } elseif ($calc < $minimumGradeValueBoundary) {
                $left = $attempt + 1;
            }
        }

        // return fail notification
        return $this->getSymbolXMark();
    }

    /**
     * Helper function for rendering a check mark
     * 
     * @return string
     */
    private function getSymbolCheckMark() {
        return '&#x2713;';
    }

    /**
     * Helper function for rendering an "X" mark
     * 
     * @return string
     */
    private function getSymbolXMark() {
        return '&#10005;';
    }

    /**
     * Returns a calculated course whole number total given a value to add as it's missing item input
     * 
     * @param  int $gradeItemValue  grade value for its course's missing item
     * @return int  calculated course total as whole number rounded down
     */
    private function calculateTotalWithUngradedValue($gradeItemValue) {
        // clear the calculated aggregate cache
        $this->itemAggregates = [];

        // add the grade value to the stored grade input index
        $this->inputData['grades'][$this->ungradedGradeItemKey] = $gradeItemValue;
            
        // recalculate all categories
        // @TODO: for optimization, only calculate necessary (affected) categories!!!
        $this->getTransformedCategoryGrades();

        // transform value into a whole number
        $calculatedTotal = $this->getTransformedCourseGrade(true, 'percentage-value');

        return $calculatedTotal;
    }

    /**
     * Returns a "must make" array with all boundary levels containing the same value
     * 
     * @param  string $value
     * @return array
     */
    private function createMustMakeArray($value) {
        $mustMakeArray = [];

        // iterate through the boundaries, starting at the lowest
        foreach ($this->letters as $boundary => $letter) {
            // include the transformed master course grade
            if ( ! empty($this->courseGradeData)) {
                $mustMakeArray[$this->getMustMakeLetterId($boundary)] = $value;
            }
        }

        return $mustMakeArray;
    }

    /**
     * Returns the HTML for the "must make" modal component
     * 
     * @return string
     */
    public function getMustMakeModal() {
        $mustMakeMarkup = '
            <div class="modal fade" tabindex="-1" role="dialog" id="mustMakeModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">' . get_string('must_make_modal_heading', 'gradereport_forecast') . '</h4>
                        </div>';

                    $mustMakeMarkup .= $this->getMustMakeModalTable();

                    $mustMakeMarkup .= '
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-lg btn-block" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>';

        return $mustMakeMarkup;
    }

    /**
     * Returns an HTML table of letters and bounds to be embedded into the "must make" modal
     * 
     * @return string
     */
    private function getMustMakeModalTable() {
        $modalTable = '
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th></th>
                        <th>' . get_string('must_make_modal_letter_column_heading', 'gradereport_forecast') . '</th>
                        <th>' . get_string('must_make_modal_grade_column_heading', 'gradereport_forecast') . '</th>
                        <th></th>
                    </tr>
                </thead>';

        foreach ($this->letters as $boundary => $letter) {
            $modalTable .= '
                <tbody>
                    <tr>
                        <td width="10%"></td>
                        <td width="40%">' . $letter . '</td>
                        <td width="40%" id="' . $this->getMustMakeLetterId($boundary) . '"></td>
                        <td width="10%"></td>
                    </tr>
                </tbody>';
        }

        $modalTable .= '</table>';

        return $modalTable;
    }

    /**
     * Converts a letter "boundary" number to an id tag
     * 
     * @param  string $boundary
     * @return string
     */
    private function getMustMakeLetterId($boundary) {
        return $this->getMustMakeLetterIdPrefix() . str_replace('.', '-', $boundary);
    }

    /**
     * Checks whether "must make" modal should be displayed based off of grade input
     * 
     * @return bool
     */
    private function shouldShowMustMake() {
        if ( ! (array_key_exists('totalUngradedItemCount', $this->inputData) and array_key_exists('inputGradeItemCount', $this->inputData))) {
            return false;
        }

        return (((int)$this->inputData['totalUngradedItemCount'] - (int)$this->inputData['inputGradeItemCount']) == 1) ? true : false;
    }

    /**
     * Helper function for retrieving a specific grade_tree "level" item
     * 
     * @param  array  $levelItem  a gtree "level" item
     * @return array  $element  a gtree "element"
     */
    private function getLevelItemElement($levelItem) {
        $element = $this->gtree->locate_element($levelItem['eid']);

        return $element;
    }

    /**
     * Helper function for retrieving the "object" from a given element
     * 
     * @param  array  $element  a gtree "element"
     * @return object (grade_category)
     */
    private function getElementObject($element) {
        if ( ! array_key_exists('object', $element)) {
            return false;
        }

        return $element['object'];
    }

    /**
     * Reports whether or not this grade category is the main course-level category
     * 
     * @param  grade_category  $category
     * @return boolean
     */
    private function isCourseCategory($category) {
        return (is_null($category->parent)) ? true : false;
    }

    /**
     * Reports whether or not this grade item id already exists in the itemAggregates array
     * 
     * @param  int  $itemId
     * @return boolean
     */
    private function itemIdAlreadyAggregated($itemId) {
        return array_key_exists($itemId, $this->itemAggregates);
    }

    /**
     * Helper function for retrieving the "object" from a given element
     * 
     * @param  array  $element  a gtree "element"
     * @param  array  $types  a list of types of element children to be included (item|courseitem|category|categoryitem)
     * @param  boolean  $itemsOnly  if true, will add only children "grade_item" object to results
     * @return array  child object id => object
     */
    private function getElementChildren($element, $types = array(), $itemsOnly = true) {
        if ( ! array_key_exists('children', $element)) {
            return false;
        }

        $elementChildren = $element['children'];

        $results = [];

        // iterate through all children
        foreach ($elementChildren as $key => $child) {
            // add each wanted type to results array as: object id => object
            if (in_array($child['type'], $types)) {
                $childObject = $child['object'];                


                if ($child['type'] =='category' and $itemsOnly) {
                    // get this category's grade_item
                    $gradeItem = $this->getGradeItemFromCategory($childObject);

                    $results[$gradeItem->id] = $gradeItem;
                } else {
                    $results[$childObject->id] = $childObject;
                }
            }
        }

        return $results;
    }

    /**
     * Returns a given grade_category's grade_item object of a specified type
     *
     * @param  grade_category  $gradeCategory
     * @param  string  $itemType  category|course
     * @return grade_item
     */
    private function getGradeItemFromCategory($gradeCategory, $itemType = 'category') {
        $gradeItem = grade_item::fetch([
            'itemtype' => $itemType,
            'iteminstance' => $gradeCategory->id,
        ]);

        if ( ! $gradeItem or ! property_exists($gradeItem, 'id')) {
            return false;
        }

        return $gradeItem;
    }

    /**
     * Returns an array of grade_item grade values by reconciling calculated category values, input data, 
     * and this user's actual grades, and then applying a given parent grade_category's rules
     *
     * Sets ungraded/uninput item grades to zero (default), or optionally removes them from the given grade_items
     * 
     * @param  grade_category  $gradeCategory
     * @param  array  $gradeItems
     * @param  bool  $removeUngradedItems  whether or not to remove an ungraded, uninput grade item from the given list of grade_items
     * @return array  (as: grade_item id => grade value)
     */
    private function getCategoryGradeItemValuesArray($gradeCategory, &$gradeItems, $removeUngradedItems) {
        $values = [];

        foreach ($gradeItems as $gradeItemId => $gradeItem) {
            // if this is a category, try to get the value from the master array, otherwise, give it a zero and remove
            if ($gradeItem->itemtype == 'category') {
                if ( ! array_key_exists($gradeItemId, $this->itemAggregates)) {
                    // remove grade, or set to zero depending on selected option
                    if ($removeUngradedItems) {
                        // remove the item from the item container
                        unset($gradeItems[$gradeItemId]);
                    } else {
                        // set this items grade to zero
                        $values[$gradeItemId] = 0;   
                    }
                } else {
                    // otherwise, include the grade in the grade value container
                    $values[$gradeItemId] = $this->itemAggregates[$gradeItemId]['calculatedValue'];
                }
            
            // otherwise, this is an item, if a "forecasted" grade has been input for this item, include it in the container
            } elseif (array_key_exists($gradeItemId, $this->inputData['grades'])) {
                $values[$gradeItemId] = $this->inputData['grades'][$gradeItemId];
            
            // otherwise, try to get a real grade for this grade_item for this user
            } else {
                $grade = $gradeItem->get_grade($this->user->id);

                if ($grade->is_excluded()) {
                    // remove the item from the item container
                    unset($gradeItems[$gradeItemId]);
                } else if (is_null($grade->finalgrade)) {
                    // cache this missing (ungraded) grade item key
                    $this->ungradedGradeItemKey = $gradeItemId;

                    // remove grade, or set to zero depending on selected option
                    if ($removeUngradedItems) {
                        // remove the item from the item container
                        unset($gradeItems[$gradeItemId]);
                    } else {
                        // set this items grade to zero
                        $values[$gradeItemId] = 0;   
                    }
                } else {
                    // otherwise, include the grade in the grade value container
                    $values[$gradeItemId] = $grade->finalgrade;
                }
            }
        }

        // apply any special category rules (drop lowest/highest) to the remaining list of values
        $gradeCategory->apply_limit_rules($values, $gradeItems);

        return $values;
    }

    /**
     * Returns a calculated grade_category aggregate given grade_items and their corresponding values to consider
     * 
     * @param  grade_category  $gradeCategory
     * @param  array  $gradeItems  grade_item_id => grade_item
     * @param  array  $gradeItemValues  grade_item_id => value
     * @param  bool  $normalizeValues  whether or not to "normalize" grade values before calculating
     * @return decimal  (from 0.0000 to 1.0000)
     */
    private function getCategoryGradeAggregate($gradeCategory, $gradeItems, $gradeItemValues, $normalizeValues = true) {
        $gradeItemValues = ($normalizeValues) ? $this->normalizeGradeValues($gradeItems, $gradeItemValues) : $gradeItemValues;

        $aggregate = $gradeCategory->aggregate_values_and_adjust_bounds($gradeItemValues, $gradeItems);

        return ( ! is_null($aggregate)) ? $aggregate : false;
    }
    /**
     * Returns an array of normalized grade values by referencing their corresponding grade_item max values
     * 
     * @param  array  $gradeItems  grade_item_id => grade_item
     * @param  array  $gradeValues  grade_item_id => value
     * @return array
     */
    private function normalizeGradeValues($gradeItems , $gradeValues) {
        $normalizedValues = [];
        
        foreach ($gradeValues as $id => $value) {
            // if this grade item is using a scale
            if ($this->isScaleItem($gradeItems[$id])) {
                if ($gradeItems[$id]->get_parent_category()->aggregation == GRADE_AGGREGATE_SUM) {
                    $normalizedValues[$id] = $value / $gradeItems[$id]->grademax;
                } else {
                    if ($value > 1) {
                        $normalizedValues[$id] = $value / $gradeItems[$id]->grademax;
                    } else {
                        $normalizedValues[$id] = 0;
                    }
                }
            } else {
                // normalize using the item's max & min
                $normalizedValues[$id] = $value / ($gradeItems[$id]->grademax - $gradeItems[$id]->grademin);
            }

        }
        
        return $normalizedValues;
    }

    private function isScaleItem($gradeItem) {
        return  $gradeItem->gradetype == GRADE_TYPE_SCALE;
    }

    /**
     * Stores an aggregate array for a given grade_item id
     * 
     * @param  int  $itemId  a grade_item id
     * @param  array  $aggregate [grade|grademin|grademax]
     * @return void
     */
    private function storeItemAggregate($itemId, $aggregate) {
        $this->itemAggregates[$itemId] = [
            'grade' => $aggregate['grade'],
            'grademin' => $aggregate['grademin'],
            'grademax' => $aggregate['grademax'],
            'calculatedValue' => ($aggregate['grade'] * $aggregate['grademax'])
        ];
    }

    /**
     * Returns a formatted display of a given grade item and aggregate
     * 
     * @param  grade_item  $gradeItem
     * @param  array  $aggregate [grade|grademin|grademax]
     * @param  string  $transformOnly  if set, return only this value
     * @return string
     */
    private function formatItemAggregateDisplay($gradeItem, $aggregate, $transformOnly = '') {
        $value = $aggregate['grade'];

        if ($transformOnly == 'percentage-value') {
            return floor($value * 100);
        }

        // get decimal display config
        $decimalPlaces = $gradeItem->get_decimals();

        // calculate the total "points" for this category
        $points = $this->formatNumber($value * $gradeItem->grademax, $decimalPlaces);

        // show total (points) by default
        $output = $points . ' pts';

        // optionally show percentage
        if ($this->showgradepercentage) {
            $percentage = $this->formatPercentage($value * 100, $decimalPlaces);
            $output .= '  |  ' . $percentage;
        }

        // optionally show letter grade
        if ($this->showlettergrade) {
            $letter = $this->formatLetter($value);
            $output .= '  |  ' . $letter;
        }

        return $output;
    }

    /**
    * Helper for rounding a number to the configured amount of decimal places
    * 
    * @param  mixed  $value
    * @return decimal
    */
    private function formatNumber($value, $decimals) {
        return number_format($value, $decimals);
    }

    /**
     * Helper for displaying a letter grade given a specific value
     *
     * Note: This is from Moodle core (minus value bounds)
     * 
     * @param  mixed  $value
     * @return string
     */
    private function formatLetter($value) {
        global $CFG;
        
        // check for legacy stuff...
        $gradebookcalculationsfreeze = 'gradebook_calculations_freeze_' . $this->courseid;

        // find and return the proper letter grade
        foreach ($this->letters as $boundary => $letter) {
            if ( ! (property_exists($CFG, $gradebookcalculationsfreeze) && (int)$CFG->{$gradebookcalculationsfreeze} <= 20160518)) {
                // The boundary is a percentage out of 100 so use 0 as the min and 100 as the max.
                $boundary = grade_grade::standardise_score($boundary, 0, 100, 0, 100);
            }

            if ($value * 100 >= $boundary) {
                return format_string($letter);
            }
        }
        
        // default letter
        return '-';
    }

    /**
     * Helper for displaying a percentage to the configured amount of decimal places
     * 
     * @param  mixed $value
     * @return string
     */
    private function formatPercentage($value, $decimals) {
        return $this->formatNumber($value, $decimals) . '%';
    }

    /**
     * Converts raw POST data received by the report into a formatted structure to be used
     * 
     * Returns an array of: 1) actual grade item inputs ('grades') in form: (grade_item_id as int) => (input value as string),
     * 2) a 'totalUngradedItemCount' representing the total number of "hard-ungraded" grade items, and 3) a count of the grade
     * inputs given
     * 
     * @return array
     */
    private function formatRawInputData($data) {
        $grades = [];
        $totalUngradedItemCount = 0;

        // iterate through each POSTed form element
        foreach ($data as $key => $value) {
            // if this is a legitimate grade item input element
            if (strpos($key, $this->getGradeItemInputPrefix()) == 0) {
                // increment the total available grade item count
                $totalUngradedItemCount++;

                // if a value has been input, format and add to results array
                if ($value != '') {
                    $grades[str_replace($this->getGradeItemInputPrefix(), '', $key)] = $value;
                }
            }
        }

        $formattedInputData = [
            'grades' => $grades,
            'totalUngradedItemCount' => $totalUngradedItemCount,
            'inputGradeItemCount' => count($grades)
        ];

        return $formattedInputData;
    }

    /**
     * Helper function for returning the grade item prefix key
     * 
     * @return string
     */
    private function getGradeItemInputPrefix() {
        return 'input-gradeitem-';
    }

    /**
     * Helper function for returning the "forecast category" item prefix key
     * 
     * @return string
     */
    private function getForecastCategoryItemPrefix() {
        return 'fcst-cat-';
    }

    /**
     * Helper function for returning the "forecast course" item prefix key
     * 
     * @return string
     */
    private function getForecastCourseItemPrefix() {
        return 'fcst-course-';
    }

    /**
     * Helper function for returning the "must make" letter id prefix (for modal table)
     * 
     * @return string
     */
    private function getMustMakeLetterIdPrefix() {
        return 'must-make-letter-id-';
    }

    /**
     * Formats a given grade value and letter for display in grade column of report
     * 
     * @param  string      $gradeValue
     * @param  grade_item  $gradeItem
     * @return string
     */
    private function formatGradeDisplay($gradeValue, $gradeItem) {
        if (is_null($gradeValue)) {
            return '-';
        }

        $output = grade_format_gradevalue($gradeValue, $gradeItem, true, GRADE_DISPLAY_TYPE_REAL, null);

        if ($this->showgradepercentage) {
            $output .= '  (' . grade_format_gradevalue($gradeValue, $gradeItem, true, GRADE_DISPLAY_TYPE_PERCENTAGE, null) . ')';
        }

        if ($this->showlettergrade) {
            $output .= '  (' . grade_format_gradevalue($gradeValue, $gradeItem, true, GRADE_DISPLAY_TYPE_LETTER, null) . ')';
        }

        return $output;
    }

    ///////////////////////////////////////
    ///                                 ///
    ///   BEGIN MOODLE REPORT METHODS   ///
    ///                                 ///
    ///////////////////////////////////////

    /**
     * Processes the data sent by the form (grades and feedbacks).
     * @var array $data
     * @return bool Success or Failure (array of errors).
     */
    function process_data($data) {
    }
    function process_action($target, $action) {
    }

    /**
     * Trigger the grade_report_viewed event
     *
     * @since Moodle 2.9
     */
    public function viewed() {
        $event = \gradereport_forecast\event\grade_report_viewed::create(
            array(
                'context' => $this->context,
                'courseid' => $this->courseid,
                'relateduserid' => $this->user->id,
            )
        );
        $event->trigger();
    }

    /**
     * Recurses through a tree of elements setting the rowspan property on each element
     *
     * @param array $element Either the top element or, during recursion, the current element
     * @return int The number of elements processed
     */
    function inject_rowspans(&$element) {

        if ($element['depth'] > $this->maxdepth) {
            $this->maxdepth = $element['depth'];
        }
        if (empty($element['children'])) {
            return 1;
        }
        $count = 1;

        foreach ($element['children'] as $key=>$child) {
            // If category is hidden then do not include it in the rowspan.
            if ($child['type'] == 'category' && $child['object']->is_hidden() && !$this->canviewhidden
                    && ($this->showhiddenitems == GRADE_REPORT_FORECAST_HIDE_HIDDEN
                    || ($this->showhiddenitems == GRADE_REPORT_FORECAST_HIDE_UNTIL && !$child['object']->is_hiddenuntil()))) {
                // Just calculate the rowspans for children of this category, don't add them to the count.
                $this->inject_rowspans($element['children'][$key]);
            } else {
                $count += $this->inject_rowspans($element['children'][$key]);
            }
        }

        $element['rowspan'] = $count;
        return $count;
    }

    /**
     * Prepares the headers and attributes of the flexitable.
     */
    public function setup_table() {
        // setting up table headers
        $this->tablecolumns = [
            'itemname', 
            'grade'
        ];
        
        $this->tableheaders = [
            // $this->get_lang_string('gradeitem', 'grades'), 
            // $this->get_lang_string('grade', 'grades')
            get_string('gradeitem', 'gradereport_forecast'), 
            get_string('grade', 'gradereport_forecast')
        ];
    }

    function fill_table() {
        // print "<pre>";
        // print_r($this->gtree->top_element);
        $this->fill_table_recursive($this->gtree->top_element);
        // print_r($this->tabledata);
        // print "</pre>";
        return true;
    }

    /**
     * Fill the table with data.
     *
     * @param $element - An array containing the table data for the current row.
     */
    private function fill_table_recursive(&$element) {
        global $DB, $CFG;

        $type = $element['type'];
        $depth = $element['depth'];
        $grade_object = $element['object'];
        $eid = $grade_object->id;
        $element['userid'] = $this->user->id;

        // $fullname = $this->gtree->get_element_header($element, true, true, true, true, true);
        $fullname = grade_helper::get_element_header($element, true, true, true, true, true);
        $data = array();
        $hidden = '';
        $excluded = '';
        $itemlevel = ($type == 'categoryitem' || $type == 'category' || $type == 'courseitem') ? $depth : ($depth + 1);
        $class = 'level' . $itemlevel . ' level' . ($itemlevel % 2 ? 'odd' : 'even');

        // If this is a hidden grade category, hide it completely from the user
        if ($type == 'category' && $grade_object->is_hidden() && !$this->canviewhidden && (
                $this->showhiddenitems == GRADE_REPORT_FORECAST_HIDE_HIDDEN ||
                ($this->showhiddenitems == GRADE_REPORT_FORECAST_HIDE_UNTIL && !$grade_object->is_hiddenuntil()))) {
            return false;
        }

        if ($type == 'category') {
            $this->evenodd[$depth] = (($this->evenodd[$depth] + 1) % 2);
        }
        $alter = ($this->evenodd[$depth] == 0) ? 'even' : 'odd';

        /// Process those items that have scores associated
        if ($type == 'item' or $type == 'categoryitem' or $type == 'courseitem') {
            $header_row = "row_{$eid}_{$this->user->id}";
            $header_cat = "cat_{$grade_object->categoryid}_{$this->user->id}";

            $grade_grade = grade_grade::fetch(array('itemid'=>$grade_object->id,'userid'=>$this->user->id));

            if ( ! $grade_grade) {
                $grade_grade = new grade_grade([
                    'userid' => $this->user->id,
                    'itemid' => $grade_object->id,
                ], false);
            }

            $grade_grade->load_grade_item();

            /// Hidden Items
            if ($grade_grade->grade_item->is_hidden()) {
                $hidden = ' dimmed_text';
            }

            $hide = false;
            // If this is a hidden grade item, hide it completely from the user.
            if ($grade_grade->is_hidden() && !$this->canviewhidden && (
                    $this->showhiddenitems == GRADE_REPORT_FORECAST_HIDE_HIDDEN ||
                    ($this->showhiddenitems == GRADE_REPORT_FORECAST_HIDE_UNTIL && !$grade_grade->is_hiddenuntil()))) {
                $hide = true;
            } else if (!empty($grade_object->itemmodule) && !empty($grade_object->iteminstance)) {
                // The grade object can be marked visible but still be hidden if
                // the student cannot see the activity due to conditional access
                // and it's set to be hidden entirely.
                $instances = $this->modinfo->get_instances_of($grade_object->itemmodule);
                if (!empty($instances[$grade_object->iteminstance])) {
                    $cm = $instances[$grade_object->iteminstance];
                    if (!$cm->uservisible) {
                        // If there is 'availableinfo' text then it is only greyed
                        // out and not entirely hidden.
                        if (!$cm->availableinfo) {
                            $hide = true;
                        }
                    }
                }
            }

            // Actual Grade - We need to calculate this whether the row is hidden or not.
            $gradeval = $grade_grade->finalgrade;
            $hint = $grade_grade->get_aggregation_hint();
            if (!$this->canviewhidden) {
                /// Virtual Grade (may be calculated excluding hidden items etc).
                $adjustedgrade = $this->blank_hidden_total_and_adjust_bounds($this->courseid,
                                                                             $grade_grade->grade_item,
                                                                             $gradeval);

                $gradeval = $adjustedgrade['grade'];

                // We temporarily adjust the view of this grade item - because the min and
                // max are affected by the hidden values in the aggregation.
                $grade_grade->grade_item->grademax = $adjustedgrade['grademax'];
                $grade_grade->grade_item->grademin = $adjustedgrade['grademin'];
                $hint['status'] = $adjustedgrade['aggregationstatus'];
                $hint['weight'] = $adjustedgrade['aggregationweight'];
            } else {
                // The max and min for an aggregation may be different to the grade_item.
                if (!is_null($gradeval)) {
                    $grade_grade->grade_item->grademax = $grade_grade->get_grade_max();
                    $grade_grade->grade_item->grademin = $grade_grade->get_grade_min();
                }
            }


            if (!$hide) {
                /// Excluded Item
                $isExcludedGrade = false;
                if ($grade_grade->is_excluded()) {
                    $fullname .= ' ['.get_string('excluded', 'grades').']';
                    $excluded = ' excluded';
                    $isExcludedGrade = true;
                }

                /// Other class information
                $class .= $hidden . $excluded;
                if ($this->switch) { // alter style based on whether aggregation is first or last
                   $class .= ($type == 'categoryitem' or $type == 'courseitem') ? " ".$alter."d$depth baggt b2b" : " item b1b";
                } else {
                   $class .= ($type == 'categoryitem' or $type == 'courseitem') ? " ".$alter."d$depth baggb" : " item b1b";
                }
                if ($type == 'categoryitem' or $type == 'courseitem') {
                    $header_cat = "cat_{$grade_object->iteminstance}_{$this->user->id}";
                }

                /// Name
                $data['itemname']['content'] = $fullname;
                $data['itemname']['class'] = $class;
                $data['itemname']['colspan'] = ($this->maxdepth - $depth);
                $data['itemname']['celltype'] = 'th';
                $data['itemname']['id'] = $header_row;

                $class .= " itemcenter ";
                $placeholder = '';
                $inputName = '';
                $isScaleItem = false;
                $itemScaleArray = [];

                // get grade and value
                        // $gradeValue = grade_format_gradevalue($gradeval, $grade_grade->grade_item, true);
                        // $gradeLetter = grade_format_gradevalue($gradeval, $grade_grade->grade_item, true, GRADE_DISPLAY_TYPE_LETTER);
                
                // determine what type of grade item this is and apply the proper "fcst" class
                if ($type == 'item') {
                    // mark static/dynamic depending on whether there is a grade or not
                    $class .= ' fcst-' . (( ! is_null($gradeval)) ? 'static' : 'dynamic' ) . '-item-' . $eid . ' ';
                    $class .= ' grade-max-' . $grade_grade->grade_item->grademax;
                    $class .= ' grade-min-' . $grade_grade->grade_item->grademin;

                    $inputName = $this->getGradeItemInputPrefix() . $eid;
                    
                    // check if this grade item is using a scale
                    $isScaleItem = $this->isScaleItem($grade_grade->grade_item);

                    if ($isScaleItem) {
                        $itemScaleArray = $grade_grade->grade_item->load_scale()->scale_items;
                    }

                    $placeholder = $isScaleItem ? '' : $grade_grade->grade_item->grademin . ' - ' . $grade_grade->grade_item->grademax;

                    $class .= $isScaleItem ? ' fcst-dynamic-scale-item is-scale' : '';
                    
                } elseif ($type == 'categoryitem') {
                    $class .= ' ' . $this->getForecastCategoryItemPrefix() . $eid . ' ';
                } elseif ($type == 'courseitem') {
                    $class .= ' ' . $this->getForecastCourseItemPrefix() . $eid . ' ';
                }

                // Grade and Letter display
                if ($grade_grade->grade_item->needsupdate) {
                    $data['grade']['class'] = $class.' gradingerror';
                    $data['grade']['content'] = get_string('error');
                } else if (!empty($CFG->grade_hiddenasdate) and $grade_grade->get_datesubmitted() and !$this->canviewhidden and $grade_grade->is_hidden()
                       and !$grade_grade->grade_item->is_category_item() and !$grade_grade->grade_item->is_course_item()) {
                    // the problem here is that we do not have the time when grade value was modified, 'timemodified' is general modification date for grade_grades records
                    $class .= ' datesubmitted';
                    $data['grade']['class'] = $class;
                    $data['grade']['content'] = get_string('submittedon', 'grades', userdate($grade_grade->get_datesubmitted(), get_string('strftimedatetimeshort')));

                } else if ($grade_grade->is_hidden()) {
                    $data['grade']['class'] = $class.' dimmed_text';
                    $data['grade']['content'] = '-';
                    if ($this->canviewhidden) {
                        $data['grade']['content'] = $this->formatGradeDisplay($gradeval, $grade_grade->grade_item);
                        $data['grade']['placeholder'] = $placeholder;
                        $data['grade']['inputName'] = $inputName;
                    }
                } else {
                    $data['grade']['class'] = $class;
                    $data['grade']['content'] = $this->formatGradeDisplay($gradeval, $grade_grade->grade_item);
                    $data['grade']['placeholder'] = $placeholder;
                    $data['grade']['inputName'] = $inputName;
                }
                $data['grade']['headers'] = "$header_cat $header_row grade";
                $data['grade']['isScaleItem'] = $isScaleItem;
                $data['grade']['itemScaleArray'] = $itemScaleArray;
                $data['grade']['isExcludedGrade'] = $isExcludedGrade;
            }
        }

        /// Category
        if ($type == 'category') {
            $data['leader']['class'] = $class.' '.$alter."d$depth b1t b2b b1l";
            $data['leader']['rowspan'] = $element['rowspan'];

            if ($this->switch) { // alter style based on whether aggregation is first or last
               $data['itemname']['class'] = $class.' '.$alter."d$depth b1b b1t";
            } else {
               $data['itemname']['class'] = $class.' '.$alter."d$depth b2t";
            }
            $data['itemname']['colspan'] = ($this->maxdepth - $depth + count($this->tablecolumns) - 1);
            $data['itemname']['content'] = $fullname;
            $data['itemname']['celltype'] = 'th';
            $data['itemname']['id'] = "cat_{$grade_object->id}_{$this->user->id}";
        }

        /// Add this row to the overall system
        foreach ($data as $key => $celldata) {
            $data[$key]['class'] .= ' column-' . $key;
        }
        $this->tabledata[] = $data;

        /// Recursively iterate through all child elements
        if (isset($element['children'])) {
            foreach ($element['children'] as $key=>$child) {
                $this->fill_table_recursive($element['children'][$key]);
            }
        }
    }

    /**
     * Prints or returns the HTML from the flexitable.
     * @param bool $return Whether or not to return the data instead of printing it directly.
     * @return string
     */
    public function print_table($return=false) {
         $maxspan = $this->maxdepth;

        /// Build table structure
        $html = "
            <form id='forecast-form' action='#'>
            <input type='hidden' name='id' value='" . $this->courseid . "'>
            <input type='hidden' name='courseid' value='" . $this->courseid . "'>
            <input type='hidden' name='userid' value='" . $this->user->id . "'>
            <table cellspacing='0'
                   cellpadding='0'
                   summary='" . s(get_string('tablesummary', 'gradereport_forecast')) . "'
                   class='boxaligncenter generaltable user-grade'>
            <thead>
                <tr>
                    <th id='".$this->tablecolumns[0]."' class=\"header column-{$this->tablecolumns[0]}\" colspan='$maxspan'>".$this->tableheaders[0]."</th>\n";

        for ($i = 1; $i < count($this->tableheaders); $i++) {
            $html .= "<th id='".$this->tablecolumns[$i]."' class=\"header column-{$this->tablecolumns[$i]}\">".$this->tableheaders[$i]."</th>\n";
        }

        $html .= "
                </tr>
            </thead>
            <tbody>\n";

        /// Print out the table data
        for ($i = 0; $i < count($this->tabledata); $i++) {
            $html .= "<tr>\n";
            if (isset($this->tabledata[$i]['leader'])) {
                $rowspan = $this->tabledata[$i]['leader']['rowspan'];
                $class = $this->tabledata[$i]['leader']['class'];
                $html .= "<td class='$class' rowspan='$rowspan'></td>\n";
            }
            for ($j = 0; $j < count($this->tablecolumns); $j++) {
                $name = $this->tablecolumns[$j];
                $class = (isset($this->tabledata[$i][$name]['class'])) ? $this->tabledata[$i][$name]['class'] : '';
                $colspan = (isset($this->tabledata[$i][$name]['colspan'])) ? "colspan='".$this->tabledata[$i][$name]['colspan']."'" : '';
                
                // $content = (isset($this->tabledata[$i][$name]['content'])) ? $this->tabledata[$i][$name]['content'] : null;

                if ( ! isset($this->tabledata[$i][$name]['content'])) {
                    $content = null;
                } else {
                    if ($this->tabledata[$i][$name]['content'] == '-') {
                        if (strpos($this->tabledata[$i][$name]['class'], ' item ')) {
                            $placeholder = isset($this->tabledata[$i][$name]['placeholder']) ? $this->tabledata[$i][$name]['placeholder'] : 'Enter grade';
                            $inputName = isset($this->tabledata[$i][$name]['inputName']) ? $this->tabledata[$i][$name]['inputName'] : 'default-input-gradeitem';
                            
                            if ($this->tabledata[$i][$name]['isScaleItem']) {
                                $content = '<select name="' . $inputName . '">';
                                
                                $content .= '<option value="">Select</option>';

                                foreach ($this->tabledata[$i][$name]['itemScaleArray'] as $optionid => $optionname) {
                                    $content .= '<option value="' . ( (int) $optionid + 1 ) . '">' . ucfirst($optionname) . '</option>';
                                }

                                $content .= '</select>';
                            } else {
                                $disabled = $this->tabledata[$i][$name]['isExcludedGrade'] ? ' disabled' : '';

                                $content = '<input type="text" name="' . $inputName . '" placeholder="' . $placeholder . '"' . $disabled . '><br>
                                        <span class="fcst-error fcst-error-invalid" style="display: none; color: red;">Invalid input!</span>
                                        <span class="fcst-error fcst-error-range" style="display: none; color: red;">Must be within range!</span>';
                            }
                        } else {
                            $content = '--';
                        }
                    } else {
                        $content = $this->tabledata[$i][$name]['content'];
                    }
                }

                $celltype = (isset($this->tabledata[$i][$name]['celltype'])) ? $this->tabledata[$i][$name]['celltype'] : 'td';
                $id = (isset($this->tabledata[$i][$name]['id'])) ? "id='{$this->tabledata[$i][$name]['id']}'" : '';
                $headers = (isset($this->tabledata[$i][$name]['headers'])) ? "headers='{$this->tabledata[$i][$name]['headers']}'" : '';
                if (isset($content)) {
                    $html .= "<$celltype $id $headers class='$class' $colspan>$content</$celltype>\n";
                }
            }
            $html .= "</tr>\n";
        }

        $html .= "</tbody></table></form>";

        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }

}

// necessary for grade report...

function grade_report_forecast_settings_definition(&$mform) {
    global $CFG;

    $options = array(-1 => get_string('default', 'grades'),
                      0 => get_string('hide'),
                      1 => get_string('show'));

    if (empty($CFG->grade_report_forecast_showlettergrade)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    if (empty($CFG->grade_report_forecast_showgradepercentage)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $options = array(0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5);
    if (! empty($CFG->grade_report_forecast_rangedecimals)) {
        $options[-1] = $options[$CFG->grade_report_forecast_rangedecimals];
    }
    $mform->addElement('select', 'report_forecast_rangedecimals', get_string('rangedecimals', 'grades'), $options);

    $options = array(-1 => get_string('default', 'grades'),
                      0 => get_string('shownohidden', 'grades'),
                      1 => get_string('showhiddenuntilonly', 'grades'),
                      2 => get_string('showallhidden', 'grades'));

    if (empty($CFG->grade_report_forecast_showhiddenitems)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[$CFG->grade_report_forecast_showhiddenitems]);
    }

    $mform->addElement('select', 'report_forecast_showhiddenitems', get_string('showhiddenitems', 'grades'), $options);
    $mform->addHelpButton('report_forecast_showhiddenitems', 'showhiddenitems', 'grades');

    //showtotalsifcontainhidden
    $options = array(-1 => get_string('default', 'grades'),
                      GRADE_REPORT_HIDE_TOTAL_IF_CONTAINS_HIDDEN => get_string('hide'),
                      GRADE_REPORT_SHOW_TOTAL_IF_CONTAINS_HIDDEN => get_string('hidetotalshowexhiddenitems', 'grades'),
                      GRADE_REPORT_SHOW_REAL_TOTAL_IF_CONTAINS_HIDDEN => get_string('hidetotalshowinchiddenitems', 'grades') );

    if (empty($CFG->grade_report_forecast_showtotalsifcontainhidden)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[$CFG->grade_report_forecast_showtotalsifcontainhidden]);
    }

    $mform->addElement('select', 'report_forecast_showtotalsifcontainhidden', get_string('hidetotalifhiddenitems', 'grades'), $options);
    $mform->addHelpButton('report_forecast_showtotalsifcontainhidden', 'hidetotalifhiddenitems', 'grades');

    $options = array(-1 => get_string('default', 'grades'),
                      0 => get_string('no'),
                      1 => get_string('yes'));

    if (empty($CFG->grade_report_forecast_enabledforstudents)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_forecast_enabledforstudents', get_string('enabled_for_students', 'gradereport_forecast'), $options);

    $options = array(-1 => get_string('default', 'grades'),
                      0 => get_string('no'),
                      1 => get_string('yes'));

    if (empty($CFG->grade_report_forecast_mustmakeenabled)) {
        $options[-1] = get_string('defaultprev', 'grades', $options[0]);
    } else {
        $options[-1] = get_string('defaultprev', 'grades', $options[1]);
    }

    $mform->addElement('select', 'report_forecast_mustmakeenabled', get_string('must_make_enabled', 'gradereport_forecast'), $options);

    $mform->addElement('text', 'report_forecast_debouncewaittime', get_string('debounce_wait_time', 'gradereport_forecast'));
    // $mform->addHelpButton('report_forecast_debouncewaittime', 'debounce_wait_time_desc', 'gradereport_forecast');

    $mform->setDefault('report_forecast_debouncewaittime', '500');
    $mform->setType('report_forecast_debouncewaittime', PARAM_TEXT);
}

/**
 * Profile report callback.
 *
 * @param object $course The course.
 * @param object $user The user.
 * @param boolean $viewasuser True when we are viewing this as the targetted user sees it.
 */
function grade_report_forecast_profilereport($course, $user, $viewasuser = false) {
    global $OUTPUT;
    if (!empty($course->showgrades)) {

        $context = context_course::instance($course->id);

        // get tracking object
        $gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'forecast', 'courseid'=>$course->id, 'userid'=>$user->id));

        // create a report instance
        $report = new grade_report_forecast($course->id, $gpr, $context, $user->id, $viewasuser);

        // print the report
        echo '<div class="grade-report-forecast">';
        if ($report->fill_table()) {
            echo $report->print_table(true);
        }
        echo '</div>';
    }
}

/**
 * Add nodes to myprofile page.
 *
 * @param \core_user\output\myprofile\tree $tree Tree object
 * @param stdClass $user user object
 * @param bool $iscurrentuser
 * @param stdClass $course Course object
 */
function gradereport_forecast_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {}

function dd($thing) { var_dump($thing);die; }
