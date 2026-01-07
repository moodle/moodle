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

class GoogleCloudDataplexV1DataScanExecutionSpec extends \Google\Model
{
  /**
   * Immutable. The unnested field (of type Date or Timestamp) that contains
   * values which monotonically increase over time.If not specified, a data scan
   * will run for all data in the table.
   *
   * @var string
   */
  public $field;
  protected $triggerType = GoogleCloudDataplexV1Trigger::class;
  protected $triggerDataType = '';

  /**
   * Immutable. The unnested field (of type Date or Timestamp) that contains
   * values which monotonically increase over time.If not specified, a data scan
   * will run for all data in the table.
   *
   * @param string $field
   */
  public function setField($field)
  {
    $this->field = $field;
  }
  /**
   * @return string
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * Optional. Spec related to how often and when a scan should be triggered.If
   * not specified, the default is OnDemand, which means the scan will not run
   * until the user calls RunDataScan API.
   *
   * @param GoogleCloudDataplexV1Trigger $trigger
   */
  public function setTrigger(GoogleCloudDataplexV1Trigger $trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return GoogleCloudDataplexV1Trigger
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataScanExecutionSpec::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataScanExecutionSpec');
