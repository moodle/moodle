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

    /**
     * Given a simple string, this function returns the string
     * processed by enabled string filters if $CFG->filterall is enabled
     *
     * This function should be used to print short strings (non html) that
     * need filter processing e.g. activity titles, post subjects,
     * glossary concepts.
     *
     * @staticvar bool $strcache
     * @param string $string The string to be filtered. Should be plain text, expect
     * possibly for multilang tags.
     * @param boolean $striplinks To strip any link in the result text. Moodle 1.8 default changed from false to true! MDL-8713
     * @param array $options options array/object or courseid
     * @return string
     */
    public function format_string(
        $string,
        $striplinks = true,
        $options = null,
    ): string {
        global $CFG, $PAGE;

        if ($string === '' || is_null($string)) {
            // No need to do any filters and cleaning.
            return '';
        }

        // We'll use a in-memory cache here to speed up repeated strings.
        static $strcache = false;

        if (empty($CFG->version) or $CFG->version < 2013051400 or during_initial_install()) {
            // Do not filter anything during installation or before upgrade completes.
            return $string = strip_tags($string);
        }

        if ($strcache === false or count($strcache) > 2000) {
            // This number might need some tuning to limit memory usage in cron.
            $strcache = array();
        }

        if (is_numeric($options)) {
            // Legacy courseid usage.
            $options  = array('context' => context_course::instance($options));
        } else {
            // Detach object, we can not modify it.
            $options = (array)$options;
        }

        if (empty($options['context'])) {
            // Fallback to $PAGE->context this may be problematic in CLI and other non-standard pages :-(.
            $options['context'] = $PAGE->context;
        } else if (is_numeric($options['context'])) {
            $options['context'] = context::instance_by_id($options['context']);
        }
        if (!isset($options['filter'])) {
            $options['filter'] = true;
        }

        $options['escape'] = !isset($options['escape']) || $options['escape'];

        if (!$options['context']) {
            // We did not find any context? weird.
            return $string = strip_tags($string);
        }

        // Calculate md5.
        $cachekeys = array(
            $string, $striplinks, $options['context']->id,
            $options['escape'], current_language(), $options['filter']
        );
        $md5 = md5(implode('<+>', $cachekeys));

        // Fetch from cache if possible.
        if (isset($strcache[$md5])) {
            return $strcache[$md5];
        }

        // First replace all ampersands not followed by html entity code
        // Regular expression moved to its own method for easier unit testing.
        $string = $options['escape'] ? replace_ampersands_not_followed_by_entity($string) : $string;

        if (!empty($CFG->filterall) && $options['filter']) {
            $filtermanager = \filter_manager::instance();
            $filtermanager->setup_page_for_filters($PAGE, $options['context']); // Setup global stuff filters may have.
            $string = $filtermanager->filter_string($string, $options['context']);
        }

        // If the site requires it, strip ALL tags from this string.
        if (!empty($CFG->formatstringstriptags)) {
            if ($options['escape']) {
                $string = str_replace(array('<', '>'), array('&lt;', '&gt;'), strip_tags($string));
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
        $strcache[$md5] = $string;

        return $string;
    }


    /**
     * Given text in a variety of format codings, this function returns the text as safe HTML.
     *
     * This function should mainly be used for long strings like posts,
     * answers, glossary items etc. For short strings {@link format_string()}.
     *
     * <pre>
     * Options:
     *      trusted     :   If true the string won't be cleaned. Default false required noclean=true.
     *      noclean     :   If true the string won't be cleaned, unless $CFG->forceclean is set. Default false required trusted=true.
     *      nocache     :   If true the strign will not be cached and will be formatted every call. Default false.
     *      filter      :   If true the string will be run through applicable filters as well. Default true.
     *      para        :   If true then the returned string will be wrapped in div tags. Default true.
     *      newlines    :   If true then lines newline breaks will be converted to HTML newline breaks. Default true.
     *      context     :   The context that will be used for filtering.
     *      overflowdiv :   If set to true the formatted text will be encased in a div
     *                      with the class no-overflow before being returned. Default false.
     *      allowid     :   If true then id attributes will not be removed, even when
     *                      using htmlpurifier. Default false.
     *      blanktarget :   If true all <a> tags will have target="_blank" added unless target is explicitly specified.
     * </pre>
     *
     * @staticvar array $croncache
     * @param string $text The text to be formatted. This is raw text originally from user input.
     * @param int $format Identifier of the text format to be used
     *            [FORMAT_MOODLE, FORMAT_HTML, FORMAT_PLAIN, FORMAT_MARKDOWN]
     * @param stdClass|array $options text formatting options
     * @param int $courseiddonotuse deprecated course id, use context option instead
     * @return string
     */
    public function format_text(
        $text,
        $format = FORMAT_MOODLE,
        $options = null,
    ) {
        global $CFG, $DB, $PAGE;

        if ($text === '' || is_null($text)) {
            // No need to do any filters and cleaning.
            return '';
        }

        // Detach object, we can not modify it.
        $options = (array)$options;

        if (!isset($options['trusted'])) {
            $options['trusted'] = false;
        }
        if ($format == FORMAT_MARKDOWN) {
            // Markdown format cannot be trusted in trusttext areas,
            // because we do not know how to sanitise it before editing.
            $options['trusted'] = false;
        }
        if (!isset($options['noclean'])) {
            if ($options['trusted'] and trusttext_active()) {
                // No cleaning if text trusted and noclean not specified.
                $options['noclean'] = true;
            } else {
                $options['noclean'] = false;
            }
        }
        if (!empty($this->get_forceclean())) {
            // Whatever the caller claims, the admin wants all content cleaned anyway.
            $options['noclean'] = false;
        }
        if (!isset($options['nocache'])) {
            $options['nocache'] = false;
        }
        if (!isset($options['filter'])) {
            $options['filter'] = true;
        }
        if (!isset($options['para'])) {
            $options['para'] = true;
        }
        if (!isset($options['newlines'])) {
            $options['newlines'] = true;
        }
        if (!isset($options['overflowdiv'])) {
            $options['overflowdiv'] = false;
        }
        $options['blanktarget'] = !empty($options['blanktarget']);

        // Calculate best context.
        if (empty($CFG->version) or $CFG->version < 2013051400 or during_initial_install()) {
            // Do not filter anything during installation or before upgrade completes.
            $context = null;
        } else if (isset($options['context'])) { // First by explicit passed context option.
            if (is_object($options['context'])) {
                $context = $options['context'];
            } else {
                $context = context::instance_by_id($options['context']);
            }
        } else {
            // Fallback to $PAGE->context this may be problematic in CLI and other non-standard pages :-(.
            $context = $PAGE->context;
        }

        if (!$context) {
            // Either install/upgrade or something has gone really wrong because context does not exist (yet?).
            $options['nocache'] = true;
            $options['filter']  = false;
        }

        if ($options['filter']) {
            $filtermanager = \filter_manager::instance();
            $filtermanager->setup_page_for_filters($PAGE, $context); // Setup global stuff filters may have.
            $filteroptions = array(
                'originalformat' => $format,
                'noclean' => $options['noclean'],
            );
        } else {
            $filtermanager = new \null_filter_manager();
            $filteroptions = array();
        }

        switch ($format) {
            case FORMAT_HTML:
                $filteroptions['stage'] = 'pre_format';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                // Text is already in HTML format, so just continue to the next filtering stage.
                $filteroptions['stage'] = 'pre_clean';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                if (!$options['noclean']) {
                    $text = clean_text($text, FORMAT_HTML, $options);
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

            case FORMAT_WIKI:
                // This format is deprecated.
                $text = '<p>NOTICE: Wiki-like formatting has been removed from Moodle.  You should not be seeing
                     this message as all texts should have been converted to Markdown format instead.
                     Please post a bug report to http://moodle.org/bugs with information about where you
                     saw this message.</p>' . s($text);
                break;

            case FORMAT_MARKDOWN:
                $filteroptions['stage'] = 'pre_format';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                $text = markdown_to_html($text);
                $filteroptions['stage'] = 'pre_clean';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                if (!$options['noclean']) {
                    $text = clean_text($text, FORMAT_HTML, $options);
                }
                $filteroptions['stage'] = 'post_clean';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                break;

            default:  // FORMAT_MOODLE or anything else.
                $filteroptions['stage'] = 'pre_format';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                $text = text_to_html($text, null, $options['para'], $options['newlines']);
                $filteroptions['stage'] = 'pre_clean';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                if (!$options['noclean']) {
                    $text = clean_text($text, FORMAT_HTML, $options);
                }
                $filteroptions['stage'] = 'post_clean';
                $text = $filtermanager->filter_text($text, $context, $filteroptions);
                break;
        }
        if ($options['filter']) {
            // At this point there should not be any draftfile links any more,
            // this happens when developers forget to post process the text.
            // The only potential problem is that somebody might try to format
            // the text before storing into database which would be itself big bug..
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

        if (!empty($options['overflowdiv'])) {
            $text = \html_writer::tag('div', $text, array('class' => 'no-overflow'));
        }

        if ($options['blanktarget']) {
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
            $text = trim(preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $domdoc->saveHTML($domdoc->documentElement)));
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
}
