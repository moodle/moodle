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

namespace Google\Service\Compute;

class ResourcePolicyResourceStatusInstanceSchedulePolicyStatus extends \Google\Model
{
  /**
   * Output only. [Output Only] The last time the schedule successfully ran. The
   * timestamp is an RFC3339 string.
   *
   * @var string
   */
  public $lastRunStartTime;
  /**
   * Output only. [Output Only] The next time the schedule is planned to run.
   * The actual time might be slightly different. The timestamp is an RFC3339
   * string.
   *
   * @var string
   */
  public $nextRunStartTime;

  /**
   * Output only. [Output Only] The last time the schedule successfully ran. The
   * timestamp is an RFC3339 string.
   *
   * @param string $lastRunStartTime
   */
  public function setLastRunStartTime($lastRunStartTime)
  {
    $this->lastRunStartTime = $lastRunStartTime;
  }
  /**
   * @return string
   */
  public function getLastRunStartTime()
  {
    return $this->lastRunStartTime;
  }
  /**
   * Output only. [Output Only] The next time the schedule is planned to run.
   * The actual time might be slightly different. The timestamp is an RFC3339
   * string.
   *
   * @param string $nextRunStartTime
   */
  public function setNextRunStartTime($nextRunStartTime)
  {
    $this->nextRunStartTime = $nextRunStartTime;
  }
  /**
   * @return string
   */
  public function getNextRunStartTime()
  {
    return $this->nextRunStartTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourcePolicyResourceStatusInstanceSchedulePolicyStatus::class, 'Google_Service_Compute_ResourcePolicyResourceStatusInstanceSchedulePolicyStatus');
