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
 * This file contains CSS file serving functions.
 *
 * NOTE: these functions are not expected to be used from any addons.
 *
 * @package core
 * @copyright 2012 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if (!defined('THEME_DESIGNER_CACHE_LIFETIME')) {
    // This can be also set in config.php file,
    // it needs to be higher than the time it takes to generate all CSS content.
    define('THEME_DESIGNER_CACHE_LIFETIME', 10);
}

/**
 * Stores CSS in a file at the given path.
 *
 * This function either succeeds or throws an exception.
 *
 * @param theme_config $theme The theme that the CSS belongs to.
 * @param string $csspath The path to store the CSS at.
 * @param string $csscontent the complete CSS in one string
 * @param bool $chunk If set to true these files will be chunked to ensure
 *      that no one file contains more than 4095 selectors.
 * @param string $chunkurl If the CSS is be chunked then we need to know the URL
 *      to use for the chunked files.
 */
function css_store_css(theme_config $theme, $csspath, $csscontent, $chunk = false, $chunkurl = null) {
    global $CFG;

    clearstatcache();
    if (!file_exists(dirname($csspath))) {
        @mkdir(dirname($csspath), $CFG->directorypermissions, true);
    }

    // Prevent serving of incomplete file from concurrent request,
    // the rename() should be more atomic than fwrite().
    ignore_user_abort(true);

    // First up write out the single file for all those using decent browsers.
    css_write_file($csspath, $csscontent);

    if ($chunk) {
        // If we need to chunk the CSS for browsers that are sub-par.
        $css = css_chunk_by_selector_count($csscontent, $chunkurl);
        $files = count($css);
        $count = 1;
        foreach ($css as $content) {
            if ($count === $files) {
                // If there is more than one file and this IS the last file.
                $filename = preg_replace('#\.css$#', '.0.css', $csspath);
            } else {
                // If there is more than one file and this is not the last file.
                $filename = preg_replace('#\.css$#', '.'.$count.'.css', $csspath);
            }
            $count++;
            css_write_file($filename, $content);
        }
    }

    ignore_user_abort(false);
    if (connection_aborted()) {
        die;
    }
}

/**
 * Writes a CSS file.
 *
 * @param string $filename
 * @param string $content
 */
function css_write_file($filename, $content) {
    global $CFG;
    if ($fp = fopen($filename.'.tmp', 'xb')) {
        fwrite($fp, $content);
        fclose($fp);
        rename($filename.'.tmp', $filename);
        @chmod($filename, $CFG->filepermissions);
        @unlink($filename.'.tmp'); // Just in case anything fails.
    }
}

/**
 * Takes CSS and chunks it if the number of selectors within it exceeds $maxselectors.
 *
 * The chunking will not split a group of selectors, or a media query. That means that
 * if n > $maxselectors and there are n selectors grouped together,
 * they will not be chunked and you could end up with more selectors than desired.
 * The same applies for a media query that has more than n selectors.
 *
 * Also, as we do not split group of selectors or media queries, the chunking might
 * not be as optimal as it could be, having files with less selectors than it could
 * potentially contain.
 *
 * String functions used here are not compliant with unicode characters. But that is
 * not an issue as the syntax of CSS is using ASCII codes. Even if we have unicode
 * characters in comments, or in the property 'content: ""', it will behave correcly.
 *
 * Please note that this strips out the comments if chunking happens.
 *
 * @param string $css The CSS to chunk.
 * @param string $importurl The URL to use for import statements.
 * @param int $maxselectors The number of selectors to limit a chunk to.
 * @param int $buffer Not used any more.
 * @return array An array of CSS chunks.
 */
function css_chunk_by_selector_count($css, $importurl, $maxselectors = 4095, $buffer = 50) {

    // Check if we need to chunk this CSS file.
    $count = substr_count($css, ',') + substr_count($css, '{');
    if ($count < $maxselectors) {
        // The number of selectors is less then the max - we're fine.
        return array($css);
    }

    $chunks = array();                  // The final chunks.
    $offsets = array();                 // The indexes to chunk at.
    $offset = 0;                        // The current offset.
    $selectorcount = 0;                 // The number of selectors since the last split.
    $lastvalidoffset = 0;               // The last valid index to split at.
    $lastvalidoffsetselectorcount = 0;  // The number of selectors used at the time were could split.
    $inrule = 0;                        // The number of rules we are in, should not be greater than 1.
    $inmedia = false;                   // Whether or not we are in a media query.
    $mediacoming = false;               // Whether or not we are expeting a media query.
    $currentoffseterror = null;         // Not null when we have recorded an error for the current split.
    $offseterrors = array();            // The offsets where we found errors.

    // Remove the comments. Because it's easier, safer and probably a lot of other good reasons.
    $css = preg_replace('#/\*(.*?)\*/#s', '', $css);
    $strlen = strlen($css);

    // Walk through the CSS content character by character.
    for ($i = 1; $i <= $strlen; $i++) {
        $char = $css[$i - 1];
        $offset = $i;

        // Is that a media query that I see coming towards us?
        if ($char === '@') {
            if (!$inmedia && substr($css, $offset, 5) === 'media') {
                $mediacoming = true;
            }
        }

        // So we are entering a rule or a media query...
        if ($char === '{') {
            if ($mediacoming) {
                $inmedia = true;
                $mediacoming = false;
            } else {
                $inrule++;
                $selectorcount++;
            }
        }

        // Let's count the number of selectors, but only if we are not in a rule, or in
        // the definition of a media query, as they can contain commas too.
        if (!$mediacoming && !$inrule && $char === ',') {
            $selectorcount++;
        }

        // We reached the end of something.
        if ($char === '}') {
            // Oh, we are in a media query.
            if ($inmedia) {
                if (!$inrule) {
                    // This is the end of the media query.
                    $inmedia = false;
                } else {
                    // We were in a rule, in the media query.
                    $inrule--;
                }
            } else {
                $inrule--;
                // Handle stupid broken CSS where there are too many } brackets,
                // as this can cause it to break (with chunking) where it would
                // coincidentally have worked otherwise.
                if ($inrule < 0) {
                    $inrule = 0;
                }
            }

            // We are not in a media query, and there is no pending rule, it is safe to split here.
            if (!$inmedia && !$inrule) {
                $lastvalidoffset = $offset;
                $lastvalidoffsetselectorcount = $selectorcount;
            }
        }

        // Alright, this is splitting time...
        if ($selectorcount > $maxselectors) {
            if (!$lastvalidoffset) {
                // We must have reached more selectors into one set than we were allowed. That means that either
                // the chunk size value is too small, or that we have a gigantic group of selectors, or that a media
                // query contains more selectors than the chunk size. We have to ignore this because we do not
                // support split inside a group of selectors or media query.
                if ($currentoffseterror === null) {
                    $currentoffseterror = $offset;
                    $offseterrors[] = $currentoffseterror;
                }
            } else {
                // We identify the offset to split at and reset the number of selectors found from there.
                $offsets[] = $lastvalidoffset;
                $selectorcount = $selectorcount - $lastvalidoffsetselectorcount;
                $lastvalidoffset = 0;
                $currentoffseterror = null;
            }
        }
    }

    // Report offset errors.
    if (!empty($offseterrors)) {
        debugging('Could not find a safe place to split at offset(s): ' . implode(', ', $offseterrors) . '. Those were ignored.',
            DEBUG_DEVELOPER);
    }

    // Now that we have got the offets, we can chunk the CSS.
    $offsetcount = count($offsets);
    foreach ($offsets as $key => $index) {
        $start = 0;
        if ($key > 0) {
            $start = $offsets[$key - 1];
        }
        // From somewhere up to the offset.
        $chunks[] = substr($css, $start, $index - $start);
    }
    // Add the last chunk (if there is one), from the last offset to the end of the string.
    if (end($offsets) != $strlen) {
        $chunks[] = substr($css, end($offsets));
    }

    // The array $chunks now contains CSS split into perfect sized chunks.
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
    $parts = count($chunks);
    for ($i = 1; $i < $parts; $i++) {
        if ($slashargs) {
            $importcss .= "@import url({$importurl}/chunk{$i});\n";
        } else {
            $importcss .= "@import url({$importurl}&chunk={$i});\n";
        }
    }
    $importcss .= end($chunks);
    $chunks[key($chunks)] = $importcss;

    return $chunks;
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
    // 90 days only - based on Moodle point release cadence being every 3 months.
    $lifetime = 60 * 60 * 24 * 90;

    header('Etag: "'.$etag.'"');
    header('Content-Disposition: inline; filename="styles.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', filemtime($csspath)) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age='.$lifetime.', immutable');
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: '.filesize($csspath));
    }

    readfile($csspath);
    die;
}

/**
 * Sends a cached CSS content
 *
 * @param string $csscontent The actual CSS markup.
 * @param string $etag The revision to make sure we utilise any caches.
 */
function css_send_cached_css_content($csscontent, $etag) {
    // 90 days only - based on Moodle point release cadence being every 3 months.
    $lifetime = 60 * 60 * 24 * 90;

    header('Etag: "'.$etag.'"');
    header('Content-Disposition: inline; filename="styles.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age='.$lifetime.', immutable');
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: '.strlen($csscontent));
    }

    echo($csscontent);
    die;
}

/**
 * Sends CSS directly and disables all caching.
 * The Content-Length of the body is also included, but the script is not ended.
 *
 * @param string $css The CSS content to send
 */
function css_send_temporary_css($css) {
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('Content-Disposition: inline; filename="styles_debug.php"');
    header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    header('Content-Length: ' . strlen($css));

    echo $css;
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
    echo $css;
    die;
}

/**
 * Send file not modified headers
 *
 * @param int $lastmodified
 * @param string $etag
 */
function css_send_unmodified($lastmodified, $etag) {
    // 90 days only - based on Moodle point release cadence being every 3 months.
    $lifetime = 60 * 60 * 24 * 90;
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
