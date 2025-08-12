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
 * XML parsing function calles into class.
 *
 * Note: Used by xml element handler as callback.
 *
 * @param string $data the XML source to parse.
 * @param int $whitespace If set to 1 allows the parser to skip "space" characters in xml document. Default is 1
 * @param string $encoding Specify an OUTPUT encoding. If not specified, it defaults to UTF-8.
 * @param bool $reporterrors if set to true, then a {@link xml_format_exception}
 *      exception will be thrown if the XML is not well-formed. Otherwise errors are ignored.
 * @return array representation of the parsed XML.
 * @deprecated since Moodle 5.1 - please use {@see \core\xml_parser} instead
 */
#[\core\attribute\deprecated(
    \core\xml_parser::class,
    since: '5.1',
    mdl: 'MDL-86256',
)]
function xmlize($data, $whitespace = 1, $encoding = 'UTF-8', $reporterrors = false) {
    \core\deprecation::emit_deprecation(__FUNCTION__);
    $hxml = new \core\xml_parser();
    return $hxml->parse($data, $whitespace, $encoding, $reporterrors);
}
