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

class GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateRequest extends \Google\Model
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
   * The action to be performed on the update.
   *
   * @var string
   */
  public $action;

  /**
   * The action to be performed on the update.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateRequest::class, 'Google_Service_Assuredworkloads_GoogleCloudAssuredworkloadsV1ApplyWorkloadUpdateRequest');
