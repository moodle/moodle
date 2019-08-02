<?php
/**
 * The Text_Flowed:: class provides common methods for manipulating text
 * using the encoding described in RFC 3676 ('flowed' text).
 *
 * This class is based on the Text::Flowed perl module (Version 0.14) found
 * in the CPAN perl repository.  This module is released under the Perl
 * license, which is compatible with the LGPL.
 *
 * Copyright 2002-2003 Philip Mak
 * Copyright 2004-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Text_Flowed
 */
class Horde_Text_Flowed
{
    /**
     * The maximum length that a line is allowed to be (unless faced with
     * with a word that is unreasonably long). This class will re-wrap a
     * line if it exceeds this length.
     *
     * @var integer
     */
    protected $_maxlength = 78;

    /**
     * When this class wraps a line, the newly created lines will be split
     * at this length.
     *
     * @var integer
     */
    protected $_optlength = 72;

    /**
     * The text to be formatted.
     *
     * @var string
     */
    protected $_text;

    /**
     * The cached output of the formatting.
     *
     * @var array
     */
    protected $_output = array();

    /**
     * The format of the data in $_output.
     *
     * @var string
     */
    protected $_formattype = null;

    /**
     * The character set of the text.
     *
     * @var string
     */
    protected $_charset;

    /**
     * Convert text using DelSp?
     *
     * @var boolean
     */
    protected $_delsp = false;

    /**
     * Constructor.
     *
     * @param string $text     The text to process.
     * @param string $charset  The character set of $text.
     */
    public function __construct($text, $charset = 'UTF-8')
    {
        $this->_text = $text;
        $this->_charset = $charset;
    }

    /**
     * Set the maximum length of a line of text.
     *
     * @param integer $max  A new value for $_maxlength.
     */
    public function setMaxLength($max)
    {
        $this->_maxlength = $max;
    }

    /**
     * Set the optimal length of a line of text.
     *
     * @param integer $max  A new value for $_optlength.
     */
    public function setOptLength($opt)
    {
        $this->_optlength = $opt;
    }

    /**
     * Set whether to format text using DelSp.
     *
     * @param boolean $delsp  Use DelSp?
     */
    public function setDelSp($delsp)
    {
        $this->_delsp = (bool)$delsp;
    }

    /**
     * Reformats the input string, where the string is 'format=flowed' plain
     * text as described in RFC 2646.
     *
     * @param boolean $quote  Add level of quoting to each line?
     *
     * @return string  The text converted to RFC 2646 'fixed' format.
     */
    public function toFixed($quote = false)
    {
        $txt = '';

        $this->_reformat(false, $quote);
        $lines = count($this->_output) - 1;
        foreach ($this->_output as $no => $line) {
            $txt .= $line['text'] . (($lines == $no) ? '' : "\n");
        }

        return $txt;
    }

    /**
     * Reformats the input string, and returns the output in an array format
     * with quote level information.
     *
     * @param boolean $quote  Add level of quoting to each line?
     *
     * @return array  An array of arrays with the following elements:
     * <pre>
     * 'level' - The quote level of the current line.
     * 'text'  - The text for the current line.
     * </pre>
     */
    public function toFixedArray($quote = false)
    {
        $this->_reformat(false, $quote);
        return $this->_output;
    }

    /**
     * Reformats the input string, where the string is 'format=fixed' plain
     * text as described in RFC 2646.
     *
     * @param boolean $quote  Add level of quoting to each line?
     * @param array $opts     Additional options:
     * <pre>
     * 'nowrap' - (boolean) If true, does not wrap unquoted lines.
     *            DEFAULT: false
     * </pre>
     *
     * @return string  The text converted to RFC 2646 'flowed' format.
     */
    public function toFlowed($quote = false, array $opts = array())
    {
        $txt = '';

        $this->_reformat(true, $quote, empty($opts['nowrap']));
        foreach ($this->_output as $line) {
            $txt .= $line['text'] . "\n";
        }

        return $txt;
    }

    /**
     * Reformats the input string, where the string is 'format=flowed' plain
     * text as described in RFC 2646.
     *
     * @param boolean $toflowed  Convert to flowed?
     * @param boolean $quote     Add level of quoting to each line?
     * @param boolean $wrap      Wrap unquoted lines?
     */
    protected function _reformat($toflowed, $quote, $wrap = true)
    {
        $format_type = implode('|', array($toflowed, $quote));
        if ($format_type == $this->_formattype) {
            return;
        }

        $this->_output = array();
        $this->_formattype = $format_type;

        /* Set variables used in regexps. */
        $delsp = ($toflowed && $this->_delsp) ? 1 : 0;
        $opt = $this->_optlength - 1 - $delsp;

        /* Process message line by line. */
        $text = preg_split("/\r?\n/", $this->_text);
        $text_count = count($text) - 1;
        $skip = 0;

        foreach ($text as $no => $line) {
            if ($skip) {
                --$skip;
                continue;
            }

            /* Per RFC 2646 [4.3], the 'Usenet Signature Convention' line
             * (DASH DASH SP) is not considered flowed.  Watch for this when
             * dealing with potentially flowed lines. */

            /* The next three steps come from RFC 2646 [4.2]. */
            /* STEP 1: Determine quote level for line. */
            if (($num_quotes = $this->_numquotes($line))) {
                $line = substr($line, $num_quotes);
            }

            /* Only combine lines if we are converting to flowed or if the
             * current line is quoted. */
            if (!$toflowed || $num_quotes) {
                /* STEP 2: Remove space stuffing from line. */
                $line = $this->_unstuff($line);

                /* STEP 3: Should we interpret this line as flowed?
                 * While line is flowed (not empty and there is a space
                 * at the end of the line), and there is a next line, and the
                 * next line has the same quote depth, add to the current
                 * line. A line is not flowed if it is a signature line. */
                if ($line != '-- ') {
                    while (!empty($line) &&
                           (substr($line, -1) == ' ') &&
                           ($text_count != $no) &&
                           ($this->_numquotes($text[$no + 1]) == $num_quotes)) {
                        /* If DelSp is yes and this is flowed input, we need to
                         * remove the trailing space. */
                        if (!$toflowed && $this->_delsp) {
                            $line = substr($line, 0, -1);
                        }
                        $line .= $this->_unstuff(substr($text[++$no], $num_quotes));
                        ++$skip;
                    }
                }
            }

            /* Ensure line is fixed, since we already joined all flowed
             * lines. Remove all trailing ' ' from the line. */
            if ($line != '-- ') {
                $line = rtrim($line);
            }

            /* Increment quote depth if we're quoting. */
            if ($quote) {
                $num_quotes++;
            }

            /* The quote prefix for the line. */
            $quotestr = str_repeat('>', $num_quotes);

            if (empty($line)) {
                /* Line is empty. */
                $this->_output[] = array('text' => $quotestr, 'level' => $num_quotes);
            } elseif ((!$wrap && !$num_quotes) ||
                      empty($this->_maxlength) ||
                      ((Horde_String::length($line, $this->_charset) + $num_quotes) <= $this->_maxlength)) {
                /* Line does not require rewrapping. */
                $this->_output[] = array('text' => $quotestr . $this->_stuff($line, $num_quotes, $toflowed), 'level' => $num_quotes);
            } else {
                $min = $num_quotes + 1;

                /* Rewrap this paragraph. */
                while ($line) {
                    /* Stuff and re-quote the line. */
                    $line = $quotestr . $this->_stuff($line, $num_quotes, $toflowed);
                    $line_length = Horde_String::length($line, $this->_charset);
                    if ($line_length <= $this->_optlength) {
                        /* Remaining section of line is short enough. */
                        $this->_output[] = array('text' => $line, 'level' => $num_quotes);
                        break;
                    } else {
                        $regex = array();
                        if ($min <= $opt) {
                            $regex[] = '^(.{' . $min . ',' . $opt . '}) (.*)';
                        }
                        if ($min <= $this->_maxlength) {
                            $regex[] = '^(.{' . $min . ',' . $this->_maxlength . '}) (.*)';
                        }
                        $regex[] = '^(.{' . $min . ',})? (.*)';

                        if ($m = Horde_String::regexMatch($line, $regex, $this->_charset)) {
                            /* We need to wrap text at a certain number of
                             * *characters*, not a certain number of *bytes*;
                             * thus the need for a multibyte capable regex.
                             * If a multibyte regex isn't available, we are
                             * stuck with preg_match() (the function will
                             * still work - are just left with shorter rows
                             * than expected if multibyte characters exist in
                             * the row).
                             *
                             * 1. Try to find a string as long as _optlength.
                             * 2. Try to find a string as long as _maxlength.
                             * 3. Take the first word. */
                            if (empty($m[1])) {
                                $m[1] = $m[2];
                                $m[2] = '';
                            }
                            $this->_output[] = array('text' => $m[1] . ' ' . (($delsp) ? ' ' : ''), 'level' => $num_quotes);
                            $line = $m[2];
                        } elseif ($line_length > 998) {
                            /* One excessively long word left on line.  Be
                             * absolutely sure it does not exceed 998
                             * characters in length or else we must
                             * truncate. */
                            $this->_output[] = array('text' => Horde_String::substr($line, 0, 998, $this->_charset), 'level' => $num_quotes);
                            $line = Horde_String::substr($line, 998, null, $this->_charset);
                        } else {
                            $this->_output[] = array('text' => $line, 'level' => $num_quotes);
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Returns the number of leading '>' characters in the text input.
     * '>' characters are defined by RFC 2646 to indicate a quoted line.
     *
     * @param string $text  The text to analyze.
     *
     * @return integer  The number of leading quote characters.
     */
    protected function _numquotes($text)
    {
        return strspn($text, '>');
    }

    /**
     * Space-stuffs if it starts with ' ' or '>' or 'From ', or if
     * quote depth is non-zero (for aesthetic reasons so that there is a
     * space after the '>').
     *
     * @param string $text        The text to stuff.
     * @param string $num_quotes  The quote-level of this line.
     * @param boolean $toflowed   Are we converting to flowed text?
     *
     * @return string  The stuffed text.
     */
    protected function _stuff($text, $num_quotes, $toflowed)
    {
        return ($toflowed && ($num_quotes || preg_match("/^(?: |>|From |From$)/", $text)))
            ? ' ' . $text
            : $text;
    }

    /**
     * Unstuffs a space stuffed line.
     *
     * @param string $text  The text to unstuff.
     *
     * @return string  The unstuffed text.
     */
    protected function _unstuff($text)
    {
        return (!empty($text) && ($text[0] == ' '))
            ? substr($text, 1)
            : $text;
    }

}
