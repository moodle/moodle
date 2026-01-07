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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2HybridFindingDetails extends \Google\Model
{
  protected $containerDetailsType = GooglePrivacyDlpV2Container::class;
  protected $containerDetailsDataType = '';
  /**
   * Offset in bytes of the line, from the beginning of the file, where the
   * finding is located. Populate if the item being scanned is only part of a
   * bigger item, such as a shard of a file and you want to track the absolute
   * position of the finding.
   *
   * @var string
   */
  public $fileOffset;
  /**
   * Labels to represent user provided metadata about the data being inspected.
   * If configured by the job, some key values may be required. The labels
   * associated with `Finding`'s produced by hybrid inspection. Label keys must
   * be between 1 and 63 characters long and must conform to the following
   * regular expression: `[a-z]([-a-z0-9]*[a-z0-9])?`. Label values must be
   * between 0 and 63 characters long and must conform to the regular expression
   * `([a-z]([-a-z0-9]*[a-z0-9])?)?`. No more than 10 labels can be associated
   * with a given finding. Examples: * `"environment" : "production"` *
   * `"pipeline" : "etl"`
   *
   * @var string[]
   */
  public $labels;
  /**
   * Offset of the row for tables. Populate if the row(s) being scanned are part
   * of a bigger dataset and you want to keep track of their absolute position.
   *
   * @var string
   */
  public $rowOffset;
  protected $tableOptionsType = GooglePrivacyDlpV2TableOptions::class;
  protected $tableOptionsDataType = '';

  /**
   * Details about the container where the content being inspected is from.
   *
   * @param GooglePrivacyDlpV2Container $containerDetails
   */
  public function setContainerDetails(GooglePrivacyDlpV2Container $containerDetails)
  {
    $this->containerDetails = $containerDetails;
  }
  /**
   * @return GooglePrivacyDlpV2Container
   */
  public function getContainerDetails()
  {
    return $this->containerDetails;
  }
  /**
   * Offset in bytes of the line, from the beginning of the file, where the
   * finding is located. Populate if the item being scanned is only part of a
   * bigger item, such as a shard of a file and you want to track the absolute
   * position of the finding.
   *
   * @param string $fileOffset
   */
  public function setFileOffset($fileOffset)
  {
    $this->fileOffset = $fileOffset;
  }
  /**
   * @return string
   */
  public function getFileOffset()
  {
    return $this->fileOffset;
  }
  /**
   * Labels to represent user provided metadata about the data being inspected.
   * If configured by the job, some key values may be required. The labels
   * associated with `Finding`'s produced by hybrid inspection. Label keys must
   * be between 1 and 63 characters long and must conform to the following
   * regular expression: `[a-z]([-a-z0-9]*[a-z0-9])?`. Label values must be
   * between 0 and 63 characters long and must conform to the regular expression
   * `([a-z]([-a-z0-9]*[a-z0-9])?)?`. No more than 10 labels can be associated
   * with a given finding. Examples: * `"environment" : "production"` *
   * `"pipeline" : "etl"`
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Offset of the row for tables. Populate if the row(s) being scanned are part
   * of a bigger dataset and you want to keep track of their absolute position.
   *
   * @param string $rowOffset
   */
  public function setRowOffset($rowOffset)
  {
    $this->rowOffset = $rowOffset;
  }
  /**
   * @return string
   */
  public function getRowOffset()
  {
    return $this->rowOffset;
  }
  /**
   * If the container is a table, additional information to make findings
   * meaningful such as the columns that are primary keys. If not known ahead of
   * time, can also be set within each inspect hybrid call and the two will be
   * merged. Note that identifying_fields will only be stored to BigQuery, and
   * only if the BigQuery action has been included.
   *
   * @param GooglePrivacyDlpV2TableOptions $tableOptions
   */
  public function setTableOptions(GooglePrivacyDlpV2TableOptions $tableOptions)
  {
    $this->tableOptions = $tableOptions;
  }
  /**
   * @return GooglePrivacyDlpV2TableOptions
   */
  public function getTableOptions()
  {
    return $this->tableOptions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2HybridFindingDetails::class, 'Google_Service_DLP_GooglePrivacyDlpV2HybridFindingDetails');
