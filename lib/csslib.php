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
 * This file contains CSS related class, and function for the CSS optimiser
 *
 * Please see the {@link css_optimiser} class for greater detail.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: do not verify MOODLE_INTERNAL here, this is used from themes too

/**
 * Stores CSS in a file at the given path.
 *
 * This function either succeeds or throws an exception.
 *
 * @param theme_config $theme The theme that the CSS belongs to.
 * @param string $csspath The path to store the CSS at.
 * @param array $cssfiles The CSS files to store.
 * @param bool $chunk If set to true these files will be chunked to ensure
 *      that no one file contains more than 4095 selectors.
 * @param string $chunkurl If the CSS is be chunked then we need to know the URL
 *      to use for the chunked files.
 */
function css_store_css(theme_config $theme, $csspath, array $cssfiles, $chunk = false, $chunkurl = null) {
    global $CFG;

    // Check if both the CSS optimiser is enabled and the theme supports it.
    if (!empty($CFG->enablecssoptimiser) && $theme->supportscssoptimisation) {
        // This is an experimental feature introduced in Moodle 2.3
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
        if (!empty($CFG->cssoptimiserstats)) {
            $css = $optimiser->output_stats_css().$css;
        }
    } else {
        // This is the default behaviour.
        // The cssoptimise setting was introduced in Moodle 2.3 and will hopefully
        // in the future be changed from an experimental setting to the default.
        // The css_minify_css will method will use the Minify library remove
        // comments, additional whitespace and other minor measures to reduce the
        // the overall CSS being sent.
        // However it has the distinct disadvantage of having to minify the CSS
        // before running the post process functions. Potentially things may break
        // here if theme designers try to push things with CSS post processing.
        $css = $theme->post_process(css_minify_css($cssfiles));
    }

    if ($chunk) {
        // Chunk the CSS if requried.
        $css = css_chunk_by_selector_count($css, $chunkurl);
    } else {
        $css = array($css);
    }

    clearstatcache();
    if (!file_exists(dirname($csspath))) {
        @mkdir(dirname($csspath), $CFG->directorypermissions, true);
    }

    // Prevent serving of incomplete file from concurrent request,
    // the rename() should be more atomic than fwrite().
    ignore_user_abort(true);

    $files = count($css);
    $count = 0;
    foreach ($css as $content) {
        if ($files > 1 && ($count+1) !== $files) {
            // If there is more than one file and this is not the last file.
            $filename = preg_replace('#\.css$#', '.'.$count.'.css', $csspath);
            $count++;
        } else {
            $filename = $csspath;
        }
        if ($fp = fopen($filename.'.tmp', 'xb')) {
            fwrite($fp, $content);
            fclose($fp);
            rename($filename.'.tmp', $filename);
            @chmod($filename, $CFG->filepermissions);
            @unlink($filename.'.tmp'); // just in case anything fails
        }
    }

    ignore_user_abort(false);
    if (connection_aborted()) {
        die;
    }
}

/**
 * Takes CSS and chunks it if the number of selectors within it exceeds $maxselectors.
 *
 * @param string $css The CSS to chunk.
 * @param string $importurl The URL to use for import statements.
 * @param int $maxselectors The number of selectors to limit a chunk to.
 * @param int $buffer The buffer size to use when chunking. You shouldn't need to reduce this
 *      unless you are lowering the maximum selectors.
 * @return array An array of CSS chunks.
 */
function css_chunk_by_selector_count($css, $importurl, $maxselectors = 4095, $buffer = 50) {
    // Check if we need to chunk this CSS file.
    $count = substr_count($css, ',') + substr_count($css, '{');
    if ($count < $maxselectors) {
        // The number of selectors is less then the max - we're fine.
        return array($css);
    }

    // Chunk time ?!
    // Split the CSS by array, making sure to save the delimiter in the process.
    $parts = preg_split('#([,\}])#', $css, null, PREG_SPLIT_DELIM_CAPTURE + PREG_SPLIT_NO_EMPTY);
    // We need to chunk the array. Each delimiter is stored separately so we multiple by 2.
    // We also subtract 100 to give us a small buffer just in case.
    $parts = array_chunk($parts, $maxselectors * 2 - $buffer * 2);
    $css = array();
    $partcount = count($parts);
    foreach ($parts as $key => $chunk) {
        if (end($chunk) === ',') {
            // Damn last element was a comma.
            // Pretty much the only way to deal with this is to take the styles from the end of the
            // comma separated chain of selectors and apply it to the last selector we have here in place
            // of the comma.
            // Unit tests are essential for making sure this works.
            $styles = false;
            $i = $key;
            while ($styles === false && $i < ($partcount - 1)) {
                $i++;
                $nextpart = $parts[$i];
                foreach ($nextpart as $style) {
                    if (strpos($style, '{') !== false) {
                        $styles = preg_replace('#^[^\{]+#', '', $style);
                        break;
                    }
                }
            }
            if ($styles === false) {
                $styles = '/** Error chunking CSS **/';
            } else {
                $styles .= '}';
            }
            array_pop($chunk);
            array_push($chunk, $styles);
        }
        $css[] = join('', $chunk);
    }
    // The array $css now contains CSS split into perfect sized chunks.
    // Import statements can only appear at the very top of a CSS file.
    // Imported sheets are applied in the the order they are imported and
    // are followed by the contents of the CSS.
    // This is terrible for performance.
    // It means we must put the import statements at the top of the last chunk
    // to ensure that things are always applied in the correct order.
    // This way the chunked files are included in the order they were chunked
    // followed by the contents of the final chunk in the actual sheet.
    $importcss = '';
    $slashargs = strpos($importurl, '.php?') === false;
    $parts = count($css);
    for ($i = 0; $i < $parts - 1; $i++) {
        if ($slashargs) {
            $importcss .= "@import url({$importurl}/chunk{$i});\n";
        } else {
            $importcss .= "@import url({$importurl}&chunk={$i});\n";
        }
    }
    $importcss .= end($css);
    $css[key($css)] = $importcss;

    return $css;
}

/**
 * Sends IE specific CSS
 *
 * In writing the CSS parser I have a theory that we could optimise the CSS
 * then split it based upon the number of selectors to ensure we dont' break IE
 * and that we include only as many sub-stylesheets as we require.
 * Of course just a theory but may be fun to code.
 *
 * @param string $themename The name of the theme we are sending CSS for.
 * @param string $rev The revision to ensure we utilise the cache.
 * @param string $etag The revision to ensure we utilise the cache.
 * @param bool $slasharguments
 */
function css_send_ie_css($themename, $rev, $etag, $slasharguments) {
    global $CFG;

    $lifetime = 60*60*24*60; // 60 days only - the revision may get incremented quite often

    $relroot = preg_replace('|^http.?://[^/]+|', '', $CFG->wwwroot);

    $css  = "/** Unfortunately IE6-9 does not support more than 4096 selectors in one CSS file, which means we have to use some ugly hacks :-( **/";
    if ($slasharguments) {
        $css .= "\n@import url($relroot/styles.php/$themename/$rev/plugins);";
        $css .= "\n@import url($relroot/styles.php/$themename/$rev/parents);";
        $css .= "\n@import url($relroot/styles.php/$themename/$rev/theme);";
    } else {
        $css .= "\n@import url($relroot/styles.php?theme=$themename&rev=$rev&type=plugins);";
        $css .= "\n@import url($relroot/styles.php?theme=$themename&rev=$rev&type=parents);";
        $css .= "\n@import url($relroot/styles.php?theme=$themename&rev=$rev&type=theme);";
    }

    header('Etag: "'.$etag.'"');
    header('Content-Disposition: inline; filename="styles.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age='.$lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    header('Content-Length: '.strlen($css));

    echo $css;
    die;
}

/**
 * Sends a cached CSS file
 *
 * This function sends the cached CSS file. Remember it is generated on the first
 * request, then optimised/minified, and finally cached for serving.
 *
 * @param string $csspath The path to the CSS file we want to serve.
 * @param string $etag The revision to make sure we utilise any caches.
 */
function css_send_cached_css($csspath, $etag) {
    $lifetime = 60*60*24*60; // 60 days only - the revision may get incremented quite often

    header('Etag: "'.$etag.'"');
    header('Content-Disposition: inline; filename="styles.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($csspath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age='.$lifetime);
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
 * This function takes a raw CSS string, optimises it if required, and then
 * serves it.
 * Turning both themedesignermode and CSS optimiser on at the same time is awful
 * for performance because of the optimiser running here. However it was done so
 * that theme designers could utilise the optimised output during development to
 * help them optimise their CSS... not that they should write lazy CSS.
 *
 * @param string $css
 */
function css_send_uncached_css($css, $themesupportsoptimisation = true) {
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

    echo $css;

    die;
}

/**
 * Send file not modified headers
 * @param int $lastmodified
 * @param string $etag
 */
function css_send_unmodified($lastmodified, $etag) {
    $lifetime = 60*60*24*60; // 60 days only - the revision may get incremented quite often
    header('HTTP/1.1 304 Not Modified');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Cache-Control: public, max-age='.$lifetime);
    header('Content-Type: text/css; charset=utf-8');
    header('Etag: "'.$etag.'"');
    if ($lastmodified) {
        header('Last-Modified: '. gmdate('D, d M Y H:i:s', $lastmodified) .' GMT');
    }
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
 * Uses the minify library to compress CSS.
 *
 * This is used if $CFG->enablecssoptimiser has been turned off. This was
 * the original CSS optimisation library.
 * It removes whitespace and shrinks things but does no apparent optimisation.
 * Note the minify library is still being used for JavaScript.
 *
 * @param array $files An array of files to minify
 * @return string The minified CSS
 */
function css_minify_css($files) {
    global $CFG;

    if (empty($files)) {
        return '';
    }

    // We do not really want any 304 here!
    // There does not seem to be any better way to prevent them here.
    unset($_SERVER['HTTP_IF_NONE_MATCH']);
    unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);

    set_include_path($CFG->libdir . '/minify/lib' . PATH_SEPARATOR . get_include_path());
    require_once('Minify.php');

    if (0 === stripos(PHP_OS, 'win')) {
        Minify::setDocRoot(); // IIS may need help
    }
    // disable all caching, we do it in moodle
    Minify::setCache(null, false);

    $options = array(
        // JSMin is not GNU GPL compatible, use the plus version instead.
        'minifiers' => array(Minify::TYPE_JS => array('JSMinPlus', 'minify')),
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

    $error = 'unknown';
    try {
        $result = Minify::serve('Files', $options);
        if ($result['success'] and $result['statusCode'] == 200) {
            return $result['content'];
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
        $error = str_replace("\r", ' ', $error);
        $error = str_replace("\n", ' ', $error);
    }

    // minification failed - try to inform the theme developer and include the non-minified version
    $css = <<<EOD
/* Error: $error */
/* Problem detected during theme CSS minimisation, please review the following code */
/* ================================================================================ */


EOD;
    foreach ($files as $cssfile) {
        $css .= file_get_contents($cssfile)."\n";
    }
    return $css;
}

/**
 * Determines if the given value is a valid CSS colour.
 *
 * A CSS colour can be one of the following:
 *    - Hex colour:  #AA66BB
 *    - RGB colour:  rgb(0-255, 0-255, 0-255)
 *    - RGBA colour: rgba(0-255, 0-255, 0-255, 0-1)
 *    - HSL colour:  hsl(0-360, 0-100%, 0-100%)
 *    - HSLA colour: hsla(0-360, 0-100%, 0-100%, 0-1)
 *
 * Or a recognised browser colour mapping {@link css_optimiser::$htmlcolours}
 *
 * @param string $value The colour value to check
 * @return bool
 */
function css_is_colour($value) {
    $value = trim($value);

    $hex  = '/^#([a-fA-F0-9]{1,3}|[a-fA-F0-9]{6})$/';
    $rgb  = '#^rgb\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*\)$#i';
    $rgba = '#^rgba\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1,3})\s*,\s*(\d{1}(\.\d+)?)\s*\)$#i';
    $hsl  = '#^hsl\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\%\s*,\s*(\d{1,3})\%\s*\)$#i';
    $hsla = '#^hsla\s*\(\s*(\d{1,3})\s*,\s*(\d{1,3})\%\s*,\s*(\d{1,3})\%\s*,\s*(\d{1}(\.\d+)?)\s*\)$#i';

    if (in_array(strtolower($value), array('inherit'))) {
        return true;
    } else if (preg_match($hex, $value)) {
        return true;
    } else if (in_array(strtolower($value), array_keys(css_optimiser::$htmlcolours))) {
        return true;
    } else if (preg_match($rgb, $value, $m) && $m[1] < 256 && $m[2] < 256 && $m[3] < 256) {
        // It is an RGB colour
        return true;
    } else if (preg_match($rgba, $value, $m) && $m[1] < 256 && $m[2] < 256 && $m[3] < 256) {
        // It is an RGBA colour
        return true;
    } else if (preg_match($hsl, $value, $m) && $m[1] <= 360 && $m[2] <= 100 && $m[3] <= 100) {
        // It is an HSL colour
        return true;
    } else if (preg_match($hsla, $value, $m) && $m[1] <= 360 && $m[2] <= 100 && $m[3] <= 100) {
        // It is an HSLA colour
        return true;
    }
    // Doesn't look like a colour.
    return false;
}

/**
 * Returns true is the passed value looks like a CSS width.
 * In order to pass this test the value must be purely numerical or end with a
 * valid CSS unit term.
 *
 * @param string|int $value
 * @return boolean
 */
function css_is_width($value) {
    $value = trim($value);
    if (in_array(strtolower($value), array('auto', 'inherit'))) {
        return true;
    }
    if ((string)$value === '0' || preg_match('#^(\-\s*)?(\d*\.)?(\d+)\s*(em|px|pt|\%|in|cm|mm|ex|pc)$#i', $value)) {
        return true;
    }
    return false;
}

/**
 * A simple sorting function to sort two array values on the number of items they contain
 *
 * @param array $a
 * @param array $b
 * @return int
 */
function css_sort_by_count(array $a, array $b) {
    $a = count($a);
    $b = count($b);
    if ($a == $b) {
        return 0;
    }
    return ($a > $b) ? -1 : 1;
}

/**
 * A basic CSS optimiser that strips out unwanted things and then processing the
 * CSS organising styles and moving duplicates and useless CSS.
 *
 * This CSS optimiser works by reading through a CSS string one character at a
 * time and building an object structure of the CSS.
 * As part of that processing styles are expanded out as much as they can be to
 * ensure we collect all mappings, at the end of the processing those styles are
 * then combined into an optimised form to keep them as short as possible.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_optimiser {

    /**
     * Used when the processor is about to start processing.
     * Processing states. Used internally.
     */
    const PROCESSING_START = 0;

    /**
     * Used when the processor is currently processing a selector.
     * Processing states. Used internally.
     */
    const PROCESSING_SELECTORS = 0;

    /**
     * Used when the processor is currently processing a style.
     * Processing states. Used internally.
     */
    const PROCESSING_STYLES = 1;

    /**
     * Used when the processor is currently processing a comment.
     * Processing states. Used internally.
     */
    const PROCESSING_COMMENT = 2;

    /**
     * Used when the processor is currently processing an @ rule.
     * Processing states. Used internally.
     */
    const PROCESSING_ATRULE = 3;

    /**
     * The raw string length before optimisation.
     * Stats variables set during and after processing
     * @var int
     */
    protected $rawstrlen = 0;

    /**
     * The number of comments that were removed during optimisation.
     * Stats variables set during and after processing
     * @var int
     */
    protected $commentsincss = 0;

    /**
     * The number of rules in the CSS before optimisation.
     * Stats variables set during and after processing
     * @var int
     */
    protected $rawrules = 0;

    /**
     * The number of selectors using in CSS rules before optimisation.
     * Stats variables set during and after processing
     * @var int
     */
    protected $rawselectors = 0;

    /**
     * The string length after optimisation.
     * Stats variables set during and after processing
     * @var int
     */
    protected $optimisedstrlen = 0;

    /**
     * The number of rules after optimisation.
     * Stats variables set during and after processing
     * @var int
     */
    protected $optimisedrules = 0;

    /**
     * The number of selectors used in rules after optimisation.
     * Stats variables set during and after processing
     * @var int
     */
    protected $optimisedselectors = 0;

    /**
     * The start time of the optimisation.
     * Stats variables set during and after processing
     * @var int
     */
    protected $timestart = 0;

    /**
     * The end time of the optimisation.
     * Stats variables set during and after processing
     * @var int
     */
    protected $timecomplete = 0;

    /**
     * Will be set to any errors that may have occured during processing.
     * This is updated only at the end of processing NOT during.
     *
     * @var array
     */
    protected $errors = array();

    /**
     * Processes incoming CSS optimising it and then returning it.
     *
     * @param string $css The raw CSS to optimise
     * @return string The optimised CSS
     */
    public function process($css) {
        global $CFG;

        // Easiest win there is
        $css = trim($css);

        $this->reset_stats();
        $this->timestart = microtime(true);
        $this->rawstrlen = strlen($css);

        // Don't try to process files with no content... it just doesn't make sense.
        // But we should produce an error for them, an empty CSS file will lead to a
        // useless request for those running theme designer mode.
        if ($this->rawstrlen === 0) {
            $this->errors[] = 'Skipping file as it has no content.';
            return '';
        }

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
        // Keyframes are used for CSS animation they will be processed right at the very end.
        $keyframes = array();

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
        // This is why we removed line breaks and comments!
        for ($i = 0; $i < $this->rawstrlen; $i++) {
            $lastchar = $char;
            $char = substr($css, $i, 1);
            if ($char == '@' && $buffer == '') {
                $suspectatrule = true;
            }
            switch ($currentprocess) {
                // Start processing an @ rule e.g. @media, @page, @keyframes
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
                            if ($currentatrule !== 'media') {
                                $buffer = '';
                                $currentatrule = false;
                            }
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
                            continue 3;
                        case '{':
                            if ($currentatrule == 'media' && preg_match('#\s*@media\s*([a-zA-Z0-9]+(\s*,\s*[a-zA-Z0-9]+)*)\s*{#', $buffer, $matches)) {
                                // Basic media declaration
                                $mediatypes = str_replace(' ', '', $matches[1]);
                                if (!array_key_exists($mediatypes, $medias)) {
                                    $medias[$mediatypes] = new css_media($mediatypes);
                                }
                                $currentmedia = $medias[$mediatypes];
                                $currentprocess = self::PROCESSING_SELECTORS;
                                $buffer = '';
                            } else if ($currentatrule == 'media' && preg_match('#\s*@media\s*([^{]+)#', $buffer, $matches)) {
                                // Advanced media query declaration http://www.w3.org/TR/css3-mediaqueries/
                                $mediatypes = $matches[1];
                                $hash = md5($mediatypes);
                                $medias[$hash] = new css_media($mediatypes);
                                $currentmedia = $medias[$hash];
                                $currentprocess = self::PROCESSING_SELECTORS;
                                $buffer = '';
                            } else if ($currentatrule == 'keyframes' && preg_match('#@((\-moz\-|\-webkit\-)?keyframes)\s*([^\s]+)#', $buffer, $matches)) {
                                // Keyframes declaration, we treat it exactly like a @media declaration except we don't allow
                                // them to be overridden to ensure we don't mess anything up. (means we keep everything in order)
                                $keyframefor = $matches[1];
                                $keyframename = $matches[3];
                                $keyframe = new css_keyframe($keyframefor, $keyframename);
                                $keyframes[] = $keyframe;
                                $currentmedia = $keyframe;
                                $currentprocess = self::PROCESSING_SELECTORS;
                                $buffer = '';
                            }
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
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
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
                            continue 3;
                        case ']':
                            $inbrackets --;
                            $buffer .= $char;
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
                            continue 3;
                        case ' ':
                            if ($inbrackets) {
                                // continue 1: The switch processing chars
                                // continue 2: The switch processing the state
                                // continue 3: The for loop
                                continue 3;
                            }
                            if (!empty($buffer)) {
                                // Check for known @ rules
                                if ($suspectatrule && preg_match('#@(media|import|charset|(\-moz\-|\-webkit\-)?(keyframes))\s*#', $buffer, $matches)) {
                                    $currentatrule = (!empty($matches[3]))?$matches[3]:$matches[1];
                                    $currentprocess = self::PROCESSING_ATRULE;
                                    $buffer .= $char;
                                } else {
                                    $currentselector->add($buffer);
                                    $buffer = '';
                                }
                            }
                            $suspectatrule = false;
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
                            continue 3;
                        case '{':
                            if ($inbrackets) {
                                // continue 1: The switch processing chars
                                // continue 2: The switch processing the state
                                // continue 3: The for loop
                                continue 3;
                            }
                            if ($buffer !== '') {
                                $currentselector->add($buffer);
                            }
                            $currentrule->add_selector($currentselector);
                            $currentselector = css_selector::init();
                            $currentprocess = self::PROCESSING_STYLES;

                            $buffer = '';
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
                            continue 3;
                        case '}':
                            if ($inbrackets) {
                                // continue 1: The switch processing chars
                                // continue 2: The switch processing the state
                                // continue 3: The for loop
                                continue 3;
                            }
                            if ($currentatrule == 'media') {
                                $currentmedia = $medias['all'];
                                $currentatrule = false;
                                $buffer = '';
                            } else if (strpos($currentatrule, 'keyframes') !== false) {
                                $currentmedia = $medias['all'];
                                $currentatrule = false;
                                $buffer = '';
                            }
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
                            continue 3;
                        case ',':
                            if ($inbrackets) {
                                // continue 1: The switch processing chars
                                // continue 2: The switch processing the state
                                // continue 3: The for loop
                                continue 3;
                            }
                            $currentselector->add($buffer);
                            $currentrule->add_selector($currentselector);
                            $currentselector = css_selector::init();
                            $buffer = '';
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
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
                            if ($inparenthesis) {
                                $buffer .= $char;
                                // continue 1: The switch processing chars
                                // continue 2: The switch processing the state
                                // continue 3: The for loop
                                continue 3;
                            }
                            $currentrule->add_style($buffer);
                            $buffer = '';
                            $inquotes = false;
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
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
                            $inparenthesis = false;
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
                            continue 3;
                        case '(':
                            $inparenthesis = true;
                            $buffer .= $char;
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
                            continue 3;
                        case ')':
                            $inparenthesis = false;
                            $buffer .= $char;
                            // continue 1: The switch processing chars
                            // continue 2: The switch processing the state
                            // continue 3: The for loop
                            continue 3;
                    }
                    break;
            }
            $buffer .= $char;
        }

        foreach ($medias as $media) {
            $this->optimise($media);
        }
        $css = $this->produce_css($charset, $imports, $medias, $keyframes);

        $this->timecomplete = microtime(true);
        return trim($css);
    }

    /**
     * Produces CSS for the given charset, imports, media, and keyframes
     * @param string $charset
     * @param array $imports
     * @param array $medias
     * @param array $keyframes
     * @return string
     */
    protected function produce_css($charset, array $imports, array $medias, array $keyframes) {
        $css = '';
        if (!empty($charset)) {
            $imports[] = $charset;
        }
        if (!empty($imports)) {
            $css .= implode("\n", $imports);
            $css .= "\n\n";
        }

        $cssreset = array();
        $cssstandard = array();
        $csskeyframes = array();

        // Process each media declaration individually
        foreach ($medias as $media) {
            // If this declaration applies to all media types
            if (in_array('all', $media->get_types())) {
                // Collect all rules that represet reset rules and remove them from the media object at the same time.
                // We do this because we prioritise reset rules to the top of a CSS output. This ensures that they
                // can't end up out of order because of optimisation.
                $resetrules = $media->get_reset_rules(true);
                if (!empty($resetrules)) {
                    $cssreset[] = css_writer::media('all', $resetrules);
                }
            }
            // Get the standard cSS
            $cssstandard[] = $media->out();
        }

        // Finally if there are any keyframe declarations process them now.
        if (count($keyframes) > 0) {
            foreach ($keyframes as $keyframe) {
                $this->optimisedrules += $keyframe->count_rules();
                $this->optimisedselectors +=  $keyframe->count_selectors();
                if ($keyframe->has_errors()) {
                    $this->errors += $keyframe->get_errors();
                }
                $csskeyframes[] = $keyframe->out();
            }
        }

        // Join it all together
        $css .= join('', $cssreset);
        $css .= join('', $cssstandard);
        $css .= join('', $csskeyframes);

        // Record the strlenght of the now optimised CSS.
        $this->optimisedstrlen = strlen($css);

        // Return the now produced CSS
        return $css;
    }

    /**
     * Optimises the CSS rules within a rule collection of one form or another
     *
     * @param css_rule_collection $media
     * @return void This function acts in reference
     */
    protected function optimise(css_rule_collection $media) {
        $media->organise_rules_by_selectors();
        $this->optimisedrules += $media->count_rules();
        $this->optimisedselectors +=  $media->count_selectors();
        if ($media->has_errors()) {
            $this->errors += $media->get_errors();
        }
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
            'optimisedselectors'    => $this->optimisedselectors,
            'improvementstrlen'     => '-',
            'improvementrules'     => '-',
            'improvementselectors'     => '-',
        );
        // Avoid division by 0 errors by checking we have valid raw values
        if ($this->rawstrlen > 0) {
            $stats['improvementstrlen'] = round(100 - ($this->optimisedstrlen / $this->rawstrlen) * 100, 1).'%';
        }
        if ($this->rawrules > 0) {
            $stats['improvementrules'] = round(100 - ($this->optimisedrules / $this->rawrules) * 100, 1).'%';
        }
        if ($this->rawselectors > 0) {
            $stats['improvementselectors'] = round(100 - ($this->optimisedselectors / $this->rawselectors) * 100, 1).'%';
        }
        return $stats;
    }

    /**
     * Returns true if any errors have occured during processing
     *
     * @return bool
     */
    public function has_errors() {
        return !empty($this->errors);
    }

    /**
     * Returns an array of errors that have occured
     *
     * @param bool $clear If set to true the errors will be cleared after being returned.
     * @return array
     */
    public function get_errors($clear = false) {
        $errors = $this->errors;
        if ($clear) {
            // Reset the error array
            $this->errors = array();
        }
        return $errors;
    }

    /**
     * Returns any errors as a string that can be included in CSS.
     *
     * @return string
     */
    public function output_errors_css() {
        $computedcss  = "/****************************************\n";
        $computedcss .= " *--- Errors found during processing ----\n";
        foreach ($this->errors as $error) {
            $computedcss .= preg_replace('#^#m', '* ', $error);
        }
        $computedcss .= " ****************************************/\n\n";
        return $computedcss;
    }

    /**
     * Returns a string to display stats about the last generation within CSS output
     *
     * @return string
     */
    public function output_stats_css() {

        $computedcss  = "/****************************************\n";
        $computedcss .= " *------- CSS Optimisation stats --------\n";

        if ($this->rawstrlen === 0) {
            $computedcss .= " File not processed as it has no content /\n\n";
            $computedcss .= " ****************************************/\n\n";
            return $computedcss;
        } else if ($this->rawrules === 0) {
            $computedcss .= " File contained no rules to be processed /\n\n";
            $computedcss .= " ****************************************/\n\n";
            return $computedcss;
        }

        $stats = $this->get_stats();

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
        'transparent' => 'transparent',
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
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
        return (!empty($CFG->cssoptimiserpretty));
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
     * Decreases the current indent
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
            $output .= "\n@media {$typestring} {".$nl;
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
     * Returns CSS for a keyframe
     *
     * @param string $for The desired declaration. e.g. keyframes, -moz-keyframes, -webkit-keyframes
     * @param string $name The name for the keyframe
     * @param array $rules An array of rules belonging to the keyframe
     * @return string
     */
    public static function keyframe($for, $name, array &$rules) {
        $nl = self::get_separator();

        $output = "\n@{$for} {$name} {";
        foreach ($rules as $rule) {
            $output .= $rule->out();
        }
        $output .= '}';
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
     * Returns a CSS string for the provided styles
     *
     * @param array $styles Array of css_style objects
     * @return string
     */
    public static function styles(array $styles) {
        $bits = array();
        foreach ($styles as $style) {
            // Check if the style is an array. If it is then we are outputing an advanced style.
            // An advanced style is a style with one or more values, and can occur in situations like background-image
            // where browse specific values are being used.
            if (is_array($style)) {
                foreach ($style as $advstyle) {
                    $bits[] = $advstyle->out();
                }
                continue;
            }
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
        $value = trim($value);
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
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
     * Is null if there are no selectors, true if all selectors are basic and false otherwise.
     * A basic selector is one that consists of just the element type. e.g. div, span, td, a
     * @var bool|null
     */
    protected $isbasic = null;

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
        // If its already false then no need to continue, its not basic
        if ($this->isbasic !== false) {
            // If theres more than one part making up this selector its not basic
            if ($count > 1) {
                $this->isbasic = false;
            } else {
                // Check whether it is a basic element (a-z+) with possible psuedo selector
                $this->isbasic = (bool)preg_match('#^[a-z]+(:[a-zA-Z]+)?$#', $selector);
            }
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

    /**
     * Returns true is all of the selectors act only upon basic elements (no classes/ids)
     * @return bool
     */
    public function is_basic() {
        return ($this->isbasic === true);
    }
}

/**
 * A structure to represent a CSS rule.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_rule {

    /**
     * An array of CSS selectors {@link css_selector}
     * @var array
     */
    protected $selectors = array();

    /**
     * An array of CSS styles {@link css_style}
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
     * Constructs a new css rule.
     *
     * @param string $selector The selector or array of selectors that make up this rule.
     * @param array $styles An array of styles that belong to this rule.
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
     * e.g. $rule->add_selector('.one #two.two');
     *
     * @param css_selector $selector Adds a CSS selector to this rule.
     */
    public function add_selector(css_selector $selector) {
        $this->selectors[] = $selector;
    }

    /**
     * Adds a new CSS style to this rule.
     *
     * @param css_style|string $style Adds a new style to this rule
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
                $style = css_style::init_automatic($name, $value);
            }
        } else if ($style instanceof css_style) {
            // Clone the style as it may be coming from another rule and we don't
            // want references as it will likely be overwritten by proceeding
            // rules
            $style = clone($style);
        }
        if ($style instanceof css_style) {
            $name = $style->get_name();
            $exists = array_key_exists($name, $this->styles);
            // We need to find out if the current style support multiple values, or whether the style
            // is already set up to record multiple values. This can happen with background images which can have single
            // and multiple values.
            if ($style->allows_multiple_values() || ($exists && is_array($this->styles[$name]))) {
                if (!$exists) {
                    $this->styles[$name] = array();
                } else if ($this->styles[$name] instanceof css_style) {
                    $this->styles[$name] = array($this->styles[$name]);
                }
                $this->styles[$name][] = $style;
            } else if ($exists) {
                $this->styles[$name]->set_value($style->get_value());
            } else {
                $this->styles[$name] = $style;
            }
        } else if (is_array($style)) {
            // We probably shouldn't worry about processing styles here but to
            // be truthful it doesn't hurt.
            foreach ($style as $astyle) {
                $this->add_style($astyle);
            }
        }
    }

    /**
     * An easy method of adding several styles at once. Just calls add_style.
     *
     * This method simply iterates over the array and calls {@link css_rule::add_style()}
     * with each.
     *
     * @param array $styles Adds an array of styles
     */
    public function add_styles(array $styles) {
        foreach ($styles as $style) {
            $this->add_style($style);
        }
    }

    /**
     * Returns the array of selectors
     *
     * @return array
     */
    public function get_selectors() {
        return $this->selectors;
    }

    /**
     * Returns the array of styles
     *
     * @return array
     */
    public function get_styles() {
        return $this->styles;
    }

    /**
     * Outputs this rule as a fragment of CSS
     *
     * @return string
     */
    public function out() {
        $selectors = css_writer::selectors($this->selectors);
        $styles = css_writer::styles($this->get_consolidated_styles());
        return css_writer::rule($selectors, $styles);
    }

    /**
     * Consolidates all styles associated with this rule
     *
     * @return array An array of consolidated styles
     */
    public function get_consolidated_styles() {
        $organisedstyles = array();
        $finalstyles = array();
        $consolidate = array();
        $advancedstyles = array();
        foreach ($this->styles as $style) {
            // If the style is an array then we are processing an advanced style. An advanced style is a style that can have
            // one or more values. Background-image is one such example as it can have browser specific styles.
            if (is_array($style)) {
                $single = null;
                $count = 0;
                foreach ($style as $advstyle) {
                    $key = $count++;
                    $advancedstyles[$key] = $advstyle;
                    if (!$advstyle->allows_multiple_values()) {
                        if (!is_null($single)) {
                            unset($advancedstyles[$single]);
                        }
                        $single = $key;
                    }
                }
                if (!is_null($single)) {
                    $style = $advancedstyles[$single];

                    $consolidatetoclass = $style->consolidate_to();
                    if (($style->is_valid() || $style->is_special_empty_value()) && !empty($consolidatetoclass) && class_exists('css_style_'.$consolidatetoclass)) {
                        $class = 'css_style_'.$consolidatetoclass;
                        if (!array_key_exists($class, $consolidate)) {
                            $consolidate[$class] = array();
                            $organisedstyles[$class] = true;
                        }
                        $consolidate[$class][] = $style;
                        unset($advancedstyles[$single]);
                    }
                }

                continue;
            }
            $consolidatetoclass = $style->consolidate_to();
            if (($style->is_valid() || $style->is_special_empty_value()) && !empty($consolidatetoclass) && class_exists('css_style_'.$consolidatetoclass)) {
                $class = 'css_style_'.$consolidatetoclass;
                if (!array_key_exists($class, $consolidate)) {
                    $consolidate[$class] = array();
                    $organisedstyles[$class] = true;
                }
                $consolidate[$class][] = $style;
            } else {
                $organisedstyles[$style->get_name()] = $style;
            }
        }

        foreach ($consolidate as $class => $styles) {
            $organisedstyles[$class] = $class::consolidate($styles);
        }

        foreach ($organisedstyles as $style) {
            if (is_array($style)) {
                foreach ($style as $s) {
                    $finalstyles[] = $s;
                }
            } else {
                $finalstyles[] = $style;
            }
        }
        $finalstyles = array_merge($finalstyles, $advancedstyles);
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
     * @return array Array of css_rule objects
     */
    public function split_by_style() {
        $return = array();
        foreach ($this->styles as $style) {
            if (is_array($style)) {
                $return[] = new css_rule($this->selectors, $style);
                continue;
            }
            $return[] = new css_rule($this->selectors, array($style));
        }
        return $return;
    }

    /**
     * Gets a hash for the styles of this rule
     *
     * @return string
     */
    public function get_style_hash() {
        return md5(css_writer::styles($this->styles));
    }

    /**
     * Gets a hash for the selectors of this rule
     *
     * @return string
     */
    public function get_selector_hash() {
        return md5(css_writer::selectors($this->selectors));
    }

    /**
     * Gets the number of selectors that make up this rule.
     *
     * @return int
     */
    public function get_selector_count() {
        $count = 0;
        foreach ($this->selectors as $selector) {
            $count += $selector->get_selector_count();
        }
        return $count;
    }

    /**
     * Returns true if there are any errors with this rule.
     *
     * @return bool
     */
    public function has_errors() {
        foreach ($this->styles as $style) {
            if (is_array($style)) {
                foreach ($style as $advstyle) {
                    if ($advstyle->has_error()) {
                        return true;
                    }
                }
                continue;
            }
            if ($style->has_error()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns the error strings that were recorded when processing this rule.
     *
     * Before calling this function you should first call {@link css_rule::has_errors()}
     * to make sure there are errors (hopefully there arn't).
     *
     * @return string
     */
    public function get_error_string() {
        $css = $this->out();
        $errors = array();
        foreach ($this->styles as $style) {
            if (is_array($style)) {
                foreach ($style as $s) {
                    if ($style instanceof css_style && $style->has_error()) {
                        $errors[] = "  * ".$style->get_last_error();
                    }
                }
            } else if ($style instanceof css_style && $style->has_error()) {
                $errors[] = "  * ".$style->get_last_error();
            }
        }
        return $css." has the following errors:\n".join("\n", $errors);
    }

    /**
     * Returns true if this rule could be considered a reset rule.
     *
     * A reset rule is a rule that acts upon an HTML element and does not include any other parts to its selector.
     *
     * @return bool
     */
    public function is_reset_rule() {
        foreach ($this->selectors as $selector) {
            if (!$selector->is_basic()) {
                return false;
            }
        }
        return true;
    }
}

/**
 * An abstract CSS rule collection class.
 *
 * This class is extended by things such as media and keyframe declaration. They are declarations that
 * group rules together for a purpose.
 * When no declaration is specified rules accumulate into @media all.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class css_rule_collection {
    /**
     * An array of rules within this collection instance
     * @var array
     */
    protected $rules = array();

    /**
     * The collection must be able to print itself.
     */
    abstract public function out();

    /**
     * Adds a new CSS rule to this collection instance
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
     * Returns the rules used by this collection
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
     * @return bool True if the CSS was optimised by this method
     */
    public function organise_rules_by_selectors() {
        $optimised = array();
        $beforecount = count($this->rules);
        $lasthash = null;
        $lastrule = null;
        foreach ($this->rules as $rule) {
            $hash = $rule->get_style_hash();
            if ($lastrule !== null && $lasthash !== null && $hash === $lasthash) {
                foreach ($rule->get_selectors() as $selector) {
                    $lastrule->add_selector($selector);
                }
                continue;
            }
            $lastrule = clone($rule);
            $lasthash = $hash;
            $optimised[] = $lastrule;
        }
        $this->rules = array();
        foreach ($optimised as $optimised) {
            $this->rules[$optimised->get_selector_hash()] = $optimised;
        }
        $aftercount = count($this->rules);
        return ($beforecount < $aftercount);
    }

    /**
     * Returns the total number of rules that exist within this collection
     *
     * @return int
     */
    public function count_rules() {
        return count($this->rules);
    }

    /**
     * Returns the total number of selectors that exist within this collection
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
     * Returns true if the collection has any rules that have errors
     *
     * @return boolean
     */
    public function has_errors() {
        foreach ($this->rules as $rule) {
            if ($rule->has_errors()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns any errors that have happened within rules in this collection.
     *
     * @return string
     */
    public function get_errors() {
        $errors = array();
        foreach ($this->rules as $rule) {
            if ($rule->has_errors()) {
                $errors[] = $rule->get_error_string();
            }
        }
        return $errors;
    }
}

/**
 * A media class to organise rules by the media they apply to.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_media extends css_rule_collection {

    /**
     * An array of the different media types this instance applies to.
     * @var array
     */
    protected $types = array();

    /**
     * Initalises a new media instance
     *
     * @param string $for The media that the contained rules are destined for.
     */
    public function __construct($for = 'all') {
        $types = explode(',', $for);
        $this->types = array_map('trim', $types);
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

    /**
     * Returns all of the reset rules known by this media set.
     * @param bool $remove If set to true reset rules will be removed before being returned.
     * @return array
     */
    public function get_reset_rules($remove = false) {
        $resetrules = array();
        foreach ($this->rules as $key => $rule) {
            if ($rule->is_reset_rule()) {
                $resetrules[] = clone $rule;
                if ($remove) {
                    unset($this->rules[$key]);
                }
            }
        }
        return $resetrules;
    }
}

/**
 * A media class to organise rules by the media they apply to.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_keyframe extends css_rule_collection {

    /** @var string $for The directive e.g. keyframes, -moz-keyframes, -webkit-keyframes  */
    protected $for;

    /** @var string $name The name for the keyframes */
    protected $name;
    /**
     * Constructs a new keyframe
     *
     * @param string $for The directive e.g. keyframes, -moz-keyframes, -webkit-keyframes
     * @param string $name The name for the keyframes
     */
    public function __construct($for, $name) {
        $this->for = $for;
        $this->name = $name;
    }
    /**
     * Returns the directive of this keyframe
     *
     * e.g. keyframes, -moz-keyframes, -webkit-keyframes
     * @return string
     */
    public function get_for() {
        return $this->for;
    }
    /**
     * Returns the name of this keyframe
     * @return string
     */
    public function get_name() {
        return $this->name;
    }
    /**
     * Returns the CSS for this collection of keyframes and all of its rules.
     *
     * @return string
     */
    public function out() {
        return css_writer::keyframe($this->for, $this->name, $this->rules);
    }
}

/**
 * An absract class to represent CSS styles
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class css_style {

    /** Constant used for recongise a special empty value in a CSS style */
    const NULL_VALUE = '@@$NULL$@@';

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
     * Only trolls use !important.
     * Don't hide under bridges.. its not good for your skin. Do the proper thing
     * and fix the issue don't just force a fix that will undoubtedly one day
     * lead to further frustration.
     * @var bool
     */
    protected $important = false;

    /**
     * Gets set to true if this style has an error
     * @var bool
     */
    protected $error = false;

    /**
     * The last error message that occured
     * @var string
     */
    protected $errormessage = null;

    /**
     * Initialises a new style.
     *
     * This is the only public way to create a style to ensure they that appropriate
     * style class is used if it exists.
     *
     * @param string $name The name of the style.
     * @param string $value The value of the style.
     * @return css_style_generic
     */
    public static function init_automatic($name, $value) {
        $specificclass = 'css_style_'.preg_replace('#[^a-zA-Z0-9]+#', '', $name);
        if (class_exists($specificclass)) {
            return $specificclass::init($value);
        }
        return new css_style_generic($name, $value);
    }

    /**
     * Creates a new style when given its name and value
     *
     * @param string $name The name of the style.
     * @param string $value The value of the style.
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
            $value = rtrim($value);
        }
        if (!$this->important || $important) {
            $this->value = $this->clean_value($value);
            $this->important = $important;
        }
        if (!$this->is_valid()) {
            $this->set_error('Invalid value for '.$this->name);
        }
    }

    /**
     * Returns true if the value associated with this style is valid
     *
     * @return bool
     */
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
     * @param bool $includeimportant If set to true and the rule is important !important postfix will be used.
     * @return string
     */
    public function get_value($includeimportant = true) {
        $value = $this->value;
        if ($includeimportant && $this->important) {
            $value .= ' !important';
        }
        return $value;
    }

    /**
     * Returns the style ready for use in CSS
     *
     * @param string|null $value A value to use to override the value for this style.
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

    /**
     * If this particular style can be consolidated into another style this function
     * should return the style that it can be consolidated into.
     *
     * @return string|null
     */
    public function consolidate_to() {
        return null;
    }

    /**
     * Sets the last error message.
     *
     * @param string $message
     */
    protected function set_error($message) {
        $this->error = true;
        $this->errormessage = $message;
    }

    /**
     * Returns true if an error has occured
     *
     * @return bool
     */
    public function has_error() {
        return $this->error;
    }

    /**
     * Returns the last error that occured or null if no errors have happened.
     *
     * @return string
     */
    public function get_last_error() {
        return $this->errormessage;
    }

    /**
     * Returns true if the value for this style is the special null value.
     *
     * This should only be overriden in circumstances where a shorthand style can lead
     * to move explicit styles being overwritten. Not a common place occurenace.
     *
     * Example:
     *   This occurs if the shorthand background property was used but no proper value
     *   was specified for this style.
     *   This leads to a null value being used unless otherwise overridden.
     *
     * @return bool
     */
    public function is_special_empty_value() {
        return false;
    }

    /**
     * Returns true if this style permits multiple values.
     *
     * This occurs for styles such as background image that can have browser specific values that need to be maintained because
     * of course we don't know what browser the user is using, and optimisation occurs before caching.
     * Thus we must always server all values we encounter in the order we encounter them for when this is set to true.
     *
     * @return boolean False by default, true if the style supports muliple values.
     */
    public function allows_multiple_values() {
        return false;
    }

    /**
     * Returns true if this style was marked important.
     * @return bool
     */
    public function is_important() {
        return !empty($this->important);
    }

    /**
     * Sets the important flag for this style and its current value.
     * @param bool $important
     */
    public function set_important($important = true) {
        $this->important = (bool) $important;
    }
}

/**
 * A generic CSS style class to use when a more specific class does not exist.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_generic extends css_style {

    /**
     * Cleans incoming values for typical things that can be optimised.
     *
     * @param mixed $value Cleans the provided value optimising it if possible
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
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_color extends css_style {

    /**
     * Creates a new colour style
     *
     * @param mixed $value Initialises a new colour style
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
     * @param mixed $value Cleans the provided value optimising it if possible
     * @return string
     */
    protected function clean_value($value) {
        $value = trim($value);
        if (css_is_colour($value)) {
            if (preg_match('/#([a-fA-F0-9]{6})/', $value, $matches)) {
                $value = '#'.strtoupper($matches[1]);
            } else if (preg_match('/#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/', $value, $matches)) {
                $value = $matches[1] . $matches[1] . $matches[2] . $matches[2] . $matches[3] . $matches[3];
                $value = '#'.strtoupper($value);
            } else if (array_key_exists(strtolower($value), css_optimiser::$htmlcolours)) {
                $value = css_optimiser::$htmlcolours[strtolower($value)];
            }
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
     * @param string $overridevalue If provided then this value will be used instead
     *     of the styles current value.
     * @return string
     */
    public function out($overridevalue = null) {
        if ($overridevalue === null) {
            $overridevalue = $this->value;
        }
        return parent::out(self::shrink_value($overridevalue));
    }

    /**
     * Shrinks the colour value is possible.
     *
     * @param string $value Shrinks the current value to an optimial form if possible
     * @return string
     */
    public static function shrink_value($value) {
        if (preg_match('/#([a-fA-F0-9])\1([a-fA-F0-9])\2([a-fA-F0-9])\3/', $value, $matches)) {
            return '#'.$matches[1].$matches[2].$matches[3];
        }
        return $value;
    }

    /**
     * Returns true if the value is a valid colour.
     *
     * @return bool
     */
    public function is_valid() {
        return css_is_colour($this->value);
    }
}

/**
 * A width style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_width extends css_style {

    /**
     * Checks if the width is valid
     * @return bool
     */
    public function is_valid() {
        return css_is_width($this->value);
    }

    /**
     * Cleans the provided value
     *
     * @param mixed $value Cleans the provided value optimising it if possible
     * @return string
     */
    protected function clean_value($value) {
        if (!css_is_width($value)) {
            // Note we don't actually change the value to something valid. That
            // would be bad for futureproofing.
            $this->set_error('Invalid width specified for '.$this->name);
        } else if (preg_match('#^0\D+$#', $value)) {
            $value = 0;
        }
        return trim($value);
    }

    /**
     * Initialises a new width style
     *
     * @param mixed $value The value this style has
     * @return css_style_width
     */
    public static function init($value) {
        return new css_style_width('width', $value);
    }
}

/**
 * A margin style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_margin extends css_style_width {

    /**
     * Initialises a margin style.
     *
     * In this case we split the margin into several other margin styles so that
     * we can properly condense overrides and then reconsolidate them later into
     * an optimal form.
     *
     * @param string $value The value the style has
     * @return array An array of margin values that can later be consolidated
     */
    public static function init($value) {
        $important = '';
        if (strpos($value, '!important') !== false) {
            $important = ' !important';
            $value = str_replace('!important', '', $value);
        }

        $value = preg_replace('#\s+#', ' ', trim($value));
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
            new css_style_margintop('margin-top', $top.$important),
            new css_style_marginright('margin-right', $right.$important),
            new css_style_marginbottom('margin-bottom', $bottom.$important),
            new css_style_marginleft('margin-left', $left.$important)
        );
    }

    /**
     * Consolidates individual margin styles into a single margin style
     *
     * @param array $styles
     * @return array An array of consolidated styles
     */
    public static function consolidate(array $styles) {
        if (count($styles) != 4) {
            return $styles;
        }

        $someimportant = false;
        $allimportant = null;
        $notimportantequal = null;
        $firstvalue = null;
        foreach ($styles as $style) {
            if ($style->is_important()) {
                $someimportant = true;
                if ($allimportant === null) {
                    $allimportant = true;
                }
            } else {
                if ($allimportant === true) {
                    $allimportant = false;
                }
                if ($firstvalue == null) {
                    $firstvalue = $style->get_value(false);
                    $notimportantequal = true;
                } else if ($notimportantequal && $firstvalue !== $style->get_value(false)) {
                    $notimportantequal = false;
                }
            }
        }

        if ($someimportant && !$allimportant && !$notimportantequal) {
            return $styles;
        }

        if ($someimportant && !$allimportant && $notimportantequal) {
            $return = array(
                new css_style_margin('margin', $firstvalue)
            );
            foreach ($styles as $style) {
                if ($style->is_important()) {
                    $return[] = $style;
                }
            }
            return $return;
        } else {
            $top = null;
            $right = null;
            $bottom = null;
            $left = null;
            foreach ($styles as $style) {
                switch ($style->get_name()) {
                    case 'margin-top' :
                        $top = $style->get_value(false);
                        break;
                    case 'margin-right' :
                        $right = $style->get_value(false);
                        break;
                    case 'margin-bottom' :
                        $bottom = $style->get_value(false);
                        break;
                    case 'margin-left' :
                        $left = $style->get_value(false);
                        break;
                }
            }
            if ($top == $bottom && $left == $right) {
                if ($top == $left) {
                    $returnstyle = new css_style_margin('margin', $top);
                } else {
                    $returnstyle = new css_style_margin('margin', "{$top} {$left}");
                }
            } else if ($left == $right) {
                $returnstyle = new css_style_margin('margin', "{$top} {$right} {$bottom}");
            } else {
                $returnstyle = new css_style_margin('margin', "{$top} {$right} {$bottom} {$left}");
            }
            if ($allimportant) {
                $returnstyle->set_important();
            }
            return array($returnstyle);
        }
    }
}

/**
 * A margin top style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_margintop extends css_style_margin {

    /**
     * A simple init, just a single style
     *
     * @param string $value The value the style has
     * @return css_style_margintop
     */
    public static function init($value) {
        return new css_style_margintop('margin-top', $value);
    }

    /**
     * This style can be consolidated into a single margin style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'margin';
    }
}

/**
 * A margin right style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_marginright extends css_style_margin {

    /**
     * A simple init, just a single style
     *
     * @param string $value The value the style has
     * @return css_style_margintop
     */
    public static function init($value) {
        return new css_style_marginright('margin-right', $value);
    }

    /**
     * This style can be consolidated into a single margin style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'margin';
    }
}

/**
 * A margin bottom style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_marginbottom extends css_style_margin {

    /**
     * A simple init, just a single style
     *
     * @param string $value The value the style has
     * @return css_style_margintop
     */
    public static function init($value) {
        return new css_style_marginbottom('margin-bottom', $value);
    }

    /**
     * This style can be consolidated into a single margin style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'margin';
    }
}

/**
 * A margin left style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_marginleft extends css_style_margin {

    /**
     * A simple init, just a single style
     *
     * @param string $value The value the style has
     * @return css_style_margintop
     */
    public static function init($value) {
        return new css_style_marginleft('margin-left', $value);
    }

    /**
     * This style can be consolidated into a single margin style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'margin';
    }
}

/**
 * A border style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_border extends css_style {

    /**
     * Initalises the border style into an array of individual style compontents
     *
     * @param string $value The value the style has
     * @return css_style_bordercolor
     */
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 3);

        $return = array();
        if (count($bits) > 0) {
            $width = array_shift($bits);
            if (!css_style_borderwidth::is_border_width($width)) {
                $width = '0';
            }
            $return[] = new css_style_borderwidth('border-top-width', $width);
            $return[] = new css_style_borderwidth('border-right-width', $width);
            $return[] = new css_style_borderwidth('border-bottom-width', $width);
            $return[] = new css_style_borderwidth('border-left-width', $width);
        }
        if (count($bits) > 0) {
            $style = array_shift($bits);
            $return[] = new css_style_borderstyle('border-top-style', $style);
            $return[] = new css_style_borderstyle('border-right-style', $style);
            $return[] = new css_style_borderstyle('border-bottom-style', $style);
            $return[] = new css_style_borderstyle('border-left-style', $style);
        }
        if (count($bits) > 0) {
            $colour = array_shift($bits);
            $return[] = new css_style_bordercolor('border-top-color', $colour);
            $return[] = new css_style_bordercolor('border-right-color', $colour);
            $return[] = new css_style_bordercolor('border-bottom-color', $colour);
            $return[] = new css_style_bordercolor('border-left-color', $colour);
        }
        return $return;
    }

    /**
     * Consolidates all border styles into a single style
     *
     * @param array $styles An array of border styles
     * @return array An optimised array of border styles
     */
    public static function consolidate(array $styles) {

        $borderwidths = array('top' => null, 'right' => null, 'bottom' => null, 'left' => null);
        $borderstyles = array('top' => null, 'right' => null, 'bottom' => null, 'left' => null);
        $bordercolors = array('top' => null, 'right' => null, 'bottom' => null, 'left' => null);

        foreach ($styles as $style) {
            switch ($style->get_name()) {
                case 'border-top-width':
                    $borderwidths['top'] = $style->get_value();
                    break;
                case 'border-right-width':
                    $borderwidths['right'] = $style->get_value();
                    break;
                case 'border-bottom-width':
                    $borderwidths['bottom'] = $style->get_value();
                    break;
                case 'border-left-width':
                    $borderwidths['left'] = $style->get_value();
                    break;

                case 'border-top-style':
                    $borderstyles['top'] = $style->get_value();
                    break;
                case 'border-right-style':
                    $borderstyles['right'] = $style->get_value();
                    break;
                case 'border-bottom-style':
                    $borderstyles['bottom'] = $style->get_value();
                    break;
                case 'border-left-style':
                    $borderstyles['left'] = $style->get_value();
                    break;

                case 'border-top-color':
                    $bordercolors['top'] = css_style_color::shrink_value($style->get_value());
                    break;
                case 'border-right-color':
                    $bordercolors['right'] = css_style_color::shrink_value($style->get_value());
                    break;
                case 'border-bottom-color':
                    $bordercolors['bottom'] = css_style_color::shrink_value($style->get_value());
                    break;
                case 'border-left-color':
                    $bordercolors['left'] = css_style_color::shrink_value($style->get_value());
                    break;
            }
        }

        $uniquewidths = count(array_unique($borderwidths));
        $uniquestyles = count(array_unique($borderstyles));
        $uniquecolors = count(array_unique($bordercolors));

        $nullwidths = in_array(null, $borderwidths, true);
        $nullstyles = in_array(null, $borderstyles, true);
        $nullcolors = in_array(null, $bordercolors, true);

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

    /**
     * Border styles get consolidated to a single border style.
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }

    /**
     * Consolidates a series of border styles into an optimised array of border
     * styles by looking at the direction of the border and prioritising that
     * during the optimisation.
     *
     * @param array $array An array to add styles into during consolidation. Passed by reference.
     * @param string $class The class type to initalise
     * @param string $style The style to create
     * @param string|array $top The top value
     * @param string $right The right value
     * @param string $bottom The bottom value
     * @param string $left The left value
     * @return bool
     */
    public static function consolidate_styles_by_direction(&$array, $class, $style, $top, $right = null, $bottom = null, $left = null) {
        if (is_array($top)) {
            $right = $top['right'];
            $bottom = $top['bottom'];
            $left = $top['left'];
            $top = $top['top'];
        }

        if ($top == $bottom && $left == $right && $top == $left) {
            if (is_null($top)) {
                $array[] = new $class($style, '');
            } else {
                $array[] =  new $class($style, $top);
            }
        } else if ($top == null || $right == null || $bottom == null || $left == null) {
            if ($top !== null) {
                $array[] = new $class(str_replace('border-', 'border-top-', $style), $top);
            }
            if ($right !== null) {
                $array[] = new $class(str_replace('border-', 'border-right-', $style), $right);
            }
            if ($bottom !== null) {
                $array[] = new $class(str_replace('border-', 'border-bottom-', $style), $bottom);
            }
            if ($left !== null) {
                $array[] = new $class(str_replace('border-', 'border-left-', $style), $left);
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

    /**
     * Builds a border style for a set of width, style, and colour values
     *
     * @param array $array An array into which the generated style is added
     * @param string $class The class type to initialise
     * @param string $cssstyle The style to use
     * @param string $width The width of the border
     * @param string $style The style of the border
     * @param string $color The colour of the border
     * @return bool
     */
    public static function build_style_string(&$array, $class, $cssstyle, $width = null, $style = null, $color = null) {
        if (!is_null($width) && !is_null($style) && !is_null($color)) {
            $array[] = new $class($cssstyle, $width.' '.$style.' '.$color);
        } else if (!is_null($width) && !is_null($style) && is_null($color)) {
            $array[] = new $class($cssstyle, $width.' '.$style);
        } else if (!is_null($width) && is_null($style) && is_null($color)) {
            $array[] = new $class($cssstyle, $width);
        } else {
            if (!is_null($width)) {
                $array[] = new $class($cssstyle, $width);
            }
            if (!is_null($style)) {
                $array[] = new $class($cssstyle, $style);
            }
            if (!is_null($color)) {
                $array[] = new $class($cssstyle, $color);
            }
        }
        return true;
    }
}

/**
 * A border colour style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_bordercolor extends css_style_color {

    /**
     * Creates a new border colour style
     *
     * Based upon the colour style
     *
     * @param mixed $value
     * @return Array of css_style_bordercolor
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
            css_style_bordertopcolor::init($top),
            css_style_borderrightcolor::init($right),
            css_style_borderbottomcolor::init($bottom),
            css_style_borderleftcolor::init($left)
        );
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }

    /**
     * Cleans the value
     *
     * @param string $value Cleans the provided value optimising it if possible
     * @return string
     */
    protected function clean_value($value) {
        $values = explode(' ', $value);
        $values = array_map('parent::clean_value', $values);
        return join (' ', $values);
    }

    /**
     * Outputs this style
     *
     * @param string $overridevalue
     * @return string
     */
    public function out($overridevalue = null) {
        if ($overridevalue === null) {
            $overridevalue = $this->value;
        }
        $values = explode(' ', $overridevalue);
        $values = array_map('css_style_color::shrink_value', $values);
        return parent::out(join (' ', $values));
    }
}

/**
 * A border left style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderleft extends css_style_generic {

    /**
     * Initialises the border left style into individual components
     *
     * @param string $value
     * @return array Array of css_style_borderleftwidth|css_style_borderleftstyle|css_style_borderleftcolor
     */
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 3);

        $return = array();
        if (count($bits) > 0) {
            $return[] = css_style_borderleftwidth::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_borderleftstyle::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_borderleftcolor::init(array_shift($bits));
        }
        return $return;
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border right style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderright extends css_style_generic {

    /**
     * Initialises the border right style into individual components
     *
     * @param string $value The value of the style
     * @return array Array of css_style_borderrightwidth|css_style_borderrightstyle|css_style_borderrightcolor
     */
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 3);

        $return = array();
        if (count($bits) > 0) {
            $return[] = css_style_borderrightwidth::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_borderrightstyle::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_borderrightcolor::init(array_shift($bits));
        }
        return $return;
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border top style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_bordertop extends css_style_generic {

    /**
     * Initialises the border top style into individual components
     *
     * @param string $value The value of the style
     * @return array Array of css_style_bordertopwidth|css_style_bordertopstyle|css_style_bordertopcolor
     */
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 3);

        $return = array();
        if (count($bits) > 0) {
            $return[] = css_style_bordertopwidth::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_bordertopstyle::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_bordertopcolor::init(array_shift($bits));
        }
        return $return;
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border bottom style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderbottom extends css_style_generic {

    /**
     * Initialises the border bottom style into individual components
     *
     * @param string $value The value of the style
     * @return array Array of css_style_borderbottomwidth|css_style_borderbottomstyle|css_style_borderbottomcolor
     */
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value, 3);

        $return = array();
        if (count($bits) > 0) {
            $return[] = css_style_borderbottomwidth::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_borderbottomstyle::init(array_shift($bits));
        }
        if (count($bits) > 0) {
            $return[] = css_style_borderbottomcolor::init(array_shift($bits));
        }
        return $return;
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border width style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderwidth extends css_style_width {

    /**
     * Creates a new border colour style
     *
     * Based upon the colour style
     *
     * @param string $value The value of the style
     * @return array Array of css_style_border*width
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
            css_style_bordertopwidth::init($top),
            css_style_borderrightwidth::init($right),
            css_style_borderbottomwidth::init($bottom),
            css_style_borderleftwidth::init($left)
        );
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }

    /**
     * Checks if the width is valid
     * @return bool
     */
    public function is_valid() {
        return self::is_border_width($this->value);
    }

    /**
     * Cleans the provided value
     *
     * @param mixed $value Cleans the provided value optimising it if possible
     * @return string
     */
    protected function clean_value($value) {
        $isvalid = self::is_border_width($value);
        if (!$isvalid) {
            $this->set_error('Invalid width specified for '.$this->name);
        } else if (preg_match('#^0\D+$#', $value)) {
            return '0';
        }
        return trim($value);
    }

    /**
     * Returns true if the provided value is a permitted border width
     * @param string $value The value to check
     * @return bool
     */
    public static function is_border_width($value) {
        $altwidthvalues = array('thin', 'medium', 'thick');
        return css_is_width($value) || in_array($value, $altwidthvalues);
    }
}

/**
 * A border style style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderstyle extends css_style_generic {

    /**
     * Creates a new border colour style
     *
     * Based upon the colour style
     *
     * @param string $value The value of the style
     * @return array Array of css_style_border*style
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
            css_style_bordertopstyle::init($top),
            css_style_borderrightstyle::init($right),
            css_style_borderbottomstyle::init($bottom),
            css_style_borderleftstyle::init($left)
        );
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border top colour style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_bordertopcolor extends css_style_bordercolor {

    /**
     * Initialises this style object
     *
     * @param string $value The value of the style
     * @return css_style_bordertopcolor
     */
    public static function init($value) {
        return new css_style_bordertopcolor('border-top-color', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border left colour style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderleftcolor extends css_style_bordercolor {

    /**
     * Initialises this style object
     *
     * @param string $value The value of the style
     * @return css_style_borderleftcolor
     */
    public static function init($value) {
        return new css_style_borderleftcolor('border-left-color', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border right colour style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderrightcolor extends css_style_bordercolor {

    /**
     * Initialises this style object
     *
     * @param string $value The value of the style
     * @return css_style_borderrightcolor
     */
    public static function init($value) {
        return new css_style_borderrightcolor('border-right-color', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border bottom colour style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderbottomcolor extends css_style_bordercolor {

    /**
     * Initialises this style object
     *
     * @param string $value The value of the style
     * @return css_style_borderbottomcolor
     */
    public static function init($value) {
        return new css_style_borderbottomcolor('border-bottom-color', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border width top style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_bordertopwidth extends css_style_borderwidth {

    /**
     * Initialises this style object
     *
     * @param string $value The value of the style
     * @return css_style_bordertopwidth
     */
    public static function init($value) {
        return new css_style_bordertopwidth('border-top-width', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border width left style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderleftwidth extends css_style_borderwidth {

    /**
     * Initialises this style object
     *
     * @param string $value The value of the style
     * @return css_style_borderleftwidth
     */
    public static function init($value) {
        return new css_style_borderleftwidth('border-left-width', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border width right style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderrightwidth extends css_style_borderwidth {

    /**
     * Initialises this style object
     *
     * @param string $value The value of the style
     * @return css_style_borderrightwidth
     */
    public static function init($value) {
        return new css_style_borderrightwidth('border-right-width', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border width bottom style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderbottomwidth extends css_style_borderwidth {

    /**
     * Initialises this style object
     *
     * @param string $value The value of the style
     * @return css_style_borderbottomwidth
     */
    public static function init($value) {
        return new css_style_borderbottomwidth('border-bottom-width', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border top style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_bordertopstyle extends css_style_borderstyle {

    /**
     * Initialises this style object
     *
     * @param string $value The value of the style
     * @return css_style_bordertopstyle
     */
    public static function init($value) {
        return new css_style_bordertopstyle('border-top-style', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border left style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderleftstyle extends css_style_borderstyle {

    /**
     * Initialises this style object
     *
     * @param string $value The value of the style
     * @return css_style_borderleftstyle
     */
    public static function init($value) {
        return new css_style_borderleftstyle('border-left-style', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border right style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderrightstyle extends css_style_borderstyle {

    /**
     * Initialises this style object
     *
     * @param string $value The value of the style
     * @return css_style_borderrightstyle
     */
    public static function init($value) {
        return new css_style_borderrightstyle('border-right-style', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A border bottom style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_borderbottomstyle extends css_style_borderstyle {

    /**
     * Initialises this style object
     *
     * @param string $value The value for the style
     * @return css_style_borderbottomstyle
     */
    public static function init($value) {
        return new css_style_borderbottomstyle('border-bottom-style', $value);
    }

    /**
     * Consolidate this to a single border style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'border';
    }
}

/**
 * A background style
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_background extends css_style {

    /**
     * Initialises a background style
     *
     * @param string $value The value of the style
     * @return array An array of background component.
     */
    public static function init($value) {
        // colour - image - repeat - attachment - position

        $imageurl = null;
        if (preg_match('#url\(([^\)]+)\)#', $value, $matches)) {
            $imageurl = trim($matches[1]);
            $value = str_replace($matches[1], '', $value);
        }

        // Switch out the brackets so that they don't get messed up when we explode
        $brackets = array();
        $bracketcount = 0;
        while (preg_match('#\([^\)\(]+\)#', $value, $matches)) {
            $key = "##BRACKET-{$bracketcount}##";
            $bracketcount++;
            $brackets[$key] = $matches[0];
            $value = str_replace($matches[0], $key, $value);
        }

        $important = (stripos($value, '!important') !== false);
        if ($important) {
            // Great some genius put !important in the background shorthand property
            $value = str_replace('!important', '', $value);
        }

        $value = preg_replace('#\s+#', ' ', $value);
        $bits = explode(' ', $value);

        foreach ($bits as $key => $bit) {
            $bits[$key] = self::replace_bracket_placeholders($bit, $brackets);
        }
        unset($bracketcount);
        unset($brackets);

        $repeats = array('repeat', 'repeat-x', 'repeat-y', 'no-repeat', 'inherit');
        $attachments = array('scroll' , 'fixed', 'inherit');
        $positions = array('top', 'left', 'bottom', 'right', 'center');

        $return = array();
        $unknownbits = array();

        $color = self::NULL_VALUE;
        if (count($bits) > 0 && css_is_colour(reset($bits))) {
            $color = array_shift($bits);
        }

        $image = self::NULL_VALUE;
        if (count($bits) > 0 && preg_match('#^\s*(none|inherit|url\(\))\s*$#', reset($bits))) {
            $image = array_shift($bits);
            if ($image == 'url()') {
                $image = "url({$imageurl})";
            }
        }

        $repeat = self::NULL_VALUE;
        if (count($bits) > 0 && in_array(reset($bits), $repeats)) {
            $repeat = array_shift($bits);
        }

        $attachment = self::NULL_VALUE;
        if (count($bits) > 0 && in_array(reset($bits), $attachments)) {
            // scroll , fixed, inherit
            $attachment = array_shift($bits);
        }

        $position = self::NULL_VALUE;
        if (count($bits) > 0) {
            $widthbits = array();
            foreach ($bits as $bit) {
                if (in_array($bit, $positions) || css_is_width($bit)) {
                    $widthbits[] = $bit;
                } else {
                    $unknownbits[] = $bit;
                }
            }
            if (count($widthbits)) {
                $position = join(' ', $widthbits);
            }
        }

        if (count($unknownbits)) {
            foreach ($unknownbits as $bit) {
                $bit = trim($bit);
                if ($color === self::NULL_VALUE && css_is_colour($bit)) {
                    $color = $bit;
                } else if ($repeat === self::NULL_VALUE && in_array($bit, $repeats)) {
                    $repeat = $bit;
                } else if ($attachment === self::NULL_VALUE && in_array($bit, $attachments)) {
                    $attachment = $bit;
                } else if ($bit !== '') {
                    $advanced = css_style_background_advanced::init($bit);
                    if ($important) {
                        $advanced->set_important();
                    }
                    $return[] = $advanced;
                }
            }
        }

        if ($color === self::NULL_VALUE && $image === self::NULL_VALUE && $repeat === self::NULL_VALUE && $attachment === self::NULL_VALUE && $position === self::NULL_VALUE) {
            // All primaries are null, return without doing anything else. There may be advanced madness there.
            return $return;
        }

        $return[] = new css_style_backgroundcolor('background-color', $color);
        $return[] = new css_style_backgroundimage('background-image', $image);
        $return[] = new css_style_backgroundrepeat('background-repeat', $repeat);
        $return[] = new css_style_backgroundattachment('background-attachment', $attachment);
        $return[] = new css_style_backgroundposition('background-position', $position);

        if ($important) {
            foreach ($return as $style) {
                $style->set_important();
            }
        }

        return $return;
    }

    /**
     * Static helper method to switch in bracket replacements
     *
     * @param string $value
     * @param array $placeholders
     * @return string
     */
    protected static function replace_bracket_placeholders($value, array $placeholders) {
        while (preg_match('/##BRACKET-\d+##/', $value, $matches)) {
            $value = str_replace($matches[0], $placeholders[$matches[0]], $value);
        }
        return $value;
    }

    /**
     * Consolidates background styles into a single background style
     *
     * @param array $styles Consolidates the provided array of background styles
     * @return array Consolidated optimised background styles
     */
    public static function consolidate(array $styles) {

        if (empty($styles)) {
            return $styles;
        }

        $color = null;
        $image = null;
        $repeat = null;
        $attachment = null;
        $position = null;
        $size = null;
        $origin = null;
        $clip = null;

        $someimportant = false;
        $allimportant = null;
        foreach ($styles as $style) {
            if ($style instanceof css_style_backgroundimage_advanced) {
                continue;
            }
            if ($style->is_important()) {
                $someimportant = true;
                if ($allimportant === null) {
                    $allimportant = true;
                }
            } else if ($allimportant === true) {
                $allimportant = false;
            }
        }

        $organisedstyles = array();
        $advancedstyles = array();
        $importantstyles = array();
        foreach ($styles as $style) {
            if ($style instanceof css_style_backgroundimage_advanced) {
                $advancedstyles[] = $style;
                continue;
            }
            if ($someimportant && !$allimportant && $style->is_important()) {
                $importantstyles[] = $style;
                continue;
            }
            $organisedstyles[$style->get_name()] = $style;
            switch ($style->get_name()) {
                case 'background-color' :
                    $color = css_style_color::shrink_value($style->get_value(false));
                    break;
                case 'background-image' :
                    $image = $style->get_value(false);
                    break;
                case 'background-repeat' :
                    $repeat = $style->get_value(false);
                    break;
                case 'background-attachment' :
                    $attachment = $style->get_value(false);
                    break;
                case 'background-position' :
                    $position = $style->get_value(false);
                    break;
                case 'background-clip' :
                    $clip = $style->get_value();
                    break;
                case 'background-origin' :
                    $origin = $style->get_value();
                    break;
                case 'background-size' :
                    $size = $style->get_value();
                    break;
            }
        }

        $consolidatetosingle = array();
        if (!is_null($color) && !is_null($image) && !is_null($repeat) && !is_null($attachment) && !is_null($position)) {
            // We can use the shorthand background-style!
            if (!$organisedstyles['background-color']->is_special_empty_value()) {
                $consolidatetosingle[] = $color;
            }
            if (!$organisedstyles['background-image']->is_special_empty_value()) {
                $consolidatetosingle[] = $image;
            }
            if (!$organisedstyles['background-repeat']->is_special_empty_value()) {
                $consolidatetosingle[] = $repeat;
            }
            if (!$organisedstyles['background-attachment']->is_special_empty_value()) {
                $consolidatetosingle[] = $attachment;
            }
            if (!$organisedstyles['background-position']->is_special_empty_value()) {
                $consolidatetosingle[] = $position;
            }
            // Reset them all to null so we don't use them again.
            $color = null;
            $image = null;
            $repeat = null;
            $attachment = null;
            $position = null;
        }

        $return = array();
        // Single background style needs to come first;
        if (count($consolidatetosingle) > 0) {
            $returnstyle = new css_style_background('background', join(' ', $consolidatetosingle));
            if ($allimportant) {
                $returnstyle->set_important();
            }
            $return[] = $returnstyle;
        }
        foreach ($styles as $style) {
            $value = null;
            switch ($style->get_name()) {
                case 'background-color'      : $value = $color;      break;
                case 'background-image'      : $value = $image;      break;
                case 'background-repeat'     : $value = $repeat;     break;
                case 'background-attachment' : $value = $attachment; break;
                case 'background-position'   : $value = $position;   break;
                case 'background-clip'       : $value = $clip;       break;
                case 'background-origin'     : $value = $origin;     break;
                case 'background-size'       : $value = $size;       break;
            }
            if (!is_null($value)) {
                $return[] = $style;
            }
        }
        $return = array_merge($return, $importantstyles, $advancedstyles);
        return $return;
    }
}

/**
 * A advanced background style that allows multiple values to preserve unknown entities
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_background_advanced extends css_style_generic {
    /**
     * Creates a new background colour style
     *
     * @param string $value The value of the style
     * @return css_style_backgroundimage
     */
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        return new css_style_background_advanced('background', $value);
    }

    /**
     * Returns true because the advanced background image supports multiple values.
     * e.g. -webkit-linear-gradient and -moz-linear-gradient.
     *
     * @return boolean
     */
    public function allows_multiple_values() {
        return true;
    }
}

/**
 * A background colour style.
 *
 * Based upon the colour style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundcolor extends css_style_color {

    /**
     * Creates a new background colour style
     *
     * @param string $value The value of the style
     * @return css_style_backgroundcolor
     */
    public static function init($value) {
        return new css_style_backgroundcolor('background-color', $value);
    }

    /**
     * css_style_backgroundcolor consolidates to css_style_background
     *
     * @return string
     */
    public function consolidate_to() {
        return 'background';
    }

    /**
     * Returns true if the value for this style is the special null value.
     *
     * This occurs if the shorthand background property was used but no proper value
     * was specified for this style.
     * This leads to a null value being used unless otherwise overridden.
     *
     * @return bool
     */
    public function is_special_empty_value() {
        return ($this->value === self::NULL_VALUE);
    }

    /**
     * Returns true if the value for this style is valid
     * @return bool
     */
    public function is_valid() {
        return $this->is_special_empty_value() || parent::is_valid();
    }
}

/**
 * A background image style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundimage extends css_style_generic {

    /**
     * Creates a new background image style
     *
     * @param string $value The value of the style
     * @return css_style_backgroundimage
     */
    public static function init($value) {
        if (!preg_match('#^\s*(none|inherit|url\()#i', $value)) {
            return css_style_backgroundimage_advanced::init($value);
        }
        return new css_style_backgroundimage('background-image', $value);
    }

    /**
     * Consolidates this style into a single background style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'background';
    }

    /**
     * Returns true if the value for this style is the special null value.
     *
     * This occurs if the shorthand background property was used but no proper value
     * was specified for this style.
     * This leads to a null value being used unless otherwise overridden.
     *
     * @return bool
     */
    public function is_special_empty_value() {
        return ($this->value === self::NULL_VALUE);
    }

    /**
     * Returns true if the value for this style is valid
     * @return bool
     */
    public function is_valid() {
        return $this->is_special_empty_value() || parent::is_valid();
    }
}

/**
 * A background image style that supports mulitple values and masquerades as a background-image
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundimage_advanced extends css_style_generic {
    /**
     * Creates a new background colour style
     *
     * @param string $value The value of the style
     * @return css_style_backgroundimage
     */
    public static function init($value) {
        $value = preg_replace('#\s+#', ' ', $value);
        return new css_style_backgroundimage_advanced('background-image', $value);
    }

    /**
     * Returns true because the advanced background image supports multiple values.
     * e.g. -webkit-linear-gradient and -moz-linear-gradient.
     *
     * @return boolean
     */
    public function allows_multiple_values() {
        return true;
    }
}

/**
 * A background repeat style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundrepeat extends css_style_generic {

    /**
     * Creates a new background colour style
     *
     * @param string $value The value of the style
     * @return css_style_backgroundrepeat
     */
    public static function init($value) {
        return new css_style_backgroundrepeat('background-repeat', $value);
    }

    /**
     * Consolidates this style into a single background style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'background';
    }

    /**
     * Returns true if the value for this style is the special null value.
     *
     * This occurs if the shorthand background property was used but no proper value
     * was specified for this style.
     * This leads to a null value being used unless otherwise overridden.
     *
     * @return bool
     */
    public function is_special_empty_value() {
        return ($this->value === self::NULL_VALUE);
    }

    /**
     * Returns true if the value for this style is valid
     * @return bool
     */
    public function is_valid() {
        return $this->is_special_empty_value() || parent::is_valid();
    }
}

/**
 * A background attachment style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundattachment extends css_style_generic {

    /**
     * Creates a new background colour style
     *
     * @param string $value The value of the style
     * @return css_style_backgroundattachment
     */
    public static function init($value) {
        return new css_style_backgroundattachment('background-attachment', $value);
    }

    /**
     * Consolidates this style into a single background style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'background';
    }

    /**
     * Returns true if the value for this style is the special null value.
     *
     * This occurs if the shorthand background property was used but no proper value
     * was specified for this style.
     * This leads to a null value being used unless otherwise overridden.
     *
     * @return bool
     */
    public function is_special_empty_value() {
        return ($this->value === self::NULL_VALUE);
    }

    /**
     * Returns true if the value for this style is valid
     * @return bool
     */
    public function is_valid() {
        return $this->is_special_empty_value() || parent::is_valid();
    }
}

/**
 * A background position style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundposition extends css_style_generic {

    /**
     * Creates a new background colour style
     *
     * @param string $value The value of the style
     * @return css_style_backgroundposition
     */
    public static function init($value) {
        return new css_style_backgroundposition('background-position', $value);
    }

    /**
     * Consolidates this style into a single background style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'background';
    }

    /**
     * Returns true if the value for this style is the special null value.
     *
     * This occurs if the shorthand background property was used but no proper value
     * was specified for this style.
     * This leads to a null value being used unless otherwise overridden.
     *
     * @return bool
     */
    public function is_special_empty_value() {
        return ($this->value === self::NULL_VALUE);
    }

    /**
     * Returns true if the value for this style is valid
     * @return bool
     */
    public function is_valid() {
        return $this->is_special_empty_value() || parent::is_valid();
    }
}

/**
 * A background size style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundsize extends css_style_generic {

    /**
     * Creates a new background size style
     *
     * @param string $value The value of the style
     * @return css_style_backgroundposition
     */
    public static function init($value) {
        return new css_style_backgroundsize('background-size', $value);
    }

    /**
     * Consolidates this style into a single background style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'background';
    }
}

/**
 * A background clip style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundclip extends css_style_generic {

    /**
     * Creates a new background clip style
     *
     * @param string $value The value of the style
     * @return css_style_backgroundposition
     */
    public static function init($value) {
        return new css_style_backgroundclip('background-clip', $value);
    }

    /**
     * Consolidates this style into a single background style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'background';
    }
}

/**
 * A background origin style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_backgroundorigin extends css_style_generic {

    /**
     * Creates a new background origin style
     *
     * @param string $value The value of the style
     * @return css_style_backgroundposition
     */
    public static function init($value) {
        return new css_style_backgroundorigin('background-origin', $value);
    }

    /**
     * Consolidates this style into a single background style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'background';
    }
}

/**
 * A padding style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_padding extends css_style_width {

    /**
     * Initialises this padding style into several individual padding styles
     *
     * @param string $value The value fo the style
     * @return array An array of padding styles
     */
    public static function init($value) {
        $important = '';
        if (strpos($value, '!important') !== false) {
            $important = ' !important';
            $value = str_replace('!important', '', $value);
        }

        $value = preg_replace('#\s+#', ' ', trim($value));
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
            new css_style_paddingtop('padding-top', $top.$important),
            new css_style_paddingright('padding-right', $right.$important),
            new css_style_paddingbottom('padding-bottom', $bottom.$important),
            new css_style_paddingleft('padding-left', $left.$important)
        );
    }

    /**
     * Consolidates several padding styles into a single style.
     *
     * @param array $styles Array of padding styles
     * @return array Optimised+consolidated array of padding styles
     */
    public static function consolidate(array $styles) {
        if (count($styles) != 4) {
            return $styles;
        }

        $someimportant = false;
        $allimportant = null;
        $notimportantequal = null;
        $firstvalue = null;
        foreach ($styles as $style) {
            if ($style->is_important()) {
                $someimportant = true;
                if ($allimportant === null) {
                    $allimportant = true;
                }
            } else {
                if ($allimportant === true) {
                    $allimportant = false;
                }
                if ($firstvalue == null) {
                    $firstvalue = $style->get_value(false);
                    $notimportantequal = true;
                } else if ($notimportantequal && $firstvalue !== $style->get_value(false)) {
                    $notimportantequal = false;
                }
            }
        }

        if ($someimportant && !$allimportant && !$notimportantequal) {
            return $styles;
        }

        if ($someimportant && !$allimportant && $notimportantequal) {
            $return = array(
                new css_style_padding('padding', $firstvalue)
            );
            foreach ($styles as $style) {
                if ($style->is_important()) {
                    $return[] = $style;
                }
            }
            return $return;
        } else {
            $top = null;
            $right = null;
            $bottom = null;
            $left = null;
            foreach ($styles as $style) {
                switch ($style->get_name()) {
                    case 'padding-top' : $top = $style->get_value(false);break;
                    case 'padding-right' : $right = $style->get_value(false);break;
                    case 'padding-bottom' : $bottom = $style->get_value(false);break;
                    case 'padding-left' : $left = $style->get_value(false);break;
                }
            }
            if ($top == $bottom && $left == $right) {
                if ($top == $left) {
                    $returnstyle = new css_style_padding('padding', $top);
                } else {
                    $returnstyle = new css_style_padding('padding', "{$top} {$left}");
                }
            } else if ($left == $right) {
                $returnstyle = new css_style_padding('padding', "{$top} {$right} {$bottom}");
            } else {
                $returnstyle = new css_style_padding('padding', "{$top} {$right} {$bottom} {$left}");
            }
            if ($allimportant) {
                $returnstyle->set_important();
            }
            return array($returnstyle);
        }
    }
}

/**
 * A padding top style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_paddingtop extends css_style_padding {

    /**
     * Initialises this style
     *
     * @param string $value The value of the style
     * @return css_style_paddingtop
     */
    public static function init($value) {
        return new css_style_paddingtop('padding-top', $value);
    }

    /**
     * Consolidates this style into a single padding style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'padding';
    }
}

/**
 * A padding right style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_paddingright extends css_style_padding {

    /**
     * Initialises this style
     *
     * @param string $value The value of the style
     * @return css_style_paddingright
     */
    public static function init($value) {
        return new css_style_paddingright('padding-right', $value);
    }

    /**
     * Consolidates this style into a single padding style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'padding';
    }
}

/**
 * A padding bottom style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_paddingbottom extends css_style_padding {

    /**
     * Initialises this style
     *
     * @param string $value The value of the style
     * @return css_style_paddingbottom
     */
    public static function init($value) {
        return new css_style_paddingbottom('padding-bottom', $value);
    }

    /**
     * Consolidates this style into a single padding style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'padding';
    }
}

/**
 * A padding left style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_paddingleft extends css_style_padding {

    /**
     * Initialises this style
     *
     * @param string $value The value of the style
     * @return css_style_paddingleft
     */
    public static function init($value) {
        return new css_style_paddingleft('padding-left', $value);
    }

    /**
     * Consolidates this style into a single padding style
     *
     * @return string
     */
    public function consolidate_to() {
        return 'padding';
    }
}

/**
 * A cursor style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_cursor extends css_style_generic {
    /**
     * Initialises a new cursor style
     * @param string $value
     * @return css_style_cursor
     */
    public static function init($value) {
        return new css_style_cursor('cursor', $value);
    }
    /**
     * Cleans the given value and returns it.
     *
     * @param string $value
     * @return string
     */
    protected function clean_value($value) {
        // Allowed values for the cursor style
        $allowed = array('auto', 'crosshair', 'default', 'e-resize', 'help', 'move', 'n-resize', 'ne-resize', 'nw-resize',
                         'pointer', 'progress', 's-resize', 'se-resize', 'sw-resize', 'text', 'w-resize', 'wait', 'inherit');
        // Has to be one of the allowed values of an image to use. Loosely match the image... doesn't need to be thorough
        if (!in_array($value, $allowed) && !preg_match('#\.[a-zA-Z0-9_\-]{1,5}$#', $value)) {
            $this->set_error('Invalid or unexpected cursor value specified: '.$value);
        }
        return trim($value);
    }
}

/**
 * A vertical alignment style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_verticalalign extends css_style_generic {
    /**
     * Initialises a new vertical alignment style
     * @param string $value
     * @return css_style_verticalalign
     */
    public static function init($value) {
        return new css_style_verticalalign('vertical-align', $value);
    }
    /**
     * Cleans the given value and returns it.
     *
     * @param string $value
     * @return string
     */
    protected function clean_value($value) {
        $allowed = array('baseline', 'sub', 'super', 'top', 'text-top', 'middle', 'bottom', 'text-bottom', 'inherit');
        if (!css_is_width($value) && !in_array($value, $allowed)) {
            $this->set_error('Invalid vertical-align value specified: '.$value);
        }
        return trim($value);
    }
}

/**
 * A float style.
 *
 * @package core
 * @category css
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_style_float extends css_style_generic {
    /**
     * Initialises a new float style
     * @param string $value
     * @return css_style_float
     */
    public static function init($value) {
        return new css_style_float('float', $value);
    }
    /**
     * Cleans the given value and returns it.
     *
     * @param string $value
     * @return string
     */
    protected function clean_value($value) {
        $allowed = array('left', 'right', 'none', 'inherit');
        if (!css_is_width($value) && !in_array($value, $allowed)) {
            $this->set_error('Invalid float value specified: '.$value);
        }
        return trim($value);
    }
}
