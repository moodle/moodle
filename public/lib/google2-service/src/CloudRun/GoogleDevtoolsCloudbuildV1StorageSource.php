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

namespace Google\Service\CloudRun;

class GoogleDevtoolsCloudbuildV1StorageSource extends \Google\Model
{
  /**
   * Unspecified defaults to GSUTIL.
   */
  public const SOURCE_FETCHER_SOURCE_FETCHER_UNSPECIFIED = 'SOURCE_FETCHER_UNSPECIFIED';
  /**
   * Use the "gsutil" tool to download the source file.
   */
  public const SOURCE_FETCHER_GSUTIL = 'GSUTIL';
  /**
   * Use the Cloud Storage Fetcher tool to download the source file.
   */
  public const SOURCE_FETCHER_GCS_FETCHER = 'GCS_FETCHER';
  /**
   * Cloud Storage bucket containing the source (see [Bucket Name
   * Requirements](https://cloud.google.com/storage/docs/bucket-
   * naming#requirements)).
   *
   * @var string
   */
  public $bucket;
  /**
   * Optional. Cloud Storage generation for the object. If the generation is
   * omitted, the latest generation will be used.
   *
   * @var string
   */
  public $generation;
  /**
   * Required. Cloud Storage object containing the source. This object must be a
   * zipped (`.zip`) or gzipped archive file (`.tar.gz`) containing source to
   * build.
   *
   * @var string
   */
  public $object;
  /**
   * Optional. Option to specify the tool to fetch the source file for the
   * build.
   *
   * @var string
   */
  public $sourceFetcher;

  /**
   * Cloud Storage bucket containing the source (see [Bucket Name
   * Requirements](https://cloud.google.com/storage/docs/bucket-
   * naming#requirements)).
   *
   * @param string $bucket
   */
  public function setBucket($bucket)
  {
    $this->bucket = $bucket;
  }
  /**
   * @return string
   */
  public function getBucket()
  {
    return $this->bucket;
  }
  /**
   * Optional. Cloud Storage generation for the object. If the generation is
   * omitted, the latest generation will be used.
   *
   * @param string $generation
   */
  public function setGeneration($generation)
  {
    $this->generation = $generation;
  }
  /**
   * @return string
   */
  public function getGeneration()
  {
    return $this->generation;
  }
  /**
   * Required. Cloud Storage object containing the source. This object must be a
   * zipped (`.zip`) or gzipped archive file (`.tar.gz`) containing source to
   * build.
   *
   * @param string $object
   */
  public function setObject($object)
  {
    $this->object = $object;
  }
  /**
   * @return string
   */
  public function getObject()
  {
    return $this->object;
  }
  /**
   * Optional. Option to specify the tool to fetch the source file for the
   * build.
   *
   * Accepted values: SOURCE_FETCHER_UNSPECIFIED, GSUTIL, GCS_FETCHER
   *
   * @param self::SOURCE_FETCHER_* $sourceFetcher
   */
  public function setSourceFetcher($sourceFetcher)
  {
    $this->sourceFetcher = $sourceFetcher;
  }
  /**
   * @return self::SOURCE_FETCHER_*
   */
  public function getSourceFetcher()
  {
    return $this->sourceFetcher;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1StorageSource::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1StorageSource');
