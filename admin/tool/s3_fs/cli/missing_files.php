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
 * Look for missing files on S3
 *
 * @package    s3_fs
 * @copyright  2020 Open LMS
 * @author     Oscar Nadjar <oscar.nadjar@openlms.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_s3_fs;
use assignfeedback_editpdf\document_services;
use assignfeedback_editpdf\combined_document;
use context_module;
use assign;

define('CLI_SCRIPT', true);
require(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
global $CFG, $DB, $USER;

require_once($CFG->libdir.'/clilib.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

$longoptions = [
    'help' => false,
    'csv' => false,
    'pdfrestore' => false,
    'imgrestore' => false
];
list($options, $unrecognized) = cli_get_params($longoptions, ['h' => 'help']);
\core\session\manager::set_user(get_admin());

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}

if ($options['help']) {
    // The indentation of this string is "wrong" but this is to avoid a extra whitespace in console output.
    $help = <<<EOF
Validates which files from converted from assignments are missing on S3

Options:
-h, --help            Print out this help.
    --csv             Creates and CSV file with the hash of the missing files.
    --pdfrestore      Restore a missing PDF from a assign submission.
    --imgrestore      Restore annotation images.(Heavy load)

Example:
php admin/tool/s3_fs/classes/missing_files.php

EOF;

    echo $help;
    exit(0);
}

echo "Getting hash of converted files...." . PHP_EOL;
$assignfiles = $DB->get_records_sql("SELECT pathnamehash, filearea, itemid, filename, contenthash  as hash
                                       FROM {files}
                                      WHERE filearea IN ('documentconversion', 'pages', 'combined', 'readonlypages' )
                                        AND filename NOT LIKE '.'");
$config = config::create_from_cfg();
$root = 'filedir';
if ($config->folder !== '') {
    $root = $config->folder.'/'.$root;
}

$s3clientinstance = new s3_client($config->client, $config->bucket, $root);
echo "Hash of missing files on S3:" . PHP_EOL;
if ($options['csv']) {
    if (!file_exists($CFG->localcachedir . '/s3missing_files')) {
        mkdir($CFG->localcachedir . '/s3missing_files');
    }
    $fp = fopen($CFG->localcachedir . '/s3missing_files/missing_files_hash.csv', 'w');
    fputcsv($fp, ['contenthash']);
}
$filesperarea = [];
$filesperarea['documentconversion'] = [];
$filesperarea['others'] = [];

foreach ($assignfiles as $file) {
    if (!$s3clientinstance->file_exist($file->hash)) {
        if ($options['csv']) {
            fputcsv($fp, [$s3clientinstance->get_stream_path($file->hash)]);
        }
        $missingfilesfound = true;
        echo $s3clientinstance->get_stream_path($file->hash) . PHP_EOL;
        if ($file->filearea == 'documentconversion') {
            $filesperarea['documentconversion'][] = $file;
        } else {
            $filesperarea['others'][] = $file;
        }
    }
}

if ($options['pdfrestore'] && !empty($missingfilesfound && !empty($filesperarea['documentconversion']))) {
    $hashlist = [];
    $itemidlist = [];
    $filenamelist = [];
    foreach ($filesperarea['documentconversion'] as $pdflost) {
        $hashlist[] = $pdflost->hash;
        $itemidlist[] = $pdflost->itemid;
        $filenamelist[] = $pdflost->filename;
    }
    list($insql, $inparams) = $DB->get_in_or_equal($filenamelist);
    $sql = "SELECT itemid as submissionid, 0 as submissionattempt
              FROM {files}
             WHERE filearea = 'submission_files'
               AND contenthash $insql
          GROUP BY itemid";
    $idstoqueue = $DB->get_records_sql($sql, $inparams);

    // To recreate the PDF we need delete the previous record.
    $DB->delete_records_list('files', 'contenthash', $hashlist);
    // Add to the conversion queue again.
    $DB->insert_records('assignfeedback_editpdf_queue', $idstoqueue);
}

if ($options['imgrestore'] && !empty($missingfilesfound) && !empty($filesperarea['others'])) {

    $itemidlist = [];
    foreach ($filesperarea['others'] as $pdflost) {
        $itemidlist[] = $pdflost->itemid;
    }
    list($insql, $inparams) = $DB->get_in_or_equal($itemidlist);
    $sql = "SELECT id, assignment, userid, attemptnumber
              FROM {assign_grades}
             WHERE id $insql";
    $ids = $DB->get_records_sql($sql, $inparams);
    $DB->delete_records_select('files', 'filearea IN ("pages", "combined", "readonlypages") AND itemid', $itemidlist);
    // We cann't recreate page by page so we need to recreate everything.
    foreach ($ids as $assigninfo) {
        $cm = get_coursemodule_from_instance('assign', $assigninfo->assignment, 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);
        $assignment = new assign($context, null, null);
        document_services::get_combined_pdf_for_attempt($assignment, $assigninfo->userid, $assigninfo->attemptnumber);
        document_services::get_page_images_for_attempt(
            $assignment,
            $assigninfo->userid,
            $assigninfo->attemptnumber,
            false
        );
        document_services::get_page_images_for_attempt(
            $assignment,
            $assigninfo->userid,
            $assigninfo->attemptnumber,
            true
        );
    }


}
if (empty($missingfilesfound)) {
    echo "No missing files found" . PHP_EOL;
}
if ($options['csv']) {
    fclose($fp);
    echo $CFG->localcachedir . "/s3missing_files/missing_files_hash.csv" . PHP_EOL;
}
exit(0);

