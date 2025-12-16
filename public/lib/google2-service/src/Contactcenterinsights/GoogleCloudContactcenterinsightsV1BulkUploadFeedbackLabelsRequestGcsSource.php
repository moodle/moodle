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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequestGcsSource extends \Google\Model
{
  /**
   * Unspecified format.
   */
  public const FORMAT_FORMAT_UNSPECIFIED = 'FORMAT_UNSPECIFIED';
  /**
   * CSV format.
   */
  public const FORMAT_CSV = 'CSV';
  /**
   * JSON format.
   */
  public const FORMAT_JSON = 'JSON';
  /**
   * Required. File format which will be ingested.
   *
   * @var string
   */
  public $format;
  /**
   * Required. The Google Cloud Storage URI of the file to import. Format:
   * `gs://bucket_name/object_name`
   *
   * @var string
   */
  public $objectUri;

  /**
   * Required. File format which will be ingested.
   *
   * Accepted values: FORMAT_UNSPECIFIED, CSV, JSON
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Required. The Google Cloud Storage URI of the file to import. Format:
   * `gs://bucket_name/object_name`
   *
   * @param string $objectUri
   */
  public function setObjectUri($objectUri)
  {
    $this->objectUri = $objectUri;
  }
  /**
   * @return string
   */
  public function getObjectUri()
  {
    return $this->objectUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequestGcsSource::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1BulkUploadFeedbackLabelsRequestGcsSource');
