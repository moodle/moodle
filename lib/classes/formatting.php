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

namespace core;

/**
 * Content formatting methods for Moodle.
 *
 * @package   core
 * @copyright 2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class formatting {
    /** @var bool Whether to apply forceclean */
    protected ?bool $forceclean;

    /** @var bool Whether to apply striptags */
    protected ?bool $striptags;

    /** @var bool Whether to apply filters */
    protected ?bool $filterall;

    /** @var array A string cache for format_string */
    protected $formatstringcache = [];

    /**
     * Given a simple string, this function returns the string
     * processed by enabled string filters if $CFG->filterall is enabled
     *
     * This function should be used to print short strings (non html) that
     * need filter processing e.g. activity titles, post subjects,
     * glossary concepts.
     *
     * @param null|string $string The string to be filtered. Should be plain text, expect
     * possibly for multilang tags.
     * @param boolean $striplinks To strip any link in the result text.
     * @param null|context $context The context used for formatting
     * @param bool $filter Whether to apply filters
     * @param bool $escape Whether to escape ampersands
     * @return string
     */
    public function format_string(
        ?string $string,
        bool $striplinks = true,
        ?context $context = null,
        bool $filter = true,
        bool $escape = true,
    ): string {
        global $PAGE;

        if ($string === '' || is_null($string)) {
            // No need to do any filters and cleaning.
            return '';
        }

        if (!$this->should_filter_string()) {
            return strip_tags($string);
        }

        if (count($this->formatstringcache) > 2000) {
            // This number might need some tuning to limit memory usage in cron.
            $this->formatstringcache = [];
        }

        if ($context === null) {
            // Fallback to $PAGE->context this may be problematic in CLI and other non-standard pages :-(.
            // In future we may want to add debugging here.
            $context = $PAGE->context;
            if (!$context) {
                // We did not find any context? weird.
                throw new \coding_exception(
                    'Unable to identify context for format_string()',
                );
            }
        }

        // Calculate md5.
        $cachekeys = [
            $string,
            $striplinks,
            $context->id,
            $escape,
            current_language(),
            $filter,
        ];
        $md5 = md5(implode('<+>', $cachekeys));

        // Fetch from cache if possible.
        if (array_key_exists($md5, $this->formatstringcache)) {
            return $this->formatstringcache[$md5];
        }

        // First replace all ampersands not followed by html entity code
        // Regular expression moved to its own method for easier unit testing.
        if ($escape) {
            $string = replace_ampersands_not_followed_by_entity($string);
        }

        if (!empty($this->get_filterall()) && $filter) {
            $filtermanager = \filter_manager::instance();
            $filtermanager->setup_page_for_filters($PAGE, $context); // Setup global stuff filters may have.
            $string = $filtermanager->filter_string($string, $context);
        }

        // If the site requires it, strip ALL tags from this string.
        if ($this->get_striptags()) {
            if ($escape) {
                $string = str_replace(['<', '>'], ['&lt;', '&gt;'], strip_tags($string));
            } else {
                $string = strip_tags($string);
            }
        } else {
            // Otherwise strip just links if that is required (default).
            if ($striplinks) {
                // Strip links in string.
                $string = strip_links($string);
            }
            $string = clean_text($string);
        }

        // Store to cache.
        $this->formatstringcache[$md5] = $string;

        return $string;
    }

    /**
     * Given text in a variety of format codings, this function returns the text as safe HTML.
     *
     * This function should mainly be used for long strings like posts,
     * answers, glossary items etc. For short strings {@link format_string()}.
     *
     * @param null|string $text The text to be formatted. This is raw text originally from user input.
     * @param string $format Identifier of the text format to be used
     *              [FORMAT_MOODLE, FORMAT_HTML, FORMAT_PLAIN, FORMAT_MARKDOWN]
     * @param null|context $context The context used for filtering
     * @param bool $trusted If true the string won't be cleaned.
     *              Note: FORMAT_MARKDOWN does not support trusted text.
     * @param null|bool $clean If true the string will be cleaned.
     *              Note: This parameter is overridden if the text is trusted
     * @param bool $filter If true the string will be run through applicable filters as well.
     * @param bool $para If true then the returned string will be wrapped in div tags.
     * @param bool $newlines If true then lines newline breaks will be converted to HTML newline breaks.
     * @param bool $overflowdiv If set to true the formatted text will be encased in a div
     * @param bool $blanktarget If true all <a> tags will have target="_blank" added unless target is explicitly specified.
     * @param bool $allowid If true then id attributes will not be removed, even when using htmlpurifier.
     * @return string
     */
    public function format_text(
        ?string $text,
        string $format = FORMAT_MOODLE,
        ?context $context = null,
        bool $trusted = false,
        ?bool $clean = null,
        bool $filter = true,
        bool $para = true,
        bool $newlines = true,
        bool $overflowdiv = false,
        bool $blanktarget = false,
        bool $allowid = false,
    ): string {
        global $CFG, $PAGE;

        if ($text === '' || is_null($text)) {
            // No need to do any filters and cleaning.
            return '';
        }

        if ($format == FORMAT_MARKDOWN) {
            // Markdown format cannot be trusted in trusttext areas,
            // because we do not know how to sanitise it before editing.
            $trusted = false;
        }
        if ($clean === null) {
            if ($trusted && trusttext_active()) {
                // No cleaning if text trusted and clean not specified.
                $clean = false;
            } else {
                $clean = true;
            }
        }
        if (!empty($this->get_forceclean())) {
            // Whatever the caller claims, the admin wants all content cleaned anyway.
            $clean = true;
        }

        // Calculate best context.
        if (!$this->should_filter_string()) {
            // Do not filter anything during installation or before upgrade completes.
            $context = null;
        } else if ($context === null) {
            // Fallback to $PAGE->context this may be problematic in CLI and other non-standard pages.
            // In future we may want to add debugging here.
            $context = $PAGE->context;
        }

        if (!$context) {
            // Either install/upgrade or something has gone really wrong because context does not exist (yet?).
            $filter = false;
        }

        if ($filter) {
            $filtermanager = \filter_manager::instance();
            $filtermanager->setup_page_for_filters($PAGE, $context); // Setup global stuff filters may have.
            $filteroptions = [
                'originalformat' => $format,
                'noclean' => !$clean,
            ];
        } else {
            $filtermanager = new \null_filter_manager();
            $filteroptions = [];
        }

        switch ($format) {
            case FORMAT_HTML:
                $filteroptions['stage'] = 'pre_format';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                // Text is already in HTML format, so just continue to the next filtering stage.
                $filteroptions['stage'] = 'pre_clean';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                if ($clean) {
                    $text = clean_text($text, FORMAT_HTML, [
                        'allowid' => $allowid,
                    ]);
                }
                $filteroptions['stage'] = 'post_clean';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                break;

            case FORMAT_PLAIN:
                $text = s($text); // Cleans dangerous JS.
                $text = rebuildnolinktag($text);
                $text = str_replace('  ', '&nbsp; ', $text);
                $text = nl2br($text);
                break;

            case FORMAT_MARKDOWN:
                $filteroptions['stage'] = 'pre_format';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                $text = markdown_to_html($text);
                $filteroptions['stage'] = 'pre_clean';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                if ($clean) {
                    $text = clean_text($text, FORMAT_HTML, [
                        'allowid' => $allowid,
                    ]);
                }
                $filteroptions['stage'] = 'post_clean';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                break;

            case FORMAT_MOODLE:
                $filteroptions['stage'] = 'pre_format';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                $text = text_to_html($text, null, $para, $newlines);
                $filteroptions['stage'] = 'pre_clean';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                if ($clean) {
                    $text = clean_text($text, FORMAT_HTML, [
                        'allowid' => $allowid,
                    ]);
                }
                $filteroptions['stage'] = 'post_clean';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                break;
            default:  // FORMAT_MOODLE or anything else.
                throw new \coding_exception("Unknown format passed to format_text: {$format}");
        }

        if ($filter) {
            // At this point there should not be any draftfile links any more,
            // this happens when developers forget to post process the text.
            // The only potential problem is that somebody might try to format
            // the text before storing into database which would be itself big bug.
            $text = str_replace("\"$CFG->wwwroot/draftfile.php", "\"$CFG->wwwroot/brokenfile.php#", $text);

            if ($CFG->debugdeveloper) {
                if (strpos($text, '@@PLUGINFILE@@/') !== false) {
                    debugging(
                        'Before calling format_text(), the content must be processed with file_rewrite_pluginfile_urls()',
                        DEBUG_DEVELOPER
                    );
                }
            }
        }

        if (!empty($overflowdiv)) {
            $text = \html_writer::tag('div', $text, ['class' => 'no-overflow']);
        }

        if ($blanktarget) {
            $domdoc = new \DOMDocument();
            libxml_use_internal_errors(true);
            $domdoc->loadHTML('<?xml version="1.0" encoding="UTF-8" ?>' . $text);
            libxml_clear_errors();
            foreach ($domdoc->getElementsByTagName('a') as $link) {
                if ($link->hasAttribute('target') && strpos($link->getAttribute('target'), '_blank') === false) {
                    continue;
                }
                $link->setAttribute('target', '_blank');
                if (strpos($link->getAttribute('rel'), 'noreferrer') === false) {
                    $link->setAttribute('rel', trim($link->getAttribute('rel') . ' noreferrer'));
                }
            }

            // This regex is nasty and I don't like it. The correct way to solve this is by loading the HTML like so:
            // $domdoc->loadHTML($text, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD); however it seems like some libxml
            // versions don't work properly and end up leaving <html><body>, so I'm forced to use
            // this regex to remove those tags as a preventive measure.
            $text = trim(preg_replace(
                '~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i',
                '',
                $domdoc->saveHTML($domdoc->documentElement),
            ));
        }

        return $text;
    }

    /**
     * Set the value of the forceclean setting.
     *
     * @param bool $forceclean
     * @return self
     */
    public function set_forceclean(bool $forceclean): self {
        $this->forceclean = $forceclean;

        return $this;
    }

    /**
     * Get the current forceclean value.
     *
     * @return bool
     */
    public function get_forceclean(): bool {
        global $CFG;

        if (isset($this->forceclean)) {
            return $this->forceclean;
        }

        if (isset($CFG->forceclean)) {
            return $CFG->forceclean;
        }

        return false;
    }

    /**
     * Set the value of the striptags setting.
     *
     * @param bool $striptags
     * @return formatting
     */
    public function set_striptags(bool $striptags): self {
        $this->striptags = $striptags;

        return $this;
    }

    /**
     * Get the current striptags value.
     *
     * Reverts to CFG->formatstringstriptags if not set.
     *
     * @return bool
     */
    public function get_striptags(): bool {
        global $CFG;

        if (isset($this->striptags)) {
            return $this->striptags;
        }

        return !empty($CFG->formatstringstriptags);
    }

    /**
     * Set the value of the filterall setting.
     *
     * @param bool $filterall
     * @return formatting
     */
    public function set_filterall(bool $filterall): self {
        $this->filterall = $filterall;

        return $this;
    }

    /**
     * Get the current filterall value.
     *
     * Reverts to CFG->filterall if not set.
     *
     * @return bool
     */
    public function get_filterall(): bool {
        global $CFG;

        if (isset($this->filterall)) {
            return $this->filterall;
        }

        return $CFG->filterall;
    }

    /**
     * During initial install, or upgrade from a really old version of Moodle, we should not filter strings at all.
     *
     * @return bool
     */
    protected function should_filter_string(): bool {
        global $CFG;

        if (empty($CFG->version) || $CFG->version < 2013051400 || during_initial_install()) {
            // Do not filter anything during installation or before upgrade completes.
            return false;
        }

        return true;
    }
}
