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

class GoogleCloudDataplexV1TriggerOneTime extends \Google\Model
{
  /**
   * Optional. Time to live for OneTime scans. default value is 24 hours,
   * minimum value is 0 seconds, and maximum value is 365 days. The time is
   * calculated from the data scan job completion time. If value is set as 0
   * seconds, the scan will be immediately deleted upon job completion,
   * regardless of whether the job succeeded or failed.
   *
   * @var string
   */
  public $ttlAfterScanCompletion;

  /**
   * Optional. Time to live for OneTime scans. default value is 24 hours,
   * minimum value is 0 seconds, and maximum value is 365 days. The time is
   * calculated from the data scan job completion time. If value is set as 0
   * seconds, the scan will be immediately deleted upon job completion,
   * regardless of whether the job succeeded or failed.
   *
   * @param string $ttlAfterScanCompletion
   */
  public function setTtlAfterScanCompletion($ttlAfterScanCompletion)
  {
    $this->ttlAfterScanCompletion = $ttlAfterScanCompletion;
  }
  /**
   * @return string
   */
  public function getTtlAfterScanCompletion()
  {
    return $this->ttlAfterScanCompletion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1TriggerOneTime::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1TriggerOneTime');
