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
 * Box.net locallib.
 *
 * @package    repository_boxnet
 * @copyright  2013 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Migrate the references to local files.
 *
 * As the APIv1 is reaching its end of life on the 14th of Dec 2013, and we cannot
 * convert the existing references to new references, we need to convert them
 * to real files.
 *
 * @todo   Deprecate/remove this function after the 14th of December 2013.
 * @return void
 */
function repository_boxnet_migrate_references_from_apiv1() {
    global $DB;

    // A string that the old references contain.
    $apiv1signature = '/api/1.0/download/';

    // Downloading the files could take a very long time!
    @set_time_limit(0);

    // Create directory to download temporary files.
    $dir = make_temp_directory('download/repository_boxnet/');

    // Create a dummy file for the broken files.
    $fs = get_file_storage();
    list($dummyhash, $dummysize, $unused) = $fs->add_string_to_pool('Lost reference from Box.net');

    // Get the Box.net instances. There should be only one.
    $sql = "SELECT i.id, i.typeid, r.id, r.type
              FROM {repository} r, {repository_instances} i
             WHERE i.typeid = r.id
               AND r.type = :type";
    $ids = $DB->get_fieldset_sql($sql, array('type' => 'boxnet'));
    if (empty($ids)) {
        // We did not find any instance of Box.net. Let's just ignore this migration.
        mtrace('Could not find any instance of the repository, aborting migration...');
        return;
    }

    // The next bit is copied from the function file_storage::instance_sql_fields()
    // because it is private and there is nothing in file_storage that suits our needs here.
    $filefields = array('contenthash', 'pathnamehash', 'contextid', 'component', 'filearea',
        'itemid', 'filepath', 'filename', 'userid', 'filesize', 'mimetype', 'status', 'source',
        'author', 'license', 'timecreated', 'timemodified', 'sortorder', 'referencefileid');
    $referencefields = array('repositoryid' => 'repositoryid',
        'reference' => 'reference',
        'lifetime' => 'referencelifetime',
        'lastsync' => 'referencelastsync');
    $fields = array();
    $fields[] = 'f.id AS id';
    foreach ($filefields as $field) {
        $fields[] = "f.{$field}";
    }
    foreach ($referencefields as $field => $alias) {
        $fields[] = "r.{$field} AS {$alias}";
    }
    $fields = implode(', ', $fields);

    // We are not using repository::convert_references_to_local() or file_storage::get_external_files()
    // because they would select too many records and load everything in memory as it is not using a recordset.
    // Also, we filter the results not to get the draft area which should not be converted.
    list($sqlfragment, $fragmentparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
    $sql = "SELECT " . $fields . "
              FROM {files_reference} r
              LEFT JOIN {files} f
                   ON f.referencefileid = r.id
             WHERE r.repositoryid $sqlfragment
               AND f.referencefileid IS NOT NULL
               AND NOT (f.component = :component
               AND f.filearea = :filearea)";

    // For each reference we download the file. Then we add it to the file pool and update the references.
    // The reason why we are re-inventing the wheel here is because the current API ends up calling
    // repository::get_file() which includes a download timeout. As we are trying our best to copy
    // the files here, we want to ignre any timeout.
    $filerecords = $DB->get_recordset_sql($sql, array_merge($fragmentparams, array('component' => 'user', 'filearea' => 'draft')));
    $referenceids = array();
    foreach ($filerecords as $filerecord) {
        $file = $fs->get_file_instance($filerecord);
        $reference = unserialize(repository_boxnet::convert_to_valid_reference($file->get_reference()));

        if (empty($reference->downloadurl)) {
            // Something is wrong...
            mtrace('Skipping malformed reference (id: ' . $file->get_referencefileid() . ')');
            continue;
        } else if (strpos($reference->downloadurl, $apiv1signature) === false) {
            // This is not an old reference, we are not supposed to work on thos.
            mtrace('Skipping non APIv1 reference (id: ' . $file->get_referencefileid() . ')');
            continue;
        } else if (isset($referenceids[$file->get_referencefileid()])) {
            // We have already worked on that reference, we skip any other file related to it.
            // We cannot work on them here because they have been updated in the database but our
            // recordset does not have those new values. They will be taken care of after this foreach.
            continue;
        }

        mtrace('Starting migration of file reference ' . $file->get_referencefileid());

        // Manually import the file to the file pool to prevent timeout limitations of the repository method get_file().
        // We ignore the fact that the content of the file could exist locally because we want to synchronize the file
        // now to prevent the repository to try to download the file as well.
        $saveas = $dir . uniqid('', true) . '_' . time() . '.tmp';
        $c = new curl();
        $result = $c->download_one($reference->downloadurl, null, array('filepath' => $saveas, 'followlocation' => true));
        $info = $c->get_info();
        if ($result !== true || !isset($info['http_code']) || $info['http_code'] != 200) {
            // There was a problem while trying to download the reference...
            if ($fs->content_exists($file->get_contenthash()) && $file->get_contenthash() != sha1('')) {
                // Fortunately we already had a local version of this reference, so we keep it. We have to
                // set it synchronized or there is a risk that repository::sync_reference() will try to download
                // the file again. We cannot use $file->get_contenthash() and $file->get_filesize() because they
                // cause repository::sync_reference() to be called.
                $file->set_synchronized($filerecord->contenthash, $filerecord->filesize, 0, DAYSECS);
                mtrace('Could not download reference, using last synced file. (id: ' . $file->get_referencefileid() . ')');
            } else {
                // We don't know what the file was, but what can we do? In order to prevent a re-attempt to fetch the
                // file in the next bit of this script (import_external_file()), we set a dummy content to the reference.
                $file->set_synchronized($dummyhash, $dummysize, 0, DAYSECS);
                mtrace('Could not download reference, dummy file used. (id: ' . $file->get_referencefileid() . ')');
            }
        } else {
            try {
                // The file has been downloaded, we add it to the file pool and synchronize
                // all the files using this reference.
                list($contenthash, $filesize, $unused) = $fs->add_file_to_pool($saveas);
                $file->set_synchronized($contenthash, $filesize, 0, DAYSECS);
            } catch (moodle_exception $e) {
                // Something wrong happened...
                mtrace('Something went wrong during sync (id: ' . $file->get_referencefileid() . ')');
            }
        }

        // Log the reference IDs.
        $referenceids[$file->get_referencefileid()] = $file->get_referencefileid();

        // Now that the file is downloaded, we can loop over all the files using this reference
        // to convert them to local copies. We have chosen to do that in this loop so that if the
        // execution fails in the middle, we would not have to redownload the files again and again.
        // By the way, we cannot use the records fetched in $filerecords because they will not be updated.
        $sql = "SELECT " . $fields . "
                  FROM {files} f
                  LEFT JOIN {files_reference} r
                       ON f.referencefileid = r.id
                 WHERE f.referencefileid = :refid
                   AND NOT (f.component = :component
                   AND f.filearea = :filearea)";
        $reffilerecords = $DB->get_recordset_sql($sql, array('component' => 'user', 'filearea' => 'draft',
            'refid' => $file->get_referencefileid()));
        foreach ($reffilerecords as $reffilerecord) {
            $reffile = $fs->get_file_instance($reffilerecord);
            try {
                // Updating source to remove trace of APIv1 URL.
                $reffile->set_source('Box APIv1 reference');
            } catch (moodle_exception $e) {
                // Do not fail for this lame reason...
            }
            try {
                $fs->import_external_file($reffile);
                mtrace('File using reference converted to local file (id: ' . $reffile->get_id() . ')');
            } catch (moodle_exception $e) {
                // Oh well... we tried what we could!
                $reffile->delete_reference();
                mtrace('Failed to convert file from reference to local file, sorry! (id: ' . $reffile->get_id() . ')');
            }
        }
    }

    mtrace('Migration finished.');
}
