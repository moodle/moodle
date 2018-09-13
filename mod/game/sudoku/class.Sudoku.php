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

/*
 * Edit History:
 *
 *  Dick Munroe (munroe@csworks.com) 02-Nov-2005
 *      Initial version created.
 *
 *  Dick Munroe (munroe@csworks.com) 12-Nov-2005
 *      Allow initialzePuzzle to accept a file name in addition
 *      to a resource.  Windows doesn't do file redirection properly
 *      so the examples have to be able to handle a file NAME as
 *      input as well as a redirected file.
 *      Allow initializePuzzle to accept a string of 81 characters.
 *
 *  Dick Munroe (munroe@csworks.com) 13-Nov-2005
 *	It appears that getBoardAsString screws up somehow.  Rewrite it.
 *
 *  Dick Munroe (munroe@csworks.com) 16-Nov-2005
 *     Add a "pair" inference.
 *	Dick Munroe (munroe@csworks.com) 17-Nov-2005
 *		Add comments to input files.
 *		There was a bug in _applyTuple that caused premature exiting of the inference
 *		engine.
 *		If SDD isn't present, don't display error.
 *
 *	Dick Munroe (munroe@csworks.com) 19-Nov-2005
 *		Add a new tuple inference.
 *		Do a ground up ObjectS oriented redesign to make the addition of arbitrary
 *		inferences MUCH easier.
 *		Get the printing during solving right.
 *		Somehow array_equal developed a "bug".
 *
 *	Dick Munroe (munroe@csworks.com) 22-Nov-2005
 *		Add n,n+1 tuple recognition for n=2.
 *		Restructure inference engine to get maximum benefit out of each pass.
 *
 *	Dick Munroe (munroe@csworks.com) 28-Nov-2005
 *		Attempt to build harder Sudoku by implementing a coupling coefficient
 *		attempting to distribute clues more optimally.
 */

/**
 * This class is used by Sudoku game.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

@require_once("SDD/class.SDD.php");

/*
 * @author Dick Munroe <munroe@csworks.com>
 * @copyright copyright @ 2005 by Dick Munroe, Cottage Software Works, Inc.
 * @license http://www.csworks.com/publications/ModifiedNetBSD.html
 */

/**
 * Basic functionality needed for ObjectSs in the Sudoku solver.
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class objects {
    /**
     * Are two array's equal (have the same contents).
     *
     * @param array $thearray1
     * @param array $thearray2
     * @return boolean
     */
    public function array_equal($thearray1, $thearray2) {
        if (!(is_array($thearray1) && is_array($thearray2))) {
            return false;
        }

        if (count($thearray1) != count($thearray2)) {
            return false;
        }

        $xxx = array_diff($thearray1, $thearray2);

        return (count($xxx) == 0);
    }

    /**
     * Deep copy anything.
     *
     * @param array $thearray [optional] Something to be deep copied.  Default is the current
     *                        ObjectS.
     * @return mixed The deep copy of the input.  All references embedded within
     *               the array have been resolved into copies allowing things like the
     *               board array to be copied.
     */
    public function deepcopy($thearray = null) {
        if ($thearray === null) {
            return unserialize(serialize($this));
        } else {
            return unserialize(serialize($thearray));
        }
    }
}

/**
 * The individual cell on the Sudoku board.
 *
 * These cells aren't restricted to 9x9 Sudoku (although pretty much everything else
 * at the moment).  This class provides the state manipulation and searching capabilities
 * needed by the inference engine (class RCS).
 *
 * @package    mod_game
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cell extends objects {
    /** @var $r */
    protected $r;
    /** @var $c */
    protected $c;

    /** @var $state */
    protected $state = array();
    /** @var $applied */
    protected $applied = false;

    /**
     * Constructor
     *
     * @param integer $inpr row address of this cell (not used, primarily for debugging purposes).
     * @param integer $inpc column address of this cell (ditto).
     * @param integer $nstates The number of states each cell can have.  Looking forward to
     *                         implementing Super-doku.
     */
    public function init($inpr, $inpc, $nstates = 9) {
        $this->r = $inpr;
        $this->c = $inpc;

        for ($i = 1; $i <= $nstates; $i++) {
            $this->state[$i] = $i;
        }
    }

    /**
     * This cell has been "applied", i.e., solved, to the board.
     */
    public function applied() {
        $this->applied = true;
    }

    /**
     * Only those cells which are not subsets of the tuple have the
     * contents of the tuple removed.
     *
     * apply a 23Tuple to a cell.
     * @param array $atuple the tuple to be eliminated.
     */
    public function apply23tuple($atuple) {
        if (is_array($this->state)) {
            $xxx = array_intersect($this->state, $atuple);
            if ((count($xxx) > 0) && (count($xxx) != count($this->state))) {
                return $this->un_set($atuple);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * For more details on the pair tuple algorithm, see RCS::_pairSolution.
     *
     * Remove all values in the tuple, but only if the cell is a superset.
     * @param array $atuple to be eliminated from the cell's state.
     */
    public function applytuple($atuple) {
        if (is_array($this->state)) {
            if (!$this->array_equal($atuple, $this->state)) {
                return $this->un_set($atuple);
            }
        }

        return false;
    }

    /**
     * Return the string representation of the cell.
     *
     * @param boolean $theflag true if the intermediate states of the cell are to be visible.
     *
     * @return string
     */
    public function asstring($theflag = false) {
        if (is_array($this->state)) {
            if (($theflag) || (count($this->state) == 1)) {
                return implode(", ", $this->state);
            } else {
                return " ";
            }
        } else {
            return $this->state;
        }
    }

    /**
     * Assert pending solution.
     * Used to make sure that solved positions show up at print time.
     * The value is used as a candidate for "slicing and dicing" by elimination in
     * Sudoku::_newSolvedPosition.
     *
     * @param integer $value The value for the solved position.
     */
    public function flagsolvedposition($value) {
        $this->state = array($value => $value);
    }

    /**
     * return the state of a cell.
     *
     * @return mixed Either solved state or array of state pending solution.
     */
    public function &getstate() {
        return $this->state;
    }

    /**
     * Has the state of this cell been applied to the board.
     *
     * @return boolean True if it has, false otherwise.  Implies that IsSolved is true as well.
     */
    public function isapplied() {
        return $this->applied;
    }

     /**
      * Has this cell been solved?
      *
      * @return boolean True if this cell has hit a single state.
      */
    public function issolved() {
        return !is_array($this->state);
    }

    /**
     * This is used primarily by the pretty printer, but has other applications in the code.
     *
     * Return information about the state of a cell.
     *
     * @return integer 0 => the cell has been solved.
     *                 1 => the cell has been solved but not seen a solved.
     *                 2 => the cell has not been solved.
     */
    public function solvedstate() {
        if (is_array($this->state)) {
            if (count($this->state) == 1) {
                return 1;
            } else {
                return 2;
            }
        } else {
            return 0;
        }
    }

    /**
     * Eliminate one or more values from the state information of the cell.
     *
     * This is the negative inference of Sudoku.  By eliminating values the
     * cells approach solutions.  Once a cell has been completely eliminated,
     * the value causing the complete elimination must be the solution and the
     * cell is promoted into the solved state.
     *
     * @param mixed $thevalues or values to be removed from the cell state.
     *
     * @return boolean True if the cell state was modified, false otherwise.
     */
    public function un_set($thevalues) {
        if (is_array($thevalues)) {
            $thereturn = false;

            foreach ($thevalues as $thevalue) {
                $thereturn |= $this->un_set($thevalue);
            }

            return $thereturn;
        }

        if (is_array($this->state)) {
            $thereturn = isset($this->state[$thevalues]);
            unset($this->state[$thevalues]);
            if (count($this->state) == 0) {
                $this->state = $thevalues;
            }
            return $thereturn;
        } else {
            return false;
        }
    }
}

/**
 * The individual row column or square on the Sudoku board.
 *
 * package Sudoku
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rcs extends ObjectS {
    /** @var theindex */
    protected $theindex;

    /** @var therow */
    protected $therow = array();

    /** @var theheader */
    protected $theheader = "";

    /** @var thetag */
    protected $thetag = "";

    /**
     * Constructor
     *
     *  This interface is what limts things to 9x9 Sudoku currently
     * @param string $thetag "Row", "Column", "Square", used primarily in debugging.
     * @param integer $theindex 1..9, where is this on the board.  Square are numbered top left, ending bottom right
     * @param ObjectS $a1 of class Cell.
     * @param ObjectS $a2 of class Cell.
     * @param ObjectS $a3 of class Cell.
     * @param ObjectS $a4 of class Cell.
     * @param ObjectS $a5 of class Cell.
     * @param ObjectS $a6 of class Cell.
     * @param ObjectS $a7 of class Cell.
     * @param ObjectS $a8 of class Cell.
     * @param ObjectS $a9 of class Cell.
     */
    public function init($thetag, $theindex, &$a1, &$a2, &$a3, &$a4, &$a5, &$a6, &$a7, &$a8, &$a9) {
        $this->thetag = $thetag;
        $this->theindex = $theindex;
        $this->therow[1] = &$a1;
        $this->therow[2] = &$a2;
        $this->therow[3] = &$a3;
        $this->therow[4] = &$a4;
        $this->therow[5] = &$a5;
        $this->therow[6] = &$a6;
        $this->therow[7] = &$a7;
        $this->therow[8] = &$a8;
        $this->therow[9] = &$a9;
    }

    /**
     * There is a special case that comes up a lot in Sudoku.  If there
     * are values i, j, k and cells of the form (i, j), (j, k), (i, j, k)
     * the the values i, j, and k cannot appear in any other cells.  The
     * proof is a simple "by contradiction" proof.  Assume that the values
     * do occur elsewhere and you always get a contradiction for these
     * three cells.  I'm pretty sure that this is a general rule, but for
     * 9x9 Sudoku, they probably aren't of interested.
     *
     * @return boolean True if a 23 solution exists and has been applied.
     */
    public function _23solution() {
        $thecounts = array();
        $thetuples = array();
        $theunsolved = 0;

        for ($i = 1; $i <= 9; $i++) {
            $j = count($this->theRow[$i]->getState());
            $thecounts[ $j][] = $i;
            $theunsolved++;
        }

        if (array_key_exists( 2, $thecounts) and array_key_exists( 3, $thecounts)) {
            if ((count($thecounts[2]) < 2) || (count($thecounts[3]) < 1)) {
                return false;
            }
        }

        /*
         * Look at each pair of 2 tuples and see if their union exists in the 3 tuples.
         * If so, eliminate everything from the set and bail.
         */

        $the2tuples = &$thecounts[2];
        $the3tuples = &$thecounts[3];
        $thecount2 = count($the2tuples);
        $thecount3 = count($the3tuples);

        for ($i = 0; $i < $thecount2 - 1; $i++) {
            for ($j = $i + 1; $j < $thecount2; $j++) {
                $xxx = array_unique(array_merge($this->therow[$the2tuples[$i]]->getstate(),
                    $this->therow[$the2tuples[$j]]->getstate()));
                for ($k = 0; $k < $thecount3; $k++) {
                    if ($this->array_equal($xxx, $this->therow[$the3tuples[$k]]->getstate())) {
                        $thetuples[] = $xxx;
                        break;
                    }
                }
            }
        }

        /*
         * Since it takes 3 cells to construct the 23 tuple, unless there are more than 3
         * unsolved cells, further work doesn't make any sense.
         */

        $thereturn = false;

        if ((count($thetuples) != 0) && ($theunsolved > 3)) {
            foreach ($thetuples as $atuple) {
                foreach ($this->therow as $thecell) {
                    $thereturn |= $thecell->apply23tuple($atuple);
                }
            }
        }

        if ($thereturn) {
            $this->theheader[] = sprintf("<br />Apply %s[%d] 23 Tuple Inference:", $this->thetag, $this->theindex);
        }

        return $thereturn;
    }

    /**
     * apply a tuple to exclude items from within the row/column/square.
     *
     * @param array $atuple the tuple to be excluded.
     *
     * @return boolean true if anything changes.
     */
    protected function _applytuple(&$atuple) {
        $thereturn = false;

        for ($i = 1; $i <= 9; $i++) {
            $thereturn |= $this->therow[$i]->applytuple( $atuple);
        }

        return $thereturn;
    }

    /**
     * Calculate the coupling for a cell within the row/column/square.
     *
     * This is a placeholder to be overridden to calculate the "coupling" for
     * a cell.  Coupling is defined to be the sum of the sizes of the intersection
     * between this cell and all others in the row/column/square.  This provides
     * a metric for deciding placement of clues within puzzles.  In effect, this
     * forces the puzzle generator to select places for new clues depending upon
     * how little information is changed by altering the state of a cell.  The larger
     * the number returned by the coupling, function, the less information is currently
     * available for the state of the cell.  By selecting areas with the least information
     * the clue sets are substantially smaller than simple random placement.
     *
     * @param integer $therow the row coordinate on the board of the cell.
     * @param integer $thecolumn the column coordinate on the board of the cell.
     * @return integer the degree of coupling between the cell and the rest of the cells
     *      within the row/column/square.
     */
    public function coupling($therow, $thecolumn) {
        return 0;
    }

    /**
     * Run the inference engine for a row/column/square.
     *
     * I think that the goal of the inference engine is to eliminate
     * as much "junk" state as possible on each pass.  Therefore the
     * order of the inferences should be 23 tuple, pair, unique because
     * the 23 tuple allows you to eliminate 3 values (if it works), and the
     * pair (generally) only 2.  The unique solution adds no new information.
     *
     * @return boolean True when at least one inference has succeeded.
     */
    public function doaninference() {
        $this->theheader = null;

        $thereturn = $this->_23solution();
        $thereturn |= $this->_pairsolution();
        $thereturn |= $this->_uniquesolution();

        return $thereturn;
    }

    /**
     * Find all tuples with the same contents.
     *
     * @param array $thearray of n size tuples.
     *
     * @return array of tuples that appear the same number of times as the size of the contents
     */
    public function _findtuples(&$thearray) {
        $thereturn = array();
        for ($i = 0; $i < count($thearray); $i++) {
            $thecount = 1;

            for ($j = $i + 1; $j < count($thearray); $j++) {
                $s1 = &$this->theRow[$thearray[$i]];
                $s1 =& $s1->getstate();

                $s2 = &$this->therow[$thearray[$j]];
                $s2 =& $s2->getstate();

                $acount = count($s1);

                if ($this->array_equal($s1, $s2)) {
                    $thecount++;

                    if ($thecount == $acount) {
                        $thereturn[] = $s1;
                        break;
                    }
                }
            }
        }

        return $thereturn;
    }

    /**
     * Get a reference to the specified cell.
     *
     * @param int $i
     *
     * @return reference to ObjectS of class Cell.
     */
    public function &getcell($i) {
        return $this->therow[$i];
    }

    /**
     * Get the header set by the last call to doAnInference.
     */
    public function getheader() {
        return $this->theheader;
    }

    /**
     * Eliminate tuple-locked alternatives.
     *
     * Turns out if you every find a position of n squares which can only contain
     * the same values, then those values cannot appear elsewhere in the structure.
     * This is a second positive inference that provides additional negative information.
     * Thanks to Ghica van Emde Boas (also an author of a Sudoku class) for convincing
     * me that these situations really occurred.
     *
     * @return boolean True if something changed.
     */
    protected function _pairsolution() {
        $thecounts = array();
        $thetuples = array();

        for ($i = 1; $i <= 9; $i++) {
            $c = &$this->therow[$i];
            $thecounts[count($c->getstate())][] = $i;
        }

        unset($thecounts[1]);

        /*
         ** Get rid of any set of counts which cannot possibly meet the requirements.
         */

        $thepossibilities = $thecounts;

        foreach ($thecounts as $thekey => $thevalue) {
            if (count($thevalue) < $thekey) {
                unset($thepossibilities[$thekey]);
            }
        }

        if (count($thepossibilities) == 0) {
            return false;
        }

        /*
         * At this point there are 1 or more tuples which MAY satisfy the conditions.
         */

        $thereturn = false;

        foreach ($thepossibilities as $thevalue) {
            $thetuples = $this->_findtuples($thevalue);

            if (count($thetuples) != 0) {
                foreach ($thetuples as $atuple) {
                    $thereturn |= $this->_applyruple($atuple);
                }
            }
        }

        if ($thereturn) {
            $this->theheader[] = sprintf("<br />Apply %s[%d] Pair Inference:", $this->thetag, $this->theindex);
        }

        return $thereturn;
    }

    /**
     * un set
     *
     * @param object $thevalues
     *
     * @return boolean True if one or more values in the RCS has changed state.
     */
    public function un_set($thevalues) {
        $thereturn = false;

        for ($i = 1; $i <= 9; $i++) {
            $c = &$this->therow[$i];
            $thereturn |= $c->un_set($thevalues);
        }

        return $thereturn;
    }

    /**
     * Find a solution to a row/column/square.
     *
     * Find any unique numbers within the row/column/square under consideration.
     * Look through a row structure for a value that appears in only one cell.
     * When you find one, that's a solution for that cell.
     *
     * There is a second inference that can be taken.  Given "n" cells in a row/column/square
     * and whose values can only consist of a set of size "n", then those values may obtain
     * there and ONLY there and may be eliminated from consideration in the rest of the set.
     * For example, if two cells must contain the values 5 or 6, then no other cell in that
     * row/column/square may contain those values, similarly for 3 cells, etc.
     *
     * @return boolean True if one or more values in the RCS has changed state.
     */
    protected function _uniquesolution() {
        $theset = array();

        for ($i = 1; $i <= 9; $i++) {
            $c = &$this->therow[$i];
            if (!$c->issolved()) {
                foreach ($c->getstate() as $thevalue) {
                    $theset[$thevalue][] = $i;
                }
            }
        }

        /*
         * If there were no unsolved positions, then we're done and nothing has
         * changed.
         */

        if (count($theset) == 0) {
            return false;
        }

        /*
         * Pull out all those keys having only one occurrance in the RCS.
         */

        foreach ($theset as $thekey => $thevalues) {
            if (count($thevalues) != 1) {
                unset($theset[$thekey]);
            }
        }

        /*
         * If there aren't any unique values, we're done.
         */

        if (count($theset) == 0) {
            return false;
        }

        foreach ($theset as $thevalue => $theindex) {
            $this->therow[$theindex[0]]->flagsolvedposition($thevalue);
        }

        $this->theheader[] = sprintf("<br />Apply %s[%d] Unique Inference:", $this->thetag, $this->theindex);

        return true;
    }

    /**
     * Check to see if the RCS contains a valid state.
     *
     * @return boolean True if the state of the RCS could be part of a valid
     *          solution, false otherwise.
     */
    public function validatesolution() {
        $thenewset = array();

        foreach ($this->therow as $thecell) {
            if ($thecell->solvedstate() == 0) {
                $thenewset[] = $thecell->getstate();
            }
        }

        $xxx = array_unique($thenewset);

        return (count($xxx) == count($this->therow));
    }

    /**
     * Validate a part of a trial solution.
     *
     * Check a row/column/square to see if there are any invalidations on this solution.
     * Only items that are actually solved are compared.  This is used during puzzle
     * generation.
     *
     * @return True if the input parameter contains a valid solution, false otherwise.
     */
    public function validatetrialsolution() {
        $thenewset = array();

        foreach ($this->therow as $thecell) {
            if ($thecell->solvedstate() == 0) {
                $thenewset[] = $thecell->getstate();
            }
        }

        $xxx = array_unique($thenewset);

        return ((count($xxx) == count($thenewset) ? true : false));
    }
}

/**
 * Row ObjectS.
 *
 * package Sudoku
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class r extends rcs {
    /**
     * Constructor
     *
     * @param string $theTag "Row", "Column", "Square", used primarily in debugging.
     * @param integer $theIndex 1..9, where is this on the board.  Square are numbered top
     *                          left, ending bottom right
     * @param ObjectS $a1..9 of class Cell.  The cells comprising this entity.  This interface is what
     *                                      limts things to 9x9 Sudoku currently.
     */

    /**
     * see RCS::coupling
     *
     * @param int $therow
     * @param int $thecolumn
     */
    public function coupling($therow, $thecolumn) {
        return $thestate = $this->_coupling($thecolumn);
    }

    /**
     * Heavy lifting for row/column coupling calculations.
     *
     * RCS::coupling
     * @param integer $theindex the index of the cell within the row or column.
     *
     * @return integer the "coupling coefficient" for the cell.  The sum of the
     *          sizes of the intersection between this and all other
     *          cells in the row or column.
     */
    protected function _coupling($theindex) {
        $thecommonstate =& $this->getCell($theindex);
        $thecommonstate =& $thecommonstate->getstate();

        $thecoupling = 0;

        for ($i = 1; $i <= count($this->therow); $i++) {
            if ($i != $theindex) {
                $thecell =& $this->getcell($i);
                if ($thecell->solvedstate() != 0) {
                    $thecoupling += count(array_intersect($thecommonstate, $thecell->getstate()));
                }
            }
        }

        return $thecoupling;
    }
}

/**
 * The column ObjectS.
 *
 * package Sudoku
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class c extends r {
    /**
     * Constructor
     *
     * @param string $theTag "Row", "Column", "Square", used primarily in debugging.
     * @param integer $theIndex 1..9, where is this on the board.  Square are numbered top
     *                          left, ending bottom right
     * @param ObjectS $a1..9 of class Cell.  The cells comprising this entity.  This interface is what
     *          limts things to 9x9 Sudoku currently.
     */

    /**
     * see R::coupling
     *
     * @param int $therow
     * @param int $thecolumn
     */
    public function coupling($therow, $thecolumn) {
        return $thestate = $this->_coupling($therow);
    }
}

/**
 * The Square ObjectS.
 *
 * package Sudoku
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class s extends rcs {
    /**
     * The cells within the 3x3 sudoku which participate in the coupling calculation for a square.
     * Remember that the missing cells have already participated in the row or column coupling
     * calculation.
     *
     * @var array
     */
    protected $thecouplingorder = array( 1 => array(5, 6, 8, 9),
        2 => array(4, 6, 7, 9),
        3 => array(4, 5, 7, 8),
        4 => array(2, 3, 8, 9),
        5 => array(1, 3, 7, 9),
        6 => array(1, 2, 7, 8),
        7 => array(2, 3, 5, 6),
        8 => array(1, 3, 4, 6),
        9 => array(1, 2, 4, 5));

    /**
     * Constructor
     *
     * @param string $theTag "Row", "Column", "Square", used primarily in debugging.
     * @param integer $theIndex 1..9, where is this on the board.  Square are numbered top
     *                          left, ending bottom right
     * @param ObjectS $a1..9 of class Cell.  The cells comprising this entity.  This interface is what
     *                                      limits things to 9x9 Sudoku currently.
     */

    /**
     * see RCS::coupling
     *
     * @param int $therow
     * @param int $thecolumn
     */
    public function coupling($therow, $thecolumn) {
        $theindex = ((($therow - 1) % 3) * 3) + (($thecolumn - 1) % 3) + 1;
        $thecommonstate =& $this->getcell($theindex);
        $thecommonstate =& $thecommonstate->getstate();

        $thecoupling = 0;

        foreach ($this->thecouplingorder[$theindex] as $i) {
            $thecell =& $this->getcell($i);
            if ($thecell->solvedstate() != 0) {
                $thecoupling += count(array_intersect($thecommonstate, $thecell->getstate()));
            }
        }

        return $thecoupling;
    }
}

/**
 * Solve and generate Sudoku puzzles.
 *
 * Solve and generate Sudoku.  A simple output interface is provided for
 * web pages.  The primary use of this class is as infra-structure for
 * Sudoku game sites.
 *
 * The solver side of this class (solve) relies on the usual characteristic
 * of logic puzzles, i.e., at any point in time there is one (or more)
 * UNIQUE solution to some part of the puzzle.  This solution can be
 * applied, then iterated upon to find the next part of the puzzle.  A
 * properly constructed Sudoku can have only one solution which guarangees
 * that this is the case. (Sudoku with multiple solutions will always
 * require guessing at some point which is specifically disallowed by
 * the rules of Sudoku).
 *
 * While the solver side is algorithmic, the generator side is much more
 * difficult and, in fact, the generation of Sudoku appears to be NP
 * complete.  That being the case, I observed that most successful
 * generated initial conditions happened quickly, typically with < 40
 * iterations.  So the puzzle generator runs "for a while" until it
 * either succeeds or doesn't generated a solveable puzzle.  If we get
 * to that position, I just retry and so far I've always succeeded in
 * generating an initial state.  Not guarateed, but in engineering terms
 * "close enough".
 *
 * package Sudoku
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sudoku extends ObjectS {
    /** @var array of ObjectSs of type Cell. */
    protected $theboard = array();

    /** @var boolean True if debugging output is to be provided during a run. */
    protected $thedebug = false;

    /** @var ObjectS of type R An array of RCS ObjectSs, one ObjectS for each row. */
    protected $therows = array();

    /** @var ObjectS of type C An array of RCS ObjectSs, one ObjectS for each Column. */
    private $thecolumns = array();

    /** @var ObjectS of type S An array of RCS ObjectSs, one ObjectS for each square. */
    protected $thesquares = array();

    /** @var integer. Used during puzzle generation for debugging output.  There may
     * eventually be some use of theLevel to figure out where to stop
     * the backtrace when puzzle generation fails.
     */
    protected $thelevel = 0;

    /** @var integer. Used during puzzle generation to determine when the generation
     * will fail.  Failure, in this case, means to take a LONG time.  The
     * backtracing algorithm used in the puzzle generator will always find
     * a solution, it just might take a very long time.  This is a way to
     * limit the damage before taking another guess.
     */
    protected $themaxiterations = 50;

    /** @var integer. Used during puzzle generation to limit the number of trys at
     * generation a puzzle in the event puzzle generation fails
     */
    protected $thetrys = 10;

    /** @var integer. Used during puzzle generation to count the number of iterations
     * during puzzle generation.  It the number gets above $theMaxIterations,
     * puzzle generation has failed and another try is made.
     */
    protected $thegenerationiterations = 0;

    /**
     * Constructor
     *
     * @param boolean $thedebug
     */
    public function init($thedebug = false) {
        $this->thedebug = $thedebug;

        for ($i = 1; $i <= 9; $i++) {
            for ($j = 1; $j <= 9; $j++) {
                $this->theboard[$i][$j] = new cell;
                $this->theboard[$i][$j].init( $i, $j);
            }
        }

        $this->_buildrcs();
    }

    /**
     * Apply a pending solved position to the row/square/column.
     *
     * At this point, the board has been populated with any pending solutions.
     * This applies the "negative" inference that no row, column, or square
     * containing the value within the cell.
     *
     * @param integer $row The row of the board's element whose value is now fixed.
     * @param integer $col The column of the board's element whose value is now fixed.
     */
    protected function _applysolvedposition($row, $col) {
        $thevalue = $this->theboard[$row][$col]->getstate();

        /*
         * No other cell in the row, column, or square can take on the value "value" any longer.
         */

        $i = (((int)(($row - 1) / 3)) * 3);
        $i = $i + ((int)(($col - 1) / 3)) + 1;

        $this->therows[$row]->un_set($thevalue);

        $this->thecolumns[$col]->un_set($thevalue);

        $this->thesquares[$i]->un_set($thevalue);
    }

    /**
     * Apply all pending solved positions to the board.
     *
     * @return boolean True if at least one solved position was applied, false
     *                 otherwise.
     */
    protected function _applysolvedpositions() {
        $thereturn = false;

        for ($i = 1; $i <= 9; $i++) {
            for ($j = 1; $j <= 9; $j++) {
                if (!$this->theboard[$i][$j]->isapplied()) {
                    if ($this->theboard[$i][$j]->solvedstate() == 0) {
                        $this->_applysolvedposition($i, $j);

                        /*
                         * Update the solved position matrix and make sure that the board actually
                         * has a value in place.
                         */

                        $this->theboard[$i][$j]->applied();
                        $thereturn = true;
                    }
                }
            }
        }

        return $thereturn;
    }

    /**
     * build the row/column/square structures for the board.
     */
    protected function _buildrcs() {
        for ($i = 1; $i <= 9; $i++) {
            $this->therows[$i] = new r("Row",
                $i,
                $this->theboard[$i][1],
                $this->theboard[$i][2],
                $this->theboard[$i][3],
                $this->theboard[$i][4],
                $this->theboard[$i][5],
                $this->theboard[$i][6],
                $this->theboard[$i][7],
                $this->theboard[$i][8],
                $this->theboard[$i][9]);
            $this->thecolumns[$i] = new C("Column",
                $i,
                $this->theboard[1][$i],
                $this->theboard[2][$i],
                $this->theboard[3][$i],
                $this->theboard[4][$i],
                $this->theboard[5][$i],
                $this->theboard[6][$i],
                $this->theboard[7][$i],
                $this->theboard[8][$i],
                $this->theboard[9][$i]);

            $r = ((int)(($i - 1) / 3)) * 3;
            $c = (($i - 1) % 3) * 3;

            $this->thesquares[$i] = new S("Square",
                $i,
                $this->theboard[$r + 1][$c + 1],
                $this->theboard[$r + 1][$c + 2],
                $this->theboard[$r + 1][$c + 3],
                $this->theboard[$r + 2][$c + 1],
                $this->theboard[$r + 2][$c + 2],
                $this->theboard[$r + 2][$c + 3],
                $this->theboard[$r + 3][$c + 1],
                $this->theboard[$r + 3][$c + 2],
                $this->theboard[$r + 3][$c + 3]);
        }
    }

    /**
     * Seek alternate solutions in a solution set.
     *
     * Given a solution, see if there are any alternates within the solution.
     * In theory this should return the "minimum" solution given any solution.
     *
     * @param array $theinitialstate
     *
     * @return array A set of triples containing the minimum solution.
     */
    public function findalternatesolution($theinitialstate) {
        $j = count($theinitialstate);

        for ($i = 0; $i < $j; $i++) {
            $xxx = $theinitialstate;

            $xxx = array_splice($xxx, $i, 1);

            $this->sudoku();

            $this->initializepuzzlefromarray($xxx);

            if ($this->solve()) {
                return $this->findalternatesolution($xxx);
            }
        }

        return $theinitialstate;
    }

    /**
     * Initialize Sudoku puzzle generation and generate a puzzle.
     *
     * Turns out that while the solution of Sudoku is mechanical, the creation of
     * Sudoku is an NP-Complete problem.  Which means that I can use the inference
     * engine to help generate puzzles, but I need to test the solution to see if
     * I've gone wrong and back up and change my strategy.  So something in the
     * recursive descent arena will be necessary.  Since the generation can take
     * a long time to force a solution, it's easier to probe for a solution
     * if you go "too long".
     *
     * @param integer $thedifficultylevel [optional] Since virtually everybody who
     *                plays sudoku wants a variety of difficulties this controls that.
     *                1 is the easiest, 10 the most difficult.  The easier Sudoku have
     *                extra information.
     * @param integer $themaxiterations [optional] Controls the number of iterations
     *                before the puzzle generator gives up and trys a different set
     *                of initial parameters.
     * @param integer $thetrys [optional] The number of attempts at resetting the
     *                initial parameters before giving up.
     * @return array A set of triples suitable for initializing a new Sudoku class
     */
    public function generatepuzzle($thedifficultylevel = 10, $themaxiterations = 50, $thetrys = 10) {
        $thedifficultylevel = min($thedifficultylevel, 10);
        $thedifficultylevel = max($thedifficultylevel, 1);

        $this->thelevel = 0;
        $this->thetrys = $thetrys;
        $this->themaxiterations = $themaxiterations;
        $this->thegenerationiterations = 0;

        for ($thetrys = 0; $thetrys < $this->thetrys; $thetrys++) {
            $theavailablepositions = array();
            $thecluespositions = array();
            $theclues = array();

            for ($i = 1; $i <= 9; $i++) {
                for ($j = 1; $j <= 9; $j++) {
                    $theavailablepositions[] = array($i, $j);
                }
            }

            $theinitialstate = $this->_generatepuzzle($theavailablepositions, $thecluespositions, $theclues);

            if ($theinitialstate) {
                if ($thedifficultylevel != 10) {
                    $xxx = array();

                    foreach ($theinitialstate as $yyy) {
                        $xxx[] = (($yyy[0] - 1) * 9) + ($yyy[1] - 1);
                    }
                    /*
                     * Get rid of the available positions already used in the initial state.
                     */

                    sort($xxx);
                    $xxx = array_reverse($xxx);

                    foreach ($xxx as $i) {
                        array_splice($theavailablepositions, $i, 1);
                    }

                    /*
                     * Easy is defined as the number of derivable clues added to the minimum
                     * required information to solve the puzzle as returned by _generatePuzzle.
                     */

                    for ($i = 0; $i < (10 - $thedifficultylevel); $i++) {
                        $xxx = mt_rand(0, count($theavailablepositions) - 1);
                        $row = $theavailablepositions[$xxx][0];
                        $col = $theavailablepositions[$xxx][1];
                        $theinitialstate[] = array($row, $col, $this->theboard[$row][$col]);
                        array_splice($theavailablepositions, $xxx, 1);
                    }
                }

                return $theinitialstate;
            }

            if ($this->thedebug) {
                printf("<br>Too many iterations (%d), %d\n", $this->themaxiterations, $thetrys);
            }
            $this->sudoku($this->thedebug);
        }

        /*
         * No solution possible, we guess wrong too many times.
         */

        return array();
    }

    /**
     * Sudoku puzzle generator.
     *
     * This is the routine that does the heavy lifting
     * for the puzzle generation.  It works by taking a guess for a value of a cell, applying
     * the solver, testing the solution, and if it's a valid solution, calling itself
     * recursively.  If during this process, a solution cannot be found, the generator backs
     * up (backtrace in Computer Science parlance) and trys another value.  Since the generation
     * appears to be an NP complete problem (according to the literature) limits on the number
     * of iterations are asserted.  Once these limits are passed, the generator gives up and
     * makes another try.  If enough tries are made, the generator gives up entirely.
     *
     * @param array $theavailablepositions A set of pairs for all positions which have not been
     *              filled by the solver or the set of guesses.  When we run out of available
     *              positions, the solution is in hand.
     * @param array $thecluespositions A set of pairs for which values have been set by the
     *              puzzle generator.
     * @param array $theclues A set of values for each pair in $theCluesPositions.
     * @return array NULL array if no solution is possible, otherwise a set of triples
     *               suitable for feeding to {@link Sudoku::initializePuzzleFromArray}
     */
    protected function _generatepuzzle($theavailablepositions, $thecluespositions, $theclues) {
        $this->thelevel++;

        $this->thegenerationiterations++;

        /*
         * Since the last solution sequence may have eliminated one or more positions by
         * generating forced solutions for them, go through the list of available positions
         * and get rid of any that have already been solved.
         */

        $j = count($theavailablepositions);

        for ($i = 0; $i < $j; $i++) {
            if ($this->theboard[$theavailablepositions[$i][0]]
                [$theavailablepositions[$i][1]]->isapplied()) {
                array_splice($theavailablepositions, $i, 1);
                $i = $i - 1;
                $j = $j - 1;
            }
        }

        if (count($theavailablepositions) == 0) {
            /*
             * We're done, so we can return the clues and their positions to the caller.
             * This test is being done here to accommodate the eventual implementation of
             * generation from templates in which partial boards will be fed to the solver
             * and then the remaining board fed in.
             */

            for ($i = 0; $i < count($thecluespositions); $i++) {
                array_push($thecluespositions[$i], $theclues[$i]);
            }

            return $thecluespositions;
        }

        /*
         * Calculate the coupling for each available position.
         *
         * "coupling" is a measure of the amount of state affected by any change
         * to a given cell.  In effect, the larger the coupling, the less constrained
         * the state of the cell is and the greater the effect of any change made to
         * the cell.  There is some literature to this effect associated with Roku puzzles
         * (4x4 grid).  I'm trying this attempting to find a way to generate consistently
         * more difficult Sudoku and it seems to have worked; the clue count drops to 25 or
         * fewer, more in line with the numbers predicted by the literature.  The remainder
         * of the work is likely to be associated with finding better algorithms to solve
         * Sudoku (which would have the effect of generating harder ones).
         */
        $thecouplings = array();

        foreach ($theavailablepositions as $xxx) {
            $therowcoupling = $this->therows[$xxx[0]]->coupling($xxx[0], $xxx[1]);
            $thecolumncoupling = $this->thecolumns[$xxx[1]]->coupling($xxx[0], $xxx[1]);
            $thesquarecoupling = $this->thesquares[$this->_squareindex($xxx[0], $xxx[1])]->coupling($xxx[0], $xxx[1]);
            $thecouplings[$therowcoupling + $thecolumncoupling + $thesquarecoupling][] = $xxx;
        }

        $themaximumcoupling = max(array_keys($thecouplings));

        /*
         * Pick a spot on the board and get the clues set up.
         */

        $thechoice = mt_rand(0, count($thecouplings[$themaximumcoupling]) - 1);
        $thecluespositions[] = $thecouplings[$themaximumcoupling][$thechoice];
        $therow = $thecouplings[$themaximumcoupling][$thechoice][0];
        $thecolumn = $thecouplings[$themaximumcoupling][$thechoice][1];

        /*
         * Capture the necessary global state of the board
         */

        $thecurrentboard = $this->deepcopy($this->theboard);

        /*
         * This is all possible states for the chosen cell.  All values will be
         * randomly tried to see if a solution results.  If all solutions fail,
         * the we'll back up in time and try again.
         */

        $thepossibleclues = array_keys($this->theboard[$therow][$thecolumn]->getstate());

        while (count($thepossibleclues) != 0) {
            if ($this->thegenerationiterations > $this->themaxiterations) {
                $this->thelevel = $this->thelevel - 1;
                return array();
            }

            $thecluechoice = mt_rand(0, count($thepossibleclues) - 1);
            $thevalue = $thepossibleclues[$thecluechoice];
            array_splice($thepossibleclues, $thecluechoice, 1);

            $theclues[] = $thevalue;

            $this->theboard[$therow][$thecolumn]->flagsolvedposition($thevalue);

            if ($this->thedebug) {
                printf("<br>(%03d, %03d) Trying (%d, %d) = %d\n", $this->theLevel,
                    $this->thegenerationiterations, $therow, $thecolumn, $thevalue);
            }

            $theflag = $this->solve(false);

            if ($this->_validatetrialsolution()) {
                if ($theflag) {
                    /*
                     * We're done, so we can return the clues and their positions to the caller.
                     */

                    for ($i = 0; $i < count($thecluespositions); $i++) {
                        array_push($thecluespositions[$i], $theclues[$i]);
                    }

                    return $thecluespositions;
                } else {
                    $xxx = $this->_generatepuzzle($theavailablepositions, $thecluespositions, $theclues);
                    if ($xxx) {
                        return $xxx;
                    }
                }
            }

            /*
            * We failed of a solution, back out the state and try the next possible value
            * for this position.
            */

            $this->theboard = $thecurrentboard;
            $this->_buildrcs();
            array_pop($theclues);
        }

        $this->thelevel = $this->thelevel - 1;

        /*
        * If we get here, we've tried all possible values remaining for the chosen
        * position and couldn't get a solution.  Back out and try something else.
        */

        return array();
    }

    /**
     * Get the current state of the board as a string.
     *
     * Return the contents of the board as a string of digits and blanks.  Blanks
     * are used where the corresponding board item is an array, indicating the cell
     * has not yet been solved.
     */
    public function getboardasstring() {
        $thestring = "";

        for ($i = 1; $i <= 9; $i++) {
            for ($j = 1; $j <= 9; $j++) {
                $thestring .= $this->theboard[$i][$j]->asstring();
            }
        }

        return $thestring;
    }

    /**
     * Get cel
     *
     * @param int $r
     * @param int $c
     */
    public function &getcell($r, $c) {
        return $this->theboard[$r][$c];
    }

    /**
     * Each element of the input array is a triple consisting of (row, column, value).
     * Each of these values is in the range 1..9.
     *
     * @param array $thearray
     */
    public function initializepuzzlefromarray($thearray) {
        foreach ($thearray as $xxx) {
            $c =& $this->getcell($xxx[0], $xxx[1]);
            $c->flagsolvedposition($xxx[2]);
        }
    }

    /**
     * Initialize puzzle from an input file.
     *
     * The input file is a text file, blank or tab delimited, with each line being a
     * triple consisting of "row column value".  Each of these values is in the range
     * 1..9.  Input lines that are blank (all whitespace) or which begin with whitespace
     * followed by a "#" character are ignored.
     *
     * @param mixed $thehandle [optional] defaults to STDIN.  If a string is passed
     *              instead of a file handle, the file is opened.
     */
    public function initializepuzzlefromfile($thehandle = STDIN) {
        $theopenedfileflag = false;

        /*
         * If a file name is passed instead of a resource, open the
         * file and process it.
         */

        if (is_string($thehandle)) {
            $thehandle = fopen($thehandle, "r");
            if ($thehandle === false) {
                exit();
            }
        }

        $yyy = array();

        if ($thehandle) {
            while (!feof($thehandle)) {
                $thestring = trim(fgets($thehandle));
                if (($thestring != "") &&
                    (!preg_match('/^\s*#/', $thestring))) {
                    $xxx = preg_split('/\s+/', $thestring);
                    if (!feof($thehandle)) {
                        $yyy[] = array((int)$xxx[0], (int)$xxx[1], (int)$xxx[2]);
                    }
                }
            }
        }

        $this->initializepuzzlefromarray($yyy);

        if ($theopenedfileflag) {
            fclose( $thehandle);
        }
    }

    /**
     * Initialize puzzle from a string.
     *
     * The input parameter consists of a string of 81 digits and blanks.  If fewer characters
     * are provide, the string is padded on the right.
     *
     * @param string $thestring The initial state of each cell in the puzzle.
     */
    public function initializepuzzlefromstring($thestring) {
        $thestring = str_pad($thestring, 81, " ");

        for ($i = 0; $i < 81; $i++) {
            if ($thestring{$i} != " ") {
                $thearray[] = array((int)($i / 9) + 1, ($i % 9) + 1, (int)$thestring{$i});
            }
        }

        $this->initializepuzzlefromrray($therray);
    }

    /**
     * predicate to determine if the current puzzle has been solved.
     *
     * @return boolean true if the puzzle has been solved.
     */
    public function issolved() {
        for ($i = 1; $i <= 9; $i++) {
            for ($j = 1; $j <= 9; $j++) {
                if (!$this->theboard[$i][$j]->issolved()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Convert pending to actual solutions.
     *
     * This step is actually unnecessary unless you want a pretty output of the
     * intermediate.
     *
     * @return boolean True if at least on pending solution existed, false otherwise.
     */
    protected function _newsolvedposition() {
        $thereturn = false;

        for ($i = 1; $i <= 9; $i++) {
            for ($j = 1; $j <= 9; $j++) {
                if ($this->theboard[$i][$j]->solvedstate() == 1) {
                    $this->theboard[$i][$j]->un_set($this->theboard[$i][$j]->getstate());
                    $thereturn = true;
                }
            }
        }

        return $thereturn;
    }

    /**
     * Print the contents of the board in HTML format.
     *
     * A "hook" so that extension classes can show all the steps taken by
     * the solve function.
     *
     * @param string $theheader [optional] The header line to be output along
     *               with the intermediate solution.
     */
    protected function _printintermediatesolution($theheader = null) {
        if ($this->thedebug) {
            $this->printsolution( $theheader);
        }
    }

    /**
     * Print the contents of the board in HTML format.
     *
     * Simple output, is tailored by hand so that an initial state and
     * a solution will find nicely upon a single 8.5 x 11 page of paper.
     *
     * @param mixed $theheader [optional] The header line[s] to be output along
     *               with the solution.
     */
    public function printsolution($theheader = null) {
        if (($this->thedebug) && ($theheader != null)) {
            if (is_array($theheader)) {
                foreach ($theheader as $aheader) {
                    print $aheader;
                }
            } else {
                print $theheader;
            }
        }

        $thecolors = array("green", "blue", "red");
        $thefontsize = array("1em", "1em", ".8em");
        $thefontweight = array("bold", "bold", "lighter");

        printf("<br /><table border=\"1\" style=\"border-collapse: separate; border-spacing: 0px;\">\n");

        $thelast = 2;

        for ($i = 1; $i <= 9; $i++) {
            if ($thelast == 2) {
                printf("<tr>\n");
            }

            printf("<td><table border=\"1\" width=\"100%%\">\n");

            $thelast1 = 2;

            for ($j = 1; $j <= 9; $j++) {
                if ($thelast1 == 2) {
                    printf("<tr>\n");
                }

                $c = &$this->thesquares[$i];
                $c =& $c->getcell($j);
                $thesolvedstate = $c->solvedstate();

                printf("<td style=\"text-align: center; padding: .6em; color: %s; font-weight: %s; font-size: %s;\">",
                $thecolors[$thesolvedstate],
                $thefontweight[$thesolvedstate],
                $thefontsize[$thesolvedstate]);
                $xxx = $c->asstring($this->thedebug);
                print ($xxx == " " ? "&nbsp;" : $xxx);
                printf("</td>\n");

                $thelast1 = ($j - 1) % 3;
                if ($thelast1 == 2) {
                    printf("</tr>\n");
                }
            }

            printf("</table></td>\n");

            $thelast = ($i - 1) % 3;
            if ($thelast == 2) {
                printf("</tr>\n");
            }
        }

        printf("</table>\n");
    }

    /**
     * Solve a Sudoku.
     *
     * As explained earlier, this works by iterating upon three different
     * types of inference:
     *
     * 1. A negative one, in which a value used within a row/column/square
     * may not appear elsewhere within the enclosing row/column/square.
     * 2. A positive one, in which any value with is unique in a row
     * or column or square must be the solution to that position.
     * 3. A tuple based positive one which comes in a number of flavors:
     * 3a. The "Pair" rule as stated by the author of the "other" Sudoku
     *     class on phpclasses.org and generalized by me, e.g., in any RCS
     *     two cells containing a pair of values eliminate those values from
     *     consideration in the rest of the RC or S.
     * 3b. The n/n+1 set rule as discovered by me, e.g., in any RCS, three cells
     *     containing the following pattern, (i, j)/(j, k)/(i, j, k) eliminate
     *     the values i, j, k from consideration in the rest of the RC or S.
     *
     * During processing I explain which structures (row, column, square)
     * are being used to infer solutions.
     *
     * @param boolean $theinitialstateflag [optional] True if the initial
     *                state of the board is to be printed upon entry, false
     *                otherwise.  [Default = true]
     * @return boolean true if a solution was possible, false otherwise.
     */
    public function solve($theinitialstateflag = true) {
        $theheader = "<br />Initial Position:";

        do {
            do {
                $this->_applysolvedpositions();
                if ($theinitialstateflag) {
                    $this->_printintermediatesolution($theheader);
                    $theheader = null;
                } else {
                    $theinitialstateflag = true;
                    $theheader = "<br />Apply Slice and Dice:";
                }
            } while ($this->_newsolvedposition());

            $therowiteration = false;

            for ($i = 1; $i <= 9; $i++) {
                if ($this->theRows[$i]->doninference()) {
                    $theheader = $this->therows[$i]->getheader();
                    $therowiteration = true;
                    break;
                }
            }

            $thecolumniteration = false;

            if (!$therowiteration) {
                for ($i = 1; $i <= 9; $i++) {
                    if ($this->thecolumns[$i]->doaninference()) {
                        $theheader = $this->thecolumns[$i]->getheader();
                        $thecolumniteration = true;
                        break;
                    }
                }
            }

            $thesquareiteration = false;

            if (!($therowiteration || $thecolumniteration)) {
                for ($i = 1; $i <= 9; $i++) {
                    if ($this->thesquares[$i]->doaninference()) {
                        $theheader = $this->thesquares[$i]->getheader();
                        $thesquareiteration = true;
                        break;
                    }
                }
            }
        } while ($therowiteration || $thecolumniteration || $thesquareiteration);

        return $this->issolved();
    }

    /**
     * Brute force additional solutions.
     *
     * Here there be dragons.  In conversations with other Sudoku folks, I find that there ARE Sudoku with
     * unique solutions for which a clue set may be incomplete, i.e., does not lead to a solution.  The
     * solution may only be found by guessing the next move.  I'm of the opinion that this violates the
     * definition of Sudoku (in which it's frequently said "never guess") but if it's possible to find
     * a solution, this will do it.
     *
     * The problem is that it can take a LONG time if there ISN'T a solution since this is basically a
     * backtracing solution trier.
     *
     * The basic algorithm is pretty simple:
     *
     * 1. Find the first unsolved cell.
     * 2. For every possible value, substutite value for the cell, apply inferences.
     * 3. If a solution was found, we're done.
     * 4. Recurse looking for the next cell to try a value for.
     *
     * There's a bit of bookkeeping to keep the state right when backing up, but that's pretty
     * straightforward and looks a lot like that of generatePuzzle.
     *
     * @param int $i
     * @param int $j
     *
     * @return array The clues added sufficient to solve the puzzle.
     */
    public function solvebruteforce($i = 1, $j = 1) {
        for (; $i <= 9; $i++) {
            for (; $j <= 9; $j++) {
                if ($this->theboard[$i][$j]->solvedstate() != 0) {
                    if ($this->thedebug) {
                        printf("<br />Applying Brute Force to %d, %d\n", $i, $j);
                    }

                    $thecurrentboard = $this->deepcopy($this->theboard);
                    $thevalues = $this->theboard[$i][$j]->getstate();

                    foreach ($thevalues as $thevalue) {
                        $this->theboard[$i][$j]->flagsolvedposition($thevalue);

                        $thesolutionflag = $this->solve();
                        $thetrialsolutionflag = $this->_validatetrialsolution();

                        if ($thetrialsolutionflag && $thesolutionflag) {
                            return array(array($i, $j, $thevalue));
                        }

                        if ($thetrialsolutionflag) {
                            $thenewguesses = $this->solvebruteForce($i, $j + 1);

                            if ($thenewguesses) {
                                $thenewguesses[] = array($i, $j, $thevalue);

                                return $thenewguesses;
                            }
                        }

                        if ($this->thedebug) {
                            printf("<br />Backing out\n");
                        }

                        $this->theboard = $thecurrentboard;
                        $this->_buildrcs();
                    }

                    return array();
                }
            }
        }
    }

    /**
     * Calculate the index of the square containing a specific cell.
     *
     * @param integer $therow the row coordinate.
     * @param integer $thecolumn the column coordinate.
     * @return integer the square index in the range 1..9
     */
    protected function _squareindex($therow, $thecolumn) {
        $theindex = ((int)(($therow - 1) / 3) * 3) + (int)(($thecolumn - 1) / 3) + 1;
        return $theindex;
    }

    /**
     * Validate a complete solution.
     *
     * After a complete solution has been generated check the board and
     * report any inconsistencies.  This is primarily intended for debugging
     * purposes.
     *
     * @return mixed true if the solution is valid, an array containing the
     *               error details.
     */
    public function validatesolution() {
        $thereturn = array();

        for ($i = 1; $i <= 9; $i++) {
            if (!$this->therows[$i]->validatesolution()) {
                $thereturn[0][] = $i;
            }
            if (!$this->thecolumns[$i]->validatesolution()) {
                $thereturn[1][] = $i;
            }
            if (!$this->thesquares[$i]->validatesolution()) {
                $thereturn[2][] = $i;
            }
        }

        return (count($thereturn) == 0 ? true : $thereturn);
    }

    /**
     * Validate an entire trial solution.
     *
     * Used during puzzle generation to determine when to backtrace.
     *
     * @return True when the intermediate soltuion is valid, false otherwise.
     */
    protected function _validatetrialsolution() {
        for ($i = 1; $i <= 9; $i++) {
            if (!(($this->therows[$i]->validatetrialsolution()) &&
                ($this->thecolumns[$i]->validatetrialsolution()) &&
                ($this->thesquares[$i]->validatetrialsolution()))) {
                return false;
            }
        }

        return true;
    }
}

/**
 * Extend Sudoku to generate puzzles based on templates.
 *
 * Templates are either input files or arrays containing doubles.
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class SudokuTemplates extends Sudoku
{
    /**
     * Generate puzzle from file
     *
     * @param int $thehandle
     * @param int $thedifficultylevel
     */
    public function generatepuzzlefromfile($thehandle = STDIN, $thedifficultylevel = 10) {
        $yyy = array();

        if ($thehandle) {
            while (!feof($thehandle)) {
                $thestring = trim(fgets($thehandle));
                $xxx = preg_split("/\s+/", $thestring);
                if (!feof($thehandle)) {
                    $yyy[] = array((int)$xxx[0], (int)$xxx[1]);
                }
            }
        }

        return $this->generatepuzzlefromarray($yyy, $thedifficultylevel);
    }

    /**
     * Generate puzzle from file
     *
     * @param int $thearray
     * @param int $thedifficultylevel
     */
    public function generatepuzzlefromarray($thearray, $thedifficultylevel = 10) {
        $this->_generatepuzzle($thearray, array(), array());

        /*
        ** Because the generation process may infer values for some of the
        ** template cells, we construct the clues from the board and the
        ** input array before continuing to generate the puzzle.
        */
        foreach ($thearray as $thekey => $theposition) {
            $thetemplateclues[] = array($theposition[0], $theposition[1], $this->theboard[$theposition[0]][$theposition[1]]);
        }

        $theotherclues = $this->generatepuzzle($thedifficultylevel);

        return array_merge($thetemplateclues, $theotherclues);
    }
}

/**
 * Extend Sudoku to print all intermediate results.
 *
 * @copyright  2007 Vasilis Daloukas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sudokuintermediatesolution extends sudoku {
    /**
     * intermediate results
     *
     * @param int $thedebug
     */
    public function sudokuintermediateresults($thedebug = false) {
        $this->sudoku($thedebug);
    }
    /**
     * print intermediate solution
     *
     * @param object $theheader
     */
    protected function _printintermediatesolution($theheader = null) {
        $this->printsolution($theheader);
    }
}

/**
 * make seed
 */
function make_seed() {
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}
