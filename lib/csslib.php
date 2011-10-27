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
 * This file contains CSS related methods and a CSS optimiser
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Stores CSS in a file at the given path.
 *
 * @param theme_config $theme
 * @param string $csspath
 * @param array $cssfiles
 */
function css_store_css(theme_config $theme, $csspath, array $cssfiles) {
    $css = '';
    foreach ($cssfiles as $file) {
        $css .= "\n".file_get_contents($file);
    }
    $css = $theme->post_process($css);

    $optimiser = new css_optimiser;
    $css = $optimiser->process($css);

    check_dir_exists(dirname($csspath));
    $fp = fopen($csspath, 'w');
    fwrite($fp, $css);
    fclose($fp);
    return true;
}

/**
 * Sends IE specific CSS
 *
 * @param string $themename
 * @param string $rev
 */
function css_send_ie_css($themename, $rev) {
    $lifetime = 60*60*24*30; // 30 days

    $css = "/** Unfortunately IE6/7 does not support more than 4096 selectors in one CSS file, which means we have to use some ugly hacks :-( **/";
    $css = "@import url(styles.php?theme=$themename&rev=$rev&type=plugins);";
    $css = "@import url(styles.php?theme=$themename&rev=$rev&type=parents);";
    $css = "@import url(styles.php?theme=$themename&rev=$rev&type=theme);";

    header('Etag: '.md5($rev));
    header('Content-Disposition: inline; filename="styles.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: max-age='.$lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    header('Content-Length: '.strlen($css));

    echo $css;
    die;
}

/**
 * Sends a cached CSS file
 *
 * @param string $csspath
 * @param string $rev
 */
function css_send_cached_css($csspath, $rev) {
    $lifetime = 60*60*24*30; // 30 days

    header('Content-Disposition: inline; filename="styles.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($csspath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: max-age='.$lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: '.filesize($csspath));
    }

    readfile($csspath);
    die;
}

/**
 * Sends CSS directly without caching it.
 *
 * @param string CSS
 */
function css_send_uncached_css($css) {

    header('Content-Disposition: inline; filename="styles_debug.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + THEME_DESIGNER_CACHE_LIFETIME) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');

    if (is_array($css)) {
        $css = implode("\n\n", $css);
    }
    $css = str_replace("\n", "\r\n", $css);
    $optimiser = new css_optimiser;
    echo $optimiser->process($css);

    die;
}

/**
 * Sends a 404 message about CSS not being found.
 */
function css_send_css_not_found() {
    header('HTTP/1.0 404 not found');
    die('CSS was not found, sorry.');
}

/**
 * A basic CSS optimiser that strips out unwanted things and then processing the
 * CSS organising styles and moving duplicates and useless CSS.
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_optimiser {

    /**#@+
     * Processing states. Used internally.
     */
    const PROCESSING_START = 0;
    const PROCESSING_SELECTORS = 0;
    const PROCESSING_STYLES = 1;
    const PROCESSING_COMMENT = 2;
    const PROCESSING_ATRULE = 3;
    /**#@-*/

    /**#@+
     * Stats variables set during and after processing
     * @var int
     */
    protected $rawstrlen = 0;
    protected $commentsincss = 0;
    protected $rawrules = 0;
    protected $rawselectors = 0;
    protected $optimisedstrlen = 0;
    protected $optimisedrules = 0;
    protected $optimisedselectors = 0;
    protected $timestart = 0;
    protected $timecomplete = 0;
    /**#@-*/

    /**
     * Processes incoming CSS optimising it and then returning it.
     *
     * @param string $css The raw CSS to optimise
     * @return string The optimised CSS
     */
    public function process($css) {
        global $CFG;

        $this->reset_stats();
        $this->timestart = microtime(true);
        $this->rawstrlen = strlen($css);

        // First up we need to remove all line breaks - this allows us to instantly
        // reduce our processing requirements and as we will process everything
        // into a new structure there's really nothing lost.
        $css = preg_replace('#\r?\n#', ' ', $css);

        // Next remove the comments... no need to them in an optimised world and
        // knowing they're all gone allows us to REALLY make our processing simpler
        $css = preg_replace('#/\*(.*?)\*/#m', '', $css, -1, $this->commentsincss);

        $medias = array(
            'all' => new css_media()
        );
        $imports = array();
        $charset = false;
        
        $currentprocess = self::PROCESSING_START;
        $currentstyle = css_rule::init();
        $currentselector = css_selector::init();
        $inquotes = false;      // ' or "
        $inbraces = false;      // {
        $inbrackets = false;    // [
        $inparenthesis = false; // (
        $currentmedia = $medias['all'];
        $currentatrule = null;
        $suspectatrule = false;

        $buffer = '';
        $char = null;

        // Next we are going to iterate over every single character in $css.
        // This is why re removed line breaks and comments!
        for ($i = 0; $i < $this->rawstrlen; $i++) {
            $lastchar = $char;
            $char = substr($css, $i, 1);
            if ($char == '@' && $buffer == '') {
                $suspectatrule = true;
            }
            switch ($currentprocess) {
                // Start processing an at rule e.g. @media, @page
                case self::PROCESSING_ATRULE:
                    switch ($char) {
                        case ';':
                            if (!$inbraces) {
                                $buffer .= $char;
                                if ($currentatrule == 'import') {
                                    $imports[] = $buffer;
                                    $currentprocess = self::PROCESSING_SELECTORS;
                                } else if ($currentatrule == 'charset') {
                                    $charset = $buffer;
                                    $currentprocess = self::PROCESSING_SELECTORS;
                                }
                            }
                            $buffer = '';
                            $currentatrule = false;
                            continue 3;
                        case '{':
                            if ($currentatrule == 'media' && preg_match('#\s*@media\s*([a-zA-Z0-9]+(\s*,\s*[a-zA-Z0-9]+)*)#', $buffer, $matches)) {
                                $mediatypes = str_replace(' ', '', $matches[1]);
                                if (!array_key_exists($mediatypes, $medias)) {
                                    $medias[$mediatypes] = new css_media($mediatypes);
                                }
                                $currentmedia = $medias[$mediatypes];
                                $currentprocess = self::PROCESSING_SELECTORS;
                                $buffer = '';
                            }
                            continue 3;
                    }
                    break;
                // Start processing selectors
                case self::PROCESSING_START:
                case self::PROCESSING_SELECTORS:
                    switch ($char) {
                        case '[':
                            $inbrackets ++;
                            $buffer .= $char;
                            continue 3;
                        case ']':
                            $inbrackets --;
                            $buffer .= $char;
                            continue 3;
                        case ' ':
                            if ($inbrackets) {
                                continue 3;
                            }
                            if (!empty($buffer)) {
                                if ($suspectatrule && preg_match('#@(media|import|charset)\s*#', $buffer, $matches)) {
                                    $currentatrule = $matches[1];
                                    $currentprocess = self::PROCESSING_ATRULE;
                                    $buffer .= $char;
                                } else {
                                    $currentselector->add($buffer);
                                    $buffer = '';
                                }
                            }
                            $suspectatrule = false;
                            continue 3;
                        case '{':
                            if ($inbrackets) {
                                continue 3;
                            }
                            
                            $currentselector->add($buffer);
                            $currentstyle->add_selector($currentselector);
                            $currentselector = css_selector::init();
                            $currentprocess = self::PROCESSING_STYLES;

                            $buffer = '';
                            continue 3;
                        case '}':
                            if ($inbrackets) {
                                continue 3;
                            }
                            if ($currentatrule == 'media') {
                                $currentmedia = $medias['all'];
                                $currentatrule = false;
                                $buffer = '';
                            }
                            continue 3;
                        case ',':
                            if ($inbrackets) {
                                continue 3;
                            }
                            $currentselector->add($buffer);
                            $currentstyle->add_selector($currentselector);
                            $currentselector = css_selector::init();
                            $buffer = '';
                            continue 3;
                    }
                    break;
                // Start processing styles
                case self::PROCESSING_STYLES:
                    if ($char == '"' || $char == "'") {
                        if ($inquotes === false) {
                            $inquotes = $char;
                        }
                        if ($inquotes === $char && $lastchar !== '\\') {
                            $inquotes = false;
                        }
                    }
                    if ($inquotes) {
                        $buffer .= $char;
                        continue 2;
                    }
                    switch ($char) {
                        case ';':
                            $currentstyle->add_style($buffer);
                            $buffer = '';
                            $inquotes = false;
                            continue 3;
                        case '}':
                            $currentstyle->add_style($buffer);
                            $this->rawselectors += $currentstyle->get_selector_count();

                            $currentmedia->add_rule($currentstyle);

                            $currentstyle = css_rule::init();
                            $currentprocess = self::PROCESSING_SELECTORS;
                            $this->rawrules++;
                            $buffer = '';
                            $inquotes = false;
                            continue 3;
                    }
                    break;
            }
            $buffer .= $char;
        }

        $css = '';
        if (!empty($charset)) {
            $imports[] = $charset;
        }
        if (!empty($imports)) {
            $css .= implode("\n", $imports);
            $css .= "\n\n";
        }
        foreach ($medias as $media) {
            $media->organise_rules_by_selectors();
            $this->optimisedrules += $media->count_rules();
            $this->optimisedselectors +=  $media->count_selectors();
            $css .= $media->out();
        }
        $this->optimisedstrlen = strlen($css);

        $this->timecomplete = microtime(true);
        if (!empty($CFG->cssincludestats)) {
            $css = $this->output_stats_css().$css;
        }
        return trim($css);
    }

    /**
     * Returns an array of stats from the last processing run
     * @return string
     */
    public function get_stats() {

        $stats = array(
            'timestart'             => $this->timestart,
            'timecomplete'          => $this->timecomplete,
            'timetaken'             => round($this->timecomplete - $this->timestart, 4),
            'commentsincss'         => $this->commentsincss,
            'rawstrlen'             => $this->rawstrlen,
            'rawselectors'          => $this->rawselectors,
            'rawrules'              => $this->rawrules,
            'optimisedstrlen'       => $this->optimisedstrlen,
            'optimisedrules'        => $this->optimisedrules,
            'optimiedselectors'     => $this->optimisedselectors,
            'improvementstrlen'     => round(100 - ($this->optimisedstrlen / $this->rawstrlen) * 100, 1).'%',
            'improvementrules'      => round(100 - ($this->optimisedrules / $this->rawrules) * 100, 1).'%',
            'improvementselectors'  => round(100 - ($this->optimisedselectors / $this->rawselectors) * 100, 1).'%',
        );
        return $stats;
    }

    /**
     * Returns a string to display stats about the last generation within CSS output
     * @return string
     */
    public function output_stats_css() {
        $stats = $this->get_stats();

        $strlenimprovement = round(100 - ($this->optimisedstrlen / $this->rawstrlen) * 100, 1);
        $ruleimprovement = round(100 - ($this->optimisedrules / $this->rawrules) * 100, 1);
        $selectorimprovement = round(100 - ($this->optimisedselectors / $this->rawselectors) * 100, 1);
        $timetaken = round($this->timecomplete - $this->timestart, 4);

        $computedcss  = "/****************************************\n";
        $computedcss .= " *------- CSS Optimisation stats --------\n";
        $computedcss .= " *  ".date('r')."\n";
        $computedcss .= " *  {$stats[commentsincss]}  \t comments removed\n";
        $computedcss .= " *  Optimisation took {$stats[timetaken]} seconds\n";
        $computedcss .= " *--------------- before ----------------\n";
        $computedcss .= " *  {$stats[rawstrlen]}  \t chars read in\n";
        $computedcss .= " *  {$stats[rawrules]}  \t rules read in\n";
        $computedcss .= " *  {$stats[rawselectors]}  \t total selectors\n";
        $computedcss .= " *---------------- after ----------------\n";
        $computedcss .= " *  {$stats[optimisedstrlen]}  \t chars once optimized\n";
        $computedcss .= " *  {$stats[optimisedrules]}  \t optimized rules\n";
        $computedcss .= " *  {$stats[optimisedselectors]}  \t total selectors once optimized\n";
        $computedcss .= " *---------------- stats ----------------\n";
        $computedcss .= " *  {$stats[strlenimprovement]}%  \t reduction in chars\n";
        $computedcss .= " *  {$stats[ruleimprovement]}%  \t reduction in rules\n";
        $computedcss .= " *  {$stats[selectorimprovement]}%  \t reduction in selectors\n";
        $computedcss .= " ****************************************/\n\n";

        return $computedcss;
    }

    /**
     * Resets the stats ready for another fresh processing
     */
    public function reset_stats() {
        $this->commentsincss = 0;
        $this->optimisedrules = 0;
        $this->optimisedselectors = 0;
        $this->optimisedstrlen = 0;
        $this->rawrules = 0;
        $this->rawselectors = 0;
        $this->rawstrlen = 0;
        $this->timecomplete = 0;
        $this->timestart = 0;
    }

    /**
     * An array of the common HTML colours that are supported by most browsers.
     *
     * This reference table is used to allow us to unify colours, and will aid
     * us in identifying buggy CSS using unsupported colours.
     *
     * @staticvar array
     * @var array
     */
    public static $htmlcolours = array(
        'aliceblue' => '#F0F8FF',
        'antiquewhite' => '#FAEBD7',
        'aqua' => '#00FFFF',
        'aquamarine' => '#7FFFD4',
        'azure' => '#F0FFFF',
        'beige' => '#F5F5DC',
        'bisque' => '#FFE4C4',
        'black' => '#000000',
        'blanchedalmond' => '#FFEBCD',
        'blue' => '#0000FF',
        'blueviolet' => '#8A2BE2',
        'brown' => '#A52A2A',
        'burlywood' => '#DEB887',
        'cadetblue' => '#5F9EA0',
        'chartreuse' => '#7FFF00',
        'chocolate' => '#D2691E',
        'coral' => '#FF7F50',
        'cornflowerblue' => '#6495ED',
        'cornsilk' => '#FFF8DC',
        'crimson' => '#DC143C',
        'cyan' => '#00FFFF',
        'darkblue' => '#00008B',
        'darkcyan' => '#008B8B',
        'darkgoldenrod' => '#B8860B',
        'darkgray' => '#A9A9A9',
        'darkgrey' => '#A9A9A9',
        'darkgreen' => '#006400',
        'darkKhaki' => '#BDB76B',
        'darkmagenta' => '#8B008B',
        'darkolivegreen' => '#556B2F',
        'arkorange' => '#FF8C00',
        'darkorchid' => '#9932CC',
        'darkred' => '#8B0000',
        'darksalmon' => '#E9967A',
        'darkseagreen' => '#8FBC8F',
        'darkslateblue' => '#483D8B',
        'darkslategray' => '#2F4F4F',
        'darkslategrey' => '#2F4F4F',
        'darkturquoise' => '#00CED1',
        'darkviolet' => '#9400D3',
        'deeppink' => '#FF1493',
        'deepskyblue' => '#00BFFF',
        'dimgray' => '#696969',
        'dimgrey' => '#696969',
        'dodgerblue' => '#1E90FF',
        'firebrick' => '#B22222',
        'floralwhite' => '#FFFAF0',
        'forestgreen' => '#228B22',
        'fuchsia' => '#FF00FF',
        'gainsboro' => '#DCDCDC',
        'ghostwhite' => '#F8F8FF',
        'gold' => '#FFD700',
        'goldenrod' => '#DAA520',
        'gray' => '#808080',
        'grey' => '#808080',
        'green' => '#008000',
        'greenyellow' => '#ADFF2F',
        'honeydew' => '#F0FFF0',
        'hotpink' => '#FF69B4',
        'indianred ' => '#CD5C5C',
        'indigo ' => '#4B0082',
        'ivory' => '#FFFFF0',
        'khaki' => '#F0E68C',
        'lavender' => '#E6E6FA',
        'lavenderblush' => '#FFF0F5',
        'lawngreen' => '#7CFC00',
        'lemonchiffon' => '#FFFACD',
        'lightblue' => '#ADD8E6',
        'lightcoral' => '#F08080',
        'lightcyan' => '#E0FFFF',
        'lightgoldenrodyellow' => '#FAFAD2',
        'lightgray' => '#D3D3D3',
        'lightgrey' => '#D3D3D3',
        'lightgreen' => '#90EE90',
        'lightpink' => '#FFB6C1',
        'lightsalmon' => '#FFA07A',
        'lightseagreen' => '#20B2AA',
        'lightskyblue' => '#87CEFA',
        'lightslategray' => '#778899',
        'lightslategrey' => '#778899',
        'lightsteelblue' => '#B0C4DE',
        'lightyellow' => '#FFFFE0',
        'lime' => '#00FF00',
        'limegreen' => '#32CD32',
        'linen' => '#FAF0E6',
        'magenta' => '#FF00FF',
        'maroon' => '#800000',
        'mediumaquamarine' => '#66CDAA',
        'mediumblue' => '#0000CD',
        'mediumorchid' => '#BA55D3',
        'mediumpurple' => '#9370D8',
        'mediumseagreen' => '#3CB371',
        'mediumslateblue' => '#7B68EE',
        'mediumspringgreen' => '#00FA9A',
        'mediumturquoise' => '#48D1CC',
        'mediumvioletred' => '#C71585',
        'midnightblue' => '#191970',
        'mintcream' => '#F5FFFA',
        'mistyrose' => '#FFE4E1',
        'moccasin' => '#FFE4B5',
        'navajowhite' => '#FFDEAD',
        'navy' => '#000080',
        'oldlace' => '#FDF5E6',
        'olive' => '#808000',
        'olivedrab' => '#6B8E23',
        'orange' => '#FFA500',
        'orangered' => '#FF4500',
        'orchid' => '#DA70D6',
        'palegoldenrod' => '#EEE8AA',
        'palegreen' => '#98FB98',
        'paleturquoise' => '#AFEEEE',
        'palevioletred' => '#D87093',
        'papayawhip' => '#FFEFD5',
        'peachpuff' => '#FFDAB9',
        'peru' => '#CD853F',
        'pink' => '#FFC0CB',
        'plum' => '#DDA0DD',
        'powderblue' => '#B0E0E6',
        'purple' => '#800080',
        'red' => '#FF0000',
        'rosybrown' => '#BC8F8F',
        'royalblue' => '#4169E1',
        'saddlebrown' => '#8B4513',
        'salmon' => '#FA8072',
        'sandybrown' => '#F4A460',
        'seagreen' => '#2E8B57',
        'seashell' => '#FFF5EE',
        'sienna' => '#A0522D',
        'silver' => '#C0C0C0',
        'skyblue' => '#87CEEB',
        'slateblue' => '#6A5ACD',
        'slategray' => '#708090',
        'slategrey' => '#708090',
        'snow' => '#FFFAFA',
        'springgreen' => '#00FF7F',
        'steelblue' => '#4682B4',
        'tan' => '#D2B48C',
        'teal' => '#008080',
        'thistle' => '#D8BFD8',
        'tomato' => '#FF6347',
        'turquoise' => '#40E0D0',
        'violet' => '#EE82EE',
        'wheat' => '#F5DEB3',
        'white' => '#FFFFFF',
        'whitesmoke' => '#F5F5F5',
        'yellow' => '#FFFF00',
        'yellowgreen' => '#9ACD32'
    );
}

/**
 * A structure to represent a CSS selector.
 *
 * The selector is the classes, id, elements, and psuedo bits that make up a CSS
 * rule.
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_selector {

    /**
     * An array of selector bits
     * @var array
     */
    protected $selectors = array();

    /**
     * The number of selectors.
     * @var int
     */
    protected $count = 0;

    /**
     * Initialises a new CSS selector
     * @return css_selector 
     */
    public static function init() {
        return new css_selector();
    }

    /**
     * CSS selectors can only be created through the init method above.
     */
    protected function __construct() {}

    /**
     * Adds a selector to the end of the current selector
     * @param string $selector
     */
    public function add($selector) {
        $selector = trim($selector);
        $count = 0;
        $count += preg_match_all('/(\.|#)/', $selector, $matchesarray);
        if (strpos($selector, '.') !== 0 && strpos($selector, '#') !== 0) {
            $count ++;
        }
        $this->count = $count;
        $this->selectors[] = $selector;
    }
    /**
     * Returns the number of individual components that make up this selector
     * @return int
     */
    public function get_selector_count() {
        return $this->count;
    }

    /**
     * Returns the selector for use in a CSS rule
     * @return string
     */
    public function out() {
        return trim(join(' ', $this->selectors));
    }
}

/**
 * A structure to represent a CSS rule.
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_rule {

    /**
     * An array of CSS selectors {@see css_selector}
     * @var array
     */
    protected $selectors = array();

    /**
     * An array of CSS styles {@see css_style}
     * @var array
     */
    protected $styles = array();

    /**
     * Created a new CSS rule. This is the only way to create a new CSS rule externally.
     * @return css_rule
     */
    public static function init() {
        return new css_rule();
    }

    /**
     * Constructs a new css rule - this can only be called from within the scope of
     * this class or its descendants.
     *
     * @param type $selector
     * @param array $styles
     */
    protected function __construct($selector = null, array $styles = array()) {
        if ($selector != null) {
            if (is_array($selector)) {
                $this->selectors = $selector;
            } else {
                $this->selectors = array($selector);
            }
            $this->add_styles($styles);
        }
    }

    /**
     * Adds a new CSS selector to this rule
     *
     * @param css_selector $selector
     */
    public function add_selector(css_selector $selector) {
        $this->selectors[] = $selector;
    }

    /**
     * Adds a new CSS style to this rule.
     *
     * @param css_style|string $style
     */
    public function add_style($style) {
        if (is_string($style)) {
            $style = trim($style);
            if (empty($style)) {
                return;
            }
            $bits = explode(':', $style, 2);
            if (count($bits) == 2) {
                list($name, $value) = array_map('trim', $bits);
            }
            if (isset($name) && isset($value) && $name !== '' && $value !== '') {
                $style = css_style::init($name, $value);
            }
        }
        if ($style instanceof css_style) {
            $name = $style->get_name();
            if (array_key_exists($name, $this->styles)) {
                $this->styles[$name]->set_value($style->get_value());
            } else {
                $this->styles[$name] = $style;
            }
        }
    }

    /**
     * An easy method of adding several styles at once. Just calls add_style.
     *
     * @param array $styles
     */
    public function add_styles(array $styles) {
        foreach ($styles as $style) {
            $this->add_style($style);
        }
    }

    /**
     * Returns all of the styles as a single string that can be used in a CSS
     * rule.
     *
     * @return string
     */
    protected function get_style_sting() {
        $bits = array();
        foreach ($this->styles as $style) {
            $bits[] = $style->out();
        }
        return join('', $bits);
    }

    /**
     * Returns all of the selectors as a single string that can be used in a
     * CSS rule
     *
     * @return string
     */
    protected function get_selector_string() {
        $selectors = array();
        foreach ($this->selectors as $selector) {
            $selectors[] = $selector->out();
        }
        return join(",\n", $selectors);
    }

    /**
     * Returns the array of selectors
     * @return array
     */
    public function get_selectors() {
        return $this->selectors;
    }

    /**
     * Returns the array of styles
     * @return array
     */
    public function get_styles() {
        return $this->styles;
    }

    /**
     * Outputs this rule as a fragment of CSS
     * @return string
     */
    public function out() {
        $css = $this->get_selector_string();
        $css .= '{';
        $css .= $this->get_style_sting();
        $css .= '}';
        return $css;
    }

    /**
     * Splits this rules into an array of CSS rules. One for each of the selectors
     * that make up this rule.
     *
     * @return array(css_rule)
     */
    public function split_by_selector() {
        $return = array();
        foreach ($this->selectors as $selector) {
            $return[] = new css_rule($selector, $this->styles);
        }
        return $return;
    }

    /**
     * Splits this rule into an array of rules. One for each of the styles that
     * make up this rule
     *
     * @return array(css_rule)
     */
    public function split_by_style() {
        $return = array();
        foreach ($this->styles as $style) {
            $return[] = new css_rule($this->selectors, array($style));
        }
        return $return;
    }

    /**
     * Gets a hash for the styles of this rule
     * @return string
     */
    public function get_style_hash() {
        $styles = $this->get_style_sting();
        return md5($styles);
    }

    /**
     * Gets a hash for the selectors of this rule
     * @return string
     */
    public function get_selector_hash() {
        $selector = $this->get_selector_string();
        return md5($selector);
    }

    /**
     * Gets the number of selectors that make up this rule.
     * @return int
     */
    public function get_selector_count() {
        $count = 0;
        foreach ($this->selectors as $selector) {
            $count += $selector->get_selector_count();
        }
        return $count;
    }
}

/**
 * A media class to organise rules by the media they apply to.
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_media {

    /**
     * An array of the different media types this instance applies to.
     * @var array
     */
    protected $types = array();

    /**
     * An array of rules within this media instance
     * @var array
     */
    protected $rules = array();

    /**
     * Initalises a new media instance
     *
     * @param type $for
     */
    public function __construct($for = 'all') {
        $types = explode(',', $for);
        $this->types = array_map('trim', $types);
    }

    /**
     * Adds a new CSS rule to this media instance
     *
     * @param css_rule $newrule
     */
    public function add_rule(css_rule $newrule) {
        foreach ($newrule->split_by_selector() as $rule) {
            $hash = $rule->get_selector_hash();
            if (!array_key_exists($hash, $this->rules)) {
                $this->rules[$hash] = $rule;
            } else {
                $this->rules[$hash]->add_styles($rule->get_styles());
            }
        }
    }

    /**
     * Returns the rules used by this
     *
     * @return array
     */
    public function get_rules() {
        return $this->rules;
    }

    /**
     * Organises rules by gropuing selectors based upon the styles and consolidating
     * those selectors into single rules.
     *
     * @return array An array of optimised styles
     */
    public function organise_rules_by_selectors() {
        $optimised = array();
        $beforecount = count($this->rules);
        foreach ($this->rules as $rule) {
            $hash = $rule->get_style_hash();
            if (!array_key_exists($hash, $optimised)) {
                $optimised[$hash] = clone($rule);
            } else {
                foreach ($rule->get_selectors() as $selector) {
                    $optimised[$hash]->add_selector($selector);
                }
            }
        }
        $this->rules = $optimised;
        $aftercount = count($this->rules);
        return ($beforecount < $aftercount);
    }

    /**
     * Returns the total number of rules that exist within this media set
     *
     * @return int
     */
    public function count_rules() {
        return count($this->rules);
    }

    /**
     * Returns the total number of selectors that exist within this media set
     *
     * @return int
     */
    public function count_selectors() {
        $count = 0;
        foreach ($this->rules as $rule) {
            $count += $rule->get_selector_count();
        }
        return $count;
    }

    /**
     * Returns the CSS for this media and all of its rules.
     *
     * @return string
     */
    public function out() {
        $output = '';
        $types = join(',', $this->types);
        if ($types !== 'all') {
            $output .= "\n\n/***** New media declaration *****/\n";
            $output .= "@media {$types} {\n";
        }
        foreach ($this->rules as $rule) {
            $output .= $rule->out()."\n";
        }
        if ($types !== 'all') {
            $output .= '}';
            $output .= "\n/***** Media declaration end for $types *****/";
        }
        return $output;
    }

    /**
     * Returns an array of media that this media instance applies to
     *
     * @return array
     */
    public function get_types() {
        return $this->types;
    }
}

/**
 * An absract class to represent CSS styles
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class css_style {

    /**
     * The name of the style
     * @var string
     */
    protected $name;

    /**
     * The value for the style
     * @var mixed
     */
    protected $value;

    /**
     * If set to true this style was defined with the !important rule.
     * @var bool
     */
    protected $important = false;

    /**
     * Initialises a new style.
     *
     * This is the only public way to create a style to ensure they that appropriate
     * style class is used if it exists.
     *
     * @param type $name
     * @param type $value
     * @return css_style_generic 
     */
    public static function init($name, $value) {
        $specificclass = 'css_style_'.preg_replace('#[^a-zA-Z0-9]+#', '', $name);
        if (class_exists($specificclass)) {
            return $specificclass::init($value);
        }
        return new css_style_generic($name, $value);
    }

    /**
     * Creates a new style when given its name and value
     *
     * @param string $name
     * @param string $value
     */
    protected function __construct($name, $value) {
        $this->name = $name;
        $this->set_value($value);
    }

    /**
     * Sets the value for the style
     *
     * @param string $value
     */
    final public function set_value($value) {
        $value = trim($value);
        $important = preg_match('#(\!important\s*;?\s*)$#', $value, $matches);
        if ($important) {
            $value = substr($value, 0, -(strlen($matches[1])));
        }
        if (!$this->important || $important) {
            $this->value = $this->clean_value($value);
            $this->important = $important;
        }
    }

    /**
     * Returns the name for the style
     *
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Returns the value for the style
     *
     * @return string
     */
    public function get_value() {
        $value = $this->value;
        if ($this->important) {
            $value .= ' !important';
        }
        return $value;
    }

    /**
     * Returns the style ready for use in CSS
     *
     * @param string|null $value
     * @return string
     */
    public function out($value = null) {
        if ($value === null) {
            $value = $this->get_value();
        } else if ($this->important && strpos($value, '!important') === false) {
            $value .= ' !important';
        }
        return "{$this->name}:{$value};";
    }

    /**
     * This can be overridden by a specific style allowing it to clean its values
     * consistently.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function clean_value($value) {
        return $value;
    }
}

/**
 * A generic CSS style class to use when a more specific class does not exist.
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_generic extends css_style {
    /**
     * Cleans incoming values for typical things that can be optimised.
     *
     * @param mixed $value
     * @return string
     */
    protected function clean_value($value) {
        if (trim($value) == '0px') {
            $value = 0;
        } else if (preg_match('/^#([a-fA-F0-9]{3,6})/', $value, $matches)) {
            $value = '#'.strtoupper($matches[1]);
        }
        return $value;
    }
}

/**
 * A colour CSS style
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_color extends css_style {
    /**
     * Creates a new colour style
     *
     * @param mixed $value
     * @return css_style_color
     */
    public static function init($value) {
        return new css_style_color('color', $value);
    }

    /**
     * Cleans the colour unifing it to a 6 char hash colour if possible
     * Doing this allows us to associate identical colours being specified in
     * different ways. e.g. Red, red, #F00, and #F00000
     *
     * @param mixed $value
     * @return string
     */
    protected function clean_value($value) {
        $value = trim($value);
        if (preg_match('/#([a-fA-F0-9]{6})/', $value, $matches)) {
            $value = '#'.strtoupper($matches[1]);
        } else if (preg_match('/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/', $value, $matches)) {
            $value = $matches[1] . $matches[1] . $matches[2] . $matches[2] . $matches[3] . $matches[3];
            $value = '#'.strtoupper($value);
        } else if (array_key_exists(strtolower($value), css_optimiser::$htmlcolours)) {
            $value = css_optimiser::$htmlcolours[strtolower($value)];
        }
        return $value;
    }

    /**
     * Returns the colour style for use within CSS.
     * Will return an optimised hash colour.
     *
     * e.g #123456
     *     #123 instead of #112233
     *     #F00 instead of red
     *
     * @param string $overridevalue
     * @return string
     */
    public function out($overridevalue = null) {
        if (preg_match('/#([a-fA-F0-9])\1([a-fA-F0-9])\2([a-fA-F0-9])\3/', $this->value, $matches)) {
            $overridevalue = '#'.$matches[1].$matches[2].$matches[3];
        }
        return parent::out($overridevalue);
    }
}

/**
 * A background colour style.
 *
 * Based upon the colour style.
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundcolor extends css_style_color {
    /**
     * Creates a new background colour style
     *
     * @param mixed $value
     * @return css_style_backgroundcolor
     */
    public static function init($value) {
        return new css_style_backgroundcolor('background-color', $value);
    }
}

/**
 * A border colour style
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_bordercolor extends css_style_color {
    /**
     * Creates a new border colour style
     *
     * Based upon the colour style
     *
     * @param mixed $value
     * @return css_style_bordercolor
     */
    public static function init($value) {
        return new css_style_bordercolor('border-color', $value);
    }
}

/**
 * A border style
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_border extends css_style {
    /**
     * Created a new border style
     *
     * @param mixed $value
     * @return css_style_border
     */
    public static function init($value) {
        return new css_style_border('border', $value);
    }
}