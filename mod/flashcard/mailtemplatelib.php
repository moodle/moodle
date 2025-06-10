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
 * This library is a third-party proposal for standardizing mail
 * message constitution for third party modules. It is actually used
 * by all ethnoinformatique.fr module. It relies on mail and message content
 * templates that should reside in a mail/{$lang} directory within the
 * module space.
 *
 * @package extralibs
 * @category third-party libs
 * @author Valery Fremaux (France) (valery@valeisti.fr)
 * @date 2008/03/03
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */
defined('MOODLE_INTERNAL') || die();

/**
 * useful templating functions from an older project of mine, hacked for Moodle
 * @param template the template's file name from $CFG->sitedir
 * @param infomap a hash containing pairs of parm => data to replace in template
 * @return a fully resolved template where all data has been injected
 */
function flashcard_compile_mail_template($template, $infomap, $lang = '') {
    global $USER;

    if (empty($lang)) {
        $lang = $USER->lang;
    }
    $lang = substr($lang, 0, 2); // Be sure we are in moodle 2.

    $notification = implode('', flashcard_get_mail_template($template, $lang));
    foreach ($infomap as $akey => $avalue) {
        $notification = str_replace("<%%$akey%%>", $avalue, $notification);
    }
    return $notification;
}

/**
 * resolves and get the content of a Mail template, acoording to the user's current language.
 * @param virtual the virtual mail template name
 * @param lang if default language must be overriden
 * @return string the template's content or false if no template file is available
 */
function flashcard_get_mail_template($virtual, $lang = '') {

    return new lang_string($virtual.'_tpl', 'flashcard', '', $lang);

}
