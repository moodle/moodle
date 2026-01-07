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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2RevisionScaling extends \Google\Model
{
  /**
   * Optional. Maximum number of serving instances that this resource should
   * have. When unspecified, the field is set to the server default value of
   * 100. For more information see
   * https://cloud.google.com/run/docs/configuring/max-instances
   *
   * @var int
   */
  public $maxInstanceCount;
  /**
   * Optional. Minimum number of serving instances that this resource should
   * have.
   *
   * @var int
   */
  public $minInstanceCount;

  /**
   * Optional. Maximum number of serving instances that this resource should
   * have. When unspecified, the field is set to the server default value of
   * 100. For more information see
   * https://cloud.google.com/run/docs/configuring/max-instances
   *
   * @param int $maxInstanceCount
   */
  public function setMaxInstanceCount($maxInstanceCount)
  {
    $this->maxInstanceCount = $maxInstanceCount;
  }
  /**
   * @return int
   */
  public function getMaxInstanceCount()
  {
    return $this->maxInstanceCount;
  }
  /**
   * Optional. Minimum number of serving instances that this resource should
   * have.
   *
   * @param int $minInstanceCount
   */
  public function setMinInstanceCount($minInstanceCount)
  {
    $this->minInstanceCount = $minInstanceCount;
  }
  /**
   * @return int
   */
  public function getMinInstanceCount()
  {
    return $this->minInstanceCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2RevisionScaling::class, 'Google_Service_CloudRun_GoogleCloudRunV2RevisionScaling');
