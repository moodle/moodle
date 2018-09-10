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
 * Dump structured data, i.e., Objects and Arrays, in either plain text or html.
 *
 * This is a class wrapper for a couple of utility routines that I use
 * all the time.  It's handier to have them as a class.
 *
 * @author Dick Munroe <munroe@csworks.com> original package StructuredDataDumper
 * @copyright copyright @ by Dick Munroe, 2004
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package mod_game
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This is a class wrapper for a couple of utility routines that I use all the time.
 *
 * @author Dick Munroe <munroe@csworks.com>
 * @copyright copyright @ by Dick Munroe, 2004
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package mod_game
 */
class sdd {
    /** @var HTML to be generated flag. */
    protected $m_htmlflag;

    /**
     * Constructor.
     *
     * @param boolean $thehtmlflag [optional] True if HTML is to be generated.
     *                If omitted, $_SERVER is used to "guess" the state of
     *                    the HTML flag.  Be default, HTML is generated when
     *                    accessed by a web server.
     */
    public function init($thehtmlflag = null) {
        if ($thehtmlflag === null) {
            $thehtmlflag = (!empty($_SERVER['DOCUMENT_ROOT']));
        }

        $this->m_htmlflag = $thehtmlflag;
    }

    /**
     * Dump a structured variable.
     *
     * @param mixed $thevariable the variable to be dumped.
     * @param boolean $thehtmlflag [optional] true if HTML is to be generated,
     *                false if plain text is to be generated, null (default) if
     *                dump is to guess which to display.
     * @return string The data to be displayed.
     * @link http://www.php.net/manual/en/reserved.variables.php#reserved.variables.server Uses $_SERVER
     */
    public function dump(&$thevariable, $thehtmlflag = null) {
        if ($thehtmlflag === null) {
            if (empty($this)) {
                $thehtmlflag = (!empty($_SERVER['DOCUMENT_ROOT']));
            } else {
                if (is_subclass_of($this, "sdd")) {
                    $thehtmlflag = $this->m_htmlflag;
                } else {
                    $thehtmlflag = (!empty($_SERVER['DOCUMENT_ROOT']));
                }
            }
        }

        switch (gettype($thevariable)) {
            case 'array':
                return SDD::dArray($thevariable, $thehtmlflag);
            case 'object':
                return SDD::dObject($thevariable, $thehtmlflag);
            default:
                return SDD::scalar($thevariable, $thehtmlflag);
        }
    }

    /**
     * Dump the contents of an array.
     *
     * @param array $thearray the array whose contents are to be displayed.
     * @param boolean $thehtmlflag True if an HTML table is to be generated,
     *                false otherwise.
     * @param string $theindent [optional] Used by SDD::dArray during recursion
     *               to get indenting right.
     * @return string The display form of the array.
     */
    public function darray(&$thearray, $thehtmlflag, $theindent = "") {
        $theoutput = array();

        foreach ($thearray as $theindex => $thevalue) {
            if (is_array($thevalue)) {
                $thestring = ssd::dArray($thevalue, $thehtmlflag, $theindent . "    ");
                $theoutput[$theindex] = substr($thestring, 0, strlen($thestring) - 1);
            } else if (is_object($thevalue)) {
                $theoutput[$theindex] = $this->dobject($thevalue, $thehtmlflag);
            } else {
                $theoutput[$theindex] = ($thehtmlflag ? preg_replace('|<|s', '&lt;',
                    var_export($thevalue, true)) : var_export($thevalue, true));
            }
        }

        if ($thehtmlflag) {
            $thestring = "<table border=1>\n";
            $thestring .= "<tr><td align=left>Array (</td></tr>\n";

            foreach ($theoutput as $theindex => $thevariableoutput) {
                $thestring .= "<tr>\n<td align=right>$theindex = ></td><td align=left>\n$thevariableoutput\n</td>\n</tr>\n";
            }

            $thestring .= "<tr><td align=left>)</td></tr>\n";
            $thestring .= "</table>\n";
        } else {
            $thestring = "Array\n$theindent(\n";

            foreach ($theoutput as $theindex => $thevariableoutput) {
                $thestring .= "$theindent    [$theindex] => " . $thevariableoutput . "\n";
            }

            $thestring .= "$theindent)\n";
        }

        return $thestring;
    }

    /**
     * Dump the contents of an object.
     *
     * Provide a structured display of an object and all the
     * classes from which it was derived.  The contents of
     * the object is displayed from most derived to the base
     * class, in order.
     *
     * @param object $theobject the object to be dumped.
     * @param boolean $thehtmlflag true if HTML is to be generated.
     * @return string the display form of the object.
     */
    public function dobject(&$theobject, $thehtmlflag) {
        $theobjectvars = get_object_vars($theobject);

        /* Get the inheritance tree starting with the object and going
         * through all the parent classes from there.
         */

        $theclass = get_class($theobject);

        $theclasses[] = $theclass;

        while ($theclass = get_parent_class($theclass)) {
            $theclasses[] = $theclass;
        }

        /* Get all the class variables for each class in the inheritance
         * tree.  There will be some duplication, but we'll sort that out
         * in the output process.
         */

        foreach ($theclasses as $theclass) {
            $theclassvars[$theclass] = get_class_vars($theclass);
        }

        /* Put the inheritance tree from base class to most derived order
         * (this is how we get rid of duplication of the variable names)
         * Go through the object variables starting with the base class,
         * capture the output and delete the variable from the object
         * variables.
         */

        $theclasses = array_reverse($theclasses);

        $theoutput = array();

        foreach ($theclasses as $theclass) {
            $theoutput[$theclass] = array();

            foreach ($theclassvars[$theclass] as $thevariable => $value) {
                if (array_key_exists($thevariable, $theobjectvars)) {
                    if (is_array($theobjectvars[$thevariable])) {
                        $theoutput[$theclass][] = $thevariable . " = " .$this->darray($theobjectvars[$thevariable], $thehtmlflag);
                    } else if (is_object($theobjectvars[$thevariable])) {
                        $theoutput[$theclass][] = $thevariable . " = ".$this->dobject($theobjectvars[$thevariable], $thehtmlflag);
                    } else {
                        $theotput[$theclass][] = $thevariable . " = " .
                            ($thehtmlflag ? preg_replace('|<|s', '&lt;', var_export(
                            $theobjectvars[$thevariable], true)) : var_export($theobjectvars[$thevariable], true));
                    }

                    unset($theobjectvars[$thevariable]);
                }
            }
        }

        /* Put the classes back in most derived order for generating printable
         * output.
         */
        $theclasses = array_reverse($theclasses);

        if ($thehtmlflag) {
            $thestring = "<table>\n<thead>\n";

            foreach ($theclasses as $theclass) {
                $thestring .= "<th>\n$theclass\n</th>\n";
            }

            $thestring .= "</thead>\n<tr valign=top>\n";

            foreach ($theclasses as $theclass) {
                $thestring .= "<td>\n<table border=1>\n";

                foreach ($theoutput[$theclass] as $thevariableoutput) {
                    $thestring .= "<tr>\n<td>\n$thevariableoutput\n</td>\n</tr>\n";
                }

                $thestring .= "</table>\n</td>\n";
            }

            $thestring .= "</tr>\n</table>\n";
        } else {
            $classindent = "";

            $classdataindent = "    ";

            $thestring = "";

            foreach ($theclasses as $theclass) {
                $thestring .= "{$classindent}class $theclass\n\n";

                foreach ($theoutput[$theclass] as $thevariableoutput) {
                    $thestring .= "$classdataindent$thevariableoutput\n";
                }

                $thestring .= "\n";

                $classindent .= "    ";

                $classdataindent .= "    ";
            }
        }

        return $thestring;
    }

    /**
     * Generate context specific new line equivalents.
     *
     * @param integer $thecount [optional] the number of newlines.
     * @param boolean $thehtmlflag [optional] true if generating html newlines.
     *
     * @return string newlines.
     */
    public function newline($thecount = 1, $thehtmlflag = null) {
        if ($thehtmlflag === null) {
            if (empty($this)) {
                $thehtmlflag = (!empty($_SERVER['DOCUMENT_ROOT']));
            } else {
                if (is_subclass_of($this, "sdd")) {
                    $thehtmlflag = $this->m_htmlflag;
                } else {
                    $thehtmlflag = (!empty($_SERVER['DOCUMENT_ROOT']));
                }
            }
        }

        if ($thehtmlflag) {
            return str_repeat("<br />", max($thecount, 0)) . "\n";
        } else {
            return str_repeat("\n", max($thecount, 0));
        }
    }

    /**
     * Dump any scalar value
     *
     * @param mixed $thevariable the variable to be dumped.
     * @param boolean $thehtmlflag true if html is to be generated.
     */
    public function scalar(&$thevariable, $thehtmlflag) {
        if ($thehtmlflag) {
            return "<pre>" . preg_replace('|<|s', '&lt;', var_export($thevariable, true)) . "</pre>";
        } else {
            return var_export($thevariable, true);
        }
    }
}
