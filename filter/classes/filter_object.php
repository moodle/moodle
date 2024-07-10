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

namespace core_filters;

/**
 * This is just a little object to define a phrase and some instructions
 * for how to process it.  Filters can create an array of these to pass
 * to the @{link filter_phrases()} function below.
 *
 * Note that although the fields here are public, you almost certainly should
 * never use that. All that is supported is contructing new instances of this
 * class, and then passing an array of them to filter_phrases.
 *
 * @package core_filters
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_object {
    /** @var string this is the phrase that should be matched. */
    public $phrase;

    /** @var bool whether to match complete words. If true, 'T' won't be matched in 'Tim'. */
    public $fullmatch;

    /** @var bool whether the match needs to be case sensitive. */
    public $casesensitive;

    /** @var string HTML to insert before any match. */
    public $hreftagbegin;
    /** @var string HTML to insert after any match. */
    public $hreftagend;

    /** @var null|string replacement text to go inside begin and end. If not set,
     * the body of the replacement will be the original phrase.
     */
    public $replacementphrase;

    /** @var null|string once initialised, holds the regexp for matching this phrase. */
    public $workregexp = null;

    /** @var null|string once initialised, holds the mangled HTML to replace the regexp with. */
    public $workreplacementphrase = null;

    /** @var null|callable hold a replacement function to be called. */
    public $replacementcallback;

    /** @var null|array data to be passed to $replacementcallback. */
    public $replacementcallbackdata;

    /**
     * Constructor.
     *
     * @param string $phrase this is the phrase that should be matched.
     * @param string $hreftagbegin HTML to insert before any match. Default '<span class="highlight">'.
     * @param string $hreftagend HTML to insert after any match. Default '</span>'.
     * @param bool $casesensitive whether the match needs to be case sensitive
     * @param bool $fullmatch whether to match complete words. If true, 'T' won't be matched in 'Tim'.
     * @param mixed $replacementphrase replacement text to go inside begin and end. If not set,
     * the body of the replacement will be the original phrase.
     * @param callback $replacementcallback if set, then this will be called just before
     * $hreftagbegin, $hreftagend and $replacementphrase are needed, so they can be computed only if required.
     * The call made is
     * list($linkobject->hreftagbegin, $linkobject->hreftagend, $linkobject->replacementphrase) =
     *         call_user_func_array($linkobject->replacementcallback, $linkobject->replacementcallbackdata);
     * so the return should be an array [$hreftagbegin, $hreftagend, $replacementphrase], the last of which may be null.
     * @param null|array $replacementcallbackdata data to be passed to $replacementcallback (optional).
     */
    public function __construct(
        $phrase,
        $hreftagbegin = '<span class="highlight">',
        $hreftagend = '</span>',
        $casesensitive = false,
        $fullmatch = false,
        $replacementphrase = null,
        $replacementcallback = null,
        ?array $replacementcallbackdata = null
    ) {

        $this->phrase                  = $phrase;
        $this->hreftagbegin            = $hreftagbegin;
        $this->hreftagend              = $hreftagend;
        $this->casesensitive           = !empty($casesensitive);
        $this->fullmatch               = !empty($fullmatch);
        $this->replacementphrase       = $replacementphrase;
        $this->replacementcallback     = $replacementcallback;
        $this->replacementcallbackdata = $replacementcallbackdata;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(filter_object::class, \filterobject::class);
