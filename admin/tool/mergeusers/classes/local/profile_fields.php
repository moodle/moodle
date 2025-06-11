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

namespace tool_mergeusers\local;

/**
 * Value for the "shotname"s of the custom profile fields used by this plugin.
 *
 * @package   tool_mergeusers
 * @author    Jordi Pujol Ahulló <jordi.pujol@urv.cat>
 * @copyright 2025 onwards to Universitat Rovira i Vrigili (https://www.urv.cat)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class profile_fields {
    /** @var string  Date where the merge was performed. */
    public const MERGE_DATE = 'mergeusers_date';
    /** @var string Log id of the merge. */
    public const MERGE_LOG_ID = 'mergeusers_logid';
    /** @var string User id that has removed all her data. */
    public const MERGE_OLD_USER_ID = 'mergeusers_olduserid';
    /** @var string User that keeps data from both merged users. */
    public const MERGE_NEW_USER_ID = 'mergeusers_newuserid';
    /** @var string[] List of custom profile shortnames */
    public const MERGE_FIELD_SHORTNAMES = [
        self::MERGE_DATE,
        self::MERGE_LOG_ID,
        self::MERGE_OLD_USER_ID,
        self::MERGE_NEW_USER_ID,
    ];
    /** @var string Name of the fields category for specific tool_mergeusers profile fields. */
    public const MERGE_CATEGORY_FOR_FIELDS = 'Merge users: Detail';
}
