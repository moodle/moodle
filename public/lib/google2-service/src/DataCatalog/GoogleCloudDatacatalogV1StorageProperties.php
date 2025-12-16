<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1StorageProperties extends \Google\Collection
{
  protected $collection_key = 'filePattern';
  /**
   * Patterns to identify a set of files for this fileset. Examples of a valid
   * `file_pattern`: * `gs://bucket_name/dir`: matches all files in the
   * `bucket_name/dir` directory * `gs://bucket_name/dir*`: matches all files in
   * the `bucket_name/dir` and all subdirectories recursively *
   * `gs://bucket_name/file*`: matches files prefixed by `file` in `bucket_name`
   * * `gs://bucket_name/??.txt`: matches files with two characters followed by
   * `.txt` in `bucket_name` * `gs://bucket_name/[aeiou].txt`: matches files
   * that contain a single vowel character followed by `.txt` in `bucket_name` *
   * `gs://bucket_name/[a-m].txt`: matches files that contain `a`, `b`, ... or
   * `m` followed by `.txt` in `bucket_name` * `gs://bucket_name/a/b`: matches
   * all files in `bucket_name` that match the `a/b` pattern, such as `a/c/b`,
   * `a/d/b` * `gs://another_bucket/a.txt`: matches `gs://another_bucket/a.txt`
   *
   * @var string[]
   */
  public $filePattern;
  /**
   * File type in MIME format, for example, `text/plain`.
   *
   * @var string
   */
  public $fileType;

  /**
   * Patterns to identify a set of files for this fileset. Examples of a valid
   * `file_pattern`: * `gs://bucket_name/dir`: matches all files in the
   * `bucket_name/dir` directory * `gs://bucket_name/dir*`: matches all files in
   * the `bucket_name/dir` and all subdirectories recursively *
   * `gs://bucket_name/file*`: matches files prefixed by `file` in `bucket_name`
   * * `gs://bucket_name/??.txt`: matches files with two characters followed by
   * `.txt` in `bucket_name` * `gs://bucket_name/[aeiou].txt`: matches files
   * that contain a single vowel character followed by `.txt` in `bucket_name` *
   * `gs://bucket_name/[a-m].txt`: matches files that contain `a`, `b`, ... or
   * `m` followed by `.txt` in `bucket_name` * `gs://bucket_name/a/b`: matches
   * all files in `bucket_name` that match the `a/b` pattern, such as `a/c/b`,
   * `a/d/b` * `gs://another_bucket/a.txt`: matches `gs://another_bucket/a.txt`
   *
   * @param string[] $filePattern
   */
  public function setFilePattern($filePattern)
  {
    $this->filePattern = $filePattern;
  }
  /**
   * @return string[]
   */
  public function getFilePattern()
  {
    return $this->filePattern;
  }
  /**
   * File type in MIME format, for example, `text/plain`.
   *
   * @param string $fileType
   */
  public function setFileType($fileType)
  {
    $this->fileType = $fileType;
  }
  /**
   * @return string
   */
  public function getFileType()
  {
    return $this->fileType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1StorageProperties::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1StorageProperties');
