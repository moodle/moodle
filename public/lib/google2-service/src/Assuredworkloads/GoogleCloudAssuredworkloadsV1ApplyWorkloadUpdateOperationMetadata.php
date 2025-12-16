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

class GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateOperationMetadata extends \Google\Model
{
  /**
   * Unspecified value.
   */
  public const ACTION_WORKLOAD_UPDATE_ACTION_UNSPECIFIED = 'WORKLOAD_UPDATE_ACTION_UNSPECIFIED';
  /**
   * The update is applied.
   */
  public const ACTION_APPLY = 'APPLY';
  /**
   * Optional. The time the operation was created.
   *
   * @var string
   */
  public $action;
  /**
   * Optional. Output only. The time the operation was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. The resource name of the update
   *
   * @var string
   */
  public $updateName;

  /**
   * Optional. The time the operation was created.
   *
   * Accepted values: WORKLOAD_UPDATE_ACTION_UNSPECIFIED, APPLY
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Optional. Output only. The time the operation was created.
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
   * Required. The resource name of the update
   *
   * @param string $updateName
   */
  public function setUpdateName($updateName)
  {
    $this->updateName = $updateName;
  }
  /**
   * @return string
   */
  public function getUpdateName()
  {
    return $this->updateName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateOperationMetadata::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateOperationMetadata');
