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
 * Configurable Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @date: 2009
 */

function export_report($report)
{
    $filename = $report->name ?? 'report';
    $table = $report->table;

    set_header($filename);

    echo "ID;P\n";

    if (!empty($table->head)) {
        $head = [];

        foreach ($table->head as $title) {
            $head[] = $title;
        }

        dump_slk_row($head);
    }

    foreach ($table->data as $row) {
        dump_slk_row($row);
    }

    echo "E\n";

    exit;
}

function set_header($filename)
{
    global $CFG;

    require_once("$CFG->libdir/moodlelib.php");

    $gmdate = gmdate("Ymd_Hi");
    $filename = clean_filename("$filename-$gmdate.slk");

    if (strpos($CFG->wwwroot, 'https://') === 0) { // HTTPS sites - watch out for IE! KB812935 and KB316431.
        header('Cache-Control: max-age=10');
        header('Pragma: ');
    } else { //normal http - prevent caching at all cost
        header('Cache-Control: private, must-revalidate, pre-check=0, post-check=0, max-age=0');
        header('Pragma: no-cache');
    }
    header('Expires: ' . gmdate('D, d M Y H:i:s', 0) . ' GMT');
    header("Content-Type: application/download; charset=iso-8859-1");
    header("Content-Disposition: attachment; filename=\"$filename\"");
}

$row = 1;

function dump_slk_row($data)
{
    // Refer to https://en.wikipedia.org/wiki/Symbolic_Link_(SYLK)

    global $row;
    $col = 1;

    foreach ($data as $datum) {
        $datum =
            htmlspecialchars_decode(
                strip_tags(
                    nl2br(
                        str_replace('"', "'", $datum)
                    )
                )
            );

        if (preg_match('!!u', $datum)) {
            // https://stackoverflow.com/questions/4407854/how-do-i-detect-if-have-to-apply-utf-8-decode-or-encode-on-a-string

            $datum = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $datum);
        }

        echo "C;Y$row;X$col;K\"$datum\"\n";

        ++$col;
    }

    ++$row;
}
