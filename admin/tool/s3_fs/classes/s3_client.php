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
 * Wraps the AWS SDK S3 client.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_s3_fs;

use Aws\Result;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use local_aws_sdk\aws_sdk;
use Psr\Http\Message\StreamInterface;

/**
 * Wraps the AWS SDK S3 client.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class s3_client {
    /**
     * @var S3Client
     */
    private $s3;

    /**
     * The S3 bucket name.
     *
     * @var string
     */
    private $bucket;

    /**
     * All files are stored under this path in the bucket.
     *
     * @var string
     */
    private $root;

    /**
     * Minimum size for caching a file before triggering multipart uploads.
     *
     * @const int
     */
    const MIN_SIZE_FOR_PARTS = 512000000;

    /**
     * @param array $args Args for S3Client
     * @param string $bucket The S3 bucket name
     * @param string $root Store all files under this path
     */
    public function __construct(array $args, $bucket, $root) {
        aws_sdk::autoload();

        $this->s3     = new S3Client($args);
        $this->bucket = $bucket;
        $this->root   = $root;

        $this->s3->registerStreamWrapper();
    }

    /**
     * Handle error from S3.
     *
     * Errors from the S3 stream wrapper are not handled here.
     *
     * @param S3Exception $exception
     * @param string $hint End user visible message
     * @return \file_exception
     */
    private function handle_error(S3Exception $exception, $hint) {
        if (PHP_MAJOR_VERSION >= 7) {
            $id = bin2hex(random_bytes(7));
        } else {
            $id = uniqid();
        }

        $id   = '[ID:'.$id.']';
        $code = !empty($exception->getAwsErrorCode()) ? $exception->getAwsErrorCode() : 'Not Set';

        $log = $id.' [S3_FS] [ErrorCode:'.$code.'] ';
        $log .= $exception->getMessage().PHP_EOL;
        $log .= 'User message: '.$hint.PHP_EOL;
        $log .= 'Backtrace:'.PHP_EOL;
        $log .= format_backtrace(get_exception_info($exception)->backtrace, true);

        if (!PHPUNIT_TEST) {
            error_log($log); // @codingStandardsIgnoreLine
        }

        return new \file_exception('storedfileproblem', $hint.' '.$id);
    }

    /**
     * Remote path in S3.
     *
     * Should be compatible with \file_system_filedir::get_contentdir_from_hash.
     *
     * @param string $contenthash
     * @return string
     */
    private function get_remote_path($contenthash) {
        $l1   = $contenthash[0].$contenthash[1];
        $l2   = $contenthash[2].$contenthash[3];
        $path = "$l1/$l2/$contenthash";

        return $this->root.'/'.$path;
    }

    /**
     * Upload content to S3.
     *
     * @param string $key
     * @param StreamInterface $stream
     */
    private function upload_stream($key, StreamInterface $stream) {
        $prev = ignore_user_abort(true);

        try {
            // We use upload instead of putObject because it will automatically switch
            // to multipart uploads for files that exceed the threshold.
            $options = ['mup_threshold' => $this->get_actual_multipart_threshold()];
            $this->s3->upload($this->bucket, $key, $stream, 'private', $options);
        } catch (S3Exception $e) {
            ignore_user_abort($prev);
            throw $this->handle_error($e, 'Failed to add file to file system');
        }

        ignore_user_abort($prev);
    }

    /**
     * Get information about a file in S3 without getting the content.
     *
     * @param string $contenthash
     * @return Result|null
     */
    private function head($contenthash) {
        try {
            return $this->s3->headObject([
                'Bucket' => $this->bucket,
                'Key'    => $this->get_remote_path($contenthash),
            ]);
        } catch (S3Exception $e) {
            if ($e->getStatusCode() >= 500) {
                throw $this->handle_error($e, 'Unable to read from file system');
            }
            if ($e->getAwsErrorCode() !== 'NoSuchKey' && $e->getAwsErrorCode() !== 'NotFound') {
                throw $this->handle_error($e, 'Failed to request file information');
            }
        }

        return null;
    }

    /**
     * Get the stream path to a file S3.
     *
     * @param string $contenthash
     * @return string
     */
    public function get_stream_path($contenthash) {
        return 's3://'.$this->bucket.'/'.$this->get_remote_path($contenthash);
    }

    /**
     * Get the file size of a file in S3.
     *
     * Returns null if file not found.
     *
     * @param string $contenthash
     * @return int|null
     */
    public function file_size($contenthash) {
        $object = $this->head($contenthash);
        if ($object instanceof Result) {
            return $object->get('ContentLength');
        }

        return null;
    }

    /**
     * Download a file from S3.
     *
     * @param string $contenthash
     * @param string $destination
     */
    public function download($contenthash, $destination) {
        $resource = fopen($destination, 'w+');
        if ($resource === false) {
            throw new \file_exception('storedfileproblem', 'Cannot open file for writing', 'Path: '.$destination);
        }
        try {
            $this->s3->getObject([
                'Bucket' => $this->bucket,
                'Key'    => $this->get_remote_path($contenthash),
                'SaveAs' => $resource,
            ]);
        } catch (S3Exception $e) {
            if (is_resource($resource)) {
                fclose($resource);
            }
            if (is_file($destination)) {
                unlink($destination);
            }
            throw $this->handle_error($e, 'Failed to retrieve file from file system');
        }
        if (is_resource($resource)) {
            fclose($resource);
        }
    }

    /**
     * Verify if a custom config value exists and return the current file size that should trigger a multipart upload.
     *
     * @return int $sizethreshold
     */
    public function get_actual_multipart_threshold() {
        global $CFG;
        $sizethreshold = !empty($CFG->tool_s3_fs_sizeformultiparts) ? $CFG->tool_s3_fs_sizeformultiparts : self::MIN_SIZE_FOR_PARTS;
        return $sizethreshold;
    }

    /**
     * Upload a file by hash to S3.
     *
     * @param string $contenthash
     * @param StreamInterface $stream
     */
    public function upload($contenthash, StreamInterface $stream) {
        $this->upload_stream($this->get_remote_path($contenthash), $stream);
    }

    /**
     * Delete a file from S3.
     *
     * @param string $contenthash
     */
    public function delete($contenthash) {
        try {
            $this->s3->deleteObject([
                'Bucket' => $this->bucket,
                'Key'    => $this->get_remote_path($contenthash),
            ]);
        } catch (S3Exception $e) {
            throw $this->handle_error($e, 'Failed to remove file from file system');
        }
    }

    /**
     * Actions to take when a hash collision occurs.
     *
     * @param string $contenthash The conflicting hash
     * @param StreamInterface $stream The new content that produced the conflict
     */
    public function hash_collision($contenthash, StreamInterface $stream) {
        $this->upload_stream($this->root.'/jackpot/'.$contenthash.'_1', $stream);

        try {
            // We use copy instead of copyObject because it will automatically switch
            // to multipart uploads for large files.
            $this->s3->copy($this->bucket, $this->get_remote_path($contenthash), $this->bucket,
                $this->root.'/jackpot/'.$contenthash.'_2');
        } catch (S3Exception $e) {
            throw $this->handle_error($e, 'Failed to copy file in file system');
        }
    }

    /**
     * Open a stream to a file.
     *
     * @param string $path Can be a local file path or S3 file path (EG: s3://...)
     * @return bool|resource
     */
    public function open_stream($path) {
        return fopen($path, 'rb', false, stream_context_create(['s3' => ['seekable' => true]]));
    }

    /**
     * Open a stream to a gz-file in S3.
     *
     * @param string $contenthash
     * @return null|resource
     */
    public function open_gz_stream($contenthash) {
        $handle = $this->open_stream($this->get_stream_path($contenthash));
        if ($handle === false) {
            return null;
        }

        // Skip beyond the gzip header so zlib can decode - doesn't handle optional extra fields.
        $header = bin2hex(fread($handle, 10)); // 10 byte header: http://www.forensicswiki.org/wiki/Gzip
        fseek($handle, 10); // Seems necessary to clear buffers so stream append will work.

        $badheader  = !preg_match('#^1f8b\d\d00#', $header);
        $failappend = !stream_filter_append($handle, 'zlib.inflate', STREAM_FILTER_READ);

        if ($badheader || $failappend || feof($handle)) {
            fclose($handle);
            return null;
        }

        return $handle;
    }

    /**
     * Check if a files exist on S3.
     *
     * @param string $contenthash
     */
    public function file_exist($contenthash) {
        try {
            return $this->s3->doesObjectExist(
                $this->bucket,
                $this->get_remote_path($contenthash));
        } catch (S3Exception $e) {
            throw $this->handle_error($e, 'Failed to connect to S3');
        }
    }
}
