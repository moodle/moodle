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

class SchedulingOnInstanceStopAction extends \Google\Model
{
  /**
   * If true, the contents of any attached Local SSD disks will be discarded
   * else, the Local SSD data will be preserved when the instance is stopped at
   * the end of the run duration/termination time.
   *
   * @var bool
   */
  public $discardLocalSsd;

  /**
   * If true, the contents of any attached Local SSD disks will be discarded
   * else, the Local SSD data will be preserved when the instance is stopped at
   * the end of the run duration/termination time.
   *
   * @param bool $discardLocalSsd
   */
  public function setDiscardLocalSsd($discardLocalSsd)
  {
    $this->discardLocalSsd = $discardLocalSsd;
  }
  /**
   * @return bool
   */
  public function getDiscardLocalSsd()
  {
    return $this->discardLocalSsd;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SchedulingOnInstanceStopAction::class, 'Google_Service_Compute_SchedulingOnInstanceStopAction');
