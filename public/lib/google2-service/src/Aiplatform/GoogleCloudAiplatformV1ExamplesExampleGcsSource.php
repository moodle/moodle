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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ExamplesExampleGcsSource extends \Google\Model
{
  /**
   * Format unspecified, used when unset.
   */
  public const DATA_FORMAT_DATA_FORMAT_UNSPECIFIED = 'DATA_FORMAT_UNSPECIFIED';
  /**
   * Examples are stored in JSONL files.
   */
  public const DATA_FORMAT_JSONL = 'JSONL';
  /**
   * The format in which instances are given, if not specified, assume it's
   * JSONL format. Currently only JSONL format is supported.
   *
   * @var string
   */
  public $dataFormat;
  protected $gcsSourceType = GoogleCloudAiplatformV1GcsSource::class;
  protected $gcsSourceDataType = '';

  /**
   * The format in which instances are given, if not specified, assume it's
   * JSONL format. Currently only JSONL format is supported.
   *
   * Accepted values: DATA_FORMAT_UNSPECIFIED, JSONL
   *
   * @param self::DATA_FORMAT_* $dataFormat
   */
  public function setDataFormat($dataFormat)
  {
    $this->dataFormat = $dataFormat;
  }
  /**
   * @return self::DATA_FORMAT_*
   */
  public function getDataFormat()
  {
    return $this->dataFormat;
  }
  /**
   * The Cloud Storage location for the input instances.
   *
   * @param GoogleCloudAiplatformV1GcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudAiplatformV1GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ExamplesExampleGcsSource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ExamplesExampleGcsSource');
