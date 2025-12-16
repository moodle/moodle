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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ExportRequest extends \Google\Model
{
  /**
   * Optional. Delimiter used in the CSV file, if `outputFormat` is set to
   * `csv`. Defaults to the `,` (comma) character. Supported delimiter
   * characters include comma (`,`), pipe (`|`), and tab (`\t`).
   *
   * @var string
   */
  public $csvDelimiter;
  /**
   * Required. Name of the preconfigured datastore.
   *
   * @var string
   */
  public $datastoreName;
  protected $dateRangeType = GoogleCloudApigeeV1DateRange::class;
  protected $dateRangeDataType = '';
  /**
   * Optional. Description of the export job.
   *
   * @var string
   */
  public $description;
  /**
   * Required. Display name of the export job.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Output format of the export. Valid values include: `csv` or
   * `json`. Defaults to `json`. Note: Configure the delimiter for CSV output
   * using the `csvDelimiter` property.
   *
   * @var string
   */
  public $outputFormat;

  /**
   * Optional. Delimiter used in the CSV file, if `outputFormat` is set to
   * `csv`. Defaults to the `,` (comma) character. Supported delimiter
   * characters include comma (`,`), pipe (`|`), and tab (`\t`).
   *
   * @param string $csvDelimiter
   */
  public function setCsvDelimiter($csvDelimiter)
  {
    $this->csvDelimiter = $csvDelimiter;
  }
  /**
   * @return string
   */
  public function getCsvDelimiter()
  {
    return $this->csvDelimiter;
  }
  /**
   * Required. Name of the preconfigured datastore.
   *
   * @param string $datastoreName
   */
  public function setDatastoreName($datastoreName)
  {
    $this->datastoreName = $datastoreName;
  }
  /**
   * @return string
   */
  public function getDatastoreName()
  {
    return $this->datastoreName;
  }
  /**
   * Required. Date range of the data to export.
   *
   * @param GoogleCloudApigeeV1DateRange $dateRange
   */
  public function setDateRange(GoogleCloudApigeeV1DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }
  /**
   * @return GoogleCloudApigeeV1DateRange
   */
  public function getDateRange()
  {
    return $this->dateRange;
  }
  /**
   * Optional. Description of the export job.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. Display name of the export job.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. Output format of the export. Valid values include: `csv` or
   * `json`. Defaults to `json`. Note: Configure the delimiter for CSV output
   * using the `csvDelimiter` property.
   *
   * @param string $outputFormat
   */
  public function setOutputFormat($outputFormat)
  {
    $this->outputFormat = $outputFormat;
  }
  /**
   * @return string
   */
  public function getOutputFormat()
  {
    return $this->outputFormat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ExportRequest::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ExportRequest');
