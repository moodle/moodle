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

namespace Google\Service\CloudDeploy;

class ReleaseReadyCondition extends \Google\Model
{
  /**
   * True if the Release is in a valid state. Otherwise at least one condition
   * in `ReleaseCondition` is in an invalid state. Iterate over those conditions
   * and see which condition(s) has status = false to find out what is wrong
   * with the Release.
   *
   * @var bool
   */
  public $status;

  /**
   * True if the Release is in a valid state. Otherwise at least one condition
   * in `ReleaseCondition` is in an invalid state. Iterate over those conditions
   * and see which condition(s) has status = false to find out what is wrong
   * with the Release.
   *
   * @param bool $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return bool
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReleaseReadyCondition::class, 'Google_Service_CloudDeploy_ReleaseReadyCondition');
