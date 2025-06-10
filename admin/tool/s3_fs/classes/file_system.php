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
 * A file system that uses a S3 bucket as the storage mechanism.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_s3_fs;

use Psr\Http\Message\StreamInterface;
use stored_file;

/**
 * A file system that uses a S3 bucket as the storage mechanism.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class file_system extends \file_system {
    /**
     * @var config
     */
    private $config;

    /**
     * @var s3_client
     */
    private $client;

    /**
     * Request cache directory.
     *
     * @var string
     */
    private $cache;

    /**
     * @param config|null $config Config
     * @param s3_client|null $client S3 client
     * @param string|null $cache Cache directory
     */
    public function __construct(config $config = null, s3_client $client = null, $cache = null) {
        $this->config = $config ?: config::create_from_cfg();
        $this->cache  = $cache ?: make_request_directory();

        $root = 'filedir';
        if ($this->config->folder !== '') {
            $root = $this->config->folder.'/'.$root;
        }

        $this->client = $client ?: new s3_client($this->config->client, $this->config->bucket, $root);
    }

    /**
     * Determine if we can skip processing the file for addition.
     *
     * This does not check S3 for file existence.
     *
     * @param string $contenthash
     * @return bool
     */
    private function can_skip_add_file($contenthash) {
        return $contenthash === \file_storage::hash_from_string('');
    }

    /**
     * Upload a stream to S3.
     *
     * @param string $contenthash
     * @param int $filesize
     * @param StreamInterface $stream The file contents to upload
     * @return array
     */
    private function add_file_from_stream($contenthash, $filesize, StreamInterface $stream, $deletepath = null) {
        try {
            $result = $this->add_file($contenthash, $filesize, $stream);
        } catch (\Exception $e) {
            $stream->close();
            $this->delete_from_localcache($deletepath);
            throw $e;
        }

        $stream->close();
        $this->delete_from_localcache($deletepath);

        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
            $result[] = $deletepath;
        }
        return $result;

    }

    /**
     * Upload a file to S3.
     *
     * @param string $contenthash
     * @param int $filesize
     * @param StreamInterface $stream The file contents to upload
     * @return array
     */
    private function add_file($contenthash, $filesize, StreamInterface $stream) {

        // This logic is copied from \file_system_filedir::add_file_from_path.

        $newfile = true;
        $size    = $this->client->file_size($contenthash);

        if ($size !== null) {
            if ($size === $filesize) {
                return [$contenthash, $filesize, false];
            }
            $path = $this->cache.'/jackpot-'.$contenthash;
            $this->client->download($contenthash, $path);
            if (\file_storage::hash_from_path($path) === $contenthash) {
                // Jackpot! We have a hash collision.
                $this->client->hash_collision($contenthash, $stream);

                throw new \file_pool_content_exception($contenthash);
            }
            debugging("Replacing invalid content file $contenthash");
            $newfile = false;
        }

        $this->client->upload($contenthash, $stream);

        return [$contenthash, $filesize, $newfile];
    }

    public function xsendfile($contenthash) {
        // We do not support this because it would require that the file was downloaded to disk so that
        // nginx or Apache could then serve that file.  Since PHP would already have to download the file,
        // then might as well download the file and send it at the same time.  Do not use the files from
        // our cache directory because that gets deleted after the request, which would remove the file
        // prior to allowing the server to download it via xsendfile.
        return false;
    }

    protected function get_local_path_from_hash($contenthash, $fetchifnotfound = false) {
        $path = $this->cache.'/'.$contenthash;
        if ($fetchifnotfound && !is_readable($path)) {
            $this->client->download($contenthash, $path);
        }

        return $path;
    }

    protected function get_remote_path_from_hash($contenthash) {
        if ($this->is_file_readable_locally_by_hash($contenthash)) {
            return $this->get_local_path_from_hash($contenthash);
        }

        return $this->client->get_stream_path($contenthash) ?? '';
    }

    /**
     * @param stored_file $file
     * @return bool
     */
    public function is_file_readable_remotely_by_storedfile(stored_file $file) {
        if ($file->get_contenthash() === \file_storage::hash_from_string('')) {
            // Empty files are not sent to s3 so we fake it.
            return true;
        }
        return parent::is_file_readable_remotely_by_storedfile($file);
    }

    /**
     * @param stored_file $file
     * @param bool        $fetchifnotfound
     * @return bool
     */
    public function is_file_readable_locally_by_storedfile(stored_file $file, $fetchifnotfound = false) {
        if ($file->get_contenthash() === \file_storage::hash_from_string('')) {
            // Empty files are not sent to s3 so we fake it.
            return true;
        }
        return parent::is_file_readable_locally_by_storedfile($file, $fetchifnotfound);
    }

    public function copy_content_from_storedfile(stored_file $file, $target) {
        if ($this->is_file_readable_locally_by_storedfile($file)) {
            if ($file->get_contenthash() === \file_storage::hash_from_string('')) {
                $result = file_put_contents($target, '');
                if ($result === false) {
                    return $result;
                }
                return true;
            }
            return copy($this->get_local_path_from_storedfile($file), $target);
        }

        $this->client->download($file->get_contenthash(), $target);

        return true;
    }

    public function remove_file($contenthash) {
        if (!self::is_file_removable($contenthash)) {
            return;
        }
        if ($this->is_file_readable_locally_by_hash($contenthash)) {
            unlink($this->get_local_path_from_hash($contenthash));
        }
        if ($this->config->delete) {
            $this->client->delete($contenthash);
        }
    }

    /**
     * Caches a given file.
     *
     * @param string $originalpath The location of the file we want to cache.
     * @return string The path to the cached file.
     */
    public function cache_given_file($originalpath) {
        global $CFG;

        $alreadycached = strpos($originalpath, $CFG->localcachedir.DIRECTORY_SEPARATOR);
        if ($alreadycached !== false) {
            return $originalpath;
        }

        $uniquedirincache = make_request_directory();

        if (copy($originalpath, $uniquedirincache.'/file')) {
            $path = [];
            $path['pathname'] = $uniquedirincache.'/file';
            $path['deletepath'] = $uniquedirincache;
            return $path;
        } else {
            return $originalpath;
        }
    }

    public function add_file_from_path($pathname, $contenthash = null) {

        list($contenthash, $filesize) = $this->validate_hash_and_file_size($contenthash, $pathname);

        if ($this->can_skip_add_file($contenthash)) {
            return [$contenthash, $filesize, false];
        }

        $sizethreshold = $this->client->get_actual_multipart_threshold();
        // Necessary for multipart uploads.
        if ($filesize >= $sizethreshold) {
            $cachepath = $this->cache_given_file($pathname);
        }
        if (!empty($cachepath)) {
            if (is_array($cachepath)) {
                $pathname = $cachepath['pathname'];
                $deletepath = $cachepath['deletepath'];
            } else {
                $pathname = $cachepath;
                $deletepath = null;
            }
        } else {
            $deletepath = null;
        }
        return $this->add_file_from_stream($contenthash, $filesize, stream::from_file($pathname), $deletepath);
    }

    public function add_file_from_string($content) {
        $contenthash = \file_storage::hash_from_string($content);
        $filesize    = strlen($content);

        if ($this->can_skip_add_file($contenthash)) {
            return [$contenthash, $filesize, false];
        }

        return $this->add_file_from_stream($contenthash, $filesize, stream::from_string($content));
    }

    public function get_content_file_handle(stored_file $file, $type = stored_file::FILE_HANDLE_FOPEN) {
        global $CFG;
        switch ($type) {
            case stored_file::FILE_HANDLE_FOPEN:
                $f = $this->client->open_stream($this->get_remote_path_from_storedfile($file));
                if (empty($CFG->local_mrooms_enable_download_all_fix)) {
                    return $f;
                }
                if (empty($f) && $file->get_component() == 'assignsubmission_file') {
                    $tempdir = 'errors';
                    make_temp_directory($tempdir);
                    $filename = $CFG->tempdir . '/' . $tempdir . '/error_download';
                    file_put_contents($filename, "File failed to download");
                    $f = fopen($CFG->tempdir . '/' . $tempdir . '/error_download', 'rb');
                }
                return $f;
            case stored_file::FILE_HANDLE_GZOPEN:
                // If allowed and not already on disk, attempt to stream the file from S3.
                if ($this->config->gzstream && !$this->is_file_readable_locally_by_storedfile($file)) {
                    $handle = $this->client->open_gz_stream($file->get_contenthash());
                    if ($handle !== null) {
                        return $handle;
                    }
                }

                // Fallback, make sure local copy exists and open it.
                $path = $this->get_local_path_from_storedfile($file, true);

                return gzopen($path, 'rb');
            default:
                throw new \coding_exception('Unexpected file handle type');
        }
    }

    public function delete_from_localcache($path) {
        global $CFG;
        // Delete file from localcache after upload to S3.
        if (!empty($path) && file_exists($path) && empty($CFG->s3_fs_deletecacheoff)) {
            remove_dir($path);
        }
    }
}
