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
 * Functions for generating the HTML that Moodle should output.
 *
 * Please see http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML
 * for an overview.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 * @category output
 */

/** General rendering target, usually normal browser page */
define('RENDERER_TARGET_GENERAL', 'general');

/** General rendering target, usually normal browser page, but with limited capacity to avoid API use */
define('RENDERER_TARGET_MAINTENANCE', 'maintenance');

/** Plain text rendering for CLI scripts and cron */
define('RENDERER_TARGET_CLI', 'cli');

/** Plain text rendering for Ajax scripts*/
define('RENDERER_TARGET_AJAX', 'ajax');

/** Plain text rendering intended for sending via email */
define('RENDERER_TARGET_TEXTEMAIL', 'textemail');

/** Rich text html rendering intended for sending via email */
define('RENDERER_TARGET_HTMLEMAIL', 'htmlemail');

/**
 * Returns current theme revision number.
 *
 * @return int
 */
function theme_get_revision() {
    global $CFG;

    if (empty($CFG->themedesignermode)) {
        if (empty($CFG->themerev)) {
            // This only happens during install. It doesn't matter what themerev we use as long as it's positive.
            return 1;
        } else {
            return $CFG->themerev;
        }
    } else {
        return -1;
    }
}

/**
 * Returns current theme sub revision number. This is the revision for
 * this theme exclusively, not the global theme revision.
 *
 * @param string $themename The non-frankenstyle name of the theme
 * @return int
 */
function theme_get_sub_revision_for_theme($themename) {
    global $CFG;

    if (empty($CFG->themedesignermode)) {
        $pluginname = "theme_{$themename}";
        $revision = during_initial_install() ? null : get_config($pluginname, 'themerev');

        if (empty($revision)) {
            // This only happens during install. It doesn't matter what themerev we use as long as it's positive.
            return 1;
        } else {
            return $revision;
        }
    } else {
        return -1;
    }
}

/**
 * Calculates and returns the next theme revision number.
 *
 * @return int
 */
function theme_get_next_revision() {
    global $CFG;

    $next = time();
    if (isset($CFG->themerev) && ($next <= $CFG->themerev) && (($CFG->themerev - $next) < 60 * 60)) {
        // This resolves problems when reset is requested repeatedly within 1s,
        // the < 1h condition prevents accidental switching to future dates
        // because we might not recover from it.
        $next = $CFG->themerev + 1;
    }

    return $next;
}

/**
 * Calculates and returns the next theme revision number.
 *
 * @param string $themename The non-frankenstyle name of the theme
 * @return int
 */
function theme_get_next_sub_revision_for_theme($themename) {
    global $CFG;

    $next = time();
    $current = theme_get_sub_revision_for_theme($themename);
    if ($next <= $current && $current - $next < 60 * 60) {
        // This resolves problems when reset is requested repeatedly within 1s,
        // the < 1h condition prevents accidental switching to future dates
        // because we might not recover from it.
        $next = $current + 1;
    }

    return $next;
}

/**
 * Sets the current theme revision number.
 *
 * @param int $revision The new theme revision number
 */
function theme_set_revision($revision) {
    set_config('themerev', $revision);
}

/**
 * Sets the current theme revision number for a specific theme.
 * This does not affect the global themerev value.
 *
 * @param string $themename The non-frankenstyle name of the theme
 * @param int    $revision  The new theme revision number
 */
function theme_set_sub_revision_for_theme($themename, $revision) {
    set_config('themerev', $revision, "theme_{$themename}");
}

/**
 * Get the path to a theme config.php file.
 *
 * @param string $themename The non-frankenstyle name of the theme to check
 */
function theme_get_config_file_path($themename) {
    global $CFG;

    if (file_exists("{$CFG->dirroot}/theme/{$themename}/config.php")) {
        return "{$CFG->dirroot}/theme/{$themename}/config.php";
    } else if (!empty($CFG->themedir) && file_exists("{$CFG->themedir}/{$themename}/config.php")) {
        return "{$CFG->themedir}/{$themename}/config.php";
    } else {
        return null;
    }
}

/**
 * Get the path to the local cached CSS file.
 *
 * @param string $themename      The non-frankenstyle theme name.
 * @param int    $globalrevision The global theme revision.
 * @param int    $themerevision  The theme specific revision.
 * @param string $direction      Either 'ltr' or 'rtl' (case sensitive).
 */
function theme_get_css_filename($themename, $globalrevision, $themerevision, $direction) {
    global $CFG;

    $path = "{$CFG->localcachedir}/theme/{$globalrevision}/{$themename}/css";
    $filename = $direction == 'rtl' ? "all-rtl_{$themerevision}" : "all_{$themerevision}";
    return "{$path}/{$filename}.css";
}

/**
 * Generates and saves the CSS files for the given theme configs.
 *
 * @param theme_config[] $themeconfigs An array of theme_config instances.
 * @param array          $directions   Must be a subset of ['rtl', 'ltr'].
 * @param bool           $cache        Should the generated files be stored in local cache.
 * @return array         The built theme content in a multi-dimensional array of name => direction => content
 */
function theme_build_css_for_themes(
    $themeconfigs = [],
    $directions = ['rtl', 'ltr'],
    $cache = true,
    $mtraceprogress = false
): array {
    global $CFG;

    if (empty($themeconfigs)) {
        return [];
    }

    require_once("{$CFG->libdir}/csslib.php");

    $themescss = [];
    $themerev = theme_get_revision();
    // Make sure the local cache directory exists.
    make_localcache_directory('theme');

    foreach ($themeconfigs as $themeconfig) {
        $themecss = [];
        $oldrevision = theme_get_sub_revision_for_theme($themeconfig->name);
        $newrevision = theme_get_next_sub_revision_for_theme($themeconfig->name);

        // First generate all the new css.
        foreach ($directions as $direction) {
            if ($mtraceprogress) {
                $timestart = microtime(true);
                mtrace('Building theme CSS for ' . $themeconfig->name . ' [' .
                        $direction . '] ...', '');
            }
            // Lock it on. Technically we should build all themes for SVG and no SVG - but ie9 is out of support.
            $themeconfig->force_svg_use(true);
            $themeconfig->set_rtl_mode(($direction === 'rtl'));

            $themecss[$direction] = $themeconfig->get_css_content();
            if ($cache) {
                $themeconfig->set_css_content_cache($themecss[$direction]);
                $filename = theme_get_css_filename($themeconfig->name, $themerev, $newrevision, $direction);
                css_store_css($themeconfig, $filename, $themecss[$direction]);
            }
            if ($mtraceprogress) {
                mtrace(' done in ' . round(microtime(true) - $timestart, 2) . ' seconds.');
            }
        }
        $themescss[$themeconfig->name] = $themecss;

        if ($cache) {
            // Only update the theme revision after we've successfully created the
            // new CSS cache.
            theme_set_sub_revision_for_theme($themeconfig->name, $newrevision);

            // Now purge old files. We must purge all old files in the local cache
            // because we've incremented the theme sub revision. This will leave any
            // files with the old revision inaccessbile so we might as well removed
            // them from disk.
            foreach (['ltr', 'rtl'] as $direction) {
                $oldcss = theme_get_css_filename($themeconfig->name, $themerev, $oldrevision, $direction);
                if (file_exists($oldcss)) {
                    unlink($oldcss);
                }
            }
        }
    }

    return $themescss;
}

/**
 * Invalidate all server and client side caches.
 *
 * This method deletes the physical directory that is used to cache the theme
 * files used for serving.
 * Because it deletes the main theme cache directory all themes are reset by
 * this function.
 */
function theme_reset_all_caches() {
    global $CFG, $PAGE;
    require_once("{$CFG->libdir}/filelib.php");

    $next = theme_get_next_revision();
    theme_set_revision($next);

    if (!empty($CFG->themedesignermode)) {
        $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'core', 'themedesigner');
        $cache->purge();
    }

    // Purge compiled post processed css.
    cache::make('core', 'postprocessedcss')->purge();

    // Delete all old theme localcaches.
    $themecachedirs = glob("{$CFG->localcachedir}/theme/*", GLOB_ONLYDIR);
    foreach ($themecachedirs as $localcachedir) {
        fulldelete($localcachedir);
    }

    if ($PAGE) {
        $PAGE->reload_theme();
    }
}

/**
 * Reset static caches.
 *
 * This method indicates that all running cron processes should exit at the
 * next opportunity.
 */
function theme_reset_static_caches() {
    \core\task\manager::clear_static_caches();
}

/**
 * Enable or disable theme designer mode.
 *
 * @param bool $state
 */
function theme_set_designer_mod($state) {
    set_config('themedesignermode', (int)!empty($state));
    // Reset caches after switching mode so that any designer mode caches get purged too.
    theme_reset_all_caches();
}

/**
 * Purge theme used in context caches.
 */
function theme_purge_used_in_context_caches() {
    \cache::make('core', 'theme_usedincontext')->purge();
}

/**
 * Delete theme used in context cache for a particular theme.
 *
 * When switching themes, both old and new theme caches are deleted.
 * This gives the query the opportunity to recache accurate results for both themes.
 *
 * @param string $newtheme The incoming new theme.
 * @param string $oldtheme The theme that was already set.
 */
function theme_delete_used_in_context_cache(string $newtheme, string $oldtheme): void {
    if ((strlen($newtheme) > 0) && (strlen($oldtheme) > 0)) {
        // Theme -> theme.
        \cache::make('core', 'theme_usedincontext')->delete($oldtheme);
        \cache::make('core', 'theme_usedincontext')->delete($newtheme);
    } else {
        // No theme -> theme, or theme -> no theme.
        \cache::make('core', 'theme_usedincontext')->delete($newtheme . $oldtheme);
    }
}

/**
 * Invalidate all server and client side template caches.
 */
function template_reset_all_caches() {
    global $CFG;

    $next = time();
    if (isset($CFG->templaterev) && $next <= $CFG->templaterev && $CFG->templaterev - $next < 60 * 60) {
        // This resolves problems when reset is requested repeatedly within 1s,
        // the < 1h condition prevents accidental switching to future dates
        // because we might not recover from it.
        $next = $CFG->templaterev + 1;
    }

    set_config('templaterev', $next);
}

/**
 * Invalidate all server and client side JS caches.
 */
function js_reset_all_caches() {
    global $CFG;

    $next = time();
    if (isset($CFG->jsrev) && $next <= $CFG->jsrev && $CFG->jsrev - $next < 60 * 60) {
        // This resolves problems when reset is requested repeatedly within 1s,
        // the < 1h condition prevents accidental switching to future dates
        // because we might not recover from it.
        $next = $CFG->jsrev + 1;
    }

    set_config('jsrev', $next);
}
