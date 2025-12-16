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

class GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequestGcsDestination extends \Google\Model
{
  /**
   * Unspecified format.
   */
  public const FORMAT_FORMAT_UNSPECIFIED = 'FORMAT_UNSPECIFIED';
  /**
   * CSV format. 1,000 labels are stored per CSV file by default.
   */
  public const FORMAT_CSV = 'CSV';
  /**
   * JSON format. 1 label stored per JSON file by default.
   */
  public const FORMAT_JSON = 'JSON';
  /**
   * Optional. Add whitespace to the JSON file. Makes easier to read, but
   * increases file size. Only applicable for JSON format.
   *
   * @var bool
   */
  public $addWhitespace;
  /**
   * Optional. Always print fields with no presence. This is useful for printing
   * fields that are not set, like implicit 0 value or empty lists/maps. Only
   * applicable for JSON format.
   *
   * @var bool
   */
  public $alwaysPrintEmptyFields;
  /**
   * Required. File format in which the labels will be exported.
   *
   * @var string
   */
  public $format;
  /**
   * Required. The Google Cloud Storage URI to write the feedback labels to. The
   * file name will be used as a prefix for the files written to the bucket if
   * the output needs to be split across multiple files, otherwise it will be
   * used as is. The file extension will be appended to the file name based on
   * the format selected. E.g. `gs://bucket_name/object_uri_prefix`
   *
   * @var string
   */
  public $objectUri;
  /**
   * Optional. The number of records per file. Applicable for either format.
   *
   * @var string
   */
  public $recordsPerFileCount;

  /**
   * Optional. Add whitespace to the JSON file. Makes easier to read, but
   * increases file size. Only applicable for JSON format.
   *
   * @param bool $addWhitespace
   */
  public function setAddWhitespace($addWhitespace)
  {
    $this->addWhitespace = $addWhitespace;
  }
  /**
   * @return bool
   */
  public function getAddWhitespace()
  {
    return $this->addWhitespace;
  }
  /**
   * Optional. Always print fields with no presence. This is useful for printing
   * fields that are not set, like implicit 0 value or empty lists/maps. Only
   * applicable for JSON format.
   *
   * @param bool $alwaysPrintEmptyFields
   */
  public function setAlwaysPrintEmptyFields($alwaysPrintEmptyFields)
  {
    $this->alwaysPrintEmptyFields = $alwaysPrintEmptyFields;
  }
  /**
   * @return bool
   */
  public function getAlwaysPrintEmptyFields()
  {
    return $this->alwaysPrintEmptyFields;
  }
  /**
   * Required. File format in which the labels will be exported.
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
   * Required. The Google Cloud Storage URI to write the feedback labels to. The
   * file name will be used as a prefix for the files written to the bucket if
   * the output needs to be split across multiple files, otherwise it will be
   * used as is. The file extension will be appended to the file name based on
   * the format selected. E.g. `gs://bucket_name/object_uri_prefix`
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
  /**
   * Optional. The number of records per file. Applicable for either format.
   *
   * @param string $recordsPerFileCount
   */
  public function setRecordsPerFileCount($recordsPerFileCount)
  {
    $this->recordsPerFileCount = $recordsPerFileCount;
  }
  /**
   * @return string
   */
  public function getRecordsPerFileCount()
  {
    return $this->recordsPerFileCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequestGcsDestination::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1mainBulkDownloadFeedbackLabelsRequestGcsDestination');
