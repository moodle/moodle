<?php
/**
 * A SimpleTest report format for Moodle.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear@open.ac.uk, T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @version $Id$
 * @package SimpleTestEx
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/simpletestlib/reporter.php');

/**
 * Extended in-browser test displayer. HtmlReporter generates
 * only failure messages and a pass count. ExHtmlReporter also
 * generates pass messages and a time-stamp.
 *
 * @package SimpleTestEx
 */
class ExHtmlReporter extends HtmlReporter {

    // Options set when the class is created.
    var $showpasses;

    // Lang strings. Set in the constructor.
    var $strrunonlyfolder;
    var $strrunonlyfile;

    var $strseparator;

    /**
     * Constructor.
     *
     * @param bool $showpasses Whether this reporter should output anything for passes.
     */
    function ExHtmlReporter($showpasses) {
        global $CFG, $THEME;

        $this->HtmlReporter();
        $this->showpasses = $showpasses;

        $this->strrunonlyfolder = $this->get_string('runonlyfolder');
        $this->strrunonlyfile = $this->get_string('runonlyfile');
        $this->strseparator = get_separator();
    }

    /**
     * Called when a pass needs to be output.
     */
    function paintPass($message) {
        //(Implicitly call grandparent, as parent not implemented.)
        parent::paintPass($message);
        if ($this->showpasses) {
            $this->_paintPassFail('pass', $message);
        }
    }

    /**
     * Called when a fail needs to be output.
     */
    function paintFail($message) {
        // Explicitly call grandparent, not parent::paintFail.
        SimpleScorer::paintFail($message);
        $this->_paintPassFail('fail', $message, debug_backtrace());
    }

    /**
     * Called when an error (uncaught exception or PHP error) needs to be output.
     */
    function paintError($message) {
        // Explicitly call grandparent, not parent::paintError.
        SimpleScorer::paintError($message);
        $this->_paintPassFail('exception', $message);
    }

    /**
     * Called when a caught exception needs to be output.
     */
    function paintException($exception) {
        // Explicitly call grandparent, not parent::paintException.
        SimpleScorer::paintException($exception);
        $message = 'Unexpected exception of type [' . get_class($exception) .
                '] with message ['. $exception->getMessage() .
                '] in ['. $exception->getFile() .
                ' line ' . $exception->getLine() . ']';
        $stacktrace = null;
        if (method_exists($exception, 'getTrace')) {
            $stacktrace = $exception->getTrace();
        }
        $this->_paintPassFail('exception', $message, $stacktrace);
    }

    /**
     * Private method. Used by printPass/Fail/Error/Exception.
     */
    function _paintPassFail($passorfail, $message, $stacktrace = null) {
        global $FULLME, $CFG;

        print_simple_box_start('', '100%', '', 5, $passorfail . ' generalbox');
        $url = $this->_htmlEntities($this->_stripParameterFromUrl($FULLME, 'path'));
        echo '<b class="', $passorfail, '">', $this->get_string($passorfail), '</b>: ';
        $breadcrumb = $this->getTestList();
        array_shift($breadcrumb);
        $file = array_shift($breadcrumb);
        $pathbits = preg_split('/\/|\\\\/', substr($file, strlen($CFG->dirroot) + 1));
        $file = array_pop($pathbits);
        $folder = '';
        foreach ($pathbits as $pathbit) {
            $folder .= $pathbit . '/';
            echo "<a href=\"{$url}path=$folder\" title=\"$this->strrunonlyfolder\">$pathbit</a>/";
        }
        echo "<a href=\"{$url}path=$folder$file\" title=\"$this->strrunonlyfile\">$file</a>";
        echo $this->strseparator, implode($this->strseparator, $breadcrumb);
        echo $this->strseparator, '<br />', $this->_htmlEntities($message), "\n\n";
        if ($stacktrace) {
            $dotsadded = false;
            $interestinglines = 0;
            $filteredstacktrace = array();
            foreach ($stacktrace as $frame) {
                if (empty($frame['file']) || (strpos($frame['file'], 'simpletestlib') === false
                        && strpos($frame['file'], 'report/unittest') === false)) {
                    $filteredstacktrace[] = $frame;
                    $interestinglines += 1;
                    $dotsadded = false;
                } else if (!$dotsadded) {
                    $filteredstacktrace[] = array('line' => '...', 'file' => '...');
                    $dotsadded = true;
                }
            }
            if ($interestinglines > 1 || $passorfail == 'exception') {
                echo '<div class="notifytiny">' . format_backtrace($filteredstacktrace) . "</div>\n\n";
            }
        }
        print_simple_box_end();
        flush();
    }

    /**
     * Called when a notice needs to be output.
     */
    function paintNotice($message) {
        $this->paintMessage($this->_htmlEntities($message));
    }

    /**
     * Paints a simple supplementary message.
     * @param string $message Text to display.
     */
    function paintMessage($message) {
        if ($this->showpasses) {
            print_simple_box_start('', '100%');
            echo '<span class="notice">', $this->get_string('notice'), '</span>: ';
            $breadcrumb = $this->getTestList();
            array_shift($breadcrumb);
            echo implode($this->strseparator, $breadcrumb);
            echo $this->strseparator, '<br />', $message, "\n";
            print_simple_box_end();
            flush();
        }
    }

    /**
     * Output anything that should appear above all the test output.
     */
    function paintHeader($test_name) {
        // We do this the moodle way instead.
    }

    /**
     * Output anything that should appear below all the test output, e.g. summary information.
     */
    function paintFooter($test_name) {
        $summarydata = new stdClass;
        $summarydata->run = $this->getTestCaseProgress();
        $summarydata->total = $this->getTestCaseCount();
        $summarydata->passes = $this->getPassCount();
        $summarydata->fails = $this->getFailCount();
        $summarydata->exceptions = $this->getExceptionCount();

        if ($summarydata->fails == 0 && $summarydata->exceptions == 0) {
            $status = "passed";
        } else {
            $status = "failed";
        }
        echo '<div class="unittestsummary ', $status, '">';
        echo $this->get_string('summary', $summarydata);
        echo '</div>';

        echo '<div class="performanceinfo">',
                $this->get_string('runat', date('<b>d-m-Y H:i T</b>')),
                $this->get_string('version', SimpleTestOptions::getVersion()),
                '</div>';
    }

    /**
     * Strip a specified parameter from the query string of a URL, if present.
     * Adds a separator to the end of the URL, so that a new parameter
     * can easily be appended. For example (assuming $param = 'frog'):
     *
     * http://example.com/index.php               -> http://example.com/index.php?
     * http://example.com/index.php?frog=1        -> http://example.com/index.php?
     * http://example.com/index.php?toad=1        -> http://example.com/index.php?toad=1&
     * http://example.com/index.php?frog=1&toad=1 -> http://example.com/index.php?toad=1&
     *
     * @param string $url the URL to modify.
     * @param string $param the parameter to strip from the URL, if present.
     *
     * @return string The modified URL.
     */
    function _stripParameterFromUrl($url, $param) {
        $url = preg_replace('/(\?|&)' . $param . '=[^&]*&?/', '$1', $url);
        if (strpos($url, '?') === false) {
            $url = $url . '?';
        } else {
            $url = $url . '&';
        }
        return $url;
    }

    /**
     * Look up a lang string in the appropriate file.
     */
    function get_string($identifier, $a = NULL) {
        return get_string($identifier, 'simpletest', $a);
    }
}

/**
 * Formats a backtrace ready for output.
 *
 * @param array $callers backtrace array, as returned by debug_backtrace().
 * @param boolean $plaintext if false, generates HTML, if true generates plain text.
 * @return string formatted backtrace, ready for output.
 */
function format_backtrace($callers, $plaintext = false) {
    // do not use $CFG->dirroot because it might not be available in destructors
    $dirroot = dirname(dirname(__FILE__));

    if (empty($callers)) {
        return '';
    }

    $from = $plaintext ? '' : '<ul style="text-align: left">';
    foreach ($callers as $caller) {
        if (!isset($caller['line'])) {
            $caller['line'] = '?'; // probably call_user_func()
        }
        if (!isset($caller['file'])) {
            $caller['file'] = 'unknownfile'; // probably call_user_func()
        }
        $from .= $plaintext ? '* ' : '<li>';
        $from .= 'line ' . $caller['line'] . ' of ' . str_replace($dirroot, '', $caller['file']);
        if (isset($caller['function'])) {
            $from .= ': call to ';
            if (isset($caller['class'])) {
                $from .= $caller['class'] . $caller['type'];
            }
            $from .= $caller['function'] . '()';
        } else if (isset($caller['exception'])) {
            $from .= ': '.$caller['exception'].' thrown';
        }
        $from .= $plaintext ? "\n" : '</li>';
    }
    $from .= $plaintext ? '' : '</ul>';

    return $from;
}
?>