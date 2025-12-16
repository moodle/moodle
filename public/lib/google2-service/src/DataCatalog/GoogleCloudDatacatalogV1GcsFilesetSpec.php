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

class GoogleCloudDatacatalogV1GcsFilesetSpec extends \Google\Collection
{
  protected $collection_key = 'sampleGcsFileSpecs';
  /**
   * Required. Patterns to identify a set of files in Google Cloud Storage. For
   * more information, see [Wildcard Names]
   * (https://cloud.google.com/storage/docs/wildcards). Note: Currently, bucket
   * wildcards are not supported. Examples of valid `file_patterns`: *
   * `gs://bucket_name/dir`: matches all files in `bucket_name/dir` directory *
   * `gs://bucket_name/dir*`: matches all files in `bucket_name/dir` and all
   * subdirectories * `gs://bucket_name/file*`: matches files prefixed by `file`
   * in `bucket_name` * `gs://bucket_name/??.txt`: matches files with two
   * characters followed by `.txt` in `bucket_name` *
   * `gs://bucket_name/[aeiou].txt`: matches files that contain a single vowel
   * character followed by `.txt` in `bucket_name` *
   * `gs://bucket_name/[a-m].txt`: matches files that contain `a`, `b`, ... or
   * `m` followed by `.txt` in `bucket_name` * `gs://bucket_name/a/b`: matches
   * all files in `bucket_name` that match the `a/b` pattern, such as `a/c/b`,
   * `a/d/b` * `gs://another_bucket/a.txt`: matches `gs://another_bucket/a.txt`
   * You can combine wildcards to match complex sets of files, for example:
   * `gs://bucket_name/[a-m]??.j*g`
   *
   * @var string[]
   */
  public $filePatterns;
  protected $sampleGcsFileSpecsType = GoogleCloudDatacatalogV1GcsFileSpec::class;
  protected $sampleGcsFileSpecsDataType = 'array';

  /**
   * Required. Patterns to identify a set of files in Google Cloud Storage. For
   * more information, see [Wildcard Names]
   * (https://cloud.google.com/storage/docs/wildcards). Note: Currently, bucket
   * wildcards are not supported. Examples of valid `file_patterns`: *
   * `gs://bucket_name/dir`: matches all files in `bucket_name/dir` directory *
   * `gs://bucket_name/dir*`: matches all files in `bucket_name/dir` and all
   * subdirectories * `gs://bucket_name/file*`: matches files prefixed by `file`
   * in `bucket_name` * `gs://bucket_name/??.txt`: matches files with two
   * characters followed by `.txt` in `bucket_name` *
   * `gs://bucket_name/[aeiou].txt`: matches files that contain a single vowel
   * character followed by `.txt` in `bucket_name` *
   * `gs://bucket_name/[a-m].txt`: matches files that contain `a`, `b`, ... or
   * `m` followed by `.txt` in `bucket_name` * `gs://bucket_name/a/b`: matches
   * all files in `bucket_name` that match the `a/b` pattern, such as `a/c/b`,
   * `a/d/b` * `gs://another_bucket/a.txt`: matches `gs://another_bucket/a.txt`
   * You can combine wildcards to match complex sets of files, for example:
   * `gs://bucket_name/[a-m]??.j*g`
   *
   * @param string[] $filePatterns
   */
  public function setFilePatterns($filePatterns)
  {
    $this->filePatterns = $filePatterns;
  }
  /**
   * @return string[]
   */
  public function getFilePatterns()
  {
    return $this->filePatterns;
  }
  /**
   * Output only. Sample files contained in this fileset, not all files
   * contained in this fileset are represented here.
   *
   * @param GoogleCloudDatacatalogV1GcsFileSpec[] $sampleGcsFileSpecs
   */
  public function setSampleGcsFileSpecs($sampleGcsFileSpecs)
  {
    $this->sampleGcsFileSpecs = $sampleGcsFileSpecs;
  }
  /**
   * @return GoogleCloudDatacatalogV1GcsFileSpec[]
   */
  public function getSampleGcsFileSpecs()
  {
    return $this->sampleGcsFileSpecs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1GcsFilesetSpec::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1GcsFilesetSpec');
