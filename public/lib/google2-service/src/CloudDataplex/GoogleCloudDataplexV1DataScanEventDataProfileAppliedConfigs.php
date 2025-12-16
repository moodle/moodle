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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataScanEventDataProfileAppliedConfigs extends \Google\Model
{
  /**
   * Boolean indicating whether a column filter was applied in the DataScan job.
   *
   * @var bool
   */
  public $columnFilterApplied;
  /**
   * Boolean indicating whether a row filter was applied in the DataScan job.
   *
   * @var bool
   */
  public $rowFilterApplied;
  /**
   * The percentage of the records selected from the dataset for DataScan. Value
   * ranges between 0.0 and 100.0. Value 0.0 or 100.0 imply that sampling was
   * not applied.
   *
   * @var float
   */
  public $samplingPercent;

  /**
   * Boolean indicating whether a column filter was applied in the DataScan job.
   *
   * @param bool $columnFilterApplied
   */
  public function setColumnFilterApplied($columnFilterApplied)
  {
    $this->columnFilterApplied = $columnFilterApplied;
  }
  /**
   * @return bool
   */
  public function getColumnFilterApplied()
  {
    return $this->columnFilterApplied;
  }
  /**
   * Boolean indicating whether a row filter was applied in the DataScan job.
   *
   * @param bool $rowFilterApplied
   */
  public function setRowFilterApplied($rowFilterApplied)
  {
    $this->rowFilterApplied = $rowFilterApplied;
  }
  /**
   * @return bool
   */
  public function getRowFilterApplied()
  {
    return $this->rowFilterApplied;
  }
  /**
   * The percentage of the records selected from the dataset for DataScan. Value
   * ranges between 0.0 and 100.0. Value 0.0 or 100.0 imply that sampling was
   * not applied.
   *
   * @param float $samplingPercent
   */
  public function setSamplingPercent($samplingPercent)
  {
    $this->samplingPercent = $samplingPercent;
  }
  /**
   * @return float
   */
  public function getSamplingPercent()
  {
    return $this->samplingPercent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataScanEventDataProfileAppliedConfigs::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataScanEventDataProfileAppliedConfigs');
