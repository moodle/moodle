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
 * Classes for converting files between different file formats.
 *
 * @package    core_files
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_files;

defined('MOODLE_INTERNAL') || die();

use stored_file;

/**
 * Class representing a conversion currently in progress.
 *
 * @package    core_files
 * @copyright  2017 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class conversion extends \core\persistent {

    /**
     * Status value representing a conversion waiting to start.
     */
    const STATUS_PENDING = 0;

    /**
     * Status value representing a conversion in progress.
     */
    const STATUS_IN_PROGRESS = 1;

    /**
     * Status value representing a successful conversion.
     */
    const STATUS_COMPLETE = 2;

    /**
     * Status value representing a failed conversion.
     */
    const STATUS_FAILED = -1;

    /**
     * Table name for this persistent.
     */
    const TABLE = 'file_conversion';

    /**
     * Define properties.
     *
     * @return array
     */
    protected static function define_properties() {
        return array(
            'sourcefileid' => [
                'type' => PARAM_INT,
            ],
            'targetformat' => [
                'type' => PARAM_ALPHANUMEXT,
            ],
            'status' => [
                'type' => PARAM_INT,
                'choices' => [
                    self::STATUS_PENDING,
                    self::STATUS_IN_PROGRESS,
                    self::STATUS_COMPLETE,
                    self::STATUS_FAILED,
                ],
                'default' => self::STATUS_PENDING,
            ],
            'statusmessage' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'converter' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'destfileid' => [
                'type' => PARAM_INT,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
            'data' => [
                'type' => PARAM_RAW,
                'null' => NULL_ALLOWED,
                'default' => null,
            ],
        );
    }

    /**
     * Fetch all conversions relating to the specified file.
     *
     * Only conversions which have a valid file are returned.
     *
     * @param   stored_file $file The source file being converted
     * @param   string $format The targetforamt to filter to
     * @return  conversion[]
     */
    public static function get_conversions_for_file(stored_file $file, $format) {
        global $DB;
        $instances = [];

        // Conversion records are intended for tracking a conversion in progress or recently completed.
        // The record is removed periodically, but the destination file is not.
        // We need to fetch all conversion records which match the source file and target, and also all source and
        // destination files which do not have a conversion record.
        $sqlfields = self::get_sql_fields('c', 'conversion');

        // Fetch actual conversions which relate to the specified source file, and have a matching conversion record,
        // and either have a valid destination file which still exists, or do not have a destination file at all.
        $sql = "SELECT {$sqlfields}
                  FROM {" . self::TABLE . "} c
                  JOIN {files} conversionsourcefile ON conversionsourcefile.id = c.sourcefileid
             LEFT JOIN {files} conversiondestfile ON conversiondestfile.id = c.destfileid
                 WHERE conversionsourcefile.contenthash = :ccontenthash
                       AND c.targetformat = :cformat
                       AND (c.destfileid IS NULL OR conversiondestfile.id IS NOT NULL)";

        // Fetch a empty conversion record for each source/destination combination that we find to match where the
        // destination file is in the correct filearea/filepath/filename combination to meet the requirements.
        // This ensures that existing conversions are used where possible, even if there is no 'conversion' record for
        // them.
        $sql .= "
            UNION ALL
                SELECT
                    NULL AS conversionid,
                    orphanedsourcefile.id AS conversionsourcefileid,
                    :oformat AS conversiontargetformat,
                    2 AS conversionstatus,
                    NULL AS conversionstatusmessage,
                    NULL AS conversionconverter,
                    orphaneddestfile.id AS conversiondestfileid,
                    NULL AS conversiondata,
                    0 AS conversiontimecreated,
                    0 AS conversiontimemodified,
                    0 AS conversionusermodified
                FROM {files} orphanedsourcefile
                INNER JOIN {files} orphaneddestfile ON (
                        orphaneddestfile.filename = orphanedsourcefile.contenthash
                    AND orphaneddestfile.component = 'core'
                    AND orphaneddestfile.filearea = 'documentconversion'
                    AND orphaneddestfile.filepath = :ofilepath
                )
                LEFT JOIN {" . self::TABLE . "} orphanedconversion ON orphanedconversion.destfileid = orphaneddestfile.id
                WHERE
                    orphanedconversion.id IS NULL
                AND
                    orphanedsourcefile.id = :osourcefileid
                ";
        $records = $DB->get_records_sql($sql, [
            'ccontenthash' => $file->get_contenthash(),
            'osourcefileid' => $file->get_id(),
            'ofilepath' => "/{$format}/",
            'cformat' => $format,
            'oformat' => $format,
        ]);

        foreach ($records as $record) {
            $data = self::extract_record($record, 'conversion');
            $newrecord = new static(0, $data);
            $instances[] = $newrecord;
        }

        return $instances;
    }

    /**
     * Remove all old conversion records.
     */
    public static function remove_old_conversion_records() {
        global $DB;

        $DB->delete_records_select(self::TABLE, 'timemodified <= :weekagosecs', [
            'weekagosecs' => time() - WEEKSECS,
        ]);
    }

    /**
     * Remove orphan records.
     *
     * Records are considered orphans when their source file not longer exists.
     * In this scenario we do not want to keep the converted file any longer,
     * in particular to be compliant with privacy laws.
     */
    public static function remove_orphan_records() {
        global $DB;

        $sql = "
            SELECT c.id
              FROM {" . self::TABLE . "} c
         LEFT JOIN {files} f
                ON f.id = c.sourcefileid
             WHERE f.id IS NULL";
        $ids = $DB->get_fieldset_sql($sql, []);

        if (empty($ids)) {
            return;
        }

        list($insql, $inparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED);
        $DB->delete_records_select(self::TABLE, "id $insql", $inparams);
    }

    /**
     * Set the source file id for the conversion.
     *
     * @param   stored_file $file The file to convert
     * @return  $this
     */
    public function set_sourcefile(stored_file $file) {
        $this->raw_set('sourcefileid', $file->get_id());

        return $this;
    }

    /**
     * Fetch the source file.
     *
     * @return  stored_file|false The source file
     */
    public function get_sourcefile() {
        $fs = get_file_storage();

        return $fs->get_file_by_id($this->get('sourcefileid'));
    }

    /**
     * Set the destination file for this conversion.
     *
     * @param   string $filepath The path to the converted file
     * @return  $this
     */
    public function store_destfile_from_path($filepath) {
        if ($record = $this->get_file_record()) {
            $fs = get_file_storage();
            $existing = $fs->get_file(
                $record['contextid'],
                $record['component'],
                $record['filearea'],
                $record['itemid'],
                $record['filepath'],
                $record['filename']
            );
            if ($existing) {
                $existing->delete();
            }
            $file = $fs->create_file_from_pathname($record, $filepath);

            $this->raw_set('destfileid', $file->get_id());
        }

        return $this;
    }

    /**
     * Set the destination file for this conversion.
     *
     * @param   string $content The content of the converted file
     * @return  $this
     */
    public function store_destfile_from_string($content) {
        if ($record = $this->get_file_record()) {
            $fs = get_file_storage();
            $existing = $fs->get_file(
                $record['contextid'],
                $record['component'],
                $record['filearea'],
                $record['itemid'],
                $record['filepath'],
                $record['filename']
            );
            if ($existing) {
                $existing->delete();
            }
            $file = $fs->create_file_from_string($record, $content);

            $this->raw_set('destfileid', $file->get_id());
        }

        return $this;
    }

    /**
     * Get the destination file.
     *
     * @return  stored_file|bool Destination file
     */
    public function get_destfile() {
        $fs = get_file_storage();

        return $fs->get_file_by_id($this->get('destfileid'));
    }

    /**
     * Helper to ensure that the returned status is always an int.
     *
     * @return  int status
     */
    protected function get_status() {
        return (int) $this->raw_get('status');
    }

    /**
     * Get an instance of the current converter.
     *
     * @return  converter_interface|false current converter instance
     */
    public function get_converter_instance() {
        $currentconverter = $this->get('converter');

        if ($currentconverter && class_exists($currentconverter)) {
            return new $currentconverter();
        } else {
            return false;
        }
    }

    /**
     * Transform data into a storable format.
     *
     * @param   \stdClass $data The data to be stored
     * @return  $this
     */
    protected function set_data($data) {
        $this->raw_set('data', json_encode($data));

        return $this;
    }

    /**
     * Transform data into a storable format.
     *
     * @return  \stdClass The stored data
     */
    protected function get_data() {
        $data = $this->raw_get('data');

        if (!empty($data)) {
            return json_decode($data);
        }

        return (object) [];
    }

    /**
     * Return the file record base for use in the files table.
     *
     * @return  array|bool
     */
    protected function get_file_record() {
        $file = $this->get_sourcefile();

        if (!$file) {
            // If the source file was removed before we completed, we must return early.
            return false;
        }

        return [
            'contextid' => \context_system::instance()->id,
            'component' => 'core',
            'filearea'  => 'documentconversion',
            'itemid'    => 0,
            'filepath'  => "/" . $this->get('targetformat') . "/",
            'filename'  => $file->get_contenthash(),
        ];
    }
}
