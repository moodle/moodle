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
 * File system configuration.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_s3_fs;

use local_aws_sdk\aws_sdk;

/**
 * File system configuration.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config {
    /**
     * S3 API version to use.
     */
    const S3_VERSION = '2006-03-01';

    /**
     * AWS S3 client configuration.
     *
     * @var array
     */
    public $client = [];

    /**
     * S3 bucket name.
     *
     * @var string
     */
    public $bucket = '';

    /**
     * Place all files under this sub directory.
     *
     * @var string
     */
    public $folder = '';

    /**
     * Delete files from the bucket.
     *
     * @var bool
     */
    public $delete = true;

    /**
     * Stream gz-files from S3.
     *
     * If false, then will download the gz-file to disk before opening it.
     *
     * @var bool
     */
    public $gzstream = true;

    /**
     * Create a new config instance from $CFG.
     *
     * @return self
     */
    public static function create_from_cfg() {
        global $CFG;

        $config = new self();
        $config->client = aws_sdk::config_from_cfg('tool_s3_fs');

        // See http://docs.aws.amazon.com/aws-sdk-php/v3/guide/faq.html#why-is-the-s3-client-decompressing-gzipped-files
        // and http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html#http-decode-content
        // for why we disable this.
        $config->client['http'] = ['decode_content' => false];
        $config->client['version'] = self::S3_VERSION;

        if (empty($CFG->tool_s3_fs['bucket'])) {
            throw new \coding_exception('The $CFG->tool_s3_fs is missing the \'bucket\' option');
        }
        $config->bucket = (string) $CFG->tool_s3_fs['bucket'];

        if (array_key_exists('folder', $CFG->tool_s3_fs)) {
            $config->folder = (string) $CFG->tool_s3_fs['folder'];
        }
        if (array_key_exists('delete', $CFG->tool_s3_fs)) {
            $config->delete = (bool) $CFG->tool_s3_fs['delete'];
        }
        if (array_key_exists('gzstream', $CFG->tool_s3_fs)) {
            $config->gzstream = (bool) $CFG->tool_s3_fs['gzstream'];
        }

        return $config;
    }
}
