<?php


# See diff.doc

// A PHP diff engine for phpwiki. (Taken from phpwiki-1.3.3)
//
// Copyright (C) 2000, 2001 Geoffrey T. Dairiki <dairiki@dairiki.org>
// You may copy this code freely under the conditions of the GPL.
//

define('USE_ASSERTS_IN_WIKI', function_exists('assert'));

class _WikiDiffOp {
    var $type;
    var $orig;
    var $closing;

    function reverse() {
        trigger_error("pure virtual", E_USER_ERROR);
    }

    function norig() {
        return $this->orig ? sizeof($this->orig) : 0;
    }

    function nclosing() {
        return $this->closing ? sizeof($this->closing) : 0;
    }
}

class _WikiDiffOp_Copy extends _WikiDiffOp {
    var $type = 'copy';

    public function __construct ($orig, $closing = false) {
        if (!is_array($closing))
            $closing = $orig;
        $this->orig = $orig;
        $this->closing = $closing;
    }

    function reverse() {
        return new _WikiDiffOp_Copy($this->closing, $this->orig);
    }
}

class _WikiDiffOp_Delete extends _WikiDiffOp {
    var $type = 'delete';

    public function __construct ($lines) {
        $this->orig = $lines;
        $this->closing = false;
    }

    function reverse() {
        return new _WikiDiffOp_Add($this->orig);
    }
}

class _WikiDiffOp_Add extends _WikiDiffOp {
    var $type = 'add';

    public function __construct ($lines) {
        $this->closing = $lines;
        $this->orig = false;
    }

    function reverse() {
        return new _WikiDiffOp_Delete($this->closing);
    }
}

class _WikiDiffOp_Change extends _WikiDiffOp {
    var $type = 'change';

    public function __construct ($orig, $closing) {
        $this->orig = $orig;
        $this->closing = $closing;
    }

    function reverse() {
        return new _WikiDiffOp_Change($this->closing, $this->orig);
    }
}


/**
 * Class used internally by Diff to actually compute the diffs.
 *
 * The algorithm used here is mostly lifted from the perl module
 * Algorithm::Diff (version 1.06) by Ned Konz, which is available at:
 *     http://www.perl.com/CPAN/authors/id/N/NE/NEDKONZ/Algorithm-Diff-1.06.zip
 *
 * More ideas are taken from:
 *     http://www.ics.uci.edu/~eppstein/161/960229.html
 *
 * Some ideas are (and a bit of code) are from from analyze.c, from GNU
 * diffutils-2.7, which can be found at:
 *     ftp://gnudist.gnu.org/pub/gnu/diffutils/diffutils-2.7.tar.gz
 *
 * closingly, some ideas (subdivision by NCHUNKS > 2, and some optimizations)
 * are my own.
 *
 * @author Geoffrey T. Dairiki
 * @access private
 */
class _WikiDiffEngine
{
    function diff ($from_lines, $to_lines) {
        $n_from = sizeof($from_lines);
        $n_to = sizeof($to_lines);

        $this->xchanged = $this->ychanged = array();
        $this->xv = $this->yv = array();
        $this->xind = $this->yind = array();
        unset($this->seq);
        unset($this->in_seq);
        unset($this->lcs);

        // Skip leading common lines.
        for ($skip = 0; $skip < $n_from && $skip < $n_to; $skip++) {
            if ($from_lines[$skip] != $to_lines[$skip])
                break;
            $this->xchanged[$skip] = $this->ychanged[$skip] = false;
        }
        // Skip trailing common lines.
        $xi = $n_from; $yi = $n_to;
        for ($endskip = 0; --$xi > $skip && --$yi > $skip; $endskip++) {
            if ($from_lines[$xi] != $to_lines[$yi])
                break;
            $this->xchanged[$xi] = $this->ychanged[$yi] = false;
        }

        // Ignore lines which do not exist in both files.
        for ($xi = $skip; $xi < $n_from - $endskip; $xi++)
            $xhash[$from_lines[$xi]] = 1;
        for ($yi = $skip; $yi < $n_to - $endskip; $yi++) {
            $line = $to_lines[$yi];
            if ( ($this->ychanged[$yi] = empty($xhash[$line])) )
                continue;
            $yhash[$line] = 1;
            $this->yv[] = $line;
            $this->yind[] = $yi;
        }
        for ($xi = $skip; $xi < $n_from - $endskip; $xi++) {
            $line = $from_lines[$xi];
            if ( ($this->xchanged[$xi] = empty($yhash[$line])) )
                continue;
            $this->xv[] = $line;
            $this->xind[] = $xi;
        }

        // Find the LCS.
        $this->_compareseq(0, sizeof($this->xv), 0, sizeof($this->yv));

        // Merge edits when possible
        $this->_shift_boundaries($from_lines, $this->xchanged, $this->ychanged);
        $this->_shift_boundaries($to_lines, $this->ychanged, $this->xchanged);

        // Compute the edit operations.
        $edits = array();
        $xi = $yi = 0;
        while ($xi < $n_from || $yi < $n_to) {
            USE_ASSERTS_IN_WIKI && assert($yi < $n_to || $this->xchanged[$xi]);
            USE_ASSERTS_IN_WIKI && assert($xi < $n_from || $this->ychanged[$yi]);

            // Skip matching "snake".
            $copy = array();
            while ( $xi < $n_from && $yi < $n_to
                    && !$this->xchanged[$xi] && !$this->ychanged[$yi]) {
                $copy[] = $from_lines[$xi++];
                ++$yi;
            }
            if ($copy)
                $edits[] = new _WikiDiffOp_Copy($copy);

            // Find deletes & adds.
            $delete = array();
            while ($xi < $n_from && $this->xchanged[$xi])
                $delete[] = $from_lines[$xi++];

            $add = array();
            while ($yi < $n_to && $this->ychanged[$yi])
                $add[] = $to_lines[$yi++];

            if ($delete && $add)
                $edits[] = new _WikiDiffOp_Change($delete, $add);
            elseif ($delete)
                $edits[] = new _WikiDiffOp_Delete($delete);
            elseif ($add)
                $edits[] = new _WikiDiffOp_Add($add);
        }
        return $edits;
    }


    /* Divide the Largest Common Subsequence (LCS) of the sequences
     * [XOFF, XLIM) and [YOFF, YLIM) into NCHUNKS approximately equally
     * sized segments.
     *
     * Returns (LCS, PTS).    LCS is the length of the LCS. PTS is an
     * array of NCHUNKS+1 (X, Y) indexes giving the diving points between
     * sub sequences.  The first sub-sequence is contained in [X0, X1),
     * [Y0, Y1), the second in [X1, X2), [Y1, Y2) and so on.  Note
     * that (X0, Y0) == (XOFF, YOFF) and
     * (X[NCHUNKS], Y[NCHUNKS]) == (XLIM, YLIM).
     *
     * This function assumes that the first lines of the specified portions
     * of the two files do not match, and likewise that the last lines do not
     * match.  The caller must trim matching lines from the beginning and end
     * of the portions it is going to specify.
     */
    function _diag ($xoff, $xlim, $yoff, $ylim, $nchunks) {
    $flip = false;

    if ($xlim - $xoff > $ylim - $yoff) {
        // Things seems faster (I'm not sure I understand why)
            // when the shortest sequence in X.
            $flip = true;
        list ($xoff, $xlim, $yoff, $ylim)
        = array( $yoff, $ylim, $xoff, $xlim);
        }

    if ($flip)
        for ($i = $ylim - 1; $i >= $yoff; $i--)
        $ymatches[$this->xv[$i]][] = $i;
    else
        for ($i = $ylim - 1; $i >= $yoff; $i--)
        $ymatches[$this->yv[$i]][] = $i;

    $this->lcs = 0;
    $this->seq[0]= $yoff - 1;
    $this->in_seq = array();
    $ymids[0] = array();

    $numer = $xlim - $xoff + $nchunks - 1;
    $x = $xoff;
    for ($chunk = 0; $chunk < $nchunks; $chunk++) {
        if ($chunk > 0)
        for ($i = 0; $i <= $this->lcs; $i++)
            $ymids[$i][$chunk-1] = $this->seq[$i];

        $x1 = $xoff + (int)(($numer + ($xlim-$xoff)*$chunk) / $nchunks);
        for ( ; $x < $x1; $x++) {
                $line = $flip ? $this->yv[$x] : $this->xv[$x];
                if (empty($ymatches[$line]))
            continue;
        $matches = $ymatches[$line];
                reset($matches);
        foreach ($matches as $y)
            if (empty($this->in_seq[$y])) {
            $k = $this->_lcs_pos($y);
            USE_ASSERTS_IN_WIKI && assert($k > 0);
            $ymids[$k] = $ymids[$k-1];
            break;
            }
        foreach ($matches as $y) {
            if ($y > $this->seq[$k-1]) {
            USE_ASSERTS_IN_WIKI && assert($y < $this->seq[$k]);
            // Optimization: this is a common case:
            //    next match is just replacing previous match.
            $this->in_seq[$this->seq[$k]] = false;
            $this->seq[$k] = $y;
            $this->in_seq[$y] = 1;
                    }
            else if (empty($this->in_seq[$y])) {
            $k = $this->_lcs_pos($y);
            USE_ASSERTS_IN_WIKI && assert($k > 0);
            $ymids[$k] = $ymids[$k-1];
                    }
                }
            }
        }

    $seps[] = $flip ? array($yoff, $xoff) : array($xoff, $yoff);
    $ymid = $ymids[$this->lcs];
    for ($n = 0; $n < $nchunks - 1; $n++) {
        $x1 = $xoff + (int)(($numer + ($xlim - $xoff) * $n) / $nchunks);
        $y1 = $ymid[$n] + 1;
        $seps[] = $flip ? array($y1, $x1) : array($x1, $y1);
        }
    $seps[] = $flip ? array($ylim, $xlim) : array($xlim, $ylim);

    return array($this->lcs, $seps);
    }

    function _lcs_pos ($ypos) {
    $end = $this->lcs;
    if ($end == 0 || $ypos > $this->seq[$end]) {
        $this->seq[++$this->lcs] = $ypos;
        $this->in_seq[$ypos] = 1;
        return $this->lcs;
        }

    $beg = 1;
    while ($beg < $end) {
        $mid = (int)(($beg + $end) / 2);
        if ( $ypos > $this->seq[$mid] )
        $beg = $mid + 1;
        else
        $end = $mid;
        }

    USE_ASSERTS_IN_WIKI && assert($ypos != $this->seq[$end]);

    $this->in_seq[$this->seq[$end]] = false;
    $this->seq[$end] = $ypos;
    $this->in_seq[$ypos] = 1;
    return $end;
    }

    /* Find LCS of two sequences.
     *
     * The results are recorded in the vectors $this->{x,y}changed[], by
     * storing a 1 in the element for each line that is an insertion
     * or deletion (ie. is not in the LCS).
     *
     * The subsequence of file 0 is [XOFF, XLIM) and likewise for file 1.
     *
     * Note that XLIM, YLIM are exclusive bounds.
     * All line numbers are origin-0 and discarded lines are not counted.
     */
    function _compareseq ($xoff, $xlim, $yoff, $ylim) {
    // Slide down the bottom initial diagonal.
    while ($xoff < $xlim && $yoff < $ylim
               && $this->xv[$xoff] == $this->yv[$yoff]) {
        ++$xoff;
        ++$yoff;
        }

    // Slide up the top initial diagonal.
    while ($xlim > $xoff && $ylim > $yoff
               && $this->xv[$xlim - 1] == $this->yv[$ylim - 1]) {
        --$xlim;
        --$ylim;
        }

    if ($xoff == $xlim || $yoff == $ylim)
        $lcs = 0;
    else {
        // This is ad hoc but seems to work well.
        //$nchunks = sqrt(min($xlim - $xoff, $ylim - $yoff) / 2.5);
        //$nchunks = max(2,min(8,(int)$nchunks));
        $nchunks = min(7, $xlim - $xoff, $ylim - $yoff) + 1;
        list ($lcs, $seps)
        = $this->_diag($xoff,$xlim,$yoff, $ylim,$nchunks);
        }

    if ($lcs == 0) {
        // X and Y sequences have no common subsequence:
        // mark all changed.
        while ($yoff < $ylim)
        $this->ychanged[$this->yind[$yoff++]] = 1;
        while ($xoff < $xlim)
        $this->xchanged[$this->xind[$xoff++]] = 1;
        }
    else {
        // Use the partitions to split this problem into subproblems.
        reset($seps);
        $pt1 = $seps[0];
        while ($pt2 = next($seps)) {
        $this->_compareseq ($pt1[0], $pt2[0], $pt1[1], $pt2[1]);
        $pt1 = $pt2;
            }
        }
    }

    /* Adjust inserts/deletes of identical lines to join changes
     * as much as possible.
     *
     * We do something when a run of changed lines include a
     * line at one end and has an excluded, identical line at the other.
     * We are free to choose which identical line is included.
     * `compareseq' usually chooses the one at the beginning,
     * but usually it is cleaner to consider the following identical line
     * to be the "change".
     *
     * This is extracted verbatim from analyze.c (GNU diffutils-2.7).
     */
    function _shift_boundaries ($lines, &$changed, $other_changed) {
    $i = 0;
    $j = 0;

    USE_ASSERTS_IN_WIKI && assert('sizeof($lines) == sizeof($changed)');
    $len = sizeof($lines);
    $other_len = sizeof($other_changed);

    while (1) {
        /*
         * Scan forwards to find beginning of another run of changes.
         * Also keep track of the corresponding point in the other file.
         *
         * Throughout this code, $i and $j are adjusted together so that
         * the first $i elements of $changed and the first $j elements
         * of $other_changed both contain the same number of zeros
         * (unchanged lines).
         * Furthermore, $j is always kept so that $j == $other_len or
         * $other_changed[$j] == false.
         */
        while ($j < $other_len && $other_changed[$j])
        $j++;

        while ($i < $len && ! $changed[$i]) {
        USE_ASSERTS_IN_WIKI && assert('$j < $other_len && ! $other_changed[$j]');
        $i++; $j++;
        while ($j < $other_len && $other_changed[$j])
            $j++;
            }

        if ($i == $len)
        break;

        $start = $i;

        // Find the end of this run of changes.
        while (++$i < $len && $changed[$i])
        continue;

        do {
        /*
         * Record the length of this run of changes, so that
         * we can later determine whether the run has grown.
         */
        $runlength = $i - $start;

        /*
         * Move the changed region back, so long as the
         * previous unchanged line matches the last changed one.
         * This merges with previous changed regions.
         */
        while ($start > 0 && $lines[$start - 1] == $lines[$i - 1]) {
            $changed[--$start] = 1;
            $changed[--$i] = false;
            while ($start > 0 && $changed[$start - 1])
            $start--;
            USE_ASSERTS_IN_WIKI && assert('$j > 0');
            while ($other_changed[--$j])
            continue;
            USE_ASSERTS_IN_WIKI && assert('$j >= 0 && !$other_changed[$j]');
                }

        /*
         * Set CORRESPONDING to the end of the changed run, at the last
         * point where it corresponds to a changed run in the other file.
         * CORRESPONDING == LEN means no such point has been found.
         */
        $corresponding = $j < $other_len ? $i : $len;

        /*
         * Move the changed region forward, so long as the
         * first changed line matches the following unchanged one.
         * This merges with following changed regions.
         * Do this second, so that if there are no merges,
         * the changed region is moved forward as far as possible.
         */
        while ($i < $len && $lines[$start] == $lines[$i]) {
            $changed[$start++] = false;
            $changed[$i++] = 1;
            while ($i < $len && $changed[$i])
            $i++;

            USE_ASSERTS_IN_WIKI && assert('$j < $other_len && ! $other_changed[$j]');
            $j++;
            if ($j < $other_len && $other_changed[$j]) {
            $corresponding = $i;
            while ($j < $other_len && $other_changed[$j])
                $j++;
                    }
                }
            } while ($runlength != $i - $start);

        /*
         * If possible, move the fully-merged run of changes
         * back to a corresponding run in the other file.
         */
        while ($corresponding < $i) {
        $changed[--$start] = 1;
        $changed[--$i] = 0;
        USE_ASSERTS_IN_WIKI && assert('$j > 0');
        while ($other_changed[--$j])
            continue;
        USE_ASSERTS_IN_WIKI && assert('$j >= 0 && !$other_changed[$j]');
            }
        }
    }
}

/**
 * Class representing a 'diff' between two sequences of strings.
 */
class WikiDiff
{
    var $edits;

    /**
     * Constructor.
     * Computes diff between sequences of strings.
     *
     * @param $from_lines array An array of strings.
     *          (Typically these are lines from a file.)
     * @param $to_lines array An array of strings.
     */
    public function __construct($from_lines, $to_lines) {
        $eng = new _WikiDiffEngine;
        $this->edits = $eng->diff($from_lines, $to_lines);
        //$this->_check($from_lines, $to_lines);
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function WikiDiff($from_lines, $to_lines) {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct($from_lines, $to_lines);
    }

    /**
     * Compute reversed WikiDiff.
     *
     * SYNOPSIS:
     *
     *    $diff = new WikiDiff($lines1, $lines2);
     *    $rev = $diff->reverse();
     * @return object A WikiDiff object representing the inverse of the
     *                  original diff.
     */
    function reverse () {
    $rev = $this;
        $rev->edits = array();
        foreach ($this->edits as $edit) {
            $rev->edits[] = $edit->reverse();
        }
    return $rev;
    }

    /**
     * Check for empty diff.
     *
     * @return bool True iff two sequences were identical.
     */
    function isEmpty () {
        foreach ($this->edits as $edit) {
            if ($edit->type != 'copy')
                return false;
        }
        return true;
    }

    /**
     * Compute the length of the Longest Common Subsequence (LCS).
     *
     * This is mostly for diagnostic purposed.
     *
     * @return int The length of the LCS.
     */
    function lcs () {
    $lcs = 0;
        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy')
                $lcs += sizeof($edit->orig);
        }
    return $lcs;
    }

    /**
     * Get the original set of lines.
     *
     * This reconstructs the $from_lines parameter passed to the
     * constructor.
     *
     * @return array The original sequence of strings.
     */
    function orig() {
        $lines = array();

        foreach ($this->edits as $edit) {
            if ($edit->orig)
                array_splice($lines, sizeof($lines), 0, $edit->orig);
        }
        return $lines;
    }

    /**
     * Get the closing set of lines.
     *
     * This reconstructs the $to_lines parameter passed to the
     * constructor.
     *
     * @return array The sequence of strings.
     */
    function closing() {
        $lines = array();

        foreach ($this->edits as $edit) {
            if ($edit->closing)
                array_splice($lines, sizeof($lines), 0, $edit->closing);
        }
        return $lines;
    }

    /**
     * Check a WikiDiff for validity.
     *
     * This is here only for debugging purposes.
     */
    function _check ($from_lines, $to_lines) {
        if (serialize($from_lines) != serialize($this->orig()))
            trigger_error("Reconstructed original doesn't match", E_USER_ERROR);
        if (serialize($to_lines) != serialize($this->closing()))
            trigger_error("Reconstructed closing doesn't match", E_USER_ERROR);

        $rev = $this->reverse();
        if (serialize($to_lines) != serialize($rev->orig()))
            trigger_error("Reversed original doesn't match", E_USER_ERROR);
        if (serialize($from_lines) != serialize($rev->closing()))
            trigger_error("Reversed closing doesn't match", E_USER_ERROR);


        $prevtype = 'none';
        foreach ($this->edits as $edit) {
            if ( $prevtype == $edit->type )
                trigger_error("Edit sequence is non-optimal", E_USER_ERROR);
            $prevtype = $edit->type;
        }

        $lcs = $this->lcs();
        trigger_error("WikiDiff okay: LCS = $lcs", E_USER_NOTICE);
    }
}

/**
 * FIXME: bad name.
 */

class MappedWikiDiff
extends WikiDiff
{
    /**
     * Constructor.
     *
     * Computes diff between sequences of strings.
     *
     * This can be used to compute things like
     * case-insensitve diffs, or diffs which ignore
     * changes in white-space.
     *
     * @param $from_lines array An array of strings.
     *    (Typically these are lines from a file.)
     *
     * @param $to_lines array An array of strings.
     *
     * @param $mapped_from_lines array This array should
     *    have the same size number of elements as $from_lines.
     *    The elements in $mapped_from_lines and
     *    $mapped_to_lines are what is actually compared
     *    when computing the diff.
     *
     * @param $mapped_to_lines array This array should
     *    have the same number of elements as $to_lines.
     */
    public function __construct($from_lines, $to_lines,
                        $mapped_from_lines, $mapped_to_lines) {

        assert(sizeof($from_lines) == sizeof($mapped_from_lines));
        assert(sizeof($to_lines) == sizeof($mapped_to_lines));

        parent::__construct($mapped_from_lines, $mapped_to_lines);

        $xi = $yi = 0;
        for ($i = 0; $i < sizeof($this->edits); $i++) {
            $orig = &$this->edits[$i]->orig;
            if (is_array($orig)) {
                $orig = array_slice($from_lines, $xi, sizeof($orig));
                $xi += sizeof($orig);
            }

            $closing = &$this->edits[$i]->closing;
            if (is_array($closing)) {
                $closing = array_slice($to_lines, $yi, sizeof($closing));
                $yi += sizeof($closing);
            }
        }
    }
}

/**
 * A class to format WikiDiffs
 *
 * This class formats the diff in classic diff format.
 * It is intended that this class be customized via inheritance,
 * to obtain fancier outputs.
 */
class WikiDiffFormatter
{
    /**
     * Number of leading context "lines" to preserve.
     *
     * This should be left at zero for this class, but subclasses
     * may want to set this to other values.
     */
    var $leading_context_lines = 0;

    /**
     * Number of trailing context "lines" to preserve.
     *
     * This should be left at zero for this class, but subclasses
     * may want to set this to other values.
     */
    var $trailing_context_lines = 0;

    /**
     * Format a diff.
     *
     * @param $diff object A WikiDiff object.
     * @return string The formatted output.
     */
    function format($diff) {

        $xi = $yi = 1;
        $block = false;
        $context = array();

        $nlead = $this->leading_context_lines;
        $ntrail = $this->trailing_context_lines;

        $this->_start_diff();

        foreach ($diff->edits as $edit) {
            if ($edit->type == 'copy') {
                if (is_array($block)) {
                    if (sizeof($edit->orig) <= $nlead + $ntrail) {
                        $block[] = $edit;
                    }
                    else{
                        if ($ntrail) {
                            $context = array_slice($edit->orig, 0, $ntrail);
                            $block[] = new _WikiWikiDiffOp_Copy($context);
                        }
                        $this->_block($x0, $ntrail + $xi - $x0,
                                      $y0, $ntrail + $yi - $y0,
                                      $block);
                        $block = false;
                    }
                }
                $context = $edit->orig;
            }
            else {
                if (! is_array($block)) {
                    $context = array_slice($context, sizeof($context) - $nlead);
                    $x0 = $xi - sizeof($context);
                    $y0 = $yi - sizeof($context);
                    $block = array();
                    if ($context)
                        $block[] = new _WikiWikiDiffOp_Copy($context);
                }
                $block[] = $edit;
            }

            if ($edit->orig)
                $xi += sizeof($edit->orig);
            if ($edit->closing)
                $yi += sizeof($edit->closing);
        }

        if (is_array($block))
            $this->_block($x0, $xi - $x0,
                          $y0, $yi - $y0,
                          $block);

        return $this->_end_diff();
    }

    function _block($xbeg, $xlen, $ybeg, $ylen, &$edits) {
        $this->_start_block($this->_block_header($xbeg, $xlen, $ybeg, $ylen));
        foreach ($edits as $edit) {
            if ($edit->type == 'copy')
                $this->_context($edit->orig);
            elseif ($edit->type == 'add')
                $this->_added($edit->closing);
            elseif ($edit->type == 'delete')
                $this->_deleted($edit->orig);
            elseif ($edit->type == 'change')
                $this->_changed($edit->orig, $edit->closing);
            else
                trigger_error("Unknown edit type", E_USER_ERROR);
        }
        $this->_end_block();
    }

    function _start_diff() {
        ob_start();
    }

    function _end_diff() {
        $val = ob_get_contents();
        ob_end_clean();
        return $val;
    }

    function _block_header($xbeg, $xlen, $ybeg, $ylen) {
        if ($xlen > 1)
            $xbeg .= "," . ($xbeg + $xlen - 1);
        if ($ylen > 1)
            $ybeg .= "," . ($ybeg + $ylen - 1);

        return $xbeg . ($xlen ? ($ylen ? 'c' : 'd') : 'a') . $ybeg;
    }

    function _start_block($header) {
        echo $header;
    }

    function _end_block() {
    }

    function _lines($lines, $prefix = ' ') {
        foreach ($lines as $line)
            echo "$prefix $line\n";
    }

    function _context($lines) {
        $this->_lines($lines);
    }

    function _added($lines) {
        $this->_lines($lines, ">");
    }
    function _deleted($lines) {
        $this->_lines($lines, "<");
    }

    function _changed($orig, $closing) {
        $this->_deleted($orig);
        echo "---\n";
        $this->_added($closing);
    }
}


/**
 *    Additions by Axel Boldt follow, partly taken from diff.php, phpwiki-1.3.3
 *
 */

define('NBSP', '&#160;');            // iso-8859-x non-breaking space.

class _WikiHWLDF_WordAccumulator {
    public function __construct () {
        $this->_lines = array();
        $this->_line = '';
        $this->_group = '';
        $this->_tag = '';
    }

    function _flushGroup ($new_tag) {
        if ($this->_group !== '') {
      if ($this->_tag == 'mark')
            $this->_line .= '<span class="diffchange">'.$this->_group.'</span>';
      else
        $this->_line .= $this->_group;
    }
        $this->_group = '';
        $this->_tag = $new_tag;
    }

    function _flushLine ($new_tag) {
        $this->_flushGroup($new_tag);
        if ($this->_line != '')
            $this->_lines[] = $this->_line;
        $this->_line = '';
    }

    function addWords ($words, $tag = '') {
        if ($tag != $this->_tag)
            $this->_flushGroup($tag);

        foreach ($words as $word) {
            // new-line should only come as first char of word.
            if ($word == '')
                continue;
            if ($word[0] == "\n") {
                $this->_group .= NBSP;
                $this->_flushLine($tag);
                $word = substr($word, 1);
            }
            assert(!strstr($word, "\n"));
            $this->_group .= $word;
        }
    }

    function getLines() {
        $this->_flushLine('~done');
        return $this->_lines;
    }
}

class WordLevelWikiDiff extends MappedWikiDiff
{
    function __construct ($orig_lines, $closing_lines) {
        list ($orig_words, $orig_stripped) = $this->_split($orig_lines);
        list ($closing_words, $closing_stripped) = $this->_split($closing_lines);


        parent::__construct($orig_words, $closing_words,
                          $orig_stripped, $closing_stripped);
    }

    function _split($lines) {
        // FIXME: fix POSIX char class.
#         if (!preg_match_all('/ ( [^\S\n]+ | [[:alnum:]]+ | . ) (?: (?!< \n) [^\S\n])? /xs',
        if (!preg_match_all('/ ( [^\S\n]+ | [0-9_A-Za-z\x80-\xff]+ | . ) (?: (?!< \n) [^\S\n])? /xs',
                            implode("\n", $lines),
                            $m)) {
            return array(array(''), array(''));
        }
        return array($m[0], $m[1]);
    }

    function orig () {
        $orig = new _WikiHWLDF_WordAccumulator;

        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy')
                $orig->addWords($edit->orig);
            elseif ($edit->orig)
                $orig->addWords($edit->orig, 'mark');
        }
        return $orig->getLines();
    }

    function closing () {
        $closing = new _WikiHWLDF_WordAccumulator;

        foreach ($this->edits as $edit) {
            if ($edit->type == 'copy')
                $closing->addWords($edit->closing);
            elseif ($edit->closing)
                $closing->addWords($edit->closing, 'mark');
        }
        return $closing->getLines();
    }
}

/**
 * @TODO: Doc this class
 */
class TableWikiDiffFormatter extends WikiDiffFormatter
{
    var $htmltable = array();

    public function __construct() {
        $this->leading_context_lines = 2;
        $this->trailing_context_lines = 2;
    }

    function _block_header( $xbeg, $xlen, $ybeg, $ylen) {

    }

    function _start_block ($header) {

    }

    function _end_block() {

    }

    function _lines($lines, $prefix=' ', $color="white") {

    }

    function _added($lines) {
        global $htmltable;
        foreach ($lines as $line) {
            $htmltable[] = array('','+','<div class="wiki_diffadd">'.$line.'</div>');
        }
    }

    function _deleted($lines) {
        global $htmltable;
        foreach ($lines as $line) {
            $htmltable[] = array('<div class="wiki_diffdel">'.$line.'</div>','-','');
        }
    }

    function _context($lines) {
        global $htmltable;
        foreach ($lines as $line) {
            $htmltable[] = array($line,'',$line);
        }
    }

    function _changed( $orig, $closing ) {
        global $htmltable;
        $diff = new WordLevelWikiDiff( $orig, $closing );
        $del = $diff->orig();
        $add = $diff->closing();

        while ( $line = array_shift( $del ) ) {
            $aline = array_shift( $add );
            $htmltable[] = array('<div class="wiki_diffdel">'.$line.'</div>','-','<div class="wiki_diffadd">'.$aline.'</div>');
        }
        $this->_added( $add ); # If any leftovers
    }

    function get_result() {
        global $htmltable;
        return $htmltable;
    }

}


/**
 *    Wikipedia Table style diff formatter.
 *
 */
class TableWikiDiffFormatterOld extends WikiDiffFormatter
{
    function TableWikiDiffFormatter() {
        $this->leading_context_lines = 2;
        $this->trailing_context_lines = 2;
    }

    function _block_header( $xbeg, $xlen, $ybeg, $ylen ) {
        $l1 = wfMsg( "lineno", $xbeg );
        $l2 = wfMsg( "lineno", $ybeg );

        $r = '<tr><td colspan="2" align="left"><strong>'.$l1."</strong></td>\n" .
          '<td colspan="2" align="left"><strong>'.$l2."</strong></td></tr>\n";
        return $r;
    }

    function _start_block( $header ) {
        global $wgOut;
        $wgOut->addHTML( $header );
    }

    function _end_block() {
    }

    function _lines( $lines, $prefix=' ', $color="white" ) {
    }

    function addedLine( $line ) {
        return '<td>+</td><td class="diff-addedline">' .
          $line.'</td>';
    }

    function deletedLine( $line ) {
        return '<td>-</td><td class="diff-deletedline">' .
          $line.'</td>';
    }

    function emptyLine() {
        return '<td colspan="2">&nbsp;</td>';
    }

    function contextLine( $line ) {
        return '<td> </td><td class="diff-context">'.$line.'</td>';
    }

    function _added($lines) {
        global $wgOut;
        foreach ($lines as $line) {
            $wgOut->addHTML( '<tr>' . $this->emptyLine() .
              $this->addedLine( $line ) . "</tr>\n" );
        }
    }

    function _deleted($lines) {
        global $wgOut;
        foreach ($lines as $line) {
            $wgOut->addHTML( '<tr>' . $this->deletedLine( $line ) .
              $this->emptyLine() . "</tr>\n" );
        }
    }

    function _context( $lines ) {
        global $wgOut;
        foreach ($lines as $line) {
            $wgOut->addHTML( '<tr>' . $this->contextLine( $line ) .
              $this->contextLine( $line ) . "</tr>\n" );
        }
    }

    function _changed( $orig, $closing ) {
        global $wgOut;
        $diff = new WordLevelWikiDiff( $orig, $closing );
        $del = $diff->orig();
        $add = $diff->closing();

        while ( $line = array_shift( $del ) ) {
            $aline = array_shift( $add );
            $wgOut->addHTML( '<tr>' . $this->deletedLine( $line ) .
              $this->addedLine( $aline ) . "</tr>\n" );
        }
        $this->_added( $add ); # If any leftovers
    }
}

