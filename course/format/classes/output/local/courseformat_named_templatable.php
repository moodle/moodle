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

namespace core_courseformat\output\local;

/**
 * Base templatable class for coursformat templateables which are typically overridden by course formats.
 *
 * @package   core_courseformat
 * @copyright 2022 Andrew Lyons <andrew@nicols.co.uk>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait courseformat_named_templatable {

    /**
     * Get the name of the template to use for this templatable.
     *
     * @param \renderer_base $renderer The renderer requesting the template name
     * @return string
     */
    public function get_template_name(\renderer_base $renderer): string {
        $fullpath = str_replace('\\', '/', get_class($this));

        $specialrenderers = '@^.*/output/(local|courseformat)/(?<template>.+)$@';
        $matches = null;

        if (preg_match($specialrenderers, $fullpath, $matches)) {
            return "core_courseformat/local/{$matches['template']}";
        }

        throw new \coding_exception("Unable to determine template name for class " . get_class($this));
    }
}
