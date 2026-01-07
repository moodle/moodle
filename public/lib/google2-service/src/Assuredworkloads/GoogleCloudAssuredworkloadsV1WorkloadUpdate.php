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

namespace Google\Service\Assuredworkloads;

class GoogleCloudAssuredworkloadsV1WorkloadUpdate extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The update is available to be applied.
   */
  public const STATE_AVAILABLE = 'AVAILABLE';
  /**
   * The update has been applied.
   */
  public const STATE_APPLIED = 'APPLIED';
  /**
   * The update has been withdrawn by the service.
   */
  public const STATE_WITHDRAWN = 'WITHDRAWN';
  /**
   * The time the update was created.
   *
   * @var string
   */
  public $createTime;
  protected $detailsType = GoogleCloudAssuredworkloadsV1UpdateDetails::class;
  protected $detailsDataType = '';
  /**
   * Output only. Immutable. Identifier. Resource name of the WorkloadUpdate.
   * Format: organizations/{organization}/locations/{location}/workloads/{worklo
   * ad}/updates/{update}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state of the update.
   *
   * @var string
   */
  public $state;
  /**
   * The time the update was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The time the update was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The details of the update.
   *
   * @param GoogleCloudAssuredworkloadsV1UpdateDetails $details
   */
  public function setDetails(GoogleCloudAssuredworkloadsV1UpdateDetails $details)
  {
    $this->details = $details;
  }
  /**
   * @return GoogleCloudAssuredworkloadsV1UpdateDetails
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Output only. Immutable. Identifier. Resource name of the WorkloadUpdate.
   * Format: organizations/{organization}/locations/{location}/workloads/{worklo
   * ad}/updates/{update}
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
   * Output only. The state of the update.
   *
   * Accepted values: STATE_UNSPECIFIED, AVAILABLE, APPLIED, WITHDRAWN
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The time the update was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1WorkloadUpdate::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1WorkloadUpdate');
