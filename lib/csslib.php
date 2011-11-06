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
    global $CFG;

    if (!empty($CFG->cssoptimise)) {
        // This is an experimental feature introduced in Moodle 2.2
        // The CSS optimiser organises the CSS in order to reduce the overall number
        // of rules and styles being sent to the client. It does this by collating
        // the CSS before it is cached removing excess styles and rules and stripping
        // out any extraneous content such as comments and empty rules.
        $optimiser = new css_optimiser;
        $css = '';
        foreach ($cssfiles as $file) {
            $css .= file_get_contents($file)."\n";
        }
        $css = $theme->post_process($css);
        $css = $optimiser->process($css);

        // If cssoptimisestats is set then stats from the optimisation are collected
        // and output at the beginning of the CSS
        if (!empty($CFG->cssoptimisestats)) {
            $css = $optimiser->output_stats_css().$css;
        }
    } else {
        // This is the default behaviour.
        // The cssoptimise setting was introduced in Moodle 2.2 and will hopefully
        // in the future be changed from an experimental setting to the default.
        // The css_minify_css will method will use the Minify library remove
        // comments, additional whitespace and other minor measures to reduce the
        // the overall CSS being sent.
        // However it has the distinct disadvantage of having to minify the CSS
        // before running the post process functions. Potentially things may break
        // here if theme designers try to push things with CSS post processing.
        $css = $theme->post_process(css_minify_css($cssfiles));
    }

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
    global $CFG;

    header('Content-Disposition: inline; filename="styles_debug.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + THEME_DESIGNER_CACHE_LIFETIME) .' GMT');
    header('Pragma: ');
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');

    if (is_array($css)) {
        $css = implode("\n\n", $css);
    }

    if (!empty($CFG->cssoptimise) && !empty($CFG->cssoptimisedebug)) {
        $css = str_replace("\n", "\r\n", $css);

        $optimiser = new css_optimiser;
        $css = $optimiser->process($css);
        if (!empty($CFG->cssoptimisestats)) {
            $css = $optimiser->output_stats_css().$css;
        }
    }

    echo $css;

    die;
}

/**
 * Sends a 404 message about CSS not being found.
 */
function css_send_css_not_found() {
    header('HTTP/1.0 404 not found');
    die('CSS was not found, sorry.');
}

function css_minify_css($files) {
    global $CFG;

    set_include_path($CFG->libdir . '/minify/lib' . PATH_SEPARATOR . get_include_path());
    require_once('Minify.php');

    if (0 === stripos(PHP_OS, 'win')) {
        Minify::setDocRoot(); // IIS may need help
    }
    // disable all caching, we do it in moodle
    Minify::setCache(null, false);

    $options = array(
        'bubbleCssImports' => false,
        // Don't gzip content we just want text for storage
        'encodeOutput' => false,
        // Maximum age to cache, not used but required
        'maxAge' => (60*60*24*20),
        // The files to minify
        'files' => $files,
        // Turn orr URI rewriting
        'rewriteCssUris' => false,
        // This returns the CSS rather than echoing it for display
        'quiet' => true
    );
    $result = Minify::serve('Files', $options);
    return $result['content'];
}

/**
 * Given a value determines if it is a valid CSS colour
 *
 * @param string $value
 * @return bool
 */
function css_is_colour($value) {
    $value = trim($value);
    if (preg_match('/^#([a-fA-F0-9]{1,6})$/', $value)) {
        return true;
    } else if (in_array(strtolower($value), array_keys(css_optimiser::$htmlcolours))) {
        return true;
    } else if (preg_match('#^(rgb|hsl)\s*\(\s*\d{1,3}\%?\s*,\s*\d{1,3}\%?\s*,\s*\d{1,3}\%?\s*\)$#', $value)) {
        return true;
    } else if (preg_match('#^(rgb|hsl)a\s*\(\s*\d{1,3}\%?\s*,\s*\d{1,3}\%?\s*,\s*\d{1,3}\%?\s*,\s*\d(\.\d+)?\s*\)$#', $value)) {
        return true;
    }
    return false;
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
        $currentrule = css_rule::init();
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
                            $currentrule->add_selector($currentselector);
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
                            $currentrule->add_selector($currentselector);
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
                            $currentrule->add_style($buffer);
                            $buffer = '';
                            $inquotes = false;
                            continue 3;
                        case '}':
                            $currentrule->add_style($buffer);
                            $this->rawselectors += $currentrule->get_selector_count();

                            $currentmedia->add_rule($currentrule);

                            $currentrule = css_rule::init();
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
            'optimisedselectors'     => $this->optimisedselectors,
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
        $computedcss .= " *  {$stats['commentsincss']}  \t comments removed\n";
        $computedcss .= " *  Optimisation took {$stats['timetaken']} seconds\n";
        $computedcss .= " *--------------- before ----------------\n";
        $computedcss .= " *  {$stats['rawstrlen']}  \t chars read in\n";
        $computedcss .= " *  {$stats['rawrules']}  \t rules read in\n";
        $computedcss .= " *  {$stats['rawselectors']}  \t total selectors\n";
        $computedcss .= " *---------------- after ----------------\n";
        $computedcss .= " *  {$stats['optimisedstrlen']}  \t chars once optimized\n";
        $computedcss .= " *  {$stats['optimisedrules']}  \t optimized rules\n";
        $computedcss .= " *  {$stats['optimisedselectors']}  \t total selectors once optimized\n";
        $computedcss .= " *---------------- stats ----------------\n";
        $computedcss .= " *  {$stats['improvementstrlen']}  \t reduction in chars\n";
        $computedcss .= " *  {$stats['improvementrules']}  \t reduction in rules\n";
        $computedcss .= " *  {$stats['improvementselectors']}  \t reduction in selectors\n";
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
 * Used to prepare CSS strings
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class css_writer {
    /**
     * The current indent level
     * @var int
     */
    protected static $indent = 0;

    /**
     * Returns true if the output should still maintain minimum formatting.
     * @return bool
     */
    protected static function is_pretty() {
        global $CFG;
        return (!empty($CFG->cssoptimisepretty));
    }

    /**
     * Returns the indenting char to use for indenting things nicely.
     * @return string
     */
    protected static function get_indent() {
        if (self::is_pretty()) {
            return str_repeat("  ", self::$indent);
        }
        return '';
    }

    /**
     * Increases the current indent
     */
    protected static function increase_indent() {
        self::$indent++;
    }

    /**
     * Descreases the current indent
     */
    protected static function decrease_indent() {
        self::$indent--;
    }

    /**
     * Returns the string to use as a separator
     * @return string
     */
    protected static function get_separator() {
        return (self::is_pretty())?"\n":' ';
    }

    /**
     * Returns CSS for media
     *
     * @param string $typestring
     * @param array $rules An array of css_rule objects
     * @return string
     */
    public static function media($typestring, array &$rules) {
        $nl = self::get_separator();

        $output = '';
        if ($typestring !== 'all') {
            $output .= $nl.$nl."@media {$typestring} {".$nl;
            self::increase_indent();
        }
        foreach ($rules as $rule) {
            $output .= $rule->out().$nl;
        }
        if ($typestring !== 'all') {
            self::decrease_indent();
            $output .= '}';
        }
        return $output;
    }

    /**
     * Returns CSS for a rule
     *
     * @param string $selector
     * @param string $styles
     * @return string
     */
    public static function rule($selector, $styles) {
        $css = self::get_indent()."{$selector}{{$styles}}";
        return $css;
    }

    /**
     * Returns CSS for the selectors of a rule
     *
     * @param array $selectors Array of css_selector objects
     * @return string
     */
    public static function selectors(array $selectors) {
        $nl = self::get_separator();
        $selectorstrings = array();
        foreach ($selectors as $selector) {
            $selectorstrings[] = $selector->out();
        }
        return join(','.$nl, $selectorstrings);
    }

    /**
     * Returns a selector given the components that make it up.
     *
     * @param array $components
     * @return string
     */
    public static function selector(array $components) {
        return trim(join(' ', $components));
    }

    /**
     *
     * @param array $styles Array of css_style objects
     * @return type
     */
    public static function styles(array $styles) {
        $bits = array();
        foreach ($styles as $style) {
            $bits[] = $style->out();
        }
        return join('', $bits);
    }

    /**
     * Returns a style CSS
     *
     * @param string $name
     * @param string $value
     * @param bool $important
     * @return string
     */
    public static function style($name, $value, $important = false) {
        if ($important && strpos($value, '!important') === false) {
            $value .= ' !important';
        }
        return "{$name}:{$value};";
    }
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
        return css_writer::selector($this->selectors);
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
        } else if ($style instanceof css_style) {
            $style = clone($style);
        }
        if ($style instanceof css_style) {
            $name = $style->get_name();
            if (array_key_exists($name, $this->styles)) {
                $this->styles[$name]->set_value($style->get_value());
            } else {
                $this->styles[$name] = $style;
            }
        } else if (is_array($style)) {
            foreach ($style as $astyle) {
                $this->add_style($astyle);
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
        $selectors = css_writer::selectors($this->selectors);
        $styles = css_writer::styles($this->get_consolidated_styles());
        return css_writer::rule($selectors, $styles);
    }

    public function get_consolidated_styles() {
        $finalstyles = array();
        $consolidate = array();
        foreach ($this->styles as $style) {
            $consolidatetoclass = $style->consolidate_to();
            if ($style->is_valid() && !empty($consolidatetoclass) && class_exists('css_style_'.$consolidatetoclass)) {
                $class = 'css_style_'.$consolidatetoclass;
                if (!array_key_exists($class, $consolidate)) {
                    $consolidate[$class] = array();
                }
                $consolidate[$class][] = $style;
            } else {
                $finalstyles[] = $style;
            }
        }

        foreach ($consolidate as $class => $styles) {
            $styles = $class::consolidate($styles);
            foreach ($styles as $style) {
                $finalstyles[] = $style;
            }
        }
        return $finalstyles;
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
        return md5(css_writer::styles($this->styles));
    }

    /**
     * Gets a hash for the selectors of this rule
     * @return string
     */
    public function get_selector_hash() {
        return md5(css_writer::selectors($this->selectors));
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
        return css_writer::media(join(',', $this->types), $this->rules);
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

    public function is_valid() {
        return true;
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
        if (is_null($value)) {
            $value = $this->get_value();
        }
        return css_writer::style($this->name, $value, $this->important);
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

    public function consolidate_to() {
        return null;
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
        if ($overridevalue === null) {
            $overridevalue = $this->value;
        }
        return parent::out(self::shrink_value($overridevalue));
    }

    public static function shrink_value($value) {
        if (preg_match('/#([a-fA-F0-9])\1([a-fA-F0-9])\2([a-fA-F0-9])\3/', $value, $matches)) {
            return '#'.$matches[1].$matches[2].$matches[3];
        }
        return $value;
    }

    public function is_valid() {
        return css_is_colour($this->value);
    }
}

class css_style_margin extends css_style {
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 4);

        $top = $right = $bottom = $left = null;
        if (count($bits) > 0) {
            $top = $right = $bottom = $left = array_shift($bits);
        }
        if (count($bits) > 0) {
            $right = $left = array_shift($bits);
        }
        if (count($bits) > 0) {
            $bottom = array_shift($bits);
        }
        if (count($bits) > 0) {
            $left = array_shift($bits);
        }
        return array(
            new css_style_margintop('margin-top', $top),
            new css_style_marginright('margin-right', $right),
            new css_style_marginbottom('margin-bottom', $bottom),
            new css_style_marginleft('margin-left', $left)
        );
    }
    public static function consolidate(array $styles) {
        if (count($styles) != 4) {
            return $styles;
        }
        $top = $right = $bottom = $left = null;
        foreach ($styles as $style) {
            switch ($style->get_name()) {
                case 'margin-top' : $top = $style->get_value();break;
                case 'margin-right' : $right = $style->get_value();break;
                case 'margin-bottom' : $bottom = $style->get_value();break;
                case 'margin-left' : $left = $style->get_value();break;
            }
        }
        if ($top == $bottom && $left == $right) {
            if ($top == $left) {
                return array(new css_style_margin('margin', $top));
            } else {
                return array(new css_style_margin('margin', "{$top} {$left}"));
            }
        } else if ($left == $right) {
            return array(new css_style_margin('margin', "{$top} {$right} {$bottom}"));
        } else {
            return array(new css_style_margin('margin', "{$top} {$right} {$bottom} {$left}"));
        }
        
    }
}

class css_style_margintop extends css_style {
    public static function init($value) {
        return new css_style_margintop('margin-top', $value);
    }
    public function consolidate_to() {
        return 'margin';
    }
}

class css_style_marginright extends css_style {
    public static function init($value) {
        return new css_style_marginright('margin-right', $value);
    }
    public function consolidate_to() {
        return 'margin';
    }
}

class css_style_marginbottom extends css_style {
    public static function init($value) {
        return new css_style_marginbottom('margin-bottom', $value);
    }
    public function consolidate_to() {
        return 'margin';
    }
}

class css_style_marginleft extends css_style {
    public static function init($value) {
        return new css_style_marginleft('margin-left', $value);
    }
    public function consolidate_to() {
        return 'margin';
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
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 3);

        $return = array();
        if (count($bits) > 0) {
            $width = array_shift($bits);
            $return[] = new css_style_borderwidth('border-width-top', $width);
            $return[] = new css_style_borderwidth('border-width-right', $width);
            $return[] = new css_style_borderwidth('border-width-bottom', $width);
            $return[] = new css_style_borderwidth('border-width-left', $width);
        }
        if (count($bits) > 0) {
            $style = array_shift($bits);
            $return[] = new css_style_borderstyle('border-style-top', $style);
            $return[] = new css_style_borderstyle('border-style-right', $style);
            $return[] = new css_style_borderstyle('border-style-bottom', $style);
            $return[] = new css_style_borderstyle('border-style-left', $style);
        }
        if (count($bits) > 0) {
            $colour = array_shift($bits);
            $return[] = new css_style_bordercolor('border-color-top', $colour);
            $return[] = new css_style_bordercolor('border-color-right', $colour);
            $return[] = new css_style_bordercolor('border-color-bottom', $colour);
            $return[] = new css_style_bordercolor('border-color-left', $colour);
        }
        return $return;
    }
    public static function consolidate(array $styles) {

        $borderwidths = array('top' => null, 'right' => null, 'bottom' => null, 'left' => null);
        $borderstyles = array('top' => null, 'right' => null, 'bottom' => null, 'left' => null);
        $bordercolors = array('top' => null, 'right' => null, 'bottom' => null, 'left' => null);

        foreach ($styles as $style) {
            switch ($style->get_name()) {
                case 'border-width-top': $borderwidths['top'] = $style->get_value(); break;
                case 'border-width-right': $borderwidths['right'] = $style->get_value(); break;
                case 'border-width-bottom': $borderwidths['bottom'] = $style->get_value(); break;
                case 'border-width-left': $borderwidths['left'] = $style->get_value(); break;

                case 'border-style-top': $borderstyles['top'] = $style->get_value(); break;
                case 'border-style-right': $borderstyles['right'] = $style->get_value(); break;
                case 'border-style-bottom': $borderstyles['bottom'] = $style->get_value(); break;
                case 'border-style-left': $borderstyles['left'] = $style->get_value(); break;

                case 'border-color-top': $bordercolors['top'] = $style->get_value(); break;
                case 'border-color-right': $bordercolors['right'] = $style->get_value(); break;
                case 'border-color-bottom': $bordercolors['bottom'] = $style->get_value(); break;
                case 'border-color-left': $bordercolors['left'] = $style->get_value(); break;
            }
        }

        $uniquewidths = count(array_unique($borderwidths));
        $uniquestyles = count(array_unique($borderstyles));
        $uniquecolors = count(array_unique($bordercolors));

        $nullwidths = in_array(null, $borderwidths);
        $nullstyles = in_array(null, $borderstyles);
        $nullcolors = in_array(null, $bordercolors);

        $allwidthsthesame = ($uniquewidths === 1)?1:0;
        $allstylesthesame = ($uniquestyles === 1)?1:0;
        $allcolorsthesame = ($uniquecolors === 1)?1:0;

        $allwidthsnull = $allwidthsthesame && $nullwidths;
        $allstylesnull = $allstylesthesame && $nullstyles;
        $allcolorsnull = $allcolorsthesame && $nullcolors;

        $return = array();
        if ($allwidthsnull && $allstylesnull && $allcolorsnull) {
            // Everything is null still... boo
            return array(new css_style_border('border', ''));

        } else if ($allwidthsnull && $allstylesnull) {

            self::consolidate_styles_by_direction($return, 'css_style_bordercolor', 'border-color', $bordercolors);
            return $return;

        } else if ($allwidthsnull && $allcolorsnull) {

            self::consolidate_styles_by_direction($return, 'css_style_borderstyle', 'border-style', $borderstyles);
            return $return;

        } else if ($allcolorsnull && $allstylesnull) {

            self::consolidate_styles_by_direction($return, 'css_style_borderwidth', 'border-width', $borderwidths);
            return $return;

        }

        if ($allwidthsthesame + $allstylesthesame + $allcolorsthesame == 3) {

            $return[] = new css_style_border('border', $borderwidths['top'].' '.$borderstyles['top'].' '.$bordercolors['top']);
            
        } else if ($allwidthsthesame + $allstylesthesame + $allcolorsthesame == 2) {

            if ($allwidthsthesame && $allstylesthesame && !$nullwidths && !$nullstyles) {

                $return[] = new css_style_border('border', $borderwidths['top'].' '.$borderstyles['top']);
                self::consolidate_styles_by_direction($return, 'css_style_bordercolor', 'border-color', $bordercolors);
                
            } else if ($allwidthsthesame && $allcolorsthesame && !$nullwidths && !$nullcolors) {

                $return[] = new css_style_border('border', $borderwidths['top'].' solid '.$bordercolors['top']);
                self::consolidate_styles_by_direction($return, 'css_style_borderstyle', 'border-style', $borderstyles);
                
            } else if ($allstylesthesame && $allcolorsthesame && !$nullstyles && !$nullcolors) {

                $return[] = new css_style_border('border', '1px '.$borderstyles['top'].' '.$bordercolors['top']);
                self::consolidate_styles_by_direction($return, 'css_style_borderwidth', 'border-width', $borderwidths);

            } else {
                self::consolidate_styles_by_direction($return, 'css_style_borderwidth', 'border-width', $borderwidths);
                self::consolidate_styles_by_direction($return, 'css_style_borderstyle', 'border-style', $borderstyles);
                self::consolidate_styles_by_direction($return, 'css_style_bordercolor', 'border-color', $bordercolors);
            }

        } else if (!$nullwidths && !$nullcolors && !$nullstyles && max(array_count_values($borderwidths)) == 3 && max(array_count_values($borderstyles)) == 3 && max(array_count_values($bordercolors)) == 3) {
            $widthkeys = array();
            $stylekeys = array();
            $colorkeys = array();

            foreach ($borderwidths as $key => $value) {
                if (!array_key_exists($value, $widthkeys)) {
                    $widthkeys[$value] = array();
                }
                $widthkeys[$value][] = $key;
            }
            usort($widthkeys, 'css_sort_by_count');
            $widthkeys = array_values($widthkeys);

            foreach ($borderstyles as $key => $value) {
                if (!array_key_exists($value, $stylekeys)) {
                    $stylekeys[$value] = array();
                }
                $stylekeys[$value][] = $key;
            }
            usort($stylekeys, 'css_sort_by_count');
            $stylekeys = array_values($stylekeys);

            foreach ($bordercolors as $key => $value) {
                if (!array_key_exists($value, $colorkeys)) {
                    $colorkeys[$value] = array();
                }
                $colorkeys[$value][] = $key;
            }
            usort($colorkeys, 'css_sort_by_count');
            $colorkeys = array_values($colorkeys);

            if ($widthkeys == $stylekeys && $stylekeys == $colorkeys) {
                $key = $widthkeys[0][0];
                self::build_style_string($return, 'css_style_border', 'border',  $borderwidths[$key], $borderstyles[$key], $bordercolors[$key]);
                $key = $widthkeys[1][0];
                self::build_style_string($return, 'css_style_border'.$key, 'border-'.$key,  $borderwidths[$key], $borderstyles[$key], $bordercolors[$key]);
            } else {
                self::build_style_string($return, 'css_style_bordertop', 'border-top', $borderwidths['top'], $borderstyles['top'], $bordercolors['top']);
                self::build_style_string($return, 'css_style_borderright', 'border-right', $borderwidths['right'], $borderstyles['right'], $bordercolors['right']);
                self::build_style_string($return, 'css_style_borderbottom', 'border-bottom', $borderwidths['bottom'], $borderstyles['bottom'], $bordercolors['bottom']);
                self::build_style_string($return, 'css_style_borderleft', 'border-left', $borderwidths['left'], $borderstyles['left'], $bordercolors['left']);
            }
        } else {
            self::build_style_string($return, 'css_style_bordertop', 'border-top', $borderwidths['top'], $borderstyles['top'], $bordercolors['top']);
            self::build_style_string($return, 'css_style_borderright', 'border-right', $borderwidths['right'], $borderstyles['right'], $bordercolors['right']);
            self::build_style_string($return, 'css_style_borderbottom', 'border-bottom', $borderwidths['bottom'], $borderstyles['bottom'], $bordercolors['bottom']);
            self::build_style_string($return, 'css_style_borderleft', 'border-left', $borderwidths['left'], $borderstyles['left'], $bordercolors['left']);
        }
        foreach ($return as $key => $style) {
            if ($style->get_value() == '') {
                unset($return[$key]);
            }
        }
        return $return;
    }
    public function consolidate_to() {
        return 'border';
    }
    public static function consolidate_styles_by_direction(&$array, $class, $style, $top, $right = null, $bottom = null, $left = null) {
        if (is_array($top)) {
            $right = $top['right'];
            $bottom = $top['bottom'];
            $left = $top['left'];
            $top = $top['top'];
        }

        if ($top == $bottom && $left == $right && $top == $left) {
            if ($top == null) {
                $array[] = new $class($style, '');
            } else {
                $array[] =  new $class($style, $top);
            }
        } else if ($top == null || $right == null || $bottom == null || $left == null) {
            if ($top !== null) {
                $array[] = new $class($style.'-top', $top);
            }
            if ($right !== null) {
                $array[] = new $class($style.'-right', $right);
            }
            if ($bottom !== null) {
                $array[] = new $class($style.'-bottom', $bottom);
            }
            if ($left !== null) {
                $array[] = new $class($style.'-left', $left);
            }
        } else if ($top == $bottom && $left == $right) {
            $array[] = new $class($style, $top.' '.$right);
        } else if ($left == $right) {
            $array[] = new $class($style, $top.' '.$right.' '.$bottom);
        } else {
            $array[] = new $class($style, $top.' '.$right.' '.$bottom.' '.$left);
        }
        return true;
    }
    public static function build_style_string(&$array, $class, $cssstyle, $width = null, $style = null, $color = null) {
        if (!is_null($width) && !is_null($style) && !is_null($color)) {
            $array[] = new $class($cssstyle, $width.' '.$style.' '.$color);
        } else if (!is_null($width) && !is_null($style) && is_null($color)) {
            $array[] = new $class($cssstyle, $width.' '.$style);
        } else if (!is_null($width) && is_null($style) && is_null($color)) {
            $array[] = new $class($cssstyle.'-width', $width);
        } else {
            if (!is_null($width)) $array[] = new $class($cssstyle.'-width', $width);
            if (!is_null($style)) $array[] = new $class($cssstyle.'-style', $style);
            if (!is_null($color)) $array[] = new $class($cssstyle.'-color', $color);
        }
        return true;
    }
}

function css_sort_by_count(array $a, array $b) {
    $a = count($a);
    $b = count($b);
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
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
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 4);

        $top = $right = $bottom = $left = null;
        if (count($bits) > 0) {
            $top = $right = $bottom = $left = array_shift($bits);
        }
        if (count($bits) > 0) {
            $right = $left = array_shift($bits);
        }
        if (count($bits) > 0) {
            $bottom = array_shift($bits);
        }
        if (count($bits) > 0) {
            $left = array_shift($bits);
        }
        return array(
            css_style_bordercolortop::init($top),
            css_style_bordercolorright::init($right),
            css_style_bordercolorbottom::init($bottom),
            css_style_bordercolorleft::init($left)
        );
    }
    public function consolidate_to() {
        return 'border';
    }
    protected function clean_value($value) {
        $values = explode(' ', $value);
        $values = array_map('parent::clean_value', $values);
        return join (' ', $values);
    }
    public function out($overridevalue = null) {
        if ($overridevalue === null) {
            $overridevalue = $this->value;
        }
        $values = explode(' ', $overridevalue);
        $values = array_map('css_style_color::shrink_value', $values);
        return parent::out(join (' ', $values));
    }
}

class css_style_borderleft extends css_style_generic {
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 3);

        $return = array();
        if (count($bits) > 0) {
            $return[] = css_style_borderwidthleft::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_borderstyleleft::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_bordercolorleft::init(array_shift($bits));
        }
        return $return;
    }
    public function consolidate_to() {
        return 'border';
    }
}

class css_style_borderright extends css_style_generic {
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 3);

        $return = array();
        if (count($bits) > 0) {
            $return[] = css_style_borderwidthright::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_borderstyleright::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_bordercolorright::init(array_shift($bits));
        }
        return $return;
    }
    public function consolidate_to() {
        return 'border';
    }
}

class css_style_bordertop extends css_style_generic {
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 3);

        $return = array();
        if (count($bits) > 0) {
            $return[] = css_style_borderwidthtop::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_borderstyletop::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_bordercolortop::init(array_shift($bits));
        }
        return $return;
    }
    public function consolidate_to() {
        return 'border';
    }
}

class css_style_borderbottom extends css_style_generic {
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 3);

        $return = array();
        if (count($bits) > 0) {
            $return[] = css_style_borderwidthbottom::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_borderstylebottom::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_bordercolorbottom::init(array_shift($bits));
        }
        return $return;
    }
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border width style
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderwidth extends css_style_generic {
    /**
     * Creates a new border colour style
     *
     * Based upon the colour style
     *
     * @param mixed $value
     * @return css_style_borderwidth
     */
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 4);

        $top = $right = $bottom = $left = null;
        if (count($bits) > 0) {
            $top = $right = $bottom = $left = array_shift($bits);
        }
        if (count($bits) > 0) {
            $right = $left = array_shift($bits);
        }
        if (count($bits) > 0) {
            $bottom = array_shift($bits);
        }
        if (count($bits) > 0) {
            $left = array_shift($bits);
        }
        return array(
            css_style_borderwidthtop::init($top),
            css_style_borderwidthright::init($right),
            css_style_borderwidthbottom::init($bottom),
            css_style_borderwidthleft::init($left)
        );
    }
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border style style
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderstyle extends css_style_generic {
    /**
     * Creates a new border colour style
     *
     * Based upon the colour style
     *
     * @param mixed $value
     * @return css_style_borderstyle
     */
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 4);

        $top = $right = $bottom = $left = null;
        if (count($bits) > 0) {
            $top = $right = $bottom = $left = array_shift($bits);
        }
        if (count($bits) > 0) {
            $right = $left = array_shift($bits);
        }
        if (count($bits) > 0) {
            $bottom = array_shift($bits);
        }
        if (count($bits) > 0) {
            $left = array_shift($bits);
        }
        return array(
            css_style_borderstyletop::init($top),
            css_style_borderstyleright::init($right),
            css_style_borderstylebottom::init($bottom),
            css_style_borderstyleleft::init($left)
        );
    }
    public function consolidate_to() {
        return 'border';
    }
}

class css_style_bordercolortop extends css_style_color {
    public static function init($value) {
        return new css_style_bordercolortop('border-color-top', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}
class css_style_bordercolorleft extends css_style_color {
    public static function init($value) {
        return new css_style_bordercolorleft('border-color-left', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}
class css_style_bordercolorright extends css_style_color {
    public static function init($value) {
        return new css_style_bordercolorright('border-color-right', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}
class css_style_bordercolorbottom extends css_style_color {
    public static function init($value) {
        return new css_style_bordercolorbottom('border-color-bottom', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}

class css_style_borderwidthtop extends css_style_generic {
    public static function init($value) {
        return new css_style_borderwidthtop('border-width-top', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}
class css_style_borderwidthleft extends css_style_generic {
    public static function init($value) {
        return new css_style_borderwidthleft('border-width-left', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}
class css_style_borderwidthright extends css_style_generic {
    public static function init($value) {
        return new css_style_borderwidthright('border-width-right', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}
class css_style_borderwidthbottom extends css_style_generic {
    public static function init($value) {
        return new css_style_borderwidthbottom('border-width-bottom', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}


class css_style_borderstyletop extends css_style_generic {
    public static function init($value) {
        return new css_style_borderstyletop('border-style-top', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}
class css_style_borderstyleleft extends css_style_generic {
    public static function init($value) {
        return new css_style_borderstyleleft('border-style-left', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}
class css_style_borderstyleright extends css_style_generic {
    public static function init($value) {
        return new css_style_borderstyleright('border-style-right', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}
class css_style_borderstylebottom extends css_style_generic {
    public static function init($value) {
        return new css_style_borderstylebottom('border-style-bottom', $value);
    }
    public function consolidate_to() {
        return 'border';
    }
}


class css_style_background extends css_style {
    public static function init($value) {
        // colour - image - repeat - attachment - position

        $imageurl = null;
        if (preg_match('#url\(([^\)]+)\)#', $value, $matches)) {
            $imageurl = trim($matches[1]);
            $value = str_replace($matches[1], '', $value);
        }

        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value);

        $repeats = array('repeat', 'repeat-x', 'repeat-y', 'no-repeat', 'inherit');
        $attachments = array('scroll' , 'fixed', 'inherit');

        $return = array();
        if (count($bits) > 0 && css_is_colour(reset($bits))) {
            $return[] = new css_style_backgroundcolor('background-color', array_shift($bits));
        }
        if (count($bits) > 0 && preg_match('#(none|inherit|url\(\))#', reset($bits))) {
            $image = array_shift($bits);
            if ($image == 'url()') {
                $image = "url({$imageurl})";
            }
            $return[] = new css_style_backgroundimage('background-image', $image);
        }
        if (count($bits) > 0 && in_array(reset($bits), $repeats)) {
            $return[] = new css_style_backgroundrepeat('background-repeat', array_shift($bits));
        }
        if (count($bits) > 0 && in_array(reset($bits), $attachments)) {
            // scroll , fixed, inherit
            $return[] = new css_style_backgroundattachment('background-attachment', array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = new css_style_backgroundposition('background-position', join(' ',$bits));
        }
        return $return;
    }
    public static function consolidate(array $styles) {

        if (count($styles) < 1) {
            return $styles;
        }

        $color = $image = $repeat = $attachment = $position = null;
        foreach ($styles as $style) {
            switch ($style->get_name()) {
                case 'background-color' : $color = css_style_color::shrink_value($style->get_value()); break;
                case 'background-image' : $image = $style->get_value(); break;
                case 'background-repeat' : $repeat = $style->get_value(); break;
                case 'background-attachment' : $attachment = $style->get_value(); break;
                case 'background-position' : $position = $style->get_value(); break;
            }
        }

        if ((is_null($image) || is_null($position) || is_null($repeat)) && ($image!= null || $position != null || $repeat != null)) {
            return $styles;
        }

        $value = array();
        if (!is_null($color)) $value[] .= $color;
        if (!is_null($image)) $value[] .= $image;
        if (!is_null($repeat)) $value[] .= $repeat;
        if (!is_null($attachment)) $value[] .= $attachment;
        if (!is_null($position)) $value[] .= $position;
        return array(new css_style_background('background', join(' ', $value)));
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
    public function consolidate_to() {
        return 'background';
    }
}

/**
 * A background image style.
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundimage extends css_style_generic {
    /**
     * Creates a new background colour style
     *
     * @param mixed $value
     * @return css_style_backgroundimage
     */
    public static function init($value) {
        return new css_style_backgroundimage('background-image', $value);
    }
    public function consolidate_to() {
        return 'background';
    }
}

/**
 * A background repeat style.
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundrepeat extends css_style_generic {
    /**
     * Creates a new background colour style
     *
     * @param mixed $value
     * @return css_style_backgroundrepeat
     */
    public static function init($value) {
        return new css_style_backgroundrepeat('background-repeat', $value);
    }
    public function consolidate_to() {
        return 'background';
    }
}

/**
 * A background attachment style.
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundattachment extends css_style_generic {
    /**
     * Creates a new background colour style
     *
     * @param mixed $value
     * @return css_style_backgroundattachment
     */
    public static function init($value) {
        return new css_style_backgroundattachment('background-attachment', $value);
    }
    public function consolidate_to() {
        return 'background';
    }
}

/**
 * A background position style.
 *
 * @package   moodlecore
 * @copyright 2011 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundposition extends css_style_generic {
    /**
     * Creates a new background colour style
     *
     * @param mixed $value
     * @return css_style_backgroundposition
     */
    public static function init($value) {
        return new css_style_backgroundposition('background-position', $value);
    }
    public function consolidate_to() {
        return 'background';
    }
}

class css_style_padding extends css_style {
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 4);

        $top = $right = $bottom = $left = null;
        if (count($bits) > 0) {
            $top = $right = $bottom = $left = array_shift($bits);
        }
        if (count($bits) > 0) {
            $right = $left = array_shift($bits);
        }
        if (count($bits) > 0) {
            $bottom = array_shift($bits);
        }
        if (count($bits) > 0) {
            $left = array_shift($bits);
        }
        return array(
            new css_style_paddingtop('padding-top', $top),
            new css_style_paddingright('padding-right', $right),
            new css_style_paddingbottom('padding-bottom', $bottom),
            new css_style_paddingleft('padding-left', $left)
        );
    }
    public static function consolidate(array $styles) {
        if (count($styles) != 4) {
            return $styles;
        }
        $top = $right = $bottom = $left = null;
        foreach ($styles as $style) {
            switch ($style->get_name()) {
                case 'padding-top' : $top = $style->get_value();break;
                case 'padding-right' : $right = $style->get_value();break;
                case 'padding-bottom' : $bottom = $style->get_value();break;
                case 'padding-left' : $left = $style->get_value();break;
            }
        }
        if ($top == $bottom && $left == $right) {
            if ($top == $left) {
                return array(new css_style_padding('padding', $top));
            } else {
                return array(new css_style_padding('padding', "{$top} {$left}"));
            }
        } else if ($left == $right) {
            return array(new css_style_padding('padding', "{$top} {$right} {$bottom}"));
        } else {
            return array(new css_style_padding('padding', "{$top} {$right} {$bottom} {$left}"));
        }

    }
}

class css_style_paddingtop extends css_style {
    public static function init($value) {
        return new css_style_paddingtop('padding-top', $value);
    }
    public function consolidate_to() {
        return 'padding';
    }
}

class css_style_paddingright extends css_style {
    public static function init($value) {
        return new css_style_paddingright('padding-right', $value);
    }
    public function consolidate_to() {
        return 'padding';
    }
}

class css_style_paddingbottom extends css_style {
    public static function init($value) {
        return new css_style_paddingbottom('padding-bottom', $value);
    }
    public function consolidate_to() {
        return 'padding';
    }
}

class css_style_paddingleft extends css_style {
    public static function init($value) {
        return new css_style_paddingleft('padding-left', $value);
    }
    public function consolidate_to() {
        return 'padding';
    }
}